<?php
$page = 'guides';
$pageTitle = 'Run an HA RTMP server cluster — OpenRTMP Guide';
$pageDescription = 'Enable optional multi-node HA clustering in librtmp2-server: OpenRaft state replication, media mesh ports, bootstrap and join, panel operations, and current alpha limits.';
$canonicalPath = '/guides/rtmp-server-ha-clustering/';
$ogType = 'article';
$structuredData = [
  '@context' => 'https://schema.org',
  '@type' => 'TechArticle',
  'headline' => 'Run an HA RTMP server cluster',
  'description' => $pageDescription,
  'author' => ['@type' => 'Organization', 'name' => 'OpenRTMP'],
  'mainEntityOfPage' => 'https://openrtmp.org/guides/rtmp-server-ha-clustering/'
];
include __DIR__ . '/../../includes/header.php';
?>

<main>
  <div class="page-hero container article-hero">
    <span class="eyebrow">HA &middot; Clustering &middot; OpenRaft</span>
    <h1>Run an HA RTMP server cluster</h1>
    <p>Optional multi-node mode replicates durable stream state with OpenRaft and relays live media between peers over a media mesh. Standalone single-node operation remains the default.</p>
  </div>

  <section class="content-section" style="padding-top: 0;">
    <div class="container article-layout">
      <article class="prose">
        <div class="callout warning"><strong>Alpha feature:</strong> clustering landed in server and panel <code>0.2.0</code>. Test failover, publisher reconnect, and firewall paths thoroughly. Invalid cluster configuration fails startup hard — there is no silent fallback to standalone.</div>

        <h2 id="what-it-is">What clustering provides</h2>
        <p>With <code>CLUSTER_ENABLED=true</code>, each node keeps its own SQLite file while durable mutations (streams, viewers, tokens, ownership) go through Raft. Live frames leave the publisher owner over the media mesh so players can attach to other healthy nodes.</p>
        <ul>
          <li>Replicated stream registry without a central media proxy or mandatory Postgres/Redis</li>
          <li>Publisher ownership with epoch fencing and quorum-aware failure detection</li>
          <li>Load-based drain/resume admission on nodes</li>
          <li>Shared-secret peer authentication, with optional mTLS for control and media</li>
        </ul>
        <p>Published Docker images already compile with <code>--features cluster</code>. Native builds need the Cargo feature explicitly. Runtime still defaults to <code>CLUSTER_ENABLED=false</code>.</p>

        <h2 id="ports">Planes and ports</h2>
        <table>
          <thead><tr><th>Plane</th><th>Default</th><th>Purpose</th></tr></thead>
          <tbody>
            <tr><td>Control</td><td><code>1940/tcp</code></td><td>Raft, join/admin, heartbeats, StatsProxy</td></tr>
            <tr><td>Media</td><td><code>1941/tcp</code></td><td>Frame relay, subscribe, init-cache</td></tr>
            <tr><td>RTMP</td><td><code>1935/tcp</code></td><td>Publisher and player connections (unchanged)</td></tr>
            <tr><td>HTTP API</td><td><code>8080/tcp</code></td><td>Admin REST API and health</td></tr>
          </tbody>
        </table>
        <p>Expose <code>1940</code> and <code>1941</code> between cluster peers. Keep the admin API restricted; RTMP exposure follows the same rules as a standalone deploy.</p>

        <h2 id="bootstrap">1. Bootstrap the first voter</h2>
        <p>Start the first node with a shared secret (at least 16 characters) and advertise addresses peers can dial:</p>
        <pre><code>CLUSTER_ENABLED=true
CLUSTER_NODE_ID=1
CLUSTER_BOOTSTRAP=true
CLUSTER_SECRET=&lt;long-random-secret&gt;
CLUSTER_BIND=0.0.0.0:1940
CLUSTER_MEDIA_BIND=0.0.0.0:1941
CLUSTER_ADVERTISE_ADDR=10.0.0.1:1940
CLUSTER_MEDIA_ADVERTISE_ADDR=10.0.0.1:1941</code></pre>
        <p>Existing standalone streams, viewers, and the API token are seeded into Raft on first bootstrap. Docker mappings for a clustered node typically add:</p>
        <pre><code>ports:
  - "1935:1935"
  - "8080:8080"
  - "1940:1940"
  - "1941:1941"</code></pre>

        <h2 id="join">2. Join additional nodes</h2>
        <p>Each joiner needs an <strong>empty</strong> database (no prior <code>streams</code> or <code>raft_*</code> state) and the same secret:</p>
        <pre><code>CLUSTER_ENABLED=true
CLUSTER_NODE_ID=2
CLUSTER_JOIN=10.0.0.1:1940
CLUSTER_SECRET=&lt;same-secret&gt;
LRTMP2_DB=/data/node2.db</code></pre>
        <p>Joined nodes start as learners. After catch-up, promote to voter:</p>
        <pre><code>curl -X POST http://10.0.0.1:8080/api/v1/cluster/nodes/2/promote \
  -H "Authorization: Bearer &lt;api-token&gt;"</code></pre>
        <p>Do not copy a live SQLite file from another node and join — that creates conflicting Raft state. To reseed, delete the node's DB files and join again.</p>

        <h2 id="operate">3. Operate from the API or panel</h2>
        <p>Useful authenticated endpoints:</p>
        <table>
          <thead><tr><th>Method</th><th>Path</th><th>Purpose</th></tr></thead>
          <tbody>
            <tr><td>GET</td><td><code>/api/v1/cluster</code></td><td>Leader, term, quorum, load</td></tr>
            <tr><td>GET</td><td><code>/api/v1/cluster/nodes</code></td><td>Peer list and health states</td></tr>
            <tr><td>GET</td><td><code>/api/v1/cluster/streams</code></td><td>Owner, epoch, mesh subscriptions</td></tr>
            <tr><td>POST</td><td><code>.../nodes/{id}/drain</code></td><td>Mark node DRAINING</td></tr>
            <tr><td>POST</td><td><code>.../nodes/{id}/resume</code></td><td>Mark node READY</td></tr>
            <tr><td>DELETE</td><td><code>.../nodes/{id}</code></td><td>Remove voter (releases its owners)</td></tr>
          </tbody>
        </table>
        <p>When health reports <code>cluster.enabled=true</code>, the web panel shows a Cluster page with quorum status, node actions (drain/resume/remove), and per-stream owner/epoch placement. Point the panel at any healthy synchronized node — it does not participate in Raft.</p>

        <h2 id="limits">Current limitations</h2>
        <ul class="check-list">
          <li>After owner failure, publishers must reconnect to a public RTMP endpoint; automatic publisher migration is not implied.</li>
          <li>Peers that join mid-stream need init-cache / <code>stream_init_snapshot</code> before playback works.</li>
          <li>Enable <code>CLUSTER_TLS_ENABLED</code> with cert/key/CA for production peer links; certificate subjects must embed <code>lrtmp2-node-{id}</code>.</li>
          <li>Learner-to-voter promotion is explicit via the API, not automatic on every join.</li>
          <li>Treat clustering as evaluation-grade HA until you have validated your topology end to end.</li>
        </ul>
        <p>The canonical operator reference is <a href="https://github.com/OpenRTMP/librtmp2-server/blob/main/docs/clustering.md" target="_blank" rel="noopener"><code>docs/clustering.md</code></a> in the server repository.</p>

        <div class="cta compact-cta">
          <h2>Start standalone, then add nodes</h2>
          <p>Validate a single-node Docker stack first. Enable clustering only after RTMP publish/play and the panel work cleanly.</p>
          <div class="hero-actions" style="margin-bottom:0;">
            <a href="/quickstart/" class="btn btn-primary">Docker quickstart</a>
            <a href="/docs/#cluster" class="btn btn-ghost">Cluster docs section</a>
          </div>
        </div>
      </article>

      <aside class="toc-card" aria-label="On this page">
        <strong>On this page</strong>
        <a href="#what-it-is">What it provides</a>
        <a href="#ports">Ports</a>
        <a href="#bootstrap">Bootstrap</a>
        <a href="#join">Join nodes</a>
        <a href="#operate">Operate</a>
        <a href="#limits">Limitations</a>
      </aside>
    </div>
  </section>
</main>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
