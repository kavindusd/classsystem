<?php
define('ROOT', dirname(__DIR__));
require_once ROOT . '/app/core/Env.php';
Env::load(ROOT . '/.env');
require_once ROOT . '/config/database.php';

try {
    $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=' . DB_CHAR;
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    // Check which columns currently exist
    $cols = $pdo->query("SHOW COLUMNS FROM `courses`")->fetchAll(PDO::FETCH_COLUMN);

    // Remove old columns if they exist
    if (in_array('class_day', $cols)) {
        echo "Dropping old class_day column...\n";
        $pdo->exec("ALTER TABLE `courses` DROP COLUMN `class_day`");
    }
    if (in_array('class_time', $cols)) {
        echo "Dropping old class_time column...\n";
        $pdo->exec("ALTER TABLE `courses` DROP COLUMN `class_time`");
    }

    // Add new columns if not present
    $cols = $pdo->query("SHOW COLUMNS FROM `courses`")->fetchAll(PDO::FETCH_COLUMN);

    if (!in_array('class_days', $cols)) {
        echo "Adding class_days column...\n";
        $pdo->exec("ALTER TABLE `courses` ADD COLUMN `class_days` VARCHAR(100) DEFAULT NULL AFTER `description`");
    } else {
        echo "class_days already exists, skipping.\n";
    }
    if (!in_array('class_start_time', $cols)) {
        echo "Adding class_start_time column...\n";
        $pdo->exec("ALTER TABLE `courses` ADD COLUMN `class_start_time` TIME DEFAULT NULL AFTER `class_days`");
    } else {
        echo "class_start_time already exists, skipping.\n";
    }
    if (!in_array('class_end_time', $cols)) {
        echo "Adding class_end_time column...\n";
        $pdo->exec("ALTER TABLE `courses` ADD COLUMN `class_end_time` TIME DEFAULT NULL AFTER `class_start_time`");
    } else {
        echo "class_end_time already exists, skipping.\n";
    }

    echo "Migration complete.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
