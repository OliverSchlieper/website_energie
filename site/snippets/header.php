<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">

  <title><?= $site->title()->esc() ?> | <?= $page->title()->esc() ?></title>

  <?php /* Hier werden alle CSS Dateien gesammelt geladen */ ?>
  <?= css([
    'assets/css/prism.css',
    'assets/css/lightbox.css',
    'assets/css/index.css',
    'assets/css/components.css', // HIER eingetragen
    '@auto'                      // Lädt automatisch home.css, about.css etc.
  ]) ?>

  <link rel="shortcut icon" type="image/x-icon" href="<?= url('favicon.ico') ?>">
</head>
<body>

  <header class="header">
    <a class="logo" href="<?= $site->url() ?>">
      <img src="<?= url('assets/icons/HM_Logo_rot_RGB.svg') ?>" 
       alt="<?= $site->title()->esc() ?>" />
    </a>

    <nav class="menu">
      <?php foreach ($site->children()->listed() as $item): ?>
      <a <?php e($item->isOpen(), 'aria-current="page"') ?> href="<?= $item->url() ?>"><?= $item->title()->esc() ?></a>
      <?php endforeach ?>
    </nav>
  </header>

  <main class="main">