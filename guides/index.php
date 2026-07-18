<?php
$page = 'guides';
$pageTitle = 'OpenRTMP Guides — Docker, OBS, RTMPS, and Enhanced RTMP';
$pageDescription = 'Practical OpenRTMP guides for self-hosting an RTMP server with Docker, publishing from OBS, enabling RTMPS, and understanding Enhanced RTMP codecs.';
$canonicalPath = '/guides/';
include __DIR__ . '/../includes/header.php';
?>

<main>
  <div class="page-hero container">
    <span class="eyebrow">Practical guides</span>
    <h1>Build and operate RTMP infrastructure</h1>
    <p>Task-focused articles for operators, application developers, and contributors. Each guide states the current alpha limitations instead of hiding them behind generic feature claims.</p>
  </div>

  <section style="padding-top: 0;">
    <div class="container">
      <div class="grid-2 guide-grid">
        <article class="card guide-card">
          <span class="guide-tag">Docker &middot; OBS &middot; Self-hosted</span>
          <h2><a href="/guides/self-hosted-rtmp-server-docker/">Self-host an RTMP server with Docker</a></h2>
          <p>Deploy the OpenRTMP server and web panel, understand the exposed ports, create stream keys, publish from OBS, and prepare the stack for an internet-facing host.</p>
          <a href="/guides/self-hosted-rtmp-server-docker/" class="text-link">Read the deployment guide &rarr;</a>
        </article>

        <article class="card guide-card">
          <span class="guide-tag">Security &middot; TLS &middot; OBS</span>
          <h2><a href="/guides/rtmps-server-obs/">Configure an RTMPS server for OBS</a></h2>
          <p>Run encrypted RTMPS alongside plaintext RTMP, configure certificates, expose the correct port, and verify the server before changing OBS ingest settings.</p>
          <a href="/guides/rtmps-server-obs/" class="text-link">Read the RTMPS guide &rarr;</a>
        </article>

        <article class="card guide-card">
          <span class="guide-tag">E-RTMP &middot; HEVC &middot; AV1 &middot; Opus</span>
          <h2><a href="/guides/enhanced-rtmp-hevc-av1-opus/">Enhanced RTMP codecs explained</a></h2>
          <p>Understand why Enhanced RTMP exists, how FourCC-based media differs from legacy FLV signaling, and which OpenRTMP paths are complete versus still experimental.</p>
          <a href="/guides/enhanced-rtmp-hevc-av1-opus/" class="text-link">Read the codec guide &rarr;</a>
        </article>

        <article class="card guide-card">
          <span class="guide-tag">Comparison &middot; Migration</span>
          <h2><a href="/guides/openrtmp-vs-nginx-rtmp/">OpenRTMP vs nginx-rtmp</a></h2>
          <p>Compare architecture, deployment, statistics compatibility, missing features, and the scenarios where each project is the more appropriate choice.</p>
          <a href="/guides/openrtmp-vs-nginx-rtmp/" class="text-link">Read the comparison &rarr;</a>
        </article>
      </div>

      <div class="cta" style="margin-top: 48px;">
        <h2>Prefer a direct setup path?</h2>
        <p>The quickstart takes you from an empty Docker host to an OBS-ready stream in a few steps.</p>
        <div class="hero-actions" style="margin-bottom:0;">
          <a href="/quickstart/" class="btn btn-primary">Open the quickstart</a>
          <a href="/docs/" class="btn btn-ghost">Browse the reference docs</a>
        </div>
      </div>
    </div>
  </section>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
