<?php
declare(strict_types=1);

require __DIR__ . '/../includes/bootstrap.php';
require __DIR__ . '/../includes/admin_auth.php';

if (isAdminLoggedIn()) {
    header('Location: /admin/profile.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim((string)($_POST['email'] ?? ''));
    $password = (string)($_POST['password'] ?? '');

    if (adminLogin($email, $password)) {
        header('Location: /admin/profile.php');
        exit;
    }

    $error = 'Invalid email or password.';
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Aeterna | Admin Login</title>
  <link rel="stylesheet" href="/assets/style.css" />
  <link rel="stylesheet" href="/assets/site.css" />
</head>
<body class="admin-login-body">
  <section class="admin-login-card">
    <p class="admin-login-kicker">Authorized Access</p>
    <h1 class="admin-login-title">Admin Login</h1>

    <?php if ($error !== ''): ?>
      <div class="mb-6 bg-card border border-destructive px-4 py-3 text-sm text-destructive"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="post" action="/admin/login.php" class="space-y-6">
      <div>
        <label class="text-xs tracking-[0.2em] uppercase text-muted-foreground block mb-2">Email Address</label>
        <input type="email" name="email" required class="w-full bg-transparent border-b border-border py-3 text-foreground focus:border-accent outline-none transition-colors" />
      </div>

      <div>
        <label class="text-xs tracking-[0.2em] uppercase text-muted-foreground block mb-2">Password</label>
        <input type="password" name="password" required class="w-full bg-transparent border-b border-border py-3 text-foreground focus:border-accent outline-none transition-colors" />
      </div>

      <button type="submit" class="w-full px-8 py-3 bg-primary text-primary-foreground text-sm tracking-[0.15em] uppercase hover:bg-accent hover:text-accent-foreground transition-all duration-300">Sign In</button>
    </form>
  </section>
  <script src="/assets/admin.js"></script>
</body>
</html>
