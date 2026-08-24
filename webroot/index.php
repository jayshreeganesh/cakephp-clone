<?php
/**
 * CakePHP Clone - Front Controller
 */

define('ROOT', dirname(__DIR__));
define('APP_DIR', 'src');
define('APP', ROOT . '/' . APP_DIR . '/');
define('WEBROOT_DIR', 'webroot');
define('WWW_ROOT', __DIR__ . '/');

// Simple autoloader for App\ namespace
spl_autoload_register(function ($class) {
    if (str_starts_with($class, 'App\\')) {
        $file = ROOT . '/src/' . str_replace('\\', '/', substr($class, 4)) . '.php';
        if (file_exists($file)) {
            require_once $file;
        }
    }
});

// Load CakePHP Clone Core Engine
require_once ROOT . '/cake_core/Cake.php';

// Load Config & Routes
require_once ROOT . '/config/app.php';
require_once ROOT . '/config/routes.php';

// Dispatch Request
CakeCore\Router::dispatch();
