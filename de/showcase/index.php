<?php
$lang = 'de';
$page = 'showcase';
$pageTitle = 'OpenRTMP-Showcase — Projekte, Deployments und Integrationen auf Basis von OpenRTMP';
$pageDescription = 'Was auf OpenRTMP aufbaut: geprüfte Interoperabilität mit echten RTMP-Werkzeugen sowie Projekte, Deployments und Integrationen aus der Community. Fügen Sie Ihr eigenes hinzu.';
$canonicalPath = '/de/showcase/';
include_once __DIR__ . '/../../includes/header.php';

require __DIR__ . '/../../includes/showcase-entries.php';

$interopEntries = [
  [
    'name' => 'OBS Studio',
    'tag' => 'Publisher',
    'description' => 'Publishing über RTMP und RTMPS, einschließlich HEVC und AV1 per Enhanced-RTMP-FourCC-Signalisierung, getestet mit echten OBS-Ausgabekonfigurationen.',
    'url' => '/de/guides/hevc-streaming-obs/',
  ],
  [
    'name' => 'FFmpeg',
    'tag' => 'Publisher & Player',
    'description' => 'Interop-Skripte für Ingest und Wiedergabe senden und empfangen in der CI echte H.264/AAC- und Enhanced-RTMP-Streams (HEVC/AV1) über librtmp2.',
    'url' => 'https://github.com/OpenRTMP/librtmp2/blob/main/tests/interop/ffmpeg_interop.sh',
  ],
  [
    'name' => 'MediaMTX',
    'tag' => 'Server-Interop',
    'description' => 'Ein Interop-Test auf Wiedergabeseite holt einen Stream ab, den MediaMTX von ffmpeg empfängt, und prüft so den Client von librtmp2 gegen einen echten RTMP-Server eines Drittanbieters.',
    'url' => 'https://github.com/OpenRTMP/librtmp2/blob/main/tests/interop/play_interop.sh',
  ],
  [
    'name' => 'NOALBS',
    'tag' => 'Monitoring',
    'description' => 'NOALBS kann den nativen JSON-Statistik-Endpunkt von OpenRTMP direkt lesen, mit nginx-kompatiblem XML als Fallback für ältere Setups.',
    'url' => '/de/guides/openrtmp-noalbs-json-stats/',
  ],
];
?>

<main>
  <div class="page-hero container">
    <span class="eyebrow">Showcase</span>
    <h1>Gebaut mit OpenRTMP</h1>
    <p>Womit OpenRTMP nachweislich funktioniert und was die Community damit gebaut hat. OpenRTMP ist Software in aktiver Alpha-Phase, daher wächst diese Seite mit den realen Deployments &mdash; fügen Sie Ihres unten hinzu.</p>
  </div>

  <section style="padding-top: 0;">
    <div class="container">
      <div class="section-head">
        <span class="eyebrow">Getestet, nicht behauptet</span>
        <h2>Geprüfte Interoperabilität</h2>
        <p>Das sind Werkzeuge, gegen die OpenRTMP tatsächlich getestet wird &mdash; in der <a href="https://github.com/OpenRTMP/librtmp2/tree/main/tests/interop">Interop-Suite von librtmp2</a> oder in einer veröffentlichten Anleitung. Keine Liste von Firmen oder Logos.</p>
      </div>
      <div class="grid-2 guide-grid">
        <?php foreach ($interopEntries as $entry): ?>
        <article class="card guide-card">
          <span class="guide-tag"><?php echo htmlspecialchars($entry['tag'], ENT_QUOTES, 'UTF-8'); ?></span>
          <h2><?php echo htmlspecialchars($entry['name'], ENT_QUOTES, 'UTF-8'); ?></h2>
          <p><?php echo htmlspecialchars($entry['description'], ENT_QUOTES, 'UTF-8'); ?></p>
          <a href="<?php echo htmlspecialchars($entry['url'], ENT_QUOTES, 'UTF-8'); ?>" class="text-link"<?php echo str_starts_with($entry['url'], 'http') ? ' target="_blank" rel="noopener"' : ''; ?>>Zum Nachweis &rarr;</a>
        </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section>
    <div class="container">
      <div class="section-head">
        <span class="eyebrow">Aus der Community</span>
        <h2>Community-Projekte und Deployments</h2>
        <p>Echte Projekte, Deployments und Integrationen, die OpenRTMP-Nutzer gebaut haben und hier genannt sehen möchten.</p>
      </div>

      <?php if (empty($showcaseEntries)): ?>
      <div class="callout">
        <strong>Noch hat niemand sein Projekt eingetragen &mdash; seien Sie die oder der Erste.</strong>
        Wenn Sie OpenRTMP produktiv betreiben, <code>librtmp2</code> in Ihre eigene Anwendung einbetten oder eine Integration darauf aufgebaut haben, ist dieser Bereich für Sie.
      </div>
      <?php else: ?>
      <div class="grid-2 guide-grid">
        <?php foreach ($showcaseEntries as $entry): ?>
        <article class="card guide-card">
          <span class="guide-tag"><?php echo htmlspecialchars($entry['tag'], ENT_QUOTES, 'UTF-8'); ?></span>
          <h2><a href="<?php echo htmlspecialchars($entry['url'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener"><?php echo htmlspecialchars($entry['name'], ENT_QUOTES, 'UTF-8'); ?></a></h2>
          <p lang="en"><?php echo htmlspecialchars($entry['description'], ENT_QUOTES, 'UTF-8'); ?></p>
        </article>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <div class="cta compact-cta" style="margin-top: 32px;">
        <h2>Ihr Projekt im Showcase eintragen</h2>
        <p>Sie betreiben OpenRTMP im echten Einsatz? Reichen Sie es ein, dann wird es hier ergänzt &mdash; ohne Marketingtext, nur das, was Sie tatsächlich gebaut haben.</p>
        <div class="hero-actions" style="margin-bottom:0;">
          <a href="https://github.com/OpenRTMP/community/issues/new?template=showcase_submission.yml" target="_blank" rel="noopener" class="btn btn-primary">Projekt einreichen</a>
          <a href="https://github.com/OpenRTMP/openrtmp.org/blob/main/includes/showcase-entries.php" target="_blank" rel="noopener" class="btn btn-ghost">Stattdessen einen Pull Request öffnen</a>
        </div>
      </div>
    </div>
  </section>
</main>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>
