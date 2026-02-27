<?php
/** * Wiederverwendbarer CTA Button - Version ohne SVG-Pfeil
 * Aufruf: snippet('cta_button', ['url' => '...', 'text' => '...'])
 */
$url  = $url ?? '#';
$text = $text ?? 'Mehr erfahren';
?>

<a href="<?= $url ?>" class="blueprint-button">
  <span><?= $text ?></span>
  </a>