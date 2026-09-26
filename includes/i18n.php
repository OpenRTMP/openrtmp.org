<?php
// Language handling for the shared header and footer.
//
// English is the primary language and lives at the site root. German pages
// mirror the English URL structure below /de/ and set `$lang = 'de'` before
// including the header. Strings in the shared includes are looked up by their
// English text, so an untranslated string simply falls back to English.

$lang = (isset($lang) && $lang === 'de') ? 'de' : 'en';

const OPENRTMP_LANG_PREFIX = ['en' => '', 'de' => '/de'];
const OPENRTMP_OG_LOCALE = ['en' => 'en_US', 'de' => 'de_DE'];

const OPENRTMP_STRINGS_DE = [
  // Header
  'OpenRTMP — Rust RTMP/E-RTMP library and self-hosted server' => 'OpenRTMP — Rust-RTMP/E-RTMP-Bibliothek und selbst gehosteter Server',
  'OpenRTMP provides a Rust RTMP/E-RTMP library plus a self-hosted RTMP/RTMPS server, REST API, live statistics, and web control panel.' => 'OpenRTMP bietet eine Rust-RTMP/E-RTMP-Bibliothek sowie einen selbst gehosteten RTMP/RTMPS-Server mit REST-API, Live-Statistiken und Web-Control-Panel.',
  'OpenRTMP social preview showing the librtmp2 Rust RTMP/E-RTMP library and the self-hosted RTMP/RTMPS server with web panel.' => 'OpenRTMP-Vorschaubild mit der Rust-RTMP/E-RTMP-Bibliothek librtmp2 und dem selbst gehosteten RTMP/RTMPS-Server mit Web-Panel.',
  'OpenRTMP logo' => 'OpenRTMP-Logo',
  'Primary navigation' => 'Hauptnavigation',
  'Quickstart' => 'Schnellstart',
  'Guides' => 'Anleitungen',
  'Showcase' => 'Showcase',
  'Docs' => 'Doku',
  'Download' => 'Download',
  'View on GitHub' => 'Auf GitHub ansehen',
  'Run with Docker' => 'Mit Docker starten',
  'Toggle navigation' => 'Navigation umschalten',
  // Footer
  'Modern RTMP infrastructure for developers and stream operators: a Rust RTMP/E-RTMP library, a self-hosted RTMP/RTMPS server, REST API, live statistics, and a web control panel.' => 'Moderne RTMP-Infrastruktur für Entwickler und Stream-Betreiber: eine Rust-RTMP/E-RTMP-Bibliothek, ein selbst gehosteter RTMP/RTMPS-Server, REST-API, Live-Statistiken und ein Web-Control-Panel.',
  'Active development, pre-1.0. Pin versions and validate your complete workflow before critical production use.' => 'Aktive Entwicklung, vor 1.0. Versionen fest pinnen und den gesamten Workflow vor kritischem Produktiveinsatz validieren.',
  'Get started' => 'Loslegen',
  'Five-minute Docker quickstart' => 'Docker-Schnellstart in fünf Minuten',
  'Download &amp; build' => 'Download &amp; Build',
  'Documentation' => 'Dokumentation',
  'Practical guides' => 'Praxis-Anleitungen',
  'Projects' => 'Projekte',
  'Docker deployment' => 'Docker-Deployment',
  'Community' => 'Community',
  'Community hub' => 'Community-Hub',
  'Issue tracker' => 'Issue-Tracker',
  'Discussions' => 'Diskussionen',
  'Contributing' => 'Mitwirken',
  'GitHub organization' => 'GitHub-Organisation',
  'OpenRTMP. Released under the MIT License.' => 'OpenRTMP. Veröffentlicht unter der MIT-Lizenz.',
  'Legal Notice' => 'Impressum',
];

/** Translate a shared-include string into the current page language. */
function t(string $text): string
{
  global $lang;
  if ($lang === 'de' && isset(OPENRTMP_STRINGS_DE[$text])) {
    return OPENRTMP_STRINGS_DE[$text];
  }
  return $text;
}

/** Prefix a root-relative site path with the current language prefix. */
function lurl(string $path): string
{
  global $lang;
  return OPENRTMP_LANG_PREFIX[$lang] . $path;
}

/** Strip a language prefix, returning the language-neutral path. */
function openrtmp_neutral_path(string $path): string
{
  if ($path === '/de' || strpos($path, '/de/') === 0) {
    return substr($path, 3) ?: '/';
  }
  return $path;
}

/**
 * The URL path of every language version that exists on disk for the given
 * page path, keyed by language code.
 */
function openrtmp_alternates(string $path): array
{
  $neutral = openrtmp_neutral_path($path);
  $root = dirname(__DIR__);
  $alternates = [];
  foreach (OPENRTMP_LANG_PREFIX as $code => $prefix) {
    $localized = $prefix . $neutral;
    $file = $root . rtrim($localized, '/') . '/index.php';
    if (is_file($file)) {
      $alternates[$code] = $localized;
    }
  }
  return $alternates;
}
