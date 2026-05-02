<?php
define('ROOT', dirname(__DIR__));
require_once ROOT . '/app/core/Env.php';
Env::load(ROOT . '/.env');
require_once ROOT . '/config/database.php';

try {
    $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=' . DB_CHAR;
    $pdo = new PDO($dsn, DB_USER, DB_PASS);
    
    echo "Students:\n";
    $students = $pdo->query("SELECT s.student_id, u.email, u.role FROM students s JOIN users u ON u.id = s.user_id")->fetchAll(PDO::FETCH_ASSOC);
    print_r($students);
    
    echo "\nTeachers:\n";
    $teachers = $pdo->query("SELECT t.teacher_id, u.email, u.role FROM teachers t JOIN users u ON u.id = t.user_id")->fetchAll(PDO::FETCH_ASSOC);
    print_r($teachers);
    
    echo "\nAll Users:\n";
    $users = $pdo->query("SELECT id, email, role FROM users")->fetchAll(PDO::FETCH_ASSOC);
    print_r($users);

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
