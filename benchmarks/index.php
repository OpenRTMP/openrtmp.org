<?php
$page = 'benchmarks';
$pageTitle = 'RTMP server benchmarks — OpenRTMP vs nginx-rtmp, MediaMTX, SRS and LiveForge';
$pageDescription = 'librtmp2-server benchmarked against nginx-rtmp, MediaMTX, SRS 8.0 and LiveForge: handshake latency, handshakes per second, and viewer join latency at 1, 25 and 100 concurrent viewers.';
$canonicalPath = '/benchmarks/';
$ogType = 'article';
$structuredData = [
  '@context' => 'https://schema.org',
  '@type' => 'TechArticle',
  'headline' => 'RTMP server benchmarks: OpenRTMP vs nginx-rtmp, MediaMTX, SRS and LiveForge',
  'description' => $pageDescription,
  'author' => ['@type' => 'Organization', 'name' => 'OpenRTMP'],
  'publisher' => ['@type' => 'Organization', 'name' => 'OpenRTMP'],
  'mainEntityOfPage' => 'https://openrtmp.org/benchmarks/'
];
include_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/benchmarks-data.php';

$roundLabels = [
  'seq_connect' => 'Connect + publish, sequential',
  'single_join' => 'Single-viewer join',
  'join_100' => 'Join with 100 viewers',
  'connect_30' => 'Connect + publish, 30 concurrent',
];
?>

<main>
  <div class="page-hero container article-hero">
    <span class="eyebrow">Benchmarks &middot; RTMP relay &middot; <?php echo OPENRTMP_BENCH_DATE; ?></span>
    <h1>RTMP server benchmarks</h1>
    <p><code>librtmp2-server</code> against nginx-rtmp, MediaMTX, SRS 8.0 and LiveForge: same machine, same RTMP client, same real H.264/AAC stream. OpenRTMP has the lowest average latency in every test.</p>
  </div>

  <section class="content-section" style="padding-top: 0;">
    <div class="container article-layout">
      <article class="prose">
        <div class="bench-highlights">
          <div class="bench-highlight">
            <strong><?php echo benchMs(OPENRTMP_BENCH_JOIN[100]['openrtmp'][0]); ?></strong>
            <span>join with 100 viewers</span>
            <p>MediaMTX: <?php echo benchMs(OPENRTMP_BENCH_JOIN[100]['mediamtx'][0]); ?></p>
          </div>
          <div class="bench-highlight">
            <strong><?php echo benchMs(OPENRTMP_BENCH_HANDSHAKE['openrtmp'][1]); ?></strong>
            <span>connect + publish</span>
            <p>nginx-rtmp: <?php echo benchMs(OPENRTMP_BENCH_HANDSHAKE['nginx'][1]); ?></p>
          </div>
          <div class="bench-highlight">
            <strong><?php echo benchNum(OPENRTMP_BENCH_HANDSHAKE['openrtmp'][0], 0); ?>/s</strong>
            <span>handshakes per second</span>
            <p>MediaMTX: <?php echo benchNum(OPENRTMP_BENCH_HANDSHAKE['mediamtx'][0], 0); ?>/s</p>
          </div>
        </div>

        <h2 id="handshake">Connect and publish</h2>
        <p>120 RTMP connect + publish handshakes (up to <code>NetStream.Publish.Start</code>) at a concurrency of 30. Every server completed every handshake. <code>librtmp2-server</code> is the only one here that checks each publish against a per-stream key; the others accept any stream name.</p>
        <div class="bench-charts">
          <div class="bench-chart">
            <h3>Average handshake latency</h3>
            <p class="bench-note">Lower is better.</p>
            <?php echo benchBars(array_map(fn($r) => $r[1], OPENRTMP_BENCH_HANDSHAKE), fn($v) => benchMs($v)); ?>
          </div>
          <div class="bench-chart">
            <h3>Handshakes per second</h3>
            <p class="bench-note">Higher is better.</p>
            <?php echo benchBars(array_map(fn($r) => $r[0], OPENRTMP_BENCH_HANDSHAKE), fn($v) => benchNum($v, 0) . '/s'); ?>
          </div>
        </div>
        <table>
          <thead><tr><th>Server</th><th>Handshakes/s</th><th>avg</th><th>p50</th><th>p95</th><th>p99</th></tr></thead>
          <tbody>
            <?php foreach (OPENRTMP_BENCH_HANDSHAKE as $key => [$rate, $avg, $p50, $p95, $p99]): ?>
            <tr<?php echo $key === 'openrtmp' ? ' class="is-openrtmp"' : ''; ?>><td><?php echo OPENRTMP_BENCH_SERVERS[$key]; ?></td><td><?php echo benchNum($rate, 1); ?></td><td><?php echo benchMs($avg); ?></td><td><?php echo benchMs($p50); ?></td><td><?php echo benchMs($p95); ?></td><td><?php echo benchMs($p99); ?></td></tr>
            <?php endforeach; ?>
          </tbody>
        </table>

        <h2 id="join">Viewer join latency</h2>
        <p>One ffmpeg publisher sends a 1280x720@30 H.264 stream at 2.5 Mbps with 128 kbps AAC audio. Then 1, 25 or 100 viewers connect at the same time and play it. Join latency is the time from connecting to the first received frame.</p>
        <div class="bench-chart">
          <h3>100 concurrent viewers</h3>
          <p class="bench-note">Average join latency. Lower is better.</p>
          <?php echo benchBars(array_map(fn($r) => $r[0], OPENRTMP_BENCH_JOIN[100]), fn($v) => benchMs($v)); ?>
        </div>
        <div class="bench-charts">
          <div class="bench-chart">
            <h3>25 concurrent viewers</h3>
            <p class="bench-note">Average join latency. Lower is better.</p>
            <?php echo benchBars(array_map(fn($r) => $r[0], OPENRTMP_BENCH_JOIN[25]), fn($v) => benchMs($v)); ?>
          </div>
          <div class="bench-chart">
            <h3>1 viewer</h3>
            <p class="bench-note">Join latency. Lower is better.</p>
            <?php echo benchBars(array_map(fn($r) => $r[0], OPENRTMP_BENCH_JOIN[1]), fn($v) => benchMs($v)); ?>
          </div>
        </div>
        <table>
          <thead><tr><th>Server</th><th>Viewers</th><th>Join avg</th><th>Join p95</th><th>fps per viewer</th></tr></thead>
          <tbody>
            <?php foreach (OPENRTMP_BENCH_JOIN as $viewers => $rows): foreach ($rows as $key => [$avg, $p95, $fps]): ?>
            <tr<?php echo $key === 'openrtmp' ? ' class="is-openrtmp"' : ''; ?>><td><?php echo OPENRTMP_BENCH_SERVERS[$key]; ?></td><td><?php echo $viewers; ?></td><td><?php echo benchMs($avg); ?></td><td><?php echo benchMs($p95); ?></td><td><?php echo benchNum($fps, 1); ?></td></tr>
            <?php endforeach; endforeach; ?>
          </tbody>
        </table>
        <p>All five servers delivered the full stream (about 73 audio and video frames per second) to every viewer, up to 100 concurrent viewers and about 110 Mbps of relayed traffic.</p>

        <h2 id="head-to-head">Head to head with MediaMTX and LiveForge</h2>
        <p>The two closest competitors, measured in interleaved rounds against <code>librtmp2-server</code>. Average values; the frame rate at 100 viewers was the same for all three.</p>
        <table class="comparison-table">
          <thead><tr><th>Measurement</th><th>librtmp2-server</th><th>MediaMTX</th><th>LiveForge</th></tr></thead>
          <tbody>
            <?php foreach (OPENRTMP_BENCH_ROUNDS as $metric => $values): ?>
            <tr><td><?php echo $roundLabels[$metric]; ?></td><td><strong><?php echo $values['openrtmp']; ?></strong></td><td><?php echo $values['mediamtx']; ?></td><td><?php echo $values['liveforge']; ?></td></tr>
            <?php endforeach; ?>
          </tbody>
        </table>

        <h2 id="why">Why it is fast</h2>
        <ul>
          <li>Publishes and plays are authorized from an in-memory key snapshot as soon as the request arrives; the session row is written to SQLite right after.</li>
          <li>Each poll loop waits on a persistent <code>epoll</code> set and is woken through an <code>eventfd</code> the moment an authorization completes or a frame is relayed.</li>
          <li>Connections are spread over one <code>SO_REUSEPORT</code> shard per CPU core (up to four), and every socket has <code>TCP_NODELAY</code> set.</li>
          <li><a href="https://github.com/OpenRTMP/librtmp2" target="_blank" rel="noopener"><code>librtmp2</code></a> chunks each frame once per fan-out instead of once per viewer.</li>
        </ul>

        <h2 id="setup">Test setup</h2>
        <table class="comparison-table">
          <thead><tr><th>Server</th><th>Version</th><th>Language</th></tr></thead>
          <tbody>
            <?php foreach (OPENRTMP_BENCH_VERSIONS as $key => [$version, $language]): ?>
            <tr><td><?php echo OPENRTMP_BENCH_SERVERS[$key]; ?></td><td><?php echo htmlspecialchars($version, ENT_QUOTES, 'UTF-8'); ?></td><td><?php echo $language; ?></td></tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        <ul>
          <li>Intel Xeon @ 2.10 GHz, 4 vCPUs, 15 GiB RAM, Linux 6.18.</li>
          <li>Client: <code>bench_handshake</code> and <code>bench_relay</code> from <code>librtmp2</code>, the same binaries for every server.</li>
          <li>Source: ffmpeg <code>testsrc</code> 1280x720@30, libx264 veryfast/zerolatency at 2.5 Mbps, AAC at 128 kbps, 2 s GOP.</li>
          <li>Servers run one at a time; nginx-rtmp with <code>worker_processes 1</code>, since its live relay state is per worker process.</li>
        </ul>
        <p>Full results, server configs and the script that produces them: <a href="<?php echo OPENRTMP_BENCH_SOURCE_URL; ?>" target="_blank" rel="noopener"><code>librtmp2-server</code> BENCHMARKS.md</a>, <a href="<?php echo OPENRTMP_BENCH_SCRIPT_URL; ?>" target="_blank" rel="noopener"><code>run_rtmp_benchmarks.sh</code></a>, and the protocol microbenchmarks in <a href="<?php echo OPENRTMP_BENCH_LIB_SOURCE_URL; ?>" target="_blank" rel="noopener"><code>librtmp2</code> BENCHMARKS.md</a>.</p>

        <div class="cta compact-cta">
          <h2>Run it yourself</h2>
          <p>Start the Docker stack in a few minutes and point OBS or ffmpeg at it.</p>
          <div class="hero-actions" style="margin-bottom:0;">
            <a href="/quickstart/" class="btn btn-primary">Five-minute quickstart</a>
            <a href="/guides/openrtmp-vs-mediamtx-vs-srs/" class="btn btn-ghost">Feature comparison</a>
          </div>
        </div>
      </article>

      <aside class="toc-card" aria-label="On this page">
        <strong>On this page</strong>
        <a href="#handshake">Connect and publish</a>
        <a href="#join">Viewer join latency</a>
        <a href="#head-to-head">Head to head</a>
        <a href="#why">Why it is fast</a>
        <a href="#setup">Test setup</a>
      </aside>
    </div>
  </section>
</main>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
