<footer class="footer">
  <div class="container">
    <?php if ($site->footer_text()->isNotEmpty()): ?>
        <?= $site->footer_text()->kt() ?>
    <?php else: ?>
        <p>Hochschule München © <?= date('Y') ?> (Text im Dashboard bearbeitbar)</p>
    <?php endif; ?>
  </div>
</footer>