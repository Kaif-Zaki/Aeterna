<?php
declare(strict_types=1);

require __DIR__ . '/../includes/bootstrap.php';
require __DIR__ . '/../includes/admin_auth.php';
require __DIR__ . '/../includes/admin_layout.php';
requireAdminLogin();

$error = '';
$success = '';
$dbReady = false;
$uploadDir = realpath(__DIR__ . '/../uploads') ?: (__DIR__ . '/../uploads');
$clothesDir = $uploadDir . '/clothes';

if (!is_dir($clothesDir)) {
    @mkdir($clothesDir, 0775, true);
}

try {
    require __DIR__ . '/../config.php';
    $dbReady = true;
} catch (Throwable $e) {
    error_log('Clothes DB bootstrap error: ' . $e->getMessage());
    $error = 'Database is unavailable. Manage Clothes cannot load right now.';
}

/** @throws RuntimeException */
function storeClothesUpload(string $fieldName, string $clothesDir): string
{
    if (!isset($_FILES[$fieldName]) || !is_array($_FILES[$fieldName])) {
        throw new RuntimeException('No upload received.');
    }

    $file = $_FILES[$fieldName];

    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        throw new RuntimeException('Cloth image is required.');
    }

    if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Upload failed. Please try again.');
    }

    $tmpPath = (string)($file['tmp_name'] ?? '');
    if ($tmpPath === '' || !is_uploaded_file($tmpPath)) {
        throw new RuntimeException('Invalid uploaded file.');
    }

    $size = (int)($file['size'] ?? 0);
    if ($size <= 0 || $size > 8 * 1024 * 1024) {
        throw new RuntimeException('Image must be smaller than 8MB.');
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = (string)$finfo->file($tmpPath);

    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/gif' => 'gif',
    ];

    if (!isset($allowed[$mime])) {
        throw new RuntimeException('Only JPG, PNG, WEBP, or GIF images are allowed.');
    }

    $name = 'cloth_' . bin2hex(random_bytes(12)) . '.' . $allowed[$mime];
    $target = rtrim($clothesDir, '/') . '/' . $name;

    if (!move_uploaded_file($tmpPath, $target)) {
        throw new RuntimeException('Could not save uploaded image.');
    }

    return '/uploads/clothes/' . $name;
}

function deleteManagedClothesImage(string $path, string $uploadDir): void
{
    if (strpos($path, '/uploads/clothes/') !== 0) {
        return;
    }

    $full = rtrim($uploadDir, '/') . '/' . ltrim(substr($path, strlen('/uploads/')), '/');
    if (is_file($full)) {
        @unlink($full);
    }
}

function validateClothesName(string $name): string
{
    $trimmed = trim($name);
    if ($trimmed === '') {
        throw new RuntimeException('Name is required.');
    }

    if (mb_strlen($trimmed) > 160) {
        throw new RuntimeException('Name must be 160 characters or fewer.');
    }

    return $trimmed;
}

if ($dbReady && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = (string)($_POST['action'] ?? '');

    try {
        if ($action === 'add') {
            $name = validateClothesName((string)($_POST['name'] ?? ''));
            $categoryId = (int)($_POST['category_id'] ?? 0);
            $price = trim((string)($_POST['price'] ?? ''));

            if ($categoryId <= 0) {
                throw new RuntimeException('Collection is required.');
            }

            $catCheck = $pdo->prepare('SELECT id FROM categories WHERE id = :id LIMIT 1');
            $catCheck->execute([':id' => $categoryId]);
            if (!$catCheck->fetch()) {
                throw new RuntimeException('Selected collection does not exist.');
            }

            $priceValue = null;
            if ($price !== '') {
                if (!is_numeric($price)) {
                    throw new RuntimeException('Price must be a valid number.');
                }
                if ((float)$price < 0) {
                    throw new RuntimeException('Price cannot be negative.');
                }
                $priceValue = number_format((float)$price, 2, '.', '');
            }

            $imagePath = storeClothesUpload('image_file', $clothesDir);
            $stmt = $pdo->prepare('INSERT INTO clothes (name, category_id, price, image_path) VALUES (:name, :category_id, :price, :image_path)');
            $stmt->execute([
                ':name' => $name,
                ':category_id' => $categoryId,
                ':price' => $priceValue,
                ':image_path' => $imagePath,
            ]);
            $success = 'Clothing item added successfully.';
        }

        if ($action === 'delete') {
            $id = (int)($_POST['id'] ?? 0);
            if ($id <= 0) {
                throw new RuntimeException('Invalid clothing item selected.');
            }

            $stmt = $pdo->prepare('SELECT image_path FROM clothes WHERE id = :id');
            $stmt->execute([':id' => $id]);
            $current = $stmt->fetch();

            $delete = $pdo->prepare('DELETE FROM clothes WHERE id = :id');
            $delete->execute([':id' => $id]);
            if ($delete->rowCount() === 0) {
                throw new RuntimeException('Clothing item not found or already deleted.');
            }

            if ($current && is_string($current['image_path'] ?? null)) {
                deleteManagedClothesImage((string)$current['image_path'], $uploadDir);
            }
            $success = 'Clothing item deleted successfully.';
        }
    } catch (Throwable $e) {
        error_log('Clothes manage error: ' . $e->getMessage());
        $error = $e instanceof RuntimeException ? $e->getMessage() : 'Action failed. Please check input values.';
    }
}

$categories = [];
$clothes = [];

if ($dbReady) {
    try {
        $categories = $pdo->query('SELECT id, name FROM categories ORDER BY name')->fetchAll();
        $clothes = $pdo->query(
            'SELECT cl.id, cl.name, cl.price, cl.image_path, c.name AS category_name
             FROM clothes cl
             INNER JOIN categories c ON c.id = cl.category_id
             ORDER BY cl.id DESC'
        )->fetchAll();
    } catch (Throwable $e) {
        error_log('Clothes fetch error: ' . $e->getMessage());
        $error = 'Could not load clothes or collections.';
    }
}

adminLayoutStart('Aeterna | Manage Clothes', 'clothes');
?>
<div class="admin-section">
  <p class="admin-kicker">Catalog</p>
  <h1 class="admin-page-title">Manage Clothes</h1>

  <?php if ($error !== ''): ?>
    <div class="mb-6 bg-card border border-destructive px-4 py-3 text-sm text-destructive"><?= e($error) ?></div>
  <?php endif; ?>
  <?php if ($success !== ''): ?>
    <div class="mb-6 bg-card border border-border px-4 py-3 text-sm"><?= e($success) ?></div>
  <?php endif; ?>

  <div class="admin-panel mb-8">
    <h2 class="admin-panel-title">Add Clothing Item</h2>
    <form method="post" action="/admin/clothes.php" enctype="multipart/form-data" class="admin-form-grid admin-form-grid-3">
      <input type="hidden" name="action" value="add" />
      <input type="text" name="name" placeholder="Item name" class="admin-input" required />
      <select name="category_id" class="admin-input" required <?= (!$dbReady || empty($categories)) ? 'disabled' : '' ?>>
        <option value="">Select collection</option>
        <?php foreach ($categories as $cat): ?>
          <option value="<?= (int)$cat['id'] ?>"><?= e((string)$cat['name']) ?></option>
        <?php endforeach; ?>
      </select>
      <input type="number" step="0.01" min="0" name="price" placeholder="Price (optional)" class="admin-input" />
      <input type="file" name="image_file" accept="image/*" class="admin-input" required />
      <button type="submit" class="admin-btn-primary" <?= (!$dbReady || empty($categories)) ? 'disabled' : '' ?>>Add</button>
    </form>
    <?php if (!$dbReady): ?>
      <p class="mt-3 text-sm text-muted-foreground">No data available. Database is not connected.</p>
    <?php elseif (empty($categories)): ?>
      <p class="mt-3 text-sm text-muted-foreground">No data available. Add a collection first before adding clothes.</p>
    <?php endif; ?>
  </div>

  <div class="admin-panel">
    <h2 class="admin-panel-title">Clothes List</h2>
    <div class="space-y-3">
      <?php if (empty($clothes)): ?>
        <p class="text-sm text-muted-foreground">No data available. No clothes have been added yet.</p>
      <?php else: ?>
        <?php foreach ($clothes as $item): ?>
          <div class="admin-list-row">
            <div class="flex items-center gap-4">
              <?php if (!empty($item['image_path'])): ?>
                <img src="<?= e((string)$item['image_path']) ?>" alt="<?= e((string)$item['name']) ?>" class="admin-clothes-thumb" />
              <?php endif; ?>
              <div>
              <p class="font-display text-lg tracking-wide"><?= e((string)$item['name']) ?></p>
              <p class="text-xs tracking-[0.15em] uppercase text-muted-foreground">
                Collection: <?= e((string)$item['category_name']) ?>
                <?php if ($item['price'] !== null): ?> | Price: <?= e((string)$item['price']) ?><?php endif; ?>
              </p>
              </div>
            </div>
            <form method="post" action="/admin/clothes.php" onsubmit="return confirm('Delete this item?');">
              <input type="hidden" name="action" value="delete" />
              <input type="hidden" name="id" value="<?= (int)$item['id'] ?>" />
              <button type="submit" class="admin-btn-danger">Delete</button>
            </form>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</div>
<?php adminLayoutEnd(); ?>
