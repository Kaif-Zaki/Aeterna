<?php
declare(strict_types=1);

require __DIR__ . '/../includes/bootstrap.php';
require __DIR__ . '/../includes/admin_auth.php';
require __DIR__ . '/../includes/admin_layout.php';
requireAdminLogin();

$stats = [
    'contacts' => 0,
    'categories' => 0,
    'clothes' => 0,
    'gallery' => 0,
];
$dbError = '';

try {
    require __DIR__ . '/../config.php';

    $stats['contacts'] = (int)$pdo->query('SELECT COUNT(*) FROM contacts')->fetchColumn();
    $stats['categories'] = (int)$pdo->query('SELECT COUNT(*) FROM categories')->fetchColumn();
    $stats['clothes'] = (int)$pdo->query('SELECT COUNT(*) FROM clothes')->fetchColumn();
    $stats['gallery'] = (int)$pdo->query('SELECT COUNT(*) FROM gallery_items')->fetchColumn();
} catch (Throwable $e) {
    error_log('Admin profile DB error: ' . $e->getMessage());
    $dbError = 'Could not load profile statistics.';
}

adminLayoutStart('Aeterna | Admin Profile', 'profile');
?>
<div class="admin-section">
  <p class="admin-kicker">Admin Profile</p>
  <h1 class="admin-page-title">Welcome, <?= e((string)($_SESSION['admin_email'] ?? 'admin@aeterna.com')) ?></h1>
  <p class="admin-lead">Use the sidebar to manage collections, clothes, gallery, and page content. This area is visible only to admin users.</p>

  <?php if ($dbError !== ''): ?>
    <div class="mb-6 bg-card border border-destructive px-4 py-3 text-sm text-destructive"><?= e($dbError) ?></div>
  <?php endif; ?>

  <div class="admin-stats-grid admin-stats-grid-four">
    <article class="admin-stat-card">
      <p class="admin-stat-label">Contact Messages</p>
      <p class="admin-stat-value"><?= $stats['contacts'] ?></p>
    </article>
    <article class="admin-stat-card">
      <p class="admin-stat-label">Collections</p>
      <p class="admin-stat-value"><?= $stats['categories'] ?></p>
    </article>
    <article class="admin-stat-card">
      <p class="admin-stat-label">Clothes</p>
      <p class="admin-stat-value"><?= $stats['clothes'] ?></p>
    </article>
    <article class="admin-stat-card">
      <p class="admin-stat-label">Gallery Images</p>
      <p class="admin-stat-value"><?= $stats['gallery'] ?></p>
    </article>
  </div>
</div>
<?php adminLayoutEnd(); ?>
