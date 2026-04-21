<?php
declare(strict_types=1);
require __DIR__ . '/includes/bootstrap.php';
require __DIR__ . '/includes/cms_content.php';
$pageTitle = 'Aeterna | About';
$currentPage = 'about';
require __DIR__ . '/includes/header.php';

$content = cmsLoad();
$about = is_array($content['about'] ?? null) ? $content['about'] : [];

function aboutValue(array $about, string $key, string $fallback): string
{
    $value = trim((string)($about[$key] ?? ''));
    return $value !== '' ? $value : $fallback;
}

$timeline = [
    ['year' => '2018', 'text' => 'Founded in a small atelier in Milan with a vision for timeless design.'],
    ['year' => '2019', 'text' => 'First capsule collection launched to critical acclaim.'],
    ['year' => '2021', 'text' => 'Expanded into menswear and sustainable fabric sourcing.'],
    ['year' => '2023', 'text' => 'Global presence with flagship showrooms in Paris and Tokyo.'],
    ['year' => '2025', 'text' => 'Launched the Aeterna Foundation for emerging designers.'],
];
?>
<div class="pt-28 pb-16">
  <section class="container-luxury mb-24">
    <h1 class="font-display text-4xl md:text-7xl tracking-[0.1em] uppercase mb-8"><?= e(aboutValue($about, 'hero_title', 'No data available')) ?></h1>
    <p class="text-muted-foreground text-lg max-w-2xl leading-relaxed"><?= e(aboutValue($about, 'hero_text', 'No data available for this section.')) ?></p>
  </section>

  <section class="container-luxury grid grid-cols-1 lg:grid-cols-2 gap-12 mb-24">
    <div class="image-zoom">
      <img src="/assets/images/about-story.jpg" alt="Aeterna atelier" class="w-full h-full object-cover" loading="lazy" width="1200" height="800" />
    </div>
    <div class="flex flex-col justify-center">
      <h2 class="font-display text-3xl md:text-4xl tracking-wider uppercase mb-6"><?= e(aboutValue($about, 'mission_title', 'No data available')) ?></h2>
      <p class="text-muted-foreground leading-relaxed mb-4"><?= e(aboutValue($about, 'mission_text_1', 'No data available for this section.')) ?></p>
      <p class="text-muted-foreground leading-relaxed"><?= e(aboutValue($about, 'mission_text_2', 'No data available for this section.')) ?></p>
    </div>
  </section>

  <section class="bg-card py-24">
    <div class="container-luxury">
      <h2 class="font-display text-3xl md:text-4xl tracking-wider uppercase text-center mb-16">Our Journey</h2>
      <div class="max-w-2xl mx-auto">
        <?php foreach ($timeline as $item): ?>
          <div class="flex gap-8 mb-10">
            <div class="font-display text-2xl text-accent min-w-[80px]"><?= e($item['year']) ?></div>
            <div class="border-l border-border pl-8">
              <p class="text-muted-foreground leading-relaxed"><?= e($item['text']) ?></p>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="container-luxury py-24 grid grid-cols-1 lg:grid-cols-2 gap-12">
    <div class="aspect-[4/5] image-zoom">
      <img src="/assets/images/founder.jpg" alt="Founder" class="w-full h-full object-cover" loading="lazy" width="800" height="1000" />
    </div>
    <div class="flex flex-col justify-center">
      <p class="text-xs tracking-[0.3em] uppercase text-muted-foreground mb-3"><?= e(aboutValue($about, 'founder_role', 'No data available')) ?></p>
      <h2 class="font-display text-3xl md:text-4xl tracking-wider mb-6"><?= e(aboutValue($about, 'founder_name', 'No data available')) ?></h2>
      <p class="text-muted-foreground leading-relaxed"><?= e(aboutValue($about, 'founder_bio', 'No data available for this section.')) ?></p>
    </div>
  </section>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
