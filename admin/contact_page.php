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
$cfg = $content['contact'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cfg['heading'] = trim((string)($_POST['heading'] ?? ''));
    $cfg['subtitle'] = trim((string)($_POST['subtitle'] ?? ''));
    $cfg['visit_title'] = trim((string)($_POST['visit_title'] ?? ''));
    $cfg['visit_address'] = trim((string)($_POST['visit_address'] ?? ''));
    $cfg['email_title'] = trim((string)($_POST['email_title'] ?? ''));
    $cfg['email'] = trim((string)($_POST['email'] ?? ''));
    $cfg['follow_title'] = trim((string)($_POST['follow_title'] ?? ''));
    $cfg['instagram_url'] = trim((string)($_POST['instagram_url'] ?? ''));
    $cfg['twitter_url'] = trim((string)($_POST['twitter_url'] ?? ''));
    $cfg['map_embed'] = trim((string)($_POST['map_embed'] ?? ''));

    if (
        $cfg['heading'] === '' ||
        $cfg['subtitle'] === '' ||
        $cfg['visit_title'] === '' ||
        $cfg['visit_address'] === '' ||
        $cfg['email_title'] === '' ||
        $cfg['follow_title'] === ''
    ) {
        $error = 'Heading, subtitle, visit, email, and follow fields are required.';
    } elseif ($cfg['email'] !== '' && !filter_var($cfg['email'], FILTER_VALIDATE_EMAIL)) {
        $error = 'Please provide a valid contact email.';
    } else {
        $content['contact'] = $cfg;
        if (cmsSave($content)) {
            $success = 'Contact page content updated successfully.';
        } else {
            $error = 'Failed to save content.';
        }
    }
}

adminLayoutStart('Aeterna | Manage Contact Page', 'contact_page');
?>
<div class="admin-section">
  <p class="admin-kicker">Page Management</p>
  <h1 class="admin-page-title">Manage Contact Page</h1>
  <p class="admin-lead">Edit heading, contact details, social links, and map embed URL.</p>

  <?php if ($success !== ''): ?>
    <div class="mb-6 bg-card border border-border px-4 py-3 text-sm"><?= e($success) ?></div>
  <?php endif; ?>
  <?php if ($error !== ''): ?>
    <div class="mb-6 bg-card border border-destructive px-4 py-3 text-sm text-destructive"><?= e($error) ?></div>
  <?php endif; ?>

  <form method="post" action="/admin/contact-page.php" class="admin-panel space-y-4">
    <div><label class="admin-stat-label block mb-2">Heading</label><input class="admin-input" name="heading" value="<?= e((string)$cfg['heading']) ?>" maxlength="120" required /></div>
    <div><label class="admin-stat-label block mb-2">Subtitle</label><input class="admin-input" name="subtitle" value="<?= e((string)$cfg['subtitle']) ?>" maxlength="255" required /></div>
    <div><label class="admin-stat-label block mb-2">Visit Title</label><input class="admin-input" name="visit_title" value="<?= e((string)$cfg['visit_title']) ?>" maxlength="120" required /></div>
    <div><label class="admin-stat-label block mb-2">Visit Address</label><textarea class="admin-input" name="visit_address" rows="3" required><?= e((string)$cfg['visit_address']) ?></textarea></div>
    <div><label class="admin-stat-label block mb-2">Email Title</label><input class="admin-input" name="email_title" value="<?= e((string)$cfg['email_title']) ?>" maxlength="120" required /></div>
    <div><label class="admin-stat-label block mb-2">Email</label><input class="admin-input" name="email" value="<?= e((string)$cfg['email']) ?>" maxlength="255" /></div>
    <div><label class="admin-stat-label block mb-2">Follow Title</label><input class="admin-input" name="follow_title" value="<?= e((string)$cfg['follow_title']) ?>" maxlength="120" required /></div>
    <div><label class="admin-stat-label block mb-2">Instagram URL</label><input class="admin-input" name="instagram_url" value="<?= e((string)$cfg['instagram_url']) ?>" /></div>
    <div><label class="admin-stat-label block mb-2">Twitter URL</label><input class="admin-input" name="twitter_url" value="<?= e((string)$cfg['twitter_url']) ?>" /></div>
    <div><label class="admin-stat-label block mb-2">Google Map Embed URL</label><textarea class="admin-input" name="map_embed" rows="3"><?= e((string)$cfg['map_embed']) ?></textarea></div>
    <button type="submit" class="admin-btn-primary">Save Contact Content</button>
  </form>
</div>
<?php adminLayoutEnd(); ?>
