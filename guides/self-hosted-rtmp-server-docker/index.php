<?php
$page = 'guides';
$pageTitle = 'Self-host an RTMP server with Docker and OBS — OpenRTMP';
$pageDescription = 'Deploy a private RTMP server and web panel with Docker, create stream keys, publish from OBS, and prepare OpenRTMP for an internet-facing host.';
$canonicalPath = '/guides/self-hosted-rtmp-server-docker/';
$ogType = 'article';
$structuredData = [
  '@context' => 'https://schema.org',
  '@type' => 'TechArticle',
  'headline' => 'Self-host an RTMP server with Docker and OBS',
  'description' => $pageDescription,
  'author' => ['@type' => 'Organization', 'name' => 'OpenRTMP'],
  'publisher' => ['@type' => 'Organization', 'name' => 'OpenRTMP'],
  'mainEntityOfPage' => 'https://openrtmp.org/guides/self-hosted-rtmp-server-docker/'
];
include __DIR__ . '/../../includes/header.php';
?>

<main>
  <div class="page-hero container article-hero">
    <span class="eyebrow">Docker &middot; OBS &middot; Self-hosted</span>
    <h1>Self-host an RTMP server with Docker</h1>
    <p>Use OpenRTMP when you want a focused private RTMP/RTMPS endpoint with stream-key authentication, REST API, live statistics, and a browser control panel.</p>
  </div>

  <section class="content-section" style="padding-top: 0;">
    <div class="container article-layout">
      <article class="prose">
        <div class="callout warning"><strong>Status:</strong> OpenRTMP is active alpha software. Test failure recovery, reconnect behavior, codecs, and every client you rely on before critical production use.</div>

        <h2 id="architecture">What the Docker stack runs</h2>
        <p>The quickstart stack separates protocol handling, application policy, and user interface:</p>
        <ul>
          <li><strong>librtmp2-server:</strong> RTMP listener, optional RTMPS listener, SQLite registry, key validation, REST API, and statistics.</li>
          <li><strong>librtmp2-server-panel:</strong> browser UI for creating streams, copying URLs and keys, and viewing live data.</li>
          <li><strong>Redis:</strong> shared rate-limit state for the multi-worker panel deployment.</li>
        </ul>
        <p>The protocol library is embedded in the server. You do not have to install Rust when using the published container images.</p>

        <h2 id="deploy">Deploy locally</h2>
        <p>Follow the <a href="/quickstart/">five-minute quickstart</a> for the complete copy-and-paste commands. The short path is:</p>
        <pre><code>git clone https://github.com/OpenRTMP/librtmp2-server-panel.git
cd librtmp2-server-panel
# Create .env with LRTMP2_API_TOKEN, PASSWORD, and SECRET_KEY.
docker compose -f compose.quickstart.yml up -d</code></pre>
        <p>The stack exposes:</p>
        <table>
          <thead><tr><th>Port</th><th>Purpose</th><th>Public exposure</th></tr></thead>
          <tbody>
            <tr><td><code>1935/tcp</code></td><td>RTMP publishers and players</td><td>Expose when remote RTMP clients need it</td></tr>
            <tr><td><code>8000/tcp</code></td><td>Web control panel</td><td>Prefer HTTPS through a reverse proxy</td></tr>
            <tr><td><code>8080/tcp</code></td><td>REST API and statistics</td><td>Restrict; proxy only required routes</td></tr>
            <tr><td><code>1940/tcp</code> / <code>1941/tcp</code></td><td>Cluster control and media mesh</td><td>Only between peers when <code>CLUSTER_ENABLED=true</code>; see the <a href="/guides/rtmp-server-ha-clustering/">HA guide</a></td></tr>
          </tbody>
        </table>

        <h2 id="stream-keys">Create a stream and understand its keys</h2>
        <p>Each stream receives separate credentials:</p>
        <table>
          <thead><tr><th>Key</th><th>Use</th></tr></thead>
          <tbody>
            <tr><td><code>publish_key</code></td><td>Allows OBS, FFmpeg, or another publisher to send the stream</td></tr>
            <tr><td><code>play_key</code></td><td>Allows a player to receive the stream</td></tr>
            <tr><td><code>stats_key</code></td><td>Allows access to that stream's monitoring endpoint</td></tr>
          </tbody>
        </table>
        <p>Keeping these concerns separate means a monitoring integration does not need permission to publish or play media.</p>

        <h2 id="obs">Publish from OBS</h2>
        <ol>
          <li>Create a stream in the panel.</li>
          <li>Open OBS <strong>Settings &rarr; Stream</strong>.</li>
          <li>Select a custom service.</li>
          <li>Use the panel's RTMP server URL, normally <code>rtmp://host:1935/live</code>.</li>
          <li>Use the generated <code>publish_key</code> as the stream key.</li>
        </ol>
        <p>When the publisher connects, the panel should show bitrate, codec, resolution, frame rate, RTT, uptime, and connected player data.</p>

        <h2 id="monitoring">Monitoring and NOALBS-style statistics</h2>
        <p>OpenRTMP offers two statistics formats:</p>
        <ul>
          <li><code>/stats?key=&lt;stats_key&gt;</code> returns JSON.</li>
          <li><code>/stats-nginx?key=&lt;stats_key&gt;</code> returns nginx-rtmp-compatible XML for existing tooling.</li>
        </ul>
        <p>The XML compatibility endpoint is useful when an integration expects the classic nginx-rtmp statistics shape. New integrations should generally prefer JSON.</p>

        <h2 id="production">Internet-facing deployment checklist</h2>
        <ul class="check-list">
          <li>Use strong random values for the API token, panel password, and Flask session secret.</li>
          <li>Set <code>LRTMP2_DOMAIN</code> to the public hostname clients actually use.</li>
          <li>Put the panel behind HTTPS and enable secure cookies.</li>
          <li>Do not expose the administrative REST API broadly.</li>
          <li>Enable RTMPS when publishers need encrypted transport.</li>
          <li>For multi-node HA, follow the <a href="/guides/rtmp-server-ha-clustering/">clustering guide</a> and give each node its own SQLite volume.</li>
          <li>Back up the persistent SQLite volume.</li>
          <li>Pin tested container versions instead of using moving tags.</li>
          <li>Apply connection, request-body, and rate limits appropriate for the host.</li>
          <li>Test container restart, host reboot, publisher reconnect, and late player join behavior.</li>
        </ul>

        <h2 id="fit">When OpenRTMP is the right fit</h2>
        <p>OpenRTMP is a strong candidate for private ingest, focused RTMP experiments, custom control planes, stream-key management, or integrations that want Rust and a small understandable architecture.</p>
        <p>Choose a broader media platform when you need built-in HLS delivery, recording, transcoding, push relay, a public viewer website, or many non-RTMP protocols without writing additional services.</p>

        <h2 id="troubleshooting">Troubleshooting</h2>
        <h3>Panel loads, but stream creation fails</h3>
        <p>Check that the panel and server share the same API token and that the server container is healthy. A reused SQLite volume may still contain an older token.</p>
        <h3>OBS times out</h3>
        <p>Verify the public firewall and Docker port mapping for <code>1935/tcp</code>. Confirm the application path and use the publish key, not the play or stats key.</p>
        <h3>Statistics links are unreachable from another computer</h3>
        <p>Replace localhost values in <code>LRTMP2_DOMAIN</code> and <code>LRTMP2_STATS_URL</code> with browser-reachable addresses.</p>

        <div class="cta compact-cta">
          <h2>Deploy the stack</h2>
          <p>Use the dedicated quickstart, then return here for the production checklist.</p>
          <div class="hero-actions" style="margin-bottom:0;">
            <a href="/quickstart/" class="btn btn-primary">Open quickstart</a>
            <a href="/guides/rtmps-server-obs/" class="btn btn-ghost">Add RTMPS</a>
            <a href="/guides/rtmp-server-ha-clustering/" class="btn btn-ghost">Add clustering</a>
          </div>
        </div>
      </article>

      <aside class="toc-card" aria-label="On this page">
        <strong>On this page</strong>
        <a href="#architecture">Architecture</a>
        <a href="#deploy">Deploy</a>
        <a href="#stream-keys">Stream keys</a>
        <a href="#obs">OBS</a>
        <a href="#monitoring">Monitoring</a>
        <a href="#production">Production checklist</a>
        <a href="#fit">When to use it</a>
        <a href="#troubleshooting">Troubleshooting</a>
      </aside>
    </div>
  </section>
</main>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
