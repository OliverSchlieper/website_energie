<?php snippet('header') ?>

<div class="title-bar">
  <div class="container">

    <h1><?= $page->title() ?></h1>

    <?php if ($page->author()->isNotEmpty()): ?>
      <p class="project-author">Von <?= $page->author() ?></p>
    <?php endif ?>

  </div>
</div>


<section class="container">

  <!-- Intro + Credits -->

  <div class="content-grid">

    <div class="intro">
      <?= $page->intro()->kt() ?>
    </div>

    <div class="credits">
      <?= $page->credits()->kt() ?>
    </div>

  </div>
  
  <div class="divider">  
    <h2>Zum Hörspiel</h2>
  </div>

  <!-- Link Bereich -->

  <div class="link">

    <div class="link-grid">

      <!-- Button / Text -->
      <div class="linktext">

        <a href="<?= url('assets/projekte/konrad-und-das-dokument-a38/index.html') ?>" class="project-button">
          Jetzt anhören
        </a>
      </div>

      <!-- Klickbares Bild -->
      <a href="<?= url('assets/projekte/konrad-und-das-dokument-a38/index.html') ?>" class="project-thumb">
        <?php if($image = $page->image()): ?>
          <img src="<?= $image->url() ?>" alt="">
        <?php endif ?>
      </a>

    </div>

  </div>

</section>

<?php snippet('footer') ?>