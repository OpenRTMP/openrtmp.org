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
docs.php             Documentation
download.php         Download & build instructions
includes/            Shared header/footer PHP partials
assets/css/          Stylesheet
assets/js/           Nav toggle, copy-to-clipboard, docs scrollspy
assets/img/          Favicon / logo
```
