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
const OPENRTMP_BENCH_LATEST = '0.6.5-0.14.0';

// Two measurements within this factor of each other are a tie: a shared machine
// jitters by a couple of percent between runs, and a factor rounded to one
// decimal ("1.0×") cannot state a direction. Comparisons inside the band are
// not put in proportion at all and read "on par"/"gleichauf" instead.
const OPENRTMP_BENCH_PARITY_TOLERANCE = 1.02;

// Server key => display name. Version-specific details live in each snapshot.
const OPENRTMP_BENCH_SERVERS = [
  'openrtmp' => 'librtmp2-server',
  'mediamtx' => 'MediaMTX',
  'liveforge' => 'LiveForge',
  'srs' => 'SRS',
  'nginx' => 'nginx-rtmp',
];

// librtmp2-server 0.6.2 / librtmp2 0.11.0 release links, and the librtmp2
// 0.11.0 microbenchmarks, shared by every snapshot of that version pair.
const OPENRTMP_BENCH_SERVER_0_6_2 = '0.6.2';
const OPENRTMP_BENCH_LIVEFORGE_4E70FB3 = 'main @ 4e70fb3';
const OPENRTMP_BENCH_SERVER_0_6_2_URL = 'https://github.com/OpenRTMP/librtmp2-server/releases/tag/v0.6.2';
const OPENRTMP_BENCH_LIB_0_11_0_URL = 'https://github.com/OpenRTMP/librtmp2/releases/tag/v0.11.0';
// Values repeated across the automatically measured snapshots.
const OPENRTMP_BENCH_STR_CHUNK_WRITE_READ_ROUNDTRIP = 'chunk/write_read_roundtrip';
const OPENRTMP_BENCH_STR_AMF0_BUILD_CONNECT = 'amf0_build_connect';
const OPENRTMP_BENCH_STR_FLV_VIDEO_TAG_H264 = 'flv/video_tag_h264';
const OPENRTMP_BENCH_STR_FLV_AUDIO_TAG_AAC = 'flv/audio_tag_aac';
const OPENRTMP_BENCH_STR_FOURCC_TO_VIDEO_CODEC_AVC1 = 'fourcc_to_video_codec_avc1';
const OPENRTMP_BENCH_STR_SERVER_READ_C1 = 'server_read_c1';
const OPENRTMP_BENCH_STR_RELAY_PUBLISH_TO_PLAYER_100 = 'relay/publish_to_player/100';
const OPENRTMP_BENCH_STR_RELAY_PUBLISH_TO_PLAYER_500 = 'relay/publish_to_player/500';
const OPENRTMP_BENCH_STR_2026_10_14 = '2026-10-14';
const OPENRTMP_BENCH_STR_AMD_EPYC_7763_64_CORE_PROCESSOR_4_VCPUS = 'AMD EPYC 7763 64-Core Processor, 4 vCPUs, Linux 6.17.0-1022-azure x86_64, rustc 1.99.0';
const OPENRTMP_BENCH_STR_1_25_NS = '1.25 ns';
const OPENRTMP_BENCH_STR_V1_21_2 = 'v1.21.2';
const OPENRTMP_BENCH_STR_NGINX_1_31_6 = 'nginx 1.31.6';
const OPENRTMP_BENCH_STR_NGINX_RTMP_MODULE_6C7719D = 'nginx-rtmp-module @ 6c7719d';
const OPENRTMP_BENCH_STR_V8_0_D0 = 'v8.0-d0';
const OPENRTMP_BENCH_STR_LINUX_6_17_0_1022_AZURE_X86_64 = 'Linux 6.17.0-1022-azure x86_64';
const OPENRTMP_BENCH_STR_GITHUB_HOSTED_UBUNTU24_IMAGE_20261004_32 = 'GitHub-hosted ubuntu24 (image 20261004.327.1)';
const OPENRTMP_BENCH_STR_1_99_0 = '1.99.0';

const OPENRTMP_BENCH_LIB_0_11_0 = [
  'lib_ref_url' => OPENRTMP_BENCH_LIB_0_11_0_URL,
  'lib_bench_source_url' => 'https://github.com/OpenRTMP/librtmp2/blob/v0.11.0/BENCHMARKS.md',
  'lib_bench_environment' => 'Intel Xeon @ 2.10 GHz, 4 vCPUs, Linux 6.18 x86_64, rustc 1.97.0',
  'lib_bench_note_en' => 'Criterion microbenchmarks for librtmp2 0.11.0. The protocol paths are unchanged from 0.10.2; the cross-version differences were measured on a shared VM with a newer rustc and are not a controlled release A/B.',
  'lib_bench_note_de' => 'Criterion-Microbenchmarks für librtmp2 0.11.0. Die Protokollpfade sind gegenüber 0.10.2 unverändert; die Unterschiede zwischen den Versionen wurden auf einer Shared-VM mit neuerem rustc gemessen und sind kein kontrollierter Release-A/B-Test.',
  'lib_protocol' => [
    [OPENRTMP_BENCH_STR_CHUNK_WRITE_READ_ROUNDTRIP, '4.55 µs', '~858 MiB/s'],
    [OPENRTMP_BENCH_STR_AMF0_BUILD_CONNECT, '274 ns', '—'],
    [OPENRTMP_BENCH_STR_FLV_VIDEO_TAG_H264, '1.51 ns', '—'],
    [OPENRTMP_BENCH_STR_FLV_AUDIO_TAG_AAC, '1.24 ns', '—'],
    [OPENRTMP_BENCH_STR_FOURCC_TO_VIDEO_CODEC_AVC1, '2.19 ns', '—'],
    [OPENRTMP_BENCH_STR_SERVER_READ_C1, '1.21 µs', '—'],
  ],
  'lib_relay' => [
    [OPENRTMP_BENCH_STR_RELAY_PUBLISH_TO_PLAYER_100, '99.1 ms', '~1010 elem/s'],
    [OPENRTMP_BENCH_STR_RELAY_PUBLISH_TO_PLAYER_500, '101.4 ms', '~4930 elem/s'],
  ],
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
  '0.6.5-0.14.0' => [
    'date' => OPENRTMP_BENCH_STR_2026_10_14,
    'server_version' => '0.6.5',
    'lib_version' => '0.14.0',
    'published_server_release' => true,
    'source_url' => 'https://github.com/OpenRTMP/librtmp2-server/blob/main/bench/releases/v0.6.5.json',
    'server_ref_url' => 'https://github.com/OpenRTMP/librtmp2-server/releases/tag/v0.6.5',
    'lib_ref_url' => 'https://github.com/OpenRTMP/librtmp2/releases/tag/v0.14.0',
    'lib_bench_source_url' => 'https://github.com/OpenRTMP/librtmp2/blob/main/bench/releases/v0.14.0.json',
    'lib_bench_environment' => OPENRTMP_BENCH_STR_AMD_EPYC_7763_64_CORE_PROCESSOR_4_VCPUS,
    'lib_bench_note_en' => 'Criterion microbenchmarks for librtmp2 0.14.0, measured automatically for the release on a shared GitHub Actions runner. Differences from earlier versions are within runner noise and are not a controlled A/B.',
    'lib_bench_note_de' => 'Criterion-Microbenchmarks für librtmp2 0.14.0, automatisch für das Release auf einem geteilten GitHub-Actions-Runner gemessen. Unterschiede zu früheren Versionen liegen im Rauschen des Runners und sind kein kontrollierter A/B-Test.',
    'lib_protocol' => [
      [OPENRTMP_BENCH_STR_AMF0_BUILD_CONNECT, '226 ns', '—'],
      [OPENRTMP_BENCH_STR_CHUNK_WRITE_READ_ROUNDTRIP, '6.87 µs', '~569 MiB/s'],
      [OPENRTMP_BENCH_STR_FLV_AUDIO_TAG_AAC, OPENRTMP_BENCH_STR_1_25_NS, '—'],
      [OPENRTMP_BENCH_STR_FLV_VIDEO_TAG_H264, OPENRTMP_BENCH_STR_1_25_NS, '—'],
      [OPENRTMP_BENCH_STR_FOURCC_TO_VIDEO_CODEC_AVC1, '2.49 ns', '—'],
      [OPENRTMP_BENCH_STR_SERVER_READ_C1, '1.07 µs', '—'],
    ],
    'lib_relay' => [
      [OPENRTMP_BENCH_STR_RELAY_PUBLISH_TO_PLAYER_100, '96.5 ms', '~1036 elem/s'],
      [OPENRTMP_BENCH_STR_RELAY_PUBLISH_TO_PLAYER_500, '98.2 ms', '~5090 elem/s'],
    ],
    'note_en' => 'librtmp2-server 0.6.5 on librtmp2 0.14.0 against LiveForge, MediaMTX, nginx-rtmp and SRS at the versions listed below. Measured automatically in GitHub Actions on GitHub-hosted ubuntu24 (image 20261004.327.1); AMD EPYC 9V74 80-Core Processor, 4 vCPUs, 15.6 GiB RAM. One sweep, no repetitions on a shared machine, so compare the servers within this snapshot.',
    'note_de' => 'librtmp2-server 0.6.5 auf librtmp2 0.14.0 gegen LiveForge, MediaMTX, nginx-rtmp und SRS in den unten aufgeführten Versionen. Automatisch in GitHub Actions auf GitHub-hosted ubuntu24 (image 20261004.327.1); AMD EPYC 9V74 80-Core Processor, 4 vCPUs, 15.6 GiB RAM. Ein Sweep ohne Wiederholungen auf einer geteilten Maschine, deshalb die Server innerhalb dieses Snapshots vergleichen.',
    'load_note_en' => 'At 500 viewers every server delivered the full source rate. At 1000 viewers nginx-rtmp fell below the source rate. At 2000 viewers MediaMTX and nginx-rtmp fell below the source rate.',
    'load_note_de' => 'Bei 500 Zuschauern lieferten alle Server die volle Quellrate. Bei 1000 Zuschauern lieferte nginx-rtmp weniger als die volle Quellrate. Bei 2000 Zuschauern lieferten MediaMTX und nginx-rtmp weniger als die volle Quellrate.',
    'versions' => [
      'openrtmp' => ['0.6.5', 'librtmp2 0.14.0', 'Rust', 'https://github.com/OpenRTMP/librtmp2-server/releases/tag/v0.6.5', 'https://github.com/OpenRTMP/librtmp2/releases/tag/v0.14.0'],
      'liveforge' => [OPENRTMP_BENCH_LIVEFORGE_4E70FB3, '', 'Go'],
      'mediamtx' => [OPENRTMP_BENCH_STR_V1_21_2, '', 'Go'],
      'nginx' => [OPENRTMP_BENCH_STR_NGINX_1_31_6, OPENRTMP_BENCH_STR_NGINX_RTMP_MODULE_6C7719D, 'C'],
      'srs' => [OPENRTMP_BENCH_STR_V8_0_D0, '', 'C++'],
    ],
    'handshake' => [
      'openrtmp' => [7127.1, 3.35, 3.43, 5.54, 6.46],
      'liveforge' => [5883.7, 4.3, 4.13, 8.28, 9.33],
      'mediamtx' => [5294.6, 4.67, 4.49, 7.58, 8.11],
      'nginx' => [686.9, 42.28, 42.34, 43.57, 44.66],
      'srs' => [556.1, 48.95, 47.97, 55.42, 57.37],
    ],
    'join' => [
      1 => [
        'openrtmp' => [0.57, 0.57, 72.94],
        'liveforge' => [0.7, 0.7, 73.1],
        'mediamtx' => [0.85, 0.85, 73.11],
        'nginx' => [82.49, 82.49, 73.23],
        'srs' => [41.27, 41.27, 71.66],
      ],
      25 => [
        'openrtmp' => [1.38, 2.09, 73.1],
        'liveforge' => [6.49, 10.51, 73.11],
        'mediamtx' => [3.01, 4.11, 73.13],
        'nginx' => [82.79, 83.63, 73.17],
        'srs' => [47.03, 49.58, 71.7],
      ],
      100 => [
        'openrtmp' => [3.05, 5.92, 73.11],
        'liveforge' => [13.52, 28.96, 73.09],
        'mediamtx' => [6.65, 11.23, 73.1],
        'nginx' => [85.11, 86.84, 72.93],
        'srs' => [60, 72.48, 72.33],
      ],
    ],
    'play_handshake' => [
      'openrtmp' => [5703.5, 3.83, 2.96, 9.73, 12.58],
      'liveforge' => [4862.2, 5.26, 5.13, 10.28, 13.25],
      'mediamtx' => [4453.5, 5.38, 5.62, 9.6, 10.1],
      'nginx' => [354.1, 83.59, 83.38, 85.35, 85.89],
      'srs' => [585, 47.48, 48.54, 52.52, 55.27],
    ],
    'load' => [
      500 => [
        'openrtmp' => [15.64, 39.4, 73.11, 38, 22.5],
        'liveforge' => [113.9, 213.7, 73.12, 59.9, 94.1],
        'mediamtx' => [37.74, 65.49, 73.08, 91.7, 110.5],
        'nginx' => [97.74, 111, 73.1, 55.6, 23.6],
        'srs' => [221.81, 268.67, 73.14, 8.1, 136.4],
      ],
      1000 => [
        'openrtmp' => [19.71, 53.06, 73.13, 71, 32.4],
        'liveforge' => [327.75, 808.19, 73.11, 120.7, 151.4],
        'mediamtx' => [112.34, 204.41, 73.12, 180.9, 157.1],
        'nginx' => [135.28, 214.5, 58.32, 87.2, 29.6],
        'srs' => [456.89, 1135.9, 73.15, 16.4, 183.3],
      ],
      2000 => [
        'openrtmp' => [87, 290.75, 73.23, 111.9, 53.1],
        'liveforge' => [1465.69, 3483.81, 74.26, 157.8, 261.5],
        'mediamtx' => [363.78, 1042.86, 41.27, 176.8, 308.1],
        'nginx' => [2476.37, 7374.62, 21.93, 54.5, 42],
        'srs' => [858.19, 1366.32, 73.33, 34.6, 261.8],
      ],
    ],
    'rounds' => [],
    'environment' => [
      'cpu' => 'AMD EPYC 9V74 80-Core Processor',
      'vcpus' => 4,
      'ram' => '15.6',
      'kernel' => OPENRTMP_BENCH_STR_LINUX_6_17_0_1022_AZURE_X86_64,
      'runner' => OPENRTMP_BENCH_STR_GITHUB_HOSTED_UBUNTU24_IMAGE_20261004_32,
      'rustc' => OPENRTMP_BENCH_STR_1_99_0,
    ],
  ],
  '0.6.4-0.13.0' => [
    'date' => OPENRTMP_BENCH_STR_2026_10_14,
    'server_version' => '0.6.4',
    'lib_version' => '0.13.0',
    'published_server_release' => true,
    'source_url' => 'https://github.com/OpenRTMP/librtmp2-server/blob/main/bench/releases/v0.6.4.json',
    'server_ref_url' => 'https://github.com/OpenRTMP/librtmp2-server/releases/tag/v0.6.4',
    'lib_ref_url' => 'https://github.com/OpenRTMP/librtmp2/releases/tag/v0.13.0',
    'lib_bench_source_url' => 'https://github.com/OpenRTMP/librtmp2/blob/main/bench/releases/v0.13.0.json',
    'lib_bench_environment' => OPENRTMP_BENCH_STR_AMD_EPYC_7763_64_CORE_PROCESSOR_4_VCPUS,
    'lib_bench_note_en' => 'Criterion microbenchmarks for librtmp2 0.13.0, measured automatically for the release on a shared GitHub Actions runner. Differences from earlier versions are within runner noise and are not a controlled A/B.',
    'lib_bench_note_de' => 'Criterion-Microbenchmarks für librtmp2 0.13.0, automatisch für das Release auf einem geteilten GitHub-Actions-Runner gemessen. Unterschiede zu früheren Versionen liegen im Rauschen des Runners und sind kein kontrollierter A/B-Test.',
    'lib_protocol' => [
      [OPENRTMP_BENCH_STR_AMF0_BUILD_CONNECT, '218 ns', '—'],
      [OPENRTMP_BENCH_STR_CHUNK_WRITE_READ_ROUNDTRIP, '6.84 µs', '~571 MiB/s'],
      [OPENRTMP_BENCH_STR_FLV_AUDIO_TAG_AAC, OPENRTMP_BENCH_STR_1_25_NS, '—'],
      [OPENRTMP_BENCH_STR_FLV_VIDEO_TAG_H264, OPENRTMP_BENCH_STR_1_25_NS, '—'],
      [OPENRTMP_BENCH_STR_FOURCC_TO_VIDEO_CODEC_AVC1, '2.50 ns', '—'],
      [OPENRTMP_BENCH_STR_SERVER_READ_C1, '1.06 µs', '—'],
    ],
    'lib_relay' => [
      [OPENRTMP_BENCH_STR_RELAY_PUBLISH_TO_PLAYER_100, '97.3 ms', '~1028 elem/s'],
      [OPENRTMP_BENCH_STR_RELAY_PUBLISH_TO_PLAYER_500, '98.8 ms', '~5058 elem/s'],
    ],
    'note_en' => 'librtmp2-server 0.6.4 on librtmp2 0.13.0 against LiveForge, MediaMTX, nginx-rtmp and SRS at the versions listed below. Measured automatically in GitHub Actions on GitHub-hosted ubuntu24 (image 20261004.327.1); INTEL(R) XEON(R) PLATINUM 8573C, 4 vCPUs, 15.6 GiB RAM. One sweep, no repetitions on a shared machine, so compare the servers within this snapshot.',
    'note_de' => 'librtmp2-server 0.6.4 auf librtmp2 0.13.0 gegen LiveForge, MediaMTX, nginx-rtmp und SRS in den unten aufgeführten Versionen. Automatisch in GitHub Actions auf GitHub-hosted ubuntu24 (image 20261004.327.1); INTEL(R) XEON(R) PLATINUM 8573C, 4 vCPUs, 15.6 GiB RAM. Ein Sweep ohne Wiederholungen auf einer geteilten Maschine, deshalb die Server innerhalb dieses Snapshots vergleichen.',
    'load_note_en' => 'At 500 and 1000 viewers every server delivered the full source rate. At 2000 viewers nginx-rtmp fell below the source rate.',
    'load_note_de' => 'Bei 500 und 1000 Zuschauern lieferten alle Server die volle Quellrate. Bei 2000 Zuschauern lieferte nginx-rtmp weniger als die volle Quellrate.',
    'versions' => [
      'openrtmp' => ['0.6.4', 'librtmp2 0.13.0', 'Rust', 'https://github.com/OpenRTMP/librtmp2-server/releases/tag/v0.6.4', 'https://github.com/OpenRTMP/librtmp2/releases/tag/v0.13.0'],
      'liveforge' => [OPENRTMP_BENCH_LIVEFORGE_4E70FB3, '', 'Go'],
      'mediamtx' => [OPENRTMP_BENCH_STR_V1_21_2, '', 'Go'],
      'nginx' => [OPENRTMP_BENCH_STR_NGINX_1_31_6, OPENRTMP_BENCH_STR_NGINX_RTMP_MODULE_6C7719D, 'C'],
      'srs' => [OPENRTMP_BENCH_STR_V8_0_D0, '', 'C++'],
    ],
    'handshake' => [
      'openrtmp' => [11809.9, 1.84, 1.73, 3.4, 3.79],
      'liveforge' => [9764.8, 2.54, 2.38, 4.32, 5.2],
      'mediamtx' => [9164, 2.7, 2.37, 4.76, 7.25],
      'nginx' => [707.6, 41.72, 41.8, 42.55, 43.25],
      'srs' => [583.6, 48.06, 48.94, 53.96, 54.98],
    ],
    'join' => [
      1 => [
        'openrtmp' => [0.46, 0.46, 72.98],
        'liveforge' => [0.53, 0.53, 73.08],
        'mediamtx' => [0.7, 0.7, 73.08],
        'nginx' => [81.65, 81.65, 72.95],
        'srs' => [41.48, 41.48, 71.74],
      ],
      25 => [
        'openrtmp' => [2.23, 3.13, 73],
        'liveforge' => [4.23, 6.98, 73.09],
        'mediamtx' => [1.84, 2.63, 73.06],
        'nginx' => [82.36, 83.59, 72.89],
        'srs' => [47.01, 50.44, 71.78],
      ],
      100 => [
        'openrtmp' => [1.88, 4.2, 73.11],
        'liveforge' => [9.86, 15.44, 73.14],
        'mediamtx' => [4.41, 8.33, 73.08],
        'nginx' => [83.61, 85.35, 72.91],
        'srs' => [63.25, 71.22, 71.82],
      ],
    ],
    'play_handshake' => [
      'openrtmp' => [12477.4, 1.69, 1.5, 3.42, 4.62],
      'liveforge' => [10425.1, 2.06, 1.93, 3.95, 4.65],
      'mediamtx' => [7399.2, 3.2, 3.01, 5.43, 5.75],
      'nginx' => [357.3, 82.83, 82.84, 84.02, 84.88],
      'srs' => [627.4, 45.54, 44.64, 51.72, 51.94],
    ],
    'load' => [
      500 => [
        'openrtmp' => [7.6, 19.31, 73.13, 20.8, 22.5],
        'liveforge' => [30.61, 70.34, 73.12, 30.9, 98.4],
        'mediamtx' => [22.74, 36.29, 73.08, 57.8, 108.1],
        'nginx' => [85.9, 91.87, 73.05, 28.9, 24],
        'srs' => [171.62, 203.44, 72.21, 5.6, 138.2],
      ],
      1000 => [
        'openrtmp' => [14.86, 43.41, 73.12, 41, 32.2],
        'liveforge' => [126.04, 220.12, 73.09, 64.5, 147.7],
        'mediamtx' => [27.89, 65.42, 73.09, 114.9, 159.1],
        'nginx' => [89.6, 106.13, 73.09, 55.1, 29.9],
        'srs' => [389.01, 1100.54, 72.36, 11.3, 183.6],
      ],
      2000 => [
        'openrtmp' => [24.42, 84.88, 73.14, 77.3, 52.1],
        'liveforge' => [317.91, 846.09, 73.09, 121.6, 247.6],
        'mediamtx' => [87.45, 176.34, 73.27, 197.9, 294.3],
        'nginx' => [177.21, 175.69, 61.54, 82, 38.2],
        'srs' => [700.1, 1292.1, 73.16, 23.5, 263.5],
      ],
    ],
    'rounds' => [],
    'environment' => [
      'cpu' => 'INTEL(R) XEON(R) PLATINUM 8573C',
      'vcpus' => 4,
      'ram' => '15.6',
      'kernel' => OPENRTMP_BENCH_STR_LINUX_6_17_0_1022_AZURE_X86_64,
      'runner' => OPENRTMP_BENCH_STR_GITHUB_HOSTED_UBUNTU24_IMAGE_20261004_32,
      'rustc' => OPENRTMP_BENCH_STR_1_99_0,
    ],
  ],
  '0.6.3-0.12.0' => [
    'date' => OPENRTMP_BENCH_STR_2026_10_14,
    'server_version' => '0.6.3',
    'lib_version' => '0.12.0',
    'published_server_release' => true,
    'source_url' => 'https://github.com/OpenRTMP/librtmp2-server/blob/main/bench/releases/v0.6.3.json',
    'server_ref_url' => 'https://github.com/OpenRTMP/librtmp2-server/releases/tag/v0.6.3',
    'lib_ref_url' => 'https://github.com/OpenRTMP/librtmp2/releases/tag/v0.12.0',
    'lib_bench_source_url' => 'https://github.com/OpenRTMP/librtmp2/blob/main/bench/releases/v0.12.0.json',
    'lib_bench_environment' => OPENRTMP_BENCH_STR_AMD_EPYC_7763_64_CORE_PROCESSOR_4_VCPUS,
    'lib_bench_note_en' => 'Criterion microbenchmarks for librtmp2 0.12.0, measured automatically for the release on a shared GitHub Actions runner. Differences from earlier versions are within runner noise and are not a controlled A/B.',
    'lib_bench_note_de' => 'Criterion-Microbenchmarks für librtmp2 0.12.0, automatisch für das Release auf einem geteilten GitHub-Actions-Runner gemessen. Unterschiede zu früheren Versionen liegen im Rauschen des Runners und sind kein kontrollierter A/B-Test.',
    'lib_protocol' => [
      [OPENRTMP_BENCH_STR_AMF0_BUILD_CONNECT, '219 ns', '—'],
      [OPENRTMP_BENCH_STR_CHUNK_WRITE_READ_ROUNDTRIP, '5.89 µs', '~663 MiB/s'],
      [OPENRTMP_BENCH_STR_FLV_AUDIO_TAG_AAC, OPENRTMP_BENCH_STR_1_25_NS, '—'],
      [OPENRTMP_BENCH_STR_FLV_VIDEO_TAG_H264, OPENRTMP_BENCH_STR_1_25_NS, '—'],
      [OPENRTMP_BENCH_STR_FOURCC_TO_VIDEO_CODEC_AVC1, '2.49 ns', '—'],
      [OPENRTMP_BENCH_STR_SERVER_READ_C1, '1.07 µs', '—'],
    ],
    'lib_relay' => [
      [OPENRTMP_BENCH_STR_RELAY_PUBLISH_TO_PLAYER_100, '97.0 ms', '~1031 elem/s'],
      [OPENRTMP_BENCH_STR_RELAY_PUBLISH_TO_PLAYER_500, '100.3 ms', '~4984 elem/s'],
    ],
    'note_en' => 'librtmp2-server 0.6.3 on librtmp2 0.12.0 against LiveForge, MediaMTX, nginx-rtmp and SRS at the versions listed below. Measured automatically in GitHub Actions on GitHub-hosted ubuntu24 (image 20261004.327.1); AMD EPYC 9V45 96-Core Processor, 4 vCPUs, 15.6 GiB RAM. One sweep, no repetitions on a shared machine, so compare the servers within this snapshot.',
    'note_de' => 'librtmp2-server 0.6.3 auf librtmp2 0.12.0 gegen LiveForge, MediaMTX, nginx-rtmp und SRS in den unten aufgeführten Versionen. Automatisch in GitHub Actions auf GitHub-hosted ubuntu24 (image 20261004.327.1); AMD EPYC 9V45 96-Core Processor, 4 vCPUs, 15.6 GiB RAM. Ein Sweep ohne Wiederholungen auf einer geteilten Maschine, deshalb die Server innerhalb dieses Snapshots vergleichen.',
    'load_note_en' => 'At 500 and 1000 viewers every server delivered the full source rate. At 2000 viewers MediaMTX and nginx-rtmp fell below the source rate.',
    'load_note_de' => 'Bei 500 und 1000 Zuschauern lieferten alle Server die volle Quellrate. Bei 2000 Zuschauern lieferten MediaMTX und nginx-rtmp weniger als die volle Quellrate.',
    'versions' => [
      'openrtmp' => ['0.6.3', 'librtmp2 0.12.0', 'Rust', 'https://github.com/OpenRTMP/librtmp2-server/releases/tag/v0.6.3', 'https://github.com/OpenRTMP/librtmp2/releases/tag/v0.12.0'],
      'liveforge' => [OPENRTMP_BENCH_LIVEFORGE_4E70FB3, '', 'Go'],
      'mediamtx' => [OPENRTMP_BENCH_STR_V1_21_2, '', 'Go'],
      'nginx' => [OPENRTMP_BENCH_STR_NGINX_1_31_6, OPENRTMP_BENCH_STR_NGINX_RTMP_MODULE_6C7719D, 'C'],
      'srs' => [OPENRTMP_BENCH_STR_V8_0_D0, '', 'C++'],
    ],
    'handshake' => [
      'openrtmp' => [10073, 2.23, 1.6, 6.54, 7.01],
      'liveforge' => [9904.5, 2.23, 2.11, 3.86, 5.13],
      'mediamtx' => [9113.6, 2.74, 2.59, 4.78, 6.03],
      'nginx' => [707.8, 41.53, 41.27, 42.83, 43.04],
      'srs' => [594.6, 47.32, 47.92, 51.71, 51.79],
    ],
    'join' => [
      1 => [
        'openrtmp' => [0.41, 0.41, 73.13],
        'liveforge' => [2.37, 2.37, 73.14],
        'mediamtx' => [0.62, 0.62, 73.09],
        'nginx' => [82.02, 82.02, 72.98],
        'srs' => [41.97, 41.97, 72.19],
      ],
      25 => [
        'openrtmp' => [1.07, 1.78, 73.12],
        'liveforge' => [4.54, 8.19, 73.12],
        'mediamtx' => [2.48, 3.62, 73.08],
        'nginx' => [82.27, 82.88, 72.96],
        'srs' => [44.31, 45.6, 72.1],
      ],
      100 => [
        'openrtmp' => [1.26, 2.2, 73.13],
        'liveforge' => [6.52, 18.8, 73.07],
        'mediamtx' => [1.83, 3.93, 73.11],
        'nginx' => [84.52, 87.66, 72.97],
        'srs' => [56.93, 64.44, 71.44],
      ],
    ],
    'play_handshake' => [
      'openrtmp' => [11890.6, 1.81, 1.49, 4.11, 5.42],
      'liveforge' => [8376.2, 2.84, 2.9, 5.3, 5.88],
      'mediamtx' => [8462.5, 2.44, 2.32, 4.21, 6.75],
      'nginx' => [358, 83.08, 82.89, 85.16, 85.8],
      'srs' => [625.9, 45.92, 46.02, 50.9, 51.17],
    ],
    'load' => [
      500 => [
        'openrtmp' => [13.93, 31.45, 73.15, 26.3, 22.2],
        'liveforge' => [65.19, 124.8, 73.1, 40.2, 96],
        'mediamtx' => [20.08, 37.89, 73.12, 62.8, 112.6],
        'nginx' => [87.19, 93.51, 73.06, 40.8, 23.9],
        'srs' => [146.82, 175.21, 72.38, 5.7, 139.6],
      ],
      1000 => [
        'openrtmp' => [8.44, 26.24, 73.15, 51.9, 31.8],
        'liveforge' => [90.84, 201.68, 73.13, 83.6, 151.5],
        'mediamtx' => [30.14, 70.42, 73.08, 127.5, 163.4],
        'nginx' => [106.53, 136.4, 73.08, 88.5, 29.8],
        'srs' => [408.57, 1133.09, 72.96, 10.1, 184.2],
      ],
      2000 => [
        'openrtmp' => [29.89, 99.52, 73.14, 98.6, 52.8],
        'liveforge' => [400.28, 1058.22, 73.07, 151.4, 275.7],
        'mediamtx' => [120.23, 248.16, 69.9, 196.1, 300.2],
        'nginx' => [194.03, 227.7, 55.62, 95, 35],
        'srs' => [581.18, 1191.21, 73.14, 21.5, 268],
      ],
    ],
    'rounds' => [],
    'environment' => [
      'cpu' => 'AMD EPYC 9V45 96-Core Processor',
      'vcpus' => 4,
      'ram' => '15.6',
      'kernel' => OPENRTMP_BENCH_STR_LINUX_6_17_0_1022_AZURE_X86_64,
      'runner' => OPENRTMP_BENCH_STR_GITHUB_HOSTED_UBUNTU24_IMAGE_20261004_32,
      'rustc' => OPENRTMP_BENCH_STR_1_99_0,
    ],
  ],
  '0.6.2-0.11.0-2026-10-09' => OPENRTMP_BENCH_LIB_0_11_0 + [
    'date' => '2026-10-09',
    'server_version' => OPENRTMP_BENCH_SERVER_0_6_2,
    'lib_version' => '0.11.0',
    'published_server_release' => true,
    'source_url' => 'https://github.com/OpenRTMP/librtmp2-server/blob/main/BENCHMARKS.md',
    'server_ref_url' => OPENRTMP_BENCH_SERVER_0_6_2_URL,
    'note_en' => 'librtmp2-server 0.6.2 on librtmp2 0.11.0 against the newest version of every compared server: MediaMTX v1.21.2, nginx 1.31.6 with nginx-rtmp-module master, SRS 8.0 and LiveForge main.',
    'note_de' => 'librtmp2-server 0.6.2 auf librtmp2 0.11.0 gegen die neueste Version jedes verglichenen Servers: MediaMTX v1.21.2, nginx 1.31.6 mit nginx-rtmp-module master, SRS 8.0 und LiveForge main.',
    'load_note_en' => 'At 500 viewers every server delivered the full source rate. From 1000 viewers nginx-rtmp fell below it, and at 2000 viewers MediaMTX and LiveForge did too; only librtmp2-server and SRS delivered every frame at 2000 viewers.',
    'load_note_de' => 'Bei 500 Zuschauern lieferten alle Server die volle Quellrate. Ab 1000 Zuschauern fiel nginx-rtmp darunter, bei 2000 Zuschauern auch MediaMTX und LiveForge; nur librtmp2-server und SRS lieferten bei 2000 Zuschauern jedes Frame.',
    'versions' => [
      'openrtmp' => [
        OPENRTMP_BENCH_SERVER_0_6_2,
        'librtmp2 0.11.0',
        'Rust',
        OPENRTMP_BENCH_SERVER_0_6_2_URL,
        OPENRTMP_BENCH_LIB_0_11_0_URL,
      ],
      'mediamtx' => [OPENRTMP_BENCH_STR_V1_21_2, 'release binary', 'Go'],
      'liveforge' => [OPENRTMP_BENCH_LIVEFORGE_4E70FB3, '', 'Go'],
      'srs' => ['8.0.48', OPENRTMP_BENCH_STR_V8_0_D0, 'C++'],
      'nginx' => [OPENRTMP_BENCH_STR_NGINX_1_31_6, OPENRTMP_BENCH_STR_NGINX_RTMP_MODULE_6C7719D, 'C'],
    ],
    'handshake' => [
      'openrtmp' => [7256.8, 2.91, 2.72, 5.60, 6.45],
      'liveforge' => [5290.2, 4.70, 4.26, 9.12, 10.25],
      'mediamtx' => [4952.5, 4.72, 4.13, 8.75, 10.04],
      'nginx' => [630.5, 45.94, 45.55, 48.88, 49.41],
      'srs' => [494.2, 56.64, 56.00, 65.73, 67.72],
    ],
    'join' => [
      1 => [
        'openrtmp' => [0.89, null, null],
        'liveforge' => [1.16, null, null],
        'mediamtx' => [1.49, null, null],
        'srs' => [43.7, null, null],
        'nginx' => [88.3, null, null],
      ],
      25 => [
        'openrtmp' => [2.69, 5.03, null],
        'mediamtx' => [2.76, 4.62, null],
        'liveforge' => [4.56, 7.97, null],
        'srs' => [53.5, 57.6, null],
        'nginx' => [89.2, 92.7, null],
      ],
      100 => [
        'openrtmp' => [4.94, 10.4, null],
        'mediamtx' => [7.66, 15.8, null],
        'liveforge' => [12.5, 23.5, null],
        'srs' => [71.7, 87.6, null],
        'nginx' => [91.0, 95.7, null],
      ],
    ],
    'play_handshake' => [
      'openrtmp' => [7035.7, 2.83, 2.23, 6.77, 7.68],
      'liveforge' => [5276.2, 4.01, 3.29, 9.27, 13.32],
      'mediamtx' => [4291.6, 5.66, 5.33, 9.96, 12.38],
      'srs' => [515.6, 54.52, 54.86, 62.56, 63.43],
      'nginx' => [323.0, 89.44, 88.82, 92.27, 94.08],
    ],
    'load' => [
      500 => [
        'openrtmp' => [27.2, 68.5, 73.1, 39.4, 22.4],
        'mediamtx' => [60.8, 114.7, 73.1, 94.6, 104.7],
        'liveforge' => [62.9, 151.1, 73.1, 56.3, 89.5],
        'nginx' => [109.0, 134.8, 73.1, 64.3, 18.6],
        'srs' => [264.6, 324.6, 73.2, 10.3, 106.4],
      ],
      1000 => [
        'openrtmp' => [30.4, 83.8, 73.1, 72.5, 32.1],
        'mediamtx' => [115.0, 209.4, 73.1, 170.8, 174.7],
        'nginx' => [166.1, 225.5, 55.0, 79.2, 24.5],
        'liveforge' => [207.3, 478.4, 73.1, 107.1, 155.7],
        'srs' => [563.7, 1007.7, 73.1, 20.1, 151.7],
      ],
      2000 => [
        'openrtmp' => [83.2, 308.2, 73.2, 87.9, 51.7],
        'nginx' => [290.3, 631.2, 43.4, 79.7, 29.1],
        'mediamtx' => [538.3, 1523.4, 27.0, 146.0, 293.1],
        'liveforge' => [977.4, 2832.7, 64.9, 129.5, 251.9],
        'srs' => [1147.8, 2272.2, 73.1, 38.2, 236.3],
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
      [OPENRTMP_BENCH_STR_CHUNK_WRITE_READ_ROUNDTRIP, '3.76 µs', '~1040 MiB/s'],
      [OPENRTMP_BENCH_STR_AMF0_BUILD_CONNECT, '229 ns', '—'],
      [OPENRTMP_BENCH_STR_FLV_VIDEO_TAG_H264, '1.10 ns', '—'],
      [OPENRTMP_BENCH_STR_FLV_AUDIO_TAG_AAC, '1.15 ns', '—'],
      [OPENRTMP_BENCH_STR_FOURCC_TO_VIDEO_CODEC_AVC1, '2.21 ns', '—'],
      [OPENRTMP_BENCH_STR_SERVER_READ_C1, '1.08 µs', '—'],
    ],
    'lib_relay' => [
      [OPENRTMP_BENCH_STR_RELAY_PUBLISH_TO_PLAYER_100, '99.0 ms', '~1010 elem/s'],
      [OPENRTMP_BENCH_STR_RELAY_PUBLISH_TO_PLAYER_500, '101.0 ms', '~4950 elem/s'],
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
      'liveforge' => [OPENRTMP_BENCH_LIVEFORGE_4E70FB3, '', 'Go'],
      'srs' => ['v8.0.48', '', 'C++'],
      'nginx' => [OPENRTMP_BENCH_STR_NGINX_1_31_6, 'nginx-rtmp master @ 6c7719d', 'C'],
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
 * How many times worse than the leader a value is, or null when either side is
 * zero (a zero cannot be put in proportion; the caller then shows no factor).
 */
function benchBarFactor(float $value, float $lead, bool $higherIsBetter): ?float
{
  if ($value <= 0 || $lead <= 0) {
    return null;
  }
  return $higherIsBetter ? $lead / $value : $value / $lead;
}

/**
 * The badge for the leader, the "n.n×" factor for everyone else. A row inside
 * the parity band is a tie: printing "1.0×" would claim a difference it cannot
 * express -- and would sit next to the "Fastest" badge of the server it just
 * called a full 1.0× ahead -- so it is labelled as level instead.
 */
function benchBarDelta(int $rank, float $value, float $lead, bool $higherIsBetter, string $best, string $lang): string
{
  if ($rank === 1) {
    return '<span class="bench-badge">' . $best . '</span>';
  }
  $factor = benchBarFactor($value, $lead, $higherIsBetter);
  if ($factor === null) {
    return '';
  }
  if ($factor < OPENRTMP_BENCH_PARITY_TOLERANCE) {
    return '<span class="bench-delta">' . ($lang === 'de' ? 'gleichauf' : 'on par') . '</span>';
  }
  return '<span class="bench-delta">' . benchNum($factor, $factor < 1.1 ? 2 : 1, $lang) . '&times;</span>';
}

/** One <li> of the bar chart. */
function benchBarRow(string $key, int $rank, float $width, string $formatted, string $delta): string
{
  $class = $key === 'openrtmp' ? 'bench-row is-openrtmp' : 'bench-row';
  return '<li class="' . $class . '">'
    . '<span class="bench-rank">' . $rank . '</span>'
    . '<span class="bench-name">' . htmlspecialchars(OPENRTMP_BENCH_SERVERS[$key], ENT_QUOTES, 'UTF-8') . '</span>'
    . '<span class="bench-track"><span class="bench-fill" style="width: ' . $width . '%"></span></span>'
    . '<span class="bench-value">' . htmlspecialchars($formatted, ENT_QUOTES, 'UTF-8') . '</span>'
    . $delta
    . '</li>';
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
    $html .= benchBarRow($key, $rank, $width, $format($value), benchBarDelta($rank, $value, $lead, $higherIsBetter, $best, $lang));
  }
  return $html . '</ol>';
}
