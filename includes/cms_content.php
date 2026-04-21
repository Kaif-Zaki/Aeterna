<?php
declare(strict_types=1);

function cmsDbContentKey(): string
{
    return 'site';
}

function cmsStoragePath(): string
{
    return __DIR__ . '/../storage/content.json';
}

function cmsDefaults(): array
{
    return [
        'about' => [
            'hero_title' => 'Our Story',
            'hero_text' => 'Aeterna was born from a belief that fashion should transcend seasons. We craft pieces that feel as relevant tomorrow as they do today — garments that age with grace.',
            'mission_title' => 'Mission & Vision',
            'mission_text_1' => 'Our mission is to create clothing that respects both the wearer and the world. We source the finest sustainable materials and collaborate with master artisans who share our dedication to craft.',
            'mission_text_2' => 'We envision a future where fashion is conscious, purposeful, and enduring — where every thread carries meaning.',
            'founder_role' => 'Founder & Creative Director',
            'founder_name' => 'Aria Laurent',
            'founder_bio' => 'With over a decade in luxury fashion, Aria founded Aeterna to bridge the gap between timeless craftsmanship and contemporary design. Her philosophy centers on intentionality — every seam, every silhouette is a deliberate choice.',
        ],
        'contact' => [
            'heading' => 'Contact',
            'subtitle' => "We'd love to hear from you",
            'visit_title' => 'Visit Us',
            'visit_address' => "42 Via della Moda\nMilan, Italy 20121",
            'email_title' => 'Email',
            'email' => 'hello@aeterna.com',
            'follow_title' => 'Follow',
            'instagram_url' => 'https://instagram.com',
            'twitter_url' => 'https://twitter.com',
            'map_embed' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d126743.63244604084!2d79.8211869!3d6.927079!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ae2596d1f8ebf75%3A0x4f13d0b6c6d25ea8!2sColombo!5e0!3m2!1sen!2slk!4v1713600000000',
        ],
        'home' => [
            'hero_title' => 'Aeterna',
            'hero_subtitle' => 'Timeless elegance for the modern soul',
            'featured_title' => 'Featured Collections',
            'featured_subtitle' => 'A curated selection from our latest seasonal offerings',
            'quote_text' => 'We believe clothing is not merely worn — it is inhabited. Each piece tells a story of craft, intention, and quiet confidence.',
            'quote_author' => 'The Aeterna Philosophy',
        ],
        'faq' => [
            'heading' => 'FAQ',
            'subtitle' => 'Common questions answered',
            'items' => [
                ['q' => 'Is Aeterna an e-commerce brand?', 'a' => 'No. Aeterna is a brand experience. We showcase our collections and story here. For purchasing inquiries, please contact us directly.'],
                ['q' => 'Where can I view Aeterna pieces in person?', 'a' => 'Visit our flagship showrooms in Milan and Paris, or contact us for a private viewing appointment.'],
                ['q' => 'Do you offer custom tailoring?', 'a' => 'Yes. Our bespoke service is available by appointment. Reach out through our contact page to discuss your vision.'],
                ['q' => 'What materials do you use?', 'a' => 'We source premium sustainable materials including Italian wool, Japanese denim, Mongolian cashmere, and peace silk.'],
                ['q' => 'How can I collaborate with Aeterna?', 'a' => 'We welcome collaborations with aligned brands, artists, and creatives. Send us a proposal through the contact page.'],
                ['q' => 'Do you ship internationally?', 'a' => 'For private orders, we offer worldwide delivery through our concierge service. Contact us for details.'],
            ],
        ],
    ];
}

function arrayMergeRecursiveDistinct(array $base, array $override): array
{
    $merged = $base;

    foreach ($override as $key => $value) {
        if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
            $merged[$key] = arrayMergeRecursiveDistinct($merged[$key], $value);
        } else {
            $merged[$key] = $value;
        }
    }

    return $merged;
}

function cmsLoad(): array
{
    $defaults = cmsDefaults();
    $dbContent = cmsLoadFromDatabase();
    if ($dbContent !== null) {
        return arrayMergeRecursiveDistinct($defaults, $dbContent);
    }

    $path = cmsStoragePath();

    if (!is_file($path) || !is_readable($path)) {
        return $defaults;
    }

    $raw = file_get_contents($path);
    if ($raw === false || $raw === '') {
        return $defaults;
    }

    $decoded = json_decode($raw, true);
    if (!is_array($decoded)) {
        return $defaults;
    }

    return arrayMergeRecursiveDistinct($defaults, $decoded);
}

function cmsSave(array $content): bool
{
    $dbSaved = cmsSaveToDatabase($content);
    if ($dbSaved) {
        // Keep JSON file as backup for portability.
        cmsSaveToFile($content);
        return true;
    }

    return cmsSaveToFile($content);
}

function cmsPdo(): ?PDO
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
            cmsEnsureTable($pdo);
            return $pdo;
        }
    } catch (Throwable $e) {
        error_log('CMS DB connection unavailable: ' . $e->getMessage());
    }

    return null;
}

function cmsEnsureTable(PDO $pdo): void
{
    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS cms_content (
            id INT AUTO_INCREMENT PRIMARY KEY,
            content_key VARCHAR(100) NOT NULL UNIQUE,
            content_json LONGTEXT NOT NULL,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
    );
}

function cmsLoadFromDatabase(): ?array
{
    $pdo = cmsPdo();
    if ($pdo === null) {
        return null;
    }

    $stmt = $pdo->prepare('SELECT content_json FROM cms_content WHERE content_key = :content_key LIMIT 1');
    $stmt->execute([':content_key' => cmsDbContentKey()]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!is_array($row)) {
        return null;
    }

    $decoded = json_decode((string)($row['content_json'] ?? ''), true);
    if (!is_array($decoded)) {
        return null;
    }

    return $decoded;
}

function cmsSaveToDatabase(array $content): bool
{
    $pdo = cmsPdo();
    if ($pdo === null) {
        return false;
    }

    $json = json_encode($content, JSON_UNESCAPED_UNICODE);
    if ($json === false) {
        return false;
    }

    $stmt = $pdo->prepare(
        'INSERT INTO cms_content (content_key, content_json)
         VALUES (:content_key, :content_json)
         ON DUPLICATE KEY UPDATE content_json = VALUES(content_json)'
    );

    return $stmt->execute([
        ':content_key' => cmsDbContentKey(),
        ':content_json' => $json,
    ]);
}

function cmsSaveToFile(array $content): bool
{
    $path = cmsStoragePath();
    $dir = dirname($path);
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }

    $json = json_encode($content, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    if ($json === false) {
        return false;
    }

    return file_put_contents($path, $json) !== false;
}
