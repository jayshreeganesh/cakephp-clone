<?php
/**
 * CakePHP Application Configuration
 */

use CakeCore\Configure;

Configure::write('App.title', 'CakePHP Clone Product Store');

Configure::write('Datasources.default', [
    'driver'       => 'sqlite', // 'sqlite' or 'mysql'
    
    // SQLite settings
    'sqlite_path'  => ROOT . '/config/database.sqlite',

    // MySQL settings (for InfinityFree / Aeon / cPanel)
    'host'         => '127.0.0.1',
    'port'         => 3306,
    'username'     => 'root',
    'password'     => '',
    'database'     => 'cake_crud',
]);
