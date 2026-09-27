<?php
$lang = 'de';
$page = 'benchmarks';
$pageTitle = 'RTMP-Server-Benchmarks — OpenRTMP gegen nginx-rtmp, MediaMTX, SRS und LiveForge';
$pageDescription = 'librtmp2-server im Benchmark gegen nginx-rtmp, MediaMTX, SRS 8.0 und LiveForge: Handshake-Latenz, Handshakes pro Sekunde und Join-Latenz bei 1, 25 und 100 gleichzeitigen Zuschauern.';
$canonicalPath = '/de/benchmarks/';
$ogType = 'article';
$structuredData = [
  '@context' => 'https://schema.org',
  '@type' => 'TechArticle',
  'headline' => 'RTMP-Server-Benchmarks: OpenRTMP gegen nginx-rtmp, MediaMTX, SRS und LiveForge',
  'inLanguage' => 'de',
  'description' => $pageDescription,
  'author' => ['@type' => 'Organization', 'name' => 'OpenRTMP'],
  'publisher' => ['@type' => 'Organization', 'name' => 'OpenRTMP'],
  'mainEntityOfPage' => 'https://openrtmp.org/de/benchmarks/'
];
include_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/benchmarks-data.php';

$roundLabels = [
  'seq_connect' => 'Connect + Publish, sequenziell',
  'single_join' => 'Join eines einzelnen Zuschauers',
  'join_100' => 'Join mit 100 Zuschauern',
  'connect_30' => 'Connect + Publish, 30 gleichzeitig',
];
?>

<main>
  <div class="page-hero container article-hero">
    <span class="eyebrow">Benchmarks &middot; RTMP-Relay &middot; <?php echo OPENRTMP_BENCH_DATE; ?></span>
    <h1>RTMP-Server-Benchmarks</h1>
    <p><code>librtmp2-server</code> gegen nginx-rtmp, MediaMTX, SRS 8.0 und LiveForge: dieselbe Maschine, derselbe RTMP-Client, derselbe echte H.264/AAC-Stream. OpenRTMP hat in jedem Test die niedrigste durchschnittliche Latenz.</p>
  </div>

  <section class="content-section" style="padding-top: 0;">
    <div class="container article-layout">
      <article class="prose">
        <div class="bench-highlights">
          <div class="bench-highlight">
            <strong><?php echo benchMs(OPENRTMP_BENCH_JOIN[100]['openrtmp'][0], 'de'); ?></strong>
            <span>Join mit 100 Zuschauern</span>
            <p>MediaMTX: <?php echo benchMs(OPENRTMP_BENCH_JOIN[100]['mediamtx'][0], 'de'); ?></p>
          </div>
          <div class="bench-highlight">
            <strong><?php echo benchMs(OPENRTMP_BENCH_HANDSHAKE['openrtmp'][1], 'de'); ?></strong>
            <span>Connect + Publish</span>
            <p>nginx-rtmp: <?php echo benchMs(OPENRTMP_BENCH_HANDSHAKE['nginx'][1], 'de'); ?></p>
          </div>
          <div class="bench-highlight">
            <strong><?php echo benchNum(OPENRTMP_BENCH_HANDSHAKE['openrtmp'][0], 0, 'de'); ?>/s</strong>
            <span>Handshakes pro Sekunde</span>
            <p>MediaMTX: <?php echo benchNum(OPENRTMP_BENCH_HANDSHAKE['mediamtx'][0], 0, 'de'); ?>/s</p>
          </div>
        </div>

        <h2 id="handshake">Connect und Publish</h2>
        <p>120 RTMP-Connect- und Publish-Handshakes (bis <code>NetStream.Publish.Start</code>) mit 30 gleichzeitigen Verbindungen. Jeder Server hat jeden Handshake abgeschlossen. <code>librtmp2-server</code> ist hier der einzige, der jeden Publish gegen einen Stream-Key pro Stream prüft; die anderen akzeptieren jeden Stream-Namen.</p>
        <div class="bench-charts">
          <div class="bench-chart">
            <h3>Durchschnittliche Handshake-Latenz</h3>
            <p class="bench-note">Weniger ist besser.</p>
            <?php echo benchBars(array_map(fn($r) => $r[1], OPENRTMP_BENCH_HANDSHAKE), fn($v) => benchMs($v, 'de')); ?>
          </div>
          <div class="bench-chart">
            <h3>Handshakes pro Sekunde</h3>
            <p class="bench-note">Mehr ist besser.</p>
            <?php echo benchBars(array_map(fn($r) => $r[0], OPENRTMP_BENCH_HANDSHAKE), fn($v) => benchNum($v, 0, 'de') . '/s'); ?>
          </div>
        </div>
        <table>
          <thead><tr><th>Server</th><th>Handshakes/s</th><th>avg</th><th>p50</th><th>p95</th><th>p99</th></tr></thead>
          <tbody>
            <?php foreach (OPENRTMP_BENCH_HANDSHAKE as $key => [$rate, $avg, $p50, $p95, $p99]): ?>
            <tr<?php echo $key === 'openrtmp' ? ' class="is-openrtmp"' : ''; ?>><td><?php echo OPENRTMP_BENCH_SERVERS[$key]; ?></td><td><?php echo benchNum($rate, 1, 'de'); ?></td><td><?php echo benchMs($avg, 'de'); ?></td><td><?php echo benchMs($p50, 'de'); ?></td><td><?php echo benchMs($p95, 'de'); ?></td><td><?php echo benchMs($p99, 'de'); ?></td></tr>
            <?php endforeach; ?>
          </tbody>
        </table>

        <h2 id="join">Join-Latenz der Zuschauer</h2>
        <p>Ein ffmpeg-Publisher sendet einen H.264-Stream mit 1280x720@30 und 2,5 Mbit/s plus AAC-Audio mit 128 kbit/s. Dann verbinden sich 1, 25 oder 100 Zuschauer gleichzeitig und spielen ihn ab. Die Join-Latenz ist die Zeit vom Verbindungsaufbau bis zum ersten empfangenen Frame.</p>
        <div class="bench-chart">
          <h3>100 gleichzeitige Zuschauer</h3>
          <p class="bench-note">Durchschnittliche Join-Latenz. Weniger ist besser.</p>
          <?php echo benchBars(array_map(fn($r) => $r[0], OPENRTMP_BENCH_JOIN[100]), fn($v) => benchMs($v, 'de')); ?>
        </div>
        <div class="bench-charts">
          <div class="bench-chart">
            <h3>25 gleichzeitige Zuschauer</h3>
            <p class="bench-note">Durchschnittliche Join-Latenz. Weniger ist besser.</p>
            <?php echo benchBars(array_map(fn($r) => $r[0], OPENRTMP_BENCH_JOIN[25]), fn($v) => benchMs($v, 'de')); ?>
          </div>
          <div class="bench-chart">
            <h3>1 Zuschauer</h3>
            <p class="bench-note">Join-Latenz. Weniger ist besser.</p>
            <?php echo benchBars(array_map(fn($r) => $r[0], OPENRTMP_BENCH_JOIN[1]), fn($v) => benchMs($v, 'de')); ?>
          </div>
        </div>
        <table>
          <thead><tr><th>Server</th><th>Zuschauer</th><th>Join avg</th><th>Join p95</th><th>fps pro Zuschauer</th></tr></thead>
          <tbody>
            <?php foreach (OPENRTMP_BENCH_JOIN as $viewers => $rows): foreach ($rows as $key => [$avg, $p95, $fps]): ?>
            <tr<?php echo $key === 'openrtmp' ? ' class="is-openrtmp"' : ''; ?>><td><?php echo OPENRTMP_BENCH_SERVERS[$key]; ?></td><td><?php echo $viewers; ?></td><td><?php echo benchMs($avg, 'de'); ?></td><td><?php echo benchMs($p95, 'de'); ?></td><td><?php echo benchNum($fps, 1, 'de'); ?></td></tr>
            <?php endforeach; endforeach; ?>
          </tbody>
        </table>
        <p>Alle fünf Server haben jedem Zuschauer den vollständigen Stream geliefert (rund 73 Audio- und Video-Frames pro Sekunde), bis zu 100 gleichzeitigen Zuschauern und rund 110 Mbit/s weitergeleitetem Traffic.</p>

        <h2 id="head-to-head">Direkter Vergleich mit MediaMTX und LiveForge</h2>
        <p>Die beiden stärksten Konkurrenten, in abwechselnden Runden gegen <code>librtmp2-server</code> gemessen. Durchschnittswerte; die Framerate bei 100 Zuschauern war bei allen dreien gleich.</p>
        <table class="comparison-table">
          <thead><tr><th>Messung</th><th>librtmp2-server</th><th>MediaMTX</th><th>LiveForge</th></tr></thead>
          <tbody>
            <?php foreach (OPENRTMP_BENCH_ROUNDS as $metric => $values): ?>
            <tr><td><?php echo $roundLabels[$metric]; ?></td><td><strong><?php echo str_replace('.', ',', $values['openrtmp']); ?></strong></td><td><?php echo str_replace('.', ',', $values['mediamtx']); ?></td><td><?php echo str_replace('.', ',', $values['liveforge']); ?></td></tr>
            <?php endforeach; ?>
          </tbody>
        </table>

        <h2 id="why">Warum er so schnell ist</h2>
        <ul>
          <li>Publish und Play werden direkt beim Eintreffen der Anfrage aus einem In-Memory-Snapshot der Keys autorisiert; die Session-Zeile wird unmittelbar danach in SQLite geschrieben.</li>
          <li>Jede Poll-Schleife wartet auf einem persistenten <code>epoll</code>-Set und wird über ein <code>eventfd</code> geweckt, sobald eine Autorisierung fertig ist oder ein Frame weitergeleitet wird.</li>
          <li>Verbindungen werden auf einen <code>SO_REUSEPORT</code>-Shard pro CPU-Kern verteilt (bis zu vier), und jeder Socket hat <code>TCP_NODELAY</code> gesetzt.</li>
          <li><a href="https://github.com/OpenRTMP/librtmp2" target="_blank" rel="noopener"><code>librtmp2</code></a> zerlegt jeden Frame einmal pro Fan-out in Chunks statt einmal pro Zuschauer.</li>
        </ul>

        <h2 id="setup">Testaufbau</h2>
        <table class="comparison-table">
          <thead><tr><th>Server</th><th>Version</th><th>Sprache</th></tr></thead>
          <tbody>
            <?php foreach (OPENRTMP_BENCH_VERSIONS as $key => [$version, $language]): ?>
            <tr><td><?php echo OPENRTMP_BENCH_SERVERS[$key]; ?></td><td><?php echo htmlspecialchars($version, ENT_QUOTES, 'UTF-8'); ?></td><td><?php echo $language; ?></td></tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        <ul>
          <li>Intel Xeon @ 2,10 GHz, 4 vCPUs, 15 GiB RAM, Linux 6.18.</li>
          <li>Client: <code>bench_handshake</code> und <code>bench_relay</code> aus <code>librtmp2</code>, dieselben Binaries für jeden Server.</li>
          <li>Quelle: ffmpeg <code>testsrc</code> 1280x720@30, libx264 veryfast/zerolatency mit 2,5 Mbit/s, AAC mit 128 kbit/s, 2 s GOP.</li>
          <li>Die Server laufen nacheinander; nginx-rtmp mit <code>worker_processes 1</code>, da sein Live-Relay-Zustand pro Worker-Prozess gilt.</li>
        </ul>
        <p>Vollständige Ergebnisse, Server-Konfigurationen und das Skript, das sie erzeugt: <a href="<?php echo OPENRTMP_BENCH_SOURCE_URL; ?>" target="_blank" rel="noopener"><code>librtmp2-server</code> BENCHMARKS.md</a>, <a href="<?php echo OPENRTMP_BENCH_SCRIPT_URL; ?>" target="_blank" rel="noopener"><code>run_rtmp_benchmarks.sh</code></a> sowie die Protokoll-Microbenchmarks in <a href="<?php echo OPENRTMP_BENCH_LIB_SOURCE_URL; ?>" target="_blank" rel="noopener"><code>librtmp2</code> BENCHMARKS.md</a>.</p>

        <div class="cta compact-cta">
          <h2>Selbst ausprobieren</h2>
          <p>Den Docker-Stack in wenigen Minuten starten und OBS oder ffmpeg darauf richten.</p>
          <div class="hero-actions" style="margin-bottom:0;">
            <a href="/de/quickstart/" class="btn btn-primary">Schnellstart in fünf Minuten</a>
            <a href="/de/guides/openrtmp-vs-mediamtx-vs-srs/" class="btn btn-ghost">Funktionsvergleich</a>
          </div>
        </div>
      </article>

      <aside class="toc-card" aria-label="Auf dieser Seite">
        <strong>Auf dieser Seite</strong>
        <a href="#handshake">Connect und Publish</a>
        <a href="#join">Join-Latenz</a>
        <a href="#head-to-head">Direkter Vergleich</a>
        <a href="#why">Warum so schnell</a>
        <a href="#setup">Testaufbau</a>
      </aside>
    </div>
  </section>
</main>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>
