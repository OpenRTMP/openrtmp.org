<?php
$page = 'docs';
$pageTitle = 'Documentation — OpenRTMP';
$pageDescription = 'Getting started with librtmp2: building, the connection state machine, server callbacks, and the public ABI boundary.';
include 'includes/header.php';
?>

<main>
  <div class="page-hero container">
    <h1>Documentation</h1>
    <p>Everything you need to embed librtmp2 in a media pipeline: building, the connection lifecycle, and the public API surface.</p>
  </div>

  <section style="padding-top: 0;">
    <div class="container docs-layout">

      <aside class="docs-nav">
        <h4>On this page</h4>
        <ul>
          <li><a href="#getting-started">Getting Started</a></li>
          <li><a href="#state-machine">Connection State Machine</a></li>
          <li><a href="#callbacks">Host Callbacks</a></li>
          <li><a href="#layers">Layer Reference</a></li>
          <li><a href="#abi">ABI &amp; Versioning</a></li>
        </ul>
        <h4>Repositories</h4>
        <ul>
          <li><a href="https://github.com/openrtmp/librtmp2" target="_blank" rel="noopener">librtmp2</a></li>
          <li><a href="https://github.com/openrtmp/librtmp2-server" target="_blank" rel="noopener">librtmp2-server</a></li>
        </ul>
      </aside>

      <div class="docs-content">

        <h2 id="getting-started">Getting Started</h2>
        <p>Build with Make for local development, or Meson when embedding librtmp2 as a subproject.</p>
        <pre><code>git clone https://github.com/openrtmp/librtmp2.git
cd librtmp2
make release
make test</code></pre>
        <p>For sanitizer builds during development:</p>
        <pre><code>make clean &amp;&amp; make DEBUG=1 ASAN=1 test
make clean &amp;&amp; make DEBUG=1 UBSAN=1 test</code></pre>
        <p>Or via Meson, used for CI and subproject embedding:</p>
        <pre><code>meson setup builddir -Dtests=true -Dexamples=true
meson compile -C builddir
meson test -C builddir</code></pre>

        <h2 id="state-machine">Connection State Machine</h2>
        <p>Every <code>lrtmp2_conn_t</code> moves through a fixed set of states as the handshake, capability negotiation, and stream lifecycle progress:</p>
        <pre><code>TCP_ACCEPTED &rarr; HANDSHAKE &rarr; CONNECTED &rarr; [CAPS_NEGOTIATED] &rarr; APP_CONNECTED &rarr; STREAM_CREATED &rarr; PUBLISHING | PLAYING &rarr; CLOSING &rarr; CLOSED</code></pre>
        <p><code>CAPS_NEGOTIATED</code> is the E-RTMP v2 capability exchange step that sits between <code>CONNECTED</code> and <code>APP_CONNECTED</code>; classic RTMP and E-RTMP v1 peers skip it entirely.</p>

        <h2 id="callbacks">Host Callbacks</h2>
        <p>The library never touches storage, auth, or transcoding &mdash; it just decodes the wire and calls back into your application:</p>
        <div class="table-wrap">
          <table>
            <thead><tr><th>Callback</th><th>Fired when</th></tr></thead>
            <tbody>
              <tr><td><code>on_connect</code></td><td>peer completes the RTMP <code>connect</code> command</td></tr>
              <tr><td><code>on_publish</code></td><td>a stream key starts publishing</td></tr>
              <tr><td><code>on_play</code></td><td>a viewer requests playback of a stream</td></tr>
              <tr><td><code>on_frame</code></td><td>a bounds-checked audio/video/script frame is ready</td></tr>
              <tr><td><code>on_close</code></td><td>the connection is tearing down, for any reason</td></tr>
            </tbody>
          </table>
        </div>

        <h2 id="layers">Layer Reference</h2>
        <p>Ingest flows bottom-up through nine layers before reaching your callbacks. See the <a href="index.php#architecture">architecture overview</a> on the homepage for the full diagram. Key directories in <code>src/</code>:</p>
        <div class="table-wrap">
          <table>
            <thead><tr><th>Directory</th><th>Responsibility</th></tr></thead>
            <tbody>
              <tr><td><code>core/</code></td><td>alloc hook, growable buffers, byte helpers, logging, errors</td></tr>
              <tr><td><code>handshake/</code></td><td>C0/C1/C2 &harr; S0/S1/S2, partial-read buffering, version detection</td></tr>
              <tr><td><code>chunk/</code></td><td>chunk_reader/writer, per-csid chunk_state, SetChunkSize/Abort</td></tr>
              <tr><td><code>message/</code></td><td>reassembled message dispatch &amp; AMF command decode/encode</td></tr>
              <tr><td><code>amf/</code></td><td>AMF0 (mandatory) and AMF3 (optional)</td></tr>
              <tr><td><code>flv/</code></td><td>FLV audio/video/script tag parsing</td></tr>
              <tr><td><code>ertmp/</code></td><td>E-RTMP v1 (ExVideo/ExAudio, FourCC, HDR) + v2 (capsEx, reconnect, multitrack, ModEx)</td></tr>
              <tr><td><code>session/</code></td><td>connection object, state machine, stream bookkeeping</td></tr>
              <tr><td><code>server/</code> &amp; <code>client/</code></td><td>accept loop / per-connection poll &middot; outbound connect &amp; publish/play</td></tr>
            </tbody>
          </table>
        </div>

        <h2 id="abi">ABI &amp; Versioning</h2>
        <p>Only <code>include/librtmp2/*.h</code> is the stable public API. Everything under <code>src/**/*.h</code> may change freely between patch releases &mdash; internal symbols are <code>static</code> or hidden-visibility, never part of the exported ABI.</p>
        <p>librtmp2 follows SemVer: <code>0.x</code> while the API/ABI is still evolving, <code>1.0.0</code> once stable. Every release is checked against the previous one with <code>abi-compliance-checker</code>.</p>

      </div>
    </div>
  </section>
</main>

<?php include 'includes/footer.php'; ?>
