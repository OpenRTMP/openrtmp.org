<?php
$lang = 'de';
$page = 'guides';
$pageTitle = 'Enhanced RTMP mit HEVC, AV1 und Opus — OpenRTMP-Anleitung';
$pageDescription = 'Enhanced-RTMP-Mediensignalisierung für HEVC, AV1 und Opus verstehen: Unterschiede zu klassischem RTMP und die aktuellen Implementierungsgrenzen von OpenRTMP.';
$canonicalPath = '/de/guides/enhanced-rtmp-hevc-av1-opus/';
$ogType = 'article';
$structuredData = [
  '@context' => 'https://schema.org',
  '@type' => 'TechArticle',
  'headline' => 'Enhanced RTMP mit HEVC, AV1 und Opus',
  'description' => $pageDescription,
  'inLanguage' => 'de',
  'author' => ['@type' => 'Organization', 'name' => 'OpenRTMP'],
  'mainEntityOfPage' => 'https://openrtmp.org/de/guides/enhanced-rtmp-hevc-av1-opus/'
];
include_once __DIR__ . '/../../../includes/header.php';
?>

<main>
  <div class="page-hero container article-hero">
    <span class="eyebrow">E-RTMP &middot; HEVC &middot; AV1 &middot; Opus</span>
    <h1>Enhanced-RTMP-Codecs erklärt</h1>
    <p>Enhanced RTMP erweitert die Mediensignalisierung von klassischem RTMP/FLV, sodass moderne Codecs und umfangreichere Fähigkeiten über vertraute RTMP-Publishing-Workflows transportiert werden können.</p>
  </div>

  <section class="content-section" style="padding-top: 0;">
    <div class="container article-layout">
      <article class="prose">
        <div class="callout warning"><strong>Hinweis zur Implementierung:</strong> Codec-Passthrough, Parser-Module und vollständige Aushandlung auf Session-Ebene sind verschiedene Dinge. Prüfen Sie immer den aktuellen <a href="https://github.com/OpenRTMP/librtmp2#implementation-status" target="_blank" rel="noopener">Implementierungsstand</a>, bevor Sie sich auf eine Funktion verlassen.</div>

        <h2 id="legacy">Warum klassisches RTMP eine Erweiterung brauchte</h2>
        <p>Klassisches RTMP transportiert Medien üblicherweise mit Codec-Kennungen aus der FLV-Ära. Dieses Modell funktioniert gut für etablierte H.264/AAC-Workflows, beschreibt neuere Codecs wie HEVC, AV1 oder Opus aber nicht auf natürliche Weise.</p>
        <p>Enhanced RTMP führt erweiterte Audio-/Video-Signalisierung und FourCC-basierte Codec-Kennungen ein. So können Sender und Empfänger moderne Codec-Payloads unterscheiden, ohne sie als klassische FLV-Codecs auszugeben.</p>

        <h2 id="concepts">Zentrale Konzepte</h2>
        <h3>Erweiterte Video- und Audiopakete</h3>
        <p>Erweiterte Paketformen transportieren Informationen jenseits des kleinen klassischen Codec-Kennungsraums. Implementierungen müssen den erweiterten Header korrekt parsen, bevor sie codecspezifische Payloads interpretieren.</p>
        <h3>FourCC-Codec-Kennungen</h3>
        <p>Vier-Zeichen-Codes identifizieren Codecs wie HEVC oder AV1 auf eine Weise, die erweiterbarer ist als die ursprüngliche FLV-Aufzählung.</p>
        <h3>Capability-Aushandlung</h3>
        <p>Enhanced RTMP v2 ergänzt Strukturen, um unterstützte Fähigkeiten anzukündigen und auszuhandeln. Parser-Code für diese Strukturen zu haben ist nicht dasselbe, wie jeden Aushandlungspfad in einem produktiven Session-Zustandsautomaten abzuschließen.</p>
        <h3>Multitrack und ModEx</h3>
        <p>Spätere Erweiterungen beschreiben mehrere Medienspuren und modulare Metadaten. Anwendungen müssen entscheiden, wie diese Spuren auf ihre eigenen Modelle für Routing, Wiedergabe, Aufzeichnung und Statistiken abgebildet werden.</p>

        <h2 id="openrtmp">Wie OpenRTMP mit Enhanced RTMP umgeht</h2>
        <p>OpenRTMP trennt mehrere Schichten:</p>
        <ul>
          <li><strong>Parser-/Serializer-Module auf Leitungsebene:</strong> wiederverwendbare Strukturen für erweiterte Medien und Protokollerweiterungen.</li>
          <li><strong>Standard-Live-Session-Pfad:</strong> der Code, der tatsächlich genutzt wird, wenn sich Publisher und Player über die Bibliothek verbinden.</li>
          <li><strong>Server-Anwendungsschicht:</strong> Authentifizierung, Routing, Stream-Registry, Statistiken und API-Verhalten rund um die Bibliothek.</li>
        </ul>
        <p>Aktuelle OpenRTMP-Workflows können erweiterte Medien-Payloads von kompatiblen OBS/FFmpeg-Publishern weiterleiten. Manche fortgeschrittenen v2-Funktionen existieren möglicherweise als Bibliothekscode und Tests, ohne vollständig in jeden Standard-Live-Session-Pfad integriert zu sein. Maßgeblich ist die Statustabelle im Repository.</p>

        <h2 id="codecs">Erwartungen an Codecs</h2>
        <table>
          <thead><tr><th>Codec</th><th>Was zu prüfen ist</th></tr></thead>
          <tbody>
            <tr><td>H.264 + AAC</td><td>Klassische Initialisierungs-Frames, Kompatibilität von Publisher/Player und Wiedergabe bei spätem Beitritt</td></tr>
            <tr><td>HEVC</td><td>Erweiterte Videosignalisierung, Sequence-Start-Behandlung, Player-Unterstützung und tatsächliche Decoder-Verfügbarkeit</td></tr>
            <tr><td>AV1</td><td>Sender-Unterstützung, FourCC-Signalisierung, Decoder-Unterstützung und CPU/GPU-Anforderungen beim Client</td></tr>
            <tr><td>Opus</td><td>Erweiterte Audiosignalisierung und ob der empfangende Player Opus in diesem RTMP-Workflow versteht</td></tr>
          </tbody>
        </table>
        <p>Ein erfolgreicher Publish garantiert nicht, dass jeder nachgelagerte Player den Codec dekodieren kann. Testen Sie die komplette Kette Publisher &rarr; Server &rarr; Player.</p>

        <p>Für aufgabenorientierte Anleitungen siehe <a href="/de/guides/hevc-streaming-obs/">HEVC-Streaming mit OBS</a>, <a href="/de/guides/av1-over-rtmp/">AV1 über RTMP</a> und <a href="/de/guides/enhanced-rtmp-v2-explained/">Enhanced RTMP v2 erklärt</a>.</p>

        <h2 id="testing">Ein praktischer Interoperabilitäts-Testplan</h2>
        <ol>
          <li>Notieren Sie die exakte OBS- oder FFmpeg-Version und die Befehle/Einstellungen.</li>
          <li>Testen Sie einen Publisher und einen Player mit H.264/AAC als Basislinie.</li>
          <li>Ändern Sie immer nur einen Codec.</li>
          <li>Prüfen Sie die erste Wiedergabe und den späten Beitritt eines Players.</li>
          <li>Trennen Sie den Publisher und verbinden Sie ihn neu.</li>
          <li>Testen Sie mehrere Player und längeres Streaming.</li>
          <li>Prüfen Sie Server-Statistiken und Logs auf Codec-Erkennung.</li>
          <li>Wiederholen Sie alles über RTMPS, wenn verschlüsselter Ingest Teil des Deployments ist.</li>
        </ol>

        <h2 id="library">librtmp2 in der eigenen Anwendung nutzen</h2>
        <p>Entwickler können die Rust-Crate direkt nutzen oder die erzeugte dynamische/statische Bibliothek über das C-kompatible FFI verwenden. Die Host-Anwendung bleibt für Entscheidungen wie Autorisierung, Speicherung, Transcoding, Aufzeichnung und Track-Routing verantwortlich.</p>
        <pre><code>cargo add librtmp2</code></pre>
        <p>Lassen Sie Cargo das aktuelle Release wählen, statt eine Versionsnummer aus dieser Anleitung zu kopieren. Anwendungen sollten <code>Cargo.lock</code> committen; Bibliotheken sollten einen expliziten Kompatibilitätsbereich passend zum getesteten Release wählen und vor Updates die Release Notes lesen.</p>

        <h2 id="avoid-overclaiming">Diese verbreiteten Annahmen vermeiden</h2>
        <ul>
          <li>Parser-Unterstützung bedeutet nicht automatisch, dass eine Funktion im Standard-Session-Pfad verdrahtet ist.</li>
          <li>Opakes Medien-Relay bedeutet nicht, dass der Server jeden Codec transcodieren oder im Detail untersuchen kann.</li>
          <li>Unterstützung beim Publisher bedeutet nicht Unterstützung bei allen Playern.</li>
          <li>Multitrack-Protokollstrukturen ergeben nicht automatisch eine vollständige Multitrack-Steuerungs-API.</li>
          <li>Enhanced RTMP ersetzt keine Interoperabilitätstests über Versionen hinweg.</li>
        </ul>

        <div class="cta compact-cta">
          <h2>Den codegenauen Status prüfen</h2>
          <p>Das Repository dokumentiert, was vollständig, teilweise, nur als Parser oder noch nicht implementiert ist.</p>
          <div class="hero-actions" style="margin-bottom:0;">
            <a href="https://github.com/OpenRTMP/librtmp2#implementation-status" target="_blank" rel="noopener" class="btn btn-primary">Implementierungsstand</a>
            <a href="https://docs.rs/librtmp2" target="_blank" rel="noopener" class="btn btn-ghost">docs.rs</a>
          </div>
        </div>
      </article>

      <aside class="toc-card" aria-label="Auf dieser Seite">
        <strong>Auf dieser Seite</strong>
        <a href="#legacy">Warum Enhanced RTMP</a>
        <a href="#concepts">Zentrale Konzepte</a>
        <a href="#openrtmp">Verhalten von OpenRTMP</a>
        <a href="#codecs">Erwartungen an Codecs</a>
        <a href="#testing">Testplan</a>
        <a href="#library">Bibliothek nutzen</a>
        <a href="#avoid-overclaiming">Verbreitete Annahmen</a>
      </aside>
    </div>
  </section>
</main>

<?php include_once __DIR__ . '/../../../includes/footer.php'; ?>
