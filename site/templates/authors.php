<?php snippet('header') ?>

<main class="authors-page">
  <header class="section-header">
    <h1><?= $page->headline()->or($page->title())->esc() ?></h1>
  </header>

  <div class="authors-grid">
    <?php foreach ($page->authors_list()->toStructure() as $author): ?>
      <article class="author-card">
        <div class="author-image">
          <?php if ($image = $author->image()->toFile()): ?>
            <img src="<?= $image->url() ?>" alt="<?= $author->name()->esc() ?>">
          <?php endif ?>
        </div>
        <div class="author-details">
          <h2><?= $author->name()->esc() ?></h2>
          <span class="role-tag"><?= $author->role()->esc() ?></span>
          <div class="bio"><?= $author->bio()->kt() ?></div>
          
          <div class="author-footer">
            <a href="mailto:<?= $author->email() ?>" class="author-email">
              ✉️ <?= $author->email()->esc() ?>
            </a>
          </div>
        </div>
      </article>
    <?php endforeach ?>
  </div>
</main>

<?php snippet('footer') ?>