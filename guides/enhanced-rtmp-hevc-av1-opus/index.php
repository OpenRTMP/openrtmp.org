<?php
$page = 'guides';
$pageTitle = 'Enhanced RTMP with HEVC, AV1, and Opus — OpenRTMP Guide';
$pageDescription = 'Understand Enhanced RTMP media signaling for HEVC, AV1, and Opus, how it differs from legacy RTMP, and the current OpenRTMP implementation boundaries.';
$canonicalPath = '/guides/enhanced-rtmp-hevc-av1-opus/';
$ogType = 'article';
$structuredData = [
  '@context' => 'https://schema.org',
  '@type' => 'TechArticle',
  'headline' => 'Enhanced RTMP with HEVC, AV1, and Opus',
  'description' => $pageDescription,
  'author' => ['@type' => 'Organization', 'name' => 'OpenRTMP'],
  'mainEntityOfPage' => 'https://openrtmp.org/guides/enhanced-rtmp-hevc-av1-opus/'
];
include __DIR__ . '/../../includes/header.php';
?>

<main>
  <div class="page-hero container article-hero">
    <span class="eyebrow">E-RTMP &middot; HEVC &middot; AV1 &middot; Opus</span>
    <h1>Enhanced RTMP codecs explained</h1>
    <p>Enhanced RTMP extends the media signaling used by classic RTMP/FLV so modern codecs and richer capabilities can travel through familiar RTMP publishing workflows.</p>
  </div>

  <section class="content-section" style="padding-top: 0;">
    <div class="container article-layout">
      <article class="prose">
        <div class="callout warning"><strong>Implementation note:</strong> codec passthrough, parser modules, and full session-level negotiation are different things. Always check the current <a href="https://github.com/OpenRTMP/librtmp2#implementation-status" target="_blank" rel="noopener">implementation status</a> before depending on a feature.</div>

        <h2 id="legacy">Why legacy RTMP needed an extension</h2>
        <p>Classic RTMP commonly carries media using FLV-era codec identifiers. That model works well for established H.264/AAC workflows but does not naturally describe newer codecs such as HEVC, AV1, or Opus.</p>
        <p>Enhanced RTMP introduces extended audio/video signaling and FourCC-based codec identification. This allows a sender and receiver to distinguish modern codec payloads without pretending they are legacy FLV codecs.</p>

        <h2 id="concepts">Key concepts</h2>
        <h3>Enhanced video and audio packets</h3>
        <p>Extended packet forms carry information beyond the small legacy codec identifier space. Implementations must parse the extended header correctly before interpreting codec-specific payloads.</p>
        <h3>FourCC codec identifiers</h3>
        <p>Four-character codes identify codecs such as HEVC or AV1 in a way that is more extensible than the original FLV enumeration.</p>
        <h3>Capability negotiation</h3>
        <p>Enhanced RTMP v2 adds structures for advertising and negotiating supported capabilities. Having parser code for these structures is not the same as completing every negotiation path in a production session state machine.</p>
        <h3>Multitrack and ModEx</h3>
        <p>Later extensions describe multiple media tracks and modular metadata. Applications must decide how those tracks map into their own routing, playback, recording, and statistics models.</p>

        <h2 id="openrtmp">How OpenRTMP handles Enhanced RTMP</h2>
        <p>OpenRTMP separates several layers:</p>
        <ul>
          <li><strong>Wire parser/serializer modules:</strong> reusable structures for enhanced media and protocol extensions.</li>
          <li><strong>Default live session path:</strong> the code actually used when publishers and players connect through the library.</li>
          <li><strong>Server application layer:</strong> authentication, routing, stream registry, statistics, and API behavior built around the library.</li>
        </ul>
        <p>Current OpenRTMP workflows can relay enhanced media payloads from compatible OBS/FFmpeg publishers. Some advanced v2 features may exist as library code and tests without being fully integrated into every default live-session path. The repository's status table is the source of truth.</p>

        <h2 id="codecs">Codec expectations</h2>
        <table>
          <thead><tr><th>Codec</th><th>What to verify</th></tr></thead>
          <tbody>
            <tr><td>H.264 + AAC</td><td>Legacy initialization frames, publisher/player compatibility, and late-join playback</td></tr>
            <tr><td>HEVC</td><td>Enhanced video signaling, sequence-start handling, player support, and actual decoder availability</td></tr>
            <tr><td>AV1</td><td>Sender support, FourCC signaling, decoder support, and client CPU/GPU requirements</td></tr>
            <tr><td>Opus</td><td>Enhanced audio signaling and whether the receiving player understands Opus in this RTMP workflow</td></tr>
          </tbody>
        </table>
        <p>A successful publish does not guarantee that every downstream player can decode the codec. Test the complete publisher &rarr; server &rarr; player chain.</p>

        <h2 id="testing">A practical interoperability test plan</h2>
        <ol>
          <li>Record the exact OBS or FFmpeg version and command/settings.</li>
          <li>Test one publisher and one player with H.264/AAC as a baseline.</li>
          <li>Change only one codec at a time.</li>
          <li>Verify initial playback and late player join.</li>
          <li>Disconnect and reconnect the publisher.</li>
          <li>Test multiple players and prolonged streaming.</li>
          <li>Inspect server statistics and logs for codec detection.</li>
          <li>Repeat over RTMPS if encrypted ingest is part of the deployment.</li>
        </ol>

        <h2 id="library">Using librtmp2 in your own application</h2>
        <p>Developers can consume the Rust crate directly or use the generated dynamic/static library through the C-compatible FFI. The host application remains responsible for decisions such as authorization, storage, transcoding, recording, and track routing.</p>
        <pre><code>[dependencies]
librtmp2 = "0.4"</code></pre>
        <p>While the project remains in <code>0.x</code>, pin the version range appropriate for your compatibility policy and review release notes before updating.</p>

        <h2 id="avoid-overclaiming">Avoid these common assumptions</h2>
        <ul>
          <li>Parser support does not automatically mean a feature is wired into the default session path.</li>
          <li>Opaque media relay does not mean the server can transcode or inspect every codec detail.</li>
          <li>Publisher support does not imply universal player support.</li>
          <li>Multitrack protocol structures do not automatically create a complete multitrack control API.</li>
          <li>Enhanced RTMP does not replace the need for interoperability testing across versions.</li>
        </ul>

        <div class="cta compact-cta">
          <h2>Check the code-accurate status</h2>
          <p>The repository documents what is complete, partial, parser-only, or not yet implemented.</p>
          <div class="hero-actions" style="margin-bottom:0;">
            <a href="https://github.com/OpenRTMP/librtmp2#implementation-status" target="_blank" rel="noopener" class="btn btn-primary">Implementation status</a>
            <a href="https://docs.rs/librtmp2" target="_blank" rel="noopener" class="btn btn-ghost">docs.rs</a>
          </div>
        </div>
      </article>

      <aside class="toc-card" aria-label="On this page">
        <strong>On this page</strong>
        <a href="#legacy">Why Enhanced RTMP</a>
        <a href="#concepts">Key concepts</a>
        <a href="#openrtmp">OpenRTMP behavior</a>
        <a href="#codecs">Codec expectations</a>
        <a href="#testing">Test plan</a>
        <a href="#library">Using the library</a>
        <a href="#avoid-overclaiming">Common assumptions</a>
      </aside>
    </div>
  </section>
</main>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
