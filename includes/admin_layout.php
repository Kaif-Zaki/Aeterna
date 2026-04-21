<?php
declare(strict_types=1);

/**
 * Admin shell layout without public navbar.
 */
function adminLayoutStart(string $pageTitle, string $active): void
{
    ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= e($pageTitle) ?></title>
  <link rel="stylesheet" href="/assets/style.css" />
  <link rel="stylesheet" href="/assets/site.css" />
</head>
<body class="admin-body" data-page="admin">
  <div class="admin-shell">
    <aside class="admin-sidebar">
      <div class="admin-brand-wrap">
        <a href="/admin/profile.php" class="admin-brand">Aeterna Admin</a>
        <p class="admin-subtitle">Management Console</p>
      </div>

      <nav class="admin-nav">
        <a href="/admin/profile.php" class="admin-link <?= $active === 'profile' ? 'is-active' : '' ?>">Profile</a>
        <a href="/admin/edit-profile.php" class="admin-link <?= $active === 'edit_profile' ? 'is-active' : '' ?>">⚙️ Edit Profile</a>
        <a href="/admin/clothes.php" class="admin-link <?= $active === 'clothes' ? 'is-active' : '' ?>">Manage Clothes</a>
        <a href="/admin/categories.php" class="admin-link <?= $active === 'categories' ? 'is-active' : '' ?>">Manage Collections</a>
        <a href="/admin/gallery.php" class="admin-link <?= $active === 'gallery' ? 'is-active' : '' ?>">Manage Gallery</a>
        <a href="/admin/home-page.php" class="admin-link <?= $active === 'home_page' ? 'is-active' : '' ?>">Manage Home Page</a>
        <a href="/admin/faq-page.php" class="admin-link <?= $active === 'faq_page' ? 'is-active' : '' ?>">Manage FAQ Page</a>
        <a href="/admin/about-page.php" class="admin-link <?= $active === 'about_page' ? 'is-active' : '' ?>">Manage About Page</a>
        <a href="/admin/contact-page.php" class="admin-link <?= $active === 'contact_page' ? 'is-active' : '' ?>">Manage Contact Page</a>
        <a href="/index.php" class="admin-link">View Live Site</a>
      </nav>

      <button type="button" id="admin-theme-toggle" class="admin-theme-btn">Dark Mode</button>

      <form method="post" action="/admin/logout.php" class="admin-logout-form">
        <button type="submit" class="admin-logout-btn">Logout</button>
      </form>
    </aside>

    <main class="admin-main">
    <?php
}

function adminLayoutEnd(): void
{
    ?>
    </main>
  </div>
  <script src="/assets/admin.js"></script>
  <script src="/assets/scroll-animations.js"></script>
</body>
</html>
    <?php
}
