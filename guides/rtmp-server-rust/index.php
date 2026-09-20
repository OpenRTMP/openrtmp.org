<?php
$page = 'guides';
$pageTitle = 'Build an RTMP server in Rust with librtmp2 — OpenRTMP Developer Guide';
$pageDescription = 'Build a custom RTMP/RTMPS server or relay in Rust with librtmp2. Understand the protocol/library boundary, session flow, TLS, E-RTMP, FFI, and when to use librtmp2-server instead.';
$canonicalPath = '/guides/rtmp-server-rust/';
$ogType = 'article';
$structuredData = [
  '@context' => 'https://schema.org',
  '@type' => 'TechArticle',
  'headline' => 'Build an RTMP server in Rust with librtmp2',
  'description' => $pageDescription,
  'author' => ['@type' => 'Organization', 'name' => 'OpenRTMP'],
  'publisher' => ['@type' => 'Organization', 'name' => 'OpenRTMP'],
  'mainEntityOfPage' => 'https://openrtmp.org/guides/rtmp-server-rust/'
];
include_once __DIR__ . '/../../includes/header.php';
?>

<main>
  <div class="page-hero container article-hero">
    <span class="eyebrow">Rust &middot; RTMP server &middot; Developer</span>
    <h1>Build an RTMP server in Rust with librtmp2</h1>
    <p><code>librtmp2</code> provides RTMP/RTMPS protocol primitives and live session behavior so your Rust application can own authentication, routing, storage, APIs, transcoding policy, and everything around the media protocol.</p>
  </div>

  <section class="content-section" style="padding-top: 0;">
    <div class="container article-layout">
      <article class="prose">
        <div class="callout warning"><strong>Project status:</strong> librtmp2 is pre-1.0. Pin a tested version, read release notes, and validate protocol behavior against your exact publishers and players before building critical infrastructure around it.</div>

        <h2 id="library-vs-server">Library or ready-made server?</h2>
        <p>OpenRTMP deliberately separates the protocol library from the application server:</p>
        <table>
          <thead><tr><th>Use</th><th>Start with</th></tr></thead>
          <tbody>
            <tr><td>You are writing a custom Rust server, client, relay, gateway, plugin, or protocol tool</td><td><code>librtmp2</code></td></tr>
            <tr><td>You want a ready-to-run RTMP/RTMPS endpoint with stream keys, SQLite, API and statistics</td><td><code>librtmp2-server</code></td></tr>
            <tr><td>You also want a browser UI</td><td><code>librtmp2-server-panel</code></td></tr>
          </tbody>
        </table>

        <h2 id="install">Add librtmp2 to a Rust project</h2>
        <p>Use Cargo to add the current published crate:</p>
        <pre><code>cargo add librtmp2</code></pre>
        <p>RTMPS/TLS support is exposed through the crate's TLS feature and is enabled by default in current OpenRTMP releases. If your application intentionally needs a plaintext-only build, review the current crate features before disabling defaults.</p>
        <p>For applications, commit <code>Cargo.lock</code> so deployments use the dependency versions you tested.</p>

        <h2 id="architecture">Understand the boundary</h2>
        <p><code>librtmp2</code> is the media protocol layer, not an HTTP application framework or a transcoder. Its responsibilities include areas such as:</p>
        <ul>
          <li>RTMP handshake and chunking.</li>
          <li>Control messages and AMF commands.</li>
          <li>Publish/play session state.</li>
          <li>Publisher-to-player live relay primitives.</li>
          <li>RTMPS transport support.</li>
          <li>E-RTMP and FLV parser/serializer structures.</li>
          <li>C-compatible FFI for non-Rust hosts.</li>
        </ul>
        <p>Your application decides who may publish, how stream names map to tenants, where metadata is stored, what gets recorded, whether anything is transcoded, how observability works, and what happens when a publisher disconnects.</p>

        <h2 id="session">Typical session flow</h2>
        <p>A simplified live server flow looks like this:</p>
        <pre><code>TCP/TLS accept
    |
RTMP handshake
    |
connect
    |
createStream
    |
publish OR play
    |
media/control messages
    |
close / disconnect</code></pre>
        <p>E-RTMP-capable sessions can add capability negotiation and enhanced media packet types without requiring a separate application protocol.</p>

        <h2 id="minimal-design">A minimal custom-server design</h2>
        <p>A practical application around the library normally needs at least these layers:</p>
        <ol>
          <li><strong>Listener:</strong> accept TCP and optionally TLS connections.</li>
          <li><strong>RTMP session:</strong> delegate handshake, chunks, commands and media handling to librtmp2.</li>
          <li><strong>Authorization:</strong> validate app/stream names or publish/play credentials.</li>
          <li><strong>Registry:</strong> track active publishers and players.</li>
          <li><strong>Routing:</strong> map a publisher to the players subscribed to the same stream.</li>
          <li><strong>Observability:</strong> expose connection counts, bitrate, bytes, RTT where available, logs and health.</li>
          <li><strong>Lifecycle:</strong> define cleanup, reconnect and shutdown behavior.</li>
        </ol>
        <p>The <a href="https://github.com/OpenRTMP/librtmp2-server" target="_blank" rel="noopener">librtmp2-server source</a> is a concrete example of how OpenRTMP wraps the protocol library with application concerns such as configuration, SQLite, REST endpoints, authentication keys and statistics.</p>

        <h2 id="ertmp">Modern codecs and E-RTMP</h2>
        <p>If your application needs HEVC, AV1, Opus or E-RTMP v2 features, treat parser availability, session negotiation and complete live-path behavior as separate layers. Review the <a href="https://github.com/OpenRTMP/librtmp2#implementation-status" target="_blank" rel="noopener">implementation status</a> before depending on a specific advanced feature.</p>
        <p>For protocol background, read <a href="/guides/enhanced-rtmp-v2-explained/">Enhanced RTMP v2 explained</a>.</p>

        <h2 id="ffi">Using the C-compatible library</h2>
        <p>The crate is configured to produce Rust, dynamic-library and static-library outputs. This allows the same protocol work to be embedded into applications that cannot consume a Rust crate directly.</p>
        <p>When using the FFI, define ownership and thread-safety boundaries explicitly in the host application, and pin the ABI/API version you have tested.</p>

        <h2 id="security">Security boundaries</h2>
        <ul>
          <li>Use RTMPS when the transport needs TLS encryption.</li>
          <li>Apply connection limits and incomplete-handshake limits appropriate for internet-facing listeners.</li>
          <li>Do not treat a stream name as authentication unless your application explicitly designs it that way.</li>
          <li>Limit media-buffer and reassembly memory to avoid unbounded resource consumption.</li>
          <li>Fuzz parsers and test malformed chunk/message inputs.</li>
          <li>Keep application API authentication separate from publish/play authorization.</li>
        </ul>

        <h2 id="testing">Interoperability testing</h2>
        <p>At minimum, test with OBS and FFmpeg for publishing, a known-good RTMP player, late-player join, repeated publish/stop cycles, invalid credentials, malformed clients, RTMPS, and any E-RTMP codec your application intends to advertise.</p>
        <p>Add captured regression cases for every interoperability bug you fix. Protocol libraries gain more value from a growing corpus of real client behavior than from a feature list alone.</p>

        <h2 id="server">When to stop building and use librtmp2-server</h2>
        <p>If your custom application starts reimplementing a stream registry, API token, SQLite persistence, publish/play/stats keys, health endpoint, JSON statistics and panel integration, compare that work with the existing <code>librtmp2-server</code>. You can often extend or integrate the server instead of rebuilding its control plane.</p>

        <div class="cta compact-cta">
          <h2>Start from the protocol layer</h2>
          <p>Use the crate for custom architecture, or deploy the server when you mostly need a managed RTMP endpoint.</p>
          <div class="hero-actions" style="margin-bottom:0;">
            <a href="https://github.com/OpenRTMP/librtmp2" target="_blank" rel="noopener" class="btn btn-primary">Open librtmp2</a>
            <a href="/quickstart/" class="btn btn-ghost">Run the full server</a>
          </div>
        </div>
      </article>

      <aside class="toc-card" aria-label="On this page">
        <strong>On this page</strong>
        <a href="#library-vs-server">Library or server</a>
        <a href="#install">Install crate</a>
        <a href="#architecture">Boundary</a>
        <a href="#session">Session flow</a>
        <a href="#minimal-design">Server design</a>
        <a href="#ertmp">E-RTMP</a>
        <a href="#ffi">C FFI</a>
        <a href="#security">Security</a>
        <a href="#testing">Testing</a>
      </aside>
    </div>
  </section>
</main>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>
