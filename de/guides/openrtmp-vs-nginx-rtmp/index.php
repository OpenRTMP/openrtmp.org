<?php
$lang = 'de';
$page = 'guides';
$pageTitle = 'OpenRTMP vs. nginx-rtmp — Welchen RTMP-Server sollten Sie nutzen?';
$pageDescription = 'OpenRTMP und nginx-rtmp im Vergleich: Architektur, Deployment, APIs, Stream-Keys, Statistiken, Codec-Ziele und fehlende Funktionen.';
$canonicalPath = '/de/guides/openrtmp-vs-nginx-rtmp/';
$ogType = 'article';
$structuredData = [
  '@context' => 'https://schema.org',
  '@type' => 'TechArticle',
  'headline' => 'OpenRTMP vs. nginx-rtmp',
  'description' => $pageDescription,
  'inLanguage' => 'de',
  'author' => ['@type' => 'Organization', 'name' => 'OpenRTMP'],
  'mainEntityOfPage' => 'https://openrtmp.org/de/guides/openrtmp-vs-nginx-rtmp/'
];
include_once __DIR__ . '/../../../includes/header.php';
?>

<main>
  <div class="page-hero container article-hero">
    <span class="eyebrow">Vergleich &middot; Migration &middot; Architektur</span>
    <h1>OpenRTMP vs. nginx-rtmp</h1>
    <p>Beide Projekte überschneiden sich bei RTMP-Ingest und -Wiedergabe, verfolgen aber unterschiedliche Ziele. Die richtige Wahl hängt eher von der benötigten Anwendungsschicht ab als von der Portnummer, die sie bereitstellen.</p>
  </div>

  <section class="content-section" style="padding-top: 0;">
    <div class="container article-layout">
      <article class="prose">
        <div class="callout"><strong>Kurzfassung:</strong> Wählen Sie nginx-rtmp für etablierte nginx-Workflows und integrierte Modulfunktionen. Wählen Sie OpenRTMP für einen Rust-basierten Protokoll-Stack, einen kleinen API-gesteuerten Server, getrennte Stream-Keys, JSON-Statistiken, optionales HA-Clustering und eine einbettbare Bibliothek — und akzeptieren Sie dabei den Status vor 1.0 und den schmaleren Medienfunktionsumfang.</div>

        <h2 id="overview">Vergleich im Überblick</h2>
        <table class="comparison-table">
          <thead><tr><th>Bereich</th><th>OpenRTMP</th><th>nginx-rtmp</th></tr></thead>
          <tbody>
            <tr><td>Grundarchitektur</td><td>Rust-Protokollbibliothek plus separater Server und Panel</td><td>Drittanbieter-Modul für nginx, konfiguriert über nginx-Direktiven</td></tr>
            <tr><td>Reife</td><td>Aktive Entwicklung (vor 1.0)</td><td>Lange etabliertes Ökosystem</td></tr>
            <tr><td>Administration</td><td>REST-API, SQLite und optionales Web-Panel</td><td>Konfigurationsdateien, Callbacks und umgebende nginx-Werkzeuge</td></tr>
            <tr><td>Stream-Zugangsdaten</td><td>Getrennte Publish-, Play- und Statistik-Keys pro Stream</td><td>Meist über Callbacks oder eigene nginx-Konfiguration umgesetzt</td></tr>
            <tr><td>Statistiken</td><td>JSON plus nginx-kompatibles XML</td><td>Klassischer XML-Statistik-Endpunkt mit XSL-Darstellung</td></tr>
            <tr><td>Einbettbare Bibliothek</td><td>Rust-Crate und C-kompatibles FFI</td><td>Keine vergleichbare eigenständige Protokoll-Crate</td></tr>
            <tr><td>HLS, Aufzeichnung, Exec, Push</td><td>Im aktuellen Server nicht enthalten</td><td>Übliche Funktionen des nginx-rtmp-Moduls</td></tr>
            <tr><td>Multi-Node-HA</td><td>Optionales Clustering mit OpenRaft + Media-Mesh (vor 1.0, standardmäßig aus)</td><td>Meist externe Load-Balancer, gemeinsamer Speicher oder eigene Push-Topologie</td></tr>
            <tr><td>Modernes RTMP</td><td>Expliziter Fokus auf RTMPS und Enhanced-RTMP-Bausteine</td><td>Vorwiegend klassische RTMP-Modul-Workflows</td></tr>
          </tbody>
        </table>

        <h2 id="openrtmp-fit">Wählen Sie OpenRTMP, wenn</h2>
        <ul class="check-list">
          <li>Sie eine Rust-Anwendung bauen und wiederverwendbaren RTMP-Protokollcode möchten.</li>
          <li>Sie einen kleinen selbst gehosteten Server mit expliziter REST-API und SQLite-basierter Stream-Registry möchten.</li>
          <li>Sie getrennte Zugangsdaten für Publishing, Wiedergabe und Monitoring möchten.</li>
          <li>Sie JSON-Statistiken brauchen, aber auch Kompatibilität mit Werkzeugen, die XML im nginx-Stil erwarten.</li>
          <li>Sie an Enhanced RTMP, RTMPS, Interoperabilität oder Parser-Sicherheit mitarbeiten möchten.</li>
          <li>Sie optionales Multi-Node-HA mit repliziertem Stream-Zustand möchten und es in Ihrer Topologie sorgfältig evaluieren können.</li>
          <li>Sie den exakten Publishing-/Wiedergabe-Workflow vor kritischem Produktiveinsatz testen können.</li>
        </ul>

        <h2 id="nginx-fit">Wählen Sie nginx-rtmp, wenn</h2>
        <ul class="check-list">
          <li>Sie nginx bereits betreiben und sein Konfigurationsmodell kennen.</li>
          <li>Sie auf integrierte HLS-Erzeugung, Aufzeichnung, Exec-Hooks oder Push-Relay angewiesen sind.</li>
          <li>Sie ein ausgereiftes Deployment-Muster mit vielen vorhandenen Beispielen brauchen.</li>
          <li>Ihr Monitoring und Ihre Automatisierung bereits direkt auf das Verhalten von nginx-rtmp zugeschnitten sind.</li>
          <li>Sie keine einbettbare Rust/C-Protokollbibliothek brauchen.</li>
        </ul>

        <h2 id="migration">Überlegungen zur Migration</h2>
        <h3>Statistik-Integrationen</h3>
        <p>OpenRTMP stellt <code>/stats-nginx?key=&lt;stats_key&gt;</code> für Werkzeuge bereit, die nginx-rtmp-XML verstehen. Der Endpunkt schützt jeden Stream bewusst mit seinem Statistik-Key, daher unterscheidet sich der URL-Aufbau von einer öffentlichen serverweiten Statistikseite.</p>
        <h3>Authentifizierung</h3>
        <p>nginx-rtmp-Deployments nutzen oft HTTP-Callbacks mit <code>on_publish</code> und <code>on_play</code>. OpenRTMP hält Stream-Einträge und Keys in SQLite und prüft sie in der Anwendungsschicht des Servers.</p>
        <h3>Konfigurationsmodell</h3>
        <p>Versuchen Sie nicht, jede nginx-Direktive eins zu eins zu übertragen. OpenRTMP ist kein nginx-Modul und verzichtet bewusst auf mehrere Anwendungsfunktionen von nginx-rtmp.</p>
        <h3>Medienfunktionen</h3>
        <p>Wenn Ihre nginx-Konfiguration aufzeichnet, transcodiert, HLS paketiert oder an andere Ziele pusht, behalten Sie diese Dienste bei oder ergänzen Sie separate Komponenten, bevor Sie den Ingest migrieren.</p>

        <h2 id="coexist">Beide können parallel laufen</h2>
        <p>Eine Migration muss nicht alles oder nichts sein. Sie können OpenRTMP auf einem separaten Port oder Host testen, das Verhalten von OBS/FFmpeg vergleichen und nginx-rtmp für HLS- oder Relay-Aufgaben behalten, während Sie die API und das Key-Modell von OpenRTMP evaluieren.</p>
        <p>Ein sinnvoller gestufter Test:</p>
        <ol>
          <li>Einen H.264/AAC-Workflow mit einem Publisher und einem Player nachbilden.</li>
          <li>Start, Reconnect, späten Beitritt und Monitoring-Verhalten vergleichen.</li>
          <li>Das nginx-kompatible XML mit der bestehenden Automatisierung prüfen.</li>
          <li>Jede aktuell genutzte nginx-Direktive erfassen.</li>
          <li>Nicht unterstützte Funktionen in nginx belassen oder durch explizite Dienste ersetzen.</li>
        </ol>

        <h2 id="limitations">Einschränkungen von OpenRTMP, die Sie berücksichtigen sollten</h2>
        <p>Der aktuelle Server ist kein direkter Ersatz für nginx-rtmp. Er bietet kein integriertes HLS, Exec, Push-Relay, keine Aufzeichnung und keine vollständige Parität der nginx-Direktiven. Protokoll und öffentliche APIs entwickeln sich vor 1.0 noch weiter.</p>
        <p>Diese Einschränkungen sind akzeptabel, wenn das Zielsystem ein fokussierter RTMP-Endpunkt oder ein einbettbarer Protokoll-Stack ist. Sie sind ein Ausschlusskriterium, wenn die bestehende nginx-Konfiguration als vollständige Medien-Workflow-Engine dient.</p>

        <h2 id="alternatives">Über nginx-rtmp hinaus</h2>
        <p>Wenn Ihre Migration eher von Multiprotokoll-Auslieferung als speziell von OpenRTMP getrieben ist, vergleichen Sie die breiteren Optionen in <a href="/de/guides/nginx-rtmp-alternatives/">Alternativen zu nginx-rtmp</a> und <a href="/de/guides/openrtmp-vs-mediamtx-vs-srs/">OpenRTMP vs. MediaMTX vs. SRS</a>.</p>

        <div class="cta compact-cta">
          <h2>OpenRTMP evaluieren, ohne nginx zu ersetzen</h2>
          <p>Starten Sie den Docker-Stack auf einem anderen Host oder Port und testen Sie einen Workflow Ende-zu-Ende.</p>
          <div class="hero-actions" style="margin-bottom:0;">
            <a href="/de/quickstart/" class="btn btn-primary">Evaluierung starten</a>
            <a href="https://github.com/OpenRTMP/librtmp2-server#project-status" target="_blank" rel="noopener" class="btn btn-ghost">Projektstatus lesen</a>
          </div>
        </div>
      </article>

      <aside class="toc-card" aria-label="Auf dieser Seite">
        <strong>Auf dieser Seite</strong>
        <a href="#overview">Vergleich</a>
        <a href="#openrtmp-fit">OpenRTMP wählen</a>
        <a href="#nginx-fit">nginx-rtmp wählen</a>
        <a href="#migration">Migration</a>
        <a href="#coexist">Parallelbetrieb</a>
        <a href="#limitations">Einschränkungen</a>
        <a href="#alternatives">Alternativen</a>
      </aside>
    </div>
  </section>
</main>

<?php include_once __DIR__ . '/../../../includes/footer.php'; ?>
