<?php
$page = 'docs';
$pageTitle = 'Documentation — OpenRTMP';
$pageDescription = 'Getting started with librtmp2 and librtmp2-server: building with Cargo, the connection state machine, server callbacks, and the public crate API.';
include __DIR__ . '/../includes/header.php';
?>

<main>
  <div class="page-hero container">
    <h1>Documentation</h1>
    <p>Everything you need to embed librtmp2 in a media pipeline: building with Cargo, the connection lifecycle, the public crate API surface, and the reference server.</p>
  </div>

  <section style="padding-top: 0;">
    <div class="container docs-layout">

      <aside class="docs-nav">
        <h4>On this page</h4>
        <ul>
          <li><a href="#getting-started">Getting Started</a></li>
          <li><a href="#state-machine">Connection State Machine</a></li>
          <li><a href="#callbacks">Host Callbacks</a></li>
          <li><a href="#layers">Module Reference</a></li>
          <li><a href="#server">librtmp2-server</a></li>
          <li><a href="#abi">API &amp; Versioning</a></li>
        </ul>
        <h4>Repositories</h4>
        <ul>
          <li><a href="https://github.com/OpenRTMP/librtmp2" target="_blank" rel="noopener">librtmp2</a></li>
          <li><a href="https://github.com/OpenRTMP/librtmp2-server" target="_blank" rel="noopener">librtmp2-server</a></li>
          <li><a href="https://github.com/OpenRTMP/librtmp2-server-panel" target="_blank" rel="noopener">librtmp2-server-panel</a></li>
        </ul>
      </aside>

      <div class="docs-content">

        <h2 id="getting-started">Getting Started</h2>
        <p>Add librtmp2 as a dependency in your <code>Cargo.toml</code>:</p>
        <pre><code>[dependencies.lrtmp2]
git = "https://github.com/OpenRTMP/librtmp2.git"</code></pre>
        <p>Or clone and build locally for development:</p>
        <pre><code>git clone https://github.com/OpenRTMP/librtmp2.git
cd librtmp2
cargo build --release
cargo test</code></pre>
        <p>For sanitizer builds during development:</p>
        <pre><code>RUSTFLAGS="-Z sanitizer=address" cargo test
RUSTFLAGS="-Z sanitizer=undefined" cargo test</code></pre>

        <h2 id="state-machine">Connection State Machine</h2>
        <p>Every <code>Lrtmp2Conn</code> moves through a fixed set of states as the handshake, capability negotiation, and stream lifecycle progress:</p>
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
        <p>Ingest flows bottom-up through nine layers before reaching your callbacks. See the <a href="/#architecture">architecture overview</a> on the homepage for the full diagram. Key directories in <code>src/</code>:</p>
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

        <h2 id="server">librtmp2-server</h2>
        <p><a href="https://github.com/OpenRTMP/librtmp2-server" target="_blank" rel="noopener">librtmp2-server</a> is the reference ingest/playback server built on top of librtmp2. It is a separate repository and binary &mdash; librtmp2 itself stays free of any server loop, socket policy, or storage decisions.</p>
        <p>What it adds on top of the library:</p>
        <div class="table-wrap">
          <table>
            <thead><tr><th>Piece</th><th>What it does</th></tr></thead>
            <tbody>
              <tr><td>Accept loop</td><td>drives <code>lrtmp2_server_poll()</code> for many concurrent publishers and players over loopback or LAN</td></tr>
              <tr><td>Stream registry</td><td>maps stream keys from <code>on_publish</code> to subscriber lists built from <code>on_play</code></td></tr>
              <tr><td>Frame fan-out</td><td>forwards each <code>on_frame</code> from a publisher to every matching player, GOP-aware</td></tr>
              <tr><td>Process model</td><td>single-threaded per connection, same as librtmp2 &mdash; safe to run many connections per process</td></tr>
            </tbody>
          </table>
        </div>
        <p>Build it the same way as the library:</p>
        <pre><code>git clone https://github.com/OpenRTMP/librtmp2-server.git
cd librtmp2-server
cargo build --release</code></pre>
        <p>Use it as a drop-in ingest/playback endpoint, or read its source as a worked example of wiring host callbacks into a real server &mdash; see the <a href="/download/">download page</a> for build details.</p>

        <h2 id="abi">API &amp; Versioning</h2>
        <p>Only the public <code>lrtmp2</code> crate interface is the stable API. Everything under <code>src/**/*</code> that is not <code>pub</code> may change freely between patch releases &mdash; internal modules are private, never part of the exported crate API.</p>
        <p>librtmp2 follows SemVer: <code>0.x</code> while the API is still evolving, <code>1.0.0</code> once stable. Every release is checked against the previous one for API compatibility.</p>

      </div>
    </div>
  </section>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
