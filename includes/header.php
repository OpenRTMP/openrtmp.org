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
<link rel="icon" href="assets/img/favicon.svg" type="image/svg+xml">
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<header class="site-header">
  <div class="container nav" id="nav">
    <a href="index.php" class="brand">
      <svg width="28" height="28" viewBox="0 0 32 32" fill="none">
        <rect width="32" height="32" rx="8" fill="#ff5c35"/>
        <path d="M11 9L22 16L11 23V9Z" fill="#0a0e14"/>
      </svg>
      OpenRTMP<span class="dot">.org</span>
    </a>
    <nav class="nav-links">
      <a href="index.php#features">Features</a>
      <a href="index.php#architecture">Architecture</a>
      <a href="docs.php">Docs</a>
      <a href="download.php">Download</a>
      <a href="https://github.com/openrtmp" target="_blank" rel="noopener">GitHub</a>
    </nav>
    <div class="nav-cta">
      <a href="https://github.com/openrtmp/librtmp2" target="_blank" rel="noopener" class="btn btn-ghost">Star on GitHub</a>
      <a href="download.php" class="btn btn-primary">Get librtmp2</a>
    </div>
    <button class="nav-toggle" aria-label="Toggle navigation">&#9776;</button>
  </div>
</header>
