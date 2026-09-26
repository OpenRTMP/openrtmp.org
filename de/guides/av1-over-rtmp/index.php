<?php
$lang = 'de';
$page = 'guides';
$pageTitle = 'AV1 über RTMP: Streaming mit Enhanced RTMP und OBS — OpenRTMP';
$pageDescription = 'Wie AV1 über Enhanced RTMP transportiert wird, wie Sie AV1-Streaming von OBS zu OpenRTMP testen und wie Sie Kompatibilitätsprobleme bei Encoder, Server und Player eingrenzen.';
$canonicalPath = '/de/guides/av1-over-rtmp/';
$ogType = 'article';
$structuredData = [
  '@context' => 'https://schema.org',
  '@type' => 'TechArticle',
  'headline' => 'AV1 über RTMP mit Enhanced RTMP und OBS',
  'description' => $pageDescription,
  'inLanguage' => 'de',
  'author' => ['@type' => 'Organization', 'name' => 'OpenRTMP'],
  'publisher' => ['@type' => 'Organization', 'name' => 'OpenRTMP'],
  'mainEntityOfPage' => 'https://openrtmp.org/de/guides/av1-over-rtmp/'
];
include_once __DIR__ . '/../../../includes/header.php';
?>

<main>
  <div class="page-hero container article-hero">
    <span class="eyebrow">AV1 &middot; E-RTMP &middot; OBS</span>
    <h1>AV1 über RTMP</h1>
    <p>AV1-Streaming über RTMP ist mit Enhanced RTMP möglich: Es ergänzt moderne FourCC-basierte Codec-Signalisierung und behält dabei das Session- und Transportmodell von RTMP bei.</p>
  </div>

  <section class="content-section" style="padding-top: 0;">
    <div class="container article-layout">
      <article class="prose">
        <div class="callout"><strong>Die Kernidee:</strong> „AV1 über RTMP“ heißt nicht, dass klassisches FLV AV1 als H.264 ausgibt. E-RTMP definiert explizite moderne Codec-Signalisierung, damit ein E-RTMP-fähiger Sender und Server AV1 korrekt erkennen.</div>

        <h2 id="support">Woher die Unterstützung für AV1 über RTMP kommt</h2>
        <p>Die Enhanced-RTMP-Spezifikation von Veovera definiert moderne Video-Codec-Signalisierung einschließlich AV1. OBS Studio hat mit Version 29.1 AV1- und HEVC-Streaming über Enhanced RTMP eingeführt und diesen Workflow damit in einem verbreiteten Live-Encoder verfügbar gemacht.</p>
        <p>Server- und Player-Unterstützung sind eigene Fragen. Jede Komponente muss das erweiterte Paketformat verstehen, und der Empfänger braucht außerdem einen AV1-Decoder, der zu Profil und Leistungsanforderungen des Streams passt.</p>

        <h2 id="requirements">Voraussetzungen</h2>
        <ul>
          <li>Eine aktuelle OBS-Studio-Version oder ein anderer E-RTMP-fähiger Publisher.</li>
          <li>Ein für die Publishing-Anwendung verfügbarer AV1-Encoder.</li>
          <li>Ein E-RTMP-fähiger Serverpfad.</li>
          <li>Ein nachgelagerter Player oder Dienst, der AV1 im resultierenden RTMP-Workflow unterstützt.</li>
        </ul>
        <p>Die Verfügbarkeit von AV1-Hardware-Encoding hängt von GPU-Generation und Plattform ab. Software-Encoding kann möglich sein, ist aber deutlich CPU-intensiver; prüfen Sie Echtzeitleistung daher getrennt von der Protokollkompatibilität.</p>

        <h2 id="openrtmp">AV1 mit OpenRTMP testen</h2>
        <p>Starten Sie den <a href="/de/quickstart/">OpenRTMP-Schnellstart</a>, legen Sie im Panel einen Stream an und prüfen Sie zunächst exakt denselben Pfad mit H.264. Wenn die Basislinie stabil ist, stellen Sie nur den Video-Encoder auf AV1 um.</p>
        <pre><code># Basislinie:
OBS (H.264) -> OpenRTMP -> bewährter Player

# Erweiterter Test:
OBS (AV1 / E-RTMP) -> OpenRTMP -> AV1-fähiger Player</code></pre>
        <p>So trennen Sie Codec-Signalisierung und Dekodierung von unabhängigen Firewall-, Authentifizierungs- und Stream-Key-Problemen.</p>

        <h2 id="obs">OBS konfigurieren</h2>
        <ol>
          <li>Richten Sie OpenRTMP als RTMP-Endpunkt ein und verwenden Sie den erzeugten <code>publish_key</code>.</li>
          <li>Öffnen Sie die Einstellungen der Streaming-Ausgabe.</li>
          <li>Wählen Sie einen AV1-Encoder, sofern die aktuelle OBS-Version, die Hardware und die Dienst-/Ausgabekonfiguration einen anbieten.</li>
          <li>Verwenden Sie Auflösung, Bildrate und Bitrate, die Ihr Encoder ohne Überlastung halten kann.</li>
          <li>Starten Sie den Stream und prüfen Sie Statistiken und Logs von OpenRTMP.</li>
        </ol>
        <p>Bietet OBS für die gewählte Streaming-Ausgabe kein AV1 an, prüfen Sie zuerst Encoder und Dienstfähigkeiten. Der Medienserver kann keine AV1-Pakete erzeugen, solange der Publisher noch H.264 sendet.</p>

        <h2 id="verify">Was zu prüfen ist</h2>
        <ul>
          <li>Der Server erkennt den Stream als AV1 statt H.264.</li>
          <li>Der Publisher bleibt verbunden.</li>
          <li>Der erste Player erhält Initialisierungsdaten und dekodiert Video.</li>
          <li>Ein später hinzukommender Player startet erfolgreich.</li>
          <li>Stoppen und Neustarten von OBS erzeugt eine saubere neue Session.</li>
          <li>Längere Läufe führen nicht zu wachsenden Frame-Verlusten oder Encoder-Überlastung auf dem sendenden Rechner.</li>
        </ul>

        <h2 id="rtmps">AV1 über RTMPS</h2>
        <p>AV1 und RTMPS sind unabhängige Funktionen. AV1 ist der Codec, E-RTMP die erweiterte Mediensignalisierung, RTMPS ergänzt TLS-Verschlüsselung um die RTMP-Verbindung. Ist verschlüsselter Ingest nötig, aktivieren Sie den RTMPS-Listener von OpenRTMP und folgen Sie der <a href="/de/guides/rtmps-server-obs/">RTMPS-Anleitung</a>.</p>

        <h2 id="troubleshooting">Fehlerbehebung</h2>
        <h3>OBS streamt, aber es erscheint kein Video</h3>
        <p>Testen Sie denselben Endpunkt mit H.264. Funktioniert H.264, prüfen Sie, ob der nachgelagerte Player AV1 in E-RTMP versteht und ob OpenRTMP den erweiterten Codec korrekt meldet.</p>

        <h3>Encoder-Überlastung oder verlorene Frames</h3>
        <p>Das ist meist ein Leistungsproblem des Encoders, kein Problem des RTMP-Protokolls. Senken Sie Auflösung/Bildrate, passen Sie die Encoder-Einstellungen an oder nutzen Sie, wo verfügbar, Hardware-Encoding.</p>

        <h3>Der Server akzeptiert AV1, aber ein klassischer Player scheitert</h3>
        <p>Das ist zu erwarten, wenn der Player nur klassisches H.264/AAC-RTMP implementiert. Verwenden Sie einen AV1/E-RTMP-fähigen Empfänger oder behalten Sie einen H.264-Kompatibilitätspfad bei.</p>

        <h2 id="hevc">AV1 vs. HEVC in einem RTMP-Workflow</h2>
        <p>Beide können die Enhanced-RTMP-Signalisierung nutzen. Die praktische Wahl hängt von Encoder-Verfügbarkeit, Hardware-Decoding, nachgelagerter Software, Qualitätszielen und lizenz- bzw. betriebsbezogenen Rahmenbedingungen außerhalb des RTMP-Protokolls ab. Testen Sie beide gegen die komplette Produktionskette, statt allein nach Codec-Effizienz zu entscheiden.</p>
        <p>Zur HEVC-Einrichtung siehe <a href="/de/guides/hevc-streaming-obs/">HEVC-Streaming mit OBS</a>.</p>

        <div class="cta compact-cta">
          <h2>Mit einem kontrollierten AV1-Test beginnen</h2>
          <p>Etablieren Sie einen funktionierenden H.264-Pfad, ändern Sie eine Variable auf AV1 und prüfen Sie sowohl die erste Wiedergabe als auch den späten Beitritt.</p>
          <div class="hero-actions" style="margin-bottom:0;">
            <a href="/de/quickstart/" class="btn btn-primary">OpenRTMP starten</a>
            <a href="/de/guides/enhanced-rtmp-v2-explained/" class="btn btn-ghost">E-RTMP v2 verstehen</a>
          </div>
        </div>
      </article>

      <aside class="toc-card" aria-label="Auf dieser Seite">
        <strong>Auf dieser Seite</strong>
        <a href="#support">AV1-Unterstützung</a>
        <a href="#requirements">Voraussetzungen</a>
        <a href="#openrtmp">Mit OpenRTMP testen</a>
        <a href="#obs">OBS konfigurieren</a>
        <a href="#verify">Prüfen</a>
        <a href="#rtmps">RTMPS</a>
        <a href="#troubleshooting">Fehlerbehebung</a>
        <a href="#hevc">AV1 vs. HEVC</a>
      </aside>
    </div>
  </section>
</main>

<?php include_once __DIR__ . '/../../../includes/footer.php'; ?>
