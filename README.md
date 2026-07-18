# openrtmp.org

Source for [OpenRTMP.org](https://openrtmp.org), the public website for the OpenRTMP ecosystem.

OpenRTMP includes:

- [`librtmp2`](https://github.com/OpenRTMP/librtmp2) — Rust RTMP/RTMPS and Enhanced RTMP protocol library with a C-compatible FFI
- [`librtmp2-server`](https://github.com/OpenRTMP/librtmp2-server) — self-hosted RTMP/RTMPS server with SQLite, stream keys, REST API, and statistics
- [`librtmp2-server-panel`](https://github.com/OpenRTMP/librtmp2-server-panel) — web UI for stream lifecycle and live monitoring

All projects are active alpha software. The website intentionally avoids hard-coded release numbers where a package registry or GitHub release page can remain the source of truth.

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

## Structure

```text
index.php                         Homepage and audience paths
quickstart/index.php              Five-minute Docker + OBS setup
docs/index.php                    Reference documentation
download/index.php                Crate, source, and Docker downloads
guides/index.php                  Guide landing page
guides/*/index.php                Search-focused technical guides
legal/index.php                   Contact and legal notice
includes/                         Shared header and footer
assets/css/style.css              Core design system
assets/css/content.css            Article and quickstart styles
assets/js/                        Navigation, copy, and docs behavior
assets/img/                       Logo and favicon
robots.txt                        Crawler policy
sitemap.xml                       Indexable public pages
```

## Content principles

- Separate the **developer/library** path from the **operator/server** path.
- State the alpha status and implementation boundaries consistently.
- Link to repository implementation-status tables for code-accurate claims.
- Prefer package registries and release pages over manually copied latest-version strings.
- Create one canonical page for each major search intent instead of duplicating setup text.
- Keep guides task-focused, honest about missing features, and useful without marketing language.

## Adding a guide

1. Create `guides/<slug>/index.php`.
2. Set a unique `$pageTitle`, `$pageDescription`, and `$canonicalPath`.
3. Add `TechArticle` structured data when appropriate.
4. Link the guide from `guides/index.php` and relevant existing pages.
5. Add the canonical URL to `sitemap.xml`.
6. Run PHP lint and review mobile table/code overflow.

## Deployment

Deploy the repository contents to a PHP-capable web root. The server should route directory requests such as `/quickstart/` to the corresponding `index.php` and serve XML/TXT/CSS/JS/image files directly.
