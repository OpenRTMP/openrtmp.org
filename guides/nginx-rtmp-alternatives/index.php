<?php
$page = 'guides';
$pageTitle = 'nginx-rtmp alternatives: OpenRTMP, MediaMTX, SRS, or nginx-rtmp?';
$pageDescription = 'Compare practical nginx-rtmp alternatives including OpenRTMP, MediaMTX, and SRS by protocols, API model, recording, HLS, WebRTC, Enhanced RTMP, and deployment goals.';
$canonicalPath = '/guides/nginx-rtmp-alternatives/';
$ogType = 'article';
$structuredData = [
  '@context' => 'https://schema.org',
  '@type' => 'TechArticle',
  'headline' => 'nginx-rtmp alternatives: OpenRTMP, MediaMTX, SRS, or nginx-rtmp?',
  'description' => $pageDescription,
  'author' => ['@type' => 'Organization', 'name' => 'OpenRTMP'],
  'publisher' => ['@type' => 'Organization', 'name' => 'OpenRTMP'],
  'mainEntityOfPage' => 'https://openrtmp.org/guides/nginx-rtmp-alternatives/'
];
include_once __DIR__ . '/../../includes/header.php';
?>

<main>
  <div class="page-hero container article-hero">
    <span class="eyebrow">Comparison &middot; Self-hosted &middot; Migration</span>
    <h1>nginx-rtmp alternatives</h1>
    <p>There is no universal drop-in replacement for nginx-rtmp. The useful question is which server architecture matches the protocols and application features your deployment actually needs.</p>
  </div>

  <section class="content-section" style="padding-top: 0;">
    <div class="container article-layout">
      <article class="prose">
        <div class="callout"><strong>Four sensible paths:</strong> keep nginx-rtmp for its established module workflows; choose OpenRTMP for a focused Rust RTMP/E-RTMP stack; choose MediaMTX for a compact multi-protocol media router; or choose SRS for a broader live-streaming server with RTMP, WebRTC, HLS, SRT and protocol conversion.</div>

        <h2 id="why">Why people look beyond nginx-rtmp</h2>
        <p>nginx-rtmp remains useful, especially when an existing deployment already depends on nginx directives, callbacks, HLS, recording, exec hooks, or push behavior. Alternatives become interesting when a project wants a different control model, modern protocol support, an embeddable library, simpler containers, or built-in support for protocols outside traditional RTMP.</p>
        <p>A migration should therefore start by inventorying the current nginx configuration rather than by comparing GitHub star counts.</p>

        <h2 id="overview">Quick comparison</h2>
        <table class="comparison-table">
          <thead><tr><th>Project</th><th>Primary fit</th><th>Notable strengths</th><th>Important trade-off</th></tr></thead>
          <tbody>
            <tr><td><strong>OpenRTMP</strong></td><td>Focused RTMP/RTMPS and E-RTMP infrastructure</td><td>Rust library + server + REST API + keys + live stats + optional HA</td><td>Pre-1.0 and intentionally lacks built-in HLS, recording, transcoding, and push relay</td></tr>
            <tr><td><strong>MediaMTX</strong></td><td>Compact multi-protocol routing</td><td>RTSP, RTMP, HLS, WebRTC, SRT, MoQ, recording, forwarding, API, Prometheus metrics</td><td>Different architecture from nginx; migration is configuration redesign rather than directive translation</td></tr>
            <tr><td><strong>SRS</strong></td><td>Broad live-streaming and WebRTC server</td><td>RTMP, WebRTC, HLS, HTTP-FLV, SRT, DASH, APIs and protocol conversion</td><td>Larger feature surface and operational model than a narrow RTMP-only server</td></tr>
            <tr><td><strong>nginx-rtmp</strong></td><td>Established nginx module workflows</td><td>Mature examples, HLS, recording, exec/push patterns, nginx integration</td><td>Older module architecture and less focus on modern E-RTMP application design</td></tr>
          </tbody>
        </table>

        <h2 id="openrtmp">OpenRTMP: when you want RTMP to stay the center</h2>
        <p>OpenRTMP is deliberately narrow. <code>librtmp2</code> provides the protocol layer, <code>librtmp2-server</code> adds stream keys, SQLite, REST API, JSON/nginx-compatible statistics and optional clustering, and the panel provides a browser UI.</p>
        <p>This is useful when the system around RTMP is yours and you want explicit application APIs rather than a large all-in-one media server. It is also the only option in this list whose core project is structured around a reusable Rust RTMP/E-RTMP library with a C-compatible FFI.</p>
        <p>Read the dedicated <a href="/guides/openrtmp-vs-nginx-rtmp/">OpenRTMP vs nginx-rtmp comparison</a> for a migration-focused breakdown.</p>

        <h2 id="mediamtx">MediaMTX: when you need a media router</h2>
        <p><a href="https://mediamtx.org/docs/kickoff/introduction" target="_blank" rel="noopener">MediaMTX</a> describes itself as a ready-to-use media server and proxy that can publish, read, proxy, record and play back real-time streams. Its current documentation covers RTSP, RTMP/RTMPS, HLS, WebRTC, SRT, Media over QUIC, recording, forwarding, a control API, authentication, and Prometheus-compatible metrics.</p>
        <p>That makes it attractive when protocol conversion and routing are first-class requirements. If your desired architecture is “ingest in one protocol, serve another,” MediaMTX should be evaluated directly rather than treated as a simple nginx module replacement.</p>

        <h2 id="srs">SRS: when you need a broader streaming platform</h2>
        <p><a href="https://ossrs.io/lts/en-us/docs/v6/doc/introduction" target="_blank" rel="noopener">SRS</a> supports RTMP, WebRTC, HLS, HTTP-FLV, SRT, MPEG-DASH and related conversion workflows. Its documentation also covers HTTP APIs and clustering/topology patterns.</p>
        <p>SRS is therefore a natural candidate when the deployment needs RTMP ingest but also expects browser delivery, HLS, WebRTC, SRT, or a larger streaming feature set from the same project.</p>

        <h2 id="stay">When staying on nginx-rtmp is reasonable</h2>
        <ul>
          <li>Your current setup is stable and already solves the problem.</li>
          <li>You rely on nginx-rtmp-specific directives, HLS generation, recording, exec hooks, or push chains.</li>
          <li>Your team already operates nginx and does not need a new control plane.</li>
          <li>The cost and risk of migration exceeds the benefit of newer architecture or protocols.</li>
        </ul>
        <p>Replacing working infrastructure solely because a newer project exists is rarely a good migration strategy.</p>

        <h2 id="choose">Choose by requirement, not by brand</h2>
        <table>
          <thead><tr><th>If your main requirement is…</th><th>Evaluate first</th></tr></thead>
          <tbody>
            <tr><td>Focused RTMP/RTMPS + E-RTMP, Rust library, per-stream keys, API-driven control</td><td>OpenRTMP</td></tr>
            <tr><td>Many ingest/output protocols and a compact routing layer</td><td>MediaMTX</td></tr>
            <tr><td>RTMP plus WebRTC/HLS/SRT and a broader live-streaming platform</td><td>SRS</td></tr>
            <tr><td>Existing nginx-rtmp deployment with module-specific workflows</td><td>Keep nginx-rtmp unless a concrete limitation justifies migration</td></tr>
          </tbody>
        </table>

        <h2 id="migration">Migration checklist</h2>
        <ol>
          <li>List every protocol currently ingested and served.</li>
          <li>List every nginx-rtmp directive, callback, exec command, recording path, and push target.</li>
          <li>Identify which functions belong in the media server and which could become separate services.</li>
          <li>Build one parallel test path on a different host or port.</li>
          <li>Test publish, first playback, late join, reconnect, authentication, monitoring, and failure recovery.</li>
          <li>Measure CPU, memory, startup behavior and operational complexity under your own workload.</li>
          <li>Move production traffic only after feature parity for the functions you actually use.</li>
        </ol>

        <h2 id="deeper">OpenRTMP, MediaMTX and SRS in more detail</h2>
        <p>For a feature-by-feature comparison of those three modern options, see <a href="/guides/openrtmp-vs-mediamtx-vs-srs/">OpenRTMP vs MediaMTX vs SRS</a>.</p>

        <div class="cta compact-cta">
          <h2>Evaluate instead of guessing</h2>
          <p>OpenRTMP can run next to your existing nginx-rtmp host so you can compare one real workflow without replacing production first.</p>
          <div class="hero-actions" style="margin-bottom:0;">
            <a href="/quickstart/" class="btn btn-primary">Run OpenRTMP</a>
            <a href="/guides/openrtmp-vs-nginx-rtmp/" class="btn btn-ghost">Migration comparison</a>
          </div>
        </div>
      </article>

      <aside class="toc-card" aria-label="On this page">
        <strong>On this page</strong>
        <a href="#why">Why alternatives</a>
        <a href="#overview">Comparison</a>
        <a href="#openrtmp">OpenRTMP</a>
        <a href="#mediamtx">MediaMTX</a>
        <a href="#srs">SRS</a>
        <a href="#stay">Stay on nginx</a>
        <a href="#choose">Choose by need</a>
        <a href="#migration">Migration</a>
      </aside>
    </div>
  </section>
</main>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>
