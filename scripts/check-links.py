#!/usr/bin/env python3
"""Check internal links and anchors on every page of a running site.

Usage: scripts/check-links.py BASE_URL PATH...

Each PATH (for example /guides/ or /de/docs/) is fetched from BASE_URL. Every
same-site href/src on those pages must answer 200, and every #fragment that
points at one of the fetched pages must match an id on that page. Duplicate
ids on a page are reported too, because a duplicate makes a fragment ambiguous.
External links are not fetched.
"""

import sys
import urllib.error
import urllib.request
from html import unescape
from html.parser import HTMLParser
from urllib.parse import urljoin, urlsplit

SITE = "https://openrtmp.org"


class Page(HTMLParser):
    def __init__(self):
        super().__init__()
        self.ids = set()
        self.duplicate_ids = []
        self.refs = []

    def handle_starttag(self, tag, attrs):
        attrs = dict(attrs)
        element_id = attrs.get("id")
        if element_id is not None:
            if element_id in self.ids:
                self.duplicate_ids.append(element_id)
            self.ids.add(element_id)
        for name in ("href", "src"):
            value = attrs.get(name)
            if value and tag in ("a", "link", "img", "script"):
                self.refs.append(unescape(value))


def fetch(url):
    with urllib.request.urlopen(url, timeout=15) as response:
        return response.status, response.read().decode("utf-8", "replace")


def load_pages(base, paths, errors):
    pages = {}
    for path in paths:
        page = Page()
        page.feed(fetch(base + path)[1])
        pages[path] = page
        for element_id in page.duplicate_ids:
            errors.append(f"{path}: duplicate id '{element_id}'")
    return pages


def status_of(base, path, cache):
    if path not in cache:
        try:
            cache[path] = fetch(base + path)[0]
        except urllib.error.HTTPError as exc:
            cache[path] = exc.code
    return cache[path]


def check_ref(base, pages, path, ref, cache):
    """Return a problem description for one href/src, or None."""
    if ref.startswith(("mailto:", "tel:", "data:", "javascript:")):
        return None
    if ref.startswith(SITE):
        ref = ref[len(SITE):] or "/"
    target = urlsplit(urljoin(path, ref))
    if target.scheme or target.netloc:
        return None  # external
    if target.path in pages:
        if target.fragment and target.fragment not in pages[target.path].ids:
            return f"{path}: '{ref}' points at a missing anchor"
        return None
    status = status_of(base, target.path, cache)
    return None if status == 200 else f"{path}: '{ref}' returned HTTP {status}"


def main():
    if len(sys.argv) < 3:
        print(__doc__.strip(), file=sys.stderr)
        return 2
    base = sys.argv[1].rstrip("/")
    errors = []
    pages = load_pages(base, sys.argv[2:], errors)

    cache = {}
    for path, page in pages.items():
        for ref in page.refs:
            problem = check_ref(base, pages, path, ref, cache)
            if problem:
                errors.append(problem)

    for error in errors:
        print(error, file=sys.stderr)
    print(f"check-links: {len(pages)} pages, {len(errors)} problem(s)")
    return 1 if errors else 0


if __name__ == "__main__":
    sys.exit(main())
