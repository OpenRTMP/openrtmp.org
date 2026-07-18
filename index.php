<?php
$page = 'home';
$pageTitle = 'OpenRTMP — Rust RTMP/E-RTMP library and self-hosted server';
$pageDescription = 'Build RTMP/E-RTMP applications with Rust or deploy a private RTMP/RTMPS server with Docker, stream-key authentication, live statistics, REST API, and a web panel.';
$canonicalPath = '/';
$structuredData = [
  '@context' => 'https://schema.org',
  '@graph' => [
    [
      '@type' => 'SoftwareSourceCode',
      'name' => 'librtmp2',
      'description' => 'Rust RTMP/RTMPS and Enhanced RTMP protocol library with a C-compatible FFI.',
      'codeRepository' => 'https://github.com/OpenRTMP/librtmp2',
      'programmingLanguage' => 'Rust',
      'license' => 'https://opensource.org/license/mit'
    ],
    [
      '@type' => 'SoftwareApplication',
      'name' => 'OpenRTMP Server and Panel',
      'applicationCategory' => 'DeveloperApplication',
      'operatingSystem' => 'Linux, Docker',
      'description' => 'Self-hosted RTMP/RTMPS server with REST API, stream keys, live statistics, and a web control panel.',
      'url' => 'https://openrtmp.org/quickstart/'
    ]
  ]
];
include __DIR__ . '/includes/header.php';
?>

<main>

  <section class="hero">
    <div class="container">
      <span class="eyebrow">Active alpha &middot; Rust &middot; Docker &middot; MIT</span>
      <h1>Modern RTMP infrastructure<br><span class="gradient">for builders and operators.</span></h1>
      <p>
        Embed a focused RTMP/E-RTMP protocol library in your own application,
        or deploy a private RTMP/RTMPS server with stream keys, REST API,
        live statistics, and a browser-based control panel.
      </p>
      <div class="hero-actions">
        <a href="/quickstart/" class="btn btn-primary">Run the Docker stack</a>
        <a href="https://docs.rs/librtmp2" target="_blank" rel="noopener" class="btn btn-ghost">Use the Rust crate</a>
        <a href="/docs/" class="btn btn-ghost">Read the docs</a>
      </div>
      <div class="hero-stats">
        <div class="stat"><strong>RTMP/S</strong><span>publish and playback</span></div>
        <div class="stat"><strong>HEVC / AV1</strong><span>enhanced media passthrough</span></div>
        <div class="stat"><strong>Rust + C</strong><span>native API and FFI</span></div>
        <div class="stat"><strong>MIT</strong><span>commercial-friendly license</span></div>
      </div>
    </div>
  </section>

  <section style="padding-top: 24px;" id="paths">
    <div class="container">
      <div class="section-head">
        <span class="eyebrow">Choose your path</span>
        <h2>Start with the part you actually need</h2>
        <p>The library and deployable server are separate projects with different audiences, installation paths, and stability boundaries.</p>
      </div>
      <div class="grid-2">
        <article class="card path-card">
          <div class="icon">&#128736;&#65039;</div>
          <h3>Build with the protocol library</h3>
          <p>Use <code>librtmp2</code> in a Rust server, relay, plugin, gateway, or protocol experiment. The crate provides RTMP/RTMPS session handling, parser modules, relay primitives, and a C-compatible FFI.</p>
          <ul class="check-list">
            <li>Rust crate plus <code>cdylib</code> and <code>staticlib</code></li>
            <li>OBS/FFmpeg-style publish and play workflows</li>
            <li>Legacy RTMP plus Enhanced RTMP building blocks</li>
            <li>Your application owns authentication, routing, and media policy</li>
          </ul>
          <div class="card-actions">
            <a href="https://github.com/OpenRTMP/librtmp2" target="_blank" rel="noopener" class="btn btn-primary">View librtmp2</a>
            <a href="https://github.com/OpenRTMP/librtmp2#implementation-status" target="_blank" rel="noopener" class="btn btn-ghost">Implementation status</a>
          </div>
        </article>

        <article class="card path-card">
          <div class="icon">&#128225;</div>
          <h3>Run a self-hosted streaming server</h3>
          <p>Deploy <code>librtmp2-server</code> and its panel when you want a private RTMP endpoint instead of building the application layer yourself.</p>
          <ul class="check-list">
            <li>RTMP and optional RTMPS listeners</li>
            <li>Per-stream publish, play, and statistics keys</li>
            <li>SQLite persistence and Bearer-authenticated REST API</li>
            <li>JSON and nginx-compatible XML statistics</li>
            <li>Web panel for stream creation and live monitoring</li>
          </ul>
          <div class="card-actions">
            <a href="/quickstart/" class="btn btn-primary">Five-minute quickstart</a>
            <a href="/guides/self-hosted-rtmp-server-docker/" class="btn btn-ghost">Deployment guide</a>
          </div>
        </article>
      </div>
    </div>
  </section>

  <section id="quick-preview">
    <div class="container">
      <div class="section-head">
        <span class="eyebrow">Fast evaluation</span>
        <h2>Server, panel, and Redis from published images</h2>
        <p>The standalone Compose file does not require a Rust toolchain or neighboring source repositories.</p>
      </div>
      <div class="code-panel">
        <div class="code-panel-head">
          <div class="traffic"><span></span><span></span><span></span></div>
          <span class="filename">terminal</span>
        </div>
        <pre><code>git clone https://github.com/OpenRTMP/librtmp2-server-panel.git
cd librtmp2-server-panel

# Generate the three required secrets as shown in the quickstart.
docker compose -f compose.quickstart.yml up -d

# Panel: http://localhost:8000
# API:   http://localhost:8080/api/v1/health
# RTMP:  rtmp://localhost:1935/live</code></pre>
      </div>
      <div class="center-link"><a href="/quickstart/" class="btn btn-primary">Open the complete copy-and-paste guide</a></div>
    </div>
  </section>

  <section id="features">
    <div class="container">
      <div class="section-head">
        <span class="eyebrow">Why OpenRTMP</span>
        <h2>A focused RTMP ecosystem</h2>
        <p>OpenRTMP stays intentionally narrower than all-in-one media platforms, so each component can remain understandable, reusable, and testable.</p>
      </div>
      <div class="grid">
        <div class="card">
          <div class="icon">&#128274;</div>
          <h3>Defensive protocol handling</h3>
          <p>Network-provided lengths and parser state are validated, with tests and fuzz targets covering protocol-critical code paths.</p>
        </div>
        <div class="card">
          <div class="icon">&#127909;</div>
          <h3>Modern codec workflows</h3>
          <p>Enhanced RTMP media can carry HEVC, AV1, and Opus in supported OBS/FFmpeg workflows. Exact live-path support is documented per release.</p>
        </div>
        <div class="card">
          <div class="icon">&#128268;</div>
          <h3>RTMP and RTMPS together</h3>
          <p>The server can expose plaintext RTMP and an additional TLS listener while sharing one stream registry and relay core.</p>
        </div>
        <div class="card">
          <div class="icon">&#128272;</div>
          <h3>Private by default</h3>
          <p>Separate publish, playback, and stats keys avoid a public stream directory and let operators share only the access each integration needs.</p>
        </div>
        <div class="card">
          <div class="icon">&#128202;</div>
          <h3>Monitoring-friendly</h3>
          <p>Use modern JSON statistics or nginx-rtmp-compatible XML for existing monitoring and automation tools.</p>
        </div>
        <div class="card">
          <div class="icon">&#129513;</div>
          <h3>Composable projects</h3>
          <p>Use only the crate, run only the server, attach the panel, or integrate the REST API into your own control plane.</p>
        </div>
      </div>
    </div>
  </section>

  <section id="architecture">
    <div class="container">
      <div class="section-head">
        <span class="eyebrow">Architecture</span>
        <h2>Protocol, application layer, and UI stay separate</h2>
        <p>That separation makes it clear which project to adopt and where to contribute.</p>
      </div>
      <div class="stack">
        <div class="stack-row"><span class="layer-name">Panel</span><span class="layer-desc">Browser UI for stream lifecycle, copyable URLs, keys, and live statistics</span></div>
        <div class="stack-arrow">&#8595;</div>
        <div class="stack-row"><span class="layer-name">Server</span><span class="layer-desc">REST API, authentication, SQLite, statistics, listener configuration, and stream registry</span></div>
        <div class="stack-arrow">&#8595;</div>
        <div class="stack-row"><span class="layer-name">librtmp2</span><span class="layer-desc">RTMP/RTMPS connection, handshake, chunking, AMF commands, relay primitives, and E-RTMP modules</span></div>
        <div class="stack-arrow">&#8595;</div>
        <div class="stack-row"><span class="layer-name">Clients</span><span class="layer-desc">OBS, FFmpeg, custom publishers, players, relays, and embedded applications</span></div>
      </div>
    </div>
  </section>

  <section id="scope">
    <div class="container">
      <div class="section-head">
        <span class="eyebrow">Honest scope</span>
        <h2>Know what OpenRTMP is — and what it is not</h2>
        <p>Clear boundaries help operators choose the right tool and help contributors focus on the highest-value gaps.</p>
      </div>
      <div class="grid-2">
        <div class="card">
          <h3>Good fit today</h3>
          <ul class="check-list">
            <li>Private RTMP/RTMPS ingest and playback</li>
            <li>Custom Rust or FFI-based RTMP applications</li>
            <li>OBS/FFmpeg integration testing</li>
            <li>Stream-key and statistics tooling</li>
            <li>Protocol research and contribution</li>
          </ul>
        </div>
        <div class="card">
          <h3>Use another platform when you need</h3>
          <ul class="check-list muted-list">
            <li>A turnkey public video platform and viewer website</li>
            <li>Built-in HLS, recording, transcoding, or push relay</li>
            <li>Broad multi-protocol routing beyond RTMP</li>
            <li>A guaranteed stable 1.0 API today</li>
          </ul>
        </div>
      </div>
    </div>
  </section>

  <section id="ecosystem">
    <div class="container">
      <div class="section-head">
        <span class="eyebrow">OpenRTMP projects</span>
        <h2>Adopt one component or the complete stack</h2>
      </div>
      <div class="grid">
        <div class="card">
          <div class="icon">&#128230;</div>
          <h3>librtmp2</h3>
          <p>Rust protocol library and C-compatible FFI for custom servers, clients, relays, plugins, and research.</p>
          <p class="card-link"><a href="https://github.com/OpenRTMP/librtmp2" target="_blank" rel="noopener">Repository &rarr;</a></p>
        </div>
        <div class="card">
          <div class="icon">&#128225;</div>
          <h3>librtmp2-server</h3>
          <p>RTMP/RTMPS application layer with SQLite, keys, REST API, and monitoring endpoints.</p>
          <p class="card-link"><a href="https://github.com/OpenRTMP/librtmp2-server" target="_blank" rel="noopener">Repository &rarr;</a></p>
        </div>
        <div class="card">
          <div class="icon">&#127912;</div>
          <h3>librtmp2-server-panel</h3>
          <p>Flask web UI for creating streams, copying URLs, and monitoring live publisher/player statistics.</p>
          <p class="card-link"><a href="https://github.com/OpenRTMP/librtmp2-server-panel" target="_blank" rel="noopener">Repository &rarr;</a></p>
        </div>
      </div>
    </div>
  </section>

  <section>
    <div class="container">
      <div class="cta">
        <h2>Try the stack, then choose your integration depth</h2>
        <p>Start with Docker to understand the workflow, or go directly to the crate when you are building your own application layer.</p>
        <div class="hero-actions" style="margin-bottom:0;">
          <a href="/quickstart/" class="btn btn-primary">Run OpenRTMP locally</a>
          <a href="/guides/" class="btn btn-ghost">Browse practical guides</a>
          <a href="https://github.com/OpenRTMP" target="_blank" rel="noopener" class="btn btn-ghost">Star the projects</a>
        </div>
      </div>
    </div>
  </section>

</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
