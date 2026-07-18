<?php
$page = 'guides';
$pageTitle = 'Configure an RTMPS server for OBS — OpenRTMP Guide';
$pageDescription = 'Enable RTMPS alongside RTMP in OpenRTMP, configure TLS certificate files and ports, verify server health, and connect OBS securely.';
$canonicalPath = '/guides/rtmps-server-obs/';
$ogType = 'article';
$structuredData = [
  '@context' => 'https://schema.org',
  '@type' => 'TechArticle',
  'headline' => 'Configure an RTMPS server for OBS',
  'description' => $pageDescription,
  'author' => ['@type' => 'Organization', 'name' => 'OpenRTMP'],
  'mainEntityOfPage' => 'https://openrtmp.org/guides/rtmps-server-obs/'
];
include __DIR__ . '/../../includes/header.php';
?>

<main>
  <div class="page-hero container article-hero">
    <span class="eyebrow">RTMPS &middot; TLS &middot; OBS</span>
    <h1>Configure an RTMPS server for OBS</h1>
    <p>OpenRTMP can run an encrypted RTMPS listener alongside normal RTMP. Both listeners share the same stream registry, authentication keys, relay core, and connection limits.</p>
  </div>

  <section class="content-section" style="padding-top: 0;">
    <div class="container article-layout">
      <article class="prose">
        <div class="callout warning"><strong>Experimental area:</strong> RTMPS support should be tested with your exact OBS, FFmpeg, certificate, reverse-proxy, and firewall setup before production use.</div>

        <h2 id="how-it-works">How RTMPS works in OpenRTMP</h2>
        <p>Enabling TLS starts an additional listener. It does not replace the plaintext RTMP listener. A publisher can connect over RTMPS while a player connects over RTMP, or the other way around, because both listeners use the same server instance.</p>
        <p>Typical ports are:</p>
        <table>
          <thead><tr><th>Protocol</th><th>Default example</th></tr></thead>
          <tbody>
            <tr><td>RTMP</td><td><code>1935/tcp</code></td></tr>
            <tr><td>RTMPS</td><td><code>1936/tcp</code></td></tr>
          </tbody>
        </table>
        <p>RTMPS is RTMP transported directly over TLS. It is not the same as placing an HTTPS reverse proxy in front of the HTTP API.</p>

        <h2 id="certificate">1. Prepare a certificate and private key</h2>
        <p>Use a certificate valid for the hostname publishers enter in OBS. The server needs readable paths to the certificate chain and matching private key.</p>
        <p>For a container deployment, mount the files read-only:</p>
        <pre><code>volumes:
  - /etc/letsencrypt/live/stream.example.com/fullchain.pem:/certs/fullchain.pem:ro
  - /etc/letsencrypt/live/stream.example.com/privkey.pem:/certs/privkey.pem:ro</code></pre>
        <p>Do not bake private keys into a public image or repository.</p>

        <h2 id="configuration">2. Enable the RTMPS listener</h2>
        <p>Set the server configuration values:</p>
        <pre><code>TLS_ENABLED=true
TLS_CERT_FILE=/certs/fullchain.pem
TLS_KEY_FILE=/certs/privkey.pem
RTMPS_BIND=0.0.0.0:1936</code></pre>
        <p>The normal <code>RTMP_BIND</code> remains active. Expose the additional port in Docker:</p>
        <pre><code>ports:
  - "1935:1935"
  - "1936:1936"</code></pre>
        <p>The server refuses to enable TLS when either certificate path is missing, which prevents a false-positive configuration where the health endpoint suggests encrypted ingest is available but the listener never started.</p>

        <h2 id="verify">3. Verify the server before changing OBS</h2>
        <p>Check the health endpoint:</p>
        <pre><code>curl https://api.example.com/api/v1/health</code></pre>
        <p>The response should indicate that RTMPS is enabled and identify the bound port. Also verify the certificate from the client network:</p>
        <pre><code>openssl s_client -connect stream.example.com:1936 \
  -servername stream.example.com</code></pre>
        <p>Look for a successful certificate chain and confirm that the hostname matches.</p>

        <h2 id="obs">4. Connect OBS over RTMPS</h2>
        <ol>
          <li>Open OBS <strong>Settings &rarr; Stream</strong>.</li>
          <li>Select a custom service.</li>
          <li>Use <code>rtmps://stream.example.com:1936/live</code> as the server.</li>
          <li>Use the OpenRTMP <code>publish_key</code> as the stream key.</li>
          <li>Start streaming and watch the server logs and panel statistics.</li>
        </ol>
        <p>The panel only displays RTMPS URLs when the server health endpoint reports that the TLS listener is active.</p>

        <h2 id="network">Network and certificate pitfalls</h2>
        <h3>Port 1936 is closed</h3>
        <p>Open the TCP port in the host firewall, cloud security group, Docker mapping, and any upstream network firewall.</p>
        <h3>The certificate is valid in a browser but OBS rejects it</h3>
        <p>Confirm that the full chain is supplied, not only the leaf certificate. Test from the same network and hostname OBS uses.</p>
        <h3>A TCP proxy terminates TLS first</h3>
        <p>Decide whether OpenRTMP or the proxy owns TLS. If the proxy terminates TLS, it must forward plaintext RTMP to the RTMP listener. If OpenRTMP owns TLS, use TCP passthrough rather than HTTP proxying.</p>
        <h3>The panel still shows only RTMP URLs</h3>
        <p>Verify that the panel can reach the current health endpoint, that RTMPS reports enabled, and that <code>LRTMP2_RTMPS_PORT</code> matches the public port.</p>

        <h2 id="security">Security recommendations</h2>
        <ul class="check-list">
          <li>Use a publicly trusted certificate for internet-facing publishers.</li>
          <li>Automate renewal and restart or reload the server after certificate replacement.</li>
          <li>Keep the private key readable only by the service account or container.</li>
          <li>Retain publish-key authentication even when transport is encrypted.</li>
          <li>Monitor failed handshakes and repeated authentication failures.</li>
          <li>Test both RTMP and RTMPS after every certificate or network change.</li>
        </ul>

        <div class="cta compact-cta">
          <h2>Start from the Docker deployment</h2>
          <p>Deploy the basic stack first, then add certificates and the second listener.</p>
          <div class="hero-actions" style="margin-bottom:0;">
            <a href="/quickstart/" class="btn btn-primary">Docker quickstart</a>
            <a href="/docs/#server" class="btn btn-ghost">Server reference</a>
          </div>
        </div>
      </article>

      <aside class="toc-card" aria-label="On this page">
        <strong>On this page</strong>
        <a href="#how-it-works">How it works</a>
        <a href="#certificate">Certificate</a>
        <a href="#configuration">Configuration</a>
        <a href="#verify">Verification</a>
        <a href="#obs">OBS setup</a>
        <a href="#network">Pitfalls</a>
        <a href="#security">Security</a>
      </aside>
    </div>
  </section>
</main>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
