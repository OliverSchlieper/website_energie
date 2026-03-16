<?php snippet('header') ?>

<main class="about-page">

  <?php
    $headline  = $page->pageHeadline()->or($page->title());
    $intro     = $page->pageIntro();
    $p1Name    = $page->person1Name();
    $p1Role    = $page->person1Role();
    $p1Bio     = $page->person1Bio();
    $p1Image   = $page->person1Image()->toFile();
    $p1Email   = $page->person1Email();
    $p1Link    = $page->person1Link();

    $p2Name    = $page->person2Name();
    $p2Role    = $page->person2Role();
    $p2Bio     = $page->person2Bio();
    $p2Image   = $page->person2Image()->toFile();
    $p2Email   = $page->person2Email();
    $p2Link    = $page->person2Link();
  ?>

  <!-- Hero Header -->
  <header class="about-header">
    <h1><?= $headline->esc() ?></h1>
    <?php if ($intro->isNotEmpty()): ?>
      <p class="about-intro"><?= $intro->kt() ?></p>
    <?php endif ?>
  </header>

  <!-- Person Cards -->
  <div class="about-persons">

    <!-- Person 1 -->
    <?php if ($p1Name->isNotEmpty()): ?>
    <article class="person-card person-card--left">
      <div class="person-image">
        <?php if ($p1Image): ?>
          <img src="<?= $p1Image->url() ?>" alt="<?= $p1Name->esc() ?>">
        <?php else: ?>
          <div class="person-image-placeholder"></div>
        <?php endif ?>
      </div>
      <div class="person-info">
        <?php if ($p1Role->isNotEmpty()): ?>
          <span class="person-role"><?= $p1Role->esc() ?></span>
        <?php endif ?>
        <h2><?= $p1Name->esc() ?></h2>
        <?php if ($p1Bio->isNotEmpty()): ?>
          <div class="person-bio"><?= $p1Bio->kt() ?></div>
        <?php endif ?>
        <div class="person-links">
          <?php if ($p1Email->isNotEmpty()): ?>
            <a href="mailto:<?= $p1Email->esc() ?>" class="person-link">✉ <?= $p1Email->esc() ?></a>
          <?php endif ?>
          <?php if ($p1Link->isNotEmpty()): ?>
            <a href="<?= $p1Link->esc() ?>" class="person-link" target="_blank" rel="noopener">↗ Website</a>
          <?php endif ?>
        </div>
      </div>
    </article>
    <?php endif ?>

    <!-- Person 2 -->
    <?php if ($p2Name->isNotEmpty()): ?>
    <article class="person-card person-card--right">
      <div class="person-image">
        <?php if ($p2Image): ?>
          <img src="<?= $p2Image->url() ?>" alt="<?= $p2Name->esc() ?>">
        <?php else: ?>
          <div class="person-image-placeholder"></div>
        <?php endif ?>
      </div>
      <div class="person-info">
        <?php if ($p2Role->isNotEmpty()): ?>
          <span class="person-role"><?= $p2Role->esc() ?></span>
        <?php endif ?>
        <h2><?= $p2Name->esc() ?></h2>
        <?php if ($p2Bio->isNotEmpty()): ?>
          <div class="person-bio"><?= $p2Bio->kt() ?></div>
        <?php endif ?>
        <div class="person-links">
          <?php if ($p2Email->isNotEmpty()): ?>
            <a href="mailto:<?= $p2Email->esc() ?>" class="person-link">✉ <?= $p2Email->esc() ?></a>
          <?php endif ?>
          <?php if ($p2Link->isNotEmpty()): ?>
            <a href="<?= $p2Link->esc() ?>" class="person-link" target="_blank" rel="noopener">↗ Website</a>
          <?php endif ?>
        </div>
      </div>
    </article>
    <?php endif ?>

  </div>

</main>

<?php snippet('footer') ?>
