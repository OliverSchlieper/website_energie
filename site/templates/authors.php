<?php snippet('header') ?>

<main class="authors-page">
  <div class="container">
    
    <header class="authors-header">
      <h1 class="huge-title"><?= $page->headline()->esc() ?></h1>
    </header>

    <div class="authors-stack">
      <?php foreach ($page->authors_list()->toStructure() as $author): ?>
        <article class="author-entry">
          
          <div class="author-visual">
            <?php if ($img = $author->image()->toFile()): ?>
              <img src="<?= $img->url() ?>" alt="<?= $author->name() ?>">
            <?php else: ?>
              <div class="img-placeholder">Bild fehlt</div>
            <?php endif ?>
          </div>
          
          <div class="author-details">
            <h2 class="name"><?= $author->name()->esc() ?></h2>
            <p class="role-badge"><?= $author->role()->esc() ?></p>
            
            <div class="bio-content">
              <?= $author->bio()->kt() ?>
            </div>
            
            <div class="contact-info">
              <a href="mailto:<?= $author->email() ?>" class="email-link">
                <span>✉</span> <?= $author->email()->esc() ?>
              </a>
            </div>
          </div>

        </article>
      <?php endforeach ?>
    </div>

  </div>
</main>

<?php snippet('footer') ?>