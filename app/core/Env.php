<?php

class Env {
    private static array $variables = [];

    public static function load(string $path): void {
        if (!file_exists($path)) {
            return;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if (str_starts_with($line, '#')) {
                continue;
            }

            if (strpos($line, '=') !== false) {
                [$key, $value] = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);

                // Remove quotes if present
                if (preg_match('/^"(.*)"$/', $value, $matches) || preg_match("/^'(.*)'$/", $value, $matches)) {
                    $value = $matches[1];
                }

                self::$variables[$key] = $value;
                $_ENV[$key] = $value;
                putenv("$key=$value");
            }
        }
    }

    public static function get(string $key, mixed $default = null): mixed {
        if (array_key_exists($key, self::$variables)) {
            return self::$variables[$key];
        }
        $val = getenv($key);
        return $val !== false ? $val : $default;
    }
}
