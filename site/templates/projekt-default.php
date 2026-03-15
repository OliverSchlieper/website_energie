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

  <!-- Intro 
  <div class="content-grid">

    <div class="intro p">
      <?= $page->intro()->kt() ?>
    </div>

  </div>-->

  <article class="video-grid">
    <div class="intro2 p">
      <?= $page->intro()->kt () ?>
    </div>

    <?php if ($page->youtube()->isNotEmpty()): ?>
    <div class="video">
      <iframe
        src="https://www.youtube.com/embed/<?= $page->youtube() ?>"
        frameborder="0"
        allowfullscreen>
      </iframe>
    </div>
    <?php endif ?>

  </article>

  </section>

  <?php snippet('footer') ?>
  </section>