<?php
declare(strict_types=1);

require __DIR__ . '/../includes/bootstrap.php';
require __DIR__ . '/../includes/admin_auth.php';
require __DIR__ . '/../includes/admin_layout.php';
requireAdminLogin();

$error = '';
$success = '';
$dbReady = false;

try {
    require __DIR__ . '/../config.php';
    $dbReady = true;
} catch (Throwable $e) {
    error_log('Categories DB bootstrap error: ' . $e->getMessage());
    $error = 'Database is unavailable. Manage Collections cannot load right now.';
}

function validateCategoryName(string $name): string
{
    $trimmed = trim($name);
    if ($trimmed === '') {
        throw new RuntimeException('Collection name is required.');
    }

    if (mb_strlen($trimmed) > 120) {
        throw new RuntimeException('Collection name must be 120 characters or fewer.');
    }

    return $trimmed;
}

if ($dbReady && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = (string)($_POST['action'] ?? '');

    try {
        if ($action === 'add') {
            $name = validateCategoryName((string)($_POST['name'] ?? ''));
            $stmt = $pdo->prepare('SELECT id FROM categories WHERE LOWER(name) = LOWER(:name) LIMIT 1');
            $stmt->execute([':name' => $name]);
            if ($stmt->fetch()) {
                throw new RuntimeException('A collection with this name already exists.');
            }

            $insert = $pdo->prepare('INSERT INTO categories (name) VALUES (:name)');
            $insert->execute([':name' => $name]);
            $success = 'Collection added successfully.';
        }

        if ($action === 'delete') {
            $id = (int)($_POST['id'] ?? 0);
            if ($id <= 0) {
                throw new RuntimeException('Invalid collection selected.');
            }

            $countStmt = $pdo->prepare('SELECT COUNT(*) FROM clothes WHERE category_id = :id');
            $countStmt->execute([':id' => $id]);
            $itemsCount = (int)$countStmt->fetchColumn();

            if ($itemsCount > 0) {
                throw new RuntimeException('This collection has clothes. Remove those items first.');
            }

            $stmt = $pdo->prepare('DELETE FROM categories WHERE id = :id');
            $stmt->execute([':id' => $id]);
            if ($stmt->rowCount() === 0) {
                throw new RuntimeException('Collection not found or already deleted.');
            }

            $success = 'Collection deleted successfully.';
        }
    } catch (Throwable $e) {
        error_log('Categories manage error: ' . $e->getMessage());
        $error = $e instanceof RuntimeException
            ? $e->getMessage()
            : 'Action failed. Please try again.';
    }
}

$rows = [];
if ($dbReady) {
    try {
        $rows = $pdo->query(
            'SELECT c.id, c.name, COUNT(cl.id) AS clothes_count
             FROM categories c
             LEFT JOIN clothes cl ON cl.category_id = c.id
             GROUP BY c.id, c.name
             ORDER BY c.id DESC'
        )->fetchAll();
    } catch (Throwable $e) {
        error_log('Categories fetch error: ' . $e->getMessage());
        $error = 'Could not load collections.';
    }
}

adminLayoutStart('Aeterna | Manage Collections', 'categories');
?>
<div class="admin-section">
  <p class="admin-kicker">Catalog</p>
  <h1 class="admin-page-title">Manage Collections</h1>

  <?php if ($error !== ''): ?>
    <div class="mb-6 bg-card border border-destructive px-4 py-3 text-sm text-destructive"><?= e($error) ?></div>
  <?php endif; ?>
  <?php if ($success !== ''): ?>
    <div class="mb-6 bg-card border border-border px-4 py-3 text-sm"><?= e($success) ?></div>
  <?php endif; ?>

  <div class="admin-panel mb-8">
    <h2 class="admin-panel-title">Add Collection</h2>
    <form method="post" action="/admin/categories.php" class="admin-form-grid">
      <input type="hidden" name="action" value="add" />
      <input type="text" name="name" placeholder="Collection name" class="admin-input" required <?= $dbReady ? '' : 'disabled' ?> />
      <button type="submit" class="admin-btn-primary" <?= $dbReady ? '' : 'disabled' ?>>Add</button>
    </form>
  </div>

  <div class="admin-panel">
    <h2 class="admin-panel-title">Collection List</h2>
    <div class="space-y-3">
      <?php if (empty($rows)): ?>
        <p class="text-sm text-muted-foreground">No data available. Collections are empty right now.</p>
      <?php else: ?>
        <?php foreach ($rows as $row): ?>
          <div class="admin-list-row">
            <div>
              <p class="font-display text-lg tracking-wide"><?= e((string)$row['name']) ?></p>
              <p class="text-xs tracking-[0.15em] uppercase text-muted-foreground">Items: <?= e((string)$row['clothes_count']) ?></p>
            </div>
            <form method="post" action="/admin/categories.php" onsubmit="return confirm('Delete this collection?');">
              <input type="hidden" name="action" value="delete" />
              <input type="hidden" name="id" value="<?= (int)$row['id'] ?>" />
              <button type="submit" class="admin-btn-danger">Delete</button>
            </form>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</div>
<?php adminLayoutEnd(); ?>
