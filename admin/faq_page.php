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
$faq = is_array($content['faq'] ?? null) ? $content['faq'] : [];
$faqItems = is_array($faq['items'] ?? null) ? $faq['items'] : [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $faq['heading'] = trim((string)($_POST['heading'] ?? ''));
    $faq['subtitle'] = trim((string)($_POST['subtitle'] ?? ''));

    $questions = $_POST['question'] ?? [];
    $answers = $_POST['answer'] ?? [];
    $items = [];

    if (is_array($questions) && is_array($answers)) {
        $max = max(count($questions), count($answers));
        for ($i = 0; $i < $max; $i++) {
            $q = trim((string)($questions[$i] ?? ''));
            $a = trim((string)($answers[$i] ?? ''));
            if ($q === '' && $a === '') {
                continue;
            }
            if ($q === '' || $a === '') {
                $error = 'Each FAQ item must have both question and answer.';
                break;
            }
            $items[] = ['q' => $q, 'a' => $a];
        }
    }

    if ($error === '') {
        if ($faq['heading'] === '' || $faq['subtitle'] === '') {
            $error = 'FAQ heading and subtitle are required.';
        } elseif (count($items) === 0) {
            $error = 'At least one FAQ item is required.';
        } else {
            $faq['items'] = $items;
            $content['faq'] = $faq;
            if (cmsSave($content)) {
                $success = 'FAQ content updated successfully.';
                $faqItems = $items;
            } else {
                $error = 'Failed to save FAQ content.';
            }
        }
    }
}

if (empty($faqItems)) {
    $faqItems = [
        ['q' => '', 'a' => ''],
        ['q' => '', 'a' => ''],
        ['q' => '', 'a' => ''],
    ];
}

adminLayoutStart('Aeterna | Manage FAQ Page', 'faq_page');
?>
<div class="admin-section">
  <p class="admin-kicker">Page Management</p>
  <h1 class="admin-page-title">Manage FAQ Page</h1>
  <p class="admin-lead">Edit FAQ heading, subtitle, and all question-answer items.</p>

  <?php if ($success !== ''): ?>
    <div class="mb-6 bg-card border border-border px-4 py-3 text-sm"><?= e($success) ?></div>
  <?php endif; ?>
  <?php if ($error !== ''): ?>
    <div class="mb-6 bg-card border border-destructive px-4 py-3 text-sm text-destructive"><?= e($error) ?></div>
  <?php endif; ?>

  <form method="post" action="/admin/faq-page.php" class="admin-panel space-y-4">
    <div>
      <label class="admin-stat-label block mb-2">Heading</label>
      <input class="admin-input" name="heading" value="<?= e((string)($faq['heading'] ?? 'FAQ')) ?>" maxlength="120" required />
    </div>
    <div>
      <label class="admin-stat-label block mb-2">Subtitle</label>
      <input class="admin-input" name="subtitle" value="<?= e((string)($faq['subtitle'] ?? 'Common questions answered')) ?>" maxlength="255" required />
    </div>

    <?php foreach ($faqItems as $index => $item): ?>
      <div class="border border-border p-4">
        <p class="admin-stat-label mb-3">FAQ Item <?= (int)$index + 1 ?></p>
        <div class="space-y-3">
          <input class="admin-input" name="question[]" placeholder="Question" value="<?= e((string)($item['q'] ?? '')) ?>" />
          <textarea class="admin-input" name="answer[]" rows="3" placeholder="Answer"><?= e((string)($item['a'] ?? '')) ?></textarea>
        </div>
      </div>
    <?php endforeach; ?>

    <button type="submit" class="admin-btn-primary">Save FAQ Content</button>
  </form>
</div>
<?php adminLayoutEnd(); ?>
