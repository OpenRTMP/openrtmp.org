# openrtmp.org

Source for the [OpenRTMP.org](https://openrtmp.org) website — a static-ish PHP/HTML/JS site (no build step, no framework, no database).

## Local development

Requires PHP 7.4+.

```bash
php -S localhost:8000
```

Then open http://localhost:8000.

If port `8000` is already in use (e.g. by `librtmp2-server-panel`), pick another port:

```bash
php -S localhost:8090
```

## Structure

```
index.php            Home page (hero, features, architecture, ecosystem)
docs/index.php       Documentation (librtmp2, server, panel, Docker)
download/index.php   Download, build, and Docker deployment instructions
legal/index.php      Legal notice (contact / imprint)
includes/            Shared header/footer PHP partials
assets/css/          Stylesheet
assets/js/           Nav toggle, copy-to-clipboard, docs scrollspy
assets/img/          Favicon / logo
```

## Pages

| Path | Content |
|------|---------|
| `/` | Project overview, code example, layer stack, ecosystem cards |
| `/docs/` | Getting started, callbacks, `librtmp2-server`, panel, Docker |
| `/download/` | Cargo deps, source builds, GHCR Docker images |
| `/legal/` | Legal notice and contact information |

## Context

The site documents the OpenRTMP ecosystem:

- **[librtmp2](https://github.com/OpenRTMP/librtmp2)** — Rust RTMP/E-RTMP protocol library (alpha, currently `v0.3.1`)
- **[librtmp2-server](https://github.com/OpenRTMP/librtmp2-server)** — reference media server with REST API and stats
- **[librtmp2-server-panel](https://github.com/OpenRTMP/librtmp2-server-panel)** — Flask web UI for stream management

All three projects are alpha. The website is English-only.
