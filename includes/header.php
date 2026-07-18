<?php
$page = $page ?? 'home';
$pageTitle = $pageTitle ?? 'OpenRTMP — Rust RTMP/E-RTMP library and self-hosted server';
$pageDescription = $pageDescription ?? 'OpenRTMP provides a Rust RTMP/E-RTMP library plus a self-hosted RTMP/RTMPS server, REST API, live statistics, and web control panel.';
$canonicalPath = $canonicalPath ?? ($_SERVER['REQUEST_URI'] ?? '/');
$canonicalPath = parse_url($canonicalPath, PHP_URL_PATH) ?: '/';
$canonicalUrl = 'https://openrtmp.org' . $canonicalPath;
$ogType = $ogType ?? 'website';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?></title>
<meta name="description" content="<?php echo htmlspecialchars($pageDescription, ENT_QUOTES, 'UTF-8'); ?>">
<meta name="robots" content="index, follow, max-image-preview:large">
<link rel="canonical" href="<?php echo htmlspecialchars($canonicalUrl, ENT_QUOTES, 'UTF-8'); ?>">
<meta property="og:type" content="<?php echo htmlspecialchars($ogType, ENT_QUOTES, 'UTF-8'); ?>">
<meta property="og:site_name" content="OpenRTMP">
<meta property="og:title" content="<?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?>">
<meta property="og:description" content="<?php echo htmlspecialchars($pageDescription, ENT_QUOTES, 'UTF-8'); ?>">
<meta property="og:url" content="<?php echo htmlspecialchars($canonicalUrl, ENT_QUOTES, 'UTF-8'); ?>">
<meta name="twitter:card" content="summary">
<meta name="twitter:title" content="<?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?>">
<meta name="twitter:description" content="<?php echo htmlspecialchars($pageDescription, ENT_QUOTES, 'UTF-8'); ?>">
<link rel="icon" href="/assets/img/favicon.svg" type="image/svg+xml">
<link rel="stylesheet" href="/assets/css/style.css">
<?php if (!empty($structuredData)): ?>
<script type="application/ld+json"><?php echo json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?></script>
<?php endif; ?>
</head>
<body>

<header class="site-header">
  <div class="container nav" id="nav">
    <a href="/" class="brand">
      <img src="/assets/img/favicon.svg" width="28" height="28" alt="OpenRTMP logo">
      OpenRTMP<span class="dot">.org</span>
    </a>
    <nav class="nav-links" aria-label="Primary navigation">
      <a href="/quickstart/">Quickstart</a>
      <a href="/guides/">Guides</a>
      <a href="/docs/">Docs</a>
      <a href="/download/">Download</a>
      <a href="https://github.com/OpenRTMP" target="_blank" rel="noopener">GitHub</a>
    </nav>
    <div class="nav-cta">
      <a href="https://github.com/OpenRTMP" target="_blank" rel="noopener" class="btn btn-ghost">View on GitHub</a>
      <a href="/quickstart/" class="btn btn-primary">Run with Docker</a>
    </div>
    <button class="nav-toggle" aria-label="Toggle navigation" aria-expanded="false">&#9776;</button>
  </div>
</header>
