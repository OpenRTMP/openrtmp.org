# openrtmp.org

Source for [OpenRTMP.org](https://openrtmp.org), the public website for the OpenRTMP ecosystem.

OpenRTMP includes:

- [`librtmp2`](https://github.com/OpenRTMP/librtmp2) — Rust RTMP/RTMPS and Enhanced RTMP protocol library with a C-compatible FFI
- [`librtmp2-server`](https://github.com/OpenRTMP/librtmp2-server) — self-hosted RTMP/RTMPS server with SQLite, stream keys, REST API, statistics, and optional HA clustering
- [`librtmp2-server-panel`](https://github.com/OpenRTMP/librtmp2-server-panel) — web UI for stream lifecycle, live monitoring, and cluster-aware operations
- [`community`](https://github.com/OpenRTMP/community) — central issue tracker and discussion hub for all OpenRTMP projects

All projects are under active development and remain pre-1.0. Interfaces and configuration may still evolve; the website intentionally avoids hard-coded current release numbers where a package registry or GitHub release page can remain the source of truth.

## Community links

- Issues, bug reports, feature requests, and interoperability reports: [OpenRTMP Community Issues](https://github.com/OpenRTMP/community/issues/new/choose)
- Questions, setup help, ideas, and design discussion: [OpenRTMP Community Discussions](https://github.com/OpenRTMP/community/discussions)
- Source-code pull requests stay in the repository that owns the implementation.

## Local development

The site is a static-ish PHP/HTML/CSS/JavaScript project with no framework, build step, or database. It requires PHP 7.4 or newer.

```bash
php -S localhost:8090
```

Open `http://localhost:8090`.

Validate PHP files before publishing:

```bash
find . -name '*.php' -print0 | xargs -0 -n1 php -l
```

Start the server with `php -S localhost:8090 scripts/dev-router.php` so a path
with no page behind it answers 404 instead of falling back to the nearest
`index.php`, then check every internal link and `#anchor`:

```bash
scripts/check-links.py http://localhost:8090 $(find . -name index.php -not -path './includes/*' | sed 's|^\.||; s|index\.php$||')
scripts/check-seo.py   http://localhost:8090 $(find . -name index.php -not -path './includes/*' | sed 's|^\.||; s|index\.php$||')
```

`check-seo.py` asserts the conventions a page must keep: one title and
description, a self-referencing canonical, an indexable robots meta tag, one
`h1`, alt text on images, complete `TechArticle` structured data, reciprocal
`hreflang` alternates, and a URL set that matches `sitemap.xml`.

`Website checks` runs both checks in CI, renders every page with all
PHP diagnostics enabled and fails on any warning, notice or deprecation. The
production deploy runs these checks first and uploads nothing when they fail.

## Structure

```text
index.php                         Homepage and audience paths
quickstart/index.php              Five-minute Docker + OBS setup
docs/index.php                    Reference documentation
download/index.php                Crate, source, and Docker downloads
guides/index.php                  Guide landing page
guides/*/index.php                Search-focused technical guides
legal/index.php                   Contact and legal notice
de/                               German translation, mirroring the paths above
includes/                         Shared header, footer, and i18n strings
assets/css/style.css              Core design system
assets/css/content.css            Article and quickstart styles
assets/js/                        Navigation, copy, and docs behavior
assets/img/                       Logo, favicon, and social preview
robots.txt                        Crawler policy
sitemap.xml                       Indexable public pages (generated)
scripts/generate-sitemap.sh       Rebuilds sitemap.xml from git history
scripts/check-links.py            Checks internal links and anchors on a running site
scripts/check-seo.py              Checks metadata, canonicals, hreflang, and the sitemap
scripts/dev-router.php            php -S router that answers 404 for missing pages
```

## Sitemap

`sitemap.xml` is generated, not hand-edited. Each `<lastmod>` is the date of the
most recent commit that touched that page's `index.php`, so the sitemap cannot
drift away from the content it describes.

```bash
scripts/generate-sitemap.sh          # rewrite sitemap.xml
scripts/generate-sitemap.sh -o -     # preview on stdout
```

The URL list, `changefreq`, `priority`, and any extra files a page depends on
(for example the shared showcase entries) live in the `PAGES` table at the top
of the script. The script fails if an `index.php` exists on disk but is missing
from that table, and it needs full git history — a shallow clone is rejected
rather than silently producing wrong dates.

CI enforces both ends of this: `Website checks` fails when the committed
`sitemap.xml` differs from what the script produces, and the production deploy
regenerates it just before upload.

## Languages

English is the primary language and lives at the site root. German is the
second language and mirrors every page below `/de/` (for example
`/guides/av1-over-rtmp/` ↔ `/de/guides/av1-over-rtmp/`).

- A German page sets `$lang = 'de';` and its `/de/...` `$canonicalPath` before
  including the header, and links to other pages through their `/de/` paths.
- The header emits `hreflang` alternates (`en`, `de`, `x-default`) and the
  EN/DE switcher automatically whenever both language versions exist on disk.
- Shared header/footer strings are translated in `includes/i18n.php`; wrap new
  shared strings in `t('...')` and add the German text there.
- When you change an English page, update its German counterpart in the same
  pull request.

## Content principles

- Separate the **developer/library** path from the **operator/server** path.
- State the pre-1.0 status and implementation boundaries consistently.
- Link to repository implementation-status tables for code-accurate claims.
- Route issues to [`OpenRTMP/community/issues`](https://github.com/OpenRTMP/community/issues) and discussions to [`OpenRTMP/community/discussions`](https://github.com/OpenRTMP/community/discussions), rather than to an individual source repository.
- Prefer package registries and release pages over manually copied latest-version strings.
- Create one canonical page for each major search intent instead of duplicating setup text.
- Keep guides task-focused, honest about missing features, and useful without marketing language.

## Adding a guide

1. Create `guides/<slug>/index.php`.
2. Set a unique `$pageTitle`, `$pageDescription`, and `$canonicalPath`.
3. Add `TechArticle` structured data when appropriate.
4. Link the guide from `guides/index.php` and relevant existing pages.
5. Add the German translation at `de/guides/<slug>/index.php` and link it from
   `de/guides/index.php` (see [Languages](#languages)).
6. Add both pages to the `PAGES` table in `scripts/generate-sitemap.sh`, then run
   `scripts/generate-sitemap.sh` and commit the regenerated `sitemap.xml`.
7. Run PHP lint and review mobile table/code overflow.

## Benchmark data

`includes/benchmarks-data.php` holds the release snapshots up to
librtmp2-server 0.6.5 (EN + DE). Newer releases need no edit here: the release
job of `librtmp2-server` records every release run as `bench/releases/<tag>.json`
on `main`, and `includes/benchmarks-ci.php` lists that directory (GitHub
contents API), reads each file plus the matching `librtmp2`
`bench/releases/v<librtmp2>.json`, and shows it as a release snapshot. The
newest release is the default on `/benchmarks/` and feeds the homepage teaser.

The **Preview** card (`?run=ci`) is not stored here either.
`includes/benchmarks-ci.php` reads `bench/latest.json` on `main` of
`OpenRTMP/librtmp2-server` and `OpenRTMP/librtmp2` (written by their
`benchmarks.yml` after every merge to `main`), validates it, caches it in the
system temp directory for 15 minutes and renders it like a snapshot. If GitHub
cannot be reached a stale copy is used for up to a week; without any copy the
card is simply not shown. To test it offline, define the constant `OPENRTMP_BENCH_DATA_BASE` (e.g. a
`file://` directory with the same layout) from a `php -d auto_prepend_file=…`
file; it is deliberately not read from the environment or the request.

## Deployment

Deploy the repository contents to a PHP-capable web root. The server should route directory requests such as `/quickstart/` to the corresponding `index.php` and serve XML/TXT/CSS/JS/image files directly.
