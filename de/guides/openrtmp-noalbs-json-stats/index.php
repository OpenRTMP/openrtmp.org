<?php
$lang = 'de';
$page = 'guides';
$pageTitle = 'OpenRTMP-JSON-Statistiken mit NOALBS — Einrichtungsanleitung';
$pageDescription = 'NOALBS v2.19.0 oder neuer mit den JSON-Statistiken von OpenRTMP verbinden, Stream-Keys verstehen, XML-Statistiken als Fallback konfigurieren und häufige Fehler beheben.';
$canonicalPath = '/de/guides/openrtmp-noalbs-json-stats/';
$ogType = 'article';
$structuredData = [
  '@context' => 'https://schema.org',
  '@type' => 'TechArticle',
  'headline' => 'OpenRTMP-JSON-Statistiken mit NOALBS nutzen',
  'description' => $pageDescription,
  'inLanguage' => 'de',
  'author' => ['@type' => 'Organization', 'name' => 'OpenRTMP'],
  'mainEntityOfPage' => 'https://openrtmp.org/de/guides/openrtmp-noalbs-json-stats/'
];
include_once __DIR__ . '/../../../includes/header.php';
?>

<main>
  <div class="page-hero container article-hero">
    <span class="eyebrow">NOALBS &middot; JSON-Statistiken &middot; Monitoring</span>
    <h1>OpenRTMP-JSON-Statistiken mit NOALBS nutzen</h1>
    <p>Verbinden Sie NOALBS mit dem key-geschützten Statistik-Endpunkt von OpenRTMP, konfigurieren Sie Bitrate- und RTT-Trigger und behalten Sie den nginx-kompatiblen XML-Endpunkt als Fallback für ältere NOALBS-Versionen oder bestehende Integrationen.</p>
  </div>

  <section class="content-section" style="padding-top: 0;">
    <div class="container article-layout">
      <article class="prose">
        <div class="callout"><strong>Veröffentlichte Unterstützung:</strong> NOALBS <a href="https://github.com/NOALBS/nginx-obs-automatic-low-bitrate-switching/releases/tag/v2.19.0" target="_blank" rel="noopener">v2.19.0</a> enthält den nativen Provider <code>OpenRTMP</code>. Er liest <code>/stats?key=...</code> direkt und nutzt die OpenRTMP-Werte <code>bitrate_kbps</code> und <code>rtt_ms</code> für den Szenenwechsel.</div>

        <h2 id="requirements">Was Sie brauchen</h2>
        <ul class="check-list">
          <li>Eine laufende <code>librtmp2-server</code>-Instanz, deren HTTP-Listener von NOALBS aus erreichbar ist.</li>
          <li>Einen über das OpenRTMP-Panel oder die REST-API angelegten Stream.</li>
          <li>Den <code>stats_key</code> des Streams.</li>
          <li>NOALBS v2.19.0 oder neuer für den nativen JSON-Provider.</li>
        </ul>
        <p>OpenRTMP-Server und Protokoll-Stack sind noch vor 1.0 und in aktiver Entwicklung. Testen Sie den exakten Workflow aus Publisher, Statistiken, Reconnect und Szenenwechsel, bevor Sie sich bei einem kritischen Produktions-Stream darauf verlassen.</p>

        <h2 id="create-stream">Stream anlegen und Keys erhalten</h2>
        <p>Am einfachsten geht es mit <a href="https://github.com/OpenRTMP/librtmp2-server-panel" target="_blank" rel="noopener">librtmp2-server-panel</a>. Legen Sie im Panel einen Stream an und kopieren Sie dessen Statistik-URL oder <code>stats_key</code>.</p>
        <p>Alternativ legen Sie einen Stream über die REST-API an:</p>
        <pre><code>curl -X POST http://openrtmp-server:8080/api/v1/streams \
  -H "Authorization: Bearer &lt;api_token&gt;" \
  -H "Content-Type: application/json" \
  -d '{"id":"mobile","name":"Mobile ingest","app":"live"}'</code></pre>
        <p>Die Antwort enthält drei getrennte Zugangsdaten:</p>
        <pre><code>{
  "id": "mobile",
  "name": "Mobile ingest",
  "app": "live",
  "publish_key": "live_REDACTED_EXAMPLE",
  "play_key": "play_REDACTED_EXAMPLE",
  "stats_key": "sts_REDACTED_EXAMPLE",
  "enabled": true
}</code></pre>

        <h2 id="keys">Publish-, Play- und Statistik-Keys</h2>
        <table>
          <thead><tr><th>Key</th><th>Zweck</th><th>Wo er verwendet wird</th></tr></thead>
          <tbody>
            <tr><td><code>publish_key</code></td><td>Autorisiert einen Publisher.</td><td>OBS, FFmpeg, Moblin oder ein anderer RTMP/RTMPS-Publishing-Client.</td></tr>
            <tr><td><code>play_key</code></td><td>Autorisiert die Wiedergabe.</td><td>OBS-Medienquellen, Player oder Relay-Konsumenten, die den Stream abholen.</td></tr>
            <tr><td><code>stats_key</code></td><td>Autorisiert den Zugriff auf die Statistiken eines Streams.</td><td>NOALBS, Monitoring-Werkzeuge, Dashboards und direkte Statistikabfragen.</td></tr>
          </tbody>
        </table>
        <div class="callout warning"><strong>Keys nicht vertauschen.</strong> NOALBS braucht nur den <code>stats_key</code>. Tragen Sie niemals das API-Bearer-Token, den <code>publish_key</code> oder den <code>play_key</code> in die NOALBS-Statistik-URL ein.</div>

        <h2 id="json-endpoint">Nativer JSON-Endpunkt</h2>
        <p>Der öffentliche Endpunkt pro Stream lautet:</p>
        <pre><code>http://openrtmp-server:8080/stats?key=sts_REDACTED_EXAMPLE</code></pre>
        <p>Solange der Publisher live ist, ist die Antwort ein flaches JSON-Objekt, das zum nativen NOALBS-Provider passt:</p>
        <pre><code>{
  "uptime": 18,
  "bitrate_kbps": 10565.5,
  "rtt_ms": 100.4,
  "bytes_in": 34618510,
  "video": {
    "codec": "hvc1",
    "width": 1920,
    "height": 1080,
    "fps": 30.0
  },
  "audio": {
    "codec": "Opus"
  }
}</code></pre>
        <p>Der key-geschützte öffentliche Endpunkt lässt Stream-ID, Anzeigename, Application-Namen, Player und die serverweite Zusammenfassung bewusst weg. Administrative API-Endpunkte können mit dem API-Bearer-Token mehr Details liefern.</p>

        <h3>Statistikfelder und Einheiten</h3>
        <table>
          <thead><tr><th>Feld</th><th>Bedeutung</th></tr></thead>
          <tbody>
            <tr><td><code>bitrate_kbps</code></td><td>Aktuelle eingehende Publisher-Bitrate in Kilobit pro Sekunde. NOALBS vergleicht diesen Wert mit den Schwellen <code>low</code> und <code>offline</code>.</td></tr>
            <tr><td><code>rtt_ms</code></td><td>Aktuelle Round-Trip-Time des Publishers in Millisekunden. NOALBS vergleicht diesen Wert mit den Schwellen <code>rtt</code> und <code>rttOffline</code>.</td></tr>
            <tr><td><code>uptime</code></td><td>Dauer der Publisher-Verbindung in Sekunden.</td></tr>
            <tr><td><code>bytes_in</code></td><td>Gesamtzahl der von der aktuellen Publisher-Verbindung empfangenen Medienbytes.</td></tr>
          </tbody>
        </table>

        <h2 id="noalbs-config">NOALBS v2.19.0 oder neuer konfigurieren</h2>
        <p>Fügen Sie NOALBS einen Stream-Server-Eintrag vom Typ <code>OpenRTMP</code> hinzu. Die vollständige <code>statsUrl</code> muss den <code>stats_key</code> des Streams enthalten:</p>
        <pre><code>{
  "switcher": {
    "triggers": {
      "low": 2500,
      "offline": 500,
      "rtt": 200,
      "rttOffline": 2000
    },
    "streamServers": [
      {
        "name": "openrtmp",
        "priority": 0,
        "enabled": true,
        "streamServer": {
          "type": "OpenRTMP",
          "statsUrl": "http://openrtmp-server:8080/stats?key=sts_REDACTED_EXAMPLE"
        }
      }
    ]
  }
}</code></pre>
        <p>Die Schwellen gehören in <code>switcher.triggers</code>, nicht in das Provider-Objekt:</p>
        <ul>
          <li><code>low</code>: Wechsel zur Low-Bitrate-Szene, wenn <code>bitrate_kbps</code> diesen Wert erreicht oder unterschreitet.</li>
          <li><code>offline</code>: Wechsel zur Offline-Szene, wenn ein positiver <code>bitrate_kbps</code>-Wert diesen Wert erreicht oder unterschreitet.</li>
          <li><code>rtt</code>: Wechsel zur Low-Bitrate-Szene, wenn <code>rtt_ms</code> diesen Wert erreicht oder überschreitet.</li>
          <li><code>rttOffline</code>: Wechsel zur Offline-Szene, wenn <code>rtt_ms</code> diesen Wert erreicht oder überschreitet.</li>
        </ul>
        <p>Ist der Stream offline, liefert der Endpunkt kein Live-JSON-Objekt. Der native Provider behandelt eine nicht erfolgreiche, nicht erreichbare oder nicht-JSON-Antwort als offline.</p>

        <h2 id="xml-fallback">nginx-kompatibles XML als Fallback</h2>
        <p>Verwenden Sie den XML-Endpunkt, wenn Sie ein älteres NOALBS-Release betreiben, eine bestehende <code>Nginx</code>-Provider-Konfiguration beibehalten oder die Kompatibilität mit Software testen, die nginx-rtmp-Statistiken erwartet:</p>
        <pre><code>http://openrtmp-server:8080/stats-nginx?key=sts_REDACTED_EXAMPLE</code></pre>
        <p>OpenRTMP schwärzt in der öffentlichen XML-Antwort die echten Application- und Stream-Namen. Deshalb muss der NOALBS-Provider die festen Werte <code>live</code> und <code>stream</code> verwenden:</p>
        <pre><code>{
  "name": "openrtmp-xml",
  "priority": 0,
  "enabled": true,
  "streamServer": {
    "type": "Nginx",
    "statsUrl": "http://openrtmp-server:8080/stats-nginx?key=sts_REDACTED_EXAMPLE",
    "application": "live",
    "key": "stream"
  }
}</code></pre>
        <p>Der <code>key</code>-Wert des Providers oben ist der wörtliche geschwärzte Stream-Name <code>stream</code>. Er ist nicht der OpenRTMP-<code>stats_key</code>; diese Zugangsdaten bleiben in der <code>statsUrl</code>.</p>

        <h2 id="networking">Den richtigen Host wählen</h2>
        <p>Die URL muss von dem Rechner oder Container aus erreichbar sein, auf dem NOALBS läuft. Eine URL, die in Ihrem Desktop-Browser funktioniert, kann innerhalb eines Containers trotzdem scheitern.</p>
        <table>
          <thead><tr><th>Deployment</th><th>Empfohlener Host in <code>statsUrl</code></th></tr></thead>
          <tbody>
            <tr><td>NOALBS und OpenRTMP im selben Docker-Netzwerk</td><td>Verwenden Sie den DNS-Namen des OpenRTMP-Dienstes oder -Containers, zum Beispiel <code>http://openrtmp-server:8080/...</code>.</td></tr>
            <tr><td>NOALBS auf dem Docker-Host</td><td>Verwenden Sie den veröffentlichten Host-Port, zum Beispiel <code>http://127.0.0.1:8080/...</code>.</td></tr>
            <tr><td>NOALBS auf einem anderen Server</td><td>Verwenden Sie eine private Netzwerkadresse, einen VPN-Hostnamen oder einen geschützten öffentlichen HTTPS-Hostnamen, den der NOALBS-Rechner auflösen und erreichen kann.</td></tr>
            <tr><td>NOALBS in einem separaten Container</td><td>Verwenden Sie <code>localhost</code> nur, wenn OpenRTMP denselben Network-Namespace teilt. In einem normalen Container zeigt <code>localhost</code> auf NOALBS selbst.</td></tr>
          </tbody>
        </table>
        <p>Bevorzugen Sie den internen Dienstnamen, wenn beide Anwendungen ein vertrauenswürdiges Docker-Netzwerk teilen. Bevorzugen Sie die öffentliche HTTPS-URL, wenn NOALBS entfernt läuft und der Endpunkt bewusst über einen Reverse-Proxy freigegeben ist.</p>

        <h2 id="security">Statistik-Keys schützen</h2>
        <ul class="check-list">
          <li>Ersetzen Sie Statistik-Keys durch <code>sts_REDACTED</code>, bevor Sie Screenshots, Logs, Konfigurationsdateien oder Issue-Berichte veröffentlichen.</li>
          <li>Committen Sie keine echte <code>statsUrl</code> mit Key in ein öffentliches Repository.</li>
          <li>Schwärzen Sie Query-Strings nach Möglichkeit in Reverse-Proxy-Access-Logs und Monitoring-Alarmen.</li>
          <li>Rotieren oder erstellen Sie einen Stream neu, wenn sein Statistik-Key offengelegt wurde.</li>
          <li>Verwenden Sie HTTPS, sobald die Statistikabfrage ein nicht vertrauenswürdiges Netzwerk durchquert.</li>
        </ul>

        <h2 id="troubleshooting">Fehlerbehebung</h2>
        <h3>401 Unauthorized</h3>
        <p>Prüfen Sie, ob der Query-Parameter exakt den <code>stats_key</code> des Streams enthält. Prüfen Sie außerdem, ob ein Reverse-Proxy eine eigene Authentifizierung ergänzt oder beim Umschreiben der Anfrage den Query-String entfernt hat.</p>

        <h3>404 Not Found</h3>
        <p>Prüfen Sie Endpunkt-Pfad und HTTP-Port. Testen Sie <code>/stats?key=...</code> direkt gegen den OpenRTMP-HTTP-Listener, bevor Sie die Reverse-Proxy-URL testen. Eine Proxy-Location, die nur ein anderes Präfix weiterleitet, liefert 404, auch wenn OpenRTMP gesund ist.</p>

        <h3>502 Bad Gateway</h3>
        <p>Der Reverse-Proxy erreicht den OpenRTMP-HTTP-Dienst nicht. Prüfen Sie Upstream-Host, Port, Docker-Netzwerk, Container-Health und ob der Proxy einen öffentlichen Hostnamen nutzt, der auf ihn selbst zurückverweist. Verwenden Sie in einem containerisierten Proxy den richtigen internen Dienstnamen und Port.</p>

        <h3>NOALBS meldet den Server als nicht erreichbar</h3>
        <p>Führen Sie die Anfrage vom selben Rechner oder Container aus wie NOALBS. Prüfen Sie DNS-Auflösung, Firewall-Regeln, Vertrauen in das TLS-Zertifikat und ob <code>localhost</code> auf den falschen Container verweist. Lassen Sie den Query-String unverändert.</p>

        <h3>Die URL funktioniert öffentlich, aber nicht intern</h3>
        <p>Hairpin-NAT, Split-DNS oder Reverse-Proxy-Routing können den öffentlichen Hostnamen im internen Netzwerk unbrauchbar machen. Verwenden Sie in NOALBS einen internen Docker-DNS-Namen, eine private IP oder einen internen DNS-Eintrag und behalten Sie die öffentliche URL für Browser und entfernte Integrationen.</p>

        <h3>Der XML-Provider zeigt Bitrate null oder offline</h3>
        <p>Verwenden Sie exakt <code>application: "live"</code> und <code>key: "stream"</code>. Tragen Sie dort nicht die echte OpenRTMP-Application oder Stream-ID ein. Prüfen Sie, ob der Publisher live ist und die URL auf <code>/stats-nginx</code> zeigt, nicht auf <code>/stats</code>.</p>

        <div class="cta compact-cta">
          <h2>Endpunkt testen, bevor Sie den Wechsel aktivieren</h2>
          <p>Öffnen Sie die bereinigte URL mit <code>curl</code>, prüfen Sie Live-Werte für Bitrate und RTT, starten Sie dann NOALBS und testen Sie jede Schwelle mit unkritischen Szenen.</p>
          <div class="hero-actions" style="margin-bottom:0;">
            <a href="https://github.com/OpenRTMP/librtmp2-server#http-api" target="_blank" rel="noopener" class="btn btn-primary">Server-Dokumentation öffnen</a>
            <a href="https://github.com/NOALBS/nginx-obs-automatic-low-bitrate-switching/tree/v2.19.0" target="_blank" rel="noopener" class="btn btn-ghost">NOALBS v2.19.0 ansehen</a>
          </div>
        </div>
      </article>

      <aside class="toc-card" aria-label="Auf dieser Seite">
        <strong>Auf dieser Seite</strong>
        <a href="#requirements">Voraussetzungen</a>
        <a href="#create-stream">Stream anlegen</a>
        <a href="#keys">Key-Typen</a>
        <a href="#json-endpoint">JSON-Endpunkt</a>
        <a href="#noalbs-config">NOALBS-Konfiguration</a>
        <a href="#xml-fallback">XML-Fallback</a>
        <a href="#networking">Netzwerk</a>
        <a href="#security">Key-Sicherheit</a>
        <a href="#troubleshooting">Fehlerbehebung</a>
      </aside>
    </div>
  </section>
</main>

<?php include_once __DIR__ . '/../../../includes/footer.php'; ?>
