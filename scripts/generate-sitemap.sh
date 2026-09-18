#!/bin/sh
# Generate sitemap.xml with <lastmod> taken from each page's git history.
#
# The lastmod of a URL is the committer date of the most recent commit that
# touched the page's index.php, so the sitemap cannot drift away from the
# content it describes.
#
# Usage:
#   scripts/generate-sitemap.sh              # rewrite sitemap.xml in place
#   scripts/generate-sitemap.sh -o FILE      # write to FILE ('-' for stdout)

set -eu

BASE_URL='https://openrtmp.org'

# One row per indexable page: URL path, source file, changefreq, priority.
# Adding a page here is the only step needed to get it into the sitemap; the
# coverage check below fails if a page exists on disk but is missing from this
# table.
PAGES='
/|index.php|weekly|1.0
/quickstart/|quickstart/index.php|monthly|0.9
/guides/|guides/index.php|weekly|0.8
/guides/self-hosted-rtmp-server-docker/|guides/self-hosted-rtmp-server-docker/index.php|monthly|0.8
/guides/rtmps-server-obs/|guides/rtmps-server-obs/index.php|monthly|0.8
/guides/rtmp-server-ha-clustering/|guides/rtmp-server-ha-clustering/index.php|monthly|0.8
/guides/enhanced-rtmp-hevc-av1-opus/|guides/enhanced-rtmp-hevc-av1-opus/index.php|monthly|0.8
/guides/openrtmp-noalbs-json-stats/|guides/openrtmp-noalbs-json-stats/index.php|monthly|0.8
/guides/openrtmp-vs-nginx-rtmp/|guides/openrtmp-vs-nginx-rtmp/index.php|monthly|0.8
/docs/|docs/index.php|weekly|0.8
/download/|download/index.php|weekly|0.8
/legal/|legal/index.php|yearly|0.2
'

output='sitemap.xml'
while [ $# -gt 0 ]; do
  case "$1" in
    -o|--output) output="${2:?-o needs a file}"; shift 2 ;;
    -h|--help)
      echo 'Usage: generate-sitemap.sh [-o FILE]'
      echo
      echo "Rewrite sitemap.xml with each URL's <lastmod> taken from the date of"
      echo "the most recent commit touching that page. Use '-o -' for stdout."
      exit 0 ;;
    *) echo "generate-sitemap: unknown argument '$1'" >&2; exit 2 ;;
  esac
done

repo_root=$(git rev-parse --show-toplevel)
cd "$repo_root"

# lastmod is read from commit history, so a shallow checkout would silently
# produce wrong dates. Fail loudly instead.
if [ "$(git rev-parse --is-shallow-repository)" = 'true' ]; then
  echo "generate-sitemap: shallow clone - full history is required" >&2
  echo "  In GitHub Actions, check out with 'fetch-depth: 0'." >&2
  exit 1
fi

# Every page on disk must appear in PAGES.
missing=''
for page in $(find . -name 'index.php' -not -path './includes/*' | sed 's|^\./||' | sort); do
  case "$PAGES" in
    *"|$page|"*) ;;
    *) missing="$missing  $page
" ;;
  esac
done
if [ -n "$missing" ]; then
  echo "generate-sitemap: pages are missing from the PAGES table in $0:" >&2
  printf '%s' "$missing" >&2
  exit 1
fi

emit() {
  echo '<?xml version="1.0" encoding="UTF-8"?>'
  # The sitemap protocol defines this exact namespace URI. It is an
  # identifier, never fetched, and crawlers match it literally - an https
  # variant is simply not a sitemap namespace. NOSONAR(S5332): the
  # clear-text-protocol warning does not apply to an XML namespace.
  echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' # NOSONAR
  # Redirected, not piped: a failure inside the loop must exit the script.
  while IFS='|' read -r path source changefreq priority; do
    [ -n "$path" ] || continue

    if [ ! -f "$source" ]; then
      echo "generate-sitemap: $source does not exist" >&2
      exit 1
    fi

    lastmod=$(git log -1 --format=%cs -- "$source")
    if [ -z "$lastmod" ]; then
      lastmod=$(date -u +%F)
      echo "generate-sitemap: $source has no commit yet, using today ($lastmod)" >&2
    fi

    echo '  <url>'
    echo "    <loc>${BASE_URL}${path}</loc>"
    echo "    <lastmod>${lastmod}</lastmod>"
    echo "    <changefreq>${changefreq}</changefreq>"
    echo "    <priority>${priority}</priority>"
    echo '  </url>'
  done <<EOF
$PAGES
EOF
  echo '</urlset>'
}

if [ "$output" = '-' ]; then
  emit
else
  # mktemp rather than a $$-derived name: predictable temp paths are a
  # symlink-attack vector. It creates the file 0600, so restore the mode a
  # publicly served file needs before moving it into place.
  tmp=$(mktemp "${output}.XXXXXX")
  trap 'rm -f "$tmp"' EXIT
  emit >"$tmp"
  chmod 644 "$tmp"
  mv "$tmp" "$output"
  echo "generate-sitemap: wrote $output"
fi
