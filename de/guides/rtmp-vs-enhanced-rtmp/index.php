<?php
$lang = 'de';
$page = 'guides';
$pageTitle = 'RTMP vs. Enhanced RTMP (E-RTMP): Was ist anders? — OpenRTMP';
$pageDescription = 'Klassisches RTMP und Enhanced RTMP (E-RTMP) im Vergleich: FourCC-Codec-Signalisierung, HEVC, AV1, Opus, Multitrack, Reconnect-Unterstützung, Kompatibilität und Migration.';
$canonicalPath = '/de/guides/rtmp-vs-enhanced-rtmp/';
$ogType = 'article';
$structuredData = [
  '@context' => 'https://schema.org',
  '@type' => 'TechArticle',
  'headline' => 'RTMP vs. Enhanced RTMP (E-RTMP): Was ist anders?',
  'description' => $pageDescription,
  'inLanguage' => 'de',
  'author' => ['@type' => 'Organization', 'name' => 'OpenRTMP'],
  'publisher' => ['@type' => 'Organization', 'name' => 'OpenRTMP'],
  'mainEntityOfPage' => 'https://openrtmp.org/de/guides/rtmp-vs-enhanced-rtmp/'
];
include_once __DIR__ . '/../../../includes/header.php';
?>

<main>
  <div class="page-hero container article-hero">
    <span class="eyebrow">RTMP &middot; E-RTMP &middot; Protokoll</span>
    <h1>RTMP vs. Enhanced RTMP (E-RTMP)</h1>
    <p>Enhanced RTMP modernisiert den etablierten RTMP/FLV-Medienpfad, ohne das Kompatibilitätsmodell aufzugeben, das RTMP für Live-Ingest so nützlich gemacht hat.</p>
  </div>

  <section class="content-section" style="padding-top: 0;">
    <div class="container article-layout">
      <article class="prose">
        <div class="callout"><strong>Kurzfassung:</strong> Klassisches RTMP bleibt die Grundlage für Transport und Session. E-RTMP erweitert die Mediensignalisierung und die Fähigkeiten darum herum, sodass moderne Codecs und neuere Streaming-Funktionen ausgehandelt werden können, ohne ein völlig neues Ingest-Protokoll zu benötigen.</div>

        <h2 id="legacy">Was klassisches RTMP bietet</h2>
        <p>RTMP ist ein Protokoll auf Anwendungsebene, aufgebaut aus Handshake, Chunk-Streams, Steuernachrichten, AMF-Befehlen und Mediennachrichten. Klassische Live-Workflows transportieren normalerweise H.264-Video und AAC-Audio mit dem klassischen FLV-Signalisierungsmodell.</p>
        <p>Diese Kombination ist mit Encodern, Medienservern und Produktionswerkzeugen äußerst interoperabel. Deshalb ist RTMP als Ingest-Protokoll weiterhin weit verbreitet, obwohl Flash selbst längst Geschichte ist.</p>

        <h2 id="enhanced">Was E-RTMP ändert</h2>
        <p>Die <a href="https://veovera.org/docs/enhanced/enhanced-rtmp-v2.html" target="_blank" rel="noopener">E-RTMP-v2-Spezifikation von Veovera</a> erweitert RTMP und FLV und behält das klassische Protokoll als Teil des Ökosystems bei. Zu den Erweiterungen gehören:</p>
        <ul>
          <li><strong>FourCC-basierte Codec-Signalisierung</strong> für moderne Video- und Audioformate.</li>
          <li><strong>Moderne Video-Codecs</strong> einschließlich Signalisierung für HEVC, AV1, VP9 und VVC.</li>
          <li><strong>Moderne Audio-Codecs</strong> einschließlich Signalisierung für Opus und FLAC.</li>
          <li><strong>Multitrack-Fähigkeiten</strong> für mehrere Audio- oder Videospuren.</li>
          <li><strong>Signalisierung von Reconnect-Anfragen</strong> für robustere Workflows.</li>
          <li><strong>Zusätzliche Metadaten und genauere Zeitstempel</strong> für moderne Medien-Pipelines.</li>
        </ul>
        <p>E-RTMP erfordert keine neue Version des klassischen RTMP-Handshakes. Seine Fähigkeiten werden über Ergänzungen im Bitstream und in der Session-Signalisierung eingeführt – wichtig für eine schrittweise Einführung.</p>

        <h2 id="comparison">Klassisches RTMP und E-RTMP im direkten Vergleich</h2>
        <table class="comparison-table">
          <thead><tr><th>Bereich</th><th>Klassisches RTMP</th><th>E-RTMP</th></tr></thead>
          <tbody>
            <tr><td>Session-Grundlage</td><td>RTMP-Handshake, Chunks, AMF-Befehle</td><td>Baut auf derselben RTMP-Grundlage auf</td></tr>
            <tr><td>Typisches Video</td><td>H.264</td><td>H.264 plus moderne, per FourCC signalisierte Codecs wie HEVC und AV1</td></tr>
            <tr><td>Typisches Audio</td><td>FLV-Signalisierung aus der AAC-/MP3-Ära</td><td>Ergänzt moderne Codec-Signalisierung wie Opus und FLAC</td></tr>
            <tr><td>Codec-Kennung</td><td>Klassische FLV-Codec-Kennungen</td><td>FourCC-basierte Signalisierung verfügbar</td></tr>
            <tr><td>Mehrere Medienspuren</td><td>Nicht auf das moderne E-RTMP-Multitrack-Modell ausgelegt</td><td>Multitrack-Fähigkeiten sind spezifiziert</td></tr>
            <tr><td>Reconnect-Signalisierung</td><td>Keine E-RTMP-Reconnect-Anfrage</td><td>Reconnect-Anfrage ist Teil der v2-Spezifikation</td></tr>
            <tr><td>Kompatibilitätsziel</td><td>Maximale klassische Interoperabilität</td><td>RTMP erweitern und dabei möglichst abwärtskompatibel bleiben</td></tr>
          </tbody>
        </table>

        <h2 id="compatibility">Bricht E-RTMP alte RTMP-Clients?</h2>
        <p>Nicht automatisch. Ein Server kann weiterhin klassische H.264/AAC-Publisher annehmen und gleichzeitig erweiterte Pakettypen verstehen. Der wichtige Vorbehalt betrifft die <em>gesamte Kette</em>: Ein Publisher kann erfolgreich HEVC oder AV1 senden, während ein älterer Player die resultierenden Medien oder sogar deren Signalisierung nicht versteht.</p>
        <p>Behandeln Sie in gemischten Umgebungen H.264/AAC als Kompatibilitäts-Basislinie und aktivieren Sie erweiterte Codecs nur dort, wo Sender, Server, Relay-Pfad und Empfänger validiert sind.</p>

        <h2 id="obs">OBS und Enhanced RTMP</h2>
        <p>OBS Studio hat mit Version 29.1 AV1- und HEVC-Streaming über Enhanced RTMP eingeführt. Aktuelle OBS-Versionen enthalten die Protokollunterstützung; ob ein bestimmter Codec wählbar ist, hängt aber weiterhin von der gewählten Ausgabe-/Dienstkonfiguration und einem verfügbaren Encoder ab.</p>
        <p>Für die praktische Einrichtung siehe <a href="/de/guides/hevc-streaming-obs/">HEVC-Streaming mit OBS</a> und <a href="/de/guides/av1-over-rtmp/">AV1 über RTMP</a>.</p>

        <h2 id="openrtmp">Wie OpenRTMP mit beidem umgeht</h2>
        <p><a href="https://github.com/OpenRTMP/librtmp2" target="_blank" rel="noopener"><code>librtmp2</code></a> ist sowohl auf klassisches RTMP als auch auf E-RTMP ausgelegt. Der Standard-Live-Pfad unterstützt klassische Publish-/Play-Workflows und, wo implementiert, das Durchreichen erweiterter Medien, während weitere E-RTMP-Parser- und Aushandlungsfunktionen weiterentwickelt werden.</p>
        <p>Schließen Sie nicht aus der Existenz eines Parser-Typs auf vollständige End-to-End-Unterstützung. Die <a href="https://github.com/OpenRTMP/librtmp2#implementation-status" target="_blank" rel="noopener">Tabelle zum Implementierungsstand</a> im Repository ist die codegenaue Quelle dafür, was vollständig, teilweise, nur als Parser oder noch experimentell ist.</p>

        <h2 id="migration">Ein sicherer Migrationspfad</h2>
        <ol>
          <li>Einen bewährten H.264/AAC-RTMP-Workflow für Publish und Play etablieren.</li>
          <li>Exakte Versionen von OBS/FFmpeg, Server und Player notieren.</li>
          <li>Nur den Video-Codec auf HEVC oder AV1 umstellen.</li>
          <li>Codec-Erkennung, Start mit dem ersten Frame und späten Player-Beitritt prüfen.</li>
          <li>Trennen/Neuverbinden und längere Sessions testen.</li>
          <li>Über RTMPS wiederholen, wenn verschlüsselter Ingest nötig ist.</li>
          <li>Einen klassischen H.264-Pfad verfügbar halten, bis jeder benötigte Client nachweislich kompatibel ist.</li>
        </ol>

        <h2 id="which">Was sollten Sie verwenden?</h2>
        <p>Nutzen Sie klassisches H.264/AAC-RTMP, wenn Interoperabilität die Hauptanforderung ist. Nutzen Sie E-RTMP-Funktionen, wenn Sie genug von der Medienkette kontrollieren, um von neueren Codecs, Multitrack-Signalisierung oder neueren Protokollfähigkeiten zu profitieren, und diese Clients gezielt testen können.</p>
        <p>Die Wahl lautet also nicht wirklich „RTMP oder E-RTMP“. E-RTMP ist eine Weiterentwicklung des RTMP-Ökosystems, und moderne Server können beide Pfade gleichzeitig unterstützen.</p>

        <div class="cta compact-cta">
          <h2>Den erweiterten Medienpfad erkunden</h2>
          <p>Beginnen Sie mit der umfassenderen Codec-Anleitung und validieren Sie dann Ihre exakte Kombination aus Sender und Player.</p>
          <div class="hero-actions" style="margin-bottom:0;">
            <a href="/de/guides/enhanced-rtmp-hevc-av1-opus/" class="btn btn-primary">Codec-Anleitung</a>
            <a href="/de/quickstart/" class="btn btn-ghost">OpenRTMP starten</a>
          </div>
        </div>
      </article>

      <aside class="toc-card" aria-label="Auf dieser Seite">
        <strong>Auf dieser Seite</strong>
        <a href="#legacy">Klassisches RTMP</a>
        <a href="#enhanced">Was E-RTMP ändert</a>
        <a href="#comparison">Vergleich</a>
        <a href="#compatibility">Kompatibilität</a>
        <a href="#obs">OBS</a>
        <a href="#openrtmp">OpenRTMP</a>
        <a href="#migration">Migration</a>
        <a href="#which">Was verwenden</a>
      </aside>
    </div>
  </section>
</main>

<?php include_once __DIR__ . '/../../../includes/footer.php'; ?>
