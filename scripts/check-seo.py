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


class Page(HTMLParser):
    def __init__(self):
        super().__init__(convert_charrefs=True)
        self.title = None
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
        for meta in self.metas:
            if name is not None and meta.get("name", "").lower() == name:
                return meta.get("content", "")
            if prop is not None and meta.get("property", "").lower() == prop:
                return meta.get("content", "")
        return None

    def canonical(self):
        for link in self.links:
            if (link.get("rel") or "").lower() == "canonical":
                return link.get("href")
        return None

    def alternates(self):
        return {
            link.get("hreflang"): link.get("href")
            for link in self.links
            if (link.get("rel") or "").lower() == "alternate" and link.get("hreflang")
        }


def fetch(base, path):
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


def check_page(path, page, errors):
    if not page.title:
        errors.append(f"{path}: missing <title>")
    description = page.meta(name="description")
    if not description:
        errors.append(f"{path}: missing meta description")
    if page.meta(name="robots") is None:
        errors.append(f"{path}: missing meta robots")
    elif "noindex" in page.meta(name="robots").lower():
        errors.append(f"{path}: public page is marked noindex")
    if page.lang != ("de" if path.startswith("/de/") else "en"):
        errors.append(f"{path}: <html lang> is {page.lang!r}")
    if page.h1 != 1:
        errors.append(f"{path}: expected one <h1>, found {page.h1}")
    for image in page.images:
        if not image.get("alt"):
            errors.append(f"{path}: image without alt text: {image.get('src')}")

    for prop in ("og:title", "og:description", "og:image"):
        if not page.meta(prop=prop):
            errors.append(f"{path}: missing {prop}")
    if page.meta(prop="og:image") and not page.meta(prop="og:image").startswith("https://"):
        errors.append(f"{path}: og:image is not absolute")

    canonical = page.canonical()
    if canonical is None:
        errors.append(f"{path}: missing canonical")
    else:
        parts = urlsplit(canonical)
        if not parts.scheme or not parts.netloc:
            errors.append(f"{path}: canonical is not absolute: {canonical}")
        elif parts.path != path:
            errors.append(f"{path}: canonical points at {parts.path}")

    for blob in page.json_ld:
        try:
            nodes = list(schema_nodes(blob))
        except json.JSONDecodeError as exc:
            errors.append(f"{path}: JSON-LD does not parse: {exc}")
            continue
        for node in nodes:
            context = node.get("@context")
            if context is not None and SCHEMA_CONTEXT not in str(context):
                errors.append(f"{path}: unexpected JSON-LD @context {context!r}")
            if node.get("@type") == "TechArticle":
                missing = [field for field in ARTICLE_FIELDS if not node.get(field)]
                if missing:
                    errors.append(
                        f"{path}: TechArticle is missing {', '.join(missing)}"
                    )


def check_alternates(pages, errors):
    for path, page in pages.items():
        alternates = page.alternates()
        if not alternates:
            continue
        for lang, href in alternates.items():
            target = urlsplit(href).path
            if target not in pages:
                errors.append(f"{path}: hreflang {lang} points outside the page set: {href}")
                continue
            if "x-default" in lang:
                continue
            if page.canonical() not in pages[target].alternates().values():
                errors.append(f"{path}: hreflang {lang} -> {target} is not reciprocal")
        if "x-default" not in alternates:
            errors.append(f"{path}: hreflang set has no x-default")
        elif urlsplit(alternates["x-default"]).path != (path if not path.startswith("/de/") else path[3:]):
            errors.append(f"{path}: x-default does not point at the English page")


def check_uniqueness(pages, errors):
    for field, value in (("title", lambda p: p.title), ("description", lambda p: p.meta(name="description"))):
        seen = {}
        for path, page in pages.items():
            text = value(page)
            if not text:
                continue
            if text in seen:
                errors.append(f"{path}: duplicate {field} also used by {seen[text]}")
            seen[text] = path


def check_sitemap(base, paths, pages, errors):
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
