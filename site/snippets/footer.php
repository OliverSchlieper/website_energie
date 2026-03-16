<?php
/*
  Snippets are a great way to store code snippets for reuse
  or to keep your templates clean.

  This footer snippet is reused in all templates.

  More about snippets:
  https://getkirby.com/docs/guide/templates/snippets
*/
?>
  </main>

  <footer class="footer">
    <div class="footer-content">
      <div class="footer-info">
        <h3><?= $site->footerHeadline()->or('Kontakt & Infos')->esc() ?></h3>
        <?php if ($site->footerText()->isNotEmpty()): ?>
          <div class="footer-text">
            <?= $site->footerText()->kt() ?>
          </div>
        <?php endif ?>
      </div>

      <?php $links = $site->footerLinks()->toStructure(); ?>
      <?php if ($links->isNotEmpty()): ?>
        <div class="footer-links">
          <h3>Links</h3>
          <ul>
            <?php foreach ($links as $link): ?>
              <li><a href="<?= $link->linkUrl() ?>"><?= $link->linkText()->esc() ?></a></li>
            <?php endforeach ?>
          </ul>
        </div>
      <?php endif ?>

      <?php $socials = $site->socialLinks()->toStructure(); ?>
      <?php if ($socials->isNotEmpty()): ?>
        <div class="footer-social">
          <h3>Social Media</h3>
          <ul>
            <?php foreach ($socials as $social): ?>
              <li><a href="<?= $social->platformUrl() ?>" target="_blank" rel="noopener noreferrer"><?= $social->platformName()->esc() ?></a></li>
            <?php endforeach ?>
          </ul>
        </div>
      <?php endif ?>
    </div>
    
    <div class="footer-bottom">
      <p>&copy; <?= date('Y') ?> <?= $site->title()->esc() ?>. Alle Rechte vorbehalten.</p>
    </div>
  </footer>

  <?= js([
    'assets/js/prism.js',
    'assets/js/lightbox.js',
    'assets/js/index.js',
    '@auto'
  ]) ?>

</body>
</html>
