<?php
$page = 'download';
$pageTitle = 'Download — OpenRTMP';
$pageDescription = 'Get librtmp2 and librtmp2-server: clone the repositories, pick Make or Meson, and build the client/server protocol library.';
include __DIR__ . '/../includes/header.php';
?>

<main>
  <div class="page-hero container">
    <h1>Download &amp; Build</h1>
    <p>librtmp2 ships as source &mdash; no package manager required. Clone, build, link. Want a ready-to-run endpoint instead? Grab librtmp2-server too.</p>
  </div>

  <section style="padding-top: 0;">
    <div class="container">
      <div class="download-grid">

        <div class="download-card">
          <h3>&#128193; Source (recommended)</h3>
          <p>Always the latest commit on the default branch. Use this for development or to track upstream fixes closely.</p>
          <pre><code>git clone https://github.com/openrtmp/librtmp2.git
cd librtmp2
make release</code></pre>
        </div>

        <div class="download-card">
          <h3>&#127991;&#65039; Tagged release</h3>
          <p>Pin to a specific SemVer tag for production builds. Releases are ABI-checked against the previous tag.</p>
          <pre><code>git clone --branch v0.9.0 \
  https://github.com/openrtmp/librtmp2.git
cd librtmp2
make release &amp;&amp; make install</code></pre>
        </div>

        <div class="download-card">
          <h3>&#9881;&#65039; Meson subproject</h3>
          <p>Embed librtmp2 directly inside another Meson-based project.</p>
          <pre><code>[wrap-git]
url = https://github.com/openrtmp/librtmp2.git
revision = head
depth = 1</code></pre>
        </div>

        <div class="download-card">
          <h3>&#128268; librtmp2-server</h3>
          <p>The reference ingest/playback server built on top of librtmp2 &mdash; a separate repository and binary, ready to run as a deployment target or to read as a worked integration example. See the <a href="/docs/#server">docs</a> for what it adds on top of the library.</p>
          <pre><code>git clone https://github.com/openrtmp/librtmp2-server.git
cd librtmp2-server
make release</code></pre>
        </div>

      </div>
    </div>
  </section>

  <section>
    <div class="container">
      <div class="cta">
        <h2>Need the full build matrix?</h2>
        <p>Debug, ASan, UBSan, and integration test targets are documented alongside the source.</p>
        <div class="hero-actions" style="margin-bottom:0;">
          <a href="/docs/" class="btn btn-ghost">Read the Docs</a>
          <a href="https://github.com/openrtmp/librtmp2" target="_blank" rel="noopener" class="btn btn-primary">Open librtmp2 on GitHub</a>
        </div>
      </div>
    </div>
  </section>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
