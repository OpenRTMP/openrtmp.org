<?php
// Benchmark numbers shown on the homepage teaser and on /benchmarks/ (EN + DE).
//
// Source of truth: BENCHMARKS.md in OpenRTMP/librtmp2-server. When that file
// is refreshed, update the values here so both pages stay in sync with it.

const OPENRTMP_BENCH_SOURCE_URL = 'https://github.com/OpenRTMP/librtmp2-server/blob/main/BENCHMARKS.md';
const OPENRTMP_BENCH_LIB_SOURCE_URL = 'https://github.com/OpenRTMP/librtmp2/blob/main/BENCHMARKS.md';
const OPENRTMP_BENCH_SCRIPT_URL = 'https://github.com/OpenRTMP/librtmp2-server/blob/main/scripts/run_rtmp_benchmarks.sh';
const OPENRTMP_BENCH_DATE = '2026-09-28';

// Server key => display name. librtmp2-server is highlighted in every chart.
const OPENRTMP_BENCH_SERVERS = [
  'openrtmp' => 'librtmp2-server',
  'mediamtx' => 'MediaMTX',
  'liveforge' => 'LiveForge',
  'srs' => 'SRS 8.0',
  'nginx' => 'nginx-rtmp',
];

// Server key => [version, what it was built with or against, language].
const OPENRTMP_BENCH_VERSIONS = [
  'openrtmp' => ['0.6.1', 'librtmp2 0.10.2', 'Rust'],
  'mediamtx' => ['v1.21.1', '', 'Go'],
  'liveforge' => ['main @ 4e70fb3', '', 'Go'],
  'srs' => ['v8.0.48', '', 'C++'],
  'nginx' => ['nginx 1.31.6', 'nginx-rtmp master @ 6c7719d', 'C'],
];

// Connect + publish handshake, 120 handshakes at concurrency 30.
// [handshakes/s, avg ms, p50 ms, p95 ms, p99 ms]
const OPENRTMP_BENCH_HANDSHAKE = [
  'openrtmp' => [9486.0, 2.41, 2.15, 5.53, 6.56],
  'liveforge' => [7042.5, 3.32, 3.07, 6.57, 7.98],
  'mediamtx' => [6894.7, 3.51, 3.05, 6.66, 7.39],
  'nginx' => [633.7, 44.30, 44.04, 46.53, 48.51],
  'srs' => [512.7, 54.42, 54.73, 63.06, 65.20],
];

// Join latency (connect -> first frame) per concurrent viewer count.
// viewers => server => [avg ms, p95 ms, fps per viewer]
const OPENRTMP_BENCH_JOIN = [
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
];

// Connect + play handshake against a live stream, 120 at concurrency 30.
// [handshakes/s, avg ms, p50 ms, p95 ms, p99 ms]
const OPENRTMP_BENCH_PLAY_HANDSHAKE = [
  'openrtmp' => [10073.2, 1.95, 1.58, 4.32, 5.82],
  'liveforge' => [7176.8, 3.00, 2.62, 6.67, 8.52],
  'mediamtx' => [5415.4, 4.61, 4.11, 8.76, 9.69],
  'srs' => [556.0, 51.20, 50.60, 58.23, 60.55],
  'nginx' => [331.4, 89.47, 89.79, 92.61, 93.19],
];

// One stream watched by 500 or 1000 concurrent viewers.
// viewers => server => [join avg ms, join p95 ms, fps per viewer, server CPU % of one core, peak RSS MiB]
const OPENRTMP_BENCH_LOAD = [
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
];

// Head-to-head rounds against the two closest competitors, average ms.
// metric key => [decimals, [server => value]]
const OPENRTMP_BENCH_ROUNDS = [
  'join_100' => [1, ['openrtmp' => 4.4, 'mediamtx' => 5.5, 'liveforge' => 16.7]],
  'single_join' => [2, ['openrtmp' => 0.94, 'liveforge' => 1.09, 'mediamtx' => 1.17]],
  'seq_connect' => [2, ['openrtmp' => 0.32, 'liveforge' => 0.39, 'mediamtx' => 0.46]],
  'connect_30' => [1, ['openrtmp' => 2.2, 'mediamtx' => 2.6, 'liveforge' => 3.0]],
];

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
      $factor = $higherIsBetter ? $lead / $value : $value / $lead;
      $delta = '<span class="bench-delta">' . benchNum($factor, 1, $lang) . '&times;</span>';
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
