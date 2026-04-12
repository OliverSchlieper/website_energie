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
    <article class="content-grid">

        <div class="intro p">
            <?= $page->intro()->kt() ?>
        </div>

        <div>
            <h3>Kapitel</h3>
            <?php if ($page->youtube()->isNotEmpty()): ?>

                <?php
                $chapters = [
                    ['title' => 'Vorstellung & Einleitung',                   'seconds' => 0],
                    ['title' => 'Wärmepumpe & E-Auto als politische Reizthemen', 'seconds' => 190],
                    ['title' => 'E-Mobilität & die Effizienz-Revolution',     'seconds' => 915],
                    ['title' => 'Familie Müller: Schreiben für alle',         'seconds' => 1215],
                    ['title' => 'Wie erreichen wir die Menschen?',            'seconds' => 1744],
                    ['title' => 'Können wir uns Nachhaltigkeit leisten?',     'seconds' => 2305],
                    ['title' => 'Kernfusion – kein Ausweg',                   'seconds' => 2684],
                    ['title' => 'Die Technologien sind da – handeln wir jetzt', 'seconds' => 2943],
                ];
                ?>

                <nav class="chapter-menu">
                    <?php foreach ($chapters as $chapter): ?>
                        <button class="chapter-btn" data-seconds="<?= $chapter['seconds'] ?>">
                            <?= htmlspecialchars($chapter['title']) ?>
                        </button>
                    <?php endforeach ?>
                </nav>
        </div>

        <div class="video">
            <iframe
                id="yt-player"
                src="https://www.youtube.com/embed/<?= $page->youtube() ?>?enablejsapi=1"
                frameborder="0"
                allowfullscreen>
            </iframe>
        </div>

    <?php endif ?>

    </article>
</section>

<script>
    const tag = document.createElement('script');
    tag.src = "https://www.youtube.com/iframe_api";
    document.head.appendChild(tag);

    let player;

    window.onYouTubeIframeAPIReady = function() {
        player = new YT.Player('yt-player', {
            events: {
                onReady: () => {
                    document.querySelectorAll('.chapter-btn').forEach(btn => {
                        btn.addEventListener('click', () => {
                            player.seekTo(parseInt(btn.dataset.seconds), true);
                            player.playVideo();
                            document.querySelectorAll('.chapter-btn').forEach(b => b.classList.remove('active'));
                            btn.classList.add('active');
                        });
                    });
                }
            }
        });
    };
</script>

<?php snippet('footer') ?>