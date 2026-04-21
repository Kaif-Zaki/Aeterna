<?php
declare(strict_types=1);

require __DIR__ . '/../includes/bootstrap.php';
require __DIR__ . '/../includes/admin_auth.php';
require __DIR__ . '/../includes/admin_layout.php';
require __DIR__ . '/../includes/cms_content.php';
requireAdminLogin();

$error = '';
$success = '';
$content = cmsLoad();
$about = $content['about'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $about['hero_title'] = trim((string)($_POST['hero_title'] ?? ''));
    $about['hero_text'] = trim((string)($_POST['hero_text'] ?? ''));
    $about['mission_title'] = trim((string)($_POST['mission_title'] ?? ''));
    $about['mission_text_1'] = trim((string)($_POST['mission_text_1'] ?? ''));
    $about['mission_text_2'] = trim((string)($_POST['mission_text_2'] ?? ''));
    $about['founder_role'] = trim((string)($_POST['founder_role'] ?? ''));
    $about['founder_name'] = trim((string)($_POST['founder_name'] ?? ''));
    $about['founder_bio'] = trim((string)($_POST['founder_bio'] ?? ''));

    if (
        $about['hero_title'] === '' ||
        $about['hero_text'] === '' ||
        $about['mission_title'] === '' ||
        $about['mission_text_1'] === '' ||
        $about['mission_text_2'] === '' ||
        $about['founder_role'] === '' ||
        $about['founder_name'] === '' ||
        $about['founder_bio'] === ''
    ) {
        $error = 'All fields are required. Please fill in every section.';
    } else {
        $content['about'] = $about;

        if (cmsSave($content)) {
            $success = 'About page content updated successfully.';
        } else {
            $error = 'Failed to save content.';
        }
    }
}

adminLayoutStart('Aeterna | Manage About Page', 'about_page');
?>
<div class="admin-section">
  <p class="admin-kicker">Page Management</p>
  <h1 class="admin-page-title">Manage About Page</h1>
  <p class="admin-lead">Update user-facing text blocks for your About page.</p>

  <?php if ($success !== ''): ?>
    <div class="mb-6 bg-card border border-border px-4 py-3 text-sm"><?= e($success) ?></div>
  <?php endif; ?>
  <?php if ($error !== ''): ?>
    <div class="mb-6 bg-card border border-destructive px-4 py-3 text-sm text-destructive"><?= e($error) ?></div>
  <?php endif; ?>

  <form method="post" action="/admin/about-page.php" class="admin-panel space-y-4">
    <div>
      <label class="admin-stat-label block mb-2">Hero Title</label>
      <input class="admin-input" name="hero_title" value="<?= e((string)$about['hero_title']) ?>" maxlength="120" required />
    </div>
    <div>
      <label class="admin-stat-label block mb-2">Hero Text</label>
      <textarea class="admin-input" name="hero_text" rows="3" required><?= e((string)$about['hero_text']) ?></textarea>
    </div>
    <div>
      <label class="admin-stat-label block mb-2">Mission Title</label>
      <input class="admin-input" name="mission_title" value="<?= e((string)$about['mission_title']) ?>" maxlength="120" required />
    </div>
    <div>
      <label class="admin-stat-label block mb-2">Mission Text 1</label>
      <textarea class="admin-input" name="mission_text_1" rows="3" required><?= e((string)$about['mission_text_1']) ?></textarea>
    </div>
    <div>
      <label class="admin-stat-label block mb-2">Mission Text 2</label>
      <textarea class="admin-input" name="mission_text_2" rows="3" required><?= e((string)$about['mission_text_2']) ?></textarea>
    </div>
    <div>
      <label class="admin-stat-label block mb-2">Founder Role</label>
      <input class="admin-input" name="founder_role" value="<?= e((string)$about['founder_role']) ?>" maxlength="120" required />
    </div>
    <div>
      <label class="admin-stat-label block mb-2">Founder Name</label>
      <input class="admin-input" name="founder_name" value="<?= e((string)$about['founder_name']) ?>" maxlength="120" required />
    </div>
    <div>
      <label class="admin-stat-label block mb-2">Founder Bio</label>
      <textarea class="admin-input" name="founder_bio" rows="4" required><?= e((string)$about['founder_bio']) ?></textarea>
    </div>
    <button type="submit" class="admin-btn-primary">Save About Content</button>
  </form>
</div>
<?php adminLayoutEnd(); ?>
