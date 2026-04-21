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
$galleryDir = $uploadDir . '/gallery';

if (!is_dir($galleryDir)) {
    @mkdir($galleryDir, 0775, true);
}

try {
    require __DIR__ . '/../config.php';
    $dbReady = true;
} catch (Throwable $e) {
    error_log('Gallery DB bootstrap error: ' . $e->getMessage());
    $error = 'Database is unavailable. Manage Gallery cannot load right now.';
}

/** @throws RuntimeException */
function storeGalleryUpload(string $fieldName, string $galleryDir): string
{
    if (!isset($_FILES[$fieldName]) || !is_array($_FILES[$fieldName])) {
        throw new RuntimeException('No upload received.');
    }

    $file = $_FILES[$fieldName];

    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        throw new RuntimeException('Please select an image.');
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

    $name = 'gallery_' . bin2hex(random_bytes(12)) . '.' . $allowed[$mime];
    $target = rtrim($galleryDir, '/') . '/' . $name;

    if (!move_uploaded_file($tmpPath, $target)) {
        throw new RuntimeException('Could not save uploaded image.');
    }

    return '/uploads/gallery/' . $name;
}

function deleteManagedGalleryImage(string $path, string $uploadDir): void
{
    if (strpos($path, '/uploads/gallery/') !== 0) {
        return;
    }

    $full = rtrim($uploadDir, '/') . '/' . ltrim(substr($path, strlen('/uploads/')), '/');
    if (is_file($full)) {
        @unlink($full);
    }
}

if ($dbReady && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = (string)($_POST['action'] ?? '');

    try {
        if ($action === 'add') {
            $altText = trim((string)($_POST['alt_text'] ?? ''));
            $sortOrder = (int)($_POST['sort_order'] ?? 0);
            $isTall = isset($_POST['is_tall']) ? 1 : 0;
            $imagePath = storeGalleryUpload('image_file', $galleryDir);

            if ($altText === '') {
                $altText = 'Gallery Image';
            }

            $stmt = $pdo->prepare('INSERT INTO gallery_items (image_path, alt_text, is_tall, sort_order) VALUES (:image_path, :alt_text, :is_tall, :sort_order)');
            $stmt->execute([
                ':image_path' => $imagePath,
                ':alt_text' => $altText,
                ':is_tall' => $isTall,
                ':sort_order' => $sortOrder,
            ]);

            $success = 'Gallery item added.';
        }

        if ($action === 'update') {
            $id = (int)($_POST['id'] ?? 0);
            if ($id <= 0) {
                throw new RuntimeException('Invalid item ID.');
            }

            $stmt = $pdo->prepare('SELECT image_path FROM gallery_items WHERE id = :id');
            $stmt->execute([':id' => $id]);
            $current = $stmt->fetch();

            if (!$current) {
                throw new RuntimeException('Gallery item not found.');
            }

            $altText = trim((string)($_POST['alt_text'] ?? ''));
            $sortOrder = (int)($_POST['sort_order'] ?? 0);
            $isTall = isset($_POST['is_tall']) ? 1 : 0;
            $imagePath = (string)$current['image_path'];

            if (isset($_FILES['image_file']) && is_array($_FILES['image_file']) && (int)($_FILES['image_file']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
                $newPath = storeGalleryUpload('image_file', $galleryDir);
                deleteManagedGalleryImage($imagePath, $uploadDir);
                $imagePath = $newPath;
            }

            if ($altText === '') {
                $altText = 'Gallery Image';
            }

            $update = $pdo->prepare('UPDATE gallery_items SET image_path = :image_path, alt_text = :alt_text, is_tall = :is_tall, sort_order = :sort_order WHERE id = :id');
            $update->execute([
                ':image_path' => $imagePath,
                ':alt_text' => $altText,
                ':is_tall' => $isTall,
                ':sort_order' => $sortOrder,
                ':id' => $id,
            ]);

            $success = 'Gallery item updated.';
        }

        if ($action === 'delete') {
            $id = (int)($_POST['id'] ?? 0);
            if ($id <= 0) {
                throw new RuntimeException('Invalid item ID.');
            }

            $stmt = $pdo->prepare('SELECT image_path FROM gallery_items WHERE id = :id');
            $stmt->execute([':id' => $id]);
            $current = $stmt->fetch();

            if ($current) {
                $del = $pdo->prepare('DELETE FROM gallery_items WHERE id = :id');
                $del->execute([':id' => $id]);
                deleteManagedGalleryImage((string)$current['image_path'], $uploadDir);
                $success = 'Gallery item deleted.';
            }
        }
    } catch (Throwable $e) {
        error_log('Gallery manage error: ' . $e->getMessage());
        $error = $e instanceof RuntimeException ? $e->getMessage() : 'Gallery action failed.';
    }
}

$rows = [];
if ($dbReady) {
    try {
        $rows = $pdo->query('SELECT id, image_path, alt_text, is_tall, sort_order FROM gallery_items ORDER BY sort_order ASC, id DESC')->fetchAll();
    } catch (Throwable $e) {
        error_log('Gallery fetch error: ' . $e->getMessage());
        $error = 'Could not load gallery items.';
    }
}

adminLayoutStart('Aeterna | Manage Gallery', 'gallery');
?>
<div class="admin-section">
  <p class="admin-kicker">Gallery Management</p>
  <h1 class="admin-page-title">Manage Gallery</h1>
  <p class="admin-lead">Upload images, update captions, and control gallery layout order.</p>

  <?php if ($success !== ''): ?>
    <div class="mb-6 bg-card border border-border px-4 py-3 text-sm"><?= e($success) ?></div>
  <?php endif; ?>
  <?php if ($error !== ''): ?>
    <div class="mb-6 bg-card border border-destructive px-4 py-3 text-sm text-destructive"><?= e($error) ?></div>
  <?php endif; ?>

  <div class="admin-panel mb-8">
    <h2 class="admin-panel-title">Add New Image</h2>
    <form method="post" action="/admin/gallery.php" enctype="multipart/form-data" class="admin-form-grid admin-form-grid-3">
      <input type="hidden" name="action" value="add" />
      <input type="file" name="image_file" accept="image/*" class="admin-input" required <?= $dbReady ? '' : 'disabled' ?> />
      <input type="text" name="alt_text" placeholder="Alt text" class="admin-input" <?= $dbReady ? '' : 'disabled' ?> />
      <input type="number" name="sort_order" placeholder="Sort order" value="0" class="admin-input" <?= $dbReady ? '' : 'disabled' ?> />
      <label class="admin-checkbox-wrap"><input type="checkbox" name="is_tall" <?= $dbReady ? '' : 'disabled' ?> /> Tall Card</label>
      <button type="submit" class="admin-btn-primary" <?= $dbReady ? '' : 'disabled' ?>>Upload</button>
    </form>
    <?php if (!$dbReady): ?>
      <p class="mt-3 text-sm text-muted-foreground">No data available. Database is not connected.</p>
    <?php endif; ?>
  </div>

  <div class="admin-panel">
    <h2 class="admin-panel-title">Current Gallery Items</h2>
    <div class="space-y-4">
      <?php if (empty($rows)): ?>
        <p class="text-sm text-muted-foreground">No gallery items yet. Upload your first image.</p>
      <?php else: ?>
        <?php foreach ($rows as $row): ?>
          <article class="admin-list-row admin-gallery-row">
            <img src="<?= e((string)$row['image_path']) ?>" alt="<?= e((string)$row['alt_text']) ?>" class="admin-gallery-thumb" />

            <form method="post" action="/admin/gallery.php" enctype="multipart/form-data" class="admin-gallery-edit">
              <input type="hidden" name="action" value="update" />
              <input type="hidden" name="id" value="<?= (int)$row['id'] ?>" />

              <div class="admin-gallery-fields">
                <input type="text" name="alt_text" value="<?= e((string)$row['alt_text']) ?>" class="admin-input" />
                <input type="number" name="sort_order" value="<?= (int)$row['sort_order'] ?>" class="admin-input" />
                <input type="file" name="image_file" accept="image/*" class="admin-input" />
                <label class="admin-checkbox-wrap"><input type="checkbox" name="is_tall" <?= ((int)$row['is_tall'] === 1 ? 'checked' : '') ?> /> Tall Card</label>
              </div>

              <div class="admin-gallery-actions">
                <button type="submit" class="admin-btn-primary">Save</button>
              </div>
            </form>

            <form method="post" action="/admin/gallery.php" onsubmit="return confirm('Delete this gallery item?');">
              <input type="hidden" name="action" value="delete" />
              <input type="hidden" name="id" value="<?= (int)$row['id'] ?>" />
              <button type="submit" class="admin-btn-danger">Delete</button>
            </form>
          </article>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</div>
<?php adminLayoutEnd(); ?>
