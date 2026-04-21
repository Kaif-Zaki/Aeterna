<?php
declare(strict_types=1);
require __DIR__ . '/includes/bootstrap.php';
require __DIR__ . '/includes/cms_content.php';
require __DIR__ . '/includes/contact_mail.php';
$pageTitle = 'Aeterna | Contact';
$currentPage = 'contact';

$content = cmsLoad();
$contactCfg = is_array($content['contact'] ?? null) ? $content['contact'] : [];

function contactValue(array $cfg, string $key, string $fallback = 'No data available'): string
{
    $value = trim((string)($cfg[$key] ?? ''));
    return $value !== '' ? $value : $fallback;
}

$errors = [];
$successMessage = '';
$form = ['name' => '', 'email' => '', 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form['name'] = trim((string)($_POST['name'] ?? ''));
    $form['email'] = trim((string)($_POST['email'] ?? ''));
    $form['message'] = trim((string)($_POST['message'] ?? ''));

    if ($form['name'] === '' || mb_strlen($form['name']) > 100) {
        $errors['name'] = 'Name is required (max 100 chars).';
    }

    if ($form['email'] === '' || mb_strlen($form['email']) > 255 || !filter_var($form['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'A valid email is required (max 255 chars).';
    }

    if ($form['message'] === '' || mb_strlen($form['message']) > 5000) {
        $errors['message'] = 'Message is required (max 5000 chars).';
    }

    if (!$errors) {
        try {
            require __DIR__ . '/config.php';
            $stmt = $pdo->prepare('INSERT INTO contacts (name, email, message) VALUES (:name, :email, :message)');
            $stmt->execute([
                ':name' => $form['name'],
                ':email' => $form['email'],
                ':message' => $form['message'],
            ]);

            $mailError = null;
            if (sendContactNotificationEmail($form['name'], $form['email'], $form['message'], $mailError)) {
                $successMessage = "Message sent successfully. We'll be in touch.";
                $form = ['name' => '', 'email' => '', 'message' => ''];
            } else {
                $errors['general'] = $mailError ?? 'Message saved, but email could not be delivered.';
            }
        } catch (Throwable $e) {
            error_log('Contact page insert error: ' . $e->getMessage());
            $errors['general'] = 'Server error. Please try again later.';
        }
    }
}

require __DIR__ . '/includes/header.php';
?>
<div class="pt-28 pb-16">
  <section class="container-luxury">
    <h1 class="font-display text-4xl md:text-7xl tracking-[0.1em] uppercase mb-4"><?= e(contactValue($contactCfg, 'heading')) ?></h1>
    <p class="text-muted-foreground text-sm tracking-wider mb-16"><?= e(contactValue($contactCfg, 'subtitle', 'No data available for this section.')) ?></p>

    <?php if ($successMessage !== ''): ?>
      <div class="mb-6 bg-card border border-border px-4 py-3 text-sm"><?= e($successMessage) ?></div>
    <?php endif; ?>
    <?php if (isset($errors['general'])): ?>
      <div class="mb-6 bg-card border border-destructive px-4 py-3 text-sm text-destructive"><?= e($errors['general']) ?></div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-16">
      <form method="post" action="/contact.php" class="space-y-6">
        <div>
          <label class="text-xs tracking-[0.2em] uppercase text-muted-foreground block mb-2">Name</label>
          <input type="text" name="name" value="<?= e($form['name']) ?>" class="w-full bg-transparent border-b border-border py-3 text-foreground focus:border-accent outline-none transition-colors" />
          <?php if (isset($errors['name'])): ?><p class="text-destructive text-xs mt-1"><?= e($errors['name']) ?></p><?php endif; ?>
        </div>

        <div>
          <label class="text-xs tracking-[0.2em] uppercase text-muted-foreground block mb-2">Email</label>
          <input type="email" name="email" value="<?= e($form['email']) ?>" class="w-full bg-transparent border-b border-border py-3 text-foreground focus:border-accent outline-none transition-colors" />
          <?php if (isset($errors['email'])): ?><p class="text-destructive text-xs mt-1"><?= e($errors['email']) ?></p><?php endif; ?>
        </div>

        <div>
          <label class="text-xs tracking-[0.2em] uppercase text-muted-foreground block mb-2">Message</label>
          <textarea name="message" rows="5" class="w-full bg-transparent border-b border-border py-3 text-foreground focus:border-accent outline-none transition-colors resize-none"><?= e($form['message']) ?></textarea>
          <?php if (isset($errors['message'])): ?><p class="text-destructive text-xs mt-1"><?= e($errors['message']) ?></p><?php endif; ?>
        </div>

        <button type="submit" class="flex items-center gap-2 px-8 py-3 bg-primary text-primary-foreground text-sm tracking-[0.15em] uppercase hover:bg-accent hover:text-accent-foreground transition-all duration-300">Send Message</button>
      </form>

      <div class="space-y-10">
        <div>
          <h3 class="font-display text-xl tracking-wider mb-3"><?= e(contactValue($contactCfg, 'visit_title')) ?></h3>
          <p class="text-sm leading-relaxed text-muted-foreground"><?= nl2br(e(contactValue($contactCfg, 'visit_address', 'No data available for this section.'))) ?></p>
        </div>
        <div>
          <h3 class="font-display text-xl tracking-wider mb-3"><?= e(contactValue($contactCfg, 'email_title')) ?></h3>
          <?php $contactEmail = trim((string)($contactCfg['email'] ?? '')); ?>
          <?php if ($contactEmail !== ''): ?>
            <a href="mailto:<?= e($contactEmail) ?>" class="text-sm text-muted-foreground hover:text-accent transition-colors"><?= e($contactEmail) ?></a>
          <?php else: ?>
            <p class="text-sm text-muted-foreground">No data available.</p>
          <?php endif; ?>
        </div>
        <div>
          <h3 class="font-display text-xl tracking-wider mb-3"><?= e(contactValue($contactCfg, 'follow_title')) ?></h3>
          <?php $instagramUrl = trim((string)($contactCfg['instagram_url'] ?? '')); ?>
          <?php $twitterUrl = trim((string)($contactCfg['twitter_url'] ?? '')); ?>
          <div class="flex gap-4">
            <?php if ($instagramUrl !== ''): ?>
              <a href="<?= e($instagramUrl) ?>" target="_blank" rel="noopener noreferrer" class="text-muted-foreground hover:text-accent transition-colors">Instagram</a>
            <?php else: ?>
              <span class="text-muted-foreground">Instagram: No data available</span>
            <?php endif; ?>
            <?php if ($twitterUrl !== ''): ?>
              <a href="<?= e($twitterUrl) ?>" target="_blank" rel="noopener noreferrer" class="text-muted-foreground hover:text-accent transition-colors">Twitter</a>
            <?php else: ?>
              <span class="text-muted-foreground">Twitter: No data available</span>
            <?php endif; ?>
          </div>
        </div>
        <div class="mt-8">
          <?php $mapEmbed = trim((string)($contactCfg['map_embed'] ?? '')); ?>
          <?php if ($mapEmbed !== ''): ?>
            <iframe
              src="<?= e($mapEmbed) ?>"
              width="100%"
              height="250"
              loading="lazy"
              referrerpolicy="no-referrer-when-downgrade"
              title="Store location"
              class="contact-map-embed grayscale opacity-80"
            ></iframe>
          <?php else: ?>
            <p class="text-sm text-muted-foreground">No data available. Map is not configured.</p>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </section>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
