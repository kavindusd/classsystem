<?php
define('ROOT', dirname(__DIR__));
require_once ROOT . '/app/core/Env.php';
Env::load(ROOT . '/.env');
require_once ROOT . '/config/database.php';

$dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';charset=' . DB_CHAR;

try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    $pdo->exec("USE `" . DB_NAME . "`");
    
    echo "Dropping all tables in " . DB_NAME . "...\n";
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    foreach ($tables as $table) {
        echo "Dropping table $table...\n";
        try {
            $pdo->exec("DROP TABLE IF EXISTS `$table` CASCADE");
        } catch (Exception $e) {
            echo "Failed to drop $table: " . $e->getMessage() . "\n";
            // Try to discard tablespace if it's an InnoDB issue
            $pdo->exec("DROP TABLE `$table` "); // Force try
        }
    }
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    
    // Fix for orphaned migrations table
    echo "Attempting to fix orphaned migrations tablespace...\n";
    try {
        $pdo->exec("CREATE TABLE `migrations` (id INT) ENGINE=InnoDB");
        $pdo->exec("DROP TABLE `migrations` ");
    } catch (Exception $e) {
        echo "Note: " . $e->getMessage() . "\n";
        // If it already exists or tablespace error, try to just drop it again
        try {
            $pdo->exec("DROP TABLE IF EXISTS `migrations` ");
        } catch (Exception $e2) {}
    }
    
    echo "All tables dropped (where possible).\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
