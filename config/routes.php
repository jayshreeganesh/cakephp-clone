<?php
/**
 * CakePHP Application Routes
 */

use CakeCore\Router;

Router::connect('/', ['controller' => 'Products', 'action' => 'index']);
Router::connect('/products', ['controller' => 'Products', 'action' => 'index']);
Router::connect('/products/add', ['controller' => 'Products', 'action' => 'add']);
