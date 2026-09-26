<?php
$lang = 'de';
$page = 'download';
$pageTitle = 'OpenRTMP herunterladen — Rust-Crate, Quellcode und Docker-Images';
$pageDescription = 'librtmp2 von crates.io installieren, OpenRTMP-Projekte aus dem Quellcode bauen oder RTMP-Server und Web-Panel aus veröffentlichten Docker-Images betreiben.';
$canonicalPath = '/de/download/';
include_once __DIR__ . '/../../includes/header.php';
?>

<main>
  <div class="page-hero container">
    <span class="eyebrow">Crate &middot; Quellcode &middot; Docker</span>
    <h1>OpenRTMP herunterladen und bereitstellen</h1>
    <p>Wählen Sie die Protokollbibliothek, wenn Sie eine Anwendung entwickeln, oder nutzen Sie die veröffentlichten Server- und Panel-Images für einen selbst gehosteten RTMP-Stack.</p>
  </div>

  <section style="padding-top: 0;">
    <div class="container">
      <div class="callout warning">
        <strong>OpenRTMP befindet sich in aktiver Entwicklung und ist noch vor Version 1.0.</strong> Prüfen Sie den Implementierungsstand jedes Repositorys, pinnen Sie getestete Versionen und validieren Sie Ihren kompletten Workflow vor kritischem Produktiveinsatz.
      </div>

      <div class="grid-2" style="margin-bottom: 42px;">
        <article class="card path-card">
          <div class="icon">&#128225;</div>
          <h2>Server + Panel betreiben</h2>
          <p>Nutzen Sie den eigenständigen Compose-Stack für die schnellste Evaluierung. Er lädt veröffentlichte Images und benötigt weder Quellcode-Checkouts noch eine Rust-Toolchain.</p>
          <div class="card-actions">
            <a href="/de/quickstart/" class="btn btn-primary">Schnellstart in fünf Minuten</a>
            <a href="https://github.com/OpenRTMP/librtmp2-server-panel/blob/main/compose.quickstart.yml" target="_blank" rel="noopener" class="btn btn-ghost">Compose-Datei ansehen</a>
          </div>
        </article>
        <article class="card path-card">
          <div class="icon">&#128736;&#65039;</div>
          <h2>Rust-Bibliothek einbinden</h2>
          <p>Fügen Sie <code>librtmp2</code> von crates.io hinzu – für eigene RTMP-Server, Clients, Relays, Plugins und Protokollwerkzeuge.</p>
          <div class="card-actions">
            <a href="https://crates.io/crates/librtmp2" target="_blank" rel="noopener" class="btn btn-primary">crates.io öffnen</a>
            <a href="https://docs.rs/librtmp2" target="_blank" rel="noopener" class="btn btn-ghost">docs.rs öffnen</a>
          </div>
        </article>
      </div>

      <div class="download-grid">
        <div class="download-card download-card--wide" id="cargo">
          <h3>&#128230; librtmp2 mit Cargo installieren</h3>
          <p>Lassen Sie Cargo das aktuelle Release wählen und committen Sie bei Anwendungen die <code>Cargo.lock</code>. Bibliotheken sollten einen expliziten Kompatibilitätsbereich passend zum getesteten Release festlegen.</p>
          <pre><code>cargo add librtmp2
cargo build
cargo test</code></pre>
          <p>RTMPS/TLS ist standardmäßig aktiviert. So bauen Sie ohne das optionale OpenSSL-basierte TLS-Feature:</p>
          <pre><code>cargo add librtmp2 --no-default-features</code></pre>
          <p>Maßgeblich ist das auf <a href="https://crates.io/crates/librtmp2" target="_blank" rel="noopener">crates.io</a> angezeigte Release – kopieren Sie keine Versionsnummer von dieser Website.</p>
        </div>

        <div class="download-card">
          <h3>&#9881;&#65039; librtmp2 aus dem Quellcode bauen</h3>
          <pre><code>git clone https://github.com/OpenRTMP/librtmp2.git
cd librtmp2
cargo build --release
cargo test</code></pre>
          <p>Das Repository dokumentiert den codegenauen Implementierungsstand für klassisches RTMP, E-RTMP v1/v2, Client-Verhalten, TLS und Interoperabilitätstests.</p>
        </div>

        <div class="download-card">
          <h3>&#128268; librtmp2-server aus dem Quellcode bauen</h3>
          <pre><code>git clone https://github.com/OpenRTMP/librtmp2-server.git
cd librtmp2-server
cargo build --release
# Optionales HA-Clustering:
# cargo build --release --features cluster
cp .env.example .env
LRTMP2_DB=./server.db ./target/release/librtmp2-server</code></pre>
          <p>Das API-Token wird beim ersten Start erzeugt, sofern es nicht als echte Prozess-Umgebungsvariable <code>LRTMP2_API_TOKEN</code> übergeben wird. Die Docker-Images enthalten das Feature <code>cluster</code> bereits; Clustering bleibt zur Laufzeit aus, bis Sie <code>CLUSTER_ENABLED=true</code> setzen. Siehe die <a href="/de/guides/rtmp-server-ha-clustering/">Anleitung zum HA-Clustering</a>.</p>
        </div>

        <div class="download-card download-card--wide" id="docker-stack">
          <h3>&#128051; Docker Compose: Server + Panel + Redis</h3>
          <p>Der empfohlene Evaluierungsweg nutzt die eigenständige Compose-Datei aus dem Panel-Repository:</p>
          <pre><code>git clone https://github.com/OpenRTMP/librtmp2-server-panel.git
cd librtmp2-server-panel

# .env mit LRTMP2_API_TOKEN, PASSWORD und SECRET_KEY anlegen.
docker compose -f compose.quickstart.yml up -d</code></pre>
          <p>Folgen Sie dem <a href="/de/quickstart/">Schnellstart</a> für sichere Secret-Erzeugung, OBS-Einstellungen, Health-Checks, Fehlerbehebung und die Produktions-Checkliste.</p>
        </div>

        <div class="download-card" id="docker-server">
          <h3>&#128051; Server-Image</h3>
          <p>Für mehrere Architekturen veröffentlicht unter:</p>
          <pre><code>ghcr.io/openrtmp/librtmp2-server</code></pre>
          <p>Verwenden Sie für getestete Deployments ein Release-Tag. Bewegliche Tags wie <code>latest</code>, <code>beta</code> und <code>alpha</code> sind praktisch zur Evaluierung, können sich aber bei einem automatischen Redeploy unbemerkt ändern.</p>
        </div>

        <div class="download-card" id="docker-panel">
          <h3>&#128051; Panel-Image</h3>
          <p>Für mehrere Architekturen veröffentlicht unter:</p>
          <pre><code>ghcr.io/openrtmp/librtmp2-server-panel</code></pre>
          <p>Das Panel benötigt eine erreichbare Server-API, das passende API-Token, einen öffentlichen RTMP-Hostnamen, Zugangsdaten und ein Flask-Session-Secret.</p>
        </div>

        <div class="download-card download-card--wide" id="server-only">
          <h3>&#128225; Server-Image allein betreiben</h3>
          <pre><code>LRTMP2_API_TOKEN="$(openssl rand -hex 32)"
printf 'API-Token notieren: %s\n' "${LRTMP2_API_TOKEN}"

docker run -d \
  --name librtmp2-server \
  -p 1935:1935 \
  -p 8080:8080 \
  -e LRTMP2_API_TOKEN="${LRTMP2_API_TOKEN}" \
  -v librtmp2-server-data:/data \
  ghcr.io/openrtmp/librtmp2-server:latest</code></pre>
          <p>Der Server speichert ein übergebenes Token, statt eines auszugeben – notieren Sie also den ausgegebenen Wert. Lassen Sie <code>-e LRTMP2_API_TOKEN</code> weg, finden Sie das erzeugte Token in den Logs des ersten Starts. Pinnen Sie für reproduzierbare Deployments das Image-Tag.</p>
        </div>

        <div class="download-card download-card--wide" id="panel-only">
          <h3>&#127912; Panel gegen einen bestehenden Server betreiben</h3>
          <pre><code>docker run -d \
  --name librtmp2-server-panel \
  -p 8000:8000 \
  -e LRTMP2_API_URL=http://server.internal:8080 \
  -e LRTMP2_STATS_URL=https://api.example.com \
  -e LRTMP2_DOMAIN=stream.example.com \
  -e LRTMP2_API_TOKEN=&lt;passendes-server-token&gt; \
  -e USERNAME=admin \
  -e PASSWORD=&lt;starkes-passwort&gt; \
  -e SECRET_KEY=&lt;zufaelliges-session-secret&gt; \
  ghcr.io/openrtmp/librtmp2-server-panel:latest</code></pre>
        </div>
      </div>

      <div class="cta" style="margin-top: 48px;">
        <h2>Unsicher, welche Komponente Sie brauchen?</h2>
        <p>Die Startseite trennt Bibliotheks- und Betreiberweg, die Anleitungen behandeln Docker, OBS, RTMPS, HA-Clustering und Enhanced RTMP.</p>
        <div class="hero-actions" style="margin-bottom:0;">
          <a href="/de/#paths" class="btn btn-primary">Weg wählen</a>
          <a href="/de/guides/" class="btn btn-ghost">Anleitungen ansehen</a>
          <a href="/de/docs/" class="btn btn-ghost">Referenzdoku</a>
        </div>
      </div>
    </div>
  </section>
</main>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>
