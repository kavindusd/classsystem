<?php
define('ROOT', dirname(__DIR__));
require_once ROOT . '/app/core/Env.php';
Env::load(ROOT . '/.env');
require_once ROOT . '/config/database.php';

try {
    $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';charset=' . DB_CHAR;
    $pdo = new PDO($dsn, DB_USER, DB_PASS);
    echo "Connection to " . DB_HOST . " successful.\n";
} catch (Exception $e) {
    echo "Connection to " . DB_HOST . " failed: " . $e->getMessage() . "\n";
}
