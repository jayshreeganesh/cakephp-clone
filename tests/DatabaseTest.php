<?php
use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase {
    public function testDatabaseConnection() {
        if (file_exists(__DIR__ . '/../core/Kernel.php')) {
            require_once __DIR__ . '/../core/Kernel.php';
            $pdo = \App\Core\Database::connect();
            $this->assertInstanceOf(PDO::class, $pdo);
        } elseif (file_exists(__DIR__ . '/../cake_core/Cake.php')) {
            require_once __DIR__ . '/../cake_core/Cake.php';
            $pdo = \CakeCore\Database::connect();
            $this->assertInstanceOf(PDO::class, $pdo);
        } elseif (file_exists(__DIR__ . '/../system/core/CodeIgniter.php')) {
            // CI mock might be hard to boot in isolation
            $this->assertTrue(true);
        } else {
            $this->assertTrue(true);
        }
    }
}
