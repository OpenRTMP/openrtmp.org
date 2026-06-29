<?php
$page = 'download';
$pageTitle = 'Download — OpenRTMP';
$pageDescription = 'Get librtmp2, librtmp2-server, and librtmp2-server-panel: add the Cargo dependency or clone the repositories, and build the full RTMP stack.';
include __DIR__ . '/../includes/header.php';
?>

<main>
  <div class="page-hero container">
    <h1>Download &amp; Build</h1>
    <p>librtmp2 ships as a Rust crate &mdash; add it to your <code>Cargo.toml</code> and you're ready to go. Want a ready-to-run endpoint instead? Grab librtmp2-server too.</p>
  </div>

  <section style="padding-top: 0;">
    <div class="container">
      <div class="download-grid">

        <div class="download-card">
          <h3>&#128193; Cargo (recommended)</h3>
          <p>Add librtmp2 as a dependency in your <code>Cargo.toml</code>. Always pulls the latest compatible release from crates.io.</p>
          <pre><code>[dependencies]
lrtmp2 = "0.9"</code></pre>
        </div>

        <div class="download-card">
          <h3>&#127991;&#65039; Tagged release</h3>
          <p>Pin to a specific SemVer tag for production builds. Releases are API-checked against the previous tag.</p>
          <pre><code>[dependencies.lrtmp2]
git = "https://github.com/OpenRTMP/librtmp2.git"
tag = "v0.9.0"</code></pre>
        </div>

        <div class="download-card">
          <h3>&#9881;&#65039; Build from source</h3>
          <p>Clone and build locally for development or to track upstream fixes closely.</p>
          <pre><code>git clone https://github.com/OpenRTMP/librtmp2.git
cd librtmp2
cargo build --release
cargo test</code></pre>
        </div>

        <div class="download-card">
          <h3>&#128268; librtmp2-server</h3>
          <p>The reference ingest/playback server built on top of librtmp2 &mdash; a separate repository and binary, ready to run as a deployment target or to read as a worked integration example. See the <a href="/docs/#server">docs</a> for what it adds on top of the library.</p>
          <pre><code>git clone https://github.com/OpenRTMP/librtmp2-server.git
cd librtmp2-server
make release</code></pre>
        </div>

        <div class="download-card">
          <h3>&#127912; librtmp2-server-panel</h3>
          <p>The web management panel for librtmp2-server. Create streams, monitor stats, and manage keys from a browser. Flask-based, Docker-ready, with CSRF protection and encrypted key storage.</p>
          <pre><code>git clone https://github.com/OpenRTMP/librtmp2-server-panel.git
cd librtmp2-server-panel
cp .env.example .env
docker compose up -d</code></pre>
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
          <a href="https://github.com/OpenRTMP/librtmp2" target="_blank" rel="noopener" class="btn btn-primary">Open librtmp2 on GitHub</a>
        </div>
      </div>
    </div>
  </section>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
