<?php
$page = 'showcase';
$pageTitle = 'OpenRTMP Showcase — Projects, deployments, and integrations built on OpenRTMP';
$pageDescription = 'See what is built on OpenRTMP: verified interoperability with real RTMP tooling, and community projects, deployments, and integrations. Add your own.';
$canonicalPath = '/showcase/';
include_once __DIR__ . '/../includes/header.php';

require_once __DIR__ . '/../includes/showcase-entries.php';

$interopEntries = [
  [
    'name' => 'OBS Studio',
    'tag' => 'Publisher',
    'description' => 'RTMP and RTMPS publishing, including HEVC and AV1 via Enhanced RTMP FourCC signaling, tested against real OBS output configurations.',
    'url' => '/guides/hevc-streaming-obs/',
  ],
  [
    'name' => 'FFmpeg',
    'tag' => 'Publisher & player',
    'description' => 'Ingest and playback interop scripts publish and pull real H.264/AAC and Enhanced RTMP (HEVC/AV1) streams through librtmp2 in CI.',
    'url' => 'https://github.com/OpenRTMP/librtmp2/blob/main/tests/interop/ffmpeg_interop.sh',
  ],
  [
    'name' => 'MediaMTX',
    'tag' => 'Server interop',
    'description' => 'Play-side interop test pulls a stream that MediaMTX receives from ffmpeg, verifying librtmp2\'s client against a real third-party RTMP server.',
    'url' => 'https://github.com/OpenRTMP/librtmp2/blob/main/tests/interop/play_interop.sh',
  ],
  [
    'name' => 'NOALBS',
    'tag' => 'Monitoring',
    'description' => 'NOALBS can read OpenRTMP\'s native JSON statistics endpoint directly, with an nginx-compatible XML fallback for older setups.',
    'url' => '/guides/openrtmp-noalbs-json-stats/',
  ],
];
?>

<main>
  <div class="page-hero container">
    <span class="eyebrow">Showcase</span>
    <h1>Built on OpenRTMP</h1>
    <p>What OpenRTMP has been verified to work with, and what the community has built with it. OpenRTMP is active alpha software, so this page grows as real deployments do &mdash; add yours below.</p>
  </div>

  <section style="padding-top: 0;">
    <div class="container">
      <div class="section-head">
        <span class="eyebrow">Tested, not claimed</span>
        <h2>Verified interoperability</h2>
        <p>These are tools OpenRTMP is actually tested against, in the <a href="https://github.com/OpenRTMP/librtmp2/tree/main/tests/interop">librtmp2 interop suite</a> or in a published guide &mdash; not a list of companies or logos.</p>
      </div>
      <div class="grid-2 guide-grid">
        <?php foreach ($interopEntries as $entry): ?>
        <article class="card guide-card">
          <span class="guide-tag"><?php echo htmlspecialchars($entry['tag'], ENT_QUOTES, 'UTF-8'); ?></span>
          <h2><?php echo htmlspecialchars($entry['name'], ENT_QUOTES, 'UTF-8'); ?></h2>
          <p><?php echo htmlspecialchars($entry['description'], ENT_QUOTES, 'UTF-8'); ?></p>
          <a href="<?php echo htmlspecialchars($entry['url'], ENT_QUOTES, 'UTF-8'); ?>" class="text-link"<?php echo strpos($entry['url'], 'http') === 0 ? ' target="_blank" rel="noopener"' : ''; ?>>See the evidence &rarr;</a>
        </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section>
    <div class="container">
      <div class="section-head">
        <span class="eyebrow">From the community</span>
        <h2>Community projects and deployments</h2>
        <p>Real projects, deployments, and integrations that OpenRTMP users have built and are willing to have named here.</p>
      </div>

      <?php if (empty($showcaseEntries)): ?>
      <div class="callout">
        <strong>Nobody has added their project yet &mdash; be the first.</strong>
        If you are running OpenRTMP in production, embedding <code>librtmp2</code> in your own application, or built an integration on top of it, this section is for you.
      </div>
      <?php else: ?>
      <div class="grid-2 guide-grid">
        <?php foreach ($showcaseEntries as $entry): ?>
        <article class="card guide-card">
          <span class="guide-tag"><?php echo htmlspecialchars($entry['tag'], ENT_QUOTES, 'UTF-8'); ?></span>
          <h2><a href="<?php echo htmlspecialchars($entry['url'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener"><?php echo htmlspecialchars($entry['name'], ENT_QUOTES, 'UTF-8'); ?></a></h2>
          <p><?php echo htmlspecialchars($entry['description'], ENT_QUOTES, 'UTF-8'); ?></p>
        </article>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <div class="cta compact-cta" style="margin-top: 32px;">
        <h2>Add your project to the showcase</h2>
        <p>Running OpenRTMP somewhere real? Open a submission and it will be added here &mdash; no marketing copy required, just what you actually built.</p>
        <div class="hero-actions" style="margin-bottom:0;">
          <a href="https://github.com/OpenRTMP/community/issues/new?template=showcase_submission.yml" target="_blank" rel="noopener" class="btn btn-primary">Submit your project</a>
          <a href="https://github.com/OpenRTMP/openrtmp.org/blob/main/includes/showcase-entries.php" target="_blank" rel="noopener" class="btn btn-ghost">Open a pull request instead</a>
        </div>
      </div>
    </div>
  </section>
</main>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
