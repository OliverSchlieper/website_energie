<?php
$type = $projekt->type()->value();
$link = '';

if ($type === 'youtube') {
  $link = $projekt->youtubeUrl()->value();
} else {
  $link = $projekt->externalUrl()->value();
}

$cover = $projekt->cover()->toFile();
?>

<div class="projekt-card">
  <a href="<?= $link ?>" target="_blank">

    <?php if ($cover): ?>
      <img src="<?= $cover->url() ?>" alt="<?= $projekt->title() ?>">
    <?php elseif ($type === 'youtube'): ?>
      <?php
      // extract youtube ID
      preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([^&]+)/', $link, $matches);
      $youtubeId = $matches[1] ?? null;
      ?>
      <?php if ($youtubeId): ?>
        <img src="https://img.youtube.com/vi/<?= $youtubeId ?>/hqdefault.jpg">
      <?php endif ?>
    <?php endif ?>

    <div class="projekt-content">
      <h3><?= $projekt->title() ?></h3>
      <p><?= $projekt->description() ?></p>
    </div>

  </a>
</div>
