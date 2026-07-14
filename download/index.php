<?php
$page = 'download';
$pageTitle = 'Download — OpenRTMP';
$pageDescription = 'Get librtmp2, librtmp2-server, and librtmp2-server-panel: add the Cargo dependency, build from source, or deploy with Docker.';
include __DIR__ . '/../includes/header.php';
?>

<main>
  <div class="page-hero container">
    <h1>Download &amp; Build</h1>
    <p><code>librtmp2</code> is published on <a href="https://crates.io/crates/librtmp2" target="_blank" rel="noopener">crates.io</a>. <code>librtmp2-server</code> and <code>librtmp2-server-panel</code> are separate repositories with prebuilt Docker images. All three projects are currently <strong>Alpha</strong> &mdash; pin to a specific version and test your workflow before production use.</p>
  </div>

  <section style="padding-top: 0;">
    <div class="container">
      <div class="download-grid">

        <div class="download-card">
          <h3>&#128193; Cargo (recommended for alpha)</h3>
          <p>Add <code>librtmp2</code> from <a href="https://crates.io/crates/librtmp2" target="_blank" rel="noopener">crates.io</a>. <code>version = "0"</code> accepts any <code>0.x</code> release &mdash; run <code>cargo update -p librtmp2</code> regularly to pull security fixes and new alpha builds. RTMPS/TLS is on by default (<code>tls</code> feature).</p>
          <pre><code>[dependencies.librtmp2]
version = "0"

# refresh to the newest matching crates.io release:
# cargo update -p librtmp2</code></pre>
        </div>

        <div class="download-card">
          <h3>&#128274; Pinned version</h3>
          <p>Lock to an exact release for reproducible CI or production builds. Latest on crates.io: <code>0.3.1</code>. Prefer the alpha range above if you want automatic security updates within <code>0.x</code>.</p>
          <pre><code>[dependencies.librtmp2]
version = "=0.3.1"</code></pre>
        </div>

        <div class="download-card">
          <h3>&#9881;&#65039; Build from source</h3>
          <p>Clone and build locally for development or to track upstream fixes closely. Requires Rust 1.93+.</p>
          <pre><code>git clone https://github.com/OpenRTMP/librtmp2.git
cd librtmp2
cargo build --release
cargo test</code></pre>
        </div>

        <div class="download-card">
          <h3>&#128268; librtmp2-server</h3>
          <p>The reference RTMP/E-RTMP media server (Alpha). SQLite persistence, REST API, key-protected stats, optional RTMPS. See the <a href="/docs/#server">server docs</a> for configuration and API details.</p>
          <pre><code>git clone https://github.com/OpenRTMP/librtmp2-server.git
cd librtmp2-server
cargo build --release
cp .env.example .env
LRTMP2_DB=./server.db ./target/release/librtmp2-server</code></pre>
        </div>

        <div class="download-card download-card--wide" id="docker-server">
          <h3>&#128051; Docker: librtmp2-server only</h3>
          <p>Prebuilt multi-arch images (<code>amd64</code> / <code>arm64</code> / <code>riscv64</code>) are published to <code>ghcr.io/openrtmp/librtmp2-server</code> on every release. On first start the server generates an API bearer token and prints it once to the logs &mdash; copy it for REST API calls or for the panel.</p>
          <pre><code>docker run -d \
  --name librtmp2-server \
  -p 1935:1935 \
  -p 8080:8080 \
  # -p 1936:1936   # RTMPS — only when TLS_ENABLED=true
  -v librtmp2-server-data:/data \
  ghcr.io/openrtmp/librtmp2-server:latest

docker logs librtmp2-server   # copy the generated API token</code></pre>
          <p>Or build from source with the repo's <code>docker-compose.yml</code>:</p>
          <pre><code>git clone https://github.com/OpenRTMP/librtmp2-server.git
cd librtmp2-server
docker compose up -d</code></pre>
        </div>

        <div class="download-card download-card--wide" id="docker-panel">
          <h3>&#128051; Docker: librtmp2-server-panel only</h3>
          <p>Prebuilt image: <code>ghcr.io/openrtmp/librtmp2-server-panel</code>. Requires a running server and its API token (<code>docker logs librtmp2-server</code>). Put both containers on the same Docker network so the panel can reach the server by container name.</p>
          <pre><code>docker network create openrtmp   # skip if it already exists

docker run -d \
  --name librtmp2-server-panel \
  --network openrtmp \
  -p 8000:8000 \
  -e LRTMP2_API_URL=http://librtmp2-server:8080 \
  -e LRTMP2_STATS_URL=http://localhost:8080 \
  -e LRTMP2_API_TOKEN=&lt;token-from-server-logs&gt; \
  -e LRTMP2_DOMAIN=localhost \
  -e PASSWORD=&lt;panel-password-12-chars-or-more&gt; \
  -e SECRET_KEY=&lt;random-32-plus-char-secret&gt; \
  ghcr.io/openrtmp/librtmp2-server-panel:latest

# Panel: http://localhost:8000</code></pre>
          <p>If the server runs on the host (not in Docker), use <code>LRTMP2_API_URL=http://host.docker.internal:8080</code> on Windows/macOS instead of the container name.</p>
        </div>

        <div class="download-card download-card--wide" id="docker-stack">
          <h3>&#128051; Docker: server + panel (<code>docker run</code>)</h3>
          <p>Full stack without Compose &mdash; shared network, optional Redis for panel rate limiting, one API token for both services. Generate secrets first:</p>
          <pre><code>export LRTMP2_API_TOKEN=$(openssl rand -hex 32)
export PANEL_PASSWORD='your-panel-password'
export PANEL_SECRET=$(python3 -c "import secrets; print(secrets.token_hex(32))")

docker network create openrtmp

docker run -d \
  --name librtmp2-panel-redis \
  --network openrtmp \
  redis:7-alpine

docker run -d \
  --name librtmp2-server \
  --network openrtmp \
  -p 1935:1935 \
  -p 8080:8080 \
  -e LRTMP2_API_TOKEN=$LRTMP2_API_TOKEN \
  -e LRTMP2_DB=/data/server.db \
  -v librtmp2-server-data:/data \
  ghcr.io/openrtmp/librtmp2-server:latest

docker run -d \
  --name librtmp2-server-panel \
  --network openrtmp \
  -p 8000:8000 \
  -e LRTMP2_API_URL=http://librtmp2-server:8080 \
  -e LRTMP2_STATS_URL=http://localhost:8080 \
  -e LRTMP2_API_TOKEN=$LRTMP2_API_TOKEN \
  -e LRTMP2_DOMAIN=localhost \
  -e PASSWORD=$PANEL_PASSWORD \
  -e SECRET_KEY=$PANEL_SECRET \
  -e RATELIMIT_STORAGE_URI=redis://librtmp2-panel-redis:6379/0 \
  ghcr.io/openrtmp/librtmp2-server-panel:latest

# RTMP:  rtmp://localhost:1935/live
# API:   http://localhost:8080/api/v1/health
# Panel: http://localhost:8000</code></pre>
        </div>

        <div class="download-card download-card--wide" id="docker-compose">
          <h3>&#128051; Docker: server + panel (<code>docker compose</code>)</h3>
          <p>Same stack via the panel repo's <code>docker-compose.yml</code> (server, panel, Redis). Set secrets in <code>.env</code> <em>before</em> the first start.</p>
          <pre><code>git clone https://github.com/OpenRTMP/librtmp2-server-panel.git
cd librtmp2-server-panel
cp .env.example .env

# Edit .env — at minimum set:
#   LRTMP2_API_TOKEN  (openssl rand -hex 32)
#   PASSWORD          (panel login, 12+ chars)
#   SECRET_KEY        (python3 -c "import secrets; print(secrets.token_hex(32))")
#   LRTMP2_DOMAIN     (public host/IP for RTMP URLs)

docker compose up -d</code></pre>
          <p>The shared <code>LRTMP2_API_TOKEN</code> is seeded into the server's SQLite database on first startup. See the <a href="/docs/#panel">panel docs</a> for all environment variables.</p>
        </div>

        <div class="download-card">
          <h3>&#127912; librtmp2-server-panel (source)</h3>
          <p>Flask web UI for stream management. Can connect to an already-running server (Docker or native). Default login is enabled &mdash; set <code>PASSWORD</code> and <code>SECRET_KEY</code> in <code>.env</code>.</p>
          <pre><code>git clone https://github.com/OpenRTMP/librtmp2-server-panel.git
cd librtmp2-server-panel
cp .env.example .env   # set LRTMP2_API_TOKEN, PASSWORD, etc.
pip install -r requirements.txt
python3 app.py</code></pre>
        </div>

      </div>
    </div>
  </section>

  <section>
    <div class="container">
      <div class="cta">
        <h2>Need configuration details?</h2>
        <p>RTMPS setup, stream keys, REST API endpoints, and panel environment variables are documented on the docs page.</p>
        <div class="hero-actions" style="margin-bottom:0;">
          <a href="/docs/" class="btn btn-ghost">Read the Docs</a>
          <a href="https://github.com/OpenRTMP/librtmp2-server" target="_blank" rel="noopener" class="btn btn-primary">librtmp2-server on GitHub</a>
        </div>
      </div>
    </div>
  </section>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
