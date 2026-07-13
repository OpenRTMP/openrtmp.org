# AGENTS.md

## Cursor Cloud specific instructions

`openrtmp.org` is a static-ish PHP/HTML/JS site — no build step, no framework, no database. Run it with PHP's built-in server (see `README.md`): `php -S localhost:8000`.

Non-obvious note for this environment: port `8000` collides with `librtmp2-server-panel`'s dev server, so when both run at once serve this site on another port, e.g. `php -S localhost:8090`. PHP 8.3 CLI is preinstalled in the snapshot.
