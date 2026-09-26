<?php
$lang = 'de';
$page = 'guides';
$pageTitle = 'RTMP-Server in Rust mit librtmp2 bauen — OpenRTMP-Entwickleranleitung';
$pageDescription = 'Einen eigenen RTMP/RTMPS-Server oder ein Relay in Rust mit librtmp2 bauen. Die Grenze zwischen Protokoll und Bibliothek, Session-Ablauf, TLS, E-RTMP, FFI verstehen – und wann librtmp2-server die bessere Wahl ist.';
$canonicalPath = '/de/guides/rtmp-server-rust/';
$ogType = 'article';
$structuredData = [
  '@context' => 'https://schema.org',
  '@type' => 'TechArticle',
  'headline' => 'RTMP-Server in Rust mit librtmp2 bauen',
  'description' => $pageDescription,
  'inLanguage' => 'de',
  'author' => ['@type' => 'Organization', 'name' => 'OpenRTMP'],
  'publisher' => ['@type' => 'Organization', 'name' => 'OpenRTMP'],
  'mainEntityOfPage' => 'https://openrtmp.org/de/guides/rtmp-server-rust/'
];
include_once __DIR__ . '/../../../includes/header.php';
?>

<main>
  <div class="page-hero container article-hero">
    <span class="eyebrow">Rust &middot; RTMP-Server &middot; Entwickler</span>
    <h1>RTMP-Server in Rust mit librtmp2 bauen</h1>
    <p><code>librtmp2</code> liefert RTMP/RTMPS-Protokollprimitive und Live-Session-Verhalten, sodass Ihre Rust-Anwendung Authentifizierung, Routing, Speicherung, APIs, Transcoding-Policy und alles rund um das Medienprotokoll selbst verantwortet.</p>
  </div>

  <section class="content-section" style="padding-top: 0;">
    <div class="container article-layout">
      <article class="prose">
        <div class="callout warning"><strong>Projektstatus:</strong> librtmp2 ist noch vor 1.0. Pinnen Sie eine getestete Version, lesen Sie die Release Notes und validieren Sie das Protokollverhalten mit Ihren exakten Publishern und Playern, bevor Sie kritische Infrastruktur darauf aufbauen.</div>

        <h2 id="library-vs-server">Bibliothek oder fertiger Server?</h2>
        <p>OpenRTMP trennt die Protokollbibliothek bewusst vom Anwendungsserver:</p>
        <table>
          <thead><tr><th>Einsatz</th><th>Beginnen mit</th></tr></thead>
          <tbody>
            <tr><td>Sie schreiben einen eigenen Rust-Server, Client, ein Relay, Gateway, Plugin oder Protokollwerkzeug</td><td><code>librtmp2</code></td></tr>
            <tr><td>Sie möchten einen sofort lauffähigen RTMP/RTMPS-Endpunkt mit Stream-Keys, SQLite, API und Statistiken</td><td><code>librtmp2-server</code></td></tr>
            <tr><td>Sie möchten zusätzlich eine Browser-UI</td><td><code>librtmp2-server-panel</code></td></tr>
          </tbody>
        </table>

        <h2 id="install">librtmp2 zu einem Rust-Projekt hinzufügen</h2>
        <p>Fügen Sie die aktuell veröffentlichte Crate mit Cargo hinzu:</p>
        <pre><code>cargo add librtmp2</code></pre>
        <p>RTMPS/TLS-Unterstützung wird über das TLS-Feature der Crate bereitgestellt und ist in aktuellen OpenRTMP-Releases standardmäßig aktiviert. Braucht Ihre Anwendung bewusst einen Build nur mit Klartext, prüfen Sie die aktuellen Crate-Features, bevor Sie die Defaults deaktivieren.</p>
        <p>Committen Sie bei Anwendungen die <code>Cargo.lock</code>, damit Deployments die getesteten Abhängigkeitsversionen verwenden.</p>

        <h2 id="architecture">Die Grenze verstehen</h2>
        <p><code>librtmp2</code> ist die Medienprotokollschicht, kein HTTP-Anwendungsframework und kein Transcoder. Zu ihren Aufgaben gehören unter anderem:</p>
        <ul>
          <li>RTMP-Handshake und Chunking.</li>
          <li>Steuernachrichten und AMF-Befehle.</li>
          <li>Publish/Play-Session-Zustand.</li>
          <li>Primitive für das Live-Relay vom Publisher zu den Playern.</li>
          <li>RTMPS-Transportunterstützung.</li>
          <li>E-RTMP- und FLV-Parser-/Serializer-Strukturen.</li>
          <li>C-kompatibles FFI für Hosts außerhalb von Rust.</li>
        </ul>
        <p>Ihre Anwendung entscheidet, wer publishen darf, wie Stream-Namen auf Mandanten abgebildet werden, wo Metadaten gespeichert werden, was aufgezeichnet wird, ob etwas transcodiert wird, wie Observability funktioniert und was passiert, wenn ein Publisher die Verbindung verliert.</p>

        <h2 id="session">Typischer Session-Ablauf</h2>
        <p>Ein vereinfachter Ablauf eines Live-Servers sieht so aus:</p>
        <pre><code>TCP/TLS accept
    |
RTMP handshake
    |
connect
    |
createStream
    |
publish OR play
    |
media/control messages
    |
close / disconnect</code></pre>
        <p>E-RTMP-fähige Sessions können Capability-Aushandlung und erweiterte Medienpakettypen ergänzen, ohne ein separates Anwendungsprotokoll zu benötigen.</p>

        <h2 id="minimal-design">Ein minimales Design für einen eigenen Server</h2>
        <p>Eine praxistaugliche Anwendung rund um die Bibliothek braucht normalerweise mindestens diese Schichten:</p>
        <ol>
          <li><strong>Listener:</strong> TCP- und optional TLS-Verbindungen annehmen.</li>
          <li><strong>RTMP-Session:</strong> Handshake, Chunks, Befehle und Medienverarbeitung an librtmp2 delegieren.</li>
          <li><strong>Autorisierung:</strong> App-/Stream-Namen oder Publish-/Play-Zugangsdaten prüfen.</li>
          <li><strong>Registry:</strong> aktive Publisher und Player verfolgen.</li>
          <li><strong>Routing:</strong> einen Publisher den Playern zuordnen, die denselben Stream abonniert haben.</li>
          <li><strong>Observability:</strong> Verbindungszahlen, Bitrate, Bytes, RTT (sofern verfügbar), Logs und Health bereitstellen.</li>
          <li><strong>Lebenszyklus:</strong> Aufräumen, Reconnect und Herunterfahren definieren.</li>
        </ol>
        <p>Der <a href="https://github.com/OpenRTMP/librtmp2-server" target="_blank" rel="noopener">Quellcode von librtmp2-server</a> ist ein konkretes Beispiel dafür, wie OpenRTMP die Protokollbibliothek um Anwendungsaspekte wie Konfiguration, SQLite, REST-Endpunkte, Authentifizierungs-Keys und Statistiken ergänzt.</p>

        <h2 id="ertmp">Moderne Codecs und E-RTMP</h2>
        <p>Braucht Ihre Anwendung HEVC, AV1, Opus oder E-RTMP-v2-Funktionen, behandeln Sie Parser-Verfügbarkeit, Session-Aushandlung und vollständiges Verhalten im Live-Pfad als getrennte Schichten. Prüfen Sie den <a href="https://github.com/OpenRTMP/librtmp2#implementation-status" target="_blank" rel="noopener">Implementierungsstand</a>, bevor Sie sich auf eine bestimmte fortgeschrittene Funktion verlassen.</p>
        <p>Hintergrund zum Protokoll finden Sie in <a href="/de/guides/enhanced-rtmp-v2-explained/">Enhanced RTMP v2 erklärt</a>.</p>

        <h2 id="ffi">Die C-kompatible Bibliothek nutzen</h2>
        <p>Die Crate ist so konfiguriert, dass sie Rust-, dynamische und statische Bibliotheksausgaben erzeugt. So lässt sich dieselbe Protokollarbeit auch in Anwendungen einbetten, die keine Rust-Crate direkt nutzen können.</p>
        <p>Definieren Sie bei der Nutzung des FFI Ownership- und Thread-Safety-Grenzen explizit in der Host-Anwendung und pinnen Sie die getestete ABI-/API-Version.</p>

        <h2 id="security">Sicherheitsgrenzen</h2>
        <ul>
          <li>Verwenden Sie RTMPS, wenn der Transport TLS-Verschlüsselung braucht.</li>
          <li>Setzen Sie für Listener mit Internetzugang passende Limits für Verbindungen und unvollständige Handshakes.</li>
          <li>Behandeln Sie einen Stream-Namen nicht als Authentifizierung, sofern Ihre Anwendung das nicht ausdrücklich so vorsieht.</li>
          <li>Begrenzen Sie Speicher für Medienpuffer und Reassembly, um unbegrenzten Ressourcenverbrauch zu vermeiden.</li>
          <li>Fuzzen Sie Parser und testen Sie fehlerhafte Chunk-/Nachrichten-Eingaben.</li>
          <li>Halten Sie die Authentifizierung der Anwendungs-API getrennt von der Publish-/Play-Autorisierung.</li>
        </ul>

        <h2 id="testing">Interoperabilitätstests</h2>
        <p>Testen Sie mindestens mit OBS und FFmpeg als Publisher, einem bewährten RTMP-Player, spätem Player-Beitritt, wiederholten Publish-/Stop-Zyklen, ungültigen Zugangsdaten, fehlerhaften Clients, RTMPS und jedem E-RTMP-Codec, den Ihre Anwendung anbieten will.</p>
        <p>Ergänzen Sie für jeden behobenen Interoperabilitätsfehler einen aufgezeichneten Regressionsfall. Protokollbibliotheken profitieren mehr von einem wachsenden Korpus echten Client-Verhaltens als von einer bloßen Funktionsliste.</p>

        <h2 id="server">Wann Sie aufhören sollten zu bauen und librtmp2-server nutzen</h2>
        <p>Wenn Ihre eigene Anwendung beginnt, eine Stream-Registry, ein API-Token, SQLite-Persistenz, Publish-/Play-/Statistik-Keys, einen Health-Endpunkt, JSON-Statistiken und eine Panel-Integration nachzubauen, vergleichen Sie diesen Aufwand mit dem bestehenden <code>librtmp2-server</code>. Oft können Sie den Server erweitern oder integrieren, statt seine Control-Plane neu zu bauen.</p>

        <div class="cta compact-cta">
          <h2>Mit der Protokollschicht beginnen</h2>
          <p>Nutzen Sie die Crate für eine eigene Architektur oder setzen Sie den Server ein, wenn Sie vor allem einen verwalteten RTMP-Endpunkt brauchen.</p>
          <div class="hero-actions" style="margin-bottom:0;">
            <a href="https://github.com/OpenRTMP/librtmp2" target="_blank" rel="noopener" class="btn btn-primary">librtmp2 öffnen</a>
            <a href="/de/quickstart/" class="btn btn-ghost">Kompletten Server starten</a>
          </div>
        </div>
      </article>

      <aside class="toc-card" aria-label="Auf dieser Seite">
        <strong>Auf dieser Seite</strong>
        <a href="#library-vs-server">Bibliothek oder Server</a>
        <a href="#install">Crate installieren</a>
        <a href="#architecture">Grenze</a>
        <a href="#session">Session-Ablauf</a>
        <a href="#minimal-design">Server-Design</a>
        <a href="#ertmp">E-RTMP</a>
        <a href="#ffi">C-FFI</a>
        <a href="#security">Sicherheit</a>
        <a href="#testing">Tests</a>
      </aside>
    </div>
  </section>
</main>

<?php include_once __DIR__ . '/../../../includes/footer.php'; ?>
