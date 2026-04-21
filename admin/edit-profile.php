<?php
declare(strict_types=1);

require __DIR__ . '/../includes/bootstrap.php';
require __DIR__ . '/../includes/admin_auth.php';
require __DIR__ . '/../includes/admin_layout.php';
requireAdminLogin();

$currentEmail = adminEmail();
$error = '';
$success = '';

/**
 * Safely update .env file with new values
 */
function updateEnvFile(string $key, string $value): bool
{
    $envPath = __DIR__ . '/../.env';
    if (!is_file($envPath) || !is_readable($envPath)) {
        return false;
    }

    $lines = file($envPath, FILE_IGNORE_NEW_LINES) ?: [];
    $keyFound = false;

    foreach ($lines as &$line) {
        if (strpos(trim($line), $key . '=') === 0) {
            $line = $key . '=' . $value;
            $keyFound = true;
            break;
        }
    }

    if (!$keyFound) {
        $lines[] = $key . '=' . $value;
    }

    return (bool)file_put_contents($envPath, implode(PHP_EOL, $lines) . PHP_EOL, LOCK_EX);
}

/**
 * Update environment variable in memory
 */
function updateEnvMemory(string $key, string $value): void
{
    putenv($key . '=' . $value);
    $_ENV[$key] = $value;
    $_SERVER[$key] = $value;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = trim((string)($_POST['action'] ?? ''));
    $currentPassword = (string)($_POST['current_password'] ?? '');

    // Verify current password
    $validPass = verifyAdminCurrentPassword($currentPassword);
    if (!$validPass) {
        $error = 'Current password is incorrect.';
    } elseif ($action === 'update_password') {
        $newPassword = (string)($_POST['new_password'] ?? '');
        $confirmPassword = (string)($_POST['confirm_password'] ?? '');

        if (empty($newPassword)) {
            $error = 'New password is required.';
        } elseif (strlen($newPassword) < 8) {
            $error = 'Password must be at least 8 characters long.';
        } elseif ($newPassword !== $confirmPassword) {
            $error = 'Passwords do not match.';
        } else {
            if (updateAdminPassword($newPassword)) {
                // Keep .env in sync as legacy fallback.
                updateEnvFile('ADMIN_PASSWORD', $newPassword);
                updateEnvMemory('ADMIN_PASSWORD', $newPassword);
                $success = 'Password updated successfully.';
            } else {
                $error = 'Failed to update password in database.';
            }
        }
    } elseif ($action === 'update_email') {
        $newEmail = trim((string)($_POST['admin_email'] ?? ''));

        if (empty($newEmail)) {
            $error = 'Email is required.';
        } elseif (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
            $error = 'Please enter a valid email address.';
        } else {
            if (updateAdminEmail($newEmail)) {
                // Keep .env in sync as legacy fallback.
                updateEnvFile('ADMIN_EMAIL', $newEmail);
                updateEnvMemory('ADMIN_EMAIL', $newEmail);
                $success = 'Email updated successfully.';
                $currentEmail = $newEmail;
            } else {
                $error = 'Failed to update email in database.';
            }
        }
    }
}

$adminEmail = adminEmail();

adminLayoutStart('Aeterna | Edit Admin Profile', 'edit_profile');
?>
<div class="admin-section">
  <p class="admin-kicker">Account Settings</p>
  <h1 class="admin-page-title">Edit Profile</h1>
  <p class="admin-lead">Update your admin password and email address.</p>

  <?php if ($error !== ''): ?>
    <div class="mb-6 bg-card border border-destructive px-4 py-3 text-sm text-destructive"><?= e($error) ?></div>
  <?php endif; ?>

  <?php if ($success !== ''): ?>
    <div class="mb-6 bg-card border border-accent px-4 py-3 text-sm text-accent"><?= e($success) ?></div>
  <?php endif; ?>

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Change Password -->
    <div class="admin-panel">
      <h2 class="admin-panel-title">Change Password</h2>
      <form method="post" action="/admin/edit-profile.php" class="space-y-4">
        <input type="hidden" name="action" value="update_password" />

        <div>
          <label class="text-xs tracking-[0.2em] uppercase text-muted-foreground block mb-2">Current Password</label>
          <input type="password" name="current_password" required class="admin-input" placeholder="Enter current password" />
        </div>

        <div>
          <label class="text-xs tracking-[0.2em] uppercase text-muted-foreground block mb-2">New Password</label>
          <input type="password" name="new_password" required class="admin-input" placeholder="Enter new password (min. 8 characters)" />
          <p class="text-xs text-muted-foreground mt-1">Minimum 8 characters required</p>
        </div>

        <div>
          <label class="text-xs tracking-[0.2em] uppercase text-muted-foreground block mb-2">Confirm Password</label>
          <input type="password" name="confirm_password" required class="admin-input" placeholder="Confirm new password" />
        </div>

        <button type="submit" class="admin-btn-primary w-full py-2">Update Password</button>
      </form>
    </div>

    <!-- Update Email -->
    <div class="admin-panel">
      <h2 class="admin-panel-title">Update Email</h2>
      <form method="post" action="/admin/edit-profile.php" class="space-y-4">
        <input type="hidden" name="action" value="update_email" />

        <div>
          <label class="text-xs tracking-[0.2em] uppercase text-muted-foreground block mb-2">Current Password</label>
          <input type="password" name="current_password" required class="admin-input" placeholder="Enter current password" />
        </div>

        <div>
          <label class="text-xs tracking-[0.2em] uppercase text-muted-foreground block mb-2">Email Address</label>
          <input type="email" name="admin_email" required class="admin-input" placeholder="Enter email address" value="<?= e($adminEmail) ?>" />
        </div>

        <button type="submit" class="admin-btn-primary w-full py-2">Update Email</button>
      </form>
    </div>
  </div>

  <!-- Account Information -->
  <div class="admin-panel mt-8">
    <h2 class="admin-panel-title">Account Information</h2>
    <div class="space-y-3">
      <div>
        <p class="text-xs tracking-[0.2em] uppercase text-muted-foreground mb-1">Email Address</p>
        <p class="text-foreground"><?= e($currentEmail) ?></p>
      </div>
    </div>
  </div>
</div>
<?php adminLayoutEnd(); ?>
