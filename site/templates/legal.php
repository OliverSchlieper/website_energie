<?php snippet('header') ?>

<?= css('assets/css/templates/legal.css') ?>

<main class="legal-page container">
  <div class="legal-content">
    <h1><?= $page->headline()->or($page->title())->esc() ?></h1>
    
    <div class="legal-sections">
      <?php 
      $sections = $page->legal_sections()->toStructure();
      $count = 0;
      foreach($sections as $section): 
        $count++;
      ?>
        <div class="legal-section-text">
          <?= $section->text() ?>
        </div>
        <?php if($count < $sections->count()): ?>
          <hr>
        <?php endif; ?>
      <?php endforeach; ?>
    </div>
  </div>
</main>

<?php snippet('footer') ?>
