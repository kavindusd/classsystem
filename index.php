<?php

define('ROOT', __DIR__);
define('APP',  ROOT . '/app');

// Autoloader
spl_autoload_register(function ($class) {
    $paths = [
        APP . '/core/',
        APP . '/controllers/',
        APP . '/controllers/admin/',
        APP . '/controllers/teacher/',
        APP . '/controllers/student/',
        APP . '/models/',
        APP . '/helpers/',
        APP . '/middleware/',
    ];
    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Load .env variables
Env::load(ROOT . '/.env');

// Load config
require_once ROOT . '/config/app.php';
require_once ROOT . '/config/database.php';
require_once ROOT . '/config/mail.php';

// Start session
Session::start();

// Boot router
$router = new Router();
require_once ROOT . '/routes/web.php';
$router->dispatch();
