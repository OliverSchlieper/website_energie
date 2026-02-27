<?php snippet('header') ?>

<main>
  <?= $page->main_content()->toBlocks() ?>
</main>

<section style="padding: 50px; display: flex; flex-direction: column; gap: 20px; align-items: flex-start;">
  
  <h2>Button Test-Bereich</h2>

  <?php snippet('cta_button') ?>

  <?php snippet('cta_button', [
    'text' => 'Das Buch vorbestellen',
    'url'  => 'https://shop.hm.edu'
  ]) ?>

  <?php if($about = page('about')): ?>
    <?php snippet('cta_button', [
      'text' => 'Über den Autor',
      'url'  => $about->url()
    ]) ?>
  <?php endif ?>

</section>

<?php snippet('footer') ?>
