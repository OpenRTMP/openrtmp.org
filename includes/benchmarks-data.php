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
  'openrtmp' => [8804.7, 2.45, 2.15, 5.38, 6.15],
  'liveforge' => [6719.1, 3.59, 3.42, 6.78, 7.82],
  'mediamtx' => [6470.3, 3.69, 3.32, 7.06, 9.19],
  'nginx' => [647.8, 44.27, 44.08, 46.22, 47.87],
  'srs' => [504.0, 54.64, 54.98, 62.85, 63.87],
];

// Join latency (connect -> first frame) per concurrent viewer count.
// viewers => server => [avg ms, p95 ms, fps per viewer]
const OPENRTMP_BENCH_JOIN = [
  1 => [
    'openrtmp' => [0.82, 0.82, 73.0],
    'liveforge' => [1.09, 1.09, 73.0],
    'mediamtx' => [1.37, 1.37, 73.0],
    'srs' => [45.67, 45.67, 71.9],
    'nginx' => [86.49, 86.49, 73.2],
  ],
  25 => [
    'openrtmp' => [1.76, 3.03, 73.0],
    'mediamtx' => [3.02, 4.71, 73.1],
    'liveforge' => [3.99, 6.67, 73.0],
    'srs' => [50.77, 53.99, 72.0],
    'nginx' => [88.72, 90.43, 73.1],
  ],
  100 => [
    'openrtmp' => [3.42, 6.65, 73.1],
    'mediamtx' => [6.22, 10.58, 73.1],
    'liveforge' => [7.41, 17.14, 73.1],
    'srs' => [71.64, 85.43, 72.4],
    'nginx' => [89.75, 93.59, 73.1],
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
