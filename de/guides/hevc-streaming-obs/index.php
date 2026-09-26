<?php
$lang = 'de';
$page = 'guides';
$pageTitle = 'HEVC-Streaming mit OBS über Enhanced RTMP — OpenRTMP-Anleitung';
$pageDescription = 'HEVC (H.265) aus OBS über Enhanced RTMP streamen, einen OpenRTMP-Ingest-Endpunkt einrichten, die Codec-Signalisierung prüfen und Encoder- sowie Player-Kompatibilität untersuchen.';
$canonicalPath = '/de/guides/hevc-streaming-obs/';
$ogType = 'article';
$structuredData = [
  '@context' => 'https://schema.org',
  '@type' => 'TechArticle',
  'headline' => 'HEVC-Streaming mit OBS über Enhanced RTMP',
  'description' => $pageDescription,
  'inLanguage' => 'de',
  'author' => ['@type' => 'Organization', 'name' => 'OpenRTMP'],
  'publisher' => ['@type' => 'Organization', 'name' => 'OpenRTMP'],
  'mainEntityOfPage' => 'https://openrtmp.org/de/guides/hevc-streaming-obs/'
];
include_once __DIR__ . '/../../../includes/header.php';
?>

<main>
  <div class="page-hero container article-hero">
    <span class="eyebrow">OBS &middot; HEVC &middot; E-RTMP</span>
    <h1>HEVC aus OBS über Enhanced RTMP streamen</h1>
    <p>HEVC kann die Bitrate bei gleicher Qualität gegenüber älteren H.264-Workflows senken, doch jede Komponente von OBS bis zum finalen Player muss die Enhanced-RTMP-Mediensignalisierung verstehen.</p>
  </div>

  <section class="content-section" style="padding-top: 0;">
    <div class="container article-layout">
      <article class="prose">
        <div class="callout warning"><strong>Kompatibilität zuerst:</strong> Ein erfolgreicher HEVC-Publish beweist nicht, dass jeder nachgelagerte Player HEVC dekodieren kann. Testen Sie die komplette Kette OBS &rarr; OpenRTMP &rarr; Player, bevor Sie sich darauf verlassen.</div>

        <h2 id="requirements">Was Sie brauchen</h2>
        <ul>
          <li>Eine aktuelle OBS-Studio-Version mit Enhanced-RTMP-Unterstützung.</li>
          <li>Einen nutzbaren HEVC-Encoder in OBS, etwa einen unterstützten Hardware- oder Software-Encoder.</li>
          <li>Einen RTMP/E-RTMP-Ingest-Server, der das erweiterte HEVC-Paketformat akzeptiert.</li>
          <li>Einen Player oder nachgelagerten Dienst, der HEVC in Ihrem Workflow empfangen und dekodieren kann.</li>
        </ul>
        <p>OBS hat HEVC- und AV1-Streaming über Enhanced RTMP mit Version 29.1 eingeführt. Die Protokollunterstützung ist also nicht auf experimentelle Kommandozeilenwerkzeuge beschränkt, auch wenn Encoder und Dienstkonfiguration weiterhin bestimmen, was OBS in der Oberfläche anbietet.</p>

        <h2 id="server">1. OpenRTMP-Ingest-Endpunkt starten</h2>
        <p>Am schnellsten testen Sie mit dem <a href="/de/quickstart/">OpenRTMP-Docker-Schnellstart</a>. Er liefert RTMP-Server, Stream-Registry, API, Statistiken und Web-Control-Panel.</p>
        <pre><code>git clone https://github.com/OpenRTMP/librtmp2-server-panel.git
cd librtmp2-server-panel
# Die benötigten .env-Secrets wie im Schnellstart gezeigt anlegen.
docker compose -f compose.quickstart.yml up -d</code></pre>
        <p>Legen Sie im Panel einen Stream an und kopieren Sie Publish-URL und <code>publish_key</code>. Für einen lokalen Test lautet die Server-URL normalerweise etwa <code>rtmp://localhost:1935/live</code>.</p>

        <h2 id="obs">2. OBS für HEVC konfigurieren</h2>
        <ol>
          <li>Öffnen Sie <strong>Einstellungen &rarr; Stream</strong> und richten Sie den OpenRTMP-Endpunkt als benutzerdefinierten RTMP-Dienst ein.</li>
          <li>Verwenden Sie den erzeugten OpenRTMP-<code>publish_key</code> als Stream-Key.</li>
          <li>Öffnen Sie <strong>Einstellungen &rarr; Ausgabe</strong>.</li>
          <li>Wechseln Sie bei Bedarf in den erweiterten Ausgabemodus, damit sich der Streaming-Encoder explizit wählen lässt.</li>
          <li>Wählen Sie einen HEVC/H.265-Encoder, sofern OBS einen für die gewählte Ausgabe und den Dienst anbietet.</li>
          <li>Beginnen Sie mit zurückhaltenden Einstellungen für Bitrate, Keyframes und Auflösung, die Ihr Decoder nachweislich verarbeitet.</li>
        </ol>
        <p>Fehlt HEVC in der Encoder-Auswahl, prüfen Sie, ob die installierte OBS-Version, GPU/Treiber oder Software-Encoder und die gewählte Dienstkonfiguration HEVC fürs Streaming anbieten. Umgehen Sie einen fehlenden Encoder nicht mit der Annahme, der Server könne H.264 in HEVC transcodieren: Der aktuelle OpenRTMP-Server ist ein fokussierter Relay- und Control-Plane-Server, kein Transcoder.</p>

        <h2 id="verify">3. Prüfen, ob tatsächlich HEVC ankommt</h2>
        <p>Nachdem sich OBS verbunden hat, prüfen Sie den Stream über das OpenRTMP-Panel oder den JSON-Statistik-Endpunkt. Das Panel zeigt Live-Bitrate, Codec, Auflösung, Bildrate, RTT, Laufzeit, Publisher und Player.</p>
        <p>Sinnvolle Prüfungen:</p>
        <ul>
          <li>Der Publisher bleibt verbunden, ohne wiederholte Reconnect-Schleifen.</li>
          <li>Der gemeldete Video-Codec ist HEVC/H.265 statt H.264.</li>
          <li>Der erste Player startet sauber.</li>
          <li>Ein später hinzukommender Player erhält den nötigen Initialisierungszustand und beginnt zu dekodieren.</li>
          <li>Der Stream funktioniert auch nach dem Stoppen und Neustarten von OBS.</li>
        </ul>

        <h2 id="rtmps">4. RTMPS ergänzen, wenn verschlüsselter Ingest nötig ist</h2>
        <p>HEVC-Signalisierung und Transportverschlüsselung lösen verschiedene Probleme. E-RTMP transportiert die moderne Codec-Signalisierung; RTMPS hüllt die RTMP-Verbindung in TLS ein.</p>
        <p>OpenRTMP kann RTMP- und RTMPS-Listener nebeneinander betreiben. Folgen Sie der <a href="/de/guides/rtmps-server-obs/">Anleitung zu RTMPS mit OBS</a>, wenn der Weg vom Publisher zum Server verschlüsselt sein muss.</p>

        <h2 id="players">Player-Kompatibilität zählt mehr als der Server allein</h2>
        <p>Klassische RTMP-Player wurden meist für H.264/AAC entworfen. Selbst wenn ein Server einen E-RTMP-HEVC-Stream korrekt weiterleitet, kann ein alter Player den erweiterten Pakettyp ablehnen oder keinen HEVC-Decoder haben.</p>
        <p>Verwenden Sie für Produktionstests mindestens einen bewährten HEVC-Empfänger und prüfen Sie jede Automatisierungs-, Restream-, Aufzeichnungs- oder Monitoring-Komponente im Pfad separat.</p>

        <h2 id="troubleshooting">Fehlerbehebung</h2>
        <h3>OBS verbindet sich, aber der Player zeigt kein Video</h3>
        <p>Prüfen Sie, ob der Player HEVC über die vom Stream genutzte Enhanced-RTMP/FLV-Signalisierung versteht. Testen Sie denselben Pfad mit H.264, um Transport-/Authentifizierungsprobleme von Codec-Kompatibilität zu trennen.</p>

        <h3>OBS bietet kein HEVC an</h3>
        <p>Prüfen Sie die Verfügbarkeit des Encoders und die Fähigkeiten des gewählten Streaming-Dienstes bzw. der Ausgabe. Aktualisieren Sie gegebenenfalls OBS und GPU-Treiber und stellen Sie sicher, dass der Encoder bei einer lokalen HEVC-Aufnahme funktioniert, bevor Sie den Netzwerkpfad untersuchen.</p>

        <h3>Der Stream läuft mit H.264, bricht aber mit HEVC ab</h3>
        <p>Halten Sie exakte OBS-Version, Encoder, Serverversion und Logs fest. Gleichen Sie dann mit dem <a href="https://github.com/OpenRTMP/librtmp2#implementation-status" target="_blank" rel="noopener">Implementierungsstand von librtmp2</a> ab und melden Sie ein reproduzierbares Interoperabilitätsproblem, falls der erweiterte Paketpfad fehlschlägt.</p>

        <h2 id="next">HEVC oder AV1?</h2>
        <p>Beide Codecs lassen sich über E-RTMP signalisieren, unterscheiden sich aber bei Hardwareverfügbarkeit und nachgelagerter Kompatibilität. Wenn Sie auch AV1 evaluieren möchten, siehe <a href="/de/guides/av1-over-rtmp/">AV1 über RTMP</a>. Zu den Unterschieden auf Protokollebene siehe <a href="/de/guides/rtmp-vs-enhanced-rtmp/">RTMP vs. E-RTMP</a>.</p>

        <div class="cta compact-cta">
          <h2>HEVC auf dem eigenen Endpunkt testen</h2>
          <p>Starten Sie den OpenRTMP-Stack, etablieren Sie zuerst H.264 und stellen Sie dann nur den Video-Codec auf HEVC um.</p>
          <div class="hero-actions" style="margin-bottom:0;">
            <a href="/de/quickstart/" class="btn btn-primary">Schnellstart öffnen</a>
            <a href="/de/guides/enhanced-rtmp-hevc-av1-opus/" class="btn btn-ghost">Codec-Details</a>
          </div>
        </div>
      </article>

      <aside class="toc-card" aria-label="Auf dieser Seite">
        <strong>Auf dieser Seite</strong>
        <a href="#requirements">Voraussetzungen</a>
        <a href="#server">Server starten</a>
        <a href="#obs">OBS konfigurieren</a>
        <a href="#verify">HEVC prüfen</a>
        <a href="#rtmps">RTMPS</a>
        <a href="#players">Player</a>
        <a href="#troubleshooting">Fehlerbehebung</a>
        <a href="#next">HEVC oder AV1</a>
      </aside>
    </div>
  </section>
</main>

<?php include_once __DIR__ . '/../../../includes/footer.php'; ?>
