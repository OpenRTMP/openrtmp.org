<?php
// Benchmark numbers shown on the homepage teaser and on /benchmarks/ (EN + DE).
//
// Source of truth: BENCHMARKS.md in OpenRTMP/librtmp2-server. Keep benchmark
// snapshots immutable once published so visitors can switch between measured
// librtmp2-server/librtmp2 version pairs without mixing values from different
// runs. The one exception is the "next release preview" (see
// benchmarks-ci.php): the latest automated CI run on main, fetched at request
// time and replaced after every merge.

const OPENRTMP_BENCH_SOURCE_URL = 'https://github.com/OpenRTMP/librtmp2-server/blob/main/BENCHMARKS.md';
const OPENRTMP_BENCH_LIB_SOURCE_URL = 'https://github.com/OpenRTMP/librtmp2/blob/main/BENCHMARKS.md';
const OPENRTMP_BENCH_SCRIPT_URL = 'https://github.com/OpenRTMP/librtmp2-server/blob/main/scripts/run_rtmp_benchmarks.sh';
const OPENRTMP_BENCH_LATEST = '0.6.2-0.11.0';

// Server key => display name. Version-specific details live in each snapshot.
const OPENRTMP_BENCH_SERVERS = [
  'openrtmp' => 'librtmp2-server',
  'mediamtx' => 'MediaMTX',
  'liveforge' => 'LiveForge',
  'srs' => 'SRS',
  'nginx' => 'nginx-rtmp',
];

// Immutable benchmark snapshots, newest first.
//
// handshake/play_handshake row:
//   [handshakes/s, avg ms, p50 ms|null, p95 ms, p99 ms]
// join row:
//   [avg ms, p95 ms|null, fps/viewer|null]
// load row:
//   [join avg ms, join p95 ms, fps/viewer, CPU % of one core, peak RSS MiB]
const OPENRTMP_BENCH_RUNS = [
  '0.6.2-0.11.0' => [
    'date' => '2026-10-02',
    'server_version' => '0.6.2',
    'lib_version' => '0.11.0',
    'published_server_release' => true,
    'source_url' => 'https://github.com/OpenRTMP/librtmp2-server/blob/v0.6.2/BENCHMARKS.md',
    'server_ref_url' => 'https://github.com/OpenRTMP/librtmp2-server/releases/tag/v0.6.2',
    'lib_ref_url' => 'https://github.com/OpenRTMP/librtmp2/releases/tag/v0.11.0',
    'lib_bench_source_url' => 'https://github.com/OpenRTMP/librtmp2/blob/v0.11.0/BENCHMARKS.md',
    'lib_bench_environment' => 'Intel Xeon @ 2.10 GHz, 4 vCPUs, Linux 6.18 x86_64, rustc 1.97.0',
    'lib_bench_note_en' => 'Criterion microbenchmarks for librtmp2 0.11.0. The protocol paths are unchanged from 0.10.2; the cross-version differences were measured on a shared VM with a newer rustc and are not a controlled release A/B.',
    'lib_bench_note_de' => 'Criterion-Microbenchmarks für librtmp2 0.11.0. Die Protokollpfade sind gegenüber 0.10.2 unverändert; die Unterschiede zwischen den Versionen wurden auf einer Shared-VM mit neuerem rustc gemessen und sind kein kontrollierter Release-A/B-Test.',
    'lib_protocol' => [
      ['chunk/write_read_roundtrip', '4.55 µs', '~858 MiB/s'],
      ['amf0_build_connect', '274 ns', '—'],
      ['flv/video_tag_h264', '1.51 ns', '—'],
      ['flv/audio_tag_aac', '1.24 ns', '—'],
      ['fourcc_to_video_codec_avc1', '2.19 ns', '—'],
      ['server_read_c1', '1.21 µs', '—'],
    ],
    'lib_relay' => [
      ['relay/publish_to_player/100', '99.1 ms', '~1010 elem/s'],
      ['relay/publish_to_player/500', '101.4 ms', '~4930 elem/s'],
    ],
    'note_en' => 'Release snapshot: librtmp2-server 0.6.2 on librtmp2 0.11.0. The cross-server table is one sweep, and competitor builds differ from the 28 Sep snapshot. Compare servers within this snapshot; do not treat differences between snapshots as an A/B test.',
    'note_de' => 'Release-Snapshot: librtmp2-server 0.6.2 auf librtmp2 0.11.0. Die Cross-Server-Tabelle stammt aus einem einzelnen Sweep, und die Konkurrenz-Builds unterscheiden sich vom Stand vom 28. September. Server innerhalb dieses Snapshots vergleichen; Unterschiede zwischen Snapshots sind kein A/B-Test.',
    'load_note_en' => 'At 500 and 1000 viewers every server delivered the full source rate. At 2000 viewers nginx-rtmp and MediaMTX fell below it, so that step is a stress indicator rather than a clean capacity comparison.',
    'load_note_de' => 'Bei 500 und 1000 Zuschauern lieferten alle Server die volle Quellrate. Bei 2000 Zuschauern fielen nginx-rtmp und MediaMTX darunter; dieser Schritt ist daher eher ein Stresstest als ein sauberer Kapazitätsvergleich.',
    'versions' => [
      'openrtmp' => [
        '0.6.2',
        'librtmp2 0.11.0',
        'Rust',
        'https://github.com/OpenRTMP/librtmp2-server/releases/tag/v0.6.2',
        'https://github.com/OpenRTMP/librtmp2/releases/tag/v0.11.0',
      ],
      'mediamtx' => ['v1.21.1', 'Go module build', 'Go'],
      'liveforge' => ['main @ 4e70fb3', '', 'Go'],
      'srs' => ['7.0.89', 'gitee mirror @ 846bc13', 'C++'],
      'nginx' => ['nginx 1.24.0', 'libnginx-mod-rtmp 1.2.2', 'C'],
    ],
    'handshake' => [
      'openrtmp' => [5850.0, 3.87, null, 9.54, 10.71],
      'mediamtx' => [4916.0, 4.68, null, 7.54, 9.71],
      'liveforge' => [4662.0, 5.29, null, 10.57, 13.31],
      'nginx' => [642.0, 45.07, null, 48.59, 49.34],
      'srs' => [512.0, 54.73, null, 63.55, 65.48],
    ],
    'join' => [
      1 => [
        'openrtmp' => [0.88, null, null],
        'liveforge' => [1.27, null, null],
        'mediamtx' => [1.35, null, null],
        'srs' => [43.9, null, null],
        'nginx' => [87.7, null, null],
      ],
      25 => [
        'openrtmp' => [2.05, 3.84, null],
        'mediamtx' => [3.18, 5.14, null],
        'liveforge' => [4.09, 6.86, null],
        'srs' => [55.9, 71.6, null],
        'nginx' => [86.8, 87.8, null],
      ],
      100 => [
        'openrtmp' => [3.97, 9.12, null],
        'mediamtx' => [5.68, 10.98, null],
        'liveforge' => [11.50, 23.05, null],
        'srs' => [64.8, 78.5, null],
        'nginx' => [93.0, 100.6, null],
      ],
    ],
    'play_handshake' => [
      'openrtmp' => [5764.0, 3.67, null, 8.45, 10.99],
      'liveforge' => [4970.0, 4.42, null, 9.25, 11.28],
      'mediamtx' => [4521.0, 5.50, null, 8.24, 10.23],
      'srs' => [540.0, 52.44, null, 58.11, 59.50],
      'nginx' => [335.0, 88.40, null, 91.64, 91.91],
    ],
    'load' => [
      500 => [
        'openrtmp' => [32.5, 69.7, 73.2, 31.5, 21.8],
        'mediamtx' => [45.6, 87.7, 73.1, 75.6, 100.7],
        'liveforge' => [55.6, 99.8, 73.1, 48.5, 90.1],
        'nginx' => [96.9, 108.5, 73.1, 47.8, 13.9],
        'srs' => [246.0, 296.0, 73.4, 7.6, 97.7],
      ],
      1000 => [
        'openrtmp' => [20.2, 60.4, 73.1, 57.3, 31.4],
        'mediamtx' => [81.8, 160.6, 73.1, 155.5, 144.5],
        'nginx' => [99.5, 114.0, 73.1, 83.3, 19.8],
        'liveforge' => [186.0, 331.0, 73.1, 95.0, 155.1],
        'srs' => [463.0, 603.0, 73.3, 14.7, 146.2],
      ],
      2000 => [
        'openrtmp' => [109.7, 216.5, 73.1, 85.1, 50.6],
        'nginx' => [115.6, 211.4, 43.1, 70.4, 31.5],
        'mediamtx' => [272.9, 877.4, 39.0, 158.4, 268.3],
        'liveforge' => [883.0, 2034.0, 73.3, 131.2, 285.7],
        'srs' => [1076.0, 1642.0, 73.2, 30.6, 133.5],
      ],
    ],
    'rounds' => [],
  ],
  '0.6.1-0.10.2' => [
    'date' => '2026-09-28',
    'server_version' => '0.6.1',
    'lib_version' => '0.10.2',
    'published_server_release' => true,
    'source_url' => 'https://github.com/OpenRTMP/librtmp2-server/blob/v0.6.1/BENCHMARKS.md',
    'server_ref_url' => 'https://github.com/OpenRTMP/librtmp2-server/releases/tag/v0.6.1',
    'lib_ref_url' => 'https://github.com/OpenRTMP/librtmp2/releases/tag/v0.10.2',
    'lib_bench_source_url' => 'https://github.com/OpenRTMP/librtmp2/blob/v0.10.2/BENCHMARKS.md',
    'lib_bench_environment' => 'Intel Xeon @ 2.10 GHz, 4 vCPUs, Linux 6.18 x86_64, rustc 1.95.0',
    'lib_bench_note_en' => 'Criterion microbenchmarks for librtmp2 0.10.2. Absolute values are illustrative measurements from the shared benchmark VM.',
    'lib_bench_note_de' => 'Criterion-Microbenchmarks für librtmp2 0.10.2. Die absoluten Werte sind illustrative Messungen von der gemeinsam genutzten Benchmark-VM.',
    'lib_protocol' => [
      ['chunk/write_read_roundtrip', '3.76 µs', '~1040 MiB/s'],
      ['amf0_build_connect', '229 ns', '—'],
      ['flv/video_tag_h264', '1.10 ns', '—'],
      ['flv/audio_tag_aac', '1.15 ns', '—'],
      ['fourcc_to_video_codec_avc1', '2.21 ns', '—'],
      ['server_read_c1', '1.08 µs', '—'],
    ],
    'lib_relay' => [
      ['relay/publish_to_player/100', '99.0 ms', '~1010 elem/s'],
      ['relay/publish_to_player/500', '101.0 ms', '~4950 elem/s'],
    ],
    'note_en' => 'Published release snapshot: librtmp2-server 0.6.1 on librtmp2 0.10.2. These values come from the three-sweep benchmark published with the release.',
    'note_de' => 'Veröffentlichter Release-Snapshot: librtmp2-server 0.6.1 auf librtmp2 0.10.2. Diese Werte stammen aus dem mit dem Release veröffentlichten Drei-Sweep-Benchmark.',
    'load_note_en' => 'Every server delivered the full source stream to every viewer at 500 and 1000 concurrent viewers.',
    'load_note_de' => 'Jeder Server lieferte den vollständigen Quellstream an alle 500 beziehungsweise 1000 gleichzeitigen Zuschauer.',
    'versions' => [
      'openrtmp' => [
        '0.6.1',
        'librtmp2 0.10.2',
        'Rust',
        'https://github.com/OpenRTMP/librtmp2-server/releases/tag/v0.6.1',
        'https://github.com/OpenRTMP/librtmp2/releases/tag/v0.10.2',
      ],
      'mediamtx' => ['v1.21.1', '', 'Go'],
      'liveforge' => ['main @ 4e70fb3', '', 'Go'],
      'srs' => ['v8.0.48', '', 'C++'],
      'nginx' => ['nginx 1.31.6', 'nginx-rtmp master @ 6c7719d', 'C'],
    ],
    'handshake' => [
      'openrtmp' => [9486.0, 2.41, 2.15, 5.53, 6.56],
      'liveforge' => [7042.5, 3.32, 3.07, 6.57, 7.98],
      'mediamtx' => [6894.7, 3.51, 3.05, 6.66, 7.39],
      'nginx' => [633.7, 44.30, 44.04, 46.53, 48.51],
      'srs' => [512.7, 54.42, 54.73, 63.06, 65.20],
    ],
    'join' => [
      1 => [
        'openrtmp' => [0.91, 0.91, 73.1],
        'liveforge' => [1.01, 1.01, 73.0],
        'mediamtx' => [1.35, 1.35, 73.1],
        'srs' => [46.89, 46.89, 71.9],
        'nginx' => [85.82, 85.82, 73.2],
      ],
      25 => [
        'openrtmp' => [1.50, 2.34, 73.1],
        'mediamtx' => [2.09, 3.40, 73.0],
        'liveforge' => [4.50, 7.72, 73.1],
        'srs' => [49.88, 53.20, 71.9],
        'nginx' => [87.87, 91.46, 73.1],
      ],
      100 => [
        'openrtmp' => [2.94, 6.49, 73.1],
        'mediamtx' => [5.51, 10.86, 73.1],
        'liveforge' => [13.00, 26.22, 73.1],
        'srs' => [73.22, 85.16, 72.4],
        'nginx' => [89.83, 96.00, 73.1],
      ],
    ],
    'play_handshake' => [
      'openrtmp' => [10073.2, 1.95, 1.58, 4.32, 5.82],
      'liveforge' => [7176.8, 3.00, 2.62, 6.67, 8.52],
      'mediamtx' => [5415.4, 4.61, 4.11, 8.76, 9.69],
      'srs' => [556.0, 51.20, 50.60, 58.23, 60.55],
      'nginx' => [331.4, 89.47, 89.79, 92.61, 93.19],
    ],
    'load' => [
      500 => [
        'mediamtx' => [31.47, 62.60, 73.1, 67.6, 98.7],
        'openrtmp' => [32.69, 64.82, 73.1, 31.1, 22.1],
        'liveforge' => [37.03, 84.94, 73.1, 40.2, 91.9],
        'nginx' => [97.29, 110.56, 73.1, 46.3, 14.0],
        'srs' => [204.45, 238.71, 73.2, 7.4, 106.2],
      ],
      1000 => [
        'openrtmp' => [32.62, 93.75, 73.1, 60.3, 31.8],
        'mediamtx' => [58.75, 107.45, 73.1, 135.4, 144.3],
        'liveforge' => [109.89, 240.27, 73.1, 74.7, 147.8],
        'nginx' => [120.61, 152.38, 73.1, 77.6, 20.0],
        'srs' => [519.38, 1182.86, 72.5, 13.4, 149.4],
      ],
    ],
    'rounds' => [
      'join_100' => [1, ['openrtmp' => 4.4, 'mediamtx' => 5.5, 'liveforge' => 16.7]],
      'single_join' => [2, ['openrtmp' => 0.94, 'liveforge' => 1.09, 'mediamtx' => 1.17]],
      'seq_connect' => [2, ['openrtmp' => 0.32, 'liveforge' => 0.39, 'mediamtx' => 0.46]],
      'connect_30' => [1, ['openrtmp' => 2.2, 'mediamtx' => 2.6, 'liveforge' => 3.0]],
    ],
  ],
];

$openrtmpLatestBench = OPENRTMP_BENCH_RUNS[OPENRTMP_BENCH_LATEST];
define('OPENRTMP_BENCH_DATE', $openrtmpLatestBench['date']);
define('OPENRTMP_BENCH_VERSIONS', $openrtmpLatestBench['versions']);
define('OPENRTMP_BENCH_HANDSHAKE', $openrtmpLatestBench['handshake']);
define('OPENRTMP_BENCH_JOIN', $openrtmpLatestBench['join']);
define('OPENRTMP_BENCH_PLAY_HANDSHAKE', $openrtmpLatestBench['play_handshake']);
define('OPENRTMP_BENCH_LOAD', $openrtmpLatestBench['load']);
define('OPENRTMP_BENCH_ROUNDS', $openrtmpLatestBench['rounds']);
unset($openrtmpLatestBench);

require_once __DIR__ . '/benchmarks-ci.php';

/**
 * All selectable snapshots: the immutable release snapshots (newest first)
 * followed, when available, by the latest automated run on main as a
 * "next release preview" under the key OPENRTMP_BENCH_CI_ID.
 */
function benchRuns(): array
{
  static $all = null;
  if ($all === null) {
    $all = OPENRTMP_BENCH_RUNS;
    $ci = benchCiRun();
    if ($ci !== null) {
      $all[OPENRTMP_BENCH_CI_ID] = $ci;
    }
  }
  return $all;
}

/** Resolve a requested benchmark snapshot, falling back to the newest release. */
function benchRunId(?string $requested = null): string
{
  if ($requested === OPENRTMP_BENCH_CI_ID) {
    return array_key_exists(OPENRTMP_BENCH_CI_ID, benchRuns()) ? $requested : OPENRTMP_BENCH_LATEST;
  }
  if ($requested !== null && array_key_exists($requested, OPENRTMP_BENCH_RUNS)) {
    return $requested;
  }
  return OPENRTMP_BENCH_LATEST;
}

/** Return one benchmark snapshot. */
function benchRun(?string $requested = null): array
{
  return benchRuns()[benchRunId($requested)];
}

/** Format a millisecond value with the page language's decimal separator. */
function benchMs(float $value, string $lang = 'en', int $decimals = 2): string
{
  $text = number_format($value, $decimals, $lang === 'de' ? ',' : '.', $lang === 'de' ? '.' : ',');
  return $text . ' ms';
}

/** Format a plain number with the page language's separators. */
function benchNum(float $value, int $decimals, string $lang = 'en'): string
{
  return number_format($value, $decimals, $lang === 'de' ? ',' : '.', $lang === 'de' ? '.' : ',');
}

/**
 * Render a ranked horizontal bar chart. $rows is server key => numeric value.
 * Rows are sorted best first, bars are scaled linearly to the largest value,
 * the leader gets a badge ($bestLabel, "Fastest" by default) and every other
 * server shows how far it is behind the leader.
 */
function benchBars(array $rows, callable $format, string $lang = 'en', bool $higherIsBetter = false, ?string $bestLabel = null): string
{
  if ($higherIsBetter) {
    arsort($rows);
  } else {
    asort($rows);
  }
  $max = max($rows);
  $lead = reset($rows);
  $best = $bestLabel ?? ($lang === 'de' ? 'Schnellster' : 'Fastest');
  $html = '<ol class="bench-bars">';
  $rank = 0;
  foreach ($rows as $key => $value) {
    $rank++;
    $width = $max > 0 ? max(2, round($value / $max * 100, 1)) : 0;
    if ($rank === 1) {
      $delta = '<span class="bench-badge">' . $best . '</span>';
    } else {
      // A zero value cannot be put in proportion; show no factor instead of dividing by it.
      $factor = $value > 0 && $lead > 0 ? ($higherIsBetter ? $lead / $value : $value / $lead) : null;
      $delta = $factor === null ? '' : '<span class="bench-delta">' . benchNum($factor, 1, $lang) . '&times;</span>';
    }
    $isOurs = $key === 'openrtmp';
    $html .= '<li class="bench-row' . ($isOurs ? ' is-openrtmp' : '') . '">'
      . '<span class="bench-rank">' . $rank . '</span>'
      . '<span class="bench-name">' . htmlspecialchars(OPENRTMP_BENCH_SERVERS[$key], ENT_QUOTES, 'UTF-8') . '</span>'
      . '<span class="bench-track"><span class="bench-fill" style="width: ' . $width . '%"></span></span>'
      . '<span class="bench-value">' . htmlspecialchars($format($value), ENT_QUOTES, 'UTF-8') . '</span>'
      . $delta
      . '</li>';
  }
  return $html . '</ol>';
}
