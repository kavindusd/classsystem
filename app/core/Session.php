<?php

class Session {

    public static function start(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_name('CLASSSYS_SESS');
            session_set_cookie_params([
                'lifetime' => 0,
                'path'     => '/',
                'secure'   => false, // set true in production with HTTPS
                'httponly' => true,
                'samesite' => 'Strict',
            ]);
            session_start();
        }
    }

    public static function set(string $key, mixed $value): void {
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, mixed $default = null): mixed {
        return $_SESSION[$key] ?? $default;
    }

    public static function has(string $key): bool {
        return isset($_SESSION[$key]);
    }

    public static function delete(string $key): void {
        unset($_SESSION[$key]);
    }

    public static function destroy(): void {
        session_unset();
        session_destroy();
    }

    // Flash messages (shown once then cleared)
    public static function flash(string $key, mixed $value = null): mixed {
        if ($value !== null) {
            $_SESSION['_flash'][$key] = $value;
            return null;
        }
        $val = $_SESSION['_flash'][$key] ?? null;
        unset($_SESSION['_flash'][$key]);
        return $val;
    }
}
