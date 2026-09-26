<footer class="site-footer">
  <div class="container">
    <div class="footer-grid">
      <div>
        <div class="brand" style="margin-bottom: 14px;">
          <img src="/assets/img/favicon.svg" width="24" height="24" alt="<?php echo t('OpenRTMP logo'); ?>">
          OpenRTMP<span class="dot">.org</span>
        </div>
        <p style="max-width: 360px; margin: 0;">
          <?php echo t('Modern RTMP infrastructure for developers and stream operators: a Rust RTMP/E-RTMP library, a self-hosted RTMP/RTMPS server, REST API, live statistics, and a web control panel.'); ?>
        </p>
        <p class="status-note" style="max-width: 360px; margin-top: 14px;">
          <?php echo t('Active development, pre-1.0. Pin versions and validate your complete workflow before critical production use.'); ?>
        </p>
      </div>
      <div>
        <h4><?php echo t('Get started'); ?></h4>
        <ul>
          <li><a href="<?php echo lurl('/quickstart/'); ?>"><?php echo t('Five-minute Docker quickstart'); ?></a></li>
          <li><a href="<?php echo lurl('/download/'); ?>"><?php echo t('Download &amp; build'); ?></a></li>
          <li><a href="<?php echo lurl('/docs/'); ?>"><?php echo t('Documentation'); ?></a></li>
          <li><a href="<?php echo lurl('/guides/'); ?>"><?php echo t('Practical guides'); ?></a></li>
        </ul>
      </div>
      <div>
        <h4><?php echo t('Projects'); ?></h4>
        <ul>
          <li><a href="https://github.com/OpenRTMP/librtmp2" target="_blank" rel="noopener">librtmp2</a></li>
          <li><a href="https://github.com/OpenRTMP/librtmp2-server" target="_blank" rel="noopener">librtmp2-server</a></li>
          <li><a href="https://github.com/OpenRTMP/librtmp2-server-panel" target="_blank" rel="noopener">librtmp2-server-panel</a></li>
          <li><a href="<?php echo lurl('/docs/'); ?>#docker"><?php echo t('Docker deployment'); ?></a></li>
        </ul>
      </div>
      <div>
        <h4><?php echo t('Community'); ?></h4>
        <ul>
          <li><a href="<?php echo lurl('/showcase/'); ?>"><?php echo t('Showcase'); ?></a></li>
          <li><a href="https://github.com/OpenRTMP/community" target="_blank" rel="noopener"><?php echo t('Community hub'); ?></a></li>
          <li><a href="https://github.com/OpenRTMP/community/issues" target="_blank" rel="noopener"><?php echo t('Issue tracker'); ?></a></li>
          <li><a href="https://github.com/OpenRTMP/community/discussions" target="_blank" rel="noopener"><?php echo t('Discussions'); ?></a></li>
          <li><a href="https://github.com/OpenRTMP/.github/blob/main/CONTRIBUTING.md" target="_blank" rel="noopener"><?php echo t('Contributing'); ?></a></li>
          <li><a href="https://github.com/OpenRTMP" target="_blank" rel="noopener"><?php echo t('GitHub organization'); ?></a></li>
        </ul>
      </div>
    </div>
    <div class="footer-bottom">
      <div class="footer-meta">
        <span>&copy; <span id="year">2026</span> <?php echo t('OpenRTMP. Released under the MIT License.'); ?></span>
        <span class="footer-meta-sep" aria-hidden="true">&middot;</span>
        <a href="<?php echo lurl('/legal/'); ?>" class="footer-legal"><?php echo t('Legal Notice'); ?></a>
      </div>
      <div class="badge-row">
        <span class="badge">Rust + C FFI</span>
        <span class="badge">RTMP / RTMPS</span>
        <span class="badge">E-RTMP v1 / v2</span>
      </div>
    </div>
  </div>
</footer>

<script src="/assets/js/main.js"></script>
</body>
</html>
