<?php
$lang = 'de';
$page = 'guides';
$pageTitle = 'Enhanced RTMP v2 erklärt — Codecs, Multitrack, Reconnect und Kompatibilität';
$pageDescription = 'Enhanced RTMP v2 (E-RTMP) verstehen: FourCC-Codec-Signalisierung, HEVC, AV1, Opus, Multitrack-Audio/-Video, Reconnect-Anfragen, Zeitstempelgenauigkeit und Abwärtskompatibilität.';
$canonicalPath = '/de/guides/enhanced-rtmp-v2-explained/';
$ogType = 'article';
$structuredData = [
  '@context' => 'https://schema.org',
  '@type' => 'TechArticle',
  'headline' => 'Enhanced RTMP v2 erklärt',
  'description' => $pageDescription,
  'inLanguage' => 'de',
  'author' => ['@type' => 'Organization', 'name' => 'OpenRTMP'],
  'publisher' => ['@type' => 'Organization', 'name' => 'OpenRTMP'],
  'mainEntityOfPage' => 'https://openrtmp.org/de/guides/enhanced-rtmp-v2-explained/'
];
include_once __DIR__ . '/../../../includes/header.php';
?>

<main>
  <div class="page-hero container article-hero">
    <span class="eyebrow">E-RTMP v2 &middot; Protokoll &middot; Medien</span>
    <h1>Enhanced RTMP v2 erklärt</h1>
    <p>E-RTMP v2 behält die vertraute Session-Grundlage von RTMP bei und erweitert Medienformat und Fähigkeiten um moderne Codecs, mehrere Spuren, Reconnect-Signalisierung, Metadaten und genaueres Timing.</p>
  </div>

  <section class="content-section" style="padding-top: 0;">
    <div class="container article-layout">
      <article class="prose">
        <div class="callout"><strong>Status der Spezifikation:</strong> Das aktuelle E-RTMP-v2-Dokument von Veovera ist eine Release-Spezifikation. Diese Anleitung ist ein implementierungsorientierter Überblick; normative Anforderungen entnehmen Sie der <a href="https://veovera.org/docs/enhanced/enhanced-rtmp-v2.html" target="_blank" rel="noopener">offiziellen Spezifikation</a>.</div>

        <h2 id="why">Warum RTMP eine Erweiterung brauchte</h2>
        <p>Klassische RTMP- und FLV-Workflows wurden für die Codec-Landschaft einer früheren Ära entworfen. H.264 und AAC wurden extrem interoperabel, doch neuere Video- und Audioformate passten nicht sauber in die ursprünglichen FLV-Codec-Kennungen.</p>
        <p>E-RTMP erweitert dieses Modell, statt das ganze Protokoll zu ersetzen. Ziel ist es, das bestehende RTMP-Ökosystem zu erhalten und Implementierungen zugleich einen definierten Weg zu geben, neuere Medienformate und Fähigkeiten zu signalisieren.</p>

        <h2 id="no-new-handshake">Es ist immer noch RTMP</h2>
        <p>Eine der wichtigsten Designentscheidungen ist, was E-RTMP <em>nicht</em> tut: Es erfordert keine neue Version des RTMP-Handshakes. Die Erweiterungen werden über Ergänzungen im Medien-Bitstream und in den Verbindungsfähigkeiten signalisiert.</p>
        <p>So kann eine Serverimplementierung klassische und erweiterte Publisher auf demselben Protokoll-Stack unterstützen.</p>

        <h2 id="fourcc">FourCC-Codec-Signalisierung</h2>
        <p>FourCC-Kennungen bieten eine erweiterbarere Möglichkeit, Medien-Codecs zu identifizieren, als die begrenzten klassischen FLV-Codec-Felder. E-RTMP nutzt diesen Mechanismus für moderne Video- und Audioformate.</p>
        <p>Die v2-Spezifikation deckt fortgeschrittene Videoformate wie HEVC, AV1, VP8/VP9 und VVC ab sowie Audioformate wie Opus, FLAC, AC-3 und E-AC-3. Sie definiert außerdem FourCC-Signalisierung für ausgewählte klassische Codecs, wo dies im erweiterten Modell sinnvoll ist.</p>

        <h2 id="multitrack">Mehrere Audio- und Videospuren</h2>
        <p>E-RTMP v2 spezifiziert Multitrack-Fähigkeiten, damit eine Session mehr als das klassische einzelne Audio-/Video-Paar beschreiben und transportieren kann. Das ist wichtig für Workflows wie mehrere Audiosprachen, alternative Mischungen oder anspruchsvollere Produktions-Pipelines.</p>
        <p>Protokollunterstützung ist nur die erste Schicht. Ein Server braucht außerdem Routing-Semantik, APIs, Persistenzregeln und Player-Verhalten, die diese Spuren verstehen. Unterscheiden Sie bei der Bewertung einer Implementierung zwischen „das Paketformat kann geparst werden“ und „die komplette Anwendung bietet Multitrack Ende-zu-Ende“.</p>

        <h2 id="reconnect">Signalisierung von Reconnect-Anfragen</h2>
        <p>Die v2-Spezifikation enthält eine Reconnect-Anfrage, die die Ausfallsicherheit und kontrollierte Migration von Live-Sessions verbessern soll. Das Protokoll kann die Anfrage transportieren, doch die Host-Anwendung muss weiterhin entscheiden, wie sie darauf reagiert und wie der Ersatztransport aufgebaut wird.</p>
        <p>Diese Unterscheidung ist besonders bei Bibliotheken wie <code>librtmp2</code> wichtig: Die Session-Aushandlung kann eine Reconnect-Fähigkeit anbieten, während die einbettende Anwendung für DNS, Load-Balancing, Socket-Lebenszyklus, Retry-Policy und Zielauswahl zuständig ist.</p>

        <h2 id="timing">Genaueres Timing und Metadaten</h2>
        <p>E-RTMP v2 ergänzt Mechanismen für präzisere Timing-Offsets und erweitert die Metadaten-Fähigkeiten, ohne das grundlegende RTMP-Zeitstempelfeld selbst zu ändern. So können moderne Medien-Pipelines Timing-Informationen erhalten, die sich im klassischen Format nur umständlich ausdrücken ließen.</p>

        <h2 id="compatibility">Abwärtskompatibilität</h2>
        <p>E-RTMP ist als Erweiterung von klassischem RTMP/FLV konzipiert, doch die End-to-End-Kompatibilität hängt davon ab, was ein Client tatsächlich versteht. Ein klassischer H.264/AAC-Publisher kann weiterhin den traditionellen Pfad nutzen. Ein AV1- oder HEVC-Publisher braucht erweiterte Signalisierung und eine nachgelagerte Kette, die den gewählten Codec versteht.</p>
        <p>Wenn Kompatibilität wichtig ist, behalten Sie einen klassischen Basistest bei und ergänzen erweiterte Funktionen schrittweise.</p>

        <h2 id="openrtmp">E-RTMP v2 in OpenRTMP</h2>
        <p><a href="https://github.com/OpenRTMP/librtmp2" target="_blank" rel="noopener"><code>librtmp2</code></a> von OpenRTMP enthält E-RTMP-Strukturen, Parser, Session-Aushandlung und Arbeiten am Live-Relay. Manche fortgeschrittenen Funktionen können als Parser-/Bibliotheksunterstützung existieren, bevor jedes Verhalten der Anwendungsschicht im Standard-Serverpfad verdrahtet ist.</p>
        <p>Deshalb ist die <a href="https://github.com/OpenRTMP/librtmp2#implementation-status" target="_blank" rel="noopener">Tabelle zum Implementierungsstand</a> aussagekräftiger als ein generisches „unterstützt E-RTMP v2“-Badge.</p>

        <h2 id="test">So testen Sie eine Implementierung</h2>
        <ol>
          <li>Beginnen Sie mit H.264/AAC und prüfen Sie normales RTMP-Publish/-Play.</li>
          <li>Testen Sie jeweils nur einen erweiterten Codec.</li>
          <li>Prüfen Sie Sequence-Start-/Konfigurationspakete und den Start mit dem ersten Frame.</li>
          <li>Verbinden Sie einen Player, nachdem der Publisher bereits live ist.</li>
          <li>Testen Sie mehrere Spuren erst, wenn erweiterte Medien mit einer Spur stabil laufen.</li>
          <li>Testen Sie die Reconnect-Signalisierung getrennt von gewöhnlichen Netzwerk-Reconnects.</li>
          <li>Halten Sie in Interoperabilitätsberichten die exakte Spezifikationsrevision und Anwendungsversionen fest.</li>
        </ol>

        <h2 id="v1-v2">Wie v2 mit der ursprünglichen Enhanced-RTMP-Arbeit zusammenhängt</h2>
        <p>Die erste Enhanced-RTMP-Spezifikation hat die Richtung für moderne Video-Codecs und HDR festgelegt und damit praktische HEVC- und AV1-Workflows über RTMP ermöglicht. V2 erweitert das Modell deutlich um zusätzliche Audio-/Video-Fähigkeiten, Multitrack, Reconnect-Verhalten, Timing-Verbesserungen und umfassendere Metadaten.</p>
        <p>Für einen einfacheren konzeptionellen Vergleich lesen Sie <a href="/de/guides/rtmp-vs-enhanced-rtmp/">RTMP vs. E-RTMP</a>. Für codecbezogene Tests siehe <a href="/de/guides/enhanced-rtmp-hevc-av1-opus/">HEVC, AV1 und Opus in Enhanced RTMP</a>.</p>

        <div class="cta compact-cta">
          <h2>Gegen die Spezifikation implementieren</h2>
          <p>Nutzen Sie das Release-Dokument von Veovera für normatives Verhalten und die Statustabelle von OpenRTMP für den aktuell implementierten Codepfad.</p>
          <div class="hero-actions" style="margin-bottom:0;">
            <a href="https://veovera.org/docs/enhanced/enhanced-rtmp-v2.html" target="_blank" rel="noopener" class="btn btn-primary">E-RTMP-v2-Spezifikation lesen</a>
            <a href="https://github.com/OpenRTMP/librtmp2#implementation-status" target="_blank" rel="noopener" class="btn btn-ghost">OpenRTMP-Status</a>
          </div>
        </div>
      </article>

      <aside class="toc-card" aria-label="Auf dieser Seite">
        <strong>Auf dieser Seite</strong>
        <a href="#why">Warum E-RTMP</a>
        <a href="#no-new-handshake">Immer noch RTMP</a>
        <a href="#fourcc">FourCC</a>
        <a href="#multitrack">Multitrack</a>
        <a href="#reconnect">Reconnect</a>
        <a href="#timing">Timing</a>
        <a href="#compatibility">Kompatibilität</a>
        <a href="#openrtmp">OpenRTMP</a>
        <a href="#test">Tests</a>
      </aside>
    </div>
  </section>
</main>

<?php include_once __DIR__ . '/../../../includes/footer.php'; ?>
