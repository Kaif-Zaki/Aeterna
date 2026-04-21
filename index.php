<?php
declare(strict_types=1);
require __DIR__ . '/includes/bootstrap.php';
require __DIR__ . '/includes/cms_content.php';
$pageTitle = 'Aeterna | Home';
$currentPage = 'home';
require __DIR__ . '/includes/header.php';

$collections = [
    ['img' => '/assets/images/collection-1.jpg', 'title' => 'Autumn Knit', 'category' => 'Women'],
    ['img' => '/assets/images/collection-2.jpg', 'title' => 'Noir Tailoring', 'category' => 'Men'],
    ['img' => '/assets/images/collection-3.jpg', 'title' => 'Urban Edge', 'category' => 'Streetwear'],
];

$content = cmsLoad();
$home = is_array($content['home'] ?? null) ? $content['home'] : [];

function homeValue(array $home, string $key, string $fallback): string
{
    $value = trim((string)($home[$key] ?? ''));
    return $value !== '' ? $value : $fallback;
}
?>
<section class="relative h-screen flex items-center justify-center overflow-hidden">
  <img src="/assets/images/hero-1.jpg" alt="Aeterna fashion campaign" class="absolute inset-0 w-full h-full object-cover" width="1920" height="1080" />
  <div class="absolute inset-0 bg-foreground/30"></div>
  <div class="relative z-10 text-center px-6">
    <h1 class="font-display text-5xl md:text-8xl tracking-[0.15em] uppercase text-primary-foreground mb-6"><?= e(homeValue($home, 'hero_title', 'Aeterna')) ?></h1>
    <p class="font-body text-lg md:text-xl tracking-[0.1em] text-primary-foreground/80 mb-10 max-w-lg mx-auto"><?= e(homeValue($home, 'hero_subtitle', 'Timeless elegance for the modern soul')) ?></p>
    <div class="flex gap-4 justify-center">
      <a href="/collections.php" class="px-8 py-3 bg-primary-foreground text-primary text-sm tracking-[0.15em] uppercase hover:bg-accent hover:text-accent-foreground transition-all duration-300">Explore</a>
      <a href="/contact.php" class="px-8 py-3 border border-primary-foreground text-primary-foreground text-sm tracking-[0.15em] uppercase hover:bg-primary-foreground/10 transition-all duration-300">Contact</a>
    </div>
  </div>
</section>

<section class="container-luxury py-24 md:py-32">
  <h2 class="font-display text-3xl md:text-5xl text-center tracking-[0.1em] uppercase mb-4"><?= e(homeValue($home, 'featured_title', 'Featured Collections')) ?></h2>
  <p class="text-muted-foreground text-center text-sm tracking-wider mb-16 max-w-md mx-auto"><?= e(homeValue($home, 'featured_subtitle', 'A curated selection from our latest seasonal offerings')) ?></p>

  <div class="grid grid-cols-1 md:grid-cols-3 gap-6 md:gap-8">
    <?php foreach ($collections as $item): ?>
      <a href="/collections.php" class="group block image-zoom">
        <div class="aspect-[3/4] overflow-hidden mb-4">
          <img src="<?= e($item['img']) ?>" alt="<?= e($item['title']) ?>" class="w-full h-full object-cover" loading="lazy" width="800" height="1100" />
        </div>
        <p class="text-xs tracking-[0.2em] uppercase text-muted-foreground mb-1"><?= e($item['category']) ?></p>
        <h3 class="font-display text-xl tracking-wider group-hover:text-accent transition-colors"><?= e($item['title']) ?></h3>
      </a>
    <?php endforeach; ?>
  </div>
</section>

<section class="bg-card py-24 md:py-32">
  <div class="container-luxury text-center max-w-3xl mx-auto">
    <p class="font-display text-2xl md:text-4xl leading-relaxed tracking-wide text-foreground/90">"<?= e(homeValue($home, 'quote_text', 'We believe clothing is not merely worn — it is inhabited. Each piece tells a story of craft, intention, and quiet confidence.')) ?>"</p>
    <p class="mt-8 text-xs tracking-[0.3em] uppercase text-muted-foreground">— <?= e(homeValue($home, 'quote_author', 'The Aeterna Philosophy')) ?></p>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
