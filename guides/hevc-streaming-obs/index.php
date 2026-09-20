<?php
$page = 'guides';
$pageTitle = 'HEVC streaming with OBS over Enhanced RTMP — OpenRTMP Guide';
$pageDescription = 'Stream HEVC (H.265) from OBS over Enhanced RTMP, configure an OpenRTMP ingest endpoint, verify codec signaling, and troubleshoot encoder and player compatibility.';
$canonicalPath = '/guides/hevc-streaming-obs/';
$ogType = 'article';
$structuredData = [
  '@context' => 'https://schema.org',
  '@type' => 'TechArticle',
  'headline' => 'HEVC streaming with OBS over Enhanced RTMP',
  'description' => $pageDescription,
  'author' => ['@type' => 'Organization', 'name' => 'OpenRTMP'],
  'publisher' => ['@type' => 'Organization', 'name' => 'OpenRTMP'],
  'mainEntityOfPage' => 'https://openrtmp.org/guides/hevc-streaming-obs/'
];
include_once __DIR__ . '/../../includes/header.php';
?>

<main>
  <div class="page-hero container article-hero">
    <span class="eyebrow">OBS &middot; HEVC &middot; E-RTMP</span>
    <h1>Stream HEVC from OBS over Enhanced RTMP</h1>
    <p>HEVC can reduce bitrate for a given quality compared with older H.264 workflows, but every component from OBS to the final player must understand the enhanced RTMP media signaling.</p>
  </div>

  <section class="content-section" style="padding-top: 0;">
    <div class="container article-layout">
      <article class="prose">
        <div class="callout warning"><strong>Compatibility first:</strong> a successful HEVC publish does not prove that every downstream player can decode HEVC. Test the complete OBS &rarr; OpenRTMP &rarr; player chain before relying on it.</div>

        <h2 id="requirements">What you need</h2>
        <ul>
          <li>A current OBS Studio build with Enhanced RTMP support.</li>
          <li>A usable HEVC encoder exposed to OBS, such as a supported hardware encoder or software encoder.</li>
          <li>An RTMP/E-RTMP ingest server that accepts the enhanced HEVC packet format.</li>
          <li>A player or downstream service that can receive and decode HEVC in your chosen workflow.</li>
        </ul>
        <p>OBS introduced HEVC and AV1 streaming over Enhanced RTMP in OBS 29.1. The protocol support is therefore not limited to experimental command-line tools, although the encoder and service configuration still determine what OBS exposes in its UI.</p>

        <h2 id="server">1. Start an OpenRTMP ingest endpoint</h2>
        <p>The fastest test path is the <a href="/quickstart/">OpenRTMP Docker quickstart</a>. It gives you an RTMP server, stream registry, API, statistics, and web control panel.</p>
        <pre><code>git clone https://github.com/OpenRTMP/librtmp2-server-panel.git
cd librtmp2-server-panel
# Create the required .env secrets as shown in the quickstart.
docker compose -f compose.quickstart.yml up -d</code></pre>
        <p>Create a stream in the panel and copy its publish URL and <code>publish_key</code>. For a local test, the server URL is normally similar to <code>rtmp://localhost:1935/live</code>.</p>

        <h2 id="obs">2. Configure OBS for HEVC</h2>
        <ol>
          <li>Open <strong>Settings &rarr; Stream</strong> and configure the OpenRTMP endpoint as a custom RTMP service.</li>
          <li>Use the generated OpenRTMP <code>publish_key</code> as the stream key.</li>
          <li>Open <strong>Settings &rarr; Output</strong>.</li>
          <li>If needed, switch to the advanced output mode so the streaming encoder can be selected explicitly.</li>
          <li>Select an HEVC/H.265 encoder if OBS offers one for the selected output and service.</li>
          <li>Start with conservative bitrate, keyframe, and resolution settings that your decoder is known to handle.</li>
        </ol>
        <p>If HEVC is unavailable in the encoder selector, confirm that the installed OBS build, GPU/driver or software encoder, and selected service configuration expose HEVC for streaming. Do not work around a missing encoder by assuming the server can transcode H.264 into HEVC: the current OpenRTMP server is a focused relay and control-plane server, not a transcoder.</p>

        <h2 id="verify">3. Verify that HEVC actually arrived</h2>
        <p>After OBS connects, use the OpenRTMP panel or JSON statistics endpoint to verify the stream. The panel can show live bitrate, codec, resolution, frame rate, RTT, uptime, publishers, and players.</p>
        <p>Useful checks include:</p>
        <ul>
          <li>The publisher remains connected without repeated reconnect loops.</li>
          <li>The reported video codec is HEVC/H.265 rather than H.264.</li>
          <li>The first player starts cleanly.</li>
          <li>A player that joins later receives the required initialization state and begins decoding.</li>
          <li>The stream still works after stopping and restarting OBS.</li>
        </ul>

        <h2 id="rtmps">4. Add RTMPS when encrypted ingest is required</h2>
        <p>HEVC signaling and transport encryption solve different problems. E-RTMP carries the modern codec signaling; RTMPS wraps the RTMP connection in TLS.</p>
        <p>OpenRTMP can run RTMP and RTMPS listeners side by side. Follow the <a href="/guides/rtmps-server-obs/">RTMPS with OBS guide</a> when the publisher-to-server path must be encrypted.</p>

        <h2 id="players">Player compatibility matters more than the server alone</h2>
        <p>Legacy RTMP players were commonly designed around H.264/AAC. Even when a server relays an E-RTMP HEVC stream correctly, an old player may reject the enhanced packet type or lack an HEVC decoder.</p>
        <p>For production testing, use at least one known-good receiver for HEVC and separately verify any automation, restream, recording, or monitoring component in the path.</p>

        <h2 id="troubleshooting">Troubleshooting</h2>
        <h3>OBS connects but the player shows no video</h3>
        <p>Check whether the player understands HEVC over the enhanced RTMP/FLV signaling used by the stream. Test the same path with H.264 to separate transport/authentication problems from codec compatibility.</p>

        <h3>OBS does not offer HEVC</h3>
        <p>Verify encoder availability and the selected streaming service/output capabilities. Update OBS and GPU drivers where appropriate, and confirm that the encoder works for a local HEVC recording before debugging the network path.</p>

        <h3>The stream works with H.264 but disconnects with HEVC</h3>
        <p>Capture the exact OBS version, encoder, server version, and logs. Then test against the <a href="https://github.com/OpenRTMP/librtmp2#implementation-status" target="_blank" rel="noopener">librtmp2 implementation status</a> and report a reproducible interoperability issue if the enhanced packet path fails.</p>

        <h2 id="next">HEVC or AV1?</h2>
        <p>Both codecs can be signaled through E-RTMP, but hardware availability and downstream compatibility differ. If you also want to evaluate AV1, see <a href="/guides/av1-over-rtmp/">AV1 over RTMP</a>. For the protocol-level differences, see <a href="/guides/rtmp-vs-enhanced-rtmp/">RTMP vs E-RTMP</a>.</p>

        <div class="cta compact-cta">
          <h2>Test HEVC on your own endpoint</h2>
          <p>Bring up the OpenRTMP stack, establish H.264 first, then switch only the video codec to HEVC.</p>
          <div class="hero-actions" style="margin-bottom:0;">
            <a href="/quickstart/" class="btn btn-primary">Open quickstart</a>
            <a href="/guides/enhanced-rtmp-hevc-av1-opus/" class="btn btn-ghost">Codec details</a>
          </div>
        </div>
      </article>

      <aside class="toc-card" aria-label="On this page">
        <strong>On this page</strong>
        <a href="#requirements">Requirements</a>
        <a href="#server">Start server</a>
        <a href="#obs">Configure OBS</a>
        <a href="#verify">Verify HEVC</a>
        <a href="#rtmps">RTMPS</a>
        <a href="#players">Players</a>
        <a href="#troubleshooting">Troubleshooting</a>
        <a href="#next">HEVC or AV1</a>
      </aside>
    </div>
  </section>
</main>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>
