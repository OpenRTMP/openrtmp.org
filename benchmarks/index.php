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

$L = [
  'eyebrow' => 'Benchmarks &middot; ' . OPENRTMP_BENCH_DATE,
  'h1' => 'The fastest RTMP server',
  'h1_accent' => 'we have measured.',
  'lead' => '<code>librtmp2-server</code> against nginx-rtmp, MediaMTX, SRS 8.0 and LiveForge: same machine, same RTMP client, same real H.264/AAC stream. It has the lowest average latency in every test.',
  'cta_results' => 'See the results',
  'cta_try' => 'Try it yourself',
  'kpi_join' => 'Join with 100 viewers',
  'kpi_handshake' => 'Connect + publish',
  'kpi_rate' => 'Handshakes per second',
  'kpi_frames' => 'Frames delivered',
  'kpi_faster' => '%s&times; faster than %s',
  'kpi_more' => '%s&times; more than %s',
  'kpi_frames_note' => 'to all 100 viewers, no drops',
  'join_eyebrow' => 'Playback',
  'join_h2' => 'Viewer join latency',
  'join_p' => 'One publisher, then 1, 25 or 100 viewers connect at once. Measured from connecting to the first received frame.',
  'join_panel' => 'Average join latency',
  'lower_better' => 'Lower is better',
  'higher_better' => 'Higher is better',
  'viewers' => 'Viewers',
  'n_viewers' => '%d viewers',
  'one_viewer' => '1 viewer',
  'join_foot' => 'Source: 1280x720@30 H.264 at 2.5 Mbps plus 128 kbps AAC. Every server delivered the full stream to every viewer.',
  'hs_eyebrow' => 'Ingest',
  'hs_h2' => 'Connect and publish',
  'hs_p' => '120 connect + publish handshakes at a concurrency of 30. librtmp2-server is the only server here that checks every publish against its stream key.',
  'hs_latency' => 'Average handshake latency',
  'hs_rate' => 'Handshakes per second',
  'duel_eyebrow' => 'Head to head',
  'duel_h2' => 'Against the closest competitors',
  'duel_p' => 'MediaMTX and LiveForge, measured in interleaved rounds against librtmp2-server.',
  'rounds' => [
    'join_100' => 'Join with 100 viewers',
    'single_join' => 'Single-viewer join',
    'seq_connect' => 'Connect + publish, sequential',
    'connect_30' => 'Connect + publish, 30 concurrent',
  ],
  'table_eyebrow' => 'All numbers',
  'table_h2' => 'Full results',
  'table_p' => 'Every value from the charts above, including the tail latencies.',
  'col_rate' => 'Handshakes/s',
  'col_join_avg' => 'Join avg',
  'col_join_p95' => 'Join p95',
  'col_fps' => 'fps per viewer',
  'why_eyebrow' => 'Under the hood',
  'why_h2' => 'Why it is this fast',
  'why' => [
    ['&#9889;', 'Instant authorization', 'Publishes and plays are answered from an in-memory key snapshot; the session row is written to SQLite right after.'],
    ['&#128276;', 'Woken, not polling', 'Each loop waits on a persistent <code>epoll</code> set and is woken through an <code>eventfd</code> the moment work arrives.'],
    ['&#129521;', 'One shard per core', 'Connections are spread over <code>SO_REUSEPORT</code> shards, and every socket runs with <code>TCP_NODELAY</code>.'],
    ['&#128230;', 'Chunked once', '<code>librtmp2</code> chunks each frame once per fan-out instead of once per viewer.'],
  ],
  'setup_eyebrow' => 'Test setup',
  'setup_h2' => 'Same machine, same client, same stream',
  'specs' => [
    ['Hardware', 'Intel Xeon @ 2.10 GHz, 4 vCPUs, 15 GiB RAM'],
    ['OS', 'Linux 6.18'],
    ['Client', '<code>bench_handshake</code> and <code>bench_relay</code> from librtmp2'],
    ['Stream', 'ffmpeg, 1280x720@30 H.264 2.5 Mbps + AAC 128 kbps, 2 s GOP'],
    ['Order', 'One server at a time; nginx-rtmp with <code>worker_processes 1</code>'],
  ],
  'col_lang' => 'Language',
  'src_server' => 'Full BENCHMARKS.md',
  'src_script' => 'Benchmark script',
  'src_lib' => 'librtmp2 microbenchmarks',
  'cta_h2' => 'Run it yourself',
  'cta_p' => 'Start the Docker stack in a few minutes and point OBS or ffmpeg at it.',
  'cta_quick' => 'Five-minute quickstart',
  'cta_compare' => 'Feature comparison',
];

include __DIR__ . '/../includes/benchmarks-page.php';
include_once __DIR__ . '/../includes/footer.php';
