<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/env.php';
loadEnvFile(__DIR__ . '/.env');

$host = getenv('DB_HOST') ?: 'localhost';
$port = getenv('DB_PORT') ?: '3306';
$dbName = getenv('DB_NAME') ?: (getenv('DB_DATABASE') ?: 'aeterna');
$user = getenv('DB_USER') ?: (getenv('DB_USERNAME') ?: 'root');
$password = getenv('DB_PASSWORD') ?: '';
$charset = getenv('DB_CHARSET') ?: 'utf8mb4';
$socket = getenv('DB_SOCKET') ?: (getenv('DB_UNIX_SOCKET') ?: '');

$dsn = $socket !== ''
    ? "mysql:unix_socket={$socket};dbname={$dbName};charset={$charset}"
    : "mysql:host={$host};port={$port};dbname={$dbName};charset={$charset}";

$pdo = new PDO($dsn, $user, $password, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
]);
