<?php
declare(strict_types=1);

require_once __DIR__ . '/cms_content.php';

function configuredAdminEmail(): ?string
{
    // Priority 1: admin_users table (latest admin profile value)
    try {
        require __DIR__ . '/../config.php';
        if (isset($pdo) && $pdo instanceof PDO) {
            $row = $pdo->query('SELECT email FROM admin_users ORDER BY id ASC LIMIT 1')->fetch(PDO::FETCH_ASSOC);
            if (is_array($row)) {
                $dbEmail = trim((string)($row['email'] ?? ''));
                if ($dbEmail !== '' && filter_var($dbEmail, FILTER_VALIDATE_EMAIL)) {
                    return $dbEmail;
                }
            }
        }
    } catch (Throwable $e) {
        error_log('Admin email DB lookup failed: ' . $e->getMessage());
    }

    // Priority 2: .env admin email
    $adminEmail = trim((string)(getenv('ADMIN_EMAIL') ?: ''));
    if ($adminEmail !== '' && filter_var($adminEmail, FILTER_VALIDATE_EMAIL)) {
        return $adminEmail;
    }

    return null;
}

function contactRecipientEmail(): ?string
{
    // Always use admin-configured email only.
    return configuredAdminEmail();
}

function contactSenderEmail(): ?string
{
    // Sender must always be admin-configured email only.
    return configuredAdminEmail();
}

function sanitizeHeaderValue(string $value): string
{
    return str_replace(["\r", "\n"], '', trim($value));
}

function emailJsConfigValue(string $key): string
{
    return trim((string)(getenv($key) ?: ''));
}

function postJson(string $url, array $payload, ?int &$statusCode = null): string
{
    $json = json_encode($payload, JSON_UNESCAPED_UNICODE);
    if ($json === false) {
        throw new RuntimeException('Failed to encode EmailJS payload.');
    }

    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        if ($ch === false) {
            throw new RuntimeException('Failed to initialize cURL.');
        }

        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_POSTFIELDS => $json,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_HEADER => true,
        ]);

        $response = curl_exec($ch);
        if ($response === false) {
            $err = curl_error($ch);
            throw new RuntimeException('EmailJS request failed: ' . $err);
        }

        $headerSize = (int)curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $statusCode = (int)curl_getinfo($ch, CURLINFO_RESPONSE_CODE);

        return (string)substr((string)$response, $headerSize);
    }

    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => "Content-Type: application/json\r\n",
            'content' => $json,
            'timeout' => 15,
            'ignore_errors' => true,
        ],
    ]);

    $response = file_get_contents($url, false, $context);
    $headers = $http_response_header ?? [];
    foreach ($headers as $headerLine) {
        if (preg_match('/^HTTP\/\S+\s+(\d{3})/', $headerLine, $matches) === 1) {
            $statusCode = (int)$matches[1];
            break;
        }
    }

    if ($response === false) {
        throw new RuntimeException('EmailJS request failed.');
    }

    return $response;
}

function sendContactNotificationEmail(string $name, string $email, string $message, ?string &$error = null): bool
{
    // Resolve one strict admin email and use it for both sender + recipient.
    $adminEmail = configuredAdminEmail();
    if ($adminEmail === null) {
        $error = 'Admin email is not configured.';
        return false;
    }
    $to = $adminEmail;
    $sender = $adminEmail;

    $safeName = sanitizeHeaderValue($name);
    $safeEmail = sanitizeHeaderValue($email);
    $serviceId = emailJsConfigValue('EMAILJS_SERVICE_ID');
    $templateId = emailJsConfigValue('EMAILJS_TEMPLATE_ID');
    $publicKey = emailJsConfigValue('EMAILJS_PUBLIC_KEY');
    $privateKey = emailJsConfigValue('EMAILJS_PRIVATE_KEY');
    $apiUrl = emailJsConfigValue('EMAILJS_API_URL');

    if ($apiUrl === '') {
        $apiUrl = 'https://api.emailjs.com/api/v1.0/email/send';
    }

    if ($serviceId === '' || $templateId === '' || $publicKey === '') {
        $error = 'EmailJS is not configured. Please set EMAILJS_SERVICE_ID, EMAILJS_TEMPLATE_ID, and EMAILJS_PUBLIC_KEY.';
        return false;
    }

    $payload = [
        'service_id' => $serviceId,
        'template_id' => $templateId,
        'user_id' => $publicKey,
        'template_params' => [
            'to_email' => $to,
            'name' => $safeName,
            'email' => $safeEmail,
            'title' => 'New Contact Message - Aeterna',
            'time' => date('Y-m-d H:i:s'),
            'message' => $message,

            // Backward-compatible aliases
            'admin_email' => $to,
            'from_name' => 'Aeterna Admin',
            'from_email' => $sender,
            'reply_to' => $safeEmail,
            'subject' => 'New Contact Message - Aeterna',
            'sender_email' => $sender,
            'sender_name' => 'Aeterna Admin',
            'customer_name' => $safeName,
            'customer_email' => $safeEmail,
            // Additional aliases for template compatibility.
            'from' => $sender,
            'fromAddress' => $sender,
            'replyTo' => $safeEmail,
            'to' => $to,
        ],
    ];

    if ($privateKey !== '') {
        $payload['accessToken'] = $privateKey;
    }

    try {
        error_log('Contact mail dispatch -> to: ' . $to . ', sender: ' . $sender . ', reply_to: ' . $safeEmail);
        $statusCode = 0;
        $response = postJson($apiUrl, $payload, $statusCode);
        if ($statusCode >= 200 && $statusCode < 300) {
            return true;
        }

        error_log('EmailJS send failed with status ' . $statusCode . ': ' . $response);
        $error = 'Message saved, but EmailJS delivery failed.';
        return false;
    } catch (Throwable $e) {
        error_log('EmailJS request error: ' . $e->getMessage());
        $error = 'Message saved, but EmailJS delivery failed.';
        return false;
    }
}
