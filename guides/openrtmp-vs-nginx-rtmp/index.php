<?php
$page = 'guides';
$pageTitle = 'OpenRTMP vs nginx-rtmp — Which RTMP server should you use?';
$pageDescription = 'Compare OpenRTMP and nginx-rtmp by architecture, deployment, APIs, stream keys, statistics, codec goals, and missing features.';
$canonicalPath = '/guides/openrtmp-vs-nginx-rtmp/';
$ogType = 'article';
$structuredData = [
  '@context' => 'https://schema.org',
  '@type' => 'TechArticle',
  'headline' => 'OpenRTMP vs nginx-rtmp',
  'description' => $pageDescription,
  'author' => ['@type' => 'Organization', 'name' => 'OpenRTMP'],
  'mainEntityOfPage' => 'https://openrtmp.org/guides/openrtmp-vs-nginx-rtmp/'
];
include __DIR__ . '/../../includes/header.php';
?>

<main>
  <div class="page-hero container article-hero">
    <span class="eyebrow">Comparison &middot; Migration &middot; Architecture</span>
    <h1>OpenRTMP vs nginx-rtmp</h1>
    <p>These projects overlap at RTMP ingest and playback, but they are built around different goals. The right choice depends more on the application layer you need than on the port number they expose.</p>
  </div>

  <section class="content-section" style="padding-top: 0;">
    <div class="container article-layout">
      <article class="prose">
        <div class="callout"><strong>Summary:</strong> choose nginx-rtmp for established nginx workflows and built-in module features. Choose OpenRTMP for a Rust-first protocol stack, a small API-driven server, separate stream keys, JSON statistics, optional HA clustering, and an embeddable library — while accepting its alpha status and narrower media feature set.</div>

        <h2 id="overview">High-level comparison</h2>
        <table class="comparison-table">
          <thead><tr><th>Area</th><th>OpenRTMP</th><th>nginx-rtmp</th></tr></thead>
          <tbody>
            <tr><td>Primary architecture</td><td>Rust protocol library plus separate server and panel</td><td>Third-party nginx module configured through nginx directives</td></tr>
            <tr><td>Project maturity</td><td>Active alpha</td><td>Long-established ecosystem</td></tr>
            <tr><td>Administration</td><td>REST API, SQLite, and optional web panel</td><td>Configuration files, callbacks, and surrounding nginx tooling</td></tr>
            <tr><td>Stream credentials</td><td>Separate publish, play, and stats keys per stream</td><td>Usually implemented through callbacks or custom nginx configuration</td></tr>
            <tr><td>Statistics</td><td>JSON plus nginx-compatible XML</td><td>Classic XML statistics endpoint with XSL presentation</td></tr>
            <tr><td>Embeddable library</td><td>Rust crate and C-compatible FFI</td><td>No equivalent standalone protocol crate</td></tr>
            <tr><td>HLS, recording, exec, push</td><td>Not built into the current server</td><td>Common nginx-rtmp module features</td></tr>
            <tr><td>Multi-node HA</td><td>Optional OpenRaft + media mesh clustering (alpha, off by default)</td><td>Usually external load balancers, shared storage, or custom push topology</td></tr>
            <tr><td>Modern RTMP work</td><td>Explicit focus on RTMPS and Enhanced RTMP building blocks</td><td>Primarily traditional RTMP module workflows</td></tr>
          </tbody>
        </table>

        <h2 id="openrtmp-fit">Choose OpenRTMP when</h2>
        <ul class="check-list">
          <li>You are building a Rust application and want reusable RTMP protocol code.</li>
          <li>You want a small self-hosted server with an explicit REST API and SQLite-backed stream registry.</li>
          <li>You want separate publish, playback, and monitoring credentials.</li>
          <li>You need JSON statistics but also want compatibility with tools that expect nginx-style XML.</li>
          <li>You want to contribute to Enhanced RTMP, RTMPS, interoperability, or parser safety work.</li>
          <li>You want optional multi-node HA with replicated stream state (evaluate carefully; still alpha).</li>
          <li>You accept alpha software and can test the exact publishing/playback workflow.</li>
        </ul>

        <h2 id="nginx-fit">Choose nginx-rtmp when</h2>
        <ul class="check-list">
          <li>You already operate nginx and understand its configuration model.</li>
          <li>You depend on built-in HLS generation, recording, exec hooks, or push relay.</li>
          <li>You need a mature deployment pattern with extensive existing examples.</li>
          <li>Your monitoring and automation are already built directly around nginx-rtmp behavior.</li>
          <li>You do not need an embeddable Rust/C protocol library.</li>
        </ul>

        <h2 id="migration">Migration considerations</h2>
        <h3>Statistics integrations</h3>
        <p>OpenRTMP exposes <code>/stats-nginx?key=&lt;stats_key&gt;</code> for tools that understand nginx-rtmp XML. The endpoint intentionally protects each stream with its statistics key, so URL construction differs from a public server-wide stats page.</p>
        <h3>Authentication</h3>
        <p>nginx-rtmp deployments often use <code>on_publish</code> and <code>on_play</code> HTTP callbacks. OpenRTMP keeps stream records and keys in SQLite and validates them inside the server application layer.</p>
        <h3>Configuration model</h3>
        <p>Do not attempt to translate every nginx directive one-to-one. OpenRTMP is not an nginx module and deliberately lacks several nginx-rtmp application features.</p>
        <h3>Media features</h3>
        <p>If your nginx configuration records, transcodes, packages HLS, or pushes to other destinations, retain those services or add separate components before migrating ingest.</p>

        <h2 id="coexist">They can coexist</h2>
        <p>A migration does not have to be all-or-nothing. You can test OpenRTMP on a separate port or host, compare OBS/FFmpeg behavior, and keep nginx-rtmp for HLS or relay tasks while evaluating the OpenRTMP API and key model.</p>
        <p>A useful staged test is:</p>
        <ol>
          <li>Reproduce one H.264/AAC publisher and player workflow.</li>
          <li>Compare startup, reconnect, late join, and monitoring behavior.</li>
          <li>Validate the nginx-compatible XML with existing automation.</li>
          <li>Inventory every nginx directive currently used.</li>
          <li>Keep unsupported functions in nginx or replace them with explicit services.</li>
        </ol>

        <h2 id="limitations">OpenRTMP limitations to account for</h2>
        <p>The current server is not a drop-in nginx-rtmp replacement. It does not provide built-in HLS, exec, push relay, recording, or full nginx directive parity. The protocol and public APIs are still evolving before 1.0.</p>
        <p>Those limitations are acceptable when the desired system is a focused RTMP endpoint or an embeddable protocol stack. They are blockers when the existing nginx configuration is acting as a full media workflow engine.</p>

        <div class="cta compact-cta">
          <h2>Evaluate OpenRTMP without replacing nginx</h2>
          <p>Run the Docker stack on an alternate host or port and test one workflow end to end.</p>
          <div class="hero-actions" style="margin-bottom:0;">
            <a href="/quickstart/" class="btn btn-primary">Start the evaluation</a>
            <a href="https://github.com/OpenRTMP/librtmp2-server#project-status" target="_blank" rel="noopener" class="btn btn-ghost">Read project status</a>
          </div>
        </div>
      </article>

      <aside class="toc-card" aria-label="On this page">
        <strong>On this page</strong>
        <a href="#overview">Comparison</a>
        <a href="#openrtmp-fit">Choose OpenRTMP</a>
        <a href="#nginx-fit">Choose nginx-rtmp</a>
        <a href="#migration">Migration</a>
        <a href="#coexist">Coexistence</a>
        <a href="#limitations">Limitations</a>
      </aside>
    </div>
  </section>
</main>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
