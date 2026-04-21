<?php
declare(strict_types=1);
require __DIR__ . '/includes/bootstrap.php';
require __DIR__ . '/includes/cms_content.php';
$pageTitle = 'Aeterna | FAQ';
$currentPage = 'faq';
require __DIR__ . '/includes/header.php';

$content = cmsLoad();
$faqCfg = is_array($content['faq'] ?? null) ? $content['faq'] : [];

$faqHeading = trim((string)($faqCfg['heading'] ?? 'FAQ'));
if ($faqHeading === '') {
    $faqHeading = 'FAQ';
}

$faqSubtitle = trim((string)($faqCfg['subtitle'] ?? 'Common questions answered'));
if ($faqSubtitle === '') {
    $faqSubtitle = 'Common questions answered';
}

$faqs = [];
$rawItems = $faqCfg['items'] ?? [];
if (is_array($rawItems)) {
    foreach ($rawItems as $item) {
        if (!is_array($item)) {
            continue;
        }
        $q = trim((string)($item['q'] ?? ''));
        $a = trim((string)($item['a'] ?? ''));
        if ($q === '' || $a === '') {
            continue;
        }
        $faqs[] = ['q' => $q, 'a' => $a];
    }
}
?>
<div class="pt-28 pb-16">
  <section class="container-luxury max-w-3xl mx-auto">
    <h1 class="font-display text-4xl md:text-7xl tracking-[0.1em] uppercase mb-4"><?= e($faqHeading) ?></h1>
    <p class="text-muted-foreground text-sm tracking-wider mb-16"><?= e($faqSubtitle) ?></p>

    <div class="space-y-2">
      <?php if (empty($faqs)): ?>
        <p class="text-sm text-muted-foreground">No FAQ data available right now.</p>
      <?php else: ?>
        <?php foreach ($faqs as $i => $faq): ?>
          <div class="faq-item <?= $i === 0 ? 'open' : '' ?>">
            <button type="button" class="faq-trigger py-6 text-left font-display text-lg tracking-wide hover:text-accent transition-colors"><?= e($faq['q']) ?></button>
            <div class="faq-content text-muted-foreground leading-relaxed pb-6"><?= e($faq['a']) ?></div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </section>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
