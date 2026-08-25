<?php
/**
 * CakePHP Application Routes
 */

use CakeCore\Router;

Router::connect('/', ['controller' => 'Products', 'action' => 'index']);
Router::connect('/products', ['controller' => 'Products', 'action' => 'index']);
Router::connect('/products/add', ['controller' => 'Products', 'action' => 'add']);

use App\Controller\AuthController;
$router->addRoute('/login', [AuthController::class, 'login']);
$router->addRoute('/register', [AuthController::class, 'register']);
$router->addRoute('/logout', [AuthController::class, 'logout']);
$router->addRoute('/products/export', [App\Controller\ProductsController::class, 'export']);
