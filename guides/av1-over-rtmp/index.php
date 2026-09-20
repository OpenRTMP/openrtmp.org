<?php
$page = 'guides';
$pageTitle = 'AV1 over RTMP: streaming with Enhanced RTMP and OBS — OpenRTMP';
$pageDescription = 'Learn how AV1 is carried over Enhanced RTMP, how to test AV1 streaming from OBS to OpenRTMP, and how to diagnose encoder, server, and player compatibility.';
$canonicalPath = '/guides/av1-over-rtmp/';
$ogType = 'article';
$structuredData = [
  '@context' => 'https://schema.org',
  '@type' => 'TechArticle',
  'headline' => 'AV1 over RTMP with Enhanced RTMP and OBS',
  'description' => $pageDescription,
  'author' => ['@type' => 'Organization', 'name' => 'OpenRTMP'],
  'publisher' => ['@type' => 'Organization', 'name' => 'OpenRTMP'],
  'mainEntityOfPage' => 'https://openrtmp.org/guides/av1-over-rtmp/'
];
include_once __DIR__ . '/../../includes/header.php';
?>

<main>
  <div class="page-hero container article-hero">
    <span class="eyebrow">AV1 &middot; E-RTMP &middot; OBS</span>
    <h1>AV1 over RTMP</h1>
    <p>AV1 streaming over RTMP is possible through Enhanced RTMP, which adds modern FourCC-based codec signaling while retaining the RTMP session and transport model.</p>
  </div>

  <section class="content-section" style="padding-top: 0;">
    <div class="container article-layout">
      <article class="prose">
        <div class="callout"><strong>The key idea:</strong> “AV1 over RTMP” is not legacy FLV pretending AV1 is H.264. E-RTMP defines explicit modern codec signaling so an enhanced-capable sender and server can identify AV1 correctly.</div>

        <h2 id="support">Where AV1-over-RTMP support comes from</h2>
        <p>The Veovera Enhanced RTMP specification defines modern video-codec signaling including AV1. OBS Studio added AV1 and HEVC streaming over Enhanced RTMP in OBS 29.1, making this workflow available from a mainstream live encoder.</p>
        <p>Server and player support remain separate questions. Every component must understand the enhanced packet format, and the receiver also needs an AV1 decoder suitable for the stream profile and performance requirements.</p>

        <h2 id="requirements">Requirements</h2>
        <ul>
          <li>A current OBS Studio build or another E-RTMP-capable publisher.</li>
          <li>An AV1 encoder available to the publishing application.</li>
          <li>An E-RTMP-capable server path.</li>
          <li>A downstream player or service that supports AV1 in the resulting RTMP workflow.</li>
        </ul>
        <p>Hardware AV1 encoding availability varies by GPU generation and platform. Software encoding may be possible but can be significantly more CPU-intensive, so validate real-time performance separately from protocol compatibility.</p>

        <h2 id="openrtmp">Test AV1 with OpenRTMP</h2>
        <p>Deploy the <a href="/quickstart/">OpenRTMP quickstart</a>, create a stream in the panel, and first verify the exact same path with H.264. Once the baseline is stable, change only the video encoder to AV1.</p>
        <pre><code># Baseline:
OBS (H.264) -> OpenRTMP -> known-good player

# Enhanced test:
OBS (AV1 / E-RTMP) -> OpenRTMP -> AV1-capable player</code></pre>
        <p>This isolates codec signaling and decoding from unrelated firewall, authentication and stream-key problems.</p>

        <h2 id="obs">Configure OBS</h2>
        <ol>
          <li>Configure OpenRTMP as the RTMP endpoint and use the generated <code>publish_key</code>.</li>
          <li>Open the streaming output settings.</li>
          <li>Select an AV1 encoder if the current OBS build, hardware and service/output configuration expose one.</li>
          <li>Use a resolution, frame rate and bitrate your encoder can sustain without overload.</li>
          <li>Start the stream and check the OpenRTMP statistics and logs.</li>
        </ol>
        <p>If OBS does not offer AV1 for the selected streaming output, verify the encoder and service capability first. The media server cannot create AV1 packets when the publisher is still sending H.264.</p>

        <h2 id="verify">What to verify</h2>
        <ul>
          <li>The server recognizes the stream as AV1 rather than H.264.</li>
          <li>The publisher remains connected.</li>
          <li>The first player receives initialization data and decodes video.</li>
          <li>A late-joining player starts successfully.</li>
          <li>Stopping and restarting OBS produces a clean new session.</li>
          <li>Longer runs do not accumulate dropped frames or encoder overload on the publishing machine.</li>
        </ul>

        <h2 id="rtmps">AV1 over RTMPS</h2>
        <p>AV1 and RTMPS are independent features. AV1 is the codec; E-RTMP is the enhanced media signaling; RTMPS adds TLS encryption around the RTMP connection. If encrypted ingest is required, enable the OpenRTMP RTMPS listener and follow the <a href="/guides/rtmps-server-obs/">RTMPS guide</a>.</p>

        <h2 id="troubleshooting">Troubleshooting</h2>
        <h3>OBS starts streaming but no video appears</h3>
        <p>Test the same endpoint with H.264. If H.264 works, inspect whether the downstream player understands AV1 in E-RTMP and whether OpenRTMP reports the enhanced codec correctly.</p>

        <h3>Encoder overload or dropped frames</h3>
        <p>This is usually an encoder-performance problem rather than an RTMP protocol problem. Lower resolution/frame rate, adjust encoder settings, or use hardware encoding where available.</p>

        <h3>Server accepts AV1 but a legacy player fails</h3>
        <p>That is expected when the player only implements traditional H.264/AAC RTMP. Use an AV1/E-RTMP-capable receiver or keep an H.264 compatibility path.</p>

        <h2 id="hevc">AV1 vs HEVC for an RTMP workflow</h2>
        <p>Both can use enhanced RTMP signaling. The practical choice depends on encoder availability, hardware decode support, downstream software, quality targets and licensing/operational constraints outside the RTMP protocol itself. Test both against the complete production chain rather than choosing from codec efficiency alone.</p>
        <p>For the HEVC setup, see <a href="/guides/hevc-streaming-obs/">HEVC streaming with OBS</a>.</p>

        <div class="cta compact-cta">
          <h2>Start with a controlled AV1 test</h2>
          <p>Establish a working H.264 path, switch one variable to AV1, and verify both first-play and late-join behavior.</p>
          <div class="hero-actions" style="margin-bottom:0;">
            <a href="/quickstart/" class="btn btn-primary">Run OpenRTMP</a>
            <a href="/guides/enhanced-rtmp-v2-explained/" class="btn btn-ghost">Understand E-RTMP v2</a>
          </div>
        </div>
      </article>

      <aside class="toc-card" aria-label="On this page">
        <strong>On this page</strong>
        <a href="#support">AV1 support</a>
        <a href="#requirements">Requirements</a>
        <a href="#openrtmp">Test OpenRTMP</a>
        <a href="#obs">Configure OBS</a>
        <a href="#verify">Verify</a>
        <a href="#rtmps">RTMPS</a>
        <a href="#troubleshooting">Troubleshooting</a>
        <a href="#hevc">AV1 vs HEVC</a>
      </aside>
    </div>
  </section>
</main>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>
