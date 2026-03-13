<!DOCTYPE html>
<html lang="de">
<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title><?= $site->title()->esc() ?> | <?= $page->title()->esc() ?></title>
  <?php
  /*
    Stylesheets can be included using the `css()` helper.
    Kirby also provides the `js()` helper to include script file.
    More Kirby helpers: https://getkirby.com/docs/reference/templates/helpers
  */
  ?>
  <?= css([
    'assets/css/prism.css',
    'assets/css/lightbox.css',
    'assets/css/index.css', // Hier sind die UntitledSans @font-face Regeln drin
    'assets/css/templates/header.css',
    'assets/css/templates/' . $page->template() . '.css' // Lädt automatisch authors.css
  ]) ?>

  <?php
  /*
    The `url()` helper is a great way to create reliable
    absolute URLs in Kirby that always start with the
    base URL of your site.
  */
  ?>
  <link rel="shortcut icon" type="image/x-icon" href="<?= url('favicon.ico') ?>">
</head>
  <header class="header">
    <div class="header_container">
      <a class="logo" href="<?= $site->url() ?>">
        <img src="<?= url('assets/icons/HM_Logo_rot_cube_RGB.svg') ?>" 
        alt="<?= $site->title()->esc() ?>" />
      </a>
      <button class="menu-toggle">
        <span></span>
        <span></span>
        <span></span>
      </button>
      <nav class="menu">
        <ul>
          <?php foreach ($site->children()->listed() as $item): ?>
            <li>
              <a class="<?= $item->isOpen() ? 'current' : '' ?>"
                href="<?= $item->url() ?>">
                <?= $item->title()->esc() ?>
              </a>
            </li>
          <?php endforeach ?>
        </ul>
      </nav>
    </div>
  </header>

  <script>
    const menuButton = document.querySelector(".menu-toggle");
    const menuList = document.querySelector('.menu ul');

    menuButton.addEventListener('click', () => {
        menuList.classList.toggle('sichtbar');
        menuButton.classList.toggle('active'); 
    });

  </script>
