<?php
declare(strict_types=1);

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function isActivePage(string $page, string $current): bool
{
    return $page === $current;
}
