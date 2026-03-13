<?php snippet('header') ?>

<div class="title-bar">
  <div class="container">
    <h1><?= $page->title() ?></h1>

    <?php if ($page->author()->isNotEmpty()): ?>
      <p class="project-author">Von <?= $page->author() ?></p>
    <?php endif ?>
  </div>
</div>

<article class="container">

  <?php if ($page->youtube()->isNotEmpty()): ?>
  <div class="video">
    <iframe
      src="https://www.youtube.com/embed/<?= $page->youtube() ?>"
      frameborder="0"
      allowfullscreen>
    </iframe>
  </div>
  <?php endif ?>

  <div class="description">
    <?= $page->description()->kirbytext() ?>
  </div>

</article>
