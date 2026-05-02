<?php
/**
 * Migration Runner
 * Run from terminal: php database/migrate.php
 */

define('ROOT', dirname(__DIR__));
require_once ROOT . '/app/core/Env.php';
Env::load(ROOT . '/.env');
require_once ROOT . '/config/database.php';

$dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';charset=' . DB_CHAR;

try {
    // Connect without DB first to create it if needed
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    $pdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "`
                CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `" . DB_NAME . "`");

    // Migrations tracking table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `migrations` (
        `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `filename`   VARCHAR(255) NOT NULL UNIQUE,
        `run_at`     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB");

    // Get already-run migrations
    $ran = $pdo->query("SELECT filename FROM migrations")->fetchAll(PDO::FETCH_COLUMN);

    // Get all migration files in order
    $files = glob(__DIR__ . '/migrations/*.sql');
    sort($files);

    $count = 0;
    foreach ($files as $file) {
        $filename = basename($file);

        if (in_array($filename, $ran)) {
            echo "  [skip]  {$filename}\n";
            continue;
        }

        $sql = file_get_contents($file);

        // Run each statement separately
        foreach (array_filter(array_map('trim', explode(';', $sql))) as $statement) {
            if ($statement) $pdo->exec($statement);
        }

        $pdo->prepare("INSERT INTO migrations (filename) VALUES (?)")->execute([$filename]);
        echo "  [done]  {$filename}\n";
        $count++;
    }

    echo "\n  Migration complete. {$count} file(s) run.\n";

} catch (PDOException $e) {
    echo "\n  [ERROR] " . $e->getMessage() . "\n";
    exit(1);
}
