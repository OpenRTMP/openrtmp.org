<?php
$lang = 'de';
$page = 'home';
$pageTitle = 'OpenRTMP — Rust-RTMP/E-RTMP-Bibliothek und selbst gehosteter Server';
$pageDescription = 'Entwickeln Sie RTMP/E-RTMP-Anwendungen mit Rust oder betreiben Sie einen privaten RTMP/RTMPS-Server mit Docker, Stream-Key-Authentifizierung, optionalem Multi-Node-Clustering, Live-Statistiken, REST-API und Web-Panel.';
$canonicalPath = '/de/';
$structuredData = [
  '@context' => 'https://schema.org',
  '@graph' => [
    [
      '@type' => 'SoftwareSourceCode',
      'name' => 'librtmp2',
      'description' => 'Rust-Protokollbibliothek für RTMP/RTMPS und Enhanced RTMP mit C-kompatiblem FFI.',
      'codeRepository' => 'https://github.com/OpenRTMP/librtmp2',
      'programmingLanguage' => 'Rust',
      'license' => 'https://opensource.org/license/mit',
      'inLanguage' => 'de'
    ],
    [
      '@type' => 'SoftwareApplication',
      'name' => 'OpenRTMP Server and Panel',
      'applicationCategory' => 'DeveloperApplication',
      'operatingSystem' => 'Linux, Docker',
      'description' => 'Selbst gehosteter RTMP/RTMPS-Server mit REST-API, Stream-Keys, optionalem HA-Clustering, Live-Statistiken und Web-Control-Panel.',
      'url' => 'https://openrtmp.org/de/quickstart/',
      'inLanguage' => 'de'
    ]
  ]
];
include_once __DIR__ . '/../includes/header.php';
?>

<main>

  <section class="hero">
    <div class="container">
      <span class="eyebrow">Aktive Entwicklung &middot; Vor 1.0 &middot; Rust &middot; Docker &middot; MIT</span>
      <h1>Moderne RTMP-Infrastruktur<br><span class="gradient">für Entwickler und Betreiber.</span></h1>
      <p>
        Binden Sie eine fokussierte RTMP/E-RTMP-Protokollbibliothek in Ihre eigene Anwendung ein
        oder betreiben Sie einen privaten RTMP/RTMPS-Server mit Stream-Keys, REST-API,
        Live-Statistiken und einem browserbasierten Control-Panel.
      </p>
      <div class="hero-actions">
        <a href="/de/quickstart/" class="btn btn-primary">Docker-Stack starten</a>
        <a href="https://docs.rs/librtmp2" target="_blank" rel="noopener" class="btn btn-ghost">Rust-Crate nutzen</a>
        <a href="/de/docs/" class="btn btn-ghost">Doku lesen</a>
      </div>
      <div class="hero-stats">
        <div class="stat"><strong>RTMP/S</strong><span>Publishing und Wiedergabe</span></div>
        <div class="stat"><strong>HEVC / AV1</strong><span>Enhanced-Media-Passthrough</span></div>
        <div class="stat"><strong>Rust + C</strong><span>native API und FFI</span></div>
        <div class="stat"><strong>MIT</strong><span>kommerziell nutzbare Lizenz</span></div>
      </div>
    </div>
  </section>

  <section style="padding-top: 24px;" id="paths">
    <div class="container">
      <div class="section-head">
        <span class="eyebrow">Wählen Sie Ihren Weg</span>
        <h2>Beginnen Sie mit dem Teil, den Sie wirklich brauchen</h2>
        <p>Die Bibliothek und der einsatzfertige Server sind getrennte Projekte mit unterschiedlichen Zielgruppen, Installationswegen und Stabilitätsgrenzen.</p>
      </div>
      <div class="grid-2">
        <article class="card path-card">
          <div class="icon">&#128736;&#65039;</div>
          <h3>Mit der Protokollbibliothek entwickeln</h3>
          <p>Nutzen Sie <code>librtmp2</code> in einem Rust-Server, Relay, Plugin, Gateway oder Protokollexperiment. Die Crate bietet RTMP/RTMPS-Session-Handling, Parser-Module, Relay-Primitive und ein C-kompatibles FFI.</p>
          <ul class="check-list">
            <li>Rust-Crate plus <code>cdylib</code> und <code>staticlib</code></li>
            <li>Publish- und Play-Workflows im Stil von OBS/FFmpeg</li>
            <li>Bausteine für klassisches RTMP und Enhanced RTMP</li>
            <li>Ihre Anwendung verantwortet Authentifizierung, Routing und Medien-Policy</li>
          </ul>
          <div class="card-actions">
            <a href="https://github.com/OpenRTMP/librtmp2" target="_blank" rel="noopener" class="btn btn-primary">librtmp2 ansehen</a>
            <a href="https://github.com/OpenRTMP/librtmp2#implementation-status" target="_blank" rel="noopener" class="btn btn-ghost">Implementierungsstand</a>
          </div>
        </article>

        <article class="card path-card">
          <div class="icon">&#128225;</div>
          <h3>Einen eigenen Streaming-Server betreiben</h3>
          <p>Setzen Sie <code>librtmp2-server</code> und sein Panel ein, wenn Sie einen privaten RTMP-Endpunkt möchten, statt die Anwendungsschicht selbst zu entwickeln.</p>
          <ul class="check-list">
            <li>RTMP- und optionale RTMPS-Listener</li>
            <li>Publish-, Play- und Statistik-Keys pro Stream</li>
            <li>SQLite-Persistenz und REST-API mit Bearer-Authentifizierung</li>
            <li>Optionales Multi-Node-HA-Clustering (standardmäßig aus)</li>
            <li>JSON- und nginx-kompatible XML-Statistiken</li>
            <li>Web-Panel zum Anlegen von Streams und für Live-Monitoring</li>
          </ul>
          <div class="card-actions">
            <a href="/de/quickstart/" class="btn btn-primary">Schnellstart in fünf Minuten</a>
            <a href="/de/guides/self-hosted-rtmp-server-docker/" class="btn btn-ghost">Deployment-Anleitung</a>
          </div>
        </article>
      </div>
    </div>
  </section>

  <section id="quick-preview">
    <div class="container">
      <div class="section-head">
        <span class="eyebrow">Schnell ausprobieren</span>
        <h2>Server, Panel und Redis aus veröffentlichten Images</h2>
        <p>Die eigenständige Compose-Datei benötigt weder eine Rust-Toolchain noch benachbarte Quell-Repositories.</p>
      </div>
      <div class="code-panel">
        <div class="code-panel-head">
          <div class="traffic"><span></span><span></span><span></span></div>
          <span class="filename">terminal</span>
        </div>
        <pre><code>git clone https://github.com/OpenRTMP/librtmp2-server-panel.git
cd librtmp2-server-panel

# Die drei benötigten Secrets wie im Schnellstart gezeigt erzeugen.
docker compose -f compose.quickstart.yml up -d

# Panel: http://localhost:8000
# API:   http://localhost:8080/api/v1/health
# RTMP:  rtmp://localhost:1935/live</code></pre>
      </div>
      <div class="center-link"><a href="/de/quickstart/" class="btn btn-primary">Vollständige Copy-and-Paste-Anleitung öffnen</a></div>
    </div>
  </section>

  <section id="features">
    <div class="container">
      <div class="section-head">
        <span class="eyebrow">Warum OpenRTMP</span>
        <h2>Ein fokussiertes RTMP-Ökosystem</h2>
        <p>OpenRTMP bleibt bewusst schlanker als All-in-one-Medienplattformen, damit jede Komponente verständlich, wiederverwendbar und testbar bleibt.</p>
      </div>
      <div class="grid">
        <div class="card">
          <div class="icon">&#128274;</div>
          <h3>Defensive Protokollverarbeitung</h3>
          <p>Vom Netzwerk gelieferte Längen und der Parser-Zustand werden validiert; Tests und Fuzz-Targets decken protokollkritische Codepfade ab.</p>
        </div>
        <div class="card">
          <div class="icon">&#127909;</div>
          <h3>Moderne Codec-Workflows</h3>
          <p>Enhanced-RTMP-Medien können in unterstützten OBS/FFmpeg-Workflows HEVC, AV1 und Opus transportieren. Die genaue Unterstützung im Live-Pfad ist pro Release dokumentiert.</p>
        </div>
        <div class="card">
          <div class="icon">&#128268;</div>
          <h3>RTMP und RTMPS gemeinsam</h3>
          <p>Der Server kann unverschlüsseltes RTMP und zusätzlich einen TLS-Listener bereitstellen und dabei eine gemeinsame Stream-Registry und denselben Relay-Kern nutzen.</p>
        </div>
        <div class="card">
          <div class="icon">&#128272;</div>
          <h3>Standardmäßig privat</h3>
          <p>Getrennte Publish-, Wiedergabe- und Statistik-Keys vermeiden ein öffentliches Stream-Verzeichnis und erlauben es, jeder Integration nur den nötigen Zugriff zu geben.</p>
        </div>
        <div class="card">
          <div class="icon">&#128202;</div>
          <h3>Monitoring-freundlich</h3>
          <p>Nutzen Sie moderne JSON-Statistiken oder nginx-rtmp-kompatibles XML für bestehende Monitoring- und Automatisierungswerkzeuge.</p>
        </div>
        <div class="card">
          <div class="icon">&#128421;&#65039;</div>
          <h3>Optionales HA-Clustering</h3>
          <p>Betreiben Sie mehrere Server-Nodes mit repliziertem Stream-Zustand und einem Media-Mesh für die Wiedergabe über Nodes hinweg. Der Standalone-Modus bleibt der Standard.</p>
        </div>
        <div class="card">
          <div class="icon">&#129513;</div>
          <h3>Kombinierbare Projekte</h3>
          <p>Nutzen Sie nur die Crate, betreiben Sie nur den Server, ergänzen Sie das Panel oder binden Sie die REST-API in Ihre eigene Control-Plane ein.</p>
        </div>
      </div>
    </div>
  </section>

  <section id="architecture">
    <div class="container">
      <div class="section-head">
        <span class="eyebrow">Architektur</span>
        <h2>Protokoll, Anwendungsschicht und UI bleiben getrennt</h2>
        <p>Diese Trennung macht klar, welches Projekt Sie einsetzen und wo Sie beitragen können.</p>
      </div>
      <div class="stack">
        <div class="stack-row"><span class="layer-name">OpenRTMP Control Panel<br><code>librtmp2-server-panel</code></span><span class="layer-desc">Browser-UI für den Stream-Lebenszyklus, kopierbare URLs, Keys, Live-Statistiken und optionale Verwaltung von Cluster-Nodes</span></div>
        <div class="stack-arrow">&#8595;</div>
        <div class="stack-row"><span class="layer-name">OpenRTMP Server<br><code>librtmp2-server</code></span><span class="layer-desc">REST-API, Authentifizierung, SQLite, Statistiken, Listener, Stream-Registry und optionales HA-Clustering</span></div>
        <div class="stack-arrow">&#8595;</div>
        <div class="stack-row"><span class="layer-name">OpenRTMP-Protokollbibliothek<br><code>librtmp2</code></span><span class="layer-desc">RTMP/RTMPS-Verbindung, Handshake, Chunking, AMF-Befehle, Relay-Primitive und E-RTMP-Module</span></div>
        <div class="stack-arrow">&#8595;</div>
        <div class="stack-row"><span class="layer-name">Clients</span><span class="layer-desc">OBS, FFmpeg, eigene Publisher, Player, Relays und eingebettete Anwendungen</span></div>
      </div>
    </div>
  </section>

  <section id="scope">
    <div class="container">
      <div class="section-head">
        <span class="eyebrow">Ehrlicher Umfang</span>
        <h2>Was OpenRTMP ist — und was nicht</h2>
        <p>Klare Grenzen helfen Betreibern bei der Wahl des richtigen Werkzeugs und Mitwirkenden, sich auf die wichtigsten Lücken zu konzentrieren.</p>
      </div>
      <div class="grid-2">
        <div class="card">
          <h3>Heute gut geeignet für</h3>
          <ul class="check-list">
            <li>Privaten RTMP/RTMPS-Ingest und Wiedergabe</li>
            <li>Eigene RTMP-Anwendungen in Rust oder über FFI</li>
            <li>Integrationstests mit OBS/FFmpeg</li>
            <li>Werkzeuge rund um Stream-Keys und Statistiken</li>
            <li>Evaluierung von Multi-Node-HA mit optionalem Clustering</li>
            <li>Protokollforschung und Mitarbeit</li>
          </ul>
        </div>
        <div class="card">
          <h3>Nutzen Sie eine andere Plattform, wenn Sie brauchen</h3>
          <ul class="check-list muted-list">
            <li>Eine schlüsselfertige öffentliche Videoplattform mit Zuschauer-Website</li>
            <li>Integriertes HLS, Aufzeichnung, Transcoding oder Push-Relay</li>
            <li>Breites Multiprotokoll-Routing über RTMP hinaus</li>
            <li>Schon heute eine garantiert stabile 1.0-API</li>
          </ul>
        </div>
      </div>
    </div>
  </section>

  <section id="ecosystem">
    <div class="container">
      <div class="section-head">
        <span class="eyebrow">OpenRTMP-Projekte</span>
        <h2>Eine Komponente oder den kompletten Stack einsetzen</h2>
      </div>
      <div class="grid">
        <div class="card">
          <div class="icon">&#128230;</div>
          <h3>OpenRTMP-Protokollbibliothek</h3>
          <p><code>librtmp2</code> — Rust-Protokollbibliothek und C-kompatibles FFI für eigene Server, Clients, Relays, Plugins und Forschung.</p>
          <p class="card-link"><a href="https://github.com/OpenRTMP/librtmp2" target="_blank" rel="noopener">Repository &rarr;</a></p>
        </div>
        <div class="card">
          <div class="icon">&#128225;</div>
          <h3>OpenRTMP Server</h3>
          <p><code>librtmp2-server</code> — RTMP/RTMPS-Anwendungsschicht mit SQLite, Keys, REST-API, Monitoring-Endpunkten und optionalem HA-Clustering.</p>
          <p class="card-link"><a href="https://github.com/OpenRTMP/librtmp2-server" target="_blank" rel="noopener">Repository &rarr;</a></p>
        </div>
        <div class="card">
          <div class="icon">&#127912;</div>
          <h3>OpenRTMP Control Panel</h3>
          <p><code>librtmp2-server-panel</code> — Flask-Web-UI zum Anlegen von Streams, Kopieren von URLs, Überwachen von Live-Statistiken und Verwalten von Cluster-Nodes bei aktiviertem HA.</p>
          <p class="card-link"><a href="https://github.com/OpenRTMP/librtmp2-server-panel" target="_blank" rel="noopener">Repository &rarr;</a></p>
        </div>
        <div class="card">
          <div class="icon">&#128230;</div>
          <h3>OpenRTMP-Pakete</h3>
          <p><code>packages</code> — offizielle Paket-Repositories und Distributionsautomatisierung für unterstützte Linux-Distributionen, macOS und Windows.</p>
          <p class="card-link"><a href="https://github.com/OpenRTMP/packages" target="_blank" rel="noopener">Repository &rarr;</a></p>
        </div>
      </div>
    </div>
  </section>

  <section>
    <div class="container">
      <div class="cta">
        <h2>Stack ausprobieren, dann die Integrationstiefe wählen</h2>
        <p>Beginnen Sie mit Docker, um den Workflow kennenzulernen, oder gehen Sie direkt zur Crate, wenn Sie Ihre eigene Anwendungsschicht bauen.</p>
        <div class="hero-actions" style="margin-bottom:0;">
          <a href="/de/quickstart/" class="btn btn-primary">OpenRTMP lokal starten</a>
          <a href="/de/guides/" class="btn btn-ghost">Praxis-Anleitungen ansehen</a>
          <a href="https://github.com/OpenRTMP" target="_blank" rel="noopener" class="btn btn-ghost">Projekte mit Stern markieren</a>
        </div>
      </div>
    </div>
  </section>

</main>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
