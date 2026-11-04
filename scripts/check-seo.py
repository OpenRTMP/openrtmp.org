#!/usr/bin/env python3
"""Check the SEO invariants of every public page on a running site.

Usage: scripts/check-seo.py BASE_URL PATH...

Each PATH (for example /guides/ or /de/docs/) must answer 200 and satisfy the
conventions this site relies on:

* one <title> and one meta description, both non-empty and unique per page;
* one self-referencing absolute canonical URL;
* an indexable robots meta tag;
* an <html lang> that matches the /de/ prefix;
* exactly one <h1>;
* alt text on every image;
* parseable JSON-LD whose TechArticle nodes carry the fields the guides rely on;
* reciprocal hreflang alternates with an x-default;
* the same URL set as sitemap.xml, with a canonical that matches it;
* a robots.txt that points at that sitemap.

The sitemap is fetched from BASE_URL, so run this against the same site the
paths were rendered from.
"""

import json
import re
import sys
import urllib.error
import urllib.request
from html.parser import HTMLParser
from urllib.parse import urlsplit

SCHEMA_CONTEXT = "schema.org"
ARTICLE_FIELDS = (
    "headline",
    "description",
    "author",
    "publisher",
    "inLanguage",
    "image",
    "mainEntityOfPage",
)

META_DESCRIPTION = "description"
META_ROBOTS = "robots"
OG_TITLE = "og:title"
OG_DESCRIPTION = "og:description"
OG_IMAGE = "og:image"
OG_IMAGE_TYPE = "og:image:type"
REQUIRED_OG = (OG_TITLE, OG_DESCRIPTION, OG_IMAGE, OG_IMAGE_TYPE)

# A guide article lives one level below /guides/ (or /de/guides/); the listing
# page itself does not match.
GUIDE_ARTICLE = re.compile(r"^/(?:de/)?guides/[^/]+/$")
NON_INDEXABLE = {"noindex", "none"}


class Page(HTMLParser):
    def __init__(self):
        super().__init__(convert_charrefs=True)
        self.title = None
        self.title_count = 0
        self._in_title = False
        self._title = ""
        self._in_ld = False
        self._ld = ""
        self.lang = None
        self.metas = []
        self.links = []
        self.images = []
        self.h1 = 0
        self.json_ld = []

    def handle_starttag(self, tag, attrs):
        attrs = dict(attrs)
        if tag == "html":
            self.lang = attrs.get("lang")
        elif tag == "title":
            self.title_count += 1
            self._in_title = True
            self._title = ""
        elif tag == "meta":
            self.metas.append(attrs)
        elif tag == "link":
            self.links.append(attrs)
        elif tag == "img":
            self.images.append(attrs)
        elif tag == "h1":
            self.h1 += 1
        elif tag == "script" and attrs.get("type") == "application/ld+json":
            self._in_ld = True
            self._ld = ""

    def handle_endtag(self, tag):
        if tag == "title":
            self._in_title = False
            self.title = " ".join(self._title.split())
        elif tag == "script" and self._in_ld:
            self._in_ld = False
            self.json_ld.append(self._ld)

    def handle_data(self, data):
        if self._in_title:
            self._title += data
        if self._in_ld:
            self._ld += data

    def meta(self, name=None, prop=None):
        """The content of the first meta tag matching name or property."""
        for meta in self.metas:
            if name is not None and meta.get("name", "").lower() == name:
                return meta.get("content", "")
            if prop is not None and meta.get("property", "").lower() == prop:
                return meta.get("content", "")
        return None

    def canonical(self):
        """The href of the rel=canonical link, or None."""
        for link in self.links:
            if (link.get("rel") or "").lower() == "canonical":
                return link.get("href")
        return None

    def alternates(self):
        """The hreflang alternates of the page, keyed by language code."""
        return {
            link.get("hreflang"): link.get("href")
            for link in self.links
            if (link.get("rel") or "").lower() == "alternate" and link.get("hreflang")
        }


def fetch(base, path):
    """Return (status, body) for a path, without following redirects."""
    try:
        with urllib.request.urlopen(base + path, timeout=20) as response:
            return response.status, response.read().decode("utf-8", "replace")
    except urllib.error.HTTPError as exc:
        return exc.code, ""
    except urllib.error.URLError as exc:
        raise SystemExit(f"check-seo: cannot reach {base}{path}: {exc.reason}")


def schema_nodes(blob):
    """Yield every node of a JSON-LD document, including @graph members."""
    document = json.loads(blob)
    stack = [document]
    while stack:
        node = stack.pop()
        if isinstance(node, dict):
            if "@type" in node:
                yield node
            stack.extend(node.values())
        elif isinstance(node, list):
            stack.extend(node)


def is_indexable(robots):
    """False when a robots meta tag forbids indexing via noindex or none."""
    directives = {part.strip().lower() for part in robots.split(",")}
    return not directives & NON_INDEXABLE


def check_head(path, page, errors):
    """Check the title, description, robots meta tag and social tags."""
    if page.title_count != 1:
        errors.append(f"{path}: expected one <title>, found {page.title_count}")
    elif not page.title:
        errors.append(f"{path}: <title> is empty")
    if not page.meta(name=META_DESCRIPTION):
        errors.append(f"{path}: missing meta description")
    robots = page.meta(name=META_ROBOTS)
    if robots is None:
        errors.append(f"{path}: missing meta robots")
    elif not is_indexable(robots):
        errors.append(f"{path}: public page is not indexable: {robots!r}")
    for prop in REQUIRED_OG:
        if not page.meta(prop=prop):
            errors.append(f"{path}: missing {prop}")
    image = page.meta(prop=OG_IMAGE)
    if image and not image.startswith("https://"):
        errors.append(f"{path}: {OG_IMAGE} is not absolute")


def check_document(path, page, errors):
    """Check the document language, its single h1 and the image alt text."""
    if page.lang != language_of(path):
        errors.append(f"{path}: <html lang> is {page.lang!r}")
    if page.h1 != 1:
        errors.append(f"{path}: expected one <h1>, found {page.h1}")
    for image in page.images:
        # alt="" is a valid, deliberate marker for a decorative image; only a
        # missing attribute is a defect.
        if "alt" not in image:
            errors.append(f"{path}: image without alt attribute: {image.get('src')}")


def check_canonical(path, page, errors):
    """Check that the canonical URL is absolute and points at this page."""
    canonical = page.canonical()
    if canonical is None:
        errors.append(f"{path}: missing canonical")
        return
    parts = urlsplit(canonical)
    if not parts.scheme or not parts.netloc:
        errors.append(f"{path}: canonical is not absolute: {canonical}")
    elif parts.path != path:
        errors.append(f"{path}: canonical points at {parts.path}")


def check_node(path, node, errors):
    """Check one JSON-LD node's context and, for articles, its fields."""
    context = node.get("@context")
    if context is not None and SCHEMA_CONTEXT not in str(context):
        errors.append(f"{path}: unexpected JSON-LD @context {context!r}")
    if node.get("@type") != "TechArticle":
        return
    missing = [field for field in ARTICLE_FIELDS if not node.get(field)]
    if missing:
        errors.append(f"{path}: TechArticle is missing {', '.join(missing)}")


def check_structured_data(path, page, errors):
    """Check that every JSON-LD block parses and guides keep an article node."""
    has_article = False
    for blob in page.json_ld:
        try:
            nodes = list(schema_nodes(blob))
        except json.JSONDecodeError as exc:
            errors.append(f"{path}: JSON-LD does not parse: {exc}")
            continue
        for node in nodes:
            has_article = has_article or node.get("@type") == "TechArticle"
            check_node(path, node, errors)
    if GUIDE_ARTICLE.match(path) and not has_article:
        errors.append(f"{path}: guide article has no TechArticle structured data")


def check_page(path, page, errors):
    """Check the per-page head, document structure and structured data."""
    check_head(path, page, errors)
    check_document(path, page, errors)
    check_canonical(path, page, errors)
    check_structured_data(path, page, errors)


def language_of(path):
    """The language a site path belongs to, from its /de/ prefix."""
    return "de" if path.startswith("/de/") else "en"


def counterpart(path):
    """The other language's path for the same page."""
    return path[3:] if path.startswith("/de/") else "/de" + path


def check_alternate_completeness(path, alternates, pages, errors):
    """Both language labels must exist when the mirror page exists."""
    mirror = counterpart(path)
    if mirror not in pages:
        return
    for lang in ("en", "de"):
        if lang not in alternates:
            errors.append(f"{path}: missing hreflang {lang} although {mirror} exists")


def check_alternate_target(path, page, lang, href, pages, errors):
    """One alternate must stay on this host and name the mirrored page."""
    parts = urlsplit(href)
    target = parts.path
    if parts.netloc != urlsplit(page.canonical() or "").netloc:
        errors.append(f"{path}: hreflang {lang} points at another host: {href}")
        return
    if target not in pages:
        errors.append(f"{path}: hreflang {lang} points outside the page set: {href}")
        return
    if lang == "x-default":
        return
    if lang not in ("en", "de"):
        errors.append(f"{path}: unexpected hreflang {lang!r}")
        return
    if language_of(target) != lang:
        errors.append(
            f"{path}: hreflang {lang} points at a {language_of(target)} page: {target}"
        )
    expected = path if lang == language_of(path) else counterpart(path)
    if target != expected:
        errors.append(f"{path}: hreflang {lang} points at {target}, expected {expected}")
    if page.canonical() not in pages[target].alternates().values():
        errors.append(f"{path}: hreflang {lang} -> {target} is not reciprocal")


def check_x_default(path, alternates, errors):
    """The alternate set must name the English page as x-default."""
    if "x-default" not in alternates:
        errors.append(f"{path}: hreflang set has no x-default")
        return
    english = path[3:] if path.startswith("/de/") else path
    if urlsplit(alternates["x-default"]).path != english:
        errors.append(f"{path}: x-default does not point at the English page")


def check_alternates(pages, errors):
    """Check that hreflang sets are complete, labelled and reciprocal."""
    for path, page in pages.items():
        alternates = page.alternates()
        check_alternate_completeness(path, alternates, pages, errors)
        for lang, href in alternates.items():
            check_alternate_target(path, page, lang, href, pages, errors)
        if alternates:
            check_x_default(path, alternates, errors)


def check_uniqueness(pages, errors):
    """Check that no two pages share a title or a description."""
    for field, value in (
        ("title", lambda p: p.title),
        ("description", lambda p: p.meta(name=META_DESCRIPTION)),
    ):
        seen = {}
        for path, page in pages.items():
            text = value(page)
            if not text:
                continue
            if text in seen:
                errors.append(f"{path}: duplicate {field} also used by {seen[text]}")
            seen[text] = path


def check_sitemap(base, paths, pages, errors):
    """Check that sitemap.xml and robots.txt describe the pages on disk."""
    status, body = fetch(base, "/sitemap.xml")
    if status != 200:
        errors.append(f"/sitemap.xml: returned HTTP {status}")
        return
    urls = []
    for fragment in body.split("<loc>")[1:]:
        urls.append(fragment.split("</loc>", 1)[0].strip())
    listed = {urlsplit(url).path for url in urls}
    for path in sorted(listed - set(paths)):
        errors.append(f"/sitemap.xml lists {path}, which is not a known page")
    for path in sorted(set(paths) - listed):
        errors.append(f"{path}: missing from /sitemap.xml")
    for path, page in pages.items():
        if path in listed and page.canonical() and urlsplit(page.canonical()).path != path:
            errors.append(f"{path}: sitemap URL and canonical disagree")
    for url in urls:
        if not url.startswith("https://"):
            errors.append(f"/sitemap.xml: URL is not absolute: {url}")

    status, robots = fetch(base, "/robots.txt")
    if status != 200:
        errors.append(f"/robots.txt: returned HTTP {status}")
    elif "/sitemap.xml" not in robots:
        errors.append("/robots.txt: does not reference /sitemap.xml")


def main():
    """Run every check against the paths given on the command line."""
    if len(sys.argv) < 3:
        print(__doc__.strip(), file=sys.stderr)
        return 2
    base = sys.argv[1].rstrip("/")
    paths = sys.argv[2:]
    errors = []
    pages = {}
    for path in paths:
        status, body = fetch(base, path)
        if status != 200:
            errors.append(f"{path}: returned HTTP {status}")
            continue
        page = Page()
        page.feed(body)
        pages[path] = page
        check_page(path, page, errors)

    check_alternates(pages, errors)
    check_uniqueness(pages, errors)
    check_sitemap(base, paths, pages, errors)

    for error in errors:
        print(error, file=sys.stderr)
    print(f"check-seo: {len(pages)} pages, {len(errors)} problem(s)")
    return 1 if errors else 0


if __name__ == "__main__":
    sys.exit(main())
