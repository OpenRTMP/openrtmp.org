<?php
$page = 'guides';
$pageTitle = 'OpenRTMP Guides — RTMP, Enhanced RTMP, OBS, Docker, Rust, and server comparisons';
$pageDescription = 'Practical OpenRTMP guides for self-hosting RTMP, OBS, RTMPS, HEVC, AV1, Enhanced RTMP v2, Rust development, clustering, NOALBS, and media-server comparisons.';
$canonicalPath = '/guides/';
include_once __DIR__ . '/../includes/header.php';
?>

<main>
  <div class="page-hero container">
    <span class="eyebrow">Practical guides</span>
    <h1>Build, understand, and operate RTMP infrastructure</h1>
    <p>Task-focused articles for stream operators, application developers, and protocol contributors. Guides separate protocol capability from complete end-to-end support and state current pre-1.0 boundaries explicitly.</p>
  </div>

  <section style="padding-top: 0;">
    <div class="container">
      <div class="grid-2 guide-grid">
        <article class="card guide-card">
          <span class="guide-tag">Docker &middot; OBS &middot; Self-hosted</span>
          <h2><a href="/guides/self-hosted-rtmp-server-docker/">Self-host an RTMP server in five minutes</a></h2>
          <p>Deploy the OpenRTMP server and web panel with Docker, create stream keys, publish from OBS, and prepare the stack for an internet-facing host.</p>
          <a href="/guides/self-hosted-rtmp-server-docker/" class="text-link">Read the deployment guide &rarr;</a>
        </article>

        <article class="card guide-card">
          <span class="guide-tag">RTMP &middot; E-RTMP &middot; Protocol</span>
          <h2><a href="/guides/rtmp-vs-enhanced-rtmp/">RTMP vs Enhanced RTMP</a></h2>
          <p>Understand what E-RTMP adds to legacy RTMP, including FourCC codec signaling, modern codecs, multitrack capabilities, reconnect signaling, and compatibility.</p>
          <a href="/guides/rtmp-vs-enhanced-rtmp/" class="text-link">Compare RTMP and E-RTMP &rarr;</a>
        </article>

        <article class="card guide-card">
          <span class="guide-tag">E-RTMP v2 &middot; Protocol</span>
          <h2><a href="/guides/enhanced-rtmp-v2-explained/">Enhanced RTMP v2 explained</a></h2>
          <p>Walk through FourCC signaling, modern audio/video codecs, multitrack support, reconnect requests, timing improvements, and implementation boundaries.</p>
          <a href="/guides/enhanced-rtmp-v2-explained/" class="text-link">Read the v2 explainer &rarr;</a>
        </article>

        <article class="card guide-card">
          <span class="guide-tag">E-RTMP &middot; HEVC &middot; AV1 &middot; Opus</span>
          <h2><a href="/guides/enhanced-rtmp-hevc-av1-opus/">Enhanced RTMP codecs explained</a></h2>
          <p>Understand why Enhanced RTMP exists, how modern codec signaling differs from legacy FLV, and which OpenRTMP paths are complete versus experimental.</p>
          <a href="/guides/enhanced-rtmp-hevc-av1-opus/" class="text-link">Read the codec guide &rarr;</a>
        </article>

        <article class="card guide-card">
          <span class="guide-tag">OBS &middot; HEVC &middot; E-RTMP</span>
          <h2><a href="/guides/hevc-streaming-obs/">HEVC streaming with OBS</a></h2>
          <p>Configure an HEVC-capable OBS output, publish through Enhanced RTMP, verify the codec at OpenRTMP, and troubleshoot player compatibility.</p>
          <a href="/guides/hevc-streaming-obs/" class="text-link">Set up HEVC streaming &rarr;</a>
        </article>

        <article class="card guide-card">
          <span class="guide-tag">AV1 &middot; OBS &middot; E-RTMP</span>
          <h2><a href="/guides/av1-over-rtmp/">AV1 over RTMP</a></h2>
          <p>See how AV1 is signaled through E-RTMP, test it from OBS, verify the OpenRTMP path, and isolate encoder, server, and decoder issues.</p>
          <a href="/guides/av1-over-rtmp/" class="text-link">Test AV1 over RTMP &rarr;</a>
        </article>

        <article class="card guide-card">
          <span class="guide-tag">Security &middot; TLS &middot; OBS</span>
          <h2><a href="/guides/rtmps-server-obs/">Configure an RTMPS server for OBS</a></h2>
          <p>Run encrypted RTMPS alongside plaintext RTMP, configure certificates, expose the correct port, and verify the server before changing OBS ingest settings.</p>
          <a href="/guides/rtmps-server-obs/" class="text-link">Read the RTMPS guide &rarr;</a>
        </article>

        <article class="card guide-card">
          <span class="guide-tag">Rust &middot; Developer &middot; Library</span>
          <h2><a href="/guides/rtmp-server-rust/">Build an RTMP server in Rust</a></h2>
          <p>Use librtmp2 for a custom server, relay, gateway, or client, and understand where the protocol library ends and your application architecture begins.</p>
          <a href="/guides/rtmp-server-rust/" class="text-link">Read the Rust developer guide &rarr;</a>
        </article>

        <article class="card guide-card">
          <span class="guide-tag">Comparison &middot; Migration</span>
          <h2><a href="/guides/openrtmp-vs-nginx-rtmp/">OpenRTMP vs nginx-rtmp</a></h2>
          <p>Compare architecture, deployment, statistics compatibility, missing features, and the scenarios where each project's design fits better.</p>
          <a href="/guides/openrtmp-vs-nginx-rtmp/" class="text-link">Read the direct comparison &rarr;</a>
        </article>

        <article class="card guide-card">
          <span class="guide-tag">Alternatives &middot; Self-hosted</span>
          <h2><a href="/guides/nginx-rtmp-alternatives/">nginx-rtmp alternatives</a></h2>
          <p>Compare nginx-rtmp with OpenRTMP, MediaMTX, and SRS by protocol coverage, control model, recording, delivery features, and migration goals.</p>
          <a href="/guides/nginx-rtmp-alternatives/" class="text-link">Explore alternatives &rarr;</a>
        </article>

        <article class="card guide-card">
          <span class="guide-tag">OpenRTMP &middot; MediaMTX &middot; SRS</span>
          <h2><a href="/guides/openrtmp-vs-mediamtx-vs-srs/">OpenRTMP vs MediaMTX vs SRS</a></h2>
          <p>Compare a focused RTMP/E-RTMP stack, a multi-protocol media router, and a broader live-streaming server using current upstream capabilities.</p>
          <a href="/guides/openrtmp-vs-mediamtx-vs-srs/" class="text-link">Read the media-server comparison &rarr;</a>
        </article>

        <article class="card guide-card">
          <span class="guide-tag">HA &middot; Clustering &middot; OpenRaft</span>
          <h2><a href="/guides/rtmp-server-ha-clustering/">Run an HA RTMP server cluster</a></h2>
          <p>Enable optional multi-node clustering, bootstrap and join voters, expose control/media ports, and operate nodes from the API or panel.</p>
          <a href="/guides/rtmp-server-ha-clustering/" class="text-link">Read the clustering guide &rarr;</a>
        </article>

        <article class="card guide-card">
          <span class="guide-tag">NOALBS &middot; JSON &middot; Monitoring</span>
          <h2><a href="/guides/openrtmp-noalbs-json-stats/">Connect NOALBS to OpenRTMP statistics</a></h2>
          <p>Configure the native OpenRTMP provider, understand bitrate and RTT values, use the XML fallback, and troubleshoot container or proxy connectivity.</p>
          <a href="/guides/openrtmp-noalbs-json-stats/" class="text-link">Read the statistics guide &rarr;</a>
        </article>
      </div>

      <div class="cta" style="margin-top: 48px;">
        <h2>Prefer a direct setup path?</h2>
        <p>The quickstart takes you from an empty Docker host to an OBS-ready stream in a few steps.</p>
        <div class="hero-actions" style="margin-bottom:0;">
          <a href="/quickstart/" class="btn btn-primary">Open the quickstart</a>
          <a href="/docs/" class="btn btn-ghost">Browse the reference docs</a>
        </div>
      </div>
    </div>
  </section>
</main>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
