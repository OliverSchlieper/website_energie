<?php
/*
  Snippets are a great way to store code snippets for reuse
  or to keep your templates clean.

  This footer snippet is reused in all templates.

  More about snippets:
  https://getkirby.com/docs/guide/templates/snippets
*/
?>
  </main>

  <footer id="footer" class="ct-section">
    <div class="ct-section-inner-wrap">
      <div id="new_columns-30-14" class="ct-new-columns">
        <div id="div_block-31-14" class="ct-div-block">
          <a id="link-120-14" class="ct-link" href="https://hm.edu" target="_blank">
            <div id="text_block-24-14" class="ct-text-block">
              <span id="span-25-14" class="ct-span" data-ninja-font="untitled-sans-web_regular_normal_vw50a">Hochschule München ©<?= date('Y') ?></span>
            </div>
          </a>
          <nav id="_nav_menu-29-14" class="oxy-nav-menu bottom-nav-menu oxy-nav-menu-dropdowns oxy-nav-menu-dropdown-arrow">
            <div class="oxy-menu-toggle">
              <div class="oxy-nav-menu-hamburger-wrap">
                <div class="oxy-nav-menu-hamburger">
                  <div class="oxy-nav-menu-hamburger-line"></div>
                  <div class="oxy-nav-menu-hamburger-line"></div>
                  <div class="oxy-nav-menu-hamburger-line"></div>
                </div>
              </div>
            </div>
            <div class="menu-fusszeile-container">
              <ul id="menu-fusszeile" class="oxy-nav-menu-list">
                <li id="menu-item-23" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-23">
                  <a href="<?= $site->url() ?>/impressum/" data-ninja-font="untitled-sans-web_regular_normal_vw50a">Impressum</a>
                </li>
                <li id="menu-item-24" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-24">
                  <a href="<?= $site->url() ?>/datenschutz/" data-ninja-font="untitled-sans-web_regular_normal_vw50a">Datenschutz</a>
                </li>
              </ul>
            </div>
          </nav>
        </div>
        <div id="div_block-32-14" class="ct-div-block"></div>
      </div>
    </div>
  </footer>

  <?= js([
    'assets/js/prism.js',
    'assets/js/lightbox.js',
    'assets/js/index.js',
    '@auto'
  ]) ?>

</body>
</html>
