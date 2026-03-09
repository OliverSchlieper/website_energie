<?php snippet('header') ?>

<main class="home-main">
  
  <?php foreach ($page->main_content()->toBlocks() as $block): ?>
    
    <?php if ($block->type() == 'book_hero'): ?>
      <section class="buch-section">
        <div class="buch-container">
          <div class="buch-visual">
            <?php if ($image = $block->cover()->toFile()): ?>
              <img src="<?= $image->url() ?>" alt="<?= $block->title()->esc() ?>">
            <?php endif ?>
          </div>
          <div class="buch-content">
            <h1 class="huge-title"><?= $block->title()->esc() ?></h1>
            <div class="buch-description"><?= $block->text()->kt() ?></div>
            <div class="buch-cta">
              <a href="<?= $block->button_link() ?>" class="blueprint-button">
                <?= $block->button_text()->or('Jetzt kaufen') ?>
              </a>
            </div>
          </div>
        </div>
      </section>

    <?php elseif ($block->type() == 'stats_block'): ?>
      <div class="section-divider">Daten & Fakten</div>
      <section class="stats-grid">
        <?php foreach ($block->stats_list()->toStructure() as $stat): ?>
          <div class="stat-card">
            <div class="stat-number"><?= $stat->number()->esc() ?></div>
            <div class="stat-text"><?= $stat->text()->esc() ?></div>
          </div>
        <?php endforeach ?>
      </section>

    <?php elseif ($block->type() == 'quote_block'): ?>
      <section class="mega-quote">
        <blockquote>
          "<?= $block->text()->esc() ?>"
          <cite>— <?= $block->author()->esc() ?></cite>
        </blockquote>
      </section>

    <?php elseif ($block->type() == 'authors_teaser'): ?>
       <section class="authors-teaser-section">
         <div class="section-divider">Das Team hinter dem Buch</div>
         <div style="padding: 60px; text-align: center;">
           <a href="<?= url('autoren') ?>" class="blueprint-button">Lerne die Autoren kennen</a>
         </div>
       </section>

    <?php endif ?>

  <?php endforeach ?>

</main>

<?php snippet('footer') ?>