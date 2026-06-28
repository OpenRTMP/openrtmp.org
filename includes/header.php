<?php
$page = $page ?? 'home';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo $pageTitle ?? 'OpenRTMP — A modern, pure RTMP / E-RTMP protocol library'; ?></title>
<meta name="description" content="<?php echo $pageDescription ?? 'librtmp2 is a pure C protocol library for RTMP and E-RTMP (v1 & v2): handshake, chunking, AMF0/AMF3, FLV and host callbacks — no media server, no HTTP, no auth policy.'; ?>">
<link rel="icon" href="/assets/img/favicon.svg" type="image/svg+xml">
<link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>

<header class="site-header">
  <div class="container nav" id="nav">
    <a href="/" class="brand">
      <img src="/assets/img/favicon.svg" width="28" height="28" alt="OpenRTMP logo">
      OpenRTMP<span class="dot">.org</span>
    </a>
    <nav class="nav-links">
      <a href="/#features">Features</a>
      <a href="/#architecture">Architecture</a>
      <a href="/#ecosystem">Ecosystem</a>
      <a href="/docs/">Docs</a>
      <a href="/download/">Download</a>
      <a href="https://github.com/OpenRTMP" target="_blank" rel="noopener">GitHub</a>
    </nav>
    <div class="nav-cta">
      <a href="https://github.com/OpenRTMP/librtmp2" target="_blank" rel="noopener" class="btn btn-ghost">Star on GitHub</a>
      <a href="/download/" class="btn btn-primary">Get librtmp2</a>
    </div>
    <button class="nav-toggle" aria-label="Toggle navigation">&#9776;</button>
  </div>
</header>
