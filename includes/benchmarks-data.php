<?php
// Benchmark numbers shown on the homepage teaser and on /benchmarks/ (EN + DE).
//
// Source of truth: BENCHMARKS.md in OpenRTMP/librtmp2-server. When that file
// is refreshed, update the values here so both pages stay in sync with it.

const OPENRTMP_BENCH_SOURCE_URL = 'https://github.com/OpenRTMP/librtmp2-server/blob/main/BENCHMARKS.md';
const OPENRTMP_BENCH_LIB_SOURCE_URL = 'https://github.com/OpenRTMP/librtmp2/blob/main/BENCHMARKS.md';
const OPENRTMP_BENCH_SCRIPT_URL = 'https://github.com/OpenRTMP/librtmp2-server/blob/main/scripts/run_rtmp_benchmarks.sh';
const OPENRTMP_BENCH_DATE = '2026-09-27';

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
  'openrtmp' => ['0.6.0', 'librtmp2 0.10.1', 'Rust'],
  'mediamtx' => ['v1.21.1', '', 'Go'],
  'liveforge' => ['main @ 4e70fb3', '', 'Go'],
  'srs' => ['v8.0.48', '', 'C++'],
  'nginx' => ['nginx 1.31.6', 'nginx-rtmp master @ 6c7719d', 'C'],
];

// Connect + publish handshake, 120 handshakes at concurrency 30.
// [handshakes/s, avg ms, p50 ms, p95 ms, p99 ms]
const OPENRTMP_BENCH_HANDSHAKE = [
  'openrtmp' => [10421.6, 2.15, 1.84, 4.97, 5.89],
  'mediamtx' => [7068.5, 3.60, 3.20, 7.79, 8.95],
  'liveforge' => [6322.5, 3.84, 3.69, 7.36, 8.85],
  'nginx' => [662.0, 44.08, 43.98, 47.11, 47.68],
  'srs' => [491.8, 57.09, 57.51, 64.82, 67.86],
];

// Join latency (connect -> first frame) per concurrent viewer count.
// viewers => server => [avg ms, p95 ms, fps per viewer]
const OPENRTMP_BENCH_JOIN = [
  1 => [
    'liveforge' => [1.09, 1.09, 73.1],
    'openrtmp' => [1.15, 1.15, 73.0],
    'mediamtx' => [1.20, 1.20, 73.0],
    'srs' => [44.37, 44.37, 71.9],
    'nginx' => [86.69, 86.69, 73.2],
  ],
  25 => [
    'openrtmp' => [1.81, 3.03, 73.0],
    'mediamtx' => [2.34, 4.07, 73.1],
    'liveforge' => [4.43, 7.81, 73.1],
    'srs' => [50.10, 52.49, 71.8],
    'nginx' => [87.95, 89.74, 73.1],
  ],
  100 => [
    'openrtmp' => [3.40, 7.07, 73.0],
    'mediamtx' => [7.25, 12.58, 73.1],
    'liveforge' => [15.33, 28.05, 73.1],
    'srs' => [69.27, 81.38, 72.5],
    'nginx' => [89.93, 93.83, 73.1],
  ],
];

// Connect + play handshake against a live stream, 120 at concurrency 30.
// [handshakes/s, avg ms, p50 ms, p95 ms, p99 ms]
const OPENRTMP_BENCH_PLAY_HANDSHAKE = [
  'openrtmp' => [10928.1, 2.03, 1.82, 4.28, 5.66],
  'mediamtx' => [7312.5, 2.96, 2.79, 5.28, 6.79],
  'liveforge' => [7324.3, 3.00, 2.17, 6.75, 8.89],
  'srs' => [557.8, 50.63, 50.45, 56.60, 57.29],
  'nginx' => [335.2, 88.89, 89.12, 91.63, 92.27],
];

// One stream watched by 500 or 1000 concurrent viewers.
// viewers => server => [join avg ms, join p95 ms, fps per viewer, server CPU % of one core, peak RSS MiB]
const OPENRTMP_BENCH_LOAD = [
  500 => [
    'mediamtx' => [27.66, 44.53, 73.1, 67.7, 97.6],
    'liveforge' => [28.61, 70.01, 73.1, 41.3, 91.3],
    'openrtmp' => [33.80, 71.69, 73.1, 34.3, 35.7],
    'nginx' => [100.79, 122.47, 73.1, 46.1, 14.1],
    'srs' => [229.83, 273.55, 72.7, 7.6, 106.2],
  ],
  1000 => [
    'openrtmp' => [42.75, 98.68, 73.1, 64.2, 59.3],
    'mediamtx' => [49.45, 96.67, 73.1, 137.0, 144.6],
    'liveforge' => [91.63, 202.32, 73.1, 78.7, 153.6],
    'nginx' => [111.24, 135.10, 73.1, 76.3, 20.1],
    'srs' => [484.65, 965.61, 73.1, 14.0, 152.8],
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
