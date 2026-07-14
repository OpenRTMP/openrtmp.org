<?php
$page = 'home';
$pageTitle = 'OpenRTMP — A modern RTMP / E-RTMP protocol library';
$pageDescription = 'librtmp2 is a Rust protocol library for RTMP and E-RTMP (v1 & v2): handshake, chunking, AMF0/AMF3, FLV and host callbacks — no HTTP, no auth policy.';
include __DIR__ . '/includes/header.php';
?>

<main>

  <section class="hero">
    <div class="container">
      <span class="eyebrow">&#9889; Now with E-RTMP v2 multitrack &amp; ModEx</span>
      <h1>The <span class="gradient">protocol layer</span><br>for RTMP, done right.</h1>
      <p>
        librtmp2 is a Rust library that speaks RTMP and Enhanced RTMP
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
        <div class="stat"><strong>0.3.x</strong><span>alpha (Rust)</span></div>
        <div class="stat"><strong>MIT</strong><span>licensed</span></div>
      </div>
    </div>
  </section>

  <section>
    <div class="container">
      <div class="code-panel">
        <div class="code-panel-head">
          <div class="traffic"><span></span><span></span><span></span></div>
          <span class="filename">examples/minimal_server.rs</span>
        </div>
        <pre><code><span class="kw">use</span> librtmp2::server::Server;
<span class="kw">use</span> librtmp2::types::*;

<span class="kw">fn</span> <span class="fn">on_frame</span>(frame: <span class="kw">&amp;</span><span class="type">Frame</span>) {
    <span class="com">// bounds-checked FLV/E-RTMP frame, ready to mux or forward</span>
    <span class="fn">push_to_pipeline</span>(frame.data, frame.size);
}

<span class="kw">fn</span> <span class="fn">on_publish</span>(_id: <span class="type">u64</span>, app: <span class="kw">&amp;</span><span class="type">str</span>, key: <span class="kw">&amp;</span><span class="type">str</span>) -> <span class="type">bool</span> {
    <span class="fn">println!</span>(<span class="str">"publish: {app}/{key}"</span>);
    <span class="kw">true</span> <span class="com">// return false to reject</span>
}

<span class="kw">let</span> <span class="kw">mut</span> server = <span class="type">Server</span>::<span class="fn">new</span>(config)?;
server.on_frame_cb = <span class="kw">Some</span>(on_frame);
server.on_publish_cb = <span class="kw">Some</span>(on_publish);
server.<span class="fn">listen</span>(<span class="str">"0.0.0.0:1935"</span>)?;

<span class="kw">while</span> running {
    server.<span class="fn">poll</span>(<span class="str">10</span>)?; <span class="com">// ms timeout</span>
}</code></pre>
      </div>
    </div>
  </section>

  <section id="features">
    <div class="container">
      <div class="section-head">
        <span class="eyebrow">Why librtmp2</span>
        <h2>A protocol library</h2>
        <p>No HTTP, no auth, no opinion about your storage or transcoding. librtmp2 decodes the wire and calls you back &mdash; with Rust safety guarantees.</p>
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
          <h3>Stable crate API</h3>
          <p>Only the public <code>librtmp2</code> crate interface is the intended stable surface. Internal modules may change freely while the project is in <code>0.x</code> alpha &mdash; use <code>version = "0"</code> and <code>cargo update -p librtmp2</code> to stay current.</p>
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
          <p>The Rust library: handshake, chunking, AMF0/AMF3, FLV and E-RTMP v1/v2 decoding, delivered through host callbacks. No server loop, no storage, no policy.</p>
          <p style="margin-top: 14px;"><a href="https://github.com/OpenRTMP/librtmp2" target="_blank" rel="noopener" class="btn btn-ghost">View librtmp2 &rarr;</a></p>
        </div>
        <div class="card">
          <div class="icon">&#128268;</div>
          <h3>librtmp2-server</h3>
          <p>A standalone RTMP/E-RTMP ingest and playback server (Alpha). SQLite-backed stream registry, per-stream publish/play/stats keys, JSON and nginx-compatible XML stats, REST API, optional RTMPS alongside plaintext RTMP.</p>
          <p style="margin-top: 14px;"><a href="/docs/#server" class="btn btn-ghost">Server docs &rarr;</a> <a href="/docs/#docker" class="btn btn-ghost">Docker &rarr;</a></p>
        </div>
        <div class="card">
          <div class="icon">&#127912;</div>
          <h3>librtmp2-server-panel</h3>
          <p>A Flask web panel for managing librtmp2-server. Create streams, monitor live stats (bitrate, codec, RTT, viewership), and copy publish/play URLs &mdash; with encrypted key storage, CSRF protection, rate limiting, and multi-user login.</p>
          <p style="margin-top: 14px;"><a href="/docs/#panel" class="btn btn-ghost">Panel docs &rarr;</a> <a href="/download/#docker-stack" class="btn btn-ghost">Docker stack &rarr;</a></p>
        </div>
      </div>
    </div>
  </section>

  <section>
    <div class="container">
      <div class="cta">
        <h2>Ready to speak RTMP?</h2>
        <p>Add the <code>librtmp2</code> crate, wire callbacks into your media pipeline, and start handling real publishers in minutes. Or deploy the full stack &mdash; server plus web panel &mdash; with Docker in a few commands.</p>
        <div class="hero-actions" style="margin-bottom:0;">
          <a href="/download/" class="btn btn-primary">Download &amp; Docker</a>
          <a href="/docs/#docker" class="btn btn-ghost">Full-stack Docker guide</a>
        </div>
      </div>
    </div>
  </section>

</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
