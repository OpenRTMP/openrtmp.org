<?php
$lang = 'de';
$page = 'guides';
$pageTitle = 'OpenRTMP vs. MediaMTX vs. SRS — Vergleich selbst gehosteter Streaming-Server';
$pageDescription = 'OpenRTMP, MediaMTX und SRS im Vergleich für RTMP, RTMPS, Enhanced RTMP, HLS, WebRTC, SRT, Aufzeichnung, APIs, Metriken und selbst gehostete Streaming-Architekturen.';
$canonicalPath = '/de/guides/openrtmp-vs-mediamtx-vs-srs/';
$ogType = 'article';
$structuredData = [
  '@context' => 'https://schema.org',
  '@type' => 'TechArticle',
  'headline' => 'OpenRTMP vs. MediaMTX vs. SRS',
  'description' => $pageDescription,
  'inLanguage' => 'de',
  'author' => ['@type' => 'Organization', 'name' => 'OpenRTMP'],
  'publisher' => ['@type' => 'Organization', 'name' => 'OpenRTMP'],
  'mainEntityOfPage' => 'https://openrtmp.org/de/guides/openrtmp-vs-mediamtx-vs-srs/'
];
include_once __DIR__ . '/../../../includes/header.php';
?>

<main>
  <div class="page-hero container article-hero">
    <span class="eyebrow">Vergleich &middot; Medienserver &middot; Selbst gehostet</span>
    <h1>OpenRTMP vs. MediaMTX vs. SRS</h1>
    <p>Alle drei Projekte können an RTMP-Workflows teilnehmen, lösen aber unterschiedlich große Probleme. Vergleichen Sie sie nach Architektur und benötigten Protokollen, statt nach einem einzigen universellen Sieger zu suchen.</p>
  </div>

  <section class="content-section" style="padding-top: 0;">
    <div class="container article-layout">
      <article class="prose">
        <div class="callout"><strong>Auf einen Blick:</strong> OpenRTMP konzentriert sich auf RTMP/RTMPS und E-RTMP mit einer wiederverwendbaren Rust-Bibliothek und einem API-gesteuerten Server. MediaMTX ist ein kompakter Multiprotokoll-Medienrouter. SRS ist ein breiter aufgestellter Live-Streaming-Server mit umfangreichen RTMP-, WebRTC-, HLS-, SRT- und Konvertierungs-Workflows.</div>

        <h2 id="comparison">Funktions- und Architekturvergleich</h2>
        <table class="comparison-table">
          <thead><tr><th>Bereich</th><th>OpenRTMP</th><th>MediaMTX</th><th>SRS</th></tr></thead>
          <tbody>
            <tr><td>Grunddesign</td><td>RTMP/E-RTMP-Bibliothek + fokussierter Server + Panel</td><td>Multiprotokoll-Medienrouter/-Proxy</td><td>Breiter Live-Streaming- und WebRTC-Server</td></tr>
            <tr><td>RTMP / RTMPS</td><td>Ja / ja</td><td>Ja / ja</td><td>RTMP ist ein Kernprotokoll; für die genaue Topologie mit verschlüsseltem Ingest die aktuelle SRS-Doku prüfen</td></tr>
            <tr><td>Fokus auf E-RTMP</td><td>Expliziter Schwerpunkt der Protokollentwicklung in librtmp2</td><td>Unterstützt laut aktuellen Codec-Tabellen moderne Codecs in RTMP-Workflows</td><td>Die aktuelle SRS-Doku behandelt Enhanced RTMP für HEVC/AV1, die v7.0-Linie nennt zusätzlich VP9</td></tr>
            <tr><td>RTSP</td><td>Nein</td><td>Ja</td><td>Nicht der Hauptgrund für SRS; für benötigte RTSP-Workflows die aktuelle Release-Doku prüfen</td></tr>
            <tr><td>HLS</td><td>Kein integrierter HLS-Server</td><td>Ja, einschließlich HLS-Erzeugung</td><td>Ja</td></tr>
            <tr><td>WebRTC / WHIP / WHEP</td><td>Nein</td><td>Ja</td><td>Ja</td></tr>
            <tr><td>SRT</td><td>Nein</td><td>Ja</td><td>Ja</td></tr>
            <tr><td>Aufzeichnung</td><td>Kein integrierter Recorder</td><td>Integrierte Aufzeichnung/Wiedergabe</td><td>In SRS-Workflows verfügbar; den aktuellen Funktionsumfang für das Ziel-Release prüfen</td></tr>
            <tr><td>Control-API</td><td>REST-API für Stream-Verwaltung + Health/Statistiken</td><td>Control-API</td><td>HTTP-API</td></tr>
            <tr><td>Metriken / Statistiken</td><td>JSON-Statistiken + nginx-kompatibles XML; Panel-UI</td><td>Prometheus-kompatible Metriken</td><td>HTTP-API und Monitoring-Integrationen</td></tr>
            <tr><td>Authentifizierungsmodell</td><td>Getrennte Publish-, Play- und Statistik-Keys pro Stream + API-Bearer-Token</td><td>Interne, externe HTTP- oder JWT-Authentifizierung</td><td>HTTP-APIs, Callbacks und protokollspezifische Auth-Optionen je nach Workflow</td></tr>
            <tr><td>Einbettbare Protokollbibliothek</td><td>Rust-Crate + C-kompatibles FFI</td><td>Serveranwendung</td><td>Serveranwendung</td></tr>
            <tr><td>HA / Clustering</td><td>Optional OpenRaft + Media-Mesh, vor 1.0 und standardmäßig deaktiviert</td><td>Auf Routing/Proxying ausgelegt; für HA eine externe Topologie evaluieren</td><td>Offizielle Doku enthält Edge-/Origin-Cluster-Designs</td></tr>
          </tbody>
        </table>

        <h2 id="openrtmp">OpenRTMP</h2>
        <p>OpenRTMP hält das Kernproblem bewusst klein: moderne RTMP-Infrastruktur implementieren und betreiben, ohne den Server in eine universelle Medienverarbeitungs-Suite zu verwandeln.</p>
        <ul>
          <li><a href="https://github.com/OpenRTMP/librtmp2" target="_blank" rel="noopener"><code>librtmp2</code></a> enthält die Rust-Protokollimplementierung und das C-kompatible FFI.</li>
          <li><a href="https://github.com/OpenRTMP/librtmp2-server" target="_blank" rel="noopener"><code>librtmp2-server</code></a> ergänzt RTMP/RTMPS-Listener, Stream-Keys, SQLite, REST-API, Live-Statistiken und optionales Clustering.</li>
          <li><a href="https://github.com/OpenRTMP/librtmp2-server-panel" target="_blank" rel="noopener"><code>librtmp2-server-panel</code></a> bietet browserbasierte Stream-Verwaltung und Monitoring.</li>
        </ul>
        <p>Diese Architektur passt zu Projekten, in denen Authentifizierung, Routing-Policy, Speicherung, Transcoding und Auslieferung getrennte Aufgaben sind oder vom Anwendungsentwickler gesteuert werden.</p>

        <h2 id="mediamtx">MediaMTX</h2>
        <p><a href="https://mediamtx.org/docs/kickoff/introduction" target="_blank" rel="noopener">MediaMTX</a> positioniert sich als einsatzbereiter Medienserver und Proxy. Die aktuelle Dokumentation behandelt Publishing und Lesen über Protokolle wie RTSP, RTMP, HLS, WebRTC und SRT sowie Protokollkonvertierung, Aufzeichnung, Weiterleitung, Authentifizierung, eine Control-API und Prometheus-kompatible Metriken.</p>
        <p>Damit ist MediaMTX besonders nützlich, wenn Streams zwischen Protokollen wechseln müssen oder ein kleiner Server mehrere Auslieferungswege bereitstellen soll, ohne für jeden eine eigene Anwendungsschicht.</p>

        <h2 id="srs">SRS</h2>
        <p><a href="https://ossrs.io/lts/en-us/docs/v6/doc/introduction" target="_blank" rel="noopener">SRS</a> ist ein Live-Streaming-Server, der nach eigener Beschreibung RTMP, WebRTC, HLS, HTTP-FLV, HTTP-TS, SRT, MPEG-DASH und GB28181 unterstützt, mit Codec-Abdeckung für H.264, H.265, AV1, VP9, AAC, Opus und G.711. Die Dokumentation umfasst HTTP-APIs und größere Deployment-Topologien wie Edge- und Origin-Cluster.</p>
        <p>SRS veröffentlicht häufig Alpha-/Dev-Builds auf Basis seiner LTS-Zweige. <a href="https://github.com/ossrs/srs/releases/tag/v7.0-a0" target="_blank" rel="noopener">v7.0-a0 (7.0.162)</a>, erschienen Mitte September 2026, ist ein aktuelles Beispiel — überwiegend SRT-Sicherheitshärtung (ein libsrt-CVE-Fix) und Robustheit beim Parsen von RTMP/WebRTC/Codecs statt neuer Protokollfunktionen. Betrachten Sie jeden Vergleich als bewegliches Ziel und prüfen Sie das Release, das Sie einsetzen möchten, statt einer festen Momentaufnahme.</p>
        <p>SRS lohnt eine Evaluierung, wenn RTMP-Ingest nur ein Teil einer breiteren Streaming-Plattform ist und dasselbe Projekt Brücken zu Browser- oder HTTP-Auslieferungswegen schlagen soll.</p>

        <h2 id="use-cases">Welche Architektur passt zu welchem Einsatzzweck?</h2>
        <table>
          <thead><tr><th>Anforderung</th><th>Näher zu evaluierendes Projekt</th><th>Warum</th></tr></thead>
          <tbody>
            <tr><td>Eigenen RTMP-Server/-Client/-Gateway in Rust bauen</td><td>OpenRTMP</td><td>Die Protokollimplementierung ist als einbettbare Crate und FFI-Bibliothek verfügbar</td></tr>
            <tr><td>Privater RTMP/RTMPS-Endpunkt mit Stream-Keys, API und Browser-Panel</td><td>OpenRTMP</td><td>Das sind die Kernaufgaben von Server und Panel</td></tr>
            <tr><td>RTSP, RTMP, HLS, WebRTC und SRT über einen kompakten Dienst routen</td><td>MediaMTX</td><td>Multiprotokoll-Routing steht im Zentrum seines Designs</td></tr>
            <tr><td>Integrierte Aufzeichnung und Wiedergabe im Routing-Server</td><td>MediaMTX</td><td>Aufzeichnung und Wiedergabe sind dokumentierte Kernfunktionen</td></tr>
            <tr><td>RTMP-Ingest plus HLS/WebRTC/SRT-Konvertierung in einem größeren Streaming-Server</td><td>SRS</td><td>Protokollkonvertierung und breite Live-Streaming-Workflows stehen im Zentrum von SRS</td></tr>
            <tr><td>Forschung an Protokollimplementierungen rund um E-RTMP in Rust</td><td>OpenRTMP</td><td>Das Projekt stellt die Protokollschicht direkt bereit, nicht nur über ein Server-Binary</td></tr>
          </tbody>
        </table>

        <h2 id="modern-codecs">HEVC und AV1 sind eine End-to-End-Frage</h2>
        <p>Unterstützung moderner Codecs sollte nie auf ein Häkchen in einer Vergleichstabelle reduziert werden. Prüfen Sie Sender, Paketformat des Servers, jede Protokollkonvertierung, Empfänger und Decoder. MediaMTX dokumentiert AV1/H.265 unter den unterstützten RTMP-Codecs, SRS dokumentiert Enhanced-RTMP-Arbeiten zu HEVC/AV1 und nennt inzwischen auch VP9, und OpenRTMP implementiert E-RTMP-Parsing/-Relay, wobei der genaue Stand in <code>librtmp2</code> gepflegt wird.</p>
        <p>Für OpenRTMP-spezifische Tests siehe <a href="/de/guides/hevc-streaming-obs/">HEVC mit OBS</a> und <a href="/de/guides/av1-over-rtmp/">AV1 über RTMP</a>.</p>

        <h2 id="benchmarks">Nicht allein nach Funktionstabellen entscheiden</h2>
        <p>Betreiben Sie die Kandidaten unter der Last, die für Sie wirklich zählt. Sinnvolle Messungen:</p>
        <ul>
          <li>Verbindungs- und Reconnect-Verhalten des Publishers.</li>
          <li>Zeit bis zum ersten dekodierbaren Frame für einen neuen Player.</li>
          <li>Verhalten bei spätem Player-Beitritt.</li>
          <li>CPU und Speicher mit Ihrem Codec, Ihrer Bitrate und Ihrer Client-Anzahl.</li>
          <li>Fehlerverhalten nach Server-Neustart oder Ausfall des Upstream-Netzwerks.</li>
          <li>Betriebsaufwand für Authentifizierung und Secret-Rotation.</li>
          <li>Qualität von Metriken, Logs und Debugging während eines echten Vorfalls.</li>
        </ul>

        <h2 id="sources">Aktuelle Upstream-Dokumentation prüfen</h2>
        <p>Für Vergleiche mit kommerziellen oder WebRTC-orientierten Produkten siehe <a href="/de/guides/openrtmp-vs-wowza/">OpenRTMP vs. Wowza</a> und <a href="/de/guides/openrtmp-vs-ant-media-server/">OpenRTMP vs. Ant Media Server</a>.</p>
        <p>Alle drei Projekte werden aktiv entwickelt. Prüfen Sie Funktionen vor einer Architekturentscheidung anhand der aktuellen Upstream-Dokumentation, statt sich auf einen Vergleichsartikel zu verlassen, der veralten kann:</p>
        <ul>
          <li><a href="https://github.com/OpenRTMP" target="_blank" rel="noopener">OpenRTMP auf GitHub</a></li>
          <li><a href="https://mediamtx.org/docs/kickoff/introduction" target="_blank" rel="noopener">MediaMTX-Dokumentation</a></li>
          <li><a href="https://ossrs.io/" target="_blank" rel="noopener">SRS-Dokumentation</a></li>
        </ul>

        <div class="cta compact-cta">
          <h2>OpenRTMP neben Ihrem bestehenden Stack ausprobieren</h2>
          <p>Der Docker-Schnellstart ist bewusst so isoliert, dass Sie ihn evaluieren können, ohne zuerst einen bestehenden Medienserver zu ersetzen.</p>
          <div class="hero-actions" style="margin-bottom:0;">
            <a href="/de/quickstart/" class="btn btn-primary">Schnellstart in fünf Minuten</a>
            <a href="/de/guides/nginx-rtmp-alternatives/" class="btn btn-ghost">Alternativen zu nginx-rtmp</a>
          </div>
        </div>
      </article>

      <aside class="toc-card" aria-label="Auf dieser Seite">
        <strong>Auf dieser Seite</strong>
        <a href="#comparison">Vergleich</a>
        <a href="#openrtmp">OpenRTMP</a>
        <a href="#mediamtx">MediaMTX</a>
        <a href="#srs">SRS</a>
        <a href="#use-cases">Einsatzzwecke</a>
        <a href="#modern-codecs">Moderne Codecs</a>
        <a href="#benchmarks">Benchmarking</a>
        <a href="#sources">Upstream-Doku</a>
      </aside>
    </div>
  </section>
</main>

<?php include_once __DIR__ . '/../../../includes/footer.php'; ?>
