<?php
// The newest automated benchmark run, offered on /benchmarks/ (EN + DE) next to
// the immutable release snapshots as "next release preview".
//
// Both OpenRTMP/librtmp2-server and OpenRTMP/librtmp2 run their benchmarks in
// GitHub Actions after every merge to main and store the result as
// `latest.json` on the `bench-data` branch (see docs/ci-benchmarks.md in those
// repositories). This file reads those two files, validates them and turns
// them into a snapshot with the same shape as the entries of
// OPENRTMP_BENCH_RUNS, so the page renders it like any other snapshot.
//
// The fetch is cached on disk and fails soft: if GitHub cannot be reached, a
// stale copy (up to a week old) is used, and without any copy the preview card
// is simply not shown. The data comes from our own repositories but is still
// validated and escaped like untrusted input.

const OPENRTMP_BENCH_CI_ID = 'ci';
const OPENRTMP_BENCH_CI_TTL = 900;         // seconds a cached copy counts as fresh
const OPENRTMP_BENCH_CI_STALE_TTL = 604800; // seconds a stale copy may still be used
const OPENRTMP_BENCH_CI_FAIL_TTL = 300;     // back off this long after a failed fetch
const OPENRTMP_BENCH_CI_MAX_BYTES = 2097152;

/**
 * Base URL of the raw `bench-data` files. A constant, never request or
 * environment input, so the outgoing request target cannot be influenced by a
 * visitor. Tests define OPENRTMP_BENCH_DATA_BASE (e.g. a file:// directory)
 * from an auto_prepend_file before this file is included.
 */
function benchCiBase(): string
{
  return rtrim(defined('OPENRTMP_BENCH_DATA_BASE') ? OPENRTMP_BENCH_DATA_BASE : 'https://raw.githubusercontent.com/OpenRTMP', '/');
}

/** GET a small document; null on any failure. */
function benchCiHttpGet(string $url): ?string
{
  $body = false;
  if (str_starts_with($url, 'file://')) {
    $body = @file_get_contents($url, false, null, 0, OPENRTMP_BENCH_CI_MAX_BYTES + 1);
  } elseif (function_exists('curl_init')) {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_CONNECTTIMEOUT => 3,
      CURLOPT_TIMEOUT => 5,
      CURLOPT_FOLLOWLOCATION => false,
      CURLOPT_FAILONERROR => true,
      CURLOPT_USERAGENT => 'openrtmp.org benchmark preview',
    ]);
    $body = curl_exec($ch);
    curl_close($ch);
  } else {
    $context = stream_context_create(['http' => ['timeout' => 5, 'ignore_errors' => false]]);
    $body = @file_get_contents($url, false, $context, 0, OPENRTMP_BENCH_CI_MAX_BYTES + 1);
  }
  if (!is_string($body) || $body === '' || strlen($body) > OPENRTMP_BENCH_CI_MAX_BYTES) {
    return null;
  }
  return $body;
}

function benchCiDecode(?string $body): ?array
{
  if ($body === null) {
    return null;
  }
  $data = json_decode($body, true, 32);
  return is_array($data) ? $data : null;
}

/** Fetch one JSON document with an on-disk cache; null if unavailable. */
function benchCiFetchJson(string $url): ?array
{
  $cache = sys_get_temp_dir() . '/openrtmp-bench-' . md5($url) . '.json';
  $failed = $cache . '.failed';
  $age = is_file($cache) ? time() - (int) @filemtime($cache) : PHP_INT_MAX;
  if ($age < OPENRTMP_BENCH_CI_TTL) {
    return benchCiDecode(@file_get_contents($cache) ?: null);
  }
  $recentlyFailed = is_file($failed) && time() - (int) @filemtime($failed) < OPENRTMP_BENCH_CI_FAIL_TTL;
  if (!$recentlyFailed) {
    $body = benchCiHttpGet($url);
    $data = benchCiDecode($body);
    if ($data !== null) {
      $tmp = $cache . '.' . getmypid() . '.tmp';
      if (@file_put_contents($tmp, $body) !== false) {
        @rename($tmp, $cache);
      }
      @unlink($failed);
      return $data;
    }
    @touch($failed);
  }
  if ($age < OPENRTMP_BENCH_CI_STALE_TTL) {
    return benchCiDecode(@file_get_contents($cache) ?: null);
  }
  return null;
}

/** @param mixed $row */
function benchCiRow($row, int $width): ?array
{
  if (!is_array($row) || count($row) !== $width) {
    return null;
  }
  $out = [];
  foreach ($row as $v) {
    if (!is_int($v) && !is_float($v)) {
      return null;
    }
    $out[] = (float) $v;
  }
  return $out;
}

/**
 * Normalise {server: row} for the known servers, in display order. Unknown
 * servers and malformed rows are dropped.
 */
function benchCiRows($rows, int $width): array
{
  $out = [];
  if (!is_array($rows)) {
    return $out;
  }
  foreach (array_keys(OPENRTMP_BENCH_SERVERS) as $key) {
    if (isset($rows[$key]) && ($row = benchCiRow($rows[$key], $width)) !== null) {
      $out[$key] = $row;
    }
  }
  return $out;
}

/** Same as benchCiRows for {viewers: {server: row}}, viewer counts as int keys. */
function benchCiTiers($tiers, int $width): array
{
  $out = [];
  if (!is_array($tiers)) {
    return $out;
  }
  foreach ($tiers as $viewers => $rows) {
    if (!is_numeric($viewers) || (int) $viewers < 1) {
      continue;
    }
    $clean = benchCiRows($rows, $width);
    if ($clean !== []) {
      $out[(int) $viewers] = $clean;
    }
  }
  ksort($out);
  return $out;
}

function benchCiString($value, int $max = 200): string
{
  $s = is_string($value) ? $value : '';
  $s = preg_replace('/[\x00-\x1f\x7f]/u', ' ', $s) ?? '';
  return mb_substr($s, 0, $max);
}

function benchCiSha($value): string
{
  return is_string($value) && preg_match('/^[0-9a-f]{7,40}$/', $value) ? substr($value, 0, 7) : '';
}

/** @return array{cpu:string,vcpus:int,ram:string,kernel:string,runner:string,rustc:string}|null */
function benchCiEnv($env): ?array
{
  if (!is_array($env) || !is_numeric($env['vcpus'] ?? null) || !is_numeric($env['ram_gib'] ?? null)) {
    return null;
  }
  return [
    'cpu' => benchCiString($env['cpu'] ?? ''),
    'vcpus' => (int) $env['vcpus'],
    'ram' => rtrim(rtrim(number_format((float) $env['ram_gib'], 1, '.', ''), '0'), '.'),
    'kernel' => benchCiString($env['kernel'] ?? ''),
    'runner' => benchCiString($env['runner'] ?? ''),
    'rustc' => benchCiString($env['rustc'] ?? ''),
  ];
}

function benchCiTime(float $ns): string
{
  if ($ns < 1e3) {
    return ($ns < 10 ? number_format($ns, 2) : number_format($ns, 0)) . ' ns';
  }
  if ($ns < 1e6) {
    return number_format($ns / 1e3, 2) . ' µs';
  }
  if ($ns < 1e9) {
    return number_format($ns / 1e6, 1) . ' ms';
  }
  return number_format($ns / 1e9, 2) . ' s';
}

function benchCiThroughput(array $b): string
{
  $ns = (float) ($b['ns'] ?? 0);
  if ($ns <= 0) {
    return '—';
  }
  if (isset($b['bytes']) && is_numeric($b['bytes']) && $b['bytes'] > 0) {
    return '~' . number_format($b['bytes'] / ($ns * 1e-9) / 1048576, 0, '.', '') . ' MiB/s';
  }
  if (isset($b['elements']) && is_numeric($b['elements']) && $b['elements'] > 0) {
    return '~' . number_format($b['elements'] / ($ns * 1e-9), 0, '.', '') . ' elem/s';
  }
  return '—';
}

/** @return list<array{string,string,string}> */
function benchCiLibRows($suite): array
{
  $rows = [];
  if (!is_array($suite)) {
    return $rows;
  }
  ksort($suite);
  foreach ($suite as $name => $b) {
    if (is_array($b) && isset($b['ns']) && is_numeric($b['ns'])) {
      $rows[] = [benchCiString((string) $name, 120), benchCiTime((float) $b['ns']), benchCiThroughput($b)];
    }
  }
  return $rows;
}

/**
 * Build the preview snapshot from the two results files, or null if the
 * librtmp2-server file does not carry a usable sweep.
 */
function benchCiSnapshot(array $srv, ?array $lib): ?array
{
  if (($srv['schema'] ?? null) !== 1 || !is_array($srv['sweep'] ?? null)) {
    return null;
  }
  $sweep = $srv['sweep'];
  $handshake = benchCiRows($sweep['handshake'] ?? null, 5);
  $playHandshake = benchCiRows($sweep['play_handshake'] ?? null, 5);
  $join = benchCiTiers($sweep['join'] ?? null, 3);
  $load = benchCiTiers($sweep['load'] ?? null, 5);
  $env = benchCiEnv($srv['environment'] ?? null);
  if (!isset($handshake['openrtmp'], $playHandshake['openrtmp'], $join[100]['openrtmp']) || $load === [] || $env === null) {
    return null;
  }
  foreach ($load as $rows) {
    if (!isset($rows['openrtmp'])) {
      return null;
    }
  }
  if (!preg_match('/^\d{4}-\d{2}-\d{2}/', (string) ($srv['date'] ?? ''), $m)) {
    return null;
  }
  $date = $m[0];
  $sha = benchCiSha($srv['commit'] ?? '');
  $serverVersion = benchCiString($srv['version'] ?? '', 40);
  $depLib = benchCiString($srv['deps']['librtmp2'] ?? '', 40);

  // Server versions: only servers that actually have rows in this run.
  $present = array_keys($handshake + $playHandshake);
  $versions = [];
  foreach (array_keys(OPENRTMP_BENCH_SERVERS) as $key) {
    if (!in_array($key, $present, true)) {
      continue;
    }
    $v = $sweep['versions'][$key] ?? [];
    $row = [
      benchCiString($v['version'] ?? '—', 80),
      benchCiString($v['detail'] ?? '', 120),
      benchCiString($v['language'] ?? '', 20),
    ];
    if ($key === 'openrtmp') {
      $row[0] = 'main @ ' . ($sha !== '' ? $sha : '?') . ($serverVersion !== '' ? ' (' . $serverVersion . ')' : '');
      if ($sha !== '') {
        $row[] = 'https://github.com/OpenRTMP/librtmp2-server/commit/' . benchCiSha($srv['commit']);
      }
    }
    $versions[$key] = $row;
  }

  // Library microbenchmarks come from librtmp2's own latest run, if available.
  $libOk = $lib !== null && ($lib['schema'] ?? null) === 1 && is_array($lib['suites'] ?? null);
  $libEnv = $libOk ? benchCiEnv($lib['environment'] ?? null) : null;
  $libSha = $libOk ? benchCiSha($lib['commit'] ?? '') : '';
  $libVersion = $libOk ? benchCiString($lib['version'] ?? '', 40) : '';
  $libLabel = $libOk && $libVersion !== ''
    ? $libVersion . ($libSha !== '' ? ' (main @ ' . $libSha . ')' : '')
    : ($depLib !== '' ? $depLib : '—');

  $runner = $env['runner'] !== '' ? $env['runner'] : 'CI runner';
  $machine = sprintf('%s, %d vCPUs, %s GiB RAM', $env['cpu'], $env['vcpus'], $env['ram']);
  $noteEn = sprintf(
    'Next-release preview: the latest automated run on main (librtmp2-server %s, %s), measured on %s: %s. It shows what the next release may look like, but it is one sweep on a shared CI machine and is not comparable with the release snapshots, which were measured on a different, dedicated machine. Compare the servers with each other inside this run; every value is replaced after the next merge.',
    $sha !== '' ? $sha : 'main', $date, $runner, $machine
  );
  $noteDe = sprintf(
    'Vorschau auf das nächste Release: der neueste automatische Lauf auf main (librtmp2-server %s, %s), gemessen auf %s: %s. Er zeigt, was das nächste Release erwarten lässt, ist aber ein einzelner Sweep auf einer geteilten CI-Maschine und nicht mit den Release-Snapshots vergleichbar, die auf einer anderen, dedizierten Maschine gemessen wurden. Server innerhalb dieses Laufs vergleichen; jeder Wert wird nach dem nächsten Merge ersetzt.',
    $sha !== '' ? $sha : 'main', $date, $runner, $machine
  );
  $loadNoteEn = 'Servers that delivered fewer frames per viewer than the source rate were overloaded at that step, so those rows are a stress indicator rather than a clean comparison.';
  $loadNoteDe = 'Server, die weniger Bilder pro Zuschauer lieferten als die Quellrate, waren in diesem Schritt überlastet; diese Zeilen sind daher eher ein Stresstest als ein sauberer Vergleich.';

  $libEnvLine = $libEnv !== null
    ? sprintf('%s, %d vCPUs, %s GiB RAM, %s, rustc %s, %s', $libEnv['cpu'], $libEnv['vcpus'], $libEnv['ram'], $libEnv['kernel'], $libEnv['rustc'], $libEnv['runner'])
    : '—';

  return [
    'kind' => 'ci',
    'date' => $date,
    'server_version' => 'main @ ' . ($sha !== '' ? $sha : '?'),
    'lib_version' => $libLabel,
    'published_server_release' => false,
    'source_url' => 'https://github.com/OpenRTMP/librtmp2-server/blob/main/BENCHMARKS.md',
    'server_ref_url' => $sha !== '' ? 'https://github.com/OpenRTMP/librtmp2-server/commit/' . $sha : null,
    'lib_ref_url' => $libSha !== '' ? 'https://github.com/OpenRTMP/librtmp2/commit/' . $libSha : null,
    'lib_bench_source_url' => 'https://github.com/OpenRTMP/librtmp2/blob/main/BENCHMARKS.md',
    'lib_bench_environment' => $libEnvLine,
    'lib_bench_note_en' => 'Criterion microbenchmarks from the latest automated librtmp2 run on main. They ran on a shared CI machine, so read them as indicative.',
    'lib_bench_note_de' => 'Criterion-Microbenchmarks aus dem neuesten automatischen librtmp2-Lauf auf main. Sie liefen auf einer geteilten CI-Maschine und sind daher nur als Richtwert zu lesen.',
    'lib_protocol' => $libOk ? benchCiLibRows($lib['suites']['protocol'] ?? null) : [],
    'lib_relay' => $libOk ? benchCiLibRows($lib['suites']['relay'] ?? null) : [],
    'note_en' => $noteEn,
    'note_de' => $noteDe,
    'load_note_en' => $loadNoteEn,
    'load_note_de' => $loadNoteDe,
    'versions' => $versions,
    'handshake' => $handshake,
    'join' => $join,
    'play_handshake' => $playHandshake,
    'load' => $load,
    'rounds' => [],
    'environment' => $env,
  ];
}

/** The preview snapshot, or null if no usable data is available. Memoised per request. */
function benchCiRun(): ?array
{
  static $done = false, $run = null;
  if ($done) {
    return $run;
  }
  $done = true;
  $base = benchCiBase();
  $srv = benchCiFetchJson($base . '/librtmp2-server/bench-data/latest.json');
  if ($srv === null) {
    return null;
  }
  $lib = benchCiFetchJson($base . '/librtmp2/bench-data/latest.json');
  return $run = benchCiSnapshot($srv, $lib);
}

/** Strings for the preview card and the setup section, by language. */
function benchCiStrings(string $lang): array
{
  return $lang === 'de'
    ? [
      'badge' => 'Vorschau',
      'frames_note_partial' => 'der Quellrate bei %d OpenRTMP-Zuschauern',
      'hardware' => '%s, %d vCPUs, %s GiB RAM (GitHub-gehosteter Runner, geteilte VM)',
      'os' => '%s, %s',
      'load_foot' => 'CPU und Speicher werden am Serverprozess gemessen, während alle Zuschauer verbunden sind. Dieser Runner hat %1$d vCPUs, daher sind %2$d %% die ganze Maschine.',
    ]
    : [
      'badge' => 'Preview',
      'frames_note_partial' => 'of the source rate at %d OpenRTMP viewers',
      'hardware' => '%s, %d vCPUs, %s GiB RAM (GitHub-hosted runner, shared VM)',
      'os' => '%s, %s',
      'load_foot' => 'CPU and memory are read from the server process while all viewers are connected. This runner has %1$d vCPUs, so %2$d %% is the whole machine.',
    ];
}
