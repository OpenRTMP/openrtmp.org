<?php
// The newest automated benchmark run, offered on /benchmarks/ (EN + DE) next to
// the immutable release snapshots as "next release preview".
//
// Both OpenRTMP/librtmp2-server and OpenRTMP/librtmp2 run their benchmarks in
// GitHub Actions after every merge to main and store the result as
// `bench/latest.json` on main (see docs/ci-benchmarks.md in those
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
 * Base URL of the raw `bench/latest.json` files. A constant, never request or
 * environment input, so the outgoing request target cannot be influenced by a
 * visitor. Tests define OPENRTMP_BENCH_DATA_BASE (e.g. a file:// directory)
 * from an auto_prepend_file before this file is included.
 */
function benchCiBase(): string
{
  return rtrim(defined('OPENRTMP_BENCH_DATA_BASE') ? OPENRTMP_BENCH_DATA_BASE : 'https://raw.githubusercontent.com/OpenRTMP', '/');
}

/** True for file:// URLs (a test base, never the production default). */
function benchCiIsFileUrl(string $url): bool
{
  return strncmp($url, 'file://', 7) === 0;
}

/**
 * Read a document through the stream wrapper (file:// test bases, or no curl).
 *
 * @return string|false
 */
function benchCiReadStream(string $url)
{
  $context = benchCiIsFileUrl($url) ? null : stream_context_create(['http' => ['timeout' => 5, 'user_agent' => 'openrtmp.org benchmark preview']]);
  return @file_get_contents($url, false, $context, 0, OPENRTMP_BENCH_CI_MAX_BYTES + 1);
}

/**
 * Download with cURL, aborting the transfer as soon as it exceeds the size cap
 * instead of buffering an oversized response first.
 *
 * @return string|false
 */
function benchCiReadCurl(string $url)
{
  $buffer = '';
  $limit = OPENRTMP_BENCH_CI_MAX_BYTES;
  $ch = curl_init($url);
  curl_setopt_array($ch, [
    CURLOPT_CONNECTTIMEOUT => 3,
    CURLOPT_TIMEOUT => 5,
    CURLOPT_FOLLOWLOCATION => false,
    CURLOPT_FAILONERROR => true,
    CURLOPT_USERAGENT => 'openrtmp.org benchmark preview',
    CURLOPT_WRITEFUNCTION => function ($handle, $chunk) use (&$buffer, $limit) {
      if (strlen($buffer) + strlen($chunk) > $limit) {
        return 0; // abort: the transfer fails with a write error
      }
      $buffer .= $chunk;
      return strlen($chunk);
    },
  ]);
  $done = curl_exec($ch);
  unset($ch); // curl_close() is a deprecated no-op since PHP 8.5; unset() releases the handle
  return $done === true ? $buffer : false;
}

/** GET a small document; null on any failure. */
function benchCiHttpGet(string $url): ?string
{
  $viaCurl = !benchCiIsFileUrl($url) && function_exists('curl_init');
  $body = $viaCurl ? benchCiReadCurl($url) : benchCiReadStream($url);
  $usable = is_string($body) && $body !== '' && strlen($body) <= OPENRTMP_BENCH_CI_MAX_BYTES;
  return $usable ? $body : null;
}

function benchCiDecode(?string $body): ?array
{
  if ($body === null) {
    return null;
  }
  $data = json_decode($body, true, 32);
  return is_array($data) ? $data : null;
}

/**
 * The uid of the user the site runs as, or null on platforms without file
 * user ids (Windows). There the system temp directory belongs to one user
 * anyway.
 */
function benchCiEuid(): ?int
{
  return function_exists('posix_geteuid') ? posix_geteuid() : null;
}

/**
 * The private cache directory of the fetched documents, or null when it cannot
 * be used. It lives in the system temp directory but is created with 0700
 * permissions and must belong to the running user, so no other local user can
 * read, replace or pre-create the documents inside it. The effective user id
 * is part of the name, so every site user on the host gets a private cache
 * instead of losing it to the user who came first.
 */
function benchCiCacheDir(): ?string
{
  static $dir = false;
  if ($dir === false) {
    $dir = benchCiUsableCacheDir();
  }
  return $dir;
}

/** The usable private cache directory, or null when it cannot be used. */
function benchCiUsableCacheDir(): ?string
{
  $euid = benchCiEuid();
  $path = sys_get_temp_dir() . '/openrtmp-bench' . ($euid === null ? '' : '-' . $euid);
  return benchCiEnsurePrivateDir($path, $euid) ? $path : null;
}

/**
 * Create the private cache directory if needed and make sure it is closed to
 * other local users. Returns false when it cannot be used safely.
 */
function benchCiEnsurePrivateDir(string $path, ?int $euid): bool
{
  if (!is_dir($path) && !@mkdir($path, 0700) && !is_dir($path)) {
    return false;
  }
  $owned = benchCiPrivateDirIsOurs($path, $euid);
  $closed = $owned && benchCiPrivateDirIsClosed($path);
  if ($owned && !$closed) {
    // Other users must not be able to read the cache or plant a document in
    // it: tighten the mode once and re-check.
    @chmod($path, 0700);
    $closed = benchCiPrivateDirIsClosed($path);
  }
  return $closed;
}

/** True when the path is a real directory (not a symlink) that we own. */
function benchCiPrivateDirIsOurs(string $path, ?int $euid): bool
{
  $stat = @lstat($path);
  if ($stat === false || ($stat['mode'] & 0170000) !== 0040000) {
    return false;
  }
  return $euid === null || (int) $stat['uid'] === $euid;
}

/** True when the directory grants no permission to group or other users. */
function benchCiPrivateDirIsClosed(string $path): bool
{
  $stat = @lstat($path);
  return $stat !== false && ($stat['mode'] & 0077) === 0;
}

/**
 * True when the path is a regular file that belongs to the running user, so a
 * document planted by another local user (or a symlink to one) is never
 * trusted.
 */
function benchCiTrustedFile(string $path): bool
{
  $stat = @lstat($path);
  if ($stat === false || ($stat['mode'] & 0170000) !== 0100000) {
    return false;
  }
  $euid = benchCiEuid();
  return $euid === null || (int) $stat['uid'] === $euid;
}

/** Age of a file in seconds, or null if it does not exist or is not ours. */
function benchCiAge(string $path): ?int
{
  return benchCiTrustedFile($path) ? time() - (int) @filemtime($path) : null;
}

/** The cache file of one document inside the private cache directory. */
function benchCiCachePath(string $url): ?string
{
  $dir = benchCiCacheDir();
  return $dir === null ? null : $dir . '/' . hash('sha256', $url) . '.json';
}

/** The cached document if it is younger than $maxAge seconds and still valid, else null. */
function benchCiCached(string $cache, int $maxAge, callable $valid): ?array
{
  $age = benchCiAge($cache);
  if ($age === null || $age < 0 || $age >= $maxAge) {
    return null;
  }
  $body = @file_get_contents($cache, false, null, 0, OPENRTMP_BENCH_CI_MAX_BYTES + 1);
  if (!is_string($body) || $body === '' || strlen($body) > OPENRTMP_BENCH_CI_MAX_BYTES) {
    return null;
  }
  $data = benchCiDecode($body === false ? null : $body);
  return $data !== null && $valid($data) ? $data : null;
}

/**
 * Download a document and store it in the cache. A document that is not
 * semantically valid never replaces the last good cached copy; a failure sets a
 * back-off marker.
 */
function benchCiRefresh(string $url, string $cache, callable $valid): ?array
{
  $failed = $cache . '.failed';
  $failedAge = benchCiAge($failed);
  if ($failedAge !== null && $failedAge >= 0 && $failedAge < OPENRTMP_BENCH_CI_FAIL_TTL) {
    return null;
  }
  $body = benchCiHttpGet($url);
  $data = benchCiDecode($body);
  if ($data === null || !$valid($data)) {
    @touch($failed);
    return null;
  }
  $dir = benchCiCacheDir();
  $tmp = $dir === null ? false : @tempnam($dir, 'openrtmp-bench-');
  if ($tmp !== false) {
    $written = @file_put_contents($tmp, $body);
    if ($written !== strlen($body) || !@rename($tmp, $cache)) {
      @unlink($tmp);
    }
  }
  @unlink($failed);
  return $data;
}

/** Fetch one JSON document with an on-disk cache; null if unavailable. */
function benchCiFetchJson(string $url, callable $valid): ?array
{
  $cache = benchCiCachePath($url);
  if ($cache === null) {
    // Without a private cache directory the document is fetched every time,
    // but never stored where another user could plant one.
    $data = benchCiDecode(benchCiHttpGet($url));
    return $data !== null && $valid($data) ? $data : null;
  }
  return benchCiCached($cache, OPENRTMP_BENCH_CI_TTL, $valid)
    ?? benchCiRefresh($url, $cache, $valid)
    ?? benchCiCached($cache, OPENRTMP_BENCH_CI_STALE_TTL, $valid);
}

/**
 * Validate one row: every value is a finite number above zero (charts divide by
 * them), except in the columns listed in $nullable, where null is allowed.
 *
 * @param mixed $row
 * @param int[] $nullable column indexes that may be null
 */
function benchCiRow($row, int $width, array $nullable = []): ?array
{
  if (!is_array($row) || count($row) !== $width) {
    return null;
  }
  $out = [];
  foreach (array_values($row) as $i => $v) {
    if ($v === null && in_array($i, $nullable, true)) {
      $out[] = null;
    } elseif ((is_int($v) || is_float($v)) && is_finite((float) $v) && $v > 0) {
      $out[] = (float) $v;
    } else {
      return null;
    }
  }
  return $out;
}

/**
 * Normalise {server: row} for the known servers, in display order. Unknown
 * servers and malformed rows are dropped.
 *
 * @param mixed $rows
 */
function benchCiRows($rows, int $width, array $nullable = []): array
{
  $out = [];
  if (!is_array($rows)) {
    return $out;
  }
  foreach (array_keys(OPENRTMP_BENCH_SERVERS) as $key) {
    if (isset($rows[$key]) && ($row = benchCiRow($rows[$key], $width, $nullable)) !== null) {
      $out[$key] = $row;
    }
  }
  return $out;
}

/**
 * Same as benchCiRows for {viewers: {server: row}}, viewer counts as int keys.
 *
 * @param mixed $tiers
 */
function benchCiTiers($tiers, int $width, array $nullable = []): array
{
  $out = [];
  if (!is_array($tiers)) {
    return $out;
  }
  foreach ($tiers as $viewers => $rows) {
    if (!is_numeric($viewers) || (int) $viewers < 1) {
      continue;
    }
    $clean = benchCiRows($rows, $width, $nullable);
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
  // mbstring is optional on the supported PHP setups; fall back to a byte cut
  // and drop a character split at the end.
  if (function_exists('mb_substr')) {
    return mb_substr($s, 0, $max);
  }
  $cut = substr($s, 0, $max);
  return preg_match('//u', $cut) === 1 ? $cut : (string) preg_replace('/[\x80-\xff]+$/', '', $cut);
}

function benchCiSha($value): string
{
  return is_string($value) && preg_match('/^[0-9a-f]{7,40}$/', $value) ? substr($value, 0, 7) : '';
}

/**
 * A measurement of the remote document as a positive, finite number, or null
 * when it is missing, not a number, infinite or not above zero. The sweep rows
 * use the same rule (see benchCiRow), so a malformed document can never be
 * published as a measurement.
 */
function benchCiPositiveNumber($value): ?float
{
  return (is_int($value) || is_float($value)) && is_finite((float) $value) && $value > 0 ? (float) $value : null;
}

/** @return array{cpu:string,vcpus:int,ram:string,kernel:string,runner:string,rustc:string}|null */
function benchCiEnv($env): ?array
{
  $vcpus = is_array($env) ? benchCiPositiveNumber($env['vcpus'] ?? null) : null;
  $ram = is_array($env) ? benchCiPositiveNumber($env['ram_gib'] ?? null) : null;
  // The vCPU count is shown as an integer, so a fractional or out-of-range
  // value would be truncated to a wrong count.
  $usable = $vcpus !== null
    && $ram !== null
    && $vcpus === floor($vcpus)
    && $vcpus < (float) PHP_INT_MAX;
  if (!$usable) {
    return null;
  }
  return [
    'cpu' => benchCiString($env['cpu'] ?? ''),
    'vcpus' => (int) $vcpus,
    'ram' => rtrim(rtrim(number_format($ram, 1, '.', ''), '0'), '.'),
    'kernel' => benchCiString($env['kernel'] ?? ''),
    'runner' => benchCiString($env['runner'] ?? ''),
    'rustc' => benchCiString($env['rustc'] ?? ''),
  ];
}

/**
 * A RAM size for display: one decimal with the page language's separators and
 * trailing zeros dropped ("15,6" / "16"). The snapshots keep the value
 * locale-neutral; only the rendered page is localised.
 */
function benchCiRam(string $ram, string $lang): string
{
  $decimal = $lang === 'de' ? ',' : '.';
  $text = number_format((float) $ram, 1, $decimal, $lang === 'de' ? '.' : ',');
  return rtrim(rtrim($text, '0'), $decimal);
}

/**
 * Localise the machine hardware recorded inside prose (snapshot notes and the
 * librtmp2 environment line): the RAM size and the CPU frequency. Those
 * strings are stored locale-neutrally, so a German page must not show
 * "15.6 GiB RAM" or "2.10 GHz" next to its localised numbers.
 */
function benchCiLocalizeRam(string $text, string $lang): string
{
    if ($lang !== 'de') {
        return $text;
    }
    $text = preg_replace_callback(
        '/(\d+)\.(\d{1,2})(?=\s*GiB RAM)/u',
        static fn(array $m): string => $m[1] . ',' . $m[2],
        $text
    );
    return preg_replace_callback(
        '/(\d+)\.(\d{1,2})(?=\s*GHz)/u',
        static fn(array $m): string => $m[1] . ',' . $m[2],
        $text
    );
}

/**
 * Localise a stored microbenchmark time ("6.87 µs") for the page language. The
 * snapshots keep the value locale-neutral; only the rendered page is localised.
 */
function benchCiLocalizeTime(string $time, string $lang): string
{
    if ($lang !== 'de') {
        return $time;
    }
    return preg_replace_callback(
        '/(\d+)\.(\d+)(?=\s*(?:ns|µs|ms|s)(?![A-Za-z0-9]))/u',
        static fn(array $m): string => $m[1] . ',' . $m[2],
        $time
    );
}

function benchCiTime(float $ns, string $lang = 'en'): string
{
  $decimal = $lang === 'de' ? ',' : '.';
  $thousand = $lang === 'de' ? '.' : ',';
  // [upper bound in ns, divisor, decimals, unit]
  foreach ([[1e3, 1, $ns < 10 ? 2 : 0, 'ns'], [1e6, 1e3, 2, 'µs'], [1e9, 1e6, 1, 'ms']] as [$limit, $divisor, $decimals, $unit]) {
    if ($ns < $limit) {
      return number_format($ns / $divisor, $decimals, $decimal, $thousand) . ' ' . $unit;
    }
  }
  return number_format($ns / 1e9, 2, $decimal, $thousand) . ' s';
}

/** Items per second for a per-iteration count, or null if it is not a positive number. */
function benchCiRate($count, float $ns): ?float
{
  if (!is_numeric($count) || !is_finite((float) $count) || $count <= 0 || $ns <= 0) {
    return null;
  }
  $seconds = $ns * 1e-9;
  if ($seconds <= 0.0 || !is_finite($seconds)) {
    return null; // the scaled duration underflows to zero
  }
  $rate = $count / $seconds;
  return is_finite($rate) ? $rate : null;
}

function benchCiThroughput(array $b): string
{
  $ns = (float) ($b['ns'] ?? 0);
  $bytes = benchCiRate($b['bytes'] ?? null, $ns);
  $elements = benchCiRate($b['elements'] ?? null, $ns);
  if ($bytes !== null) {
    return '~' . number_format($bytes / 1048576, 0, '.', '') . ' MiB/s';
  }
  return $elements !== null ? '~' . number_format($elements, 0, '.', '') . ' elem/s' : '—';
}

/** @return list<array{string,string,string}> */
function benchCiLibRows($suite, string $lang = 'en'): array
{
  $rows = [];
  if (!is_array($suite)) {
    return $rows;
  }
  ksort($suite);
  foreach ($suite as $name => $b) {
    if (!is_array($b)) {
      continue;
    }
    $ns = benchCiPositiveNumber($b['ns'] ?? null);
    if ($ns !== null) {
      $rows[] = [benchCiString((string) $name, 120), benchCiTime($ns, $lang), benchCiThroughput($b)];
    }
  }
  return $rows;
}

/**
 * Validate the sweep and pull out the rows the page needs; null if anything
 * the page relies on is missing.
 */
function benchCiSweepRows(array $srv): ?array
{
  $sweep = $srv['sweep'] ?? null;
  $env = benchCiEnv($srv['environment'] ?? null);
  $valid = ($srv['schema'] ?? null) === 1 && is_array($sweep) && $env !== null
    && preg_match('/^\d{4}-\d{2}-\d{2}/', (string) ($srv['date'] ?? ''), $m) === 1;
  if (!$valid) {
    return null;
  }
  $rows = [
    'handshake' => benchCiRows($sweep['handshake'] ?? null, 5, [2]),
    'play_handshake' => benchCiRows($sweep['play_handshake'] ?? null, 5, [2]),
    'join' => benchCiTiers($sweep['join'] ?? null, 3, [1, 2]),
    'load' => benchCiTiers($sweep['load'] ?? null, 5),
  ];
  $complete = isset($rows['handshake']['openrtmp'], $rows['play_handshake']['openrtmp'], $rows['join'][100]['openrtmp'])
    && $rows['load'] !== []
    && count(array_filter($rows['load'], fn($tier) => isset($tier['openrtmp']))) === count($rows['load']);
  return $complete ? $rows + ['env' => $env, 'date' => $m[0], 'versions' => $sweep['versions'] ?? []] : null;
}

/** Per-server version rows for the setup table; only servers that have rows in this run. */
function benchCiVersions(array $rows, string $sha, string $serverVersion): array
{
  $present = array_keys($rows['handshake'] + $rows['play_handshake']);
  foreach (array_merge(array_values($rows['join']), array_values($rows['load'])) as $tier) {
    $present = array_merge($present, array_keys($tier));
  }
  $versions = [];
  foreach (array_keys(OPENRTMP_BENCH_SERVERS) as $key) {
    if (!in_array($key, $present, true)) {
      continue;
    }
    $v = $rows['versions'][$key] ?? [];
    $row = [
      benchCiString($v['version'] ?? '—', 80),
      benchCiString($v['detail'] ?? '', 120),
      benchCiString($v['language'] ?? '', 20),
    ];
    if ($key === 'openrtmp') {
      $row[0] = 'main @ ' . ($sha !== '' ? $sha : '?') . ($serverVersion !== '' ? ' (' . $serverVersion . ')' : '');
      if ($sha !== '') {
        $row[] = 'https://github.com/OpenRTMP/librtmp2-server/commit/' . $sha;
      }
    }
    $versions[$key] = $row;
  }
  return $versions;
}

/** Library microbenchmark data from librtmp2's own latest run, if usable. */
function benchCiLibInfo(?array $lib, string $depLib, string $lang = 'en'): array
{
  $ok = $lib !== null && ($lib['schema'] ?? null) === 1 && is_array($lib['suites'] ?? null);
  $env = $ok ? benchCiEnv($lib['environment'] ?? null) : null;
  $sha = $ok ? benchCiSha($lib['commit'] ?? '') : '';
  $version = $ok ? benchCiString($lib['version'] ?? '', 40) : '';
  $label = $depLib !== '' ? $depLib : '—';
  if ($version !== '') {
    $label = $version . ($sha !== '' ? ' (main @ ' . $sha . ')' : '');
  }
  $envLine = $env === null ? '—' : sprintf(
    '%s, %d vCPUs, %s GiB RAM, %s, rustc %s, %s',
    $env['cpu'], $env['vcpus'], $env['ram'], $env['kernel'], $env['rustc'], $env['runner']
  );
  return [
    'sha' => $sha,
    'label' => $label,
    'env_line' => $envLine,
    'protocol' => $ok ? benchCiLibRows($lib['suites']['protocol'] ?? null, $lang) : [],
    'relay' => $ok ? benchCiLibRows($lib['suites']['relay'] ?? null, $lang) : [],
  ];
}

/** The English and German note shown under the snapshot cards. */
function benchCiNotes(array $env, string $sha, string $date): array
{
  $runner = $env['runner'] !== '' ? $env['runner'] : 'CI runner';
  $machine = fn(string $language): string => sprintf(
    '%s, %d vCPUs, %s GiB RAM',
    $env['cpu'],
    $env['vcpus'],
    benchCiRam((string) $env['ram'], $language)
  );
  $ref = $sha !== '' ? $sha : 'main';
  return [
    'en' => sprintf(
      'Next-release preview: the latest automated run on main (librtmp2-server %s, %s), measured on %s: %s. It shows what the next release may look like, but it is one sweep on a shared CI machine. Compare the servers with each other inside this run; every value is replaced after the next merge.',
      $ref, $date, $runner, $machine('en')
    ),
    'de' => sprintf(
      'Vorschau auf das nächste Release: der neueste automatische Lauf auf main (librtmp2-server %s, %s), gemessen auf %s: %s. Er zeigt, was das nächste Release erwarten lässt, ist aber ein einzelner Sweep auf einer geteilten CI-Maschine. Server innerhalb dieses Laufs vergleichen; jeder Wert wird nach dem nächsten Merge ersetzt.',
      $ref, $date, $runner, $machine('de')
    ),
  ];
}

/**
 * Build the preview snapshot from the two results files, or null if the
 * librtmp2-server file does not carry a usable sweep.
 */
function benchCiSnapshot(array $srv, ?array $lib, string $lang = 'en'): ?array
{
  $rows = benchCiSweepRows($srv);
  if ($rows === null) {
    return null;
  }
  $sha = benchCiSha($srv['commit'] ?? '');
  $libInfo = benchCiLibInfo($lib, benchCiString($srv['deps']['librtmp2'] ?? '', 40), $lang);
  $notes = benchCiNotes($rows['env'], $sha, $rows['date']);
  $commitUrl = fn(string $repo, string $ref) => $ref !== '' ? 'https://github.com/OpenRTMP/' . $repo . '/commit/' . $ref : null;

  return [
    'kind' => 'ci',
    'date' => $rows['date'],
    'server_version' => 'main @ ' . ($sha !== '' ? $sha : '?'),
    'lib_version' => $libInfo['label'],
    'published_server_release' => false,
    'source_url' => 'https://github.com/OpenRTMP/librtmp2-server/blob/main/bench/latest.json',
    'server_ref_url' => $commitUrl('librtmp2-server', $sha),
    'lib_ref_url' => $commitUrl('librtmp2', $libInfo['sha']),
    'lib_bench_source_url' => 'https://github.com/OpenRTMP/librtmp2/blob/main/bench/latest.json',
    'lib_bench_environment' => $libInfo['env_line'],
    'lib_bench_note_en' => 'Criterion microbenchmarks from the latest automated librtmp2 run on main. They ran on a shared CI machine, so read them as indicative.',
    'lib_bench_note_de' => 'Criterion-Microbenchmarks aus dem neuesten automatischen librtmp2-Lauf auf main. Sie liefen auf einer geteilten CI-Maschine und sind daher nur als Richtwert zu lesen.',
    'lib_protocol' => $libInfo['protocol'],
    'lib_relay' => $libInfo['relay'],
    'note_en' => $notes['en'],
    'note_de' => $notes['de'],
    'load_note_en' => 'Servers that delivered fewer frames per viewer than the source rate were overloaded at that step, so those rows are a stress indicator rather than a clean comparison.',
    'load_note_de' => 'Server, die weniger Bilder pro Zuschauer lieferten als die Quellrate, waren in diesem Schritt überlastet; diese Zeilen sind daher eher ein Stresstest als ein sauberer Vergleich.',
    'versions' => benchCiVersions($rows, $sha, benchCiString($srv['version'] ?? '', 40)),
    'handshake' => $rows['handshake'],
    'join' => $rows['join'],
    'play_handshake' => $rows['play_handshake'],
    'load' => $rows['load'],
    'rounds' => [],
    'environment' => $rows['env'],
  ];
}

/** Semantic check for the server file; used before a download replaces the cache. */
function benchCiValidServer(array $doc): bool
{
  return benchCiSweepRows($doc) !== null;
}

/** Semantic check for the library file. */
function benchCiValidLib(array $doc): bool
{
  return ($doc['schema'] ?? null) === 1 && is_array($doc['suites'] ?? null);
}

/** The preview snapshot, or null if no usable data is available. Memoised per request. */
function benchCiRun(): ?array
{
  static $done = false, $run = null;
  if ($done) {
    return $run;
  }
  $done = true;
  // The preview is rendered by the page that asks for it, so its numbers use
  // the language of that page (the site serves one language per request).
  global $lang;
  $pageLang = $lang === 'de' ? 'de' : 'en';
  $base = benchCiBase();
  $srv = benchCiFetchJson($base . '/librtmp2-server/main/bench/latest.json', 'benchCiValidServer');
  if ($srv === null) {
    return null;
  }
  $lib = benchCiFetchJson($base . '/librtmp2/main/bench/latest.json', 'benchCiValidLib');
  return $run = benchCiSnapshot($srv, $lib, $pageLang);
}

// ---------------------------------------------------------------------------
// Release snapshots
//
// The release job of librtmp2-server records every release run as
// `bench/releases/<tag>.json` on main (same schema as `latest.json`), and
// librtmp2 does the same for its own releases. Releases that have no
// hand-written entry in OPENRTMP_BENCH_RUNS are read from those files, so a
// new release appears on the site as soon as its benchmark run is committed.

const OPENRTMP_BENCH_RELEASE_LIST_URL = 'https://api.github.com/repos/OpenRTMP/librtmp2-server/contents/bench/releases?ref=main';
const OPENRTMP_BENCH_RELEASE_MAX = 12; // newest release files read per request

/** Path of a release results file inside a repository, e.g. `bench/releases/v0.6.6.json`. */
function benchCiReleasePath(string $version): string
{
  return 'bench/releases/v' . $version . '.json';
}

/** True for a `bench/releases` file name such as `v0.6.6.json`; $m[1] is the version. */
function benchCiReleaseName($name, ?array &$m = null): bool
{
  return is_string($name) && preg_match('/^v(\d{1,4}\.\d{1,4}\.\d{1,4})\.json$/', $name, $m) === 1;
}

/** Semantic check for the directory listing of `bench/releases`. */
function benchCiValidReleaseList(array $doc): bool
{
  // Same contract as array_is_list() (PHP 8.1+), but the site supports
  // PHP 7.4/8.0 where that function does not exist.
  return $doc === [] || array_keys($doc) === range(0, count($doc) - 1);
}

/**
 * Versions of the librtmp2-server releases that have a results file, newest
 * first. Read from the GitHub contents API; a file:// test base is listed
 * directly.
 *
 * @return list<string>
 */
function benchCiReleaseVersions(): array
{
  $base = benchCiBase();
  if (benchCiIsFileUrl($base)) {
    $names = @scandir(substr($base, 7) . '/librtmp2-server/main/bench/releases');
    $names = is_array($names) ? $names : [];
  } else {
    $list = benchCiFetchJson(OPENRTMP_BENCH_RELEASE_LIST_URL, 'benchCiValidReleaseList') ?? [];
    $names = array_map(fn($entry) => is_array($entry) ? ($entry['name'] ?? null) : null, $list);
  }
  $versions = [];
  foreach ($names as $name) {
    if (benchCiReleaseName($name, $m)) {
      $versions[] = $m[1];
    }
  }
  usort($versions, fn(string $a, string $b) => version_compare($b, $a));
  return array_slice(array_values(array_unique($versions)), 0, OPENRTMP_BENCH_RELEASE_MAX);
}

/** "a", "a and b", "a, b and c" in the page language. */
function benchCiJoinList(array $items, string $lang): string
{
  if (count($items) < 2) {
    return implode('', $items);
  }
  $and = $lang === 'de' ? ' und ' : ' and ';
  return implode(', ', array_slice($items, 0, -1)) . $and . end($items);
}

/** Display names of the servers in one load step that delivered less than the source rate. */
function benchCiBelowSource(array $rows): array
{
  $source = max(array_column($rows, 2));
  $below = [];
  foreach ($rows as $key => $row) {
    if ($row[2] < $source * 0.98) {
      $below[] = OPENRTMP_BENCH_SERVERS[$key];
    }
  }
  usort($below, 'strcasecmp');
  return $below;
}

/** Servers that delivered less than the source rate, per load step, as a sentence. */
function benchCiLoadNote(array $load, string $lang): string
{
  $full = [];
  $short = [];
  foreach ($load as $viewers => $rows) {
    $below = benchCiBelowSource($rows);
    if ($below === []) {
      $full[] = $viewers;
    } else {
      $short[$viewers] = $below;
    }
  }
  $de = $lang === 'de';
  $parts = [];
  if ($full !== []) {
    $format = $de ? 'Bei %s Zuschauern lieferten alle Server die volle Quellrate.' : 'At %s viewers every server delivered the full source rate.';
    $parts[] = sprintf($format, benchCiJoinList($full, $lang));
  }
  foreach ($short as $viewers => $below) {
    $plural = count($below) > 1 ? 'n' : '';
    $parts[] = $de
      ? sprintf('Bei %d Zuschauern lieferte%s %s weniger als die volle Quellrate.', $viewers, $plural, benchCiJoinList($below, $lang))
      : sprintf('At %d viewers %s fell below the source rate.', $viewers, benchCiJoinList($below, $lang));
  }
  return implode(' ', $parts);
}

/** The English and German note of a release snapshot. */
function benchCiReleaseNotes(array $snap, string $server, string $lib): array
{
  $env = $snap['environment'];
  $competitors = array_values(array_map(
    fn($key) => OPENRTMP_BENCH_SERVERS[$key],
    array_filter(array_keys($snap['versions']), fn($key) => $key !== 'openrtmp')
  ));
  usort($competitors, 'strcasecmp');
  $machine = sprintf('%s, %d vCPUs, %s GiB RAM', $env['cpu'], $env['vcpus'], $env['ram']);
  return [
    'en' => sprintf(
      'librtmp2-server %s on librtmp2 %s against %s at the versions listed below. Measured automatically in GitHub Actions on %s; %s. One sweep, no repetitions on a shared machine, so compare the servers within this snapshot.',
      $server, $lib, benchCiJoinList($competitors, 'en'), $env['runner'], $machine
    ),
    'de' => sprintf(
      'librtmp2-server %s auf librtmp2 %s gegen %s in den unten aufgeführten Versionen. Automatisch in GitHub Actions auf %s; %s. Ein Sweep ohne Wiederholungen auf einer geteilten Maschine, deshalb die Server innerhalb dieses Snapshots vergleichen.',
      $server, $lib, benchCiJoinList($competitors, 'de'), $env['runner'], $machine
    ),
  ];
}

/** Turn a release results file (and the matching librtmp2 release file) into a snapshot. */
function benchCiReleaseSnapshot(array $srv, ?array $lib, string $version, string $lang = 'en'): ?array
{
  $snap = benchCiSnapshot($srv, $lib, $lang);
  $libVersion = benchCiString($srv['deps']['librtmp2'] ?? '', 40);
  // A release run in which every competitor was skipped compares nothing;
  // it stays off the site until the release is re-measured.
  if ($snap === null || count($snap['handshake']) < 2 || preg_match('/^\d{1,4}\.\d{1,4}\.\d{1,4}$/', $libVersion) !== 1) {
    return null;
  }
  $serverUrl = 'https://github.com/OpenRTMP/librtmp2-server/releases/tag/v' . $version;
  $libUrl = 'https://github.com/OpenRTMP/librtmp2/releases/tag/v' . $libVersion;
  $snap['versions']['openrtmp'] = [$version, 'librtmp2 ' . $libVersion, 'Rust', $serverUrl, $libUrl];
  $notes = benchCiReleaseNotes($snap, $version, $libVersion);
  return array_merge($snap, [
    'kind' => 'release',
    'server_version' => $version,
    'lib_version' => $libVersion,
    'published_server_release' => true,
    'source_url' => 'https://github.com/OpenRTMP/librtmp2-server/blob/main/' . benchCiReleasePath($version),
    'server_ref_url' => $serverUrl,
    'lib_ref_url' => $libUrl,
    'lib_bench_source_url' => 'https://github.com/OpenRTMP/librtmp2/blob/main/' . benchCiReleasePath($libVersion),
    'lib_bench_note_en' => 'Criterion microbenchmarks for librtmp2 ' . $libVersion . ', measured automatically for the release on a shared GitHub Actions runner. Differences from earlier versions are within runner noise and are not a controlled A/B.',
    'lib_bench_note_de' => 'Criterion-Microbenchmarks für librtmp2 ' . $libVersion . ', automatisch für das Release auf einem geteilten GitHub-Actions-Runner gemessen. Unterschiede zu früheren Versionen liegen im Rauschen des Runners und sind kein kontrollierter A/B-Test.',
    'note_en' => $notes['en'],
    'note_de' => $notes['de'],
    'load_note_en' => benchCiLoadNote($snap['load'], 'en'),
    'load_note_de' => benchCiLoadNote($snap['load'], 'de'),
  ]);
}

/**
 * Release snapshots read from the results files, keyed like
 * OPENRTMP_BENCH_RUNS ("<server>-<librtmp2>"). Releases listed in $skip
 * (server versions that already have a hand-written snapshot) are not
 * fetched. Memoised per request.
 *
 * @param list<string> $skip
 */
function benchCiReleaseRuns(array $skip): array
{
  static $runs = null;
  if ($runs !== null) {
    return $runs;
  }
  global $lang;
  $pageLang = $lang === 'de' ? 'de' : 'en';
  $base = benchCiBase();
  $runs = [];
  foreach (benchCiReleaseVersions() as $version) {
    if (in_array($version, $skip, true)) {
      continue;
    }
    $srv = benchCiFetchJson($base . '/librtmp2-server/main/' . benchCiReleasePath($version), 'benchCiValidServer');
    $libVersion = is_array($srv) ? benchCiString($srv['deps']['librtmp2'] ?? '', 40) : '';
    $lib = preg_match('/^\d{1,4}\.\d{1,4}\.\d{1,4}$/', $libVersion) === 1
      ? benchCiFetchJson($base . '/librtmp2/main/' . benchCiReleasePath($libVersion), 'benchCiValidLib')
      : null;
    $snap = $srv === null ? null : benchCiReleaseSnapshot($srv, $lib, $version, $pageLang);
    if ($snap !== null) {
      $runs[$version . '-' . $snap['lib_version']] = $snap;
    }
  }
  return $runs;
}

/** Strings for the preview card and the setup section, by language. */
function benchCiStrings(string $lang): array
{
  return $lang === 'de'
    ? [
      'badge' => 'Vorschau',
      'kpi_par' => 'auf Augenhöhe mit %s',
      'kpi_slower' => '%s&times; langsamer als %s',
      'kpi_fewer' => '%s&times; weniger als %s',
      'frames_note_partial' => 'der Quellrate bei %d OpenRTMP-Zuschauern',
      'hardware' => '%s, %d vCPUs, %s GiB RAM (GitHub-gehosteter Runner, geteilte VM)',
      'os' => '%s, %s',
      'load_foot' => 'CPU und Speicher werden am Serverprozess gemessen, während alle Zuschauer verbunden sind. Dieser Runner hat %1$d vCPUs, daher sind %2$d %% die ganze Maschine.',
    ]
    : [
      'badge' => 'Preview',
      'kpi_par' => 'on par with %s',
      'kpi_slower' => '%s&times; slower than %s',
      'kpi_fewer' => '%s&times; fewer than %s',
      'frames_note_partial' => 'of the source rate at %d OpenRTMP viewers',
      'hardware' => '%s, %d vCPUs, %s GiB RAM (GitHub-hosted runner, shared VM)',
      'os' => '%s, %s',
      'load_foot' => 'CPU and memory are read from the server process while all viewers are connected. This runner has %1$d vCPUs, so %2$d %% is the whole machine.',
    ];
}
