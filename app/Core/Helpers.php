<?php

/**
 * Global Helper Functions
 */

/**
 * Get configuration value
 */
function config(string $key, mixed $default = null): mixed
{
    $parts = explode('.', $key);
    $file = $parts[0];
    $configKey = $parts[1] ?? null;

    $configFile = CONFIG_PATH . "/{$file}.php";

    if (!file_exists($configFile)) {
        return $default;
    }

    $config = require $configFile;

    if ($configKey === null) {
        return $config;
    }

    // Support nested keys (e.g., app.database.host)
    $keys = array_slice($parts, 1);
    $value = $config;

    foreach ($keys as $k) {
        if (is_array($value) && isset($value[$k])) {
            $value = $value[$k];
        } else {
            return $default;
        }
    }

    return $value;
}

/**
 * Generate URL
 */
function url(string $path = ''): string
{
    $baseUrl = rtrim(config('app.url', ''), '/');
    return $baseUrl . '/' . ltrim($path, '/');
}

/**
 * Generate asset URL
 */
function asset(string $path): string
{
    $baseUrl = rtrim(config('app.url', ''), '/');
    return $baseUrl . '/public/assets/' . ltrim($path, '/');
}

/**
 * Escape HTML output
 */
function e(?string $string): string
{
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Format number as Rupiah
 */
function formatRupiah(float|int $amount): string
{
    $currency = config('app.currency', 'Rp');
    return $currency . ' ' . number_format($amount, 0, ',', '.');
}

/**
 * Format date
 */
function formatDate(?string $date, string $format = 'd/m/Y H:i'): string
{
    if (!$date) return '-';
    return date($format, strtotime($date));
}

/**
 * Generate CSRF token
 */
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Generate CSRF token field for forms
 */
function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}

/**
 * Verify CSRF token
 */
function csrf_verify(string $token): bool
{
    return hash_equals($_SESSION['csrf_token'] ?? '', $token);
}

/**
 * Get setting value
 */
function setting(string $key, mixed $default = null): mixed
{
    static $settings = null;

    if ($settings === null) {
        $db = new \App\Core\Database();
        $results = $db->fetchAll("SELECT key, value FROM settings");
        $settings = [];
        foreach ($results as $row) {
            $settings[$row['key']] = $row['value'];
        }
    }

    return $settings[$key] ?? $default;
}

/**
 * Check if user is logged in
 */
function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']);
}

/**
 * Get current user
 */
function currentUser(): ?array
{
    return $_SESSION['user'] ?? null;
}

/**
 * Check if user has permission
 */
function hasPermission(string $permission): bool
{
    if (!isLoggedIn()) {
        return false;
    }

    $user = currentUser();
    if (!$user || !isset($user['permissions'])) {
        return false;
    }

    return in_array($permission, $user['permissions']);
}

/**
 * Check if user has role
 */
function hasRole(string $role): bool
{
    if (!isLoggedIn()) {
        return false;
    }

    $user = currentUser();
    return ($user['role_name'] ?? '') === $role;
}

/**
 * Generate unique SKU
 */
function generateSKU(): string
{
    $db = new \App\Core\Database();
    $lastSKU = $db->fetchColumn("SELECT sku FROM products ORDER BY id DESC LIMIT 1");

    if ($lastSKU) {
        // Extract number from last SKU (e.g., PRD001 -> 1)
        $number = (int) filter_var($lastSKU, FILTER_SANITIZE_NUMBER_INT);
        $newNumber = $number + 1;
    } else {
        $newNumber = 1;
    }

    return 'PRD' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
}

/**
 * Generate invoice number
 */
function generateInvoiceNo(): string
{
    $db = new \App\Core\Database();
    $prefix = 'INV/' . date('Ymd') . '/';

    $lastInvoice = $db->fetchColumn(
        "SELECT invoice_no FROM transactions WHERE invoice_no LIKE :prefix ORDER BY id DESC LIMIT 1",
        ['prefix' => $prefix . '%']
    );

    if ($lastInvoice) {
        $number = (int) substr($lastInvoice, -4);
        $newNumber = $number + 1;
    } else {
        $newNumber = 1;
    }

    return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
}

/**
 * Redirect helper
 */
function redirect(string $url): void
{
    header("Location: " . url($url));
    exit;
}

/**
 * Flash message setter
 */
function flash(string $type, string $message): void
{
    $_SESSION['flash'][$type] = $message;
}

/**
 * Get and clear flash message
 */
function getFlash(string $type): ?string
{
    $message = $_SESSION['flash'][$type] ?? null;
    unset($_SESSION['flash'][$type]);
    return $message;
}

/**
 * Truncate string
 */
function truncate(string $string, int $length = 50, string $suffix = '...'): string
{
    if (strlen($string) <= $length) {
        return $string;
    }

    return substr($string, 0, $length) . $suffix;
}

/**
 * Get file extension
 */
function getFileExtension(string $filename): string
{
    return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
}

/**
 * Generate random string
 */
function randomString(int $length = 10): string
{
    return bin2hex(random_bytes((int)ceil($length / 2)));
}

/**
 * Alias for randomString (snake_case)
 */
function random_string(int $length = 10): string
{
    return randomString($length);
}

/**
 * Time ago format
 */
function timeAgo(string $datetime): string
{
    $time = time() - strtotime($datetime);

    if ($time < 60) return 'just now';
    if ($time < 3600) return floor($time / 60) . ' min ago';
    if ($time < 86400) return floor($time / 3600) . ' hour ago';
    if ($time < 2592000) return floor($time / 86400) . ' day ago';

    return formatDate($datetime, 'd/m/Y');
}
