<?php
$lang = 'de';
$page = 'guides';
$pageTitle = 'HA-RTMP-Server-Cluster betreiben — OpenRTMP-Anleitung';
$pageDescription = 'Optionales Multi-Node-HA-Clustering in librtmp2-server aktivieren: OpenRaft-Zustandsreplikation, Media-Mesh-Ports, Bootstrap und Join, Panel-Bedienung und aktuelle Betriebsgrenzen.';
$canonicalPath = '/de/guides/rtmp-server-ha-clustering/';
$ogType = 'article';
$structuredData = [
  '@context' => 'https://schema.org',
  '@type' => 'TechArticle',
  'headline' => 'HA-RTMP-Server-Cluster betreiben',
  'description' => $pageDescription,
  'inLanguage' => 'de',
  'author' => ['@type' => 'Organization', 'name' => 'OpenRTMP'],
  'mainEntityOfPage' => 'https://openrtmp.org/de/guides/rtmp-server-ha-clustering/'
];
include_once __DIR__ . '/../../../includes/header.php';
?>

<main>
  <div class="page-hero container article-hero">
    <span class="eyebrow">HA &middot; Clustering &middot; OpenRaft</span>
    <h1>HA-RTMP-Server-Cluster betreiben</h1>
    <p>Der optionale Multi-Node-Modus repliziert dauerhaften Stream-Zustand mit OpenRaft und leitet Live-Medien über ein Media-Mesh zwischen Peers weiter. Der Standalone-Betrieb mit einem Node bleibt der Standard.</p>
  </div>

  <section class="content-section" style="padding-top: 0;">
    <div class="container article-layout">
      <article class="prose">
        <div class="callout warning"><strong>Status des Clusterings:</strong> Clustering kam mit Server und Panel <code>0.2.0</code> und sollte weiterhin sorgfältig validiert werden. Testen Sie Failover, Publisher-Reconnect und Firewall-Pfade gründlich. Eine ungültige Cluster-Konfiguration lässt den Start hart fehlschlagen — es gibt keinen stillen Rückfall auf Standalone.</div>

        <h2 id="what-it-is">Was Clustering bietet</h2>
        <p>Mit <code>CLUSTER_ENABLED=true</code> behält jeder Node seine eigene SQLite-Datei, während dauerhafte Änderungen (Streams, Zuschauer, Tokens, Ownership) über Raft laufen. Live-Frames verlassen den Publisher-Owner über das Media-Mesh, sodass sich Player mit anderen gesunden Nodes verbinden können.</p>
        <ul>
          <li>Replizierte Stream-Registry ohne zentralen Media-Proxy und ohne verpflichtendes Postgres/Redis</li>
          <li>Publisher-Ownership mit Epoch-Fencing und quorumbewusster Fehlererkennung</li>
          <li>Lastbasierte Drain/Resume-Zulassung auf den Nodes</li>
          <li>Peer-Authentifizierung per Shared Secret, optional mTLS für Control und Media</li>
        </ul>
        <p>Die veröffentlichten Docker-Images werden bereits mit <code>--features cluster</code> kompiliert. Native Builds brauchen das Cargo-Feature explizit. Zur Laufzeit gilt weiterhin <code>CLUSTER_ENABLED=false</code> als Standard.</p>

        <h2 id="ports">Ebenen und Ports</h2>
        <table>
          <thead><tr><th>Ebene</th><th>Standard</th><th>Zweck</th></tr></thead>
          <tbody>
            <tr><td>Control</td><td><code>1940/tcp</code></td><td>Raft, Join/Admin, Heartbeats, StatsProxy</td></tr>
            <tr><td>Media</td><td><code>1941/tcp</code></td><td>Frame-Relay, Subscribe, Init-Cache</td></tr>
            <tr><td>RTMP</td><td><code>1935/tcp</code></td><td>Publisher- und Player-Verbindungen (unverändert)</td></tr>
            <tr><td>HTTP-API</td><td><code>8080/tcp</code></td><td>Admin-REST-API und Health</td></tr>
          </tbody>
        </table>
        <p>Geben Sie <code>1940</code> und <code>1941</code> zwischen den Cluster-Peers frei. Halten Sie die Admin-API eingeschränkt; für die RTMP-Freigabe gelten dieselben Regeln wie bei einem Standalone-Deployment.</p>

        <h2 id="bootstrap">1. Ersten Voter bootstrappen</h2>
        <p>Starten Sie den ersten Node mit einem Shared Secret (mindestens 16 Zeichen) und veröffentlichen Sie Adressen, die Peers erreichen können:</p>
        <pre><code>CLUSTER_ENABLED=true
CLUSTER_NODE_ID=1
CLUSTER_BOOTSTRAP=true
CLUSTER_SECRET=&lt;langes-zufaelliges-secret&gt;
CLUSTER_BIND=0.0.0.0:1940
CLUSTER_MEDIA_BIND=0.0.0.0:1941
CLUSTER_ADVERTISE_ADDR=10.0.0.1:1940
CLUSTER_MEDIA_ADVERTISE_ADDR=10.0.0.1:1941</code></pre>
        <p>Bestehende Standalone-Streams, Zuschauer und das API-Token werden beim ersten Bootstrap in Raft übernommen. Docker-Mappings für einen Cluster-Node ergänzen typischerweise:</p>
        <pre><code>ports:
  - "1935:1935"
  - "8080:8080"
  - "1940:1940"
  - "1941:1941"</code></pre>

        <h2 id="join">2. Weitere Nodes hinzufügen</h2>
        <p>Jeder beitretende Node braucht eine <strong>leere</strong> Datenbank (ohne vorherigen <code>streams</code>- oder <code>raft_*</code>-Zustand) und dasselbe Secret. Ein frischer Beitritt braucht außerdem einen einmaligen Join-Proof — <code>CLUSTER_SECRET</code> allein kann keinen Learner mehr aufnehmen. Erzeugen Sie den Proof auf einem bestehenden Mitglied mit dem normalen API-Token und genau den Adressen, die der neue Node veröffentlichen wird:</p>
        <pre><code>curl -sS -X POST http://10.0.0.1:8080/api/v1/cluster/join-proof \
  -H "Authorization: Bearer &lt;api-token&gt;" \
  -H "Content-Type: application/json" \
  -d '{"node_id": 2, "control_addr": "10.0.0.2:1940", "media_addr": "10.0.0.2:1941"}'</code></pre>
        <p>Die Antwort enthält einen <code>proof</code>-String. Starten Sie den beitretenden Node damit und mit den Adressen, für die der Proof erzeugt wurde:</p>
        <pre><code>CLUSTER_ENABLED=true
CLUSTER_NODE_ID=2
CLUSTER_JOIN=10.0.0.1:1940
CLUSTER_JOIN_PROOF=&lt;join-proof&gt;
CLUSTER_SECRET=&lt;dasselbe-secret&gt;
CLUSTER_BIND=0.0.0.0:1940
CLUSTER_MEDIA_BIND=0.0.0.0:1941
CLUSTER_ADVERTISE_ADDR=10.0.0.2:1940
CLUSTER_MEDIA_ADVERTISE_ADDR=10.0.0.2:1941
LRTMP2_DB=/data/node2.db</code></pre>
        <p>Ein frischer Beitritt ohne <code>CLUSTER_JOIN_PROOF</code> wird abgelehnt, bevor die Join-Anfrage überhaupt gesendet wird. Der Proof ist an die Node-ID und beide veröffentlichten Adressen gebunden; erzeugen Sie also einen neuen, wenn sich einer dieser Werte ändert. Ein Neustart eines Nodes, der bereits lokalen Raft-Zustand hat, setzt fort statt neu beizutreten und braucht keinen neuen Proof.</p>
        <p>Beigetretene Nodes starten als Learner. Nach dem Aufholen befördern Sie sie zum Voter:</p>
        <pre><code>curl -X POST http://10.0.0.1:8080/api/v1/cluster/nodes/2/promote \
  -H "Authorization: Bearer &lt;api-token&gt;"</code></pre>
        <p>Kopieren Sie keine laufende SQLite-Datei von einem anderen Node, um damit beizutreten — das erzeugt widersprüchlichen Raft-Zustand. Zum Neubefüllen löschen Sie die DB-Dateien des Nodes, erzeugen einen frischen Join-Proof und treten erneut bei.</p>

        <h2 id="operate">3. Über API oder Panel betreiben</h2>
        <p>Nützliche authentifizierte Endpunkte:</p>
        <table>
          <thead><tr><th>Methode</th><th>Pfad</th><th>Zweck</th></tr></thead>
          <tbody>
            <tr><td>GET</td><td><code>/api/v1/cluster</code></td><td>Leader, Term, Quorum, Last</td></tr>
            <tr><td>GET</td><td><code>/api/v1/cluster/nodes</code></td><td>Peer-Liste und Health-Zustände</td></tr>
            <tr><td>GET</td><td><code>/api/v1/cluster/streams</code></td><td>Owner, Epoch, Mesh-Subscriptions</td></tr>
            <tr><td>POST</td><td><code>/api/v1/cluster/join-proof</code></td><td>Proof erzeugen, der einen frischen Node-Beitritt erlaubt</td></tr>
            <tr><td>POST</td><td><code>.../nodes/{id}/drain</code></td><td>Node als DRAINING markieren</td></tr>
            <tr><td>POST</td><td><code>.../nodes/{id}/resume</code></td><td>Node als READY markieren</td></tr>
            <tr><td>DELETE</td><td><code>.../nodes/{id}</code></td><td>Voter entfernen (gibt seine Ownerships frei)</td></tr>
          </tbody>
        </table>
        <p>Wenn Health <code>cluster.enabled=true</code> meldet, zeigt das Web-Panel eine Cluster-Seite mit Quorum-Status, Node-Aktionen (Drain/Resume/Remove) und Owner/Epoch-Platzierung pro Stream. Richten Sie das Panel auf einen beliebigen gesunden, synchronisierten Node — es nimmt nicht an Raft teil.</p>

        <h2 id="limits">Aktuelle Einschränkungen</h2>
        <ul class="check-list">
          <li>Nach einem Ausfall des Owners müssen sich Publisher neu mit einem öffentlichen RTMP-Endpunkt verbinden; eine automatische Publisher-Migration ist nicht vorgesehen.</li>
          <li>Peers, die mitten im Stream beitreten, brauchen Init-Cache / <code>stream_init_snapshot</code>, bevor die Wiedergabe funktioniert.</li>
          <li>Aktivieren Sie für produktive Peer-Verbindungen <code>CLUSTER_TLS_ENABLED</code> mit Zertifikat/Key/CA; die Zertifikats-Subjects müssen <code>lrtmp2-node-{id}</code> enthalten.</li>
          <li>Die Beförderung vom Learner zum Voter erfolgt explizit über die API, nicht automatisch bei jedem Beitritt.</li>
          <li>Betrachten Sie Clustering als HA auf Evaluierungsniveau, bis Sie Ihre Topologie Ende-zu-Ende validiert haben.</li>
        </ul>
        <p>Die maßgebliche Referenz für Betreiber ist <a href="https://github.com/OpenRTMP/librtmp2-server/blob/main/docs/clustering.md" target="_blank" rel="noopener"><code>docs/clustering.md</code></a> im Server-Repository (Englisch).</p>

        <div class="cta compact-cta">
          <h2>Standalone beginnen, dann Nodes ergänzen</h2>
          <p>Validieren Sie zuerst einen Docker-Stack mit einem Node. Aktivieren Sie Clustering erst, wenn RTMP-Publish/-Play und das Panel sauber funktionieren.</p>
          <div class="hero-actions" style="margin-bottom:0;">
            <a href="/de/quickstart/" class="btn btn-primary">Docker-Schnellstart</a>
            <a href="/de/docs/#cluster" class="btn btn-ghost">Cluster-Abschnitt der Doku</a>
          </div>
        </div>
      </article>

      <aside class="toc-card" aria-label="Auf dieser Seite">
        <strong>Auf dieser Seite</strong>
        <a href="#what-it-is">Was es bietet</a>
        <a href="#ports">Ports</a>
        <a href="#bootstrap">Bootstrap</a>
        <a href="#join">Nodes hinzufügen</a>
        <a href="#operate">Betrieb</a>
        <a href="#limits">Einschränkungen</a>
      </aside>
    </div>
  </section>
</main>

<?php include_once __DIR__ . '/../../../includes/footer.php'; ?>
