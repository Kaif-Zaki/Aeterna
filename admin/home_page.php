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
$home = is_array($content['home'] ?? null) ? $content['home'] : [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $home['hero_title'] = trim((string)($_POST['hero_title'] ?? ''));
    $home['hero_subtitle'] = trim((string)($_POST['hero_subtitle'] ?? ''));
    $home['featured_title'] = trim((string)($_POST['featured_title'] ?? ''));
    $home['featured_subtitle'] = trim((string)($_POST['featured_subtitle'] ?? ''));
    $home['quote_text'] = trim((string)($_POST['quote_text'] ?? ''));
    $home['quote_author'] = trim((string)($_POST['quote_author'] ?? ''));

    if (
        $home['hero_title'] === '' ||
        $home['hero_subtitle'] === '' ||
        $home['featured_title'] === '' ||
        $home['featured_subtitle'] === '' ||
        $home['quote_text'] === '' ||
        $home['quote_author'] === ''
    ) {
        $error = 'All home page fields are required.';
    } else {
        $content['home'] = $home;
        if (cmsSave($content)) {
            $success = 'Home page content updated successfully.';
        } else {
            $error = 'Failed to save home page content.';
        }
    }
}

adminLayoutStart('Aeterna | Manage Home Page', 'home_page');
?>
<div class="admin-section">
  <p class="admin-kicker">Page Management</p>
  <h1 class="admin-page-title">Manage Home Page</h1>
  <p class="admin-lead">Update hero text, featured section heading, and quote block on the homepage.</p>

  <?php if ($success !== ''): ?>
    <div class="mb-6 bg-card border border-border px-4 py-3 text-sm"><?= e($success) ?></div>
  <?php endif; ?>
  <?php if ($error !== ''): ?>
    <div class="mb-6 bg-card border border-destructive px-4 py-3 text-sm text-destructive"><?= e($error) ?></div>
  <?php endif; ?>

  <form method="post" action="/admin/home-page.php" class="admin-panel space-y-4">
    <div>
      <label class="admin-stat-label block mb-2">Hero Title</label>
      <input class="admin-input" name="hero_title" value="<?= e((string)($home['hero_title'] ?? '')) ?>" maxlength="120" required />
    </div>
    <div>
      <label class="admin-stat-label block mb-2">Hero Subtitle</label>
      <textarea class="admin-input" name="hero_subtitle" rows="3" required><?= e((string)($home['hero_subtitle'] ?? '')) ?></textarea>
    </div>
    <div>
      <label class="admin-stat-label block mb-2">Featured Title</label>
      <input class="admin-input" name="featured_title" value="<?= e((string)($home['featured_title'] ?? '')) ?>" maxlength="140" required />
    </div>
    <div>
      <label class="admin-stat-label block mb-2">Featured Subtitle</label>
      <textarea class="admin-input" name="featured_subtitle" rows="3" required><?= e((string)($home['featured_subtitle'] ?? '')) ?></textarea>
    </div>
    <div>
      <label class="admin-stat-label block mb-2">Quote Text</label>
      <textarea class="admin-input" name="quote_text" rows="4" required><?= e((string)($home['quote_text'] ?? '')) ?></textarea>
    </div>
    <div>
      <label class="admin-stat-label block mb-2">Quote Author</label>
      <input class="admin-input" name="quote_author" value="<?= e((string)($home['quote_author'] ?? '')) ?>" maxlength="120" required />
    </div>
    <button type="submit" class="admin-btn-primary">Save Home Content</button>
  </form>
</div>
<?php adminLayoutEnd(); ?>
