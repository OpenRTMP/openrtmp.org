<?php
$page = 'guides';
$pageTitle = 'RTMP vs Enhanced RTMP (E-RTMP): what is different? — OpenRTMP';
$pageDescription = 'Compare legacy RTMP with Enhanced RTMP (E-RTMP), including FourCC codec signaling, HEVC, AV1, Opus, multitrack, reconnect support, compatibility, and migration.';
$canonicalPath = '/guides/rtmp-vs-enhanced-rtmp/';
$ogType = 'article';
$structuredData = [
  '@context' => 'https://schema.org',
  '@type' => 'TechArticle',
  'headline' => 'RTMP vs Enhanced RTMP (E-RTMP): what is different?',
  'description' => $pageDescription,
  'author' => ['@type' => 'Organization', 'name' => 'OpenRTMP'],
  'publisher' => ['@type' => 'Organization', 'name' => 'OpenRTMP'],
  'mainEntityOfPage' => 'https://openrtmp.org/guides/rtmp-vs-enhanced-rtmp/'
];
include_once __DIR__ . '/../../includes/header.php';
?>

<main>
  <div class="page-hero container article-hero">
    <span class="eyebrow">RTMP &middot; E-RTMP &middot; Protocol</span>
    <h1>RTMP vs Enhanced RTMP (E-RTMP)</h1>
    <p>Enhanced RTMP modernizes the established RTMP/FLV media path without throwing away the compatibility model that made RTMP useful for live ingest.</p>
  </div>

  <section class="content-section" style="padding-top: 0;">
    <div class="container article-layout">
      <article class="prose">
        <div class="callout"><strong>Short version:</strong> legacy RTMP remains the transport and session foundation. E-RTMP extends the media signaling and capabilities around it so modern codecs and newer streaming features can be negotiated without requiring a completely new ingest protocol.</div>

        <h2 id="legacy">What legacy RTMP provides</h2>
        <p>RTMP is an application-level protocol built around a handshake, chunk streams, control messages, AMF commands, and media messages. Traditional live workflows normally carry H.264 video and AAC audio using the legacy FLV signaling model.</p>
        <p>That combination remains extremely interoperable with encoders, media servers, and production tools, which is why RTMP is still widely used as an ingest protocol even though Flash itself is long gone.</p>

        <h2 id="enhanced">What E-RTMP changes</h2>
        <p>The <a href="https://veovera.org/docs/enhanced/enhanced-rtmp-v2.html" target="_blank" rel="noopener">Veovera E-RTMP v2 specification</a> extends RTMP and FLV while preserving the legacy protocol as part of the ecosystem. The enhancements include:</p>
        <ul>
          <li><strong>FourCC-based codec signaling</strong> for modern video and audio formats.</li>
          <li><strong>Modern video codecs</strong> including HEVC, AV1, VP9, and VVC signaling.</li>
          <li><strong>Modern audio codecs</strong> including Opus and FLAC signaling.</li>
          <li><strong>Multitrack capabilities</strong> for multiple audio or video tracks.</li>
          <li><strong>Reconnect request signaling</strong> for more resilient workflows.</li>
          <li><strong>Additional metadata and timestamp precision</strong> for modern media pipelines.</li>
        </ul>
        <p>E-RTMP does not require a new legacy RTMP handshake version. Its capabilities are introduced through additions to the bitstream and session signaling, which is important for incremental deployment.</p>

        <h2 id="comparison">Legacy RTMP and E-RTMP side by side</h2>
        <table class="comparison-table">
          <thead><tr><th>Area</th><th>Legacy RTMP</th><th>E-RTMP</th></tr></thead>
          <tbody>
            <tr><td>Session foundation</td><td>RTMP handshake, chunks, AMF commands</td><td>Builds on the same RTMP foundation</td></tr>
            <tr><td>Typical video</td><td>H.264</td><td>H.264 plus modern FourCC-signaled codecs such as HEVC and AV1</td></tr>
            <tr><td>Typical audio</td><td>AAC / MP3-era FLV signaling</td><td>Adds modern codec signaling such as Opus and FLAC</td></tr>
            <tr><td>Codec identification</td><td>Legacy FLV codec identifiers</td><td>FourCC-based signaling is available</td></tr>
            <tr><td>Multiple media tracks</td><td>Not designed around the modern E-RTMP multitrack model</td><td>Multitrack capabilities are specified</td></tr>
            <tr><td>Reconnect signaling</td><td>No E-RTMP reconnect-request feature</td><td>Reconnect request is part of the v2 specification</td></tr>
            <tr><td>Compatibility goal</td><td>Maximum legacy interoperability</td><td>Extend RTMP while retaining backward-compatible behavior where possible</td></tr>
          </tbody>
        </table>

        <h2 id="compatibility">Does E-RTMP break old RTMP clients?</h2>
        <p>Not automatically. A server can continue accepting conventional H.264/AAC publishers while also understanding enhanced packet types. The important caveat is the <em>complete chain</em>: a publisher may successfully send HEVC or AV1 while an older player cannot decode or even understand the resulting media signaling.</p>
        <p>For mixed environments, treat H.264/AAC as the compatibility baseline and enable enhanced codecs only where the sender, server, relay path, and receiver have all been validated.</p>

        <h2 id="obs">OBS and Enhanced RTMP</h2>
        <p>OBS Studio added AV1 and HEVC streaming over Enhanced RTMP in OBS 29.1. Current OBS versions contain the protocol-side support, but whether a specific codec can be selected still depends on the chosen output/service configuration and an available encoder.</p>
        <p>For a practical setup, see <a href="/guides/hevc-streaming-obs/">HEVC streaming with OBS</a> and <a href="/guides/av1-over-rtmp/">AV1 over RTMP</a>.</p>

        <h2 id="openrtmp">How OpenRTMP handles both</h2>
        <p><a href="https://github.com/OpenRTMP/librtmp2" target="_blank" rel="noopener"><code>librtmp2</code></a> is designed around both legacy RTMP and E-RTMP work. The default live path supports conventional publish/play workflows and enhanced-media passthrough where implemented, while additional E-RTMP parser and negotiation features continue to evolve.</p>
        <p>Do not infer full end-to-end support from the existence of a parser type. The repository's <a href="https://github.com/OpenRTMP/librtmp2#implementation-status" target="_blank" rel="noopener">implementation-status table</a> is the code-accurate source for what is complete, partial, parser-only, or still experimental.</p>

        <h2 id="migration">A safe migration path</h2>
        <ol>
          <li>Establish a known-good H.264/AAC RTMP publish and play workflow.</li>
          <li>Record the exact OBS/FFmpeg, server, and player versions.</li>
          <li>Change only the video codec to HEVC or AV1.</li>
          <li>Verify codec detection, first-frame startup, and late-player join.</li>
          <li>Test disconnect/reconnect behavior and longer sessions.</li>
          <li>Repeat over RTMPS if encrypted ingest is required.</li>
          <li>Keep a legacy H.264 path available until every required client is proven compatible.</li>
        </ol>

        <h2 id="which">Which one should you use?</h2>
        <p>Use conventional H.264/AAC RTMP when interoperability is the main requirement. Use E-RTMP features when you control enough of the media chain to benefit from newer codecs, multitrack signaling, or newer protocol capabilities and can test those clients explicitly.</p>
        <p>The choice is therefore not really “RTMP or E-RTMP.” E-RTMP is an evolution of the RTMP ecosystem, and modern servers can support both paths at the same time.</p>

        <div class="cta compact-cta">
          <h2>Explore the enhanced media path</h2>
          <p>Start with the broader codec guide, then validate your exact sender and player combination.</p>
          <div class="hero-actions" style="margin-bottom:0;">
            <a href="/guides/enhanced-rtmp-hevc-av1-opus/" class="btn btn-primary">Enhanced codec guide</a>
            <a href="/quickstart/" class="btn btn-ghost">Run OpenRTMP</a>
          </div>
        </div>
      </article>

      <aside class="toc-card" aria-label="On this page">
        <strong>On this page</strong>
        <a href="#legacy">Legacy RTMP</a>
        <a href="#enhanced">What E-RTMP changes</a>
        <a href="#comparison">Comparison</a>
        <a href="#compatibility">Compatibility</a>
        <a href="#obs">OBS</a>
        <a href="#openrtmp">OpenRTMP</a>
        <a href="#migration">Migration</a>
        <a href="#which">Which to use</a>
      </aside>
    </div>
  </section>
</main>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>
