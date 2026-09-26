<?php
$lang = 'de';
$page = 'docs';
$pageTitle = 'Dokumentation — OpenRTMP';
$pageDescription = 'Einstieg in librtmp2, librtmp2-server und librtmp2-server-panel: Cargo-Builds, Host-Callbacks, Docker-Deployment, REST-API, optionales HA-Clustering und das Web-Panel.';
$canonicalPath = '/de/docs/';
include_once __DIR__ . '/../../includes/header.php';
?>

<main>
  <div class="page-hero container">
    <h1>Dokumentation</h1>
    <p>Alles, was Sie brauchen, um <code>librtmp2</code> in eine Medien-Pipeline einzubetten, den Referenzserver zu betreiben oder den kompletten Stack mit Docker und dem Web-Panel bereitzustellen.</p>
  </div>

  <section style="padding-top: 0;">
    <div class="container docs-layout">

      <aside class="docs-nav">
        <h4>Auf dieser Seite</h4>
        <ul>
          <li><a href="#status">Projektstatus</a></li>
          <li><a href="#getting-started">Erste Schritte</a></li>
          <li><a href="#state-machine">Verbindungs-Zustandsautomat</a></li>
          <li><a href="#callbacks">Host-Callbacks</a></li>
          <li><a href="#layers">Modulreferenz</a></li>
          <li><a href="#server">librtmp2-server</a></li>
          <li><a href="#cluster">HA-Clustering</a></li>
          <li><a href="#panel">librtmp2-server-panel</a></li>
          <li><a href="#docker">Docker-Deployment</a></li>
          <li><a href="#abi">API &amp; Versionierung</a></li>
        </ul>
        <h4>Repositories</h4>
        <ul>
          <li><a href="https://github.com/OpenRTMP/librtmp2" target="_blank" rel="noopener">librtmp2</a></li>
          <li><a href="https://github.com/OpenRTMP/librtmp2-server" target="_blank" rel="noopener">librtmp2-server</a></li>
          <li><a href="https://github.com/OpenRTMP/librtmp2-server-panel" target="_blank" rel="noopener">librtmp2-server-panel</a></li>
        </ul>
      </aside>

      <div class="docs-content">

        <div class="callout callout-warn" id="status">
          <strong>Aktive Entwicklung.</strong> Alle drei Projekte (<code>librtmp2</code>, <code>librtmp2-server</code>, <code>librtmp2-server-panel</code>) sind noch vor Version 1.0. APIs, Docker-Images und Konfiguration können sich zwischen Releases ändern. Pinnen Sie getestete Versionen und validieren Sie Ihren OBS/FFmpeg-Workflow, bevor Sie sich bei kritischen Streams darauf verlassen. Maßgeblich für aktuelle Versionen sind crates.io und GitHub Releases.
        </div>

        <h2 id="getting-started">Erste Schritte</h2>
        <p><code>librtmp2</code> ist eine <strong>Protokollbibliothek</strong> &mdash; sie dekodiert RTMP und Enhanced RTMP (E-RTMP v1/v2) auf Leitungsebene und ruft Ihre Anwendung über Callbacks auf. Sie enthält bewusst keinen HTTP-Server, keine Authentifizierungs-Policy und keine Stream-Speicherung. Wenn Sie statt der eingebetteten Crate einen sofort lauffähigen Endpunkt möchten, nutzen Sie <a href="#server">librtmp2-server</a>.</p>
        <p>Fügen Sie <code>librtmp2</code> von <a href="https://crates.io/crates/librtmp2" target="_blank" rel="noopener">crates.io</a> Ihrer <code>Cargo.toml</code> hinzu. Solange <code>librtmp2</code> auf <code>0.x</code> bleibt, verwenden Sie einen lockeren <code>0.x</code>-Bereich, wenn Sie regelmäßige Updates möchten, und aktualisieren Sie die Lockfile für Sicherheitsfixes (RTMPS/TLS über das Standard-Feature <code>tls</code>):</p>
        <pre><code>[dependencies.librtmp2]
version = "0"

cargo update -p librtmp2   # neueste 0.x-Version von crates.io holen</code></pre>
        <p>Für reproduzierbare Builds ersetzen Sie den lockeren Bereich durch das exakt getestete Release. Verwenden Sie die auf <a href="https://crates.io/crates/librtmp2" target="_blank" rel="noopener">crates.io</a> angezeigte Version, statt eine „aktuelle“ Versionsnummer von dieser Seite zu kopieren.</p>
        <p>In Cargo gibt es kein <code>version = "latest"</code> &mdash; <code>Cargo.lock</code> pinnt die aufgelöste Version immer, bis Sie <code>cargo update</code> ausführen.</p>
        <p>Alternativ für die Entwicklung lokal klonen und bauen (erfordert Rust 1.93+):</p>
        <pre><code>git clone https://github.com/OpenRTMP/librtmp2.git
cd librtmp2
cargo build --release
cargo test</code></pre>
        <p>Minimales Server-Beispiel &mdash; lauschen, Publish akzeptieren, Frames loggen (<code>examples/minimal_server.rs</code>):</p>
        <pre><code>use librtmp2::server::Server;
use librtmp2::types::*;

fn on_frame(frame: &Frame) {
    println!("frame: size={}", frame.size);
}

let config = ServerConfig {
    max_connections: 16,
    chunk_size: 4096,
    tls_enabled: 0,
    tls_cert_file: std::ptr::null(),
    tls_key_file: std::ptr::null(),
    tls_ca_file: std::ptr::null(),
    tls_insecure: 0,
};

let mut server = Server::new(config)?;
server.on_frame_cb = Some(on_frame);
server.listen("0.0.0.0:1935")?;

while running {
    server.poll(100)?;
}</code></pre>
        <p>Für Sanitizer-Builds während der Entwicklung:</p>
        <pre><code>RUSTFLAGS="-Z sanitizer=address" cargo test
RUSTFLAGS="-Z sanitizer=undefined" cargo test</code></pre>

        <h2 id="state-machine">Verbindungs-Zustandsautomat</h2>
        <p>Jede Verbindung durchläuft eine feste Folge von Zuständen, während Handshake, Capability-Aushandlung und Stream-Lebenszyklus fortschreiten:</p>
        <pre><code>TCP_ACCEPTED &rarr; HANDSHAKE &rarr; CONNECTED &rarr; [CAPS_NEGOTIATED] &rarr; APP_CONNECTED &rarr; STREAM_CREATED &rarr; PUBLISHING | PLAYING &rarr; CLOSING &rarr; CLOSED</code></pre>
        <p><code>CAPS_NEGOTIATED</code> ist der E-RTMP-v2-Schritt zum Austausch der Capabilities zwischen <code>CONNECTED</code> und <code>APP_CONNECTED</code>. Klassische RTMP- und E-RTMP-v1-Gegenstellen überspringen ihn komplett.</p>

        <h2 id="callbacks">Host-Callbacks</h2>
        <p>Die Bibliothek berührt niemals Speicherung, Authentifizierung oder Transcoding &mdash; sie dekodiert die Leitung und ruft Ihre Anwendung auf. In Rust weisen Sie Callbacks am <code>Server</code>- oder <code>Client</code>-Objekt zu (z.&nbsp;B. <code>server.on_publish_cb = Some(...)</code>):</p>
        <div class="table-wrap">
          <table>
            <thead><tr><th>Callback</th><th>Wird ausgelöst, wenn</th></tr></thead>
            <tbody>
              <tr><td><code>on_connect</code></td><td>die Gegenstelle den RTMP-Befehl <code>connect</code> abschließt</td></tr>
              <tr><td><code>on_publish</code></td><td>ein Publisher einen Stream-Key anfordert &mdash; <code>false</code> zurückgeben, um abzulehnen</td></tr>
              <tr><td><code>on_play</code></td><td>ein Zuschauer die Wiedergabe eines Streams anfordert &mdash; <code>false</code> zurückgeben, um abzulehnen</td></tr>
              <tr><td><code>on_frame</code></td><td>ein grenzgeprüfter Audio-/Video-/Script-<code>Frame</code> bereitsteht</td></tr>
              <tr><td><code>on_close</code></td><td>die Verbindung aus beliebigem Grund abgebaut wird</td></tr>
            </tbody>
          </table>
        </div>

        <h2 id="layers">Schichtenreferenz</h2>
        <p>Eingehende Daten durchlaufen neun Schichten von unten nach oben, bevor sie Ihre Callbacks erreichen. Das vollständige Diagramm finden Sie in der <a href="/de/#architecture">Architekturübersicht</a> auf der Startseite. Wichtige Verzeichnisse in <code>src/</code>:</p>
        <div class="table-wrap">
          <table>
            <thead><tr><th>Verzeichnis</th><th>Zuständigkeit</th></tr></thead>
            <tbody>
              <tr><td><code>core/</code></td><td>Alloc-Hook, wachsende Puffer, Byte-Helfer, Logging, Fehler</td></tr>
              <tr><td><code>handshake/</code></td><td>C0/C1/C2 &harr; S0/S1/S2, Pufferung bei Teil-Reads, Versionserkennung</td></tr>
              <tr><td><code>chunk/</code></td><td>chunk_reader/writer, chunk_state pro csid, SetChunkSize/Abort</td></tr>
              <tr><td><code>message/</code></td><td>Dispatch zusammengesetzter Nachrichten &amp; AMF-Befehls-Dekodierung/-Kodierung</td></tr>
              <tr><td><code>amf/</code></td><td>AMF0 (verpflichtend) und AMF3 (optional)</td></tr>
              <tr><td><code>flv/</code></td><td>Parsing von FLV-Audio-/Video-/Script-Tags</td></tr>
              <tr><td><code>ertmp/</code></td><td>E-RTMP v1 (ExVideo/ExAudio, FourCC, HDR) + v2 (capsEx, Reconnect, Multitrack, ModEx)</td></tr>
              <tr><td><code>session/</code></td><td>Verbindungsobjekt, Zustandsautomat, Stream-Verwaltung</td></tr>
              <tr><td><code>server/</code> &amp; <code>client/</code></td><td>Accept-Schleife / Poll pro Verbindung &middot; ausgehendes Connect &amp; Publish/Play</td></tr>
            </tbody>
          </table>
        </div>

        <h2 id="server">librtmp2-server</h2>
        <p><a href="https://github.com/OpenRTMP/librtmp2-server" target="_blank" rel="noopener">librtmp2-server</a> ist der RTMP/E-RTMP-<strong>Referenz-Medienserver</strong> auf Basis von <code>librtmp2</code>. Er ist ein eigenes Repository und eigenes Binary &mdash; die Bibliothek selbst bleibt frei von Server-Schleife, Socket-Policy und Speicherentscheidungen.</p>
        <p>Im Unterschied zu einer reinen <code>librtmp2</code>-Integration ergänzt der Server Funktionen der Anwendungsschicht:</p>
        <div class="table-wrap">
          <table>
            <thead><tr><th>Funktion</th><th>Was sie tut</th></tr></thead>
            <tbody>
              <tr><td>RTMP-Listener</td><td>nimmt Publisher und Player auf <code>RTMP_BIND</code> (Standard <code>0.0.0.0:1935</code>) über den integrierten <code>librtmp2</code>-Server an</td></tr>
              <tr><td>RTMPS-Listener</td><td>optionaler zweiter TLS-Listener auf <code>RTMPS_BIND</code> (<code>1936</code>) <em>zusätzlich</em> zu unverschlüsseltem RTMP, wenn <code>TLS_ENABLED=true</code></td></tr>
              <tr><td>SQLite-Persistenz</td><td>Streams, Publisher, Player und Statistiken werden in <code>LRTMP2_DB</code> gespeichert</td></tr>
              <tr><td>Keys pro Stream</td><td>automatisch erzeugte <code>publish_key</code>, <code>play_key</code> und <code>stats_key</code> &mdash; keine öffentliche Stream-Liste ohne den exakten Key</td></tr>
              <tr><td>REST-API</td><td>Stream-CRUD unter <code>/api/v1/streams</code> mit Bearer-Token-Authentifizierung (axum, Port <code>8080</code>)</td></tr>
              <tr><td>Statistik-Endpunkte</td><td><code>/stats?key=&lt;stats_key&gt;</code> (JSON) und <code>/stats-nginx?key=&lt;stats_key&gt;</code> (nginx-rtmp-kompatibles XML)</td></tr>
              <tr><td>Frame-Relay</td><td>leitet Publisher-Frames GOP-bewusst an alle passenden Player weiter</td></tr>
              <tr><td>HA-Clustering</td><td>optionaler Multi-Node-Modus (<code>CLUSTER_ENABLED</code>, standardmäßig aus) mit OpenRaft-Zustandsreplikation und Media-Mesh &mdash; siehe <a href="#cluster">HA-Clustering</a></td></tr>
            </tbody>
          </table>
        </div>
        <p>Nativ bauen und starten (Standalone):</p>
        <pre><code>git clone https://github.com/OpenRTMP/librtmp2-server.git
cd librtmp2-server
cargo build --release
cp .env.example .env
LRTMP2_DB=./server.db ./target/release/librtmp2-server</code></pre>
        <p>Beim ersten Start erzeugt der Server ein API-Bearer-Token, speichert es in SQLite und gibt es einmalig auf stderr aus. Verwenden Sie dieses Token für <code>Authorization: Bearer &lt;token&gt;</code> bei REST-API-Aufrufen.</p>
        <p><strong>Mit OBS streamen:</strong> Server <code>rtmp://&lt;host&gt;/live</code>, Stream-Key = der von <code>POST /api/v1/streams</code> zurückgegebene <code>publish_key</code>.</p>
        <pre><code>curl -X POST http://localhost:8080/api/v1/streams \
  -H "Authorization: Bearer &lt;api-token&gt;" \
  -H "Content-Type: application/json" \
  -d '{"id":"mystream","name":"My Live Stream","app":"live"}'</code></pre>

        <h2 id="cluster">HA-Clustering</h2>
        <p>Ab Server <code>0.2.0</code> kann <code>librtmp2-server</code> als Multi-Node-Cluster laufen. Clustering ist <strong>standardmäßig aus</strong>; mit <code>CLUSTER_ENABLED=false</code> bleibt das Standalone-Verhalten unverändert. Die veröffentlichten Docker-Images werden mit dem Cargo-Feature <code>cluster</code> gebaut; zur Laufzeit ist dennoch Standalone der Standard.</p>
        <p>Die Architektur in Kürze:</p>
        <div class="table-wrap">
          <table>
            <thead><tr><th>Ebene</th><th>Standard-Port</th><th>Aufgabe</th></tr></thead>
            <tbody>
              <tr><td>Control</td><td><code>1940</code></td><td>OpenRaft-RPC, Join/Admin, Heartbeats, StatsProxy</td></tr>
              <tr><td>Media</td><td><code>1941</code></td><td>Frame-Relay zwischen Nodes, Subscribe, Init-Cache</td></tr>
              <tr><td>RTMP / HTTP</td><td><code>1935</code> / <code>8080</code></td><td>Client-Publish/-Play und Admin-API (unverändert)</td></tr>
            </tbody>
          </table>
        </div>
        <ul>
          <li>Eine SQLite-DB pro Node; dauerhafte Änderungen an Streams, Zuschauern, Tokens und Ownership laufen über Raft.</li>
          <li>Kein zentraler Media-Proxy und kein verpflichtendes Postgres/Redis für die Cluster-Ebene.</li>
          <li>Publisher-Ownership nutzt Epoch-Fencing; Player auf Nicht-Owner-Nodes erhalten Medien über das Mesh.</li>
          <li>Peer-Authentifizierung per Shared Secret; optional mTLS für Control und Media.</li>
        </ul>
        <p>Minimaler Bootstrap (erster Voter):</p>
        <pre><code>CLUSTER_ENABLED=true
CLUSTER_NODE_ID=1
CLUSTER_BOOTSTRAP=true
CLUSTER_SECRET=&lt;langes-zufaelliges-secret&gt;
CLUSTER_ADVERTISE_ADDR=10.0.0.1:1940
CLUSTER_MEDIA_ADVERTISE_ADDR=10.0.0.1:1941</code></pre>
        <p>Weitere Nodes treten mit leerer Datenbank und <code>CLUSTER_JOIN=&lt;bestehende-control-adresse&gt;</code> bei; anschließend werden Learner über <code>POST /api/v1/cluster/nodes/{id}/promote</code> zu Votern befördert.</p>
        <p>Authentifizierte Cluster-APIs umfassen <code>GET /api/v1/cluster</code>, <code>/nodes</code>, <code>/streams</code> sowie Drain/Resume/Promote/Remove. Vollständige Konfiguration, Einschränkungen und Betriebshinweise stehen in <a href="https://github.com/OpenRTMP/librtmp2-server/blob/main/docs/clustering.md" target="_blank" rel="noopener"><code>docs/clustering.md</code></a> im Server-Repository und in der Anleitung <a href="/de/guides/rtmp-server-ha-clustering/">HA-RTMP-Cluster betreiben</a>.</p>
        <p>Native Builds mit Clustering müssen mit dem Feature kompiliert werden:</p>
        <pre><code>cargo build --release --features cluster</code></pre>

        <h2 id="panel">librtmp2-server-panel</h2>
        <p><a href="https://github.com/OpenRTMP/librtmp2-server-panel" target="_blank" rel="noopener">librtmp2-server-panel</a> ist eine Flask-Web-UI, die mit der REST-API des Servers spricht. Es implementiert selbst kein RTMP &mdash; es verwaltet Streams, kopiert URLs und fragt Live-Statistiken ab.</p>
        <div class="table-wrap">
          <table>
            <thead><tr><th>Funktion</th><th>Beschreibung</th></tr></thead>
            <tbody>
              <tr><td>Stream-Verwaltung</td><td>Streams über <code>/api/v1/streams</code> anlegen und löschen</td></tr>
              <tr><td>Kopieren mit einem Klick</td><td>Publish-URL, Stream-Key, Play-URL und Statistik-URL</td></tr>
              <tr><td>Live-Statistiken</td><td>Bitrate, Auflösung, Codec, Laufzeit, RTT, abgefragt von <code>/stats?key=...</code></td></tr>
              <tr><td>Cluster-UI</td><td>wenn Health <code>cluster.enabled=true</code> meldet: Quorum-Übersicht, Node Drain/Resume/Remove, Stream-Owner/Epoch-Platzierung</td></tr>
              <tr><td>Login-Sperre</td><td>optionaler Admin-Login (standardmäßig <code>REQUIRE_LOGIN=True</code>)</td></tr>
              <tr><td>Sicherheit</td><td>CSRF-Schutz, Rate-Limiting (in Docker Redis-gestützt), verschlüsselte Key-Anzeige</td></tr>
            </tbody>
          </table>
        </div>
        <p>Das Panel nimmt nicht an Raft teil. Richten Sie es auf einen beliebigen gesunden, synchronisierten Node; dauerhafte Admin-Schreibvorgänge werden innerhalb des Clusters weitergeleitet. Bei Standalone-Servern wird die Cluster-Navigation automatisch ausgeblendet.</p>
        <p>Wichtige Umgebungsvariablen (siehe <code>.env.example</code> im Panel-Repository):</p>
        <div class="table-wrap">
          <table>
            <thead><tr><th>Variable</th><th>Beschreibung</th></tr></thead>
            <tbody>
              <tr><td><code>LRTMP2_API_URL</code></td><td>Basis-URL der Server-HTTP-API (intern; z.&nbsp;B. <code>http://openrtmp-server:8080</code> in Docker)</td></tr>
              <tr><td><code>LRTMP2_API_TOKEN</code></td><td>Bearer-Token &mdash; muss dem API-Token des Servers entsprechen</td></tr>
              <tr><td><code>LRTMP2_DOMAIN</code></td><td>öffentlicher Host bzw. IP für die angezeigten RTMP-URLs</td></tr>
              <tr><td><code>LRTMP2_STATS_URL</code></td><td>vom Browser erreichbare Statistik-URL (Standard: <code>LRTMP2_API_URL</code>)</td></tr>
              <tr><td><code>PASSWORD</code> / <code>SECRET_KEY</code></td><td>Login-Passwort des Panels und Flask-Session-Secret</td></tr>
            </tbody>
          </table>
        </div>
        <p>Nach dem Start öffnen Sie das Panel unter <code>http://localhost:8000</code> (Standard-Zugangsdaten in <code>.env.example</code>: Benutzer <code>admin</code>).</p>

        <h2 id="docker">Docker-Deployment</h2>
        <p>Server und Panel veröffentlichen vorgebaute Images in der GitHub Container Registry. Copy-and-Paste-Befehle finden Sie auch auf der <a href="/de/download/">Download-Seite</a>.</p>

        <h3>Nur Server</h3>
        <p>Schnellster Weg &mdash; API-Token wird beim ersten Start automatisch erzeugt:</p>
        <pre><code>docker network create openrtmp   # gemeinsam mit den Panel-Beispielen unten

# Für RTMPS (TLS_ENABLED=true) zusätzlich -p 1936:1936 freigeben.
docker run -d \
  --name librtmp2-server \
  --network openrtmp \
  -p 1935:1935 \
  -p 8080:8080 \
  -v librtmp2-server-data:/data \
  ghcr.io/openrtmp/librtmp2-server:latest

docker logs librtmp2-server   # API-Token aus der Ausgabe des ersten Starts kopieren</code></pre>
        <p>Verfügbare Tags: <code>latest</code>, <code>beta</code>, <code>alpha</code> und gepinnte Versionen (zum Beispiel ein konkretes Release-Tag aus GitHub Releases).</p>

        <h3>Nur Panel (<code>docker run</code>)</h3>
        <p>Image: <code>ghcr.io/openrtmp/librtmp2-server-panel</code>. Mit einem bestehenden Server im selben Docker-Netzwerk verbinden (Containername <code>librtmp2-server</code>):</p>
        <pre><code>docker network create openrtmp   # überspringen, falls oben bereits angelegt

docker run -d \
  --name librtmp2-server-panel \
  --network openrtmp \
  -p 8000:8000 \
  -e LRTMP2_API_URL=http://librtmp2-server:8080 \
  -e LRTMP2_STATS_URL=http://localhost:8080 \
  -e LRTMP2_API_TOKEN=&lt;token-aus-den-server-logs&gt; \
  -e LRTMP2_DOMAIN=localhost \
  -e PASSWORD=&lt;panel-passwort&gt; \
  -e SECRET_KEY=&lt;zufaelliges-secret&gt; \
  ghcr.io/openrtmp/librtmp2-server-panel:latest</code></pre>
        <p>Läuft der Server direkt auf dem Host statt in Docker? Verwenden Sie <code>LRTMP2_API_URL=http://host.docker.internal:8080</code> (Windows/macOS).</p>

        <h3>Kompletter Stack (<code>docker run</code>)</h3>
        <p>Server + Panel + Redis ohne Compose. Setzen Sie vor dem ersten Serverstart ein gemeinsames API-Token:</p>
        <pre><code>export LRTMP2_API_TOKEN=$(openssl rand -hex 32)
export PANEL_PASSWORD="$(openssl rand -base64 24 | tr -d '\n')"
export PANEL_SECRET=$(python3 -c "import secrets; print(secrets.token_hex(32))")
printf 'Panel-Passwort notieren: %s\n' "${PANEL_PASSWORD}"

docker network create openrtmp

docker run -d --name librtmp2-panel-redis --network openrtmp redis:7-alpine

docker run -d \
  --name librtmp2-server \
  --network openrtmp \
  -p 1935:1935 -p 8080:8080 \
  -e LRTMP2_API_TOKEN=$LRTMP2_API_TOKEN \
  -e LRTMP2_DB=/data/server.db \
  -v librtmp2-server-data:/data \
  ghcr.io/openrtmp/librtmp2-server:latest

docker run -d \
  --name librtmp2-server-panel \
  --network openrtmp \
  -p 8000:8000 \
  -e LRTMP2_API_URL=http://librtmp2-server:8080 \
  -e LRTMP2_STATS_URL=http://localhost:8080 \
  -e LRTMP2_API_TOKEN=$LRTMP2_API_TOKEN \
  -e LRTMP2_DOMAIN=localhost \
  -e PASSWORD=$PANEL_PASSWORD \
  -e SECRET_KEY=$PANEL_SECRET \
  -e RATELIMIT_STORAGE_URI=redis://librtmp2-panel-redis:6379/0 \
  ghcr.io/openrtmp/librtmp2-server-panel:latest</code></pre>

        <h3>Kompletter Stack (<code>docker compose</code>)</h3>
        <p>Die <code>compose.quickstart.yml</code> im Panel-Repository startet dieselben drei Dienste aus den veröffentlichten Images. Setzen Sie die Secrets in <code>.env</code> <em>vor</em> dem ersten Start, damit der Server das gemeinsame API-Token übernimmt:</p>
        <pre><code>git clone https://github.com/OpenRTMP/librtmp2-server-panel.git
cd librtmp2-server-panel
cp .env.example .env
# LRTMP2_API_TOKEN, PASSWORD, SECRET_KEY, LRTMP2_DOMAIN setzen
docker compose -f compose.quickstart.yml up -d</code></pre>
        <p>Die Standard-<code>docker-compose.yml</code> des Repositorys baut den Server dagegen aus einem benachbarten <code>../librtmp2-server</code>-Checkout; nutzen Sie sie nur für die Entwicklung am Quellcode.</p>
        <p>Standardmäßig freigegebene Ports (aus <code>librtmp2-server-panel/docker-compose.yml</code> und <code>librtmp2-server/.env.example</code>):</p>
        <div class="table-wrap">
          <table>
            <thead><tr><th>Port</th><th>Dienst</th></tr></thead>
            <tbody>
              <tr><td><code>1935</code></td><td>RTMP-Ingest / -Wiedergabe (<code>RTMP_BIND</code>)</td></tr>
              <tr><td><code>1936</code></td><td>RTMPS-Ingest / -Wiedergabe (<code>RTMPS_BIND</code>) &mdash; nur bei <code>TLS_ENABLED=true</code>; in der Standard-Compose-Datei nicht freigegeben (dort <code>1936:1936</code> einkommentieren)</td></tr>
              <tr><td><code>1940</code></td><td>Cluster-Control-Plane (<code>CLUSTER_BIND</code>) &mdash; nur bei <code>CLUSTER_ENABLED=true</code></td></tr>
              <tr><td><code>1941</code></td><td>Cluster-Media-Mesh (<code>CLUSTER_MEDIA_BIND</code>) &mdash; nur bei aktiviertem Clustering</td></tr>
              <tr><td><code>8080</code></td><td>HTTP-API, Statistiken, Health-Check (<code>HTTP_BIND</code>)</td></tr>
              <tr><td><code>8000</code></td><td>Web-Panel</td></tr>
            </tbody>
          </table>
        </div>
        <p>Um RTMPS zusätzlich zu unverschlüsseltem RTMP zu aktivieren, setzen Sie <code>LRTMP2_TLS_ENABLED=true</code> (oder <code>TLS_ENABLED=true</code> in <code>.env</code>), mounten Zertifikat- und Key-Dateien, geben Port <code>1936</code> frei und setzen <code>RTMPS_BIND=0.0.0.0:1936</code> wie in <code>librtmp2-server/docker-compose.yml</code>. Das Panel zeigt <code>rtmps://</code>-URLs nur an, wenn <code>GET /api/v1/health</code> <code>rtmps_enabled: true</code> meldet (und nutzt <code>LRTMP2_RTMPS_PORT</code>, Standard <code>1936</code>).</p>
        <p>Für einen Multi-Node-Cluster geben Sie <code>1940</code> und <code>1941</code> zwischen den Peers frei, setzen die unter <a href="#cluster">HA-Clustering</a> beschriebenen <code>CLUSTER_*</code>-Variablen und geben jedem Node ein eigenes SQLite-Volume. Bootstrap- und Join-Schritte stehen in der <a href="/de/guides/rtmp-server-ha-clustering/">Clustering-Anleitung</a>.</p>

        <h2 id="abi">API &amp; Versionierung</h2>
        <p>Nur die öffentliche Schnittstelle der Crate <code>librtmp2</code> ist als stabile API gedacht. Alles unter <code>src/**/*</code>, was nicht <code>pub</code> ist, kann sich zwischen Releases beliebig ändern.</p>
        <p><code>librtmp2</code> folgt SemVer und bleibt während der aktiven Entwicklung auf <code>0.x</code>. Bei <code>0.x</code>-Releases können Minor-Versionen inkompatible API-Änderungen enthalten; <code>1.0.0</code> markiert die erste stabile öffentliche API. Pinnen Sie das getestete crates.io-Release, wenn Sie von einer bestimmten API-Form abhängen.</p>
        <p><code>librtmp2-server</code> und <code>librtmp2-server-panel</code> sind ebenfalls vor 1.0 &mdash; Formen der REST-API, Namen von Umgebungsvariablen und Docker-Images können sich ändern. Bevorzugen Sie GitHub Releases und Image-Tags, statt Versionen von dieser Seite fest einzutragen.</p>

      </div>
    </div>
  </section>
</main>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>
