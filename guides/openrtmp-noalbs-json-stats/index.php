<?php
$page = 'guides';
$pageTitle = 'OpenRTMP JSON statistics with NOALBS — Setup guide';
$pageDescription = 'Connect NOALBS v2.19.0 or newer to OpenRTMP JSON statistics, understand stream keys, configure fallback XML stats, and troubleshoot common errors.';
$canonicalPath = '/guides/openrtmp-noalbs-json-stats/';
$ogType = 'article';
$structuredData = [
  '@context' => 'https://schema.org',
  '@type' => 'TechArticle',
  'headline' => 'Use OpenRTMP JSON statistics with NOALBS',
  'description' => $pageDescription,
  'author' => ['@type' => 'Organization', 'name' => 'OpenRTMP'],
  'mainEntityOfPage' => 'https://openrtmp.org/guides/openrtmp-noalbs-json-stats/'
];
include __DIR__ . '/../../includes/header.php';
?>

<main>
  <div class="page-hero container article-hero">
    <span class="eyebrow">NOALBS &middot; JSON statistics &middot; Monitoring</span>
    <h1>Use OpenRTMP JSON statistics with NOALBS</h1>
    <p>Connect NOALBS to the key-protected OpenRTMP statistics endpoint, configure bitrate and RTT triggers, and keep the nginx-compatible XML endpoint as a fallback for older NOALBS versions or existing integrations.</p>
  </div>

  <section class="content-section" style="padding-top: 0;">
    <div class="container article-layout">
      <article class="prose">
        <div class="callout"><strong>Released support:</strong> NOALBS <a href="https://github.com/NOALBS/nginx-obs-automatic-low-bitrate-switching/releases/tag/v2.19.0" target="_blank" rel="noopener">v2.19.0</a> includes the native <code>OpenRTMP</code> provider. It reads <code>/stats?key=...</code> directly and uses OpenRTMP's <code>bitrate_kbps</code> and <code>rtt_ms</code> values for scene switching.</div>

        <h2 id="requirements">What you need</h2>
        <ul class="check-list">
          <li>A running <code>librtmp2-server</code> instance with its HTTP listener reachable from NOALBS.</li>
          <li>A stream created through the OpenRTMP panel or REST API.</li>
          <li>The stream's <code>stats_key</code>.</li>
          <li>NOALBS v2.19.0 or newer for the native JSON provider.</li>
        </ul>
        <p>The OpenRTMP server and protocol stack are still alpha software. Test the exact publisher, statistics, reconnect, and scene-switching workflow before relying on it for a critical production stream.</p>

        <h2 id="create-stream">Create a stream and obtain its keys</h2>
        <p>The easiest option is <a href="https://github.com/OpenRTMP/librtmp2-server-panel" target="_blank" rel="noopener">librtmp2-server-panel</a>. Create a stream in the panel and copy its statistics URL or <code>stats_key</code>.</p>
        <p>You can also create a stream with the REST API:</p>
        <pre><code>curl -X POST http://openrtmp-server:8080/api/v1/streams \
  -H "Authorization: Bearer &lt;api_token&gt;" \
  -H "Content-Type: application/json" \
  -d '{"id":"mobile","name":"Mobile ingest","app":"live"}'</code></pre>
        <p>The response contains three separate credentials:</p>
        <pre><code>{
  "id": "mobile",
  "name": "Mobile ingest",
  "app": "live",
  "publish_key": "live_REDACTED_EXAMPLE",
  "play_key": "play_REDACTED_EXAMPLE",
  "stats_key": "sts_REDACTED_EXAMPLE",
  "enabled": true
}</code></pre>

        <h2 id="keys">Publish, play, and statistics keys</h2>
        <table>
          <thead><tr><th>Key</th><th>Purpose</th><th>Where to use it</th></tr></thead>
          <tbody>
            <tr><td><code>publish_key</code></td><td>Authorizes a publisher.</td><td>OBS, FFmpeg, Moblin, or another RTMP/RTMPS publishing client.</td></tr>
            <tr><td><code>play_key</code></td><td>Authorizes playback.</td><td>OBS media sources, players, or relay consumers that pull the stream.</td></tr>
            <tr><td><code>stats_key</code></td><td>Authorizes access to statistics for one stream.</td><td>NOALBS, monitoring tools, dashboards, and direct statistics checks.</td></tr>
          </tbody>
        </table>
        <div class="callout warning"><strong>Do not interchange the keys.</strong> NOALBS only needs the <code>stats_key</code>. Never place the API bearer token, <code>publish_key</code>, or <code>play_key</code> in the NOALBS statistics URL.</div>

        <h2 id="json-endpoint">Native JSON endpoint</h2>
        <p>The public per-stream endpoint is:</p>
        <pre><code>http://openrtmp-server:8080/stats?key=sts_REDACTED_EXAMPLE</code></pre>
        <p>While the publisher is live, the response is a flat JSON object suitable for the native NOALBS provider:</p>
        <pre><code>{
  "uptime": 18,
  "bitrate_kbps": 10565.5,
  "rtt_ms": 100.4,
  "bytes_in": 34618510,
  "video": {
    "codec": "hvc1",
    "width": 1920,
    "height": 1080,
    "fps": 30.0
  },
  "audio": {
    "codec": "Opus"
  }
}</code></pre>
        <p>The key-protected public endpoint intentionally omits the stream ID, display name, application name, players, and server-wide summary. Administrative API endpoints can expose more detail when authenticated with the API bearer token.</p>

        <h3>Statistics fields and units</h3>
        <table>
          <thead><tr><th>Field</th><th>Meaning</th></tr></thead>
          <tbody>
            <tr><td><code>bitrate_kbps</code></td><td>Current incoming publisher bitrate in kilobits per second. NOALBS compares this value with the <code>low</code> and <code>offline</code> thresholds.</td></tr>
            <tr><td><code>rtt_ms</code></td><td>Current publisher round-trip time in milliseconds. NOALBS compares this value with the <code>rtt</code> and <code>rttOffline</code> thresholds.</td></tr>
            <tr><td><code>uptime</code></td><td>Publisher connection duration in seconds.</td></tr>
            <tr><td><code>bytes_in</code></td><td>Total media bytes received from the current publisher connection.</td></tr>
          </tbody>
        </table>

        <h2 id="noalbs-config">Configure NOALBS v2.19.0 or newer</h2>
        <p>Add an <code>OpenRTMP</code> stream server entry to NOALBS. The complete <code>statsUrl</code> must include the stream's <code>stats_key</code>:</p>
        <pre><code>{
  "switcher": {
    "triggers": {
      "low": 2500,
      "offline": 500,
      "rtt": 200,
      "rttOffline": 2000
    },
    "streamServers": [
      {
        "name": "openrtmp",
        "priority": 0,
        "enabled": true,
        "streamServer": {
          "type": "OpenRTMP",
          "statsUrl": "http://openrtmp-server:8080/stats?key=sts_REDACTED_EXAMPLE"
        }
      }
    ]
  }
}</code></pre>
        <p>The thresholds belong in <code>switcher.triggers</code>, not inside the provider object:</p>
        <ul>
          <li><code>low</code>: switch to the low-bitrate scene when <code>bitrate_kbps</code> is at or below this value.</li>
          <li><code>offline</code>: switch to the offline scene when a positive <code>bitrate_kbps</code> is at or below this value.</li>
          <li><code>rtt</code>: switch to the low-bitrate scene when <code>rtt_ms</code> is at or above this value.</li>
          <li><code>rttOffline</code>: switch to the offline scene when <code>rtt_ms</code> is at or above this value.</li>
        </ul>
        <p>If the stream is offline, the endpoint does not return the live JSON object. The native provider treats a non-successful, unreachable, or non-JSON response as offline.</p>

        <h2 id="xml-fallback">nginx-compatible XML fallback</h2>
        <p>Use the XML endpoint when running an older NOALBS release, preserving an existing <code>Nginx</code> provider configuration, or testing compatibility with software that expects nginx-rtmp statistics:</p>
        <pre><code>http://openrtmp-server:8080/stats-nginx?key=sts_REDACTED_EXAMPLE</code></pre>
        <p>OpenRTMP redacts the real application and stream names in the public XML response. Therefore, the NOALBS provider must use the fixed values <code>live</code> and <code>stream</code>:</p>
        <pre><code>{
  "name": "openrtmp-xml",
  "priority": 0,
  "enabled": true,
  "streamServer": {
    "type": "Nginx",
    "statsUrl": "http://openrtmp-server:8080/stats-nginx?key=sts_REDACTED_EXAMPLE",
    "application": "live",
    "key": "stream"
  }
}</code></pre>
        <p>The provider's <code>key</code> value above is the literal redacted stream name <code>stream</code>. It is not the OpenRTMP <code>stats_key</code>; that credential remains inside <code>statsUrl</code>.</p>

        <h2 id="networking">Choose the correct host</h2>
        <p>The URL must be reachable from the machine or container running NOALBS. A URL that works in your desktop browser may still fail from inside a container.</p>
        <table>
          <thead><tr><th>Deployment</th><th>Recommended host in <code>statsUrl</code></th></tr></thead>
          <tbody>
            <tr><td>NOALBS and OpenRTMP on the same Docker network</td><td>Use the OpenRTMP service or container DNS name, for example <code>http://openrtmp-server:8080/...</code>.</td></tr>
            <tr><td>NOALBS on the Docker host</td><td>Use the published host port, for example <code>http://127.0.0.1:8080/...</code>.</td></tr>
            <tr><td>NOALBS on another server</td><td>Use a private network address, VPN hostname, or protected public HTTPS hostname that the NOALBS machine can resolve and reach.</td></tr>
            <tr><td>NOALBS in a separate container</td><td>Do not use <code>localhost</code> unless OpenRTMP shares the same network namespace. Inside a normal container, <code>localhost</code> points back to NOALBS itself.</td></tr>
          </tbody>
        </table>
        <p>Prefer the internal service name when both applications share a trusted Docker network. Prefer the public HTTPS URL when NOALBS runs remotely and the endpoint is intentionally exposed through a reverse proxy.</p>

        <h2 id="security">Protect statistics keys</h2>
        <ul class="check-list">
          <li>Replace statistics keys with <code>sts_REDACTED</code> before posting screenshots, logs, configuration files, or issue reports.</li>
          <li>Do not commit a real <code>statsUrl</code> containing a key to a public repository.</li>
          <li>Redact query strings in reverse-proxy access logs and monitoring alerts where possible.</li>
          <li>Rotate or recreate a stream if its statistics key has been exposed.</li>
          <li>Use HTTPS whenever the statistics request crosses an untrusted network.</li>
        </ul>

        <h2 id="troubleshooting">Troubleshooting</h2>
        <h3>401 Unauthorized</h3>
        <p>Confirm that the query parameter contains the stream's exact <code>stats_key</code>. Also check whether a reverse proxy has added its own authentication requirement or removed the query string while rewriting the request.</p>

        <h3>404 Not Found</h3>
        <p>Verify the endpoint path and HTTP port. Test <code>/stats?key=...</code> directly against the OpenRTMP HTTP listener before testing the reverse-proxy URL. A proxy location that only forwards another prefix will return 404 even when OpenRTMP is healthy.</p>

        <h3>502 Bad Gateway</h3>
        <p>The reverse proxy cannot reach the OpenRTMP HTTP service. Check the upstream host, port, Docker network, container health, and whether the proxy is trying to use a public hostname that resolves back to itself. From a containerized proxy, use the correct internal service name and port.</p>

        <h3>NOALBS reports the server as unreachable</h3>
        <p>Run the request from the same machine or container as NOALBS. Check DNS resolution, firewall rules, TLS certificate trust, and whether <code>localhost</code> refers to the wrong container. Keep the query string intact.</p>

        <h3>The URL works publicly but not internally</h3>
        <p>Hairpin NAT, split DNS, or reverse-proxy routing may make the public hostname unusable from the internal network. Use an internal Docker DNS name, private IP, or internal DNS record in NOALBS while keeping the public URL for browsers and remote integrations.</p>

        <h3>The XML provider shows zero bitrate or offline</h3>
        <p>Use <code>application: "live"</code> and <code>key: "stream"</code> exactly. Do not put the real OpenRTMP application or stream ID in those fields. Confirm that the publisher is live and that the URL points to <code>/stats-nginx</code>, not <code>/stats</code>.</p>

        <div class="cta compact-cta">
          <h2>Test the endpoint before enabling switching</h2>
          <p>Open the sanitized URL with <code>curl</code>, confirm live bitrate and RTT values, then start NOALBS and verify each threshold with non-critical scenes.</p>
          <div class="hero-actions" style="margin-bottom:0;">
            <a href="https://github.com/OpenRTMP/librtmp2-server#http-api" target="_blank" rel="noopener" class="btn btn-primary">Open server documentation</a>
            <a href="https://github.com/NOALBS/nginx-obs-automatic-low-bitrate-switching/tree/v2.19.0" target="_blank" rel="noopener" class="btn btn-ghost">View NOALBS v2.19.0</a>
          </div>
        </div>
      </article>

      <aside class="toc-card" aria-label="On this page">
        <strong>On this page</strong>
        <a href="#requirements">Requirements</a>
        <a href="#create-stream">Create a stream</a>
        <a href="#keys">Key types</a>
        <a href="#json-endpoint">JSON endpoint</a>
        <a href="#noalbs-config">NOALBS config</a>
        <a href="#xml-fallback">XML fallback</a>
        <a href="#networking">Networking</a>
        <a href="#security">Key security</a>
        <a href="#troubleshooting">Troubleshooting</a>
      </aside>
    </div>
  </section>
</main>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
