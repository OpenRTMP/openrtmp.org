<?php
$page = 'download';
$pageTitle = 'Download OpenRTMP — Rust crate, source, and Docker images';
$pageDescription = 'Install librtmp2 from crates.io, build OpenRTMP projects from source, or deploy the RTMP server and web panel from published Docker images.';
$canonicalPath = '/download/';
include __DIR__ . '/../includes/header.php';
?>

<main>
  <div class="page-hero container">
    <span class="eyebrow">Crate &middot; Source &middot; Docker</span>
    <h1>Download and deploy OpenRTMP</h1>
    <p>Choose the protocol library when building an application, or use the published server and panel images for a self-hosted RTMP stack.</p>
  </div>

  <section style="padding-top: 0;">
    <div class="container">
      <div class="callout warning">
        <strong>All OpenRTMP projects are active alpha software.</strong> Review each repository's implementation status, pin tested versions, and validate your complete workflow before critical production use.
      </div>

      <div class="grid-2" style="margin-bottom: 42px;">
        <article class="card path-card">
          <div class="icon">&#128225;</div>
          <h2>Run server + panel</h2>
          <p>Use the standalone Compose stack for the fastest evaluation. It pulls published images and does not require source checkouts or a Rust toolchain.</p>
          <div class="card-actions">
            <a href="/quickstart/" class="btn btn-primary">Five-minute quickstart</a>
            <a href="https://github.com/OpenRTMP/librtmp2-server-panel/blob/main/compose.quickstart.yml" target="_blank" rel="noopener" class="btn btn-ghost">View Compose file</a>
          </div>
        </article>
        <article class="card path-card">
          <div class="icon">&#128736;&#65039;</div>
          <h2>Embed the Rust library</h2>
          <p>Add <code>librtmp2</code> from crates.io for custom RTMP servers, clients, relays, plugins, and protocol tooling.</p>
          <div class="card-actions">
            <a href="https://crates.io/crates/librtmp2" target="_blank" rel="noopener" class="btn btn-primary">Open crates.io</a>
            <a href="https://docs.rs/librtmp2" target="_blank" rel="noopener" class="btn btn-ghost">Open docs.rs</a>
          </div>
        </article>
      </div>

      <div class="download-grid">
        <div class="download-card download-card--wide" id="cargo">
          <h3>&#128230; Install librtmp2 with Cargo</h3>
          <p>Let Cargo select the current release, then commit <code>Cargo.lock</code> for applications. Libraries should choose an explicit compatibility range based on the release they have tested.</p>
          <pre><code>cargo add librtmp2
cargo build
cargo test</code></pre>
          <p>RTMPS/TLS is enabled by default. To build without the optional OpenSSL-backed TLS feature:</p>
          <pre><code>cargo add librtmp2 --no-default-features</code></pre>
          <p>Use the release shown on <a href="https://crates.io/crates/librtmp2" target="_blank" rel="noopener">crates.io</a> as the source of truth instead of copying a version number from this website.</p>
        </div>

        <div class="download-card">
          <h3>&#9881;&#65039; Build librtmp2 from source</h3>
          <pre><code>git clone https://github.com/OpenRTMP/librtmp2.git
cd librtmp2
cargo build --release
cargo test</code></pre>
          <p>The repository documents the code-accurate implementation status for legacy RTMP, E-RTMP v1/v2, client behavior, TLS, and interoperability tests.</p>
        </div>

        <div class="download-card">
          <h3>&#128268; Build librtmp2-server from source</h3>
          <pre><code>git clone https://github.com/OpenRTMP/librtmp2-server.git
cd librtmp2-server
cargo build --release
# Optional HA clustering:
# cargo build --release --features cluster
cp .env.example .env
LRTMP2_DB=./server.db ./target/release/librtmp2-server</code></pre>
          <p>The API token is generated on first startup unless supplied as the real <code>LRTMP2_API_TOKEN</code> process environment variable. Docker images already include the <code>cluster</code> feature; runtime clustering stays off until you set <code>CLUSTER_ENABLED=true</code>. See the <a href="/guides/rtmp-server-ha-clustering/">HA clustering guide</a>.</p>
        </div>

        <div class="download-card download-card--wide" id="docker-stack">
          <h3>&#128051; Docker Compose: server + panel + Redis</h3>
          <p>The recommended evaluation path uses the standalone Compose file from the panel repository:</p>
          <pre><code>git clone https://github.com/OpenRTMP/librtmp2-server-panel.git
cd librtmp2-server-panel

# Create .env with LRTMP2_API_TOKEN, PASSWORD, and SECRET_KEY.
docker compose -f compose.quickstart.yml up -d</code></pre>
          <p>Follow the <a href="/quickstart/">quickstart</a> for secure secret generation, OBS settings, health checks, troubleshooting, and the production checklist.</p>
        </div>

        <div class="download-card" id="docker-server">
          <h3>&#128051; Server image</h3>
          <p>Published for multiple architectures at:</p>
          <pre><code>ghcr.io/openrtmp/librtmp2-server</code></pre>
          <p>Use a release tag for tested deployments. Moving tags such as <code>latest</code>, <code>beta</code>, and <code>alpha</code> are convenient for evaluation but can change underneath an automated redeploy.</p>
        </div>

        <div class="download-card" id="docker-panel">
          <h3>&#128051; Panel image</h3>
          <p>Published for multiple architectures at:</p>
          <pre><code>ghcr.io/openrtmp/librtmp2-server-panel</code></pre>
          <p>The panel requires a reachable server API, the matching API token, a public RTMP hostname, login credentials, and a Flask session secret.</p>
        </div>

        <div class="download-card download-card--wide" id="server-only">
          <h3>&#128225; Run the server image by itself</h3>
          <pre><code>docker run -d \
  --name librtmp2-server \
  -p 1935:1935 \
  -p 8080:8080 \
  -e LRTMP2_API_TOKEN="$(openssl rand -hex 32)" \
  -v librtmp2-server-data:/data \
  ghcr.io/openrtmp/librtmp2-server:latest</code></pre>
          <p>Save the supplied token before running the command. If you omit it, retrieve the generated token from the first-start logs. For repeatable deployments, pin the image tag.</p>
        </div>

        <div class="download-card download-card--wide" id="panel-only">
          <h3>&#127912; Run the panel against an existing server</h3>
          <pre><code>docker run -d \
  --name librtmp2-server-panel \
  -p 8000:8000 \
  -e LRTMP2_API_URL=http://server.internal:8080 \
  -e LRTMP2_STATS_URL=https://api.example.com \
  -e LRTMP2_DOMAIN=stream.example.com \
  -e LRTMP2_API_TOKEN=&lt;matching-server-token&gt; \
  -e USERNAME=admin \
  -e PASSWORD=&lt;strong-password&gt; \
  -e SECRET_KEY=&lt;random-session-secret&gt; \
  ghcr.io/openrtmp/librtmp2-server-panel:latest</code></pre>
        </div>
      </div>

      <div class="cta" style="margin-top: 48px;">
        <h2>Not sure which component you need?</h2>
        <p>The homepage separates the library and operator paths, while the guides cover Docker, OBS, RTMPS, HA clustering, and Enhanced RTMP.</p>
        <div class="hero-actions" style="margin-bottom:0;">
          <a href="/#paths" class="btn btn-primary">Choose a path</a>
          <a href="/guides/" class="btn btn-ghost">Browse guides</a>
          <a href="/docs/" class="btn btn-ghost">Reference docs</a>
        </div>
      </div>
    </div>
  </section>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
