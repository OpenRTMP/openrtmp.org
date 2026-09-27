<?php
$lang = 'de';
$page = 'benchmarks';
$pageTitle = 'RTMP-Server-Benchmarks — OpenRTMP gegen nginx-rtmp, MediaMTX, SRS und LiveForge';
$pageDescription = 'librtmp2-server im Benchmark gegen nginx-rtmp, MediaMTX, SRS 8.0 und LiveForge: Publish- und Play-Handshakes, Join-Latenz bei 1, 25 und 100 gleichzeitigen Zuschauern sowie Join-Latenz, CPU und Speicher bei 500 und 1000 Zuschauern.';
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

$L = [
  'eyebrow' => 'Benchmarks &middot; ' . OPENRTMP_BENCH_DATE,
  'h1' => 'Der schnellste RTMP-Server,',
  'h1_accent' => 'den wir gemessen haben.',
  'lead' => '<code>librtmp2-server</code> gegen nginx-rtmp, MediaMTX, SRS 8.0 und LiveForge: dieselbe Maschine, derselbe RTMP-Client, derselbe echte H.264/AAC-Stream. Er verbindet Publisher und Player am schnellsten und führt beim Join mit 25, 100 und 1000 Zuschauern.',
  'cta_results' => 'Zu den Ergebnissen',
  'cta_try' => 'Selbst ausprobieren',
  'kpi_join' => 'Join mit 100 Zuschauern',
  'kpi_handshake' => 'Connect + Publish',
  'kpi_rate' => 'Handshakes pro Sekunde',
  'kpi_frames' => 'Frames zugestellt',
  'kpi_faster' => '%s&times; schneller als %s',
  'kpi_more' => '%s&times; mehr als %s',
  'kpi_frames_note' => 'an alle 1000 Zuschauer, ohne Verluste',
  'join_eyebrow' => 'Wiedergabe',
  'join_h2' => 'Join-Latenz der Zuschauer',
  'join_p' => 'Ein Publisher, dann verbinden sich 1, 25 oder 100 Zuschauer gleichzeitig. Gemessen vom Verbindungsaufbau bis zum ersten empfangenen Frame.',
  'join_panel' => 'Durchschnittliche Join-Latenz',
  'lower_better' => 'Weniger ist besser',
  'higher_better' => 'Mehr ist besser',
  'viewers' => 'Zuschauer',
  'n_viewers' => '%d Zuschauer',
  'one_viewer' => '1 Zuschauer',
  'join_foot' => 'Quelle: H.264 mit 1280x720@30 und 2,5 Mbit/s plus AAC mit 128 kbit/s. Jeder Server hat jedem Zuschauer den vollständigen Stream geliefert.',
  'hs_eyebrow' => 'Ingest',
  'hs_h2' => 'Connect und Publish',
  'hs_p' => '120 Connect- und Publish-Handshakes mit 30 gleichzeitigen Verbindungen. librtmp2-server ist hier der einzige Server, der jeden Publish gegen seinen Stream-Key prüft.',
  'hs_latency' => 'Durchschnittliche Handshake-Latenz',
  'hs_rate' => 'Handshakes pro Sekunde',
  'play_eyebrow' => 'Player',
  'play_h2' => 'Connect und Play',
  'play_p' => '120 Connect- und Play-Handshakes mit 30 gleichzeitigen Verbindungen gegen einen laufenden Stream, bis <code>NetStream.Play.Start</code>. librtmp2-server prüft jeden Play gegen seinen eigenen Play-Key.',
  'play_latency' => 'Durchschnittliche Play-Handshake-Latenz',
  'play_rate' => 'Play-Handshakes pro Sekunde',
  'load_eyebrow' => 'Unter Last',
  'load_h2' => '500 und 1000 Zuschauer auf einem Stream',
  'load_p' => 'Jeder Server hat jedem Zuschauer den vollständigen Stream geliefert, bei 1000 Zuschauern rund 1,1 Gbit/s. Der Unterschied liegt darin, wie schnell die Zuschauer drin sind und was der Server dafür braucht.',
  'load_panel' => 'Join-Latenz, CPU und Speicher',
  'load_join' => 'Durchschnittliche Join-Latenz',
  'load_cpu' => 'Server-CPU (100 % = ein Kern)',
  'load_rss' => 'Server-Speicher (Spitzen-RSS)',
  'leanest' => 'Sparsamster',
  'load_foot' => 'CPU und Speicher werden am Serverprozess gemessen, während alle Zuschauer verbunden sind. Die Maschine hat 4 vCPUs, 400 % ist also die ganze Maschine.',
  'duel_eyebrow' => 'Direkter Vergleich',
  'duel_h2' => 'Gegen die stärksten Konkurrenten',
  'duel_p' => 'MediaMTX und LiveForge, in abwechselnden Runden gegen librtmp2-server gemessen.',
  'rounds' => [
    'join_100' => 'Join mit 100 Zuschauern',
    'single_join' => 'Join eines Zuschauers',
    'seq_connect' => 'Connect + Publish, sequenziell',
    'connect_30' => 'Connect + Publish, 30 gleichzeitig',
  ],
  'table_eyebrow' => 'Alle Zahlen',
  'table_h2' => 'Vollständige Ergebnisse',
  'table_p' => 'Alle Werte aus den Diagrammen oben, inklusive der Tail-Latenzen.',
  'col_rate' => 'Handshakes/s',
  'col_join_avg' => 'Join avg',
  'col_join_p95' => 'Join p95',
  'col_fps' => 'fps pro Zuschauer',
  'col_cpu' => 'CPU',
  'col_rss' => 'Spitzen-RSS',
  'why_eyebrow' => 'Unter der Haube',
  'why_h2' => 'Warum er so schnell ist',
  'why' => [
    ['&#9889;', 'Sofortige Autorisierung', 'Publish und Play werden aus einem In-Memory-Snapshot der Keys beantwortet; die Session-Zeile landet direkt danach in SQLite.'],
    ['&#128276;', 'Geweckt statt gepollt', 'Jede Schleife wartet auf einem persistenten <code>epoll</code>-Set und wird über ein <code>eventfd</code> geweckt, sobald Arbeit ansteht.'],
    ['&#129521;', 'Ein Shard pro Kern', 'Verbindungen werden auf <code>SO_REUSEPORT</code>-Shards verteilt, und jeder Socket läuft mit <code>TCP_NODELAY</code>.'],
    ['&#128230;', 'Nur einmal gechunkt', '<code>librtmp2</code> zerlegt jeden Frame einmal pro Fan-out in Chunks statt einmal pro Zuschauer.'],
  ],
  'setup_eyebrow' => 'Testaufbau',
  'setup_h2' => 'Gleiche Maschine, gleicher Client, gleicher Stream',
  'specs' => [
    ['Hardware', 'Intel Xeon @ 2,10 GHz, 4 vCPUs, 15 GiB RAM'],
    ['OS', 'Linux 6.18'],
    ['Client', '<code>bench_handshake</code> und <code>bench_relay</code> aus librtmp2'],
    ['Stream', 'ffmpeg, H.264 1280x720@30 mit 2,5 Mbit/s + AAC 128 kbit/s, 2 s GOP'],
    ['Ablauf', 'Ein Server nach dem anderen; nginx-rtmp mit <code>worker_processes 1</code>'],
  ],
  'src_server' => 'Vollständige BENCHMARKS.md',
  'src_script' => 'Benchmark-Skript',
  'src_lib' => 'librtmp2-Microbenchmarks',
  'cta_h2' => 'Selbst ausprobieren',
  'cta_p' => 'Den Docker-Stack in wenigen Minuten starten und OBS oder ffmpeg darauf richten.',
  'cta_quick' => 'Schnellstart in fünf Minuten',
  'cta_compare' => 'Funktionsvergleich',
];

include_once __DIR__ . '/../../includes/benchmarks-page.php';
include_once __DIR__ . '/../../includes/footer.php';
