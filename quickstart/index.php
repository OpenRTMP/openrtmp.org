<?php
$page = 'quickstart';
$pageTitle = 'Run a self-hosted RTMP server with Docker — OpenRTMP Quickstart';
$pageDescription = 'Deploy OpenRTMP server, Redis, and the web control panel in about five minutes using published Docker images, then publish from OBS.';
$canonicalPath = '/quickstart/';
$structuredData = [
  '@context' => 'https://schema.org',
  '@type' => 'HowTo',
  'name' => 'Run OpenRTMP with Docker and publish from OBS',
  'description' => $pageDescription,
  'totalTime' => 'PT5M',
  'tool' => [
    ['@type' => 'HowToTool', 'name' => 'Docker with Docker Compose'],
    ['@type' => 'HowToTool', 'name' => 'OpenSSL and Python 3 for secret generation']
  ],
  'step' => [
    ['@type' => 'HowToStep', 'name' => 'Clone the panel repository', 'url' => 'https://openrtmp.org/quickstart/#clone'],
    ['@type' => 'HowToStep', 'name' => 'Generate secure environment values', 'url' => 'https://openrtmp.org/quickstart/#secrets'],
    ['@type' => 'HowToStep', 'name' => 'Start the Docker stack', 'url' => 'https://openrtmp.org/quickstart/#start'],
    ['@type' => 'HowToStep', 'name' => 'Create a stream in the panel', 'url' => 'https://openrtmp.org/quickstart/#create-stream'],
    ['@type' => 'HowToStep', 'name' => 'Publish from OBS', 'url' => 'https://openrtmp.org/quickstart/#obs']
  ]
];
include __DIR__ . '/../includes/header.php';
?>

<main>
  <div class="page-hero container">
    <span class="eyebrow">Docker quickstart</span>
    <h1>Run OpenRTMP in about five minutes</h1>
    <p>Start the RTMP server, web panel, and Redis from published images. No Rust toolchain, Python environment, or neighboring source checkout is required.</p>
  </div>

  <section class="content-section" style="padding-top: 0;">
    <div class="container article-layout">
      <article class="prose">
        <div class="callout warning">
          <strong>Alpha software:</strong> this guide is intended for evaluation and tested self-hosted deployments. Pin image versions and validate publishing, playback, authentication, and recovery before critical production use.
        </div>

        <h2 id="requirements">Requirements</h2>
        <ul>
          <li>Docker with the Compose plugin</li>
          <li>Git</li>
          <li>OpenSSL and Python 3 for the copy-and-paste secret generator</li>
        </ul>
        <p>Windows users can generate equivalent random values manually and place them in <code>.env</code>.</p>

        <h2 id="clone">1. Clone the deployment repository</h2>
        <pre><code>git clone https://github.com/OpenRTMP/librtmp2-server-panel.git
cd librtmp2-server-panel</code></pre>
        <p>The quickstart uses <code>compose.quickstart.yml</code>. Unlike the development Compose file, it pulls published images and does not expect a sibling <code>librtmp2-server</code> checkout.</p>

        <h2 id="secrets">2. Generate the required secrets</h2>
        <pre><code>API_TOKEN="$(openssl rand -hex 32)"
PANEL_SECRET="$(python3 -c 'import secrets; print(secrets.token_hex(32))')"
PANEL_PASSWORD="$(openssl rand -base64 24 | tr -d '\n')"

cat &gt; .env &lt;&lt;EOF
LRTMP2_API_TOKEN=${API_TOKEN}
LRTMP2_DOMAIN=localhost
LRTMP2_STATS_URL=http://localhost:8080
USERNAME=admin
PASSWORD=${PANEL_PASSWORD}
SECRET_KEY=${PANEL_SECRET}
REQUIRE_LOGIN=True
EOF

printf 'Save this panel password: %s\n' "${PANEL_PASSWORD}"</code></pre>
        <p>The API token is supplied to both services. On the server's first startup it is stored in SQLite. Changing only the Compose environment later does not automatically rotate the stored token.</p>

        <h2 id="start">3. Start the stack</h2>
        <pre><code>docker compose -f compose.quickstart.yml up -d
docker compose -f compose.quickstart.yml ps</code></pre>
        <p>The services are available at:</p>
        <table>
          <thead><tr><th>Service</th><th>Address</th></tr></thead>
          <tbody>
            <tr><td>Web panel</td><td><code>http://localhost:8000</code></td></tr>
            <tr><td>HTTP API</td><td><code>http://localhost:8080</code></td></tr>
            <tr><td>RTMP listener</td><td><code>rtmp://localhost:1935</code></td></tr>
          </tbody>
        </table>
        <p>Verify the API:</p>
        <pre><code>curl http://localhost:8080/api/v1/health</code></pre>
        <p>A successful response includes <code>"status":"ok"</code>.</p>

        <h2 id="create-stream">4. Create a stream</h2>
        <ol>
          <li>Open <code>http://localhost:8000</code>.</li>
          <li>Sign in as <code>admin</code> with the generated panel password.</li>
          <li>Create a new stream.</li>
          <li>Copy the generated publish URL and <code>publish_key</code>.</li>
        </ol>
        <p>The panel also creates separate playback and statistics keys. Do not publish those keys publicly unless the intended audience should have that access.</p>

        <h2 id="obs">5. Publish from OBS</h2>
        <p>In OBS, open <strong>Settings &rarr; Stream</strong>, select a custom service, and enter:</p>
        <table>
          <tbody>
            <tr><th>Server</th><td><code>rtmp://localhost:1935/live</code></td></tr>
            <tr><th>Stream key</th><td>The generated <code>publish_key</code></td></tr>
          </tbody>
        </table>
        <p>Start streaming, then return to the panel to view bitrate, codec, resolution, RTT, uptime, and connected players.</p>

        <h2 id="internet">Moving from localhost to a server</h2>
        <ul>
          <li>Set <code>LRTMP2_DOMAIN</code> to the public hostname or IP.</li>
          <li>Set <code>LRTMP2_STATS_URL</code> to the browser-reachable HTTPS API URL.</li>
          <li>Restrict direct access to port <code>8080</code>; expose only the routes your integrations need through a reverse proxy.</li>
          <li>Serve the panel over HTTPS and enable secure session cookies.</li>
          <li>Enable RTMPS with a valid certificate when encrypted ingest is required.</li>
          <li>Replace moving <code>latest</code> image tags with tested release tags.</li>
        </ul>

        <h2 id="troubleshooting">Common problems</h2>
        <div class="faq-item">
          <h3>The panel cannot reach the API</h3>
          <p>Confirm that both containers are healthy and that the panel uses <code>http://openrtmp-server:8080</code> internally. Do not use <code>localhost</code> for container-to-container traffic.</p>
        </div>
        <div class="faq-item">
          <h3>The API returns unauthorized</h3>
          <p>The panel and server must use the same <code>LRTMP2_API_TOKEN</code>. When reusing an existing server volume, remember that the original token is already stored in SQLite.</p>
        </div>
        <div class="faq-item">
          <h3>OBS cannot connect</h3>
          <p>Check that port <code>1935/tcp</code> is reachable, use the panel's exact application and publish key, and inspect <code>docker compose -f compose.quickstart.yml logs openrtmp-server</code>.</p>
        </div>
        <div class="faq-item">
          <h3>The copied URLs contain localhost</h3>
          <p>Set <code>LRTMP2_DOMAIN</code> and <code>LRTMP2_STATS_URL</code> to addresses reachable by the actual publishers and browsers.</p>
        </div>

        <h2 id="stop">Stop or remove the stack</h2>
        <pre><code># Stop containers and keep the SQLite volume
docker compose -f compose.quickstart.yml down

# Also delete stored server data
docker compose -f compose.quickstart.yml down -v</code></pre>

        <div class="cta compact-cta">
          <h2>Next steps</h2>
          <p>Read the production-oriented deployment guide, configure RTMPS, or integrate the Rust crate directly.</p>
          <div class="hero-actions" style="margin-bottom:0;">
            <a href="/guides/self-hosted-rtmp-server-docker/" class="btn btn-primary">Deployment guide</a>
            <a href="/guides/rtmps-server-obs/" class="btn btn-ghost">RTMPS with OBS</a>
            <a href="https://github.com/OpenRTMP/librtmp2" target="_blank" rel="noopener" class="btn btn-ghost">Use librtmp2</a>
          </div>
        </div>
      </article>

      <aside class="toc-card" aria-label="On this page">
        <strong>On this page</strong>
        <a href="#requirements">Requirements</a>
        <a href="#clone">Clone</a>
        <a href="#secrets">Secrets</a>
        <a href="#start">Start</a>
        <a href="#create-stream">Create a stream</a>
        <a href="#obs">Publish from OBS</a>
        <a href="#internet">Internet deployment</a>
        <a href="#troubleshooting">Troubleshooting</a>
      </aside>
    </div>
  </section>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
