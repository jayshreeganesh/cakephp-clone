<?php
/**
 * CakePHP Clone Core Engine
 * Ultra-low Inode CakePHP Architecture
 */

namespace CakeCore;

use PDO;
use Exception;

if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    session_start();
}

class Configure {
    private static array $data = [];
    public static function write($key, $val) { self::$data[$key] = $val; }
    public static function read($key, $default = null) { return self::$data[$key] ?? $default; }
}

class Request {
    public function getData(?string $key = null, $default = null) {
        if ($key === null) return $_POST;
        return isset($_POST[$key]) ? (is_string($_POST[$key]) ? trim($_POST[$key]) : $_POST[$key]) : $default;
    }
    public function getQuery(?string $key = null, $default = null) {
        if ($key === null) return $_GET;
        return isset($_GET[$key]) ? (is_string($_GET[$key]) ? trim($_GET[$key]) : $_GET[$key]) : $default;
    }
    public function is(string|array $type): bool {
        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        if (is_array($type)) {
            return in_array($method, array_map('strtoupper', $type));
        }
        return $method === strtoupper($type);
    }
}

class Flash {
    public function success(string $message) { $_SESSION['__cake_flash']['success'][] = $message; }
    public function error(string $message) { $_SESSION['__cake_flash']['error'][] = $message; }
    public function render(): string {
        $flashes = $_SESSION['__cake_flash'] ?? [];
        unset($_SESSION['__cake_flash']);
        $html = '';
        if (!empty($flashes['success'])) {
            foreach ($flashes['success'] as $msg) {
                $html .= '<div class="mb-6 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 p-4 rounded shadow-sm flex items-center justify-between"><div class="flex items-center space-x-2"><i class="fa-solid fa-circle-check text-emerald-500 text-lg"></i><span>' . htmlspecialchars($msg) . '</span></div></div>';
            }
        }
        if (!empty($flashes['error'])) {
            foreach ($flashes['error'] as $msg) {
                $html .= '<div class="mb-6 bg-rose-50 border-l-4 border-rose-500 text-rose-800 p-4 rounded shadow-sm flex items-center justify-between"><div class="flex items-center space-x-2"><i class="fa-solid fa-circle-exclamation text-rose-500 text-lg"></i><span>' . htmlspecialchars($msg) . '</span></div></div>';
            }
        }
        return $html;
    }
}

class Entity {
    protected array $properties = [];
    public function __construct(array $props = []) { $this->properties = $props; }
    public function get(string $key) { return $this->properties[$key] ?? null; }
    public function set(string|array $key, $value = null) {
        if (is_array($key)) {
            $this->properties = array_merge($this->properties, $key);
        } else {
            $this->properties[$key] = $value;
        }
    }
    public function toArray(): array { return $this->properties; }
    public function __get($key) { return $this->properties[$key] ?? null; }
    public function __set($key, $value) { $this->properties[$key] = $value; }
    public function __isset($key) { return isset($this->properties[$key]); }
}

class Table {
    protected string $table;
    protected string $entityClass = Entity::class;
    protected static ?PDO $pdo = null;

    public function __construct() {
        if (self::$pdo === null) {
            $config = Configure::read('Datasources.default');
            $driver = $config['driver'] ?? 'sqlite';
            if ($driver === 'sqlite') {
                $dbPath = $config['sqlite_path'] ?? (ROOT . '/config/database.sqlite');
                if (!is_dir(dirname($dbPath))) mkdir(dirname($dbPath), 0777, true);
                self::$pdo = new PDO('sqlite:' . $dbPath);
            } else {
                $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset=utf8mb4";
                self::$pdo = new PDO($dsn, $config['username'], $config['password']);
            }
            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->autoMigrate();
        }
    }

    protected function autoMigrate() {
        self::$pdo->exec("CREATE TABLE IF NOT EXISTS products (
            id INTEGER PRIMARY KEY " . (self::$pdo->getAttribute(PDO::ATTR_DRIVER_NAME) === 'sqlite' ? 'AUTOINCREMENT' : 'AUTO_INCREMENT') . ",
            name VARCHAR(255) NOT NULL,
            sku VARCHAR(100) NOT NULL,
            price DECIMAL(10,2) NOT NULL,
            stock INT NOT NULL DEFAULT 0,
            description TEXT,
            created DATETIME DEFAULT CURRENT_TIMESTAMP,
            modified DATETIME DEFAULT CURRENT_TIMESTAMP
        )");
    }

    public function find(string $type = 'all', array $options = []): array {
        $order = $options['order'] ?? 'id DESC';
        $stmt = self::$pdo->query("SELECT * FROM {$this->table} ORDER BY {$order}");
        $rows = $stmt->fetchAll();
        $class = $this->entityClass;
        return array_map(fn($row) => new $class($row), $rows);
    }

    public function get(int $id): ?Entity {
        $stmt = self::$pdo->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if (!$row) return null;
        $class = $this->entityClass;
        return new $class($row);
    }

    public function newEmptyEntity(): Entity {
        $class = $this->entityClass;
        return new $class([]);
    }

    public function patchEntity(Entity $entity, array $data): Entity {
        $entity->set($data);
        return $entity;
    }

    public function save(Entity $entity): bool {
        $props = $entity->toArray();
        unset($props['created'], $props['modified']);
        if (!empty($props['id'])) {
            $id = $props['id'];
            unset($props['id']);
            $set = implode(', ', array_map(fn($k) => "{$k} = ?", array_keys($props)));
            $stmt = self::$pdo->prepare("UPDATE {$this->table} SET {$set} WHERE id = ?");
            $params = array_merge(array_values($props), [$id]);
            return $stmt->execute($params);
        } else {
            unset($props['id']);
            $cols = implode(', ', array_keys($props));
            $placeholders = implode(', ', array_fill(0, count($props), '?'));
            $stmt = self::$pdo->prepare("INSERT INTO {$this->table} ({$cols}) VALUES ({$placeholders})");
            $res = $stmt->execute(array_values($props));
            $entity->id = (int)self::$pdo->lastInsertId();
            return $res;
        }
    }

    public function delete(Entity $entity): bool {
        if (empty($entity->id)) return false;
        $stmt = self::$pdo->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $stmt->execute([$entity->id]);
    }
}

class TableRegistry {
    private static array $instances = [];
    public static function get(string $alias): Table {
        if (!isset(self::$instances[$alias])) {
            $className = "\\App\\Model\\Table\\{$alias}Table";
            if (class_exists($className)) {
                self::$instances[$alias] = new $className();
            } else {
                $table = new Table();
                self::$instances[$alias] = $table;
            }
        }
        return self::$instances[$alias];
    }
}

class Router {
    private static array $routes = [];
    public static function connect(string $path, array $defaults) {
        self::$routes[$path] = $defaults;
    }
    public static function url($target = []): string {
        $scriptDir = dirname($_SERVER['SCRIPT_NAME'] ?? '');
        $base = ($scriptDir === '/' || $scriptDir === '\\') ? '' : rtrim($scriptDir, '/');
        // If webroot is present in base path, normalize
        if (str_ends_with($base, '/webroot')) {
            // normal
        }
        if (is_string($target)) {
            return $base . '/' . ltrim($target, '/');
        }
        $ctrl = strtolower($target['controller'] ?? 'products');
        $action = $target['action'] ?? 'index';
        $params = $target[0] ?? ($target['id'] ?? null);
        $url = $base . '/' . $ctrl . ($action !== 'index' ? '/' . $action : '');
        if ($params !== null) $url .= '/' . $params;
        return $url ?: '/';
    }

    public static function dispatch() {
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        $scriptDir = dirname($_SERVER['SCRIPT_NAME'] ?? '');
        if ($scriptDir !== '/' && $scriptDir !== '\\' && str_starts_with($uri, $scriptDir)) {
            $uri = substr($uri, strlen($scriptDir));
        }
        $path = '/' . trim($uri, '/');
        if ($path === '/' || $path === '/index.php') {
            $controller = 'Products';
            $action = 'index';
            $args = [];
        } else {
            // Check connected routes
            if (isset(self::$routes[$path])) {
                $controller = self::$routes[$path]['controller'] ?? 'Products';
                $action = self::$routes[$path]['action'] ?? 'index';
                $args = [];
            } else {
                $parts = explode('/', trim($path, '/'));
                $controller = ucfirst($parts[0] ?? 'Products');
                $action = $parts[1] ?? 'index';
                $args = array_slice($parts, 2);
            }
        }

        $controllerClass = "\\App\\Controller\\{$controller}Controller";
        if (!class_exists($controllerClass)) {
            http_response_code(404);
            echo "<h1>404 Not Found</h1><p>Missing Controller: {$controller}Controller</p>";
            return;
        }

        $instance = new $controllerClass();
        if (!method_exists($instance, $action)) {
            http_response_code(404);
            echo "<h1>404 Not Found</h1><p>Missing Action: {$action} in {$controller}Controller</p>";
            return;
        }

        $instance->setActionName($action);
        $instance->setControllerName($controller);
        call_user_func_array([$instance, $action], $args);
        $instance->render();
    }
}
