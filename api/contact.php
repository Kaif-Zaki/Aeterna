<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/env.php';
loadEnvFile(__DIR__ . '/../.env');

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: ' . (getenv('CORS_ORIGIN') ?: '*'));
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');

require_once __DIR__ . '/../includes/contact_mail.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

function respond(int $status, array $payload): void
{
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

function validateContact(array $body): array
{
    $errors = [];
    $name = trim((string)($body['name'] ?? ''));
    $email = trim((string)($body['email'] ?? ''));
    $message = trim((string)($body['message'] ?? ''));

    if ($name === '' || mb_strlen($name) > 100) {
        $errors[] = 'Name is required (max 100 chars).';
    }

    if ($email === '' || mb_strlen($email) > 255 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'A valid email is required (max 255 chars).';
    }

    if ($message === '' || mb_strlen($message) > 5000) {
        $errors[] = 'Message is required (max 5000 chars).';
    }

    return [
        'name' => $name,
        'email' => $email,
        'message' => $message,
        'errors' => $errors,
    ];
}

try {
    require __DIR__ . '/../config.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $rawInput = file_get_contents('php://input');
        $decoded = json_decode($rawInput ?: '', true);

        if (!is_array($decoded)) {
            respond(400, ['success' => false, 'errors' => ['Invalid JSON body.']]);
        }

        $validated = validateContact($decoded);

        if (!empty($validated['errors'])) {
            respond(400, ['success' => false, 'errors' => $validated['errors']]);
        }

        $stmt = $pdo->prepare('INSERT INTO contacts (name, email, message) VALUES (:name, :email, :message)');
        $stmt->execute([
            ':name' => $validated['name'],
            ':email' => $validated['email'],
            ':message' => $validated['message'],
        ]);

        $mailError = null;
        $mailSent = sendContactNotificationEmail($validated['name'], $validated['email'], $validated['message'], $mailError);

        respond(201, [
            'success' => true,
            'id' => (int)$pdo->lastInsertId(),
            'mail_sent' => $mailSent,
            'warning' => $mailSent ? null : ($mailError ?? 'Message saved, but email could not be delivered.'),
        ]);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $stmt = $pdo->query('SELECT id, name, email, message, created_at FROM contacts ORDER BY created_at DESC');
        respond(200, ['success' => true, 'data' => $stmt->fetchAll()]);
    }

    respond(405, ['success' => false, 'errors' => ['Method not allowed.']]);
} catch (Throwable $e) {
    error_log('PHP contact API error: ' . $e->getMessage());
    respond(500, ['success' => false, 'errors' => ['Server error. Please try again later.']]);
}
