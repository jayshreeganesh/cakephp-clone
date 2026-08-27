<?php
use CakeCore\Router;

Router::connect('/', ['controller' => 'Products', 'action' => 'index']);
Router::connect('/products', ['controller' => 'Products', 'action' => 'index']);
Router::connect('/products/create', ['controller' => 'Products', 'action' => 'create']);
Router::connect('/products/store', ['controller' => 'Products', 'action' => 'store']);

Router::connect('/login', ['controller' => 'Auth', 'action' => 'login']);
Router::connect('/register', ['controller' => 'Auth', 'action' => 'register']);
Router::connect('/logout', ['controller' => 'Auth', 'action' => 'logout']);

Router::connect('/products/export', ['controller' => 'Products', 'action' => 'export']);
