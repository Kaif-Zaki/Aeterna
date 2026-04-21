<?php
declare(strict_types=1);

require __DIR__ . '/../includes/admin_auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    adminLogout();
}

header('Location: /admin/login.php');
exit;
