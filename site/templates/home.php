<?php snippet('header') ?>

<main class="home-page-redesign container">
  
  <?php 
  // Book Tile Data
  $cover = $page->bookCover()->toFile();
  $bookHeadline = $page->bookHeadline();
  $bookIntro = $page->bookIntro();
  $ctaText = $page->ctaText();
  $ctaUrl = $page->ctaUrl();

  // Reviews Data
  $reviewsList = $page->reviewsList()->toStructure();

  // About Book Data
  $aboutHeadline = $page->aboutHeadline();
  $aboutText = $page->aboutText();
  $aboutHighlights = $page->aboutHighlights()->toStructure();

  // Projects Tile Data
  $projectsHeadline = $page->projectsHeadline();
  $projectsIntro = $page->projectsIntro();
  $projectsLinkText = $page->projectsLinkText();
  $projectsLinkObj = $page->projectsLinkUrl()->toPage();
  $projectsUrl = $projectsLinkObj ? $projectsLinkObj->url() : '';
  ?>

  <!-- Kachel 1: Buchbereich -->
  <section class="tile book-tile">
    <div class="book-tile-image">
      <?php if ($cover): ?>
        <img src="<?= $cover->resize(800)->url() ?>" alt="<?= $cover->alt()->esc() ?>" width="<?= $cover->resize(800)->width() ?>" height="<?= $cover->resize(800)->height() ?>">
      <?php else: ?>
        <div class="placeholder-image">Kein Cover hinterlegt</div>
      <?php endif ?>
    </div>
    
    <div class="book-tile-content">
      <?php if ($bookHeadline->isNotEmpty()): ?>
        <h2><?= $bookHeadline->esc() ?></h2>
      <?php endif ?>
      
      <?php if ($bookIntro->isNotEmpty()): ?>
        <div class="book-text">
          <?= $bookIntro->kt() ?>
        </div>
      <?php endif ?>
      
      <?php if ($ctaUrl->isNotEmpty() && $ctaText->isNotEmpty()): ?>
        <div class="button-wrapper">
          <a href="<?= $ctaUrl->html() ?>" class="cta-button" target="_blank" rel="noopener noreferrer">
            <?= $ctaText->esc() ?>
          </a>
        </div>
      <?php endif ?>
    </div>
  </section>

  <!-- Rezensionen / Testimonials (No outer tile) -->
  <?php if ($reviewsList->isNotEmpty()): ?>
    <section class="reviews-section">
      <div class="reviews-section-content">
        <div class="reviews-carousel-wrapper">
          <button class="carousel-control prev" aria-label="Vorherige Rezension">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
          </button>
          
          <div class="reviews-carousel">
            <div class="reviews-track">
              <?php foreach ($reviewsList as $index => $review): ?>
                <div class="review-slide <?= $index === 0 ? 'active' : '' ?>">
                  <blockquote class="review-card">
                    <?php if ($review->reviewTitle()->isNotEmpty()): ?>
                      <div class="review-title">
                        <?= $review->reviewTitle()->esc() ?>
                      </div>
                    <?php endif ?>
                    
                    <div class="review-content">
                      <div class="review-text">
                        <?= $review->reviewText()->kt() ?>
                      </div>
                      <?php if ($review->reviewAuthor()->isNotEmpty()): ?>
                        <footer class="review-author">
                          <?= $review->reviewAuthor()->esc() ?>
                        </footer>
                      <?php endif ?>
                    </div>
                  </blockquote>
                </div>
              <?php endforeach ?>
            </div>
          </div>

          <button class="carousel-control next" aria-label="Nächste Rezension">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
          </button>
        </div>
      </div>
    </section>
  <?php endif ?>

  <!-- Kachel: Über das Buch -->
  <?php if ($aboutText->isNotEmpty()): ?>
    <section class="tile about-tile">
      <div class="about-tile-content">
        <?php if ($aboutHeadline->isNotEmpty()): ?>
          <h2><?= $aboutHeadline->esc() ?></h2>
        <?php endif ?>
        <?php if ($aboutText->isNotEmpty()): ?>
          <div class="about-text text-content">
            <?= $aboutText->kt() ?>
          </div>
        <?php endif ?>
        <?php if ($aboutHighlights->isNotEmpty()): ?>
          <div class="about-highlights-grid">
            <?php foreach ($aboutHighlights as $h): ?>
              <div class="highlight-card">
                <h3 class="highlight-title"><?= $h->highlightTitle()->esc() ?></h3>
                <?php if ($h->highlightText()->isNotEmpty()): ?>
                  <div class="highlight-text"><?= $h->highlightText()->kt() ?></div>
                <?php endif ?>
              </div>
            <?php endforeach ?>
          </div>
        <?php endif ?>
      </div>
    </section>
  <?php endif ?>

  <!-- Kachel: Projekte -->
  <section class="tile projects-tile">
    <div class="projects-tile-content">
      <?php if ($projectsHeadline->isNotEmpty()): ?>
        <h2><?= $projectsHeadline->esc() ?></h2>
      <?php endif ?>
      
      <?php if ($projectsIntro->isNotEmpty()): ?>
        <div class="projects-text">
          <?= $projectsIntro->kt() ?>
        </div>
      <?php endif ?>
      
      <?php if ($projectsLinkText->isNotEmpty() && $projectsUrl): ?>
        <div class="button-wrapper">
          <a href="<?= $projectsUrl ?>" class="btn-secondary">
            <?= $projectsLinkText->esc() ?>
          </a>
        </div>
      <?php endif ?>
    </div>
  </section>

</main>

<?php snippet('footer') ?>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const track = document.querySelector('.reviews-track');
    if (!track) return;
    
    const slides = Array.from(track.children);
    const prevButton = document.querySelector('.carousel-control.prev');
    const nextButton = document.querySelector('.carousel-control.next');
    
    // Only init if we have more than 1 slide
    if (slides.length <= 1) {
        if(prevButton) prevButton.style.display = 'none';
        if(nextButton) nextButton.style.display = 'none';
        return;
    }
    
    let currentIndex = 0;
    
    function moveToSlide(index) {
        track.style.transform = `translateX(-${index * 100}%)`;
        
        slides.forEach(slide => slide.classList.remove('active'));
        slides[index].classList.add('active');
        
        currentIndex = index;
    }
    
    function nextSlide() {
        let index = currentIndex + 1;
        if (index >= slides.length) index = 0;
        moveToSlide(index);
    }
    
    function prevSlide() {
        let index = currentIndex - 1;
        if (index < 0) index = slides.length - 1;
        moveToSlide(index);
    }
    
    // Event listeners
    if (nextButton) nextButton.addEventListener('click', () => {
        nextSlide();
        resetTimer();
    });
    
    if (prevButton) prevButton.addEventListener('click', () => {
        prevSlide();
        resetTimer();
    });
    
    // Auto-advance
    let autoPlayInterval = setInterval(nextSlide, 6000);
    
    function resetTimer() {
        clearInterval(autoPlayInterval);
        autoPlayInterval = setInterval(nextSlide, 6000);
    }
});
</script>
