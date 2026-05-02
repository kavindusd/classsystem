<?php

class Request {

    public static function method(): string {
        return strtoupper($_SERVER['REQUEST_METHOD']);
    }

    public static function isPost(): bool {
        return self::method() === 'POST';
    }

    public static function isGet(): bool {
        return self::method() === 'GET';
    }

    public static function get(string $key, mixed $default = null): mixed {
        return $_GET[$key] ?? $default;
    }

    public static function post(string $key, mixed $default = null): mixed {
        $val = $_POST[$key] ?? $default;
        return is_string($val) ? trim($val) : $val;
    }

    public static function all(): array {
        return array_merge($_GET, $_POST);
    }

    public static function file(string $key): array|null {
        return $_FILES[$key] ?? null;
    }

    public static function url(): string {
        $url = $_GET['url'] ?? '';
        
        // If empty (e.g. PHP built-in server without rewrite) or contains base path
        if (empty($url) || str_starts_with($url, 'classystem/')) {
            $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            
            // Remove /classystem base path if present
            if (str_starts_with($path, '/classystem/')) {
                $path = substr($path, strlen('/classystem/'));
            }
            
            $url = trim($path, '/');
        }
        
        return trim($url, '/');
    }

    // Sanitize a string input
    public static function sanitize(string|null $value): string {
        if ($value === null) return '';
        return htmlspecialchars(strip_tags(trim($value)), ENT_QUOTES, 'UTF-8');
    }
}
