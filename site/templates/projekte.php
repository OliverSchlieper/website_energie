<?php snippet('header') ?>

<section class="projekte-grid">
  <?php foreach ($page->children()->listed() as $projekt): ?>
    <?php snippet('projekt-card', ['projekt' => $projekt]) ?>
  <?php endforeach ?>
</section>

<?php snippet('footer') ?>
