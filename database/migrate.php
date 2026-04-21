<?php
declare(strict_types=1);

/**
 * Simple migration runner for Aeterna.
 * Usage: php database/migrate.php
 */

require_once __DIR__ . '/../includes/env.php';
loadEnvFile(__DIR__ . '/../.env');

$host = getenv('DB_HOST') ?: 'localhost';
$port = getenv('DB_PORT') ?: '3306';
$dbName = getenv('DB_NAME') ?: (getenv('DB_DATABASE') ?: 'aeterna');
$user = getenv('DB_USER') ?: (getenv('DB_USERNAME') ?: 'root');
$password = getenv('DB_PASSWORD') ?: '';
$charset = getenv('DB_CHARSET') ?: 'utf8mb4';
$socket = getenv('DB_SOCKET') ?: (getenv('DB_UNIX_SOCKET') ?: '');

try {
    $rootDsn = $socket !== ''
        ? "mysql:unix_socket={$socket};charset={$charset}"
        : "mysql:host={$host};port={$port};charset={$charset}";
    $rootPdo = new PDO($rootDsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    $rootPdo->exec(sprintf('CREATE DATABASE IF NOT EXISTS `%s` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci', str_replace('`', '``', $dbName)));

    $dbDsn = $socket !== ''
        ? "mysql:unix_socket={$socket};dbname={$dbName};charset={$charset}"
        : "mysql:host={$host};port={$port};dbname={$dbName};charset={$charset}";
    $dbPdo = new PDO($dbDsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    $dbPdo->exec(
        'CREATE TABLE IF NOT EXISTS admin_users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) NOT NULL UNIQUE,
            password_hash VARCHAR(255) NOT NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
    );

    $dbPdo->exec(
        'CREATE TABLE IF NOT EXISTS cms_content (
            id INT AUTO_INCREMENT PRIMARY KEY,
            content_key VARCHAR(100) NOT NULL UNIQUE,
            content_json LONGTEXT NOT NULL,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
    );

    $dbPdo->exec(
        'CREATE TABLE IF NOT EXISTS contacts (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(255) NOT NULL,
            message TEXT NOT NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_contacts_created_at (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
    );

    $dbPdo->exec(
        'CREATE TABLE IF NOT EXISTS categories (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(120) NOT NULL UNIQUE,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
    );

    $dbPdo->exec(
        'CREATE TABLE IF NOT EXISTS clothes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(160) NOT NULL,
            category_id INT NOT NULL,
            price DECIMAL(10,2) NULL,
            image_path VARCHAR(255) NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_clothes_category_id (category_id),
            CONSTRAINT fk_clothes_category
                FOREIGN KEY (category_id)
                REFERENCES categories(id)
                ON UPDATE CASCADE
                ON DELETE RESTRICT
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
    );

    $imagePathCheckStmt = $dbPdo->prepare(
        'SELECT COUNT(*)
         FROM information_schema.columns
         WHERE table_schema = :schema
           AND table_name = :table
           AND column_name = :column'
    );
    $imagePathCheckStmt->execute([
        ':schema' => $dbName,
        ':table' => 'clothes',
        ':column' => 'image_path',
    ]);

    if ((int)$imagePathCheckStmt->fetchColumn() === 0) {
        $dbPdo->exec('ALTER TABLE clothes ADD COLUMN image_path VARCHAR(255) NULL AFTER price');
    }

    $dbPdo->exec(
        'CREATE TABLE IF NOT EXISTS gallery_items (
            id INT AUTO_INCREMENT PRIMARY KEY,
            image_path VARCHAR(255) NOT NULL,
            alt_text VARCHAR(255) NOT NULL,
            is_tall TINYINT(1) NOT NULL DEFAULT 0,
            sort_order INT NOT NULL DEFAULT 0,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_gallery_sort (sort_order, created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
    );

    $adminCountStmt = $dbPdo->query('SELECT COUNT(*) FROM admin_users');
    $adminCount = (int)$adminCountStmt->fetchColumn();
    if ($adminCount === 0) {
        $seedEmail = trim((string)(getenv('ADMIN_EMAIL') ?: 'admin@aeterna.com'));
        $seedPassword = (string)(getenv('ADMIN_PASSWORD') ?: 'admin123');
        $seedHash = password_hash($seedPassword, PASSWORD_DEFAULT);
        if ($seedHash === false) {
            throw new RuntimeException('Failed to hash default admin password.');
        }

        $insertAdmin = $dbPdo->prepare('INSERT INTO admin_users (email, password_hash) VALUES (:email, :password_hash)');
        $insertAdmin->execute([
            ':email' => $seedEmail,
            ':password_hash' => $seedHash,
        ]);
    }

    $cmsCheck = $dbPdo->prepare('SELECT COUNT(*) FROM cms_content WHERE content_key = :content_key');
    $cmsCheck->execute([':content_key' => 'site']);
    $cmsCount = (int)$cmsCheck->fetchColumn();
    if ($cmsCount === 0) {
        $seedJson = '{}';
        $contentPath = __DIR__ . '/../storage/content.json';
        if (is_file($contentPath) && is_readable($contentPath)) {
            $raw = file_get_contents($contentPath);
            if (is_string($raw) && $raw !== '') {
                $decoded = json_decode($raw, true);
                if (is_array($decoded)) {
                    $seedJson = json_encode($decoded, JSON_UNESCAPED_UNICODE) ?: '{}';
                }
            }
        }

        $insertCms = $dbPdo->prepare('INSERT INTO cms_content (content_key, content_json) VALUES (:content_key, :content_json)');
        $insertCms->execute([
            ':content_key' => 'site',
            ':content_json' => $seedJson,
        ]);
    }

    fwrite(STDOUT, "Migration completed successfully.\n");
    fwrite(STDOUT, "Database: {$dbName}\n");
    fwrite(STDOUT, "Tables: admin_users, cms_content, contacts, categories, clothes, gallery_items\n");
    exit(0);
} catch (Throwable $e) {
    fwrite(STDERR, "Migration failed: " . $e->getMessage() . "\n");
    exit(1);
}
