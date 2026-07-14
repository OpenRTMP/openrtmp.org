<?php
$page = 'docs';
$pageTitle = 'Documentation — OpenRTMP';
$pageDescription = 'Getting started with librtmp2, librtmp2-server, and librtmp2-server-panel: Cargo builds, host callbacks, Docker deployment, REST API, and the web panel.';
include __DIR__ . '/../includes/header.php';
?>

<main>
  <div class="page-hero container">
    <h1>Documentation</h1>
    <p>Everything you need to embed <code>librtmp2</code> in a media pipeline, run the reference server, or deploy the full stack with Docker and the web panel.</p>
  </div>

  <section style="padding-top: 0;">
    <div class="container docs-layout">

      <aside class="docs-nav">
        <h4>On this page</h4>
        <ul>
          <li><a href="#status">Project Status</a></li>
          <li><a href="#getting-started">Getting Started</a></li>
          <li><a href="#state-machine">Connection State Machine</a></li>
          <li><a href="#callbacks">Host Callbacks</a></li>
          <li><a href="#layers">Module Reference</a></li>
          <li><a href="#server">librtmp2-server</a></li>
          <li><a href="#panel">librtmp2-server-panel</a></li>
          <li><a href="#docker">Docker Deployment</a></li>
          <li><a href="#abi">API &amp; Versioning</a></li>
        </ul>
        <h4>Repositories</h4>
        <ul>
          <li><a href="https://github.com/OpenRTMP/librtmp2" target="_blank" rel="noopener">librtmp2</a></li>
          <li><a href="https://github.com/OpenRTMP/librtmp2-server" target="_blank" rel="noopener">librtmp2-server</a></li>
          <li><a href="https://github.com/OpenRTMP/librtmp2-server-panel" target="_blank" rel="noopener">librtmp2-server-panel</a></li>
        </ul>
      </aside>

      <div class="docs-content">

        <div class="callout callout-warn" id="status">
          <strong>Alpha software.</strong> All three projects (<code>librtmp2</code>, <code>librtmp2-server</code>, <code>librtmp2-server-panel</code>) are in active early development. APIs, Docker images, and configuration may change without notice. Pin to a crates.io version (currently <code>0.3.1</code> for the library) and test your OBS/FFmpeg workflow before relying on it for critical streams.
        </div>

        <h2 id="getting-started">Getting Started</h2>
        <p><code>librtmp2</code> is a <strong>protocol library</strong> &mdash; it decodes RTMP and Enhanced RTMP (E-RTMP v1/v2) on the wire and calls back into your application. It deliberately contains no HTTP server, no authentication policy, and no stream storage. If you want a ready-to-run endpoint instead of embedding the crate, use <a href="#server">librtmp2-server</a>.</p>
        <p>Add <code>librtmp2</code> from <a href="https://crates.io/crates/librtmp2" target="_blank" rel="noopener">crates.io</a> to your <code>Cargo.toml</code>:</p>
        <pre><code>[dependencies.librtmp2]
version = "0.3.1"</code></pre>
        <p>Or clone and build locally for development (requires Rust 1.93+):</p>
        <pre><code>git clone https://github.com/OpenRTMP/librtmp2.git
cd librtmp2
cargo build --release
cargo test</code></pre>
        <p>Minimal server example &mdash; listen, accept publish, log frames (<code>examples/minimal_server.rs</code>):</p>
        <pre><code>use librtmp2::server::Server;
use librtmp2::types::*;

fn on_frame(frame: &Frame) {
    println!("frame: size={}", frame.size);
}

let config = ServerConfig {
    max_connections: 16,
    chunk_size: 4096,
    tls_enabled: 0,
    tls_cert_file: std::ptr::null(),
    tls_key_file: std::ptr::null(),
    tls_ca_file: std::ptr::null(),
    tls_insecure: 0,
};

let mut server = Server::new(config)?;
server.on_frame_cb = Some(on_frame);
server.listen("0.0.0.0:1935")?;

while running {
    server.poll(100)?;
}</code></pre>
        <p>For sanitizer builds during development:</p>
        <pre><code>RUSTFLAGS="-Z sanitizer=address" cargo test
RUSTFLAGS="-Z sanitizer=undefined" cargo test</code></pre>

        <h2 id="state-machine">Connection State Machine</h2>
        <p>Every connection moves through a fixed set of states as the handshake, capability negotiation, and stream lifecycle progress:</p>
        <pre><code>TCP_ACCEPTED &rarr; HANDSHAKE &rarr; CONNECTED &rarr; [CAPS_NEGOTIATED] &rarr; APP_CONNECTED &rarr; STREAM_CREATED &rarr; PUBLISHING | PLAYING &rarr; CLOSING &rarr; CLOSED</code></pre>
        <p><code>CAPS_NEGOTIATED</code> is the E-RTMP v2 capability exchange step between <code>CONNECTED</code> and <code>APP_CONNECTED</code>. Classic RTMP and E-RTMP v1 peers skip it entirely.</p>

        <h2 id="callbacks">Host Callbacks</h2>
        <p>The library never touches storage, auth, or transcoding &mdash; it decodes the wire and calls back into your application. In Rust, assign callbacks on the <code>Server</code> or <code>Client</code> object (e.g. <code>server.on_publish_cb = Some(...)</code>):</p>
        <div class="table-wrap">
          <table>
            <thead><tr><th>Callback</th><th>Fired when</th></tr></thead>
            <tbody>
              <tr><td><code>on_connect</code></td><td>peer completes the RTMP <code>connect</code> command</td></tr>
              <tr><td><code>on_publish</code></td><td>a publisher requests a stream key &mdash; return <code>false</code> to reject</td></tr>
              <tr><td><code>on_play</code></td><td>a viewer requests playback of a stream &mdash; return <code>false</code> to reject</td></tr>
              <tr><td><code>on_frame</code></td><td>a bounds-checked audio/video/script <code>Frame</code> is ready</td></tr>
              <tr><td><code>on_close</code></td><td>the connection is tearing down, for any reason</td></tr>
            </tbody>
          </table>
        </div>

        <h2 id="layers">Layer Reference</h2>
        <p>Ingest flows bottom-up through nine layers before reaching your callbacks. See the <a href="/#architecture">architecture overview</a> on the homepage for the full diagram. Key directories in <code>src/</code>:</p>
        <div class="table-wrap">
          <table>
            <thead><tr><th>Directory</th><th>Responsibility</th></tr></thead>
            <tbody>
              <tr><td><code>core/</code></td><td>alloc hook, growable buffers, byte helpers, logging, errors</td></tr>
              <tr><td><code>handshake/</code></td><td>C0/C1/C2 &harr; S0/S1/S2, partial-read buffering, version detection</td></tr>
              <tr><td><code>chunk/</code></td><td>chunk_reader/writer, per-csid chunk_state, SetChunkSize/Abort</td></tr>
              <tr><td><code>message/</code></td><td>reassembled message dispatch &amp; AMF command decode/encode</td></tr>
              <tr><td><code>amf/</code></td><td>AMF0 (mandatory) and AMF3 (optional)</td></tr>
              <tr><td><code>flv/</code></td><td>FLV audio/video/script tag parsing</td></tr>
              <tr><td><code>ertmp/</code></td><td>E-RTMP v1 (ExVideo/ExAudio, FourCC, HDR) + v2 (capsEx, reconnect, multitrack, ModEx)</td></tr>
              <tr><td><code>session/</code></td><td>connection object, state machine, stream bookkeeping</td></tr>
              <tr><td><code>server/</code> &amp; <code>client/</code></td><td>accept loop / per-connection poll &middot; outbound connect &amp; publish/play</td></tr>
            </tbody>
          </table>
        </div>

        <h2 id="server">librtmp2-server</h2>
        <p><a href="https://github.com/OpenRTMP/librtmp2-server" target="_blank" rel="noopener">librtmp2-server</a> is the reference RTMP/E-RTMP <strong>media server</strong> built on top of <code>librtmp2</code>. It is a separate repository and binary &mdash; the library itself stays free of any server loop, socket policy, or storage decisions.</p>
        <p>Unlike a bare <code>librtmp2</code> integration, the server adds application-layer features:</p>
        <div class="table-wrap">
          <table>
            <thead><tr><th>Feature</th><th>What it does</th></tr></thead>
            <tbody>
              <tr><td>RTMP listener</td><td>accepts publishers and players on <code>RTMP_BIND</code> (default <code>0.0.0.0:1935</code>) via the integrated <code>librtmp2</code> server</td></tr>
              <tr><td>RTMPS listener</td><td>optional second TLS listener on <code>RTMPS_BIND</code> (<code>1936</code>) <em>alongside</em> plaintext RTMP when <code>TLS_ENABLED=true</code></td></tr>
              <tr><td>SQLite persistence</td><td>streams, publishers, players, and stats stored in <code>LRTMP2_DB</code></td></tr>
              <tr><td>Per-stream keys</td><td>auto-generated <code>publish_key</code>, <code>play_key</code>, and <code>stats_key</code> &mdash; no public stream list without the exact key</td></tr>
              <tr><td>REST API</td><td>stream CRUD on <code>/api/v1/streams</code> with Bearer token auth (axum, port <code>8080</code>)</td></tr>
              <tr><td>Stats endpoints</td><td><code>/stats?key=&lt;stats_key&gt;</code> (JSON) and <code>/stats-nginx?key=&lt;stats_key&gt;</code> (nginx-rtmp-compatible XML)</td></tr>
              <tr><td>Frame relay</td><td>forwards publisher frames to all matching players, GOP-aware</td></tr>
            </tbody>
          </table>
        </div>
        <p>Build and run natively:</p>
        <pre><code>git clone https://github.com/OpenRTMP/librtmp2-server.git
cd librtmp2-server
cargo build --release
cp .env.example .env
LRTMP2_DB=./server.db ./target/release/librtmp2-server</code></pre>
        <p>On first startup the server generates an API bearer token, stores it in SQLite, and prints it once to stderr. Use that token for <code>Authorization: Bearer &lt;token&gt;</code> on REST API calls.</p>
        <p><strong>Publish with OBS:</strong> Server <code>rtmp://&lt;host&gt;/live</code>, Stream Key = the <code>publish_key</code> returned by <code>POST /api/v1/streams</code>.</p>
        <pre><code>curl -X POST http://localhost:8080/api/v1/streams \
  -H "Authorization: Bearer &lt;api-token&gt;" \
  -H "Content-Type: application/json" \
  -d '{"id":"mystream","name":"My Live Stream","app":"live"}'</code></pre>

        <h2 id="panel">librtmp2-server-panel</h2>
        <p><a href="https://github.com/OpenRTMP/librtmp2-server-panel" target="_blank" rel="noopener">librtmp2-server-panel</a> is a Flask web UI that talks to the server's REST API. It does not implement RTMP itself &mdash; it manages streams, copies URLs, and polls live stats.</p>
        <div class="table-wrap">
          <table>
            <thead><tr><th>Feature</th><th>Description</th></tr></thead>
            <tbody>
              <tr><td>Stream management</td><td>create and delete streams via <code>/api/v1/streams</code></td></tr>
              <tr><td>One-click copy</td><td>publish URL, stream key, play URL, and stats URL</td></tr>
              <tr><td>Live stats</td><td>bitrate, resolution, codec, uptime, RTT polled from <code>/stats?key=...</code></td></tr>
              <tr><td>Login gate</td><td>optional admin login (<code>REQUIRE_LOGIN=True</code> by default)</td></tr>
              <tr><td>Security</td><td>CSRF protection, rate limiting (Redis-backed in Docker), encrypted key display</td></tr>
            </tbody>
          </table>
        </div>
        <p>Key environment variables (see <code>.env.example</code> in the panel repo):</p>
        <div class="table-wrap">
          <table>
            <thead><tr><th>Variable</th><th>Description</th></tr></thead>
            <tbody>
              <tr><td><code>LRTMP2_API_URL</code></td><td>server HTTP API base URL (internal; e.g. <code>http://openrtmp-server:8080</code> in Docker)</td></tr>
              <tr><td><code>LRTMP2_API_TOKEN</code></td><td>Bearer token &mdash; must match the server's API token</td></tr>
              <tr><td><code>LRTMP2_DOMAIN</code></td><td>public host/IP for RTMP URLs shown to users</td></tr>
              <tr><td><code>LRTMP2_STATS_URL</code></td><td>browser-reachable stats URL (defaults to <code>LRTMP2_API_URL</code>)</td></tr>
              <tr><td><code>PASSWORD</code> / <code>SECRET_KEY</code></td><td>panel login password and Flask session secret</td></tr>
            </tbody>
          </table>
        </div>
        <p>Open the panel at <code>http://localhost:8000</code> after starting (default credentials in <code>.env.example</code>: user <code>admin</code>).</p>

        <h2 id="docker">Docker Deployment</h2>
        <p>Both server and panel publish prebuilt images to GitHub Container Registry. See also the <a href="/download/">download page</a> for copy-paste commands.</p>

        <h3>Server only</h3>
        <p>Quickest path &mdash; auto-generated API token on first start:</p>
        <pre><code>docker run -d \
  --name librtmp2-server \
  -p 1935:1935 \
  -p 8080:8080 \
  # -p 1936:1936   # RTMPS — only when TLS_ENABLED=true
  -v librtmp2-server-data:/data \
  ghcr.io/openrtmp/librtmp2-server:latest

docker logs librtmp2-server   # copy API token from first-start output</code></pre>
        <p>Available tags: <code>latest</code>, <code>beta</code>, <code>alpha</code>, and pinned versions (e.g. <code>0.1.4</code>).</p>

        <h3>Full stack (server + panel + Redis)</h3>
        <p>The panel repo's <code>docker-compose.yml</code> runs all three services. Set secrets in <code>.env</code> <em>before</em> the first start so the server seeds the shared API token:</p>
        <pre><code>git clone https://github.com/OpenRTMP/librtmp2-server-panel.git
cd librtmp2-server-panel
cp .env.example .env
# Set LRTMP2_API_TOKEN, PASSWORD, SECRET_KEY, LRTMP2_DOMAIN
docker compose up -d</code></pre>
        <p>Ports exposed by default (from <code>librtmp2-server-panel/docker-compose.yml</code> and <code>librtmp2-server/.env.example</code>):</p>
        <div class="table-wrap">
          <table>
            <thead><tr><th>Port</th><th>Service</th></tr></thead>
            <tbody>
              <tr><td><code>1935</code></td><td>RTMP ingest / playback (<code>RTMP_BIND</code>)</td></tr>
              <tr><td><code>1936</code></td><td>RTMPS ingest / playback (<code>RTMPS_BIND</code>) &mdash; only when <code>TLS_ENABLED=true</code>; not exposed in the default compose file (uncomment <code>1936:1936</code> there)</td></tr>
              <tr><td><code>8080</code></td><td>HTTP API, stats, health check (<code>HTTP_BIND</code>)</td></tr>
              <tr><td><code>8000</code></td><td>Web panel</td></tr>
            </tbody>
          </table>
        </div>
        <p>To enable RTMPS alongside plaintext RTMP, set <code>LRTMP2_TLS_ENABLED=true</code> (or <code>TLS_ENABLED=true</code> in <code>.env</code>), mount cert/key files, expose port <code>1936</code>, and set <code>RTMPS_BIND=0.0.0.0:1936</code> as in <code>librtmp2-server/docker-compose.yml</code>. The panel shows <code>rtmps://</code> URLs only when <code>GET /api/v1/health</code> reports <code>rtmps_enabled: true</code> (and uses <code>LRTMP2_RTMPS_PORT</code>, default <code>1936</code>).</p>

        <h3>Panel against an existing server</h3>
        <p>If the server is already running (native or Docker), start only the panel image and point it at the server's HTTP API:</p>
        <pre><code>docker run -d \
  --name librtmp2-server-panel \
  -p 8000:8000 \
  -e LRTMP2_API_URL=http://&lt;server-host&gt;:8080 \
  -e LRTMP2_DOMAIN=&lt;public-host&gt; \
  -e LRTMP2_API_TOKEN=&lt;token&gt; \
  -e PASSWORD=&lt;panel-password&gt; \
  -e SECRET_KEY=&lt;random-secret&gt; \
  ghcr.io/openrtmp/librtmp2-server-panel:latest</code></pre>

        <h2 id="abi">API &amp; Versioning</h2>
        <p>Only the public <code>librtmp2</code> crate interface is the intended stable API surface. Everything under <code>src/**/*</code> that is not <code>pub</code> may change freely between releases.</p>
        <p><code>librtmp2</code> follows SemVer but remains on <code>0.x</code> while in alpha. Semantic-versioning guarantees begin at <code>1.0.0</code>. Pin a crates.io version (currently <code>0.3.1</code>) if you depend on a specific API shape.</p>
        <p><code>librtmp2-server</code> (currently <code>0.1.4</code>) and <code>librtmp2-server-panel</code> are also alpha &mdash; REST API shapes, environment variable names, and Docker images may evolve.</p>

      </div>
    </div>
  </section>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
