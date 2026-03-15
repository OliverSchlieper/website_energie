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


  <!-- Interviews -->
  <div class="interviews">

    <?php foreach ($page->portraits()->toStructure() as $person): ?>

      <div class="interview-grid">

        <div class="portrait-text">
          <h2>
            <?= $person->name() ?> (<?= $person->age() ?>) – <?= $person->role() ?>
          </h2>

          <?= $person->text()->kt() ?>
        </div>

        <?php if ($person->video()->isNotEmpty()): ?>
          <div class="portrait-video">
            <iframe
              src="https://www.youtube.com/embed/<?= $person->video() ?>"
              allowfullscreen>
            </iframe>
          </div>
        <?php endif ?>

      </div>

    <?php endforeach ?>

  </div>

</section>

<?php snippet('footer') ?>