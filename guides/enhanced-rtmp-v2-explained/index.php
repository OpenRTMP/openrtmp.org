<?php
$page = 'guides';
$pageTitle = 'Enhanced RTMP v2 explained — codecs, multitrack, reconnect, and compatibility';
$pageDescription = 'Understand Enhanced RTMP v2 (E-RTMP): FourCC codec signaling, HEVC, AV1, Opus, multitrack audio/video, reconnect requests, timestamp precision, and backward compatibility.';
$canonicalPath = '/guides/enhanced-rtmp-v2-explained/';
$ogType = 'article';
$structuredData = [
  '@context' => 'https://schema.org',
  '@type' => 'TechArticle',
  'headline' => 'Enhanced RTMP v2 explained',
  'description' => $pageDescription,
  'author' => ['@type' => 'Organization', 'name' => 'OpenRTMP'],
  'publisher' => ['@type' => 'Organization', 'name' => 'OpenRTMP'],
  'mainEntityOfPage' => 'https://openrtmp.org/guides/enhanced-rtmp-v2-explained/'
];
include_once __DIR__ . '/../../includes/header.php';
?>

<main>
  <div class="page-hero container article-hero">
    <span class="eyebrow">E-RTMP v2 &middot; Protocol &middot; Media</span>
    <h1>Enhanced RTMP v2 explained</h1>
    <p>E-RTMP v2 keeps RTMP's familiar session foundation while extending the media format and capabilities for modern codecs, multiple tracks, reconnect signaling, metadata, and more precise timing.</p>
  </div>

  <section class="content-section" style="padding-top: 0;">
    <div class="container article-layout">
      <article class="prose">
        <div class="callout"><strong>Specification status:</strong> Veovera's current E-RTMP v2 document is a release-version specification. This guide is an implementation-oriented overview; use the <a href="https://veovera.org/docs/enhanced/enhanced-rtmp-v2.html" target="_blank" rel="noopener">official specification</a> for normative requirements.</div>

        <h2 id="why">Why RTMP needed an extension</h2>
        <p>Traditional RTMP and FLV workflows were designed around the codec landscape of an earlier era. H.264 and AAC became extremely interoperable, but newer video and audio formats did not fit cleanly into the original legacy FLV codec identifiers.</p>
        <p>E-RTMP extends that model instead of replacing the whole protocol. The goal is to preserve the deployed RTMP ecosystem while giving implementations a defined way to signal newer media formats and capabilities.</p>

        <h2 id="no-new-handshake">It is still RTMP</h2>
        <p>One of the most important design choices is what E-RTMP does <em>not</em> do: it does not require a new RTMP handshake version. The enhancements are signaled through additions to the media bitstream and connection capabilities.</p>
        <p>This allows a server implementation to support legacy publishers and enhanced publishers on the same overall protocol stack.</p>

        <h2 id="fourcc">FourCC codec signaling</h2>
        <p>FourCC identifiers provide a more extensible way to identify media codecs than the limited legacy FLV codec fields. E-RTMP uses this mechanism for modern video and audio formats.</p>
        <p>The v2 specification covers advanced video formats such as HEVC, AV1, VP8/VP9 and VVC, and audio formats including Opus, FLAC, AC-3 and E-AC-3. It also defines FourCC signaling for selected legacy codecs where useful in the enhanced model.</p>

        <h2 id="multitrack">Multiple audio and video tracks</h2>
        <p>E-RTMP v2 specifies multitrack capabilities so a session can describe and carry more than the traditional single primary audio/video pair. That matters for workflows such as multiple audio languages, alternate mixes, or more advanced production pipelines.</p>
        <p>Protocol support is only the first layer. A server also needs routing semantics, APIs, persistence rules and player behavior that understand those tracks. When evaluating an implementation, distinguish “the packet format can be parsed” from “the complete application exposes multitrack end to end.”</p>

        <h2 id="reconnect">Reconnect request signaling</h2>
        <p>The v2 specification includes a reconnect-request feature intended to improve resilience and controlled migration of live sessions. The protocol can carry the request, but the host application still has to decide how to act on it and how the replacement transport is established.</p>
        <p>This distinction is especially important in libraries such as <code>librtmp2</code>: session negotiation can expose a reconnect capability while the embedding application owns DNS, load-balancing, socket lifecycle, retry policy and destination selection.</p>

        <h2 id="timing">More precise timing and metadata</h2>
        <p>E-RTMP v2 adds mechanisms for higher-precision timing offsets and expands metadata capabilities without changing the basic RTMP timestamp field itself. This helps modern media pipelines preserve timing information that would otherwise be awkward to express in the legacy format.</p>

        <h2 id="compatibility">Backward compatibility</h2>
        <p>E-RTMP is designed as an extension of legacy RTMP/FLV, but end-to-end compatibility still depends on what a client actually understands. A legacy H.264/AAC publisher can continue to use the traditional path. An AV1 or HEVC publisher requires enhanced signaling and a downstream chain that understands the chosen codec.</p>
        <p>When compatibility matters, keep a legacy baseline test and add enhanced features incrementally.</p>

        <h2 id="openrtmp">E-RTMP v2 in OpenRTMP</h2>
        <p>OpenRTMP's <a href="https://github.com/OpenRTMP/librtmp2" target="_blank" rel="noopener"><code>librtmp2</code></a> contains E-RTMP structures, parsers, session negotiation and live-relay work. Some advanced features can exist as parser/library support before every application-layer behavior is wired into the default server path.</p>
        <p>For that reason, the <a href="https://github.com/OpenRTMP/librtmp2#implementation-status" target="_blank" rel="noopener">implementation-status table</a> is more authoritative than a generic “supports E-RTMP v2” badge.</p>

        <h2 id="test">How to test an implementation</h2>
        <ol>
          <li>Start with H.264/AAC and verify normal RTMP publish/play.</li>
          <li>Test one enhanced codec at a time.</li>
          <li>Verify sequence-start/configuration packets and first-frame startup.</li>
          <li>Join a player after the publisher has already been live.</li>
          <li>Exercise multiple tracks only after single-track enhanced media is stable.</li>
          <li>Test reconnect signaling separately from ordinary network reconnects.</li>
          <li>Capture the exact spec revision and application versions in interoperability reports.</li>
        </ol>

        <h2 id="v1-v2">How v2 relates to the original Enhanced RTMP work</h2>
        <p>The first Enhanced RTMP specification established the modern video-codec and HDR direction that enabled practical HEVC and AV1 RTMP workflows. V2 expands the model substantially with additional audio/video capabilities, multitrack support, reconnect behavior, timing improvements and broader metadata work.</p>
        <p>For a simpler conceptual comparison, read <a href="/guides/rtmp-vs-enhanced-rtmp/">RTMP vs E-RTMP</a>. For codec-focused testing, see <a href="/guides/enhanced-rtmp-hevc-av1-opus/">HEVC, AV1 and Opus in Enhanced RTMP</a>.</p>

        <div class="cta compact-cta">
          <h2>Implement against the specification</h2>
          <p>Use Veovera's release document for normative behavior and OpenRTMP's status table for the exact code path currently implemented.</p>
          <div class="hero-actions" style="margin-bottom:0;">
            <a href="https://veovera.org/docs/enhanced/enhanced-rtmp-v2.html" target="_blank" rel="noopener" class="btn btn-primary">Read E-RTMP v2 spec</a>
            <a href="https://github.com/OpenRTMP/librtmp2#implementation-status" target="_blank" rel="noopener" class="btn btn-ghost">OpenRTMP status</a>
          </div>
        </div>
      </article>

      <aside class="toc-card" aria-label="On this page">
        <strong>On this page</strong>
        <a href="#why">Why E-RTMP</a>
        <a href="#no-new-handshake">Still RTMP</a>
        <a href="#fourcc">FourCC</a>
        <a href="#multitrack">Multitrack</a>
        <a href="#reconnect">Reconnect</a>
        <a href="#timing">Timing</a>
        <a href="#compatibility">Compatibility</a>
        <a href="#openrtmp">OpenRTMP</a>
        <a href="#test">Testing</a>
      </aside>
    </div>
  </section>
</main>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>
