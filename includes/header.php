<?php
declare(strict_types=1);

$pageTitle = $pageTitle ?? 'Aeterna';
$currentPage = $currentPage ?? 'home';
$navItems = [
    ['page' => 'home', 'href' => '/index.php', 'label' => 'Home'],
    ['page' => 'about', 'href' => '/about.php', 'label' => 'About'],
    ['page' => 'collections', 'href' => '/collections.php', 'label' => 'Collections'],
    ['page' => 'gallery', 'href' => '/gallery.php', 'label' => 'Gallery'],
    ['page' => 'contact', 'href' => '/contact.php', 'label' => 'Contact'],
    ['page' => 'faq', 'href' => '/faq.php', 'label' => 'FAQ'],
];
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= e($pageTitle) ?></title>
  <link rel="stylesheet" href="/assets/style.css" />
  <link rel="stylesheet" href="/assets/site.css" />
</head>
<body data-page="<?= e($currentPage) ?>">
  <div id="scroll-progress" class="scroll-progress"></div>
  <nav id="main-nav" class="fixed top-0 left-0 right-0 z-50 transition-all duration-500 bg-transparent py-5">
    <div class="container-luxury flex items-center justify-between gap-4">
      <a href="/index.php" class="font-display text-2xl md:text-3xl tracking-[0.2em] uppercase text-foreground">Aeterna</a>

      <button
        type="button"
        id="nav-menu-toggle"
        class="nav-menu-toggle"
        aria-label="Toggle navigation"
        aria-expanded="false"
        aria-controls="nav-links"
      >
        <span class="nav-menu-icon" aria-hidden="true">
          <span class="nav-menu-bar"></span>
          <span class="nav-menu-bar"></span>
        </span>
      </button>

      <div id="nav-links" class="nav-links flex items-center flex-nowrap justify-end gap-6 md:gap-8 lg:gap-10">
        <?php foreach ($navItems as $item): ?>
          <a href="<?= e($item['href']) ?>" class="nav-link-item px-3 py-2 text-sm tracking-[0.15em] uppercase transition-colors duration-300 hover:text-accent <?= isActivePage($item['page'], $currentPage) ? 'text-accent' : 'text-muted-foreground' ?>"><?= e($item['label']) ?></a>
        <?php endforeach; ?>
        <button type="button" id="theme-toggle" class="nav-theme-btn ml-3 px-3 py-2 rounded-md text-xs tracking-[0.15em] uppercase transition-colors duration-300 hover:bg-secondary border border-border" aria-label="Toggle theme">
          Dark Mode
        </button>
      </div>
    </div>
  </nav>
  <button type="button" id="nav-backdrop" class="nav-backdrop hidden" aria-label="Close navigation"></button>
