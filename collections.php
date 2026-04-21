<?php
declare(strict_types=1);
require __DIR__ . '/includes/bootstrap.php';
$pageTitle = 'Aeterna | Collections';
$currentPage = 'collections';
require __DIR__ . '/includes/header.php';

$error = '';
$filters = ['All'];
$items = [];
$dbReady = false;

try {
    require __DIR__ . '/config.php';
    $dbReady = true;
} catch (Throwable $e) {
    error_log('Collections DB bootstrap error: ' . $e->getMessage());
    $error = 'Collection data is unavailable right now.';
}

if ($dbReady) {
try {
    $categoryRows = $pdo->query(
        'SELECT id, name
         FROM categories
         ORDER BY name ASC'
    )->fetchAll();

    $seenFilters = ['all' => true];
    foreach ($categoryRows as $row) {
        $name = trim((string)($row['name'] ?? ''));
        $key = mb_strtolower($name);
        if ($name !== '' && !isset($seenFilters[$key])) {
            $filters[] = $name;
            $seenFilters[$key] = true;
        }
    }

    $itemRows = $pdo->query(
        'SELECT cl.id, cl.name, cl.price, cl.image_path, c.name AS category_name
         FROM clothes cl
         INNER JOIN categories c ON c.id = cl.category_id
         WHERE cl.image_path IS NOT NULL AND cl.image_path <> ""
         ORDER BY cl.id DESC'
    )->fetchAll();

    foreach ($itemRows as $row) {
        $title = trim((string)($row['name'] ?? ''));
        $imagePath = trim((string)($row['image_path'] ?? ''));
        if ($title === '' || $imagePath === '') {
            continue;
        }

        $categoryName = trim((string)($row['category_name'] ?? ''));
        if ($categoryName === '') {
            $categoryName = 'Uncategorized';
        }

        $price = $row['price'];
        $priceText = $price !== null ? number_format((float)$price, 2) : null;

        $items[] = [
            'img' => $imagePath,
            'title' => $title,
            'category' => $categoryName,
            'desc' => $priceText !== null
                ? 'Collection piece. Price: ' . $priceText
                : 'Collection piece from the latest Aeterna edit.',
        ];
    }
} catch (Throwable $e) {
    error_log('Collections fetch error: ' . $e->getMessage());
    $error = 'Could not load collection data right now.';
}
}
?>
<div class="pt-28 pb-16">
  <section class="container-luxury">
    <h1 class="font-display text-4xl md:text-7xl tracking-[0.1em] uppercase mb-4">Collections</h1>
    <p class="text-muted-foreground text-sm tracking-wider mb-12">Explore our curated lookbook</p>

    <?php if ($error !== ''): ?>
      <div class="mb-6 bg-card border border-destructive px-4 py-3 text-sm text-destructive"><?= e($error) ?></div>
    <?php endif; ?>

    <div class="flex gap-6 mb-12 flex-wrap">
      <?php foreach ($filters as $i => $category): ?>
        <button type="button" data-filter="<?= e($category) ?>" class="text-xs tracking-[0.2em] uppercase pb-1 border-b-2 transition-colors <?= $i === 0 ? 'border-accent text-foreground' : 'border-transparent text-muted-foreground hover:text-foreground' ?>"><?= e($category) ?></button>
      <?php endforeach; ?>
    </div>

    <?php if (empty($items)): ?>
      <?php if (count($filters) <= 1): ?>
        <p class="text-sm text-muted-foreground">No data available. Collections are empty right now. Add collections and clothes from admin to show them here.</p>
      <?php else: ?>
        <p class="text-sm text-muted-foreground">No data available. No collection items with images yet. Add clothes from admin with an image to display them here.</p>
      <?php endif; ?>
    <?php else: ?>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
        <?php foreach ($items as $item): ?>
          <div data-card-category="<?= e($item['category']) ?>">
            <button
              type="button"
              data-open-modal
              data-img="<?= e($item['img']) ?>"
              data-title="<?= e($item['title']) ?>"
              data-category="<?= e($item['category']) ?>"
              data-desc="<?= e($item['desc']) ?>"
              class="w-full text-left group image-zoom"
            >
              <div class="aspect-[3/4] overflow-hidden mb-4">
                <img src="<?= e($item['img']) ?>" alt="<?= e($item['title']) ?>" class="w-full h-full object-cover" loading="lazy" width="800" height="1100" />
              </div>
              <p class="text-xs tracking-[0.2em] uppercase text-muted-foreground mb-1"><?= e($item['category']) ?></p>
              <h3 class="font-display text-lg tracking-wider group-hover:text-accent transition-colors"><?= e($item['title']) ?></h3>
            </button>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </section>

  <div id="collection-modal" class="fixed inset-0 z-50 bg-foreground/60 backdrop-blur-sm flex items-center justify-center p-6 hidden">
    <div class="bg-background max-w-3xl w-full grid grid-cols-1 md:grid-cols-2 overflow-hidden relative">
      <button type="button" data-close-modal class="absolute top-4 right-4 z-10 p-2 hover:bg-secondary rounded-full transition-colors">Close</button>
      <div class="aspect-[3/4] md:aspect-auto">
        <img id="modal-image" src="" alt="" class="w-full h-full object-cover" />
      </div>
      <div class="p-8 md:p-12 flex flex-col justify-center">
        <p id="modal-category" class="text-xs tracking-[0.3em] uppercase text-muted-foreground mb-3"></p>
        <h2 id="modal-title" class="font-display text-2xl md:text-3xl tracking-wider mb-4"></h2>
        <p id="modal-desc" class="text-muted-foreground leading-relaxed"></p>
      </div>
    </div>
  </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
