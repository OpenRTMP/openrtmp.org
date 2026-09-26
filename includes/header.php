<?php
require_once __DIR__ . '/i18n.php';
$page = $page ?? 'home';
$pageTitle = $pageTitle ?? t('OpenRTMP — Rust RTMP/E-RTMP library and self-hosted server');
$pageDescription = $pageDescription ?? t('OpenRTMP provides a Rust RTMP/E-RTMP library plus a self-hosted RTMP/RTMPS server, REST API, live statistics, and web control panel.');
$canonicalPath = $canonicalPath ?? ($_SERVER['REQUEST_URI'] ?? '/');
$canonicalPath = parse_url($canonicalPath, PHP_URL_PATH) ?: '/';
$canonicalPath = preg_replace('#/index\.php$#', '/', $canonicalPath);
$canonicalUrl = 'https://openrtmp.org' . $canonicalPath;
$ogType = $ogType ?? 'website';
$socialImage = $socialImage ?? 'https://openrtmp.org/assets/img/social-preview.png';
$socialImageWidth = $socialImageWidth ?? 1200;
$socialImageHeight = $socialImageHeight ?? 630;
$socialImageAlt = $socialImageAlt ?? t('OpenRTMP social preview showing the librtmp2 Rust RTMP/E-RTMP library and the self-hosted RTMP/RTMPS server with web panel.');
$alternatePaths = openrtmp_alternates($canonicalPath);
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?></title>
<meta name="description" content="<?php echo htmlspecialchars($pageDescription, ENT_QUOTES, 'UTF-8'); ?>">
<meta name="robots" content="index, follow, max-image-preview:large">
<link rel="canonical" href="<?php echo htmlspecialchars($canonicalUrl, ENT_QUOTES, 'UTF-8'); ?>">
<?php if (count($alternatePaths) > 1): ?>
<?php foreach ($alternatePaths as $altLang => $altPath): ?>
<link rel="alternate" hreflang="<?php echo $altLang; ?>" href="<?php echo htmlspecialchars('https://openrtmp.org' . $altPath, ENT_QUOTES, 'UTF-8'); ?>">
<?php endforeach; ?>
<?php if (isset($alternatePaths['en'])): ?>
<link rel="alternate" hreflang="x-default" href="<?php echo htmlspecialchars('https://openrtmp.org' . $alternatePaths['en'], ENT_QUOTES, 'UTF-8'); ?>">
<?php endif; ?>
<?php endif; ?>
<meta property="og:type" content="<?php echo htmlspecialchars($ogType, ENT_QUOTES, 'UTF-8'); ?>">
<meta property="og:site_name" content="OpenRTMP">
<meta property="og:locale" content="<?php echo OPENRTMP_OG_LOCALE[$lang]; ?>">
<?php foreach (array_keys($alternatePaths) as $altLang): if ($altLang !== $lang): ?>
<meta property="og:locale:alternate" content="<?php echo OPENRTMP_OG_LOCALE[$altLang]; ?>">
<?php endif; endforeach; ?>
<meta property="og:title" content="<?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?>">
<meta property="og:description" content="<?php echo htmlspecialchars($pageDescription, ENT_QUOTES, 'UTF-8'); ?>">
<meta property="og:url" content="<?php echo htmlspecialchars($canonicalUrl, ENT_QUOTES, 'UTF-8'); ?>">
<meta property="og:image" content="<?php echo htmlspecialchars($socialImage, ENT_QUOTES, 'UTF-8'); ?>">
<meta property="og:image:width" content="<?php echo (int) $socialImageWidth; ?>">
<meta property="og:image:height" content="<?php echo (int) $socialImageHeight; ?>">
<meta property="og:image:alt" content="<?php echo htmlspecialchars($socialImageAlt, ENT_QUOTES, 'UTF-8'); ?>">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?>">
<meta name="twitter:description" content="<?php echo htmlspecialchars($pageDescription, ENT_QUOTES, 'UTF-8'); ?>">
<meta name="twitter:image" content="<?php echo htmlspecialchars($socialImage, ENT_QUOTES, 'UTF-8'); ?>">
<meta name="twitter:image:alt" content="<?php echo htmlspecialchars($socialImageAlt, ENT_QUOTES, 'UTF-8'); ?>">
<link rel="icon" href="/assets/img/favicon.svg" type="image/svg+xml">
<link rel="stylesheet" href="/assets/css/style.css">
<link rel="stylesheet" href="/assets/css/content.css">
<?php if (!empty($structuredData)): ?>
<script type="application/ld+json"><?php echo json_encode($structuredData, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?></script>
<?php endif; ?>
</head>
<body>

<header class="site-header">
  <div class="container nav" id="nav">
    <a href="<?php echo lurl('/'); ?>" class="brand">
      <img src="/assets/img/favicon.svg" width="28" height="28" alt="<?php echo t('OpenRTMP logo'); ?>">
      OpenRTMP<span class="dot">.org</span>
    </a>
    <nav class="nav-links" aria-label="<?php echo t('Primary navigation'); ?>">
      <a href="<?php echo lurl('/quickstart/'); ?>"><?php echo t('Quickstart'); ?></a>
      <a href="<?php echo lurl('/guides/'); ?>"><?php echo t('Guides'); ?></a>
      <a href="<?php echo lurl('/showcase/'); ?>"><?php echo t('Showcase'); ?></a>
      <a href="<?php echo lurl('/docs/'); ?>"><?php echo t('Docs'); ?></a>
      <a href="<?php echo lurl('/download/'); ?>"><?php echo t('Download'); ?></a>
      <a href="https://github.com/OpenRTMP" target="_blank" rel="noopener">GitHub</a>
      <span class="lang-switch" role="group" aria-label="<?php echo t('Language'); ?>">
        <?php foreach (['en' => ['EN', 'English'], 'de' => ['DE', 'Deutsch']] as $switchLang => [$switchCode, $switchName]): ?>
          <?php if ($switchLang === $lang): ?>
            <span class="lang-current" lang="<?php echo $switchLang; ?>" title="<?php echo $switchName; ?>" aria-current="true"><?php echo $switchCode; ?></span>
          <?php else: ?>
            <a href="<?php echo htmlspecialchars($alternatePaths[$switchLang] ?? OPENRTMP_LANG_PREFIX[$switchLang] . '/', ENT_QUOTES, 'UTF-8'); ?>" hreflang="<?php echo $switchLang; ?>" lang="<?php echo $switchLang; ?>" title="<?php echo $switchName; ?>"><?php echo $switchCode; ?></a>
          <?php endif; ?>
        <?php endforeach; ?>
      </span>
    </nav>
    <div class="nav-cta">
      <a href="https://github.com/OpenRTMP" target="_blank" rel="noopener" class="btn btn-ghost"><?php echo t('View on GitHub'); ?></a>
      <a href="<?php echo lurl('/quickstart/'); ?>" class="btn btn-primary"><?php echo t('Run with Docker'); ?></a>
    </div>
    <button class="nav-toggle" aria-label="<?php echo t('Toggle navigation'); ?>" aria-expanded="false">&#9776;</button>
  </div>
</header>
