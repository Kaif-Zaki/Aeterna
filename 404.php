<?php
declare(strict_types=1);
require __DIR__ . '/includes/bootstrap.php';
$pageTitle = 'Aeterna | Not Found';
$currentPage = '';
require __DIR__ . '/includes/header.php';
?>
<div class="min-h-screen pt-28 pb-16 container-luxury flex flex-col items-center justify-center text-center">
  <h1 class="font-display text-6xl md:text-8xl tracking-[0.15em] uppercase mb-6">404</h1>
  <p class="text-muted-foreground text-lg mb-8">The page you are looking for does not exist.</p>
  <a href="/index.php" class="px-8 py-3 bg-primary text-primary-foreground text-sm tracking-[0.15em] uppercase hover:bg-accent hover:text-accent-foreground transition-all duration-300">Back Home</a>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
