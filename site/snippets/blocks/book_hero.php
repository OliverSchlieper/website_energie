<section class="book-hero">
  <div class="hero-container">
    
    <div class="hero-image">
      <?php if ($image = $block->cover()->toFile()): ?>
        <img src="<?= $image->url() ?>" alt="Cover: <?= $block->title()->esc() ?>">
      <?php else: ?>
        <div class="image-placeholder">Cover fehlt</div>
      <?php endif ?>
    </div>

    <div class="hero-content">
      <h1 class="hero-title"><?= $block->title()->esc() ?></h1>
      <p class="hero-subtitle"><?= $block->subtitle()->kt() ?></p>
      
      <div class="hero-cta">
        <?php snippet('cta_button', [
          'url'  => $block->button_link()->url(),
          'text' => $block->button_text()->or('Zum Buch')
        ]) ?>
      </div>
    </div>

  </div>
</section>