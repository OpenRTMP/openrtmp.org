<?php
$lang = 'de';
$page = 'guides';
$pageTitle = 'Eigenen RTMP-Server in fünf Minuten mit Docker und OBS betreiben — OpenRTMP';
$pageDescription = 'Eigenen RTMP-Server in fünf Minuten mit Docker und OBS betreiben, Stream-Keys anlegen, Live-Statistiken prüfen und OpenRTMP für ein Deployment mit Internetzugang vorbereiten.';
$canonicalPath = '/de/guides/self-hosted-rtmp-server-docker/';
$ogType = 'article';
$structuredData = [
  '@context' => 'https://schema.org',
  '@type' => 'TechArticle',
  'headline' => 'Eigenen RTMP-Server in fünf Minuten mit Docker und OBS betreiben',
  'description' => $pageDescription,
  'inLanguage' => 'de',
  'author' => ['@type' => 'Organization', 'name' => 'OpenRTMP'],
  'publisher' => ['@type' => 'Organization', 'name' => 'OpenRTMP'],
  'mainEntityOfPage' => 'https://openrtmp.org/de/guides/self-hosted-rtmp-server-docker/'
];
include_once __DIR__ . '/../../../includes/header.php';
?>

<main>
  <div class="page-hero container article-hero">
    <span class="eyebrow">Docker &middot; OBS &middot; Selbst gehostet</span>
    <h1>Eigenen RTMP-Server in fünf Minuten mit Docker betreiben</h1>
    <p>Der OpenRTMP-Schnellstart führt Sie von einem leeren Docker-Host zu einem privaten RTMP/RTMPS-Endpunkt mit Stream-Keys, REST-API, Live-Statistiken, einem Control-Panel im Browser und einer OBS-fähigen Publish-URL.</p>
  </div>

  <section class="content-section" style="padding-top: 0;">
    <div class="container article-layout">
      <article class="prose">
        <div class="callout warning"><strong>Status:</strong> OpenRTMP befindet sich in aktiver Entwicklung und ist noch vor Version 1.0. Testen Sie Fehlerbehandlung, Reconnect-Verhalten, Codecs und jeden Client, auf den Sie sich verlassen, vor kritischem Produktiveinsatz.</div>

        <h2 id="five-minutes">Der Fünf-Minuten-Weg</h2>
        <ol>
          <li>Klonen Sie <code>librtmp2-server-panel</code>.</li>
          <li>Erzeugen Sie API-Token, Panel-Passwort und Flask-Session-Secret mit dem <a href="/de/quickstart/">Copy-and-Paste-Schnellstart</a>.</li>
          <li>Starten Sie <code>compose.quickstart.yml</code>.</li>
          <li>Öffnen Sie das Panel auf Port <code>8000</code> und legen Sie einen Stream an.</li>
          <li>Kopieren Sie RTMP-URL und <code>publish_key</code> in OBS.</li>
        </ol>
        <p>Der Rest dieser Anleitung erklärt, was der Stack tut und was Sie ändern sollten, bevor Sie ihn ins Internet stellen.</p>

        <h2 id="architecture">Was der Docker-Stack ausführt</h2>
        <p>Der Schnellstart-Stack trennt Protokollverarbeitung, Anwendungs-Policy und Benutzeroberfläche:</p>
        <ul>
          <li><strong>librtmp2-server:</strong> RTMP-Listener, optionaler RTMPS-Listener, SQLite-Registry, Key-Prüfung, REST-API und Statistiken.</li>
          <li><strong>librtmp2-server-panel:</strong> Browser-UI zum Anlegen von Streams, Kopieren von URLs und Keys sowie Anzeigen von Live-Daten.</li>
          <li><strong>Redis:</strong> gemeinsamer Rate-Limit-Zustand für das Panel-Deployment mit mehreren Workern.</li>
        </ul>
        <p>Die Protokollbibliothek ist in den Server eingebettet. Mit den veröffentlichten Container-Images müssen Sie Rust nicht installieren.</p>

        <h2 id="deploy">Lokal bereitstellen</h2>
        <p>Die vollständigen Copy-and-Paste-Befehle finden Sie im <a href="/de/quickstart/">Fünf-Minuten-Schnellstart</a>. Die Kurzfassung:</p>
        <pre><code>git clone https://github.com/OpenRTMP/librtmp2-server-panel.git
cd librtmp2-server-panel
# .env mit LRTMP2_API_TOKEN, PASSWORD und SECRET_KEY anlegen.
docker compose -f compose.quickstart.yml up -d</code></pre>
        <p>Der Stack stellt bereit:</p>
        <table>
          <thead><tr><th>Port</th><th>Zweck</th><th>Öffentliche Freigabe</th></tr></thead>
          <tbody>
            <tr><td><code>1935/tcp</code></td><td>RTMP-Publisher und -Player</td><td>Freigeben, wenn entfernte RTMP-Clients ihn brauchen</td></tr>
            <tr><td><code>8000/tcp</code></td><td>Web-Control-Panel</td><td>Vorzugsweise per HTTPS über einen Reverse-Proxy</td></tr>
            <tr><td><code>8080/tcp</code></td><td>REST-API und Statistiken</td><td>Einschränken; nur benötigte Routen proxyen</td></tr>
            <tr><td><code>1940/tcp</code> / <code>1941/tcp</code></td><td>Cluster-Control und Media-Mesh</td><td>Nur zwischen Peers bei <code>CLUSTER_ENABLED=true</code>; siehe die <a href="/de/guides/rtmp-server-ha-clustering/">HA-Anleitung</a></td></tr>
          </tbody>
        </table>

        <h2 id="stream-keys">Stream anlegen und seine Keys verstehen</h2>
        <p>Jeder Stream erhält getrennte Zugangsdaten:</p>
        <table>
          <thead><tr><th>Key</th><th>Verwendung</th></tr></thead>
          <tbody>
            <tr><td><code>publish_key</code></td><td>Erlaubt OBS, FFmpeg oder einem anderen Publisher, den Stream zu senden</td></tr>
            <tr><td><code>play_key</code></td><td>Erlaubt einem Player, den Stream zu empfangen</td></tr>
            <tr><td><code>stats_key</code></td><td>Erlaubt Zugriff auf den Monitoring-Endpunkt dieses Streams</td></tr>
          </tbody>
        </table>
        <p>Durch diese Trennung braucht eine Monitoring-Integration keine Berechtigung, Medien zu senden oder abzuspielen.</p>

        <h2 id="obs">Aus OBS streamen</h2>
        <ol>
          <li>Legen Sie im Panel einen Stream an.</li>
          <li>Öffnen Sie in OBS <strong>Einstellungen &rarr; Stream</strong>.</li>
          <li>Wählen Sie einen benutzerdefinierten Dienst.</li>
          <li>Verwenden Sie die RTMP-Server-URL aus dem Panel, normalerweise <code>rtmp://host:1935/live</code>.</li>
          <li>Verwenden Sie den erzeugten <code>publish_key</code> als Stream-Key.</li>
        </ol>
        <p>Sobald der Publisher verbunden ist, sollte das Panel Bitrate, Codec, Auflösung, Bildrate, RTT, Laufzeit und verbundene Player anzeigen.</p>

        <h2 id="monitoring">Monitoring und Statistiken im NOALBS-Stil</h2>
        <p>OpenRTMP bietet zwei Statistikformate:</p>
        <ul>
          <li><code>/stats?key=&lt;stats_key&gt;</code> liefert JSON.</li>
          <li><code>/stats-nginx?key=&lt;stats_key&gt;</code> liefert nginx-rtmp-kompatibles XML für bestehende Werkzeuge.</li>
        </ul>
        <p>Der XML-Kompatibilitätsendpunkt ist nützlich, wenn eine Integration das klassische nginx-rtmp-Statistikformat erwartet. Neue Integrationen sollten in der Regel JSON bevorzugen.</p>

        <h2 id="production">Checkliste für Deployments mit Internetzugang</h2>
        <ul class="check-list">
          <li>Verwenden Sie starke Zufallswerte für API-Token, Panel-Passwort und Flask-Session-Secret.</li>
          <li>Setzen Sie <code>LRTMP2_DOMAIN</code> auf den öffentlichen Hostnamen, den Clients tatsächlich verwenden.</li>
          <li>Stellen Sie das Panel hinter HTTPS und aktivieren Sie sichere Cookies.</li>
          <li>Geben Sie die administrative REST-API nicht breit frei.</li>
          <li>Aktivieren Sie RTMPS, wenn Publisher verschlüsselten Transport brauchen.</li>
          <li>Für Multi-Node-HA folgen Sie der <a href="/de/guides/rtmp-server-ha-clustering/">Clustering-Anleitung</a> und geben jedem Node ein eigenes SQLite-Volume.</li>
          <li>Sichern Sie das persistente SQLite-Volume.</li>
          <li>Pinnen Sie getestete Container-Versionen statt beweglicher Tags.</li>
          <li>Setzen Sie für den Host passende Limits für Verbindungen, Request-Bodys und Raten.</li>
          <li>Testen Sie Container-Neustart, Host-Reboot, Publisher-Reconnect und späten Player-Beitritt.</li>
        </ul>

        <h2 id="fit">Wann OpenRTMP passt</h2>
        <p>OpenRTMP ist ein starker Kandidat für privaten Ingest, fokussierte RTMP-Experimente, eigene Control-Planes, Stream-Key-Verwaltung oder Integrationen, die Rust und eine kleine, verständliche Architektur möchten.</p>
        <p>Wählen Sie eine breitere Medienplattform, wenn Sie integrierte HLS-Auslieferung, Aufzeichnung, Transcoding, Push-Relay, eine öffentliche Zuschauer-Website oder viele Nicht-RTMP-Protokolle brauchen, ohne zusätzliche Dienste zu schreiben.</p>

        <h2 id="troubleshooting">Fehlerbehebung</h2>
        <h3>Das Panel lädt, aber das Anlegen von Streams schlägt fehl</h3>
        <p>Prüfen Sie, ob Panel und Server dasselbe API-Token verwenden und der Server-Container healthy ist. Ein wiederverwendetes SQLite-Volume kann noch ein älteres Token enthalten.</p>
        <h3>OBS läuft in einen Timeout</h3>
        <p>Prüfen Sie die öffentliche Firewall und das Docker-Port-Mapping für <code>1935/tcp</code>. Kontrollieren Sie den Application-Pfad und verwenden Sie den Publish-Key, nicht den Play- oder Statistik-Key.</p>
        <h3>Statistik-Links sind von einem anderen Rechner aus nicht erreichbar</h3>
        <p>Ersetzen Sie localhost-Werte in <code>LRTMP2_DOMAIN</code> und <code>LRTMP2_STATS_URL</code> durch vom Browser erreichbare Adressen.</p>

        <div class="cta compact-cta">
          <h2>Stack bereitstellen</h2>
          <p>Nutzen Sie den eigenen Schnellstart und kehren Sie dann für die Produktions-Checkliste hierher zurück.</p>
          <div class="hero-actions" style="margin-bottom:0;">
            <a href="/de/quickstart/" class="btn btn-primary">Schnellstart öffnen</a>
            <a href="/de/guides/rtmps-server-obs/" class="btn btn-ghost">RTMPS ergänzen</a>
            <a href="/de/guides/rtmp-server-ha-clustering/" class="btn btn-ghost">Clustering ergänzen</a>
          </div>
        </div>
      </article>

      <aside class="toc-card" aria-label="Auf dieser Seite">
        <strong>Auf dieser Seite</strong>
        <a href="#five-minutes">Fünf-Minuten-Weg</a>
        <a href="#architecture">Architektur</a>
        <a href="#deploy">Bereitstellen</a>
        <a href="#stream-keys">Stream-Keys</a>
        <a href="#obs">OBS</a>
        <a href="#monitoring">Monitoring</a>
        <a href="#production">Produktions-Checkliste</a>
        <a href="#fit">Wann einsetzen</a>
        <a href="#troubleshooting">Fehlerbehebung</a>
      </aside>
    </div>
  </section>
</main>

<?php include_once __DIR__ . '/../../../includes/footer.php'; ?>
