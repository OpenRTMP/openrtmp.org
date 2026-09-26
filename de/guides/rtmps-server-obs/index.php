<?php
$lang = 'de';
$page = 'guides';
$pageTitle = 'RTMPS-Server für OBS einrichten — OpenRTMP-Anleitung';
$pageDescription = 'RTMPS zusätzlich zu RTMP in OpenRTMP aktivieren, TLS-Zertifikatsdateien und Ports konfigurieren, den Server-Status prüfen und OBS sicher verbinden.';
$canonicalPath = '/de/guides/rtmps-server-obs/';
$ogType = 'article';
$structuredData = [
  '@context' => 'https://schema.org',
  '@type' => 'TechArticle',
  'headline' => 'RTMPS-Server für OBS einrichten',
  'description' => $pageDescription,
  'inLanguage' => 'de',
  'author' => ['@type' => 'Organization', 'name' => 'OpenRTMP'],
  'mainEntityOfPage' => 'https://openrtmp.org/de/guides/rtmps-server-obs/'
];
include_once __DIR__ . '/../../../includes/header.php';
?>

<main>
  <div class="page-hero container article-hero">
    <span class="eyebrow">RTMPS &middot; TLS &middot; OBS</span>
    <h1>RTMPS-Server für OBS einrichten</h1>
    <p>OpenRTMP kann einen verschlüsselten RTMPS-Listener neben normalem RTMP betreiben. Beide Listener teilen sich Stream-Registry, Authentifizierungs-Keys, Relay-Kern und Verbindungslimits.</p>
  </div>

  <section class="content-section" style="padding-top: 0;">
    <div class="container article-layout">
      <article class="prose">
        <div class="callout warning"><strong>Experimenteller Bereich:</strong> Testen Sie die RTMPS-Unterstützung vor dem Produktiveinsatz mit Ihrem exakten OBS-, FFmpeg-, Zertifikats-, Reverse-Proxy- und Firewall-Setup.</div>

        <h2 id="how-it-works">Wie RTMPS in OpenRTMP funktioniert</h2>
        <p>Das Aktivieren von TLS startet einen zusätzlichen Listener. Er ersetzt den unverschlüsselten RTMP-Listener nicht. Ein Publisher kann sich über RTMPS verbinden, während ein Player RTMP nutzt – oder umgekehrt –, weil beide Listener dieselbe Serverinstanz verwenden.</p>
        <p>Typische Ports:</p>
        <table>
          <thead><tr><th>Protokoll</th><th>Standardbeispiel</th></tr></thead>
          <tbody>
            <tr><td>RTMP</td><td><code>1935/tcp</code></td></tr>
            <tr><td>RTMPS</td><td><code>1936/tcp</code></td></tr>
          </tbody>
        </table>
        <p>RTMPS ist RTMP, das direkt über TLS transportiert wird. Das ist nicht dasselbe wie ein HTTPS-Reverse-Proxy vor der HTTP-API.</p>

        <h2 id="certificate">1. Zertifikat und privaten Schlüssel vorbereiten</h2>
        <p>Verwenden Sie ein Zertifikat, das für den Hostnamen gültig ist, den Publisher in OBS eintragen. Der Server braucht lesbare Pfade zur Zertifikatskette und zum passenden privaten Schlüssel.</p>
        <p>Bei einem Container-Deployment mounten Sie die Dateien schreibgeschützt:</p>
        <pre><code>volumes:
  - /etc/letsencrypt/live/stream.example.com/fullchain.pem:/certs/fullchain.pem:ro
  - /etc/letsencrypt/live/stream.example.com/privkey.pem:/certs/privkey.pem:ro</code></pre>
        <p>Backen Sie private Schlüssel niemals in ein öffentliches Image oder Repository ein.</p>

        <h2 id="configuration">2. RTMPS-Listener aktivieren</h2>
        <p>Setzen Sie die Server-Konfigurationswerte:</p>
        <pre><code>TLS_ENABLED=true
TLS_CERT_FILE=/certs/fullchain.pem
TLS_KEY_FILE=/certs/privkey.pem
RTMPS_BIND=0.0.0.0:1936</code></pre>
        <p>Das normale <code>RTMP_BIND</code> bleibt aktiv. Geben Sie den zusätzlichen Port in Docker frei:</p>
        <pre><code>ports:
  - "1935:1935"
  - "1936:1936"</code></pre>
        <p>Der Server verweigert das Aktivieren von TLS, wenn einer der Zertifikatspfade fehlt. Das verhindert eine scheinbar funktionierende Konfiguration, bei der der Health-Endpunkt verschlüsselten Ingest meldet, der Listener aber nie gestartet wurde.</p>

        <h2 id="verify">3. Server prüfen, bevor Sie OBS ändern</h2>
        <p>Health-Endpunkt abfragen:</p>
        <pre><code>curl https://api.example.com/api/v1/health</code></pre>
        <p>Die Antwort sollte anzeigen, dass RTMPS aktiviert ist, und den gebundenen Port nennen. Prüfen Sie außerdem das Zertifikat aus dem Netzwerk des Clients:</p>
        <pre><code>openssl s_client -connect stream.example.com:1936 \
  -servername stream.example.com \
  -verify_hostname stream.example.com \
  -verify_return_error</code></pre>
        <p>Achten Sie auf eine erfolgreich validierte Zertifikatskette und einen passenden Hostnamen.</p>

        <h2 id="obs">4. OBS über RTMPS verbinden</h2>
        <ol>
          <li>Öffnen Sie in OBS <strong>Einstellungen &rarr; Stream</strong>.</li>
          <li>Wählen Sie einen benutzerdefinierten Dienst.</li>
          <li>Verwenden Sie <code>rtmps://stream.example.com:1936/live</code> als Server.</li>
          <li>Verwenden Sie den OpenRTMP-<code>publish_key</code> als Stream-Key.</li>
          <li>Starten Sie den Stream und beobachten Sie Server-Logs und Panel-Statistiken.</li>
        </ol>
        <p>Das Panel zeigt RTMPS-URLs nur an, wenn der Health-Endpunkt des Servers einen aktiven TLS-Listener meldet.</p>

        <h2 id="network">Stolperfallen bei Netzwerk und Zertifikat</h2>
        <h3>Port 1936 ist geschlossen</h3>
        <p>Öffnen Sie den TCP-Port in Host-Firewall, Cloud-Security-Group, Docker-Mapping und jeder vorgelagerten Netzwerk-Firewall.</p>
        <h3>Das Zertifikat ist im Browser gültig, aber OBS lehnt es ab</h3>
        <p>Stellen Sie sicher, dass die vollständige Kette übergeben wird, nicht nur das Leaf-Zertifikat. Testen Sie aus demselben Netzwerk und mit demselben Hostnamen, den OBS verwendet.</p>
        <h3>Ein TCP-Proxy terminiert TLS vorher</h3>
        <p>Entscheiden Sie, ob OpenRTMP oder der Proxy für TLS zuständig ist. Terminiert der Proxy TLS, muss er unverschlüsseltes RTMP an den RTMP-Listener weiterleiten. Ist OpenRTMP für TLS zuständig, verwenden Sie TCP-Passthrough statt HTTP-Proxying.</p>
        <h3>Das Panel zeigt weiterhin nur RTMP-URLs</h3>
        <p>Prüfen Sie, ob das Panel den aktuellen Health-Endpunkt erreicht, RTMPS als aktiviert gemeldet wird und <code>LRTMP2_RTMPS_PORT</code> dem öffentlichen Port entspricht.</p>

        <h2 id="security">Sicherheitsempfehlungen</h2>
        <ul class="check-list">
          <li>Verwenden Sie für Publisher aus dem Internet ein öffentlich vertrauenswürdiges Zertifikat.</li>
          <li>Automatisieren Sie die Erneuerung und starten Sie den Server nach dem Zertifikatstausch neu bzw. laden Sie ihn neu.</li>
          <li>Machen Sie den privaten Schlüssel nur für das Dienstkonto oder den Container lesbar.</li>
          <li>Behalten Sie die Publish-Key-Authentifizierung auch bei verschlüsseltem Transport bei.</li>
          <li>Überwachen Sie fehlgeschlagene Handshakes und wiederholte Authentifizierungsfehler.</li>
          <li>Testen Sie nach jeder Zertifikats- oder Netzwerkänderung sowohl RTMP als auch RTMPS.</li>
        </ul>

        <div class="cta compact-cta">
          <h2>Mit dem Docker-Deployment beginnen</h2>
          <p>Stellen Sie zuerst den Basis-Stack bereit und ergänzen Sie danach Zertifikate und den zweiten Listener.</p>
          <div class="hero-actions" style="margin-bottom:0;">
            <a href="/de/quickstart/" class="btn btn-primary">Docker-Schnellstart</a>
            <a href="/de/docs/#server" class="btn btn-ghost">Server-Referenz</a>
          </div>
        </div>
      </article>

      <aside class="toc-card" aria-label="Auf dieser Seite">
        <strong>Auf dieser Seite</strong>
        <a href="#how-it-works">Funktionsweise</a>
        <a href="#certificate">Zertifikat</a>
        <a href="#configuration">Konfiguration</a>
        <a href="#verify">Prüfung</a>
        <a href="#obs">OBS-Einrichtung</a>
        <a href="#network">Stolperfallen</a>
        <a href="#security">Sicherheit</a>
      </aside>
    </div>
  </section>
</main>

<?php include_once __DIR__ . '/../../../includes/footer.php'; ?>
