<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/env.php';
loadEnvFile(__DIR__ . '/../.env');

function adminPdo(): ?PDO
{
    static $pdo = null;
    static $attempted = false;

    if ($attempted) {
        return $pdo;
    }

    $attempted = true;
    try {
        require __DIR__ . '/../config.php';
        if (isset($pdo) && $pdo instanceof PDO) {
            adminEnsureTable($pdo);
            adminEnsureSeed($pdo);
            return $pdo;
        }
    } catch (Throwable $e) {
        error_log('Admin DB connection unavailable: ' . $e->getMessage());
    }

    return null;
}

function adminEnsureTable(PDO $pdo): void
{
    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS admin_users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) NOT NULL UNIQUE,
            password_hash VARCHAR(255) NOT NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
    );
}

function adminEnsureSeed(PDO $pdo): void
{
    $count = (int)$pdo->query('SELECT COUNT(*) FROM admin_users')->fetchColumn();
    if ($count > 0) {
        return;
    }

    $seedEmail = getenv('ADMIN_EMAIL') ?: 'admin@aeterna.com';
    $seedPassword = getenv('ADMIN_PASSWORD') ?: 'admin123';
    $seedHash = password_hash($seedPassword, PASSWORD_DEFAULT);
    if ($seedHash === false) {
        throw new RuntimeException('Failed to hash bootstrap admin password.');
    }

    $stmt = $pdo->prepare('INSERT INTO admin_users (email, password_hash) VALUES (:email, :password_hash)');
    $stmt->execute([
        ':email' => $seedEmail,
        ':password_hash' => $seedHash,
    ]);
}

function findAdminUserByEmail(string $email): ?array
{
    $pdo = adminPdo();
    if ($pdo === null) {
        return null;
    }

    $stmt = $pdo->prepare('SELECT id, email, password_hash FROM admin_users WHERE email = :email LIMIT 1');
    $stmt->execute([':email' => $email]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    return is_array($row) ? $row : null;
}

function currentAdminUser(): ?array
{
    $sessionEmail = trim((string)($_SESSION['admin_email'] ?? ''));
    if ($sessionEmail === '') {
        return null;
    }

    return findAdminUserByEmail($sessionEmail);
}

function adminEmail(): string
{
    $sessionEmail = trim((string)($_SESSION['admin_email'] ?? ''));
    if ($sessionEmail !== '') {
        return $sessionEmail;
    }

    $pdo = adminPdo();
    if ($pdo !== null) {
        $row = $pdo->query('SELECT email FROM admin_users ORDER BY id ASC LIMIT 1')->fetch(PDO::FETCH_ASSOC);
        if (is_array($row) && !empty($row['email'])) {
            return (string)$row['email'];
        }
    }

    return getenv('ADMIN_EMAIL') ?: 'admin@aeterna.com';
}

function adminPassword(): string
{
    // Legacy fallback only.
    return getenv('ADMIN_PASSWORD') ?: 'admin123';
}

function isAdminLoggedIn(): bool
{
    return !empty($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function requireAdminLogin(): void
{
    if (!isAdminLoggedIn()) {
        header('Location: /admin/login.php');
        exit;
    }
}

function adminLogin(string $email, string $password): bool
{
    $email = trim($email);

    $user = findAdminUserByEmail($email);
    if (is_array($user)) {
        $hash = (string)($user['password_hash'] ?? '');
        if ($hash !== '' && password_verify($password, $hash)) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_email'] = $email;
            return true;
        }
        return false;
    }

    // Legacy fallback: allow .env login if DB is unavailable.
    $validEmail = hash_equals(getenv('ADMIN_EMAIL') ?: 'admin@aeterna.com', $email);
    $validPass = hash_equals(adminPassword(), $password);
    if ($validEmail && $validPass) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_email'] = $email;
        return true;
    }
    return false;
}

function verifyAdminCurrentPassword(string $password): bool
{
    $user = currentAdminUser();
    if (is_array($user)) {
        $hash = (string)($user['password_hash'] ?? '');
        return $hash !== '' && password_verify($password, $hash);
    }

    return hash_equals(adminPassword(), $password);
}

function updateAdminEmail(string $newEmail): bool
{
    $user = currentAdminUser();
    $pdo = adminPdo();
    if ($pdo !== null && is_array($user)) {
        $stmt = $pdo->prepare('UPDATE admin_users SET email = :email WHERE id = :id');
        $ok = $stmt->execute([
            ':email' => $newEmail,
            ':id' => (int)$user['id'],
        ]);
        if ($ok) {
            $_SESSION['admin_email'] = $newEmail;
            return true;
        }
    }

    return false;
}

function updateAdminPassword(string $newPassword): bool
{
    $user = currentAdminUser();
    $pdo = adminPdo();
    if ($pdo !== null && is_array($user)) {
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        if ($hash === false) {
            return false;
        }

        $stmt = $pdo->prepare('UPDATE admin_users SET password_hash = :password_hash WHERE id = :id');
        return $stmt->execute([
            ':password_hash' => $hash,
            ':id' => (int)$user['id'],
        ]);
    }

    return false;
}

function adminLogout(): void
{
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], (bool)$params['secure'], (bool)$params['httponly']);
    }

    session_destroy();
}
