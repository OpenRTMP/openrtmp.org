<?php
// Shared markup for /benchmarks/ and /de/benchmarks/. The including page sets
// $lang and $L (its strings) before including this file after the header.

$ms = fn(float $v, int $d = 2) => benchMs($v, $lang, $d);
$num = fn(float $v, int $d) => benchNum($v, $d, $lang);
$h = fn(string $s) => htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
$rowClass = fn(string $key) => $key === 'openrtmp' ? ' class="is-openrtmp"' : '';
$viewersLabel = fn(int $n) => $n === 1 ? $L['one_viewer'] : sprintf($L['n_viewers'], $n);
$benchRunId = $benchRunId ?? OPENRTMP_BENCH_LATEST;
$bench = $bench ?? benchRun($benchRunId);
$joinData = $bench['join'];
$loadData = $bench['load'];
$join100 = $joinData[100];
$hs = $bench['handshake'];
$ph = $bench['play_handshake'];
$rounds = $bench['rounds'];
$versions = $bench['versions'];
$maxLoadViewers = max(array_keys($loadData));
$noteKey = $lang === 'de' ? 'note_de' : 'note_en';
$loadNoteKey = $lang === 'de' ? 'load_note_de' : 'load_note_en';
$libBenchNoteKey = $lang === 'de' ? 'lib_bench_note_de' : 'lib_bench_note_en';
$libProtocol = $bench['lib_protocol'];
$libRelay = $bench['lib_relay'];
$mib = fn(float $v) => $num($v, 1) . ' MiB';
$pct = fn(float $v) => $num($v, 1) . ' %';
$maybeMs = fn($v) => $v === null ? '—' : $ms((float) $v);
$maybeNum = fn($v, int $d) => $v === null ? '—' : $num((float) $v, $d);
?>

<main class="bench-page">
  <section class="bench-hero">
    <div class="container">
      <span class="eyebrow"><?php echo $L['eyebrow']; ?></span>
      <h1><?php echo $L['h1']; ?> <span class="gradient"><?php echo $L['h1_accent']; ?></span></h1>
      <p><?php echo $L['lead']; ?></p>
      <div class="hero-actions">
        <a href="#join" class="btn btn-primary"><?php echo $L['cta_results']; ?></a>
        <a href="<?php echo lurl('/quickstart/'); ?>" class="btn btn-ghost"><?php echo $L['cta_try']; ?></a>
      </div>

      <div class="bench-kpis">
        <div class="bench-kpi">
          <span class="bench-kpi-label"><?php echo $L['kpi_join']; ?></span>
          <strong><?php echo $ms($join100['openrtmp'][0]); ?></strong>
          <span class="bench-vs"><?php echo sprintf($L['kpi_faster'], $num($join100['mediamtx'][0] / $join100['openrtmp'][0], 1), 'MediaMTX'); ?></span>
        </div>
        <div class="bench-kpi">
          <span class="bench-kpi-label"><?php echo $L['kpi_handshake']; ?></span>
          <strong><?php echo $ms($hs['openrtmp'][1]); ?></strong>
          <span class="bench-vs"><?php echo sprintf($L['kpi_faster'], $num($hs['nginx'][1] / $hs['openrtmp'][1], 0), 'nginx-rtmp'); ?></span>
        </div>
        <div class="bench-kpi">
          <span class="bench-kpi-label"><?php echo $L['kpi_rate']; ?></span>
          <strong><?php echo $num($hs['openrtmp'][0], 0); ?><small>/s</small></strong>
          <span class="bench-vs"><?php echo sprintf($L['kpi_more'], $num($hs['openrtmp'][0] / $hs['mediamtx'][0], 1), 'MediaMTX'); ?></span>
        </div>
        <div class="bench-kpi">
          <span class="bench-kpi-label"><?php echo $L['kpi_frames']; ?></span>
          <strong>100<small>&nbsp;%</small></strong>
          <span class="bench-vs"><?php echo sprintf($L['kpi_frames_note'], $maxLoadViewers); ?></span>
        </div>
      </div>
    </div>
  </section>

  <section class="bench-snapshot-section" aria-labelledby="bench-snapshot-title">
    <div class="container">
      <div class="section-head">
        <span class="eyebrow"><?php echo $L['snapshot_eyebrow']; ?></span>
        <h2 id="bench-snapshot-title"><?php echo $L['snapshot_h2']; ?></h2>
        <p><?php echo $L['snapshot_p']; ?></p>
      </div>
      <div class="bench-snapshots">
        <?php foreach (OPENRTMP_BENCH_RUNS as $runId => $run): ?>
        <a
          class="bench-snapshot<?php echo $runId === $benchRunId ? ' is-selected' : ''; ?>"
          href="?run=<?php echo rawurlencode($runId); ?>"
          <?php if ($runId === $benchRunId): ?>aria-current="page"<?php endif; ?>
        >
          <span class="bench-snapshot-version">librtmp2-server <?php echo $h($run['server_version']); ?></span>
          <span class="bench-snapshot-lib">librtmp2 <?php echo $h($run['lib_version']); ?></span>
          <span class="bench-snapshot-meta">
            <time datetime="<?php echo $h($run['date']); ?>"><?php echo $h($run['date']); ?></time>
            <?php if ($runId === OPENRTMP_BENCH_LATEST): ?><span><?php echo $L['snapshot_latest']; ?></span><?php endif; ?>
            <?php if (!$run['published_server_release']): ?><span><?php echo $L['snapshot_main']; ?></span><?php endif; ?>
          </span>
        </a>
        <?php endforeach; ?>
      </div>
      <p class="bench-snapshot-note"><?php echo $h($bench[$noteKey]); ?></p>
    </div>
  </section>

  <section id="join" class="bench-section">
    <div class="container">
      <div class="section-head">
        <span class="eyebrow"><?php echo $L['join_eyebrow']; ?></span>
        <h2><?php echo $L['join_h2']; ?></h2>
        <p><?php echo $L['join_p']; ?></p>
      </div>
      <div class="bench-panel" data-bench-tabs>
        <div class="bench-panel-head">
          <div>
            <h3><?php echo $L['join_panel']; ?></h3>
            <p><?php echo $L['lower_better']; ?></p>
          </div>
          <div class="bench-tabs" role="tablist" aria-label="<?php echo $L['viewers']; ?>">
            <?php foreach (array_reverse(array_keys($joinData)) as $i => $viewers): ?>
            <button type="button" role="tab" id="tab-join-<?php echo $viewers; ?>" aria-controls="join-<?php echo $viewers; ?>" aria-selected="<?php echo $i === 0 ? 'true' : 'false'; ?>"><?php echo $viewersLabel($viewers); ?></button>
            <?php endforeach; ?>
          </div>
        </div>
        <?php foreach (array_reverse($joinData, true) as $viewers => $rows): ?>
        <div class="bench-tabpanel" role="tabpanel" id="join-<?php echo $viewers; ?>" aria-labelledby="tab-join-<?php echo $viewers; ?>">
          <p class="bench-tabpanel-title"><?php echo $viewersLabel($viewers); ?></p>
          <?php echo benchBars(array_map(fn($r) => $r[0], $rows), fn($v) => $ms($v), $lang); ?>
        </div>
        <?php endforeach; ?>
        <p class="bench-foot"><?php echo $L['join_foot']; ?></p>
      </div>
    </div>
  </section>

  <section id="handshake" class="bench-section">
    <div class="container">
      <div class="section-head">
        <span class="eyebrow"><?php echo $L['hs_eyebrow']; ?></span>
        <h2><?php echo $L['hs_h2']; ?></h2>
        <p><?php echo $L['hs_p']; ?></p>
      </div>
      <div class="bench-duo">
        <div class="bench-panel">
          <div class="bench-panel-head"><div><h3><?php echo $L['hs_latency']; ?></h3><p><?php echo $L['lower_better']; ?></p></div></div>
          <?php echo benchBars(array_map(fn($r) => $r[1], $hs), fn($v) => $ms($v), $lang); ?>
        </div>
        <div class="bench-panel">
          <div class="bench-panel-head"><div><h3><?php echo $L['hs_rate']; ?></h3><p><?php echo $L['higher_better']; ?></p></div></div>
          <?php echo benchBars(array_map(fn($r) => $r[0], $hs), fn($v) => $num($v, 0) . '/s', $lang, true); ?>
        </div>
      </div>
    </div>
  </section>

  <section id="play" class="bench-section">
    <div class="container">
      <div class="section-head">
        <span class="eyebrow"><?php echo $L['play_eyebrow']; ?></span>
        <h2><?php echo $L['play_h2']; ?></h2>
        <p><?php echo $L['play_p']; ?></p>
      </div>
      <div class="bench-duo">
        <div class="bench-panel">
          <div class="bench-panel-head"><div><h3><?php echo $L['play_latency']; ?></h3><p><?php echo $L['lower_better']; ?></p></div></div>
          <?php echo benchBars(array_map(fn($r) => $r[1], $ph), fn($v) => $ms($v), $lang); ?>
        </div>
        <div class="bench-panel">
          <div class="bench-panel-head"><div><h3><?php echo $L['play_rate']; ?></h3><p><?php echo $L['higher_better']; ?></p></div></div>
          <?php echo benchBars(array_map(fn($r) => $r[0], $ph), fn($v) => $num($v, 0) . '/s', $lang, true); ?>
        </div>
      </div>
    </div>
  </section>

  <section id="library" class="bench-section">
    <div class="container">
      <div class="section-head">
        <span class="eyebrow"><?php echo $L['lib_eyebrow']; ?></span>
        <h2><?php echo sprintf($L['lib_h2'], $h($bench['lib_version'])); ?></h2>
        <p><?php echo $h($bench[$libBenchNoteKey]); ?></p>
      </div>
      <div class="bench-duo">
        <div class="bench-table-wrap">
          <table class="bench-table">
            <caption><?php echo $L['lib_protocol']; ?></caption>
            <thead><tr><th>Benchmark</th><th><?php echo $L['lib_time']; ?></th><th><?php echo $L['lib_throughput']; ?></th></tr></thead>
            <tbody>
              <?php foreach ($libProtocol as [$name, $time, $throughput]): ?>
              <tr><td><code><?php echo $h($name); ?></code></td><td><?php echo $h($time); ?></td><td><?php echo $h($throughput); ?></td></tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <div class="bench-table-wrap">
          <table class="bench-table">
            <caption><?php echo $L['lib_relay']; ?></caption>
            <thead><tr><th>Benchmark</th><th><?php echo $L['lib_time']; ?></th><th><?php echo $L['lib_throughput']; ?></th></tr></thead>
            <tbody>
              <?php foreach ($libRelay as [$name, $time, $throughput]): ?>
              <tr><td><code><?php echo $h($name); ?></code></td><td><?php echo $h($time); ?></td><td><?php echo $h($throughput); ?></td></tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
      <p class="bench-foot"><?php echo $L['lib_environment']; ?>: <?php echo $h($bench['lib_bench_environment']); ?></p>
    </div>
  </section>

  <section id="load" class="bench-section">
    <div class="container">
      <div class="section-head">
        <span class="eyebrow"><?php echo $L['load_eyebrow']; ?></span>
        <h2><?php echo $L['load_h2']; ?></h2>
        <p><?php echo $h($bench[$loadNoteKey]); ?></p>
      </div>
      <div class="bench-panel" data-bench-tabs>
        <div class="bench-panel-head">
          <div>
            <h3><?php echo $L['load_panel']; ?></h3>
            <p><?php echo $L['lower_better']; ?></p>
          </div>
          <div class="bench-tabs" role="tablist" aria-label="<?php echo $L['viewers']; ?>">
            <?php foreach (array_reverse(array_keys($loadData)) as $i => $viewers): ?>
            <button type="button" role="tab" id="tab-load-<?php echo $viewers; ?>" aria-controls="load-<?php echo $viewers; ?>" aria-selected="<?php echo $i === 0 ? 'true' : 'false'; ?>"><?php echo $viewersLabel($viewers); ?></button>
            <?php endforeach; ?>
          </div>
        </div>
        <?php foreach (array_reverse($loadData, true) as $viewers => $rows): ?>
        <div class="bench-tabpanel" role="tabpanel" id="load-<?php echo $viewers; ?>" aria-labelledby="tab-load-<?php echo $viewers; ?>">
          <p class="bench-tabpanel-title"><?php echo $viewersLabel($viewers); ?></p>
          <h4 class="bench-subhead"><?php echo $L['load_join']; ?></h4>
          <?php echo benchBars(array_map(fn($r) => $r[0], $rows), fn($v) => $ms($v), $lang); ?>
          <div class="bench-duo bench-load-duo">
            <div>
              <h4 class="bench-subhead"><?php echo $L['load_cpu']; ?></h4>
              <?php echo benchBars(array_map(fn($r) => $r[3], $rows), $pct, $lang, false, $L['leanest']); ?>
            </div>
            <div>
              <h4 class="bench-subhead"><?php echo $L['load_rss']; ?></h4>
              <?php echo benchBars(array_map(fn($r) => $r[4], $rows), $mib, $lang, false, $L['leanest']); ?>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
        <p class="bench-foot"><?php echo $L['load_foot']; ?></p>
      </div>
    </div>
  </section>

  <?php if ($rounds !== []): ?>
  <section id="head-to-head" class="bench-section">
    <div class="container">
      <div class="section-head">
        <span class="eyebrow"><?php echo $L['duel_eyebrow']; ?></span>
        <h2><?php echo $L['duel_h2']; ?></h2>
        <p><?php echo $L['duel_p']; ?></p>
      </div>
      <div class="bench-duels">
        <?php foreach ($rounds as $metric => [$decimals, $values]): ?>
        <article class="bench-duel">
          <h3><?php echo $L['rounds'][$metric]; ?></h3>
          <div class="bench-duel-ours">
            <strong><?php echo $ms($values['openrtmp'], $decimals); ?></strong>
            <span>librtmp2-server</span>
          </div>
          <ul>
            <?php foreach ($values as $key => $value): if ($key === 'openrtmp') { continue; } ?>
            <li><span><?php echo OPENRTMP_BENCH_SERVERS[$key]; ?></span><span><?php echo $ms($value, $decimals); ?></span><em><?php echo $num($value / $values['openrtmp'], 1); ?>&times;</em></li>
            <?php endforeach; ?>
          </ul>
        </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <section id="results" class="bench-section">
    <div class="container">
      <div class="section-head">
        <span class="eyebrow"><?php echo $L['table_eyebrow']; ?></span>
        <h2><?php echo $L['table_h2']; ?></h2>
        <p><?php echo $L['table_p']; ?></p>
      </div>
      <div class="bench-table-wrap">
        <table class="bench-table">
          <caption><?php echo $L['hs_h2']; ?></caption>
          <thead><tr><th>Server</th><th><?php echo $L['col_rate']; ?></th><th>avg</th><th>p50</th><th>p95</th><th>p99</th></tr></thead>
          <tbody>
            <?php foreach ($hs as $key => [$rate, $avg, $p50, $p95, $p99]): ?>
            <tr<?php echo $rowClass($key); ?>><td><?php echo OPENRTMP_BENCH_SERVERS[$key]; ?></td><td><?php echo $num($rate, 1); ?></td><td><?php echo $ms($avg); ?></td><td><?php echo $maybeMs($p50); ?></td><td><?php echo $ms($p95); ?></td><td><?php echo $ms($p99); ?></td></tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <div class="bench-table-wrap">
        <table class="bench-table">
          <caption><?php echo $L['play_h2']; ?></caption>
          <thead><tr><th>Server</th><th><?php echo $L['col_rate']; ?></th><th>avg</th><th>p50</th><th>p95</th><th>p99</th></tr></thead>
          <tbody>
            <?php foreach ($ph as $key => [$rate, $avg, $p50, $p95, $p99]): ?>
            <tr<?php echo $rowClass($key); ?>><td><?php echo OPENRTMP_BENCH_SERVERS[$key]; ?></td><td><?php echo $num($rate, 1); ?></td><td><?php echo $ms($avg); ?></td><td><?php echo $maybeMs($p50); ?></td><td><?php echo $ms($p95); ?></td><td><?php echo $ms($p99); ?></td></tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <div class="bench-table-wrap">
        <table class="bench-table">
          <caption><?php echo $L['join_h2']; ?></caption>
          <thead><tr><th>Server</th><th><?php echo $L['viewers']; ?></th><th><?php echo $L['col_join_avg']; ?></th><th><?php echo $L['col_join_p95']; ?></th><th><?php echo $L['col_fps']; ?></th></tr></thead>
          <?php foreach ($joinData as $viewers => $rows): ?>
          <tbody>
            <?php foreach ($rows as $key => [$avg, $p95, $fps]): ?>
            <tr<?php echo $rowClass($key); ?>><td><?php echo OPENRTMP_BENCH_SERVERS[$key]; ?></td><td><?php echo $viewers; ?></td><td><?php echo $ms($avg); ?></td><td><?php echo $maybeMs($p95); ?></td><td><?php echo $maybeNum($fps, 1); ?></td></tr>
            <?php endforeach; ?>
          </tbody>
          <?php endforeach; ?>
        </table>
      </div>
      <div class="bench-table-wrap">
        <table class="bench-table">
          <caption><?php echo $L['load_h2']; ?></caption>
          <thead><tr><th>Server</th><th><?php echo $L['viewers']; ?></th><th><?php echo $L['col_join_avg']; ?></th><th><?php echo $L['col_join_p95']; ?></th><th><?php echo $L['col_fps']; ?></th><th><?php echo $L['col_cpu']; ?></th><th><?php echo $L['col_rss']; ?></th></tr></thead>
          <?php foreach ($loadData as $viewers => $rows): ?>
          <tbody>
            <?php foreach ($rows as $key => [$avg, $p95, $fps, $cpu, $rss]): ?>
            <tr<?php echo $rowClass($key); ?>><td><?php echo OPENRTMP_BENCH_SERVERS[$key]; ?></td><td><?php echo $viewers; ?></td><td><?php echo $ms($avg); ?></td><td><?php echo $maybeMs($p95); ?></td><td><?php echo $maybeNum($fps, 1); ?></td><td><?php echo $pct($cpu); ?></td><td><?php echo $mib($rss); ?></td></tr>
            <?php endforeach; ?>
          </tbody>
          <?php endforeach; ?>
        </table>
      </div>
    </div>
  </section>

  <section id="why" class="bench-section">
    <div class="container">
      <div class="section-head">
        <span class="eyebrow"><?php echo $L['why_eyebrow']; ?></span>
        <h2><?php echo $L['why_h2']; ?></h2>
      </div>
      <div class="grid bench-why">
        <?php foreach ($L['why'] as [$icon, $title, $text]): ?>
        <div class="card">
          <div class="icon"><?php echo $icon; ?></div>
          <h3><?php echo $title; ?></h3>
          <p><?php echo $text; ?></p>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section id="setup" class="bench-section">
    <div class="container">
      <div class="section-head">
        <span class="eyebrow"><?php echo $L['setup_eyebrow']; ?></span>
        <h2><?php echo $L['setup_h2']; ?></h2>
      </div>
      <div class="bench-duo">
        <dl class="bench-specs">
          <?php foreach ($L['specs'] as [$term, $value]): ?>
          <div><dt><?php echo $term; ?></dt><dd><?php echo $value; ?></dd></div>
          <?php endforeach; ?>
        </dl>
        <div class="bench-table-wrap">
          <table class="bench-table bench-versions">
            <thead><tr><th>Server</th><th>Version</th></tr></thead>
            <tbody>
              <?php foreach ($versions as $key => $versionRow): ?>
              <?php
                [$version, $detail, $language] = $versionRow;
                $versionUrl = $versionRow[3] ?? null;
                $detailUrl = $versionRow[4] ?? null;
              ?>
              <tr<?php echo $rowClass($key); ?>>
                <td><?php echo OPENRTMP_BENCH_SERVERS[$key]; ?><small><?php echo $h($language); ?></small></td>
                <td>
                  <?php if ($versionUrl !== null): ?><a href="<?php echo $h($versionUrl); ?>" target="_blank" rel="noopener"><?php endif; ?>
                  <?php echo $h($version); ?>
                  <?php if ($versionUrl !== null): ?></a><?php endif; ?>
                  <?php if ($detail !== ''): ?>
                  <small>
                    <?php if ($detailUrl !== null): ?><a href="<?php echo $h($detailUrl); ?>" target="_blank" rel="noopener"><?php endif; ?>
                    <?php echo $h($detail); ?>
                    <?php if ($detailUrl !== null): ?></a><?php endif; ?>
                  </small>
                  <?php endif; ?>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
      <div class="bench-sources">
        <a href="<?php echo $h($bench['source_url']); ?>" target="_blank" rel="noopener" class="btn btn-ghost"><?php echo $L['src_server']; ?></a>
        <a href="<?php echo OPENRTMP_BENCH_SCRIPT_URL; ?>" target="_blank" rel="noopener" class="btn btn-ghost"><?php echo $L['src_script']; ?></a>
        <a href="<?php echo $h($bench['lib_bench_source_url']); ?>" target="_blank" rel="noopener" class="btn btn-ghost"><?php echo $L['src_lib']; ?></a>
      </div>
    </div>
  </section>

  <section>
    <div class="container">
      <div class="cta">
        <h2><?php echo $L['cta_h2']; ?></h2>
        <p><?php echo $L['cta_p']; ?></p>
        <div class="hero-actions" style="margin-bottom:0;">
          <a href="<?php echo lurl('/quickstart/'); ?>" class="btn btn-primary"><?php echo $L['cta_quick']; ?></a>
          <a href="<?php echo lurl('/guides/openrtmp-vs-mediamtx-vs-srs/'); ?>" class="btn btn-ghost"><?php echo $L['cta_compare']; ?></a>
        </div>
      </div>
    </div>
  </section>
</main>
