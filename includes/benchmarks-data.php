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

const OPENRTMP_BENCH_VERSIONS = [
  'openrtmp' => ['0.5.0 (librtmp2 0.10.0)', 'Rust'],
  'mediamtx' => ['v1.11.3', 'Go'],
  'liveforge' => ['main @ 4e70fb3', 'Go'],
  'srs' => ['v8.0.48', 'C++'],
  'nginx' => ['nginx 1.24.0 + nginx-rtmp 1.2.2', 'C'],
];

// Connect + publish handshake, 120 handshakes at concurrency 30.
// [handshakes/s, avg ms, p50 ms, p95 ms, p99 ms]
const OPENRTMP_BENCH_HANDSHAKE = [
  'openrtmp' => [10446.9, 2.19, 1.89, 4.93, 6.55],
  'liveforge' => [7195.8, 3.13, 2.76, 6.60, 8.56],
  'mediamtx' => [5765.9, 4.14, 3.87, 7.75, 8.98],
  'nginx' => [652.7, 44.55, 44.00, 47.52, 49.50],
  'srs' => [514.4, 54.78, 55.13, 62.57, 63.49],
];

// Join latency (connect -> first frame) per concurrent viewer count.
// viewers => server => [avg ms, p95 ms, fps per viewer]
const OPENRTMP_BENCH_JOIN = [
  1 => [
    'openrtmp' => [1.06, 1.06, 73.1],
    'liveforge' => [1.15, 1.15, 73.0],
    'mediamtx' => [1.61, 1.61, 72.8],
    'srs' => [43.75, 43.75, 72.1],
    'nginx' => [86.93, 86.93, 73.2],
  ],
  25 => [
    'openrtmp' => [2.02, 3.22, 73.1],
    'mediamtx' => [2.14, 3.21, 73.1],
    'liveforge' => [4.36, 7.41, 73.1],
    'srs' => [52.18, 54.56, 72.1],
    'nginx' => [90.15, 91.74, 73.2],
  ],
  100 => [
    'openrtmp' => [3.54, 7.76, 73.1],
    'mediamtx' => [10.78, 17.93, 73.1],
    'liveforge' => [13.13, 25.43, 73.1],
    'srs' => [73.32, 86.84, 71.9],
    'nginx' => [91.22, 95.40, 73.1],
  ],
];

// Head-to-head rounds against the two closest competitors, average ms.
// metric key => [decimals, [server => value]]
const OPENRTMP_BENCH_ROUNDS = [
  'join_100' => [1, ['openrtmp' => 3.6, 'mediamtx' => 8.1, 'liveforge' => 15.5]],
  'single_join' => [2, ['openrtmp' => 0.78, 'liveforge' => 1.06, 'mediamtx' => 1.13]],
  'seq_connect' => [2, ['openrtmp' => 0.30, 'liveforge' => 0.38, 'mediamtx' => 0.45]],
  'connect_30' => [1, ['openrtmp' => 2.0, 'mediamtx' => 3.0, 'liveforge' => 3.4]],
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
 * and every other server shows how far it is behind librtmp2-server.
 */
function benchBars(array $rows, callable $format, string $lang = 'en', bool $higherIsBetter = false): string
{
  if ($higherIsBetter) {
    arsort($rows);
  } else {
    asort($rows);
  }
  $max = max($rows);
  $ours = $rows['openrtmp'];
  $best = $lang === 'de' ? 'Schnellster' : 'Fastest';
  $html = '<ol class="bench-bars">';
  $rank = 0;
  foreach ($rows as $key => $value) {
    $rank++;
    $width = $max > 0 ? max(2, round($value / $max * 100, 1)) : 0;
    $isOurs = $key === 'openrtmp';
    if ($isOurs) {
      $delta = '<span class="bench-badge">' . $best . '</span>';
    } else {
      $factor = $higherIsBetter ? $ours / $value : $value / $ours;
      $delta = '<span class="bench-delta">' . benchNum($factor, 1, $lang) . '&times;</span>';
    }
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
