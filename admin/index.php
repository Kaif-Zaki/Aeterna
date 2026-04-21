<?php
declare(strict_types=1);

require __DIR__ . '/../includes/admin_auth.php';

if (isAdminLoggedIn()) {
    header('Location: /admin/profile.php');
    exit;
}

header('Location: /admin/login.php');
exit;
