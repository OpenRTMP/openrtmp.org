<?php
$page = 'home';
$pageTitle = 'OpenRTMP — A modern RTMP / E-RTMP protocol library';
$pageDescription = 'librtmp2 is a C protocol library for RTMP and E-RTMP (v1 & v2): handshake, chunking, AMF0/AMF3, FLV and host callbacks — no HTTP, no auth policy.';
include __DIR__ . '/includes/header.php';
?>

<main>

  <section class="hero">
    <div class="container">
      <span class="eyebrow">&#9889; Now with E-RTMP v2 multitrack &amp; ModEx</span>
      <h1>The <span class="gradient">protocol layer</span><br>for RTMP, done right.</h1>
      <p>
        librtmp2 is a C library that speaks RTMP and Enhanced RTMP
        (v1 & v2) on the wire. Your application owns the policy &mdash;
        librtmp2 just hands you clean, bounds-checked events.
      </p>
      <div class="hero-actions">
        <a href="/download/" class="btn btn-primary">Download librtmp2</a>
        <a href="/docs/" class="btn btn-ghost">Read the Docs</a>
      </div>
      <div class="hero-stats">
        <div class="stat"><strong>9</strong><span>protocol layers</span></div>
        <div class="stat"><strong>v1 / v2</strong><span>E-RTMP support</span></div>
        <div class="stat"><strong>MIT</strong><span>licensed</span></div>
      </div>
    </div>
  </section>

  <section>
    <div class="container">
      <div class="code-panel">
        <div class="code-panel-head">
          <div class="traffic"><span></span><span></span><span></span></div>
          <span class="filename">example/ingest.c</span>
        </div>
        <pre><code><span class="kw">static void</span> <span class="fn">on_publish</span>(<span class="type">lrtmp2_conn_t</span> *conn, <span class="kw">const char</span> *stream_key) {
    <span class="fn">printf</span>(<span class="str">"publish started: %s\n"</span>, stream_key);
}

<span class="kw">static void</span> <span class="fn">on_frame</span>(<span class="type">lrtmp2_conn_t</span> *conn, <span class="type">lrtmp2_frame_t</span> *frame) {
    <span class="com">/* bounds-checked FLV/E-RTMP frame, ready to mux or forward */</span>
    <span class="fn">push_to_pipeline</span>(frame->data, frame->size);
}

<span class="type">lrtmp2_server_t</span> *srv = <span class="fn">lrtmp2_server_create</span>(&amp;(<span class="type">lrtmp2_server_cfg_t</span>){
    .port = <span class="str">1935</span>,
    .on_publish = on_publish,
    .on_frame   = on_frame,
});

<span class="kw">while</span> (running) {
    <span class="fn">lrtmp2_server_poll</span>(srv, <span class="str">10</span>); <span class="com">/* ms timeout */</span>
}</code></pre>
      </div>
    </div>
  </section>

  <section id="features">
    <div class="container">
      <div class="section-head">
        <span class="eyebrow">Why librtmp2</span>
        <h2>A protocol library</h2>
        <p>No HTTP, no auth, no opinion about your storage or transcoding. librtmp2 decodes the wire and calls you back.</p>
      </div>
      <div class="grid">
        <div class="card">
          <div class="icon">&#128274;</div>
          <h3>Bounds-checked parsing</h3>
          <p>Every parser treats network input as hostile. Length fields are validated before use, with fuzz harnesses covering handshake, chunk, AMF and FLV decoders.</p>
        </div>
        <div class="card">
          <div class="icon">&#9881;&#65039;</div>
          <h3>E-RTMP v1 &amp; v2</h3>
          <p>ExVideo/ExAudio, FourCC codecs, HDR metadata, capsEx negotiation, reconnect, and multitrack &mdash; with unknown ModEx types degrading to a safe NOP.</p>
        </div>
        <div class="card">
          <div class="icon">&#129505;</div>
          <h3>Single-threaded, predictable</h3>
          <p>One thread drives one connection. Per-connection chunk state means clients and servers can coexist safely with zero shared mutable state.</p>
        </div>
        <div class="card">
          <div class="icon">&#128268;</div>
          <h3>Stable ABI boundary</h3>
          <p>Only <code>include/librtmp2/*.h</code> is public. Internal headers move freely under semantic versioning, checked against the previous release before every tag.</p>
        </div>
        <div class="card">
          <div class="icon">&#129514;</div>
          <h3>Host owns the policy</h3>
          <p>Auth, recording, transcoding, routing &mdash; all yours. librtmp2 emits <code>on_connect</code>, <code>on_publish</code>, <code>on_play</code>, <code>on_frame</code>, <code>on_close</code> and gets out of the way.</p>
        </div>
      </div>
    </div>
  </section>

  <section id="architecture">
    <div class="container">
      <div class="section-head">
        <span class="eyebrow">Architecture</span>
        <h2>A clean layer stack, bottom to top</h2>
        <p>Each layer does one job. Ingest flows up through handshake, chunk, message and AMF/FLV/E-RTMP decoding before reaching your callbacks.</p>
      </div>
      <div class="stack">
        <div class="stack-row"><span class="layer-name">core/</span><span class="layer-desc">alloc hook, growable buffers, big-endian byte helpers, logging, errors</span></div>
        <div class="stack-arrow">&#8593;</div>
        <div class="stack-row"><span class="layer-name">handshake/</span><span class="layer-desc">C0/C1/C2 &harr; S0/S1/S2, partial-read buffering, version detection</span></div>
        <div class="stack-arrow">&#8593;</div>
        <div class="stack-row"><span class="layer-name">chunk/</span><span class="layer-desc">chunk_reader, chunk_writer, per-csid chunk_state, SetChunkSize/Abort</span></div>
        <div class="stack-arrow">&#8593;</div>
        <div class="stack-row"><span class="layer-name">message/</span><span class="layer-desc">reassembled message dispatch: control, user-control, AMF command decode/encode</span></div>
        <div class="stack-arrow">&#8593;</div>
        <div class="stack-row"><span class="layer-name">amf/ &middot; flv/ &middot; ertmp/</span><span class="layer-desc">AMF0/AMF3 &middot; FLV tag parsing &middot; ExVideo/ExAudio, FourCC, HDR, capsEx, multitrack</span></div>
        <div class="stack-arrow">&#8593;</div>
        <div class="stack-row"><span class="layer-name">session/</span><span class="layer-desc">connection object, state machine, stream bookkeeping, publish/play flows</span></div>
        <div class="stack-arrow">&#8593;</div>
        <div class="stack-row"><span class="layer-name">server/ &middot; client/</span><span class="layer-desc">accept loop &amp; per-connection poll &middot; outbound connect &rarr; publish/play</span></div>
      </div>
    </div>
  </section>

  <section id="ecosystem">
    <div class="container">
      <div class="section-head">
        <span class="eyebrow">Ecosystem</span>
        <h2>A library, a server, and a panel</h2>
        <p>librtmp2 is the protocol layer. librtmp2-server is the reference application. librtmp2-server-panel is the web UI that ties it all together.</p>
      </div>
      <div class="grid">
        <div class="card">
          <div class="icon">&#128230;</div>
          <h3>librtmp2</h3>
          <p>The C library: handshake, chunking, AMF0/AMF3, FLV and E-RTMP v1/v2 decoding, delivered through host callbacks. No server loop, no storage, no policy.</p>
          <p style="margin-top: 14px;"><a href="https://github.com/OpenRTMP/librtmp2" target="_blank" rel="noopener" class="btn btn-ghost">View librtmp2 &rarr;</a></p>
        </div>
        <div class="card">
          <div class="icon">&#128268;</div>
          <h3>librtmp2-server</h3>
          <p>A standalone ingest/playback server built on top of librtmp2's callbacks. It wires up <code>on_connect</code>, <code>on_publish</code>, <code>on_play</code> and <code>on_frame</code> into a working RTMP/E-RTMP endpoint &mdash; use it as-is, or as a blueprint for your own server.</p>
          <p style="margin-top: 14px;"><a href="https://github.com/OpenRTMP/librtmp2-server" target="_blank" rel="noopener" class="btn btn-ghost">View librtmp2-server &rarr;</a></p>
        </div>
        <div class="card">
          <div class="icon">&#127912;</div>
          <h3>librtmp2-server-panel</h3>
          <p>A Flask web panel for managing librtmp2-server. Create streams, monitor stats (bitrate, codec, viewership), and manage keys &mdash; all from a browser. Encrypted key storage, CSRF protection, rate limiting, multi-user support.</p>
          <p style="margin-top: 14px;"><a href="https://github.com/OpenRTMP/librtmp2-server-panel" target="_blank" rel="noopener" class="btn btn-ghost">View librtmp2-server-panel &rarr;</a></p>
        </div>
      </div>
    </div>
  </section>

  <section>
    <div class="container">
      <div class="cta">
        <h2>Ready to speak RTMP?</h2>
        <p>Clone the library, link it against your media pipeline, and start handling real publishers in minutes. Or deploy the full stack with the server and panel.</p>
        <div class="hero-actions" style="margin-bottom:0;">
          <a href="/download/" class="btn btn-primary">Download librtmp2</a>
          <a href="https://github.com/OpenRTMP/librtmp2" target="_blank" rel="noopener" class="btn btn-ghost">View Source on GitHub</a>
        </div>
      </div>
    </div>
  </section>

</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
