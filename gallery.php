<?php
declare(strict_types=1);
require __DIR__ . '/includes/bootstrap.php';
$pageTitle = 'Aeterna | Gallery';
$currentPage = 'gallery';
require __DIR__ . '/includes/header.php';

$images = [];
$error = '';

try {
    require __DIR__ . '/config.php';
    $rows = $pdo->query('SELECT image_path, alt_text, is_tall FROM gallery_items ORDER BY sort_order ASC, created_at DESC')->fetchAll();

    foreach ($rows as $row) {
        $images[] = [
            'src' => (string)$row['image_path'],
            'alt' => (string)$row['alt_text'],
            'span' => ((int)$row['is_tall'] === 1 ? 'row-span-2' : ''),
        ];
    }
} catch (Throwable $e) {
    error_log('Public gallery load error: ' . $e->getMessage());
    $error = 'Gallery data is unavailable right now.';
}
?>
<div class="pt-28 pb-16">
  <section class="container-luxury">
    <h1 class="font-display text-4xl md:text-7xl tracking-[0.1em] uppercase mb-4">Campaigns</h1>
    <p class="text-muted-foreground text-sm tracking-wider mb-12">A visual narrative of our brand</p>

    <?php if ($error !== ''): ?>
      <div class="mb-6 bg-card border border-destructive px-4 py-3 text-sm text-destructive"><?= e($error) ?></div>
    <?php endif; ?>

    <?php if (empty($images)): ?>
      <p class="text-sm text-muted-foreground">No data available. No gallery items to display right now.</p>
    <?php else: ?>
      <div class="grid grid-cols-2 md:grid-cols-3 gap-3 md:gap-4 auto-rows-[250px] md:auto-rows-[300px]">
        <?php foreach ($images as $image): ?>
          <button type="button" data-gallery-image data-src="<?= e($image['src']) ?>" data-alt="<?= e($image['alt']) ?>" class="w-full h-full image-zoom block <?= e($image['span']) ?>">
            <img src="<?= e($image['src']) ?>" alt="<?= e($image['alt']) ?>" class="w-full h-full object-cover" loading="lazy" />
          </button>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </section>

  <div id="gallery-lightbox" class="fixed inset-0 z-50 bg-foreground/90 flex items-center justify-center hidden">
    <button type="button" data-close-lightbox class="absolute top-6 right-6 text-primary-foreground">Close</button>
    <button type="button" id="lightbox-prev" class="absolute left-4 md:left-8 text-primary-foreground hover:text-accent transition-colors">Prev</button>
    <div class="flex flex-col items-center gap-4 px-8">
      <img id="lightbox-image" src="" alt="" class="max-h-[85vh] max-w-[90vw] object-contain" />
      <p id="lightbox-alt" class="text-primary-foreground/80 text-sm tracking-wider"></p>
    </div>
    <button type="button" id="lightbox-next" class="absolute right-4 md:right-8 text-primary-foreground hover:text-accent transition-colors">Next</button>
  </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
