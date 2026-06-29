# openrtmp.org

Source for the OpenRTMP.org website — a static-ish PHP/HTML/JS site (no build step, no framework, no database).

## Local development

Requires PHP 7.4+.

```bash
php -S localhost:8000
```

Then open http://localhost:8000.

## Structure

```
index.php           Home page (hero, features, architecture)
docs/index.php       Documentation
download/index.php   Download & build instructions
includes/            Shared header/footer PHP partials
assets/css/          Stylesheet
assets/js/           Nav toggle, copy-to-clipboard, docs scrollspy
assets/img/          Favicon / logo
```

## Context

librtmp2 has been rewritten from C to Rust. All references across the website
have been updated accordingly (C → Rust, Make/Meson → Cargo, include/*.h → crate API,
. → Rust syntax in code examples, etc.).
