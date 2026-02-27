<section class="hero-block">
  <div class="hero-text">
    <h1><?= $block->title() ?></h1>
    <p class="subtitle"><?= $block->subtitle() ?></p>
    
    <?php if($link = $block->button_link()->toUrl()): ?>
      <a href="<?= $link ?>" class="cta-button">
        <?= $block->button_text()->or('Jetzt bestellen') ?>
      </a>
    <?php endif ?>
  </div>

  <div class="hero-image">
    <?php if ($image = $block->cover()->toFile()): ?>
      <img src="<?= $image->url() ?>" alt="Buchcover von <?= $block->title() ?>">
    <?php endif ?>
  </div>
</section>