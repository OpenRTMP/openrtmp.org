<?php
// Router for `php -S` that answers 404 for paths with no page behind them.
// Without it the built-in server falls back to the nearest index.php, so a
// broken link such as /guides/typo/ renders the guides page with HTTP 200.
//   php -S localhost:8090 scripts/dev-router.php
$parsed = parse_url($_SERVER['REQUEST_URI'] ?? '/');
// A request target that starts with "//" is parsed as a host, so its path
// cannot be determined and must not fall back to the document root.
$path = is_array($parsed) && !isset($parsed['host']) && isset($parsed['path']) ? rawurldecode($parsed['path']) : '';
$target = dirname(__DIR__) . $path;
if ($path !== '' && strpos($path, '..') === false && (is_file($target) || is_file(rtrim($target, '/') . '/index.php'))) {
    return false; // let the built-in server serve or execute it as usual
}
http_response_code(404);
echo "Not Found\n";
return true;
