<?php
$lang = 'de';
$page = 'guides';
$pageTitle = 'OpenRTMP-Anleitungen — RTMP, Enhanced RTMP, OBS, Docker, Rust und Server-Vergleiche';
$pageDescription = 'Praxisnahe OpenRTMP-Anleitungen zu selbst gehostetem RTMP, OBS, RTMPS, HEVC, AV1, Enhanced RTMP v2, Rust-Entwicklung, Clustering, NOALBS und Medienserver-Vergleichen.';
$canonicalPath = '/de/guides/';
include_once __DIR__ . '/../../includes/header.php';
?>

<main>
  <div class="page-hero container">
    <span class="eyebrow">Praxis-Anleitungen</span>
    <h1>RTMP-Infrastruktur aufbauen, verstehen und betreiben</h1>
    <p>Aufgabenorientierte Artikel für Stream-Betreiber, Anwendungsentwickler und Protokoll-Mitwirkende. Die Anleitungen unterscheiden zwischen Protokollfähigkeit und vollständiger End-to-End-Unterstützung und benennen die aktuellen Grenzen vor Version 1.0 ausdrücklich.</p>
  </div>

  <section style="padding-top: 0;">
    <div class="container">
      <div class="grid-2 guide-grid">
        <article class="card guide-card">
          <span class="guide-tag">Docker &middot; OBS &middot; Selbst gehostet</span>
          <h2><a href="/de/guides/self-hosted-rtmp-server-docker/">Eigenen RTMP-Server in fünf Minuten betreiben</a></h2>
          <p>OpenRTMP-Server und Web-Panel mit Docker bereitstellen, Stream-Keys anlegen, aus OBS streamen und den Stack für einen Host mit Internetzugang vorbereiten.</p>
          <a href="/de/guides/self-hosted-rtmp-server-docker/" class="text-link">Zur Deployment-Anleitung &rarr;</a>
        </article>

        <article class="card guide-card">
          <span class="guide-tag">RTMP &middot; E-RTMP &middot; Protokoll</span>
          <h2><a href="/de/guides/rtmp-vs-enhanced-rtmp/">RTMP vs. Enhanced RTMP</a></h2>
          <p>Was E-RTMP gegenüber klassischem RTMP ergänzt: FourCC-Codec-Signalisierung, moderne Codecs, Multitrack, Reconnect-Signalisierung und Kompatibilität.</p>
          <a href="/de/guides/rtmp-vs-enhanced-rtmp/" class="text-link">RTMP und E-RTMP vergleichen &rarr;</a>
        </article>

        <article class="card guide-card">
          <span class="guide-tag">E-RTMP v2 &middot; Protokoll</span>
          <h2><a href="/de/guides/enhanced-rtmp-v2-explained/">Enhanced RTMP v2 erklärt</a></h2>
          <p>FourCC-Signalisierung, moderne Audio-/Video-Codecs, Multitrack, Reconnect-Anfragen, Timing-Verbesserungen und Implementierungsgrenzen im Überblick.</p>
          <a href="/de/guides/enhanced-rtmp-v2-explained/" class="text-link">Zur v2-Erklärung &rarr;</a>
        </article>

        <article class="card guide-card">
          <span class="guide-tag">E-RTMP &middot; HEVC &middot; AV1 &middot; Opus</span>
          <h2><a href="/de/guides/enhanced-rtmp-hevc-av1-opus/">Enhanced-RTMP-Codecs erklärt</a></h2>
          <p>Warum es Enhanced RTMP gibt, wie sich moderne Codec-Signalisierung von klassischem FLV unterscheidet und welche OpenRTMP-Pfade vollständig bzw. experimentell sind.</p>
          <a href="/de/guides/enhanced-rtmp-hevc-av1-opus/" class="text-link">Zur Codec-Anleitung &rarr;</a>
        </article>

        <article class="card guide-card">
          <span class="guide-tag">OBS &middot; HEVC &middot; E-RTMP</span>
          <h2><a href="/de/guides/hevc-streaming-obs/">HEVC-Streaming mit OBS</a></h2>
          <p>Eine HEVC-fähige OBS-Ausgabe konfigurieren, über Enhanced RTMP senden, den Codec bei OpenRTMP prüfen und Player-Kompatibilität untersuchen.</p>
          <a href="/de/guides/hevc-streaming-obs/" class="text-link">HEVC-Streaming einrichten &rarr;</a>
        </article>

        <article class="card guide-card">
          <span class="guide-tag">AV1 &middot; OBS &middot; E-RTMP</span>
          <h2><a href="/de/guides/av1-over-rtmp/">AV1 über RTMP</a></h2>
          <p>Wie AV1 über E-RTMP signalisiert wird, wie Sie es aus OBS testen, den OpenRTMP-Pfad prüfen und Probleme bei Encoder, Server und Decoder eingrenzen.</p>
          <a href="/de/guides/av1-over-rtmp/" class="text-link">AV1 über RTMP testen &rarr;</a>
        </article>

        <article class="card guide-card">
          <span class="guide-tag">Sicherheit &middot; TLS &middot; OBS</span>
          <h2><a href="/de/guides/rtmps-server-obs/">RTMPS-Server für OBS einrichten</a></h2>
          <p>Verschlüsseltes RTMPS neben unverschlüsseltem RTMP betreiben, Zertifikate konfigurieren, den richtigen Port freigeben und den Server prüfen, bevor Sie die OBS-Ingest-Einstellungen ändern.</p>
          <a href="/de/guides/rtmps-server-obs/" class="text-link">Zur RTMPS-Anleitung &rarr;</a>
        </article>

        <article class="card guide-card">
          <span class="guide-tag">Rust &middot; Entwickler &middot; Bibliothek</span>
          <h2><a href="/de/guides/rtmp-server-rust/">RTMP-Server in Rust bauen</a></h2>
          <p>librtmp2 für einen eigenen Server, ein Relay, Gateway oder einen Client nutzen und verstehen, wo die Protokollbibliothek endet und Ihre Anwendungsarchitektur beginnt.</p>
          <a href="/de/guides/rtmp-server-rust/" class="text-link">Zur Rust-Entwickleranleitung &rarr;</a>
        </article>

        <article class="card guide-card">
          <span class="guide-tag">Vergleich &middot; Migration</span>
          <h2><a href="/de/guides/openrtmp-vs-nginx-rtmp/">OpenRTMP vs. nginx-rtmp</a></h2>
          <p>Architektur, Deployment, Statistik-Kompatibilität und fehlende Funktionen im Vergleich – und in welchen Szenarien welches Design besser passt.</p>
          <a href="/de/guides/openrtmp-vs-nginx-rtmp/" class="text-link">Zum direkten Vergleich &rarr;</a>
        </article>

        <article class="card guide-card">
          <span class="guide-tag">Alternativen &middot; Selbst gehostet</span>
          <h2><a href="/de/guides/nginx-rtmp-alternatives/">Alternativen zu nginx-rtmp</a></h2>
          <p>nginx-rtmp im Vergleich mit OpenRTMP, MediaMTX und SRS nach Protokollabdeckung, Steuerungsmodell, Aufzeichnung, Auslieferungsfunktionen und Migrationszielen.</p>
          <a href="/de/guides/nginx-rtmp-alternatives/" class="text-link">Alternativen erkunden &rarr;</a>
        </article>

        <article class="card guide-card">
          <span class="guide-tag">OpenRTMP &middot; MediaMTX &middot; SRS</span>
          <h2><a href="/de/guides/openrtmp-vs-mediamtx-vs-srs/">OpenRTMP vs. MediaMTX vs. SRS</a></h2>
          <p>Ein fokussierter RTMP/E-RTMP-Stack, ein Multiprotokoll-Medienrouter und ein breiter aufgestellter Live-Streaming-Server im Vergleich anhand aktueller Upstream-Funktionen.</p>
          <a href="/de/guides/openrtmp-vs-mediamtx-vs-srs/" class="text-link">Zum Medienserver-Vergleich &rarr;</a>
        </article>

        <article class="card guide-card">
          <span class="guide-tag">Vergleich &middot; Kommerziell</span>
          <h2><a href="/de/guides/openrtmp-vs-wowza/">OpenRTMP vs. Wowza Streaming Engine</a></h2>
          <p>Ein kostenloser, selbst gehosteter Rust-RTMP-Stack im Vergleich mit einem kostenpflichtigen kommerziellen Medienserver mit integriertem Transcoding, DRM und Herstellersupport.</p>
          <a href="/de/guides/openrtmp-vs-wowza/" class="text-link">Open Source vs. kommerziell vergleichen &rarr;</a>
        </article>

        <article class="card guide-card">
          <span class="guide-tag">Vergleich &middot; WebRTC</span>
          <h2><a href="/de/guides/openrtmp-vs-ant-media-server/">OpenRTMP vs. Ant Media Server</a></h2>
          <p>Eine fokussierte RTMP/E-RTMP-Bibliothek samt Server im Vergleich mit einem WebRTC-orientierten Medienserver mit Multiprotokoll-Ingest und Community-/Enterprise-Editionen.</p>
          <a href="/de/guides/openrtmp-vs-ant-media-server/" class="text-link">Zum WebRTC-Vergleich &rarr;</a>
        </article>

        <article class="card guide-card">
          <span class="guide-tag">HA &middot; Clustering &middot; OpenRaft</span>
          <h2><a href="/de/guides/rtmp-server-ha-clustering/">HA-RTMP-Server-Cluster betreiben</a></h2>
          <p>Optionales Multi-Node-Clustering aktivieren, Voter bootstrappen und hinzufügen, Control-/Media-Ports freigeben und Nodes über API oder Panel betreiben.</p>
          <a href="/de/guides/rtmp-server-ha-clustering/" class="text-link">Zur Clustering-Anleitung &rarr;</a>
        </article>

        <article class="card guide-card">
          <span class="guide-tag">NOALBS &middot; JSON &middot; Monitoring</span>
          <h2><a href="/de/guides/openrtmp-noalbs-json-stats/">NOALBS mit OpenRTMP-Statistiken verbinden</a></h2>
          <p>Den nativen OpenRTMP-Provider konfigurieren, Bitrate- und RTT-Werte verstehen, den XML-Fallback nutzen und Verbindungsprobleme mit Containern oder Proxys beheben.</p>
          <a href="/de/guides/openrtmp-noalbs-json-stats/" class="text-link">Zur Statistik-Anleitung &rarr;</a>
        </article>
      </div>

      <div class="cta" style="margin-top: 48px;">
        <h2>Lieber direkt einrichten?</h2>
        <p>Der Schnellstart führt Sie in wenigen Schritten von einem leeren Docker-Host zu einem OBS-fähigen Stream.</p>
        <div class="hero-actions" style="margin-bottom:0;">
          <a href="/de/quickstart/" class="btn btn-primary">Schnellstart öffnen</a>
          <a href="/de/docs/" class="btn btn-ghost">Referenzdoku ansehen</a>
        </div>
      </div>
    </div>
  </section>
</main>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>
