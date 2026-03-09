<section class="arguments-section">
  <?php if ($block->headline()->isNotEmpty()): ?>
    <h2 class="arguments-headline"><?= $block->headline()->esc() ?></h2>
  <?php endif ?>

  <div class="arguments-grid">
    <?php foreach ($block->arguments()->toStructure() as $arg): ?>
      <div class="argument-card">
        <h3 class="argument-title"><?= $arg->title()->esc() ?></h3>
        <div class="argument-text">
          <?= $arg->text()->kt() ?>
        </div>
      </div>
    <?php endforeach ?>
  </div>
</section>