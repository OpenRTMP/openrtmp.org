<?php
$lang = 'de';
$page = 'quickstart';
$pageTitle = 'Eigenen RTMP-Server mit Docker betreiben — OpenRTMP-Schnellstart';
$pageDescription = 'OpenRTMP-Server, Redis und das Web-Control-Panel in etwa fünf Minuten aus veröffentlichten Docker-Images starten und anschließend aus OBS streamen.';
$canonicalPath = '/de/quickstart/';
$structuredData = [
  '@context' => 'https://schema.org',
  '@type' => 'HowTo',
  'name' => 'OpenRTMP mit Docker betreiben und aus OBS streamen',
  'description' => $pageDescription,
  'inLanguage' => 'de',
  'totalTime' => 'PT5M',
  'tool' => [
    ['@type' => 'HowToTool', 'name' => 'Docker mit Docker Compose'],
    ['@type' => 'HowToTool', 'name' => 'OpenSSL und Python 3 zum Erzeugen der Secrets']
  ],
  'step' => [
    ['@type' => 'HowToStep', 'name' => 'Panel-Repository klonen', 'url' => 'https://openrtmp.org/de/quickstart/#clone'],
    ['@type' => 'HowToStep', 'name' => 'Sichere Umgebungswerte erzeugen', 'url' => 'https://openrtmp.org/de/quickstart/#secrets'],
    ['@type' => 'HowToStep', 'name' => 'Docker-Stack starten', 'url' => 'https://openrtmp.org/de/quickstart/#start'],
    ['@type' => 'HowToStep', 'name' => 'Stream im Panel anlegen', 'url' => 'https://openrtmp.org/de/quickstart/#create-stream'],
    ['@type' => 'HowToStep', 'name' => 'Aus OBS streamen', 'url' => 'https://openrtmp.org/de/quickstart/#obs']
  ]
];
include_once __DIR__ . '/../../includes/header.php';
?>

<main>
  <div class="page-hero container">
    <span class="eyebrow">Docker-Schnellstart</span>
    <h1>OpenRTMP in etwa fünf Minuten starten</h1>
    <p>Starten Sie RTMP-Server, Web-Panel und Redis aus veröffentlichten Images. Weder eine Rust-Toolchain noch eine Python-Umgebung oder ein benachbarter Quellcode-Checkout ist nötig.</p>
  </div>

  <section class="content-section" style="padding-top: 0;">
    <div class="container article-layout">
      <article class="prose">
        <div class="callout warning">
          <strong>Aktive Entwicklung:</strong> OpenRTMP ist noch vor Version 1.0. Diese Anleitung ist für Evaluierungen und getestete selbst gehostete Deployments gedacht. Pinnen Sie Image-Versionen und prüfen Sie Publishing, Wiedergabe, Authentifizierung und Wiederherstellung vor kritischem Produktiveinsatz.
        </div>

        <h2 id="requirements">Voraussetzungen</h2>
        <ul>
          <li>Docker mit dem Compose-Plugin</li>
          <li>Git</li>
          <li>OpenSSL und Python 3 für den Copy-and-Paste-Secret-Generator</li>
        </ul>
        <p>Unter Windows können Sie gleichwertige Zufallswerte manuell erzeugen und in <code>.env</code> eintragen.</p>

        <h2 id="clone">1. Deployment-Repository klonen</h2>
        <pre><code>git clone https://github.com/OpenRTMP/librtmp2-server-panel.git
cd librtmp2-server-panel</code></pre>
        <p>Der Schnellstart verwendet <code>compose.quickstart.yml</code>. Anders als die Compose-Datei für die Entwicklung lädt sie veröffentlichte Images und erwartet keinen benachbarten <code>librtmp2-server</code>-Checkout.</p>

        <h2 id="secrets">2. Benötigte Secrets erzeugen</h2>
        <pre><code>API_TOKEN="$(openssl rand -hex 32)"
PANEL_SECRET="$(python3 -c 'import secrets; print(secrets.token_hex(32))')"
PANEL_PASSWORD="$(openssl rand -base64 24 | tr -d '\n')"

cat &gt; .env &lt;&lt;EOF
LRTMP2_API_TOKEN=${API_TOKEN}
LRTMP2_DOMAIN=localhost
LRTMP2_STATS_URL=http://localhost:8080
USERNAME=admin
PASSWORD=${PANEL_PASSWORD}
SECRET_KEY=${PANEL_SECRET}
REQUIRE_LOGIN=True
EOF

printf 'Panel-Passwort notieren: %s\n' "${PANEL_PASSWORD}"</code></pre>
        <p>Das API-Token wird beiden Diensten übergeben. Beim ersten Start des Servers wird es in SQLite gespeichert. Wenn Sie später nur die Compose-Umgebung ändern, wird das gespeicherte Token nicht automatisch rotiert.</p>

        <h2 id="start">3. Stack starten</h2>
        <pre><code>docker compose -f compose.quickstart.yml up -d
docker compose -f compose.quickstart.yml ps</code></pre>
        <p>Die Dienste sind erreichbar unter:</p>
        <table>
          <thead><tr><th>Dienst</th><th>Adresse</th></tr></thead>
          <tbody>
            <tr><td>Web-Panel</td><td><code>http://localhost:8000</code></td></tr>
            <tr><td>HTTP-API</td><td><code>http://localhost:8080</code></td></tr>
            <tr><td>RTMP-Listener</td><td><code>rtmp://localhost:1935</code></td></tr>
          </tbody>
        </table>
        <p>API prüfen:</p>
        <pre><code>curl http://localhost:8080/api/v1/health</code></pre>
        <p>Eine erfolgreiche Antwort enthält <code>"status":"ok"</code>.</p>

        <h2 id="create-stream">4. Stream anlegen</h2>
        <ol>
          <li>Öffnen Sie <code>http://localhost:8000</code>.</li>
          <li>Melden Sie sich als <code>admin</code> mit dem erzeugten Panel-Passwort an.</li>
          <li>Legen Sie einen neuen Stream an.</li>
          <li>Kopieren Sie die erzeugte Publish-URL und den <code>publish_key</code>.</li>
        </ol>
        <p>Das Panel erzeugt außerdem getrennte Wiedergabe- und Statistik-Keys. Veröffentlichen Sie diese Keys nur, wenn die jeweilige Zielgruppe diesen Zugriff haben soll.</p>

        <h2 id="obs">5. Aus OBS streamen</h2>
        <p>Öffnen Sie in OBS <strong>Einstellungen &rarr; Stream</strong>, wählen Sie einen benutzerdefinierten Dienst und tragen Sie ein:</p>
        <table>
          <tbody>
            <tr><th>Server</th><td><code>rtmp://localhost:1935/live</code></td></tr>
            <tr><th>Stream-Key</th><td>Der erzeugte <code>publish_key</code></td></tr>
          </tbody>
        </table>
        <p>Starten Sie den Stream und kehren Sie zum Panel zurück, um Bitrate, Codec, Auflösung, RTT, Laufzeit und verbundene Player zu sehen.</p>

        <h2 id="internet">Von localhost auf einen Server umziehen</h2>
        <ul>
          <li>Setzen Sie <code>LRTMP2_DOMAIN</code> auf den öffentlichen Hostnamen oder die IP.</li>
          <li>Setzen Sie <code>LRTMP2_STATS_URL</code> auf die vom Browser erreichbare HTTPS-API-URL.</li>
          <li>Beschränken Sie den direkten Zugriff auf Port <code>8080</code>; stellen Sie über einen Reverse-Proxy nur die Routen bereit, die Ihre Integrationen brauchen.</li>
          <li>Liefern Sie das Panel über HTTPS aus und aktivieren Sie sichere Session-Cookies.</li>
          <li>Aktivieren Sie RTMPS mit einem gültigen Zertifikat, wenn verschlüsselter Ingest nötig ist.</li>
          <li>Ersetzen Sie bewegliche <code>latest</code>-Image-Tags durch getestete Release-Tags.</li>
        </ul>

        <h2 id="troubleshooting">Häufige Probleme</h2>
        <div class="faq-item">
          <h3>Das Panel erreicht die API nicht</h3>
          <p>Prüfen Sie, ob beide Container healthy sind und das Panel intern <code>http://openrtmp-server:8080</code> verwendet. Nutzen Sie <code>localhost</code> nicht für Verkehr zwischen Containern.</p>
        </div>
        <div class="faq-item">
          <h3>Die API meldet „unauthorized“</h3>
          <p>Panel und Server müssen dasselbe <code>LRTMP2_API_TOKEN</code> verwenden. Wenn Sie ein bestehendes Server-Volume wiederverwenden, ist das ursprüngliche Token bereits in SQLite gespeichert.</p>
        </div>
        <div class="faq-item">
          <h3>OBS kann sich nicht verbinden</h3>
          <p>Prüfen Sie, ob Port <code>1935/tcp</code> erreichbar ist, verwenden Sie exakt die Application und den Publish-Key aus dem Panel und sehen Sie sich <code>docker compose -f compose.quickstart.yml logs openrtmp-server</code> an.</p>
        </div>
        <div class="faq-item">
          <h3>Die kopierten URLs enthalten localhost</h3>
          <p>Setzen Sie <code>LRTMP2_DOMAIN</code> und <code>LRTMP2_STATS_URL</code> auf Adressen, die für die tatsächlichen Publisher und Browser erreichbar sind.</p>
        </div>

        <h2 id="stop">Stack stoppen oder entfernen</h2>
        <pre><code># Container stoppen, SQLite-Volume behalten
docker compose -f compose.quickstart.yml down

# Zusätzlich gespeicherte Serverdaten löschen
docker compose -f compose.quickstart.yml down -v</code></pre>

        <div class="cta compact-cta">
          <h2>Nächste Schritte</h2>
          <p>Lesen Sie die produktionsorientierte Deployment-Anleitung, richten Sie RTMPS ein oder integrieren Sie die Rust-Crate direkt.</p>
          <div class="hero-actions" style="margin-bottom:0;">
            <a href="/de/guides/self-hosted-rtmp-server-docker/" class="btn btn-primary">Deployment-Anleitung</a>
            <a href="/de/guides/rtmps-server-obs/" class="btn btn-ghost">RTMPS mit OBS</a>
            <a href="https://github.com/OpenRTMP/librtmp2" target="_blank" rel="noopener" class="btn btn-ghost">librtmp2 nutzen</a>
          </div>
        </div>
      </article>

      <aside class="toc-card" aria-label="Auf dieser Seite">
        <strong>Auf dieser Seite</strong>
        <a href="#requirements">Voraussetzungen</a>
        <a href="#clone">Klonen</a>
        <a href="#secrets">Secrets</a>
        <a href="#start">Starten</a>
        <a href="#create-stream">Stream anlegen</a>
        <a href="#obs">Aus OBS streamen</a>
        <a href="#internet">Internet-Deployment</a>
        <a href="#troubleshooting">Fehlerbehebung</a>
      </aside>
    </div>
  </section>
</main>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>
