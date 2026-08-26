<?php
session_start();
if (file_exists(__DIR__ . '/install.lock') && ($_GET['step'] ?? '') != '3') { die('<h2 style="text-align:center;margin-top:100px;">Already installed. Delete install.lock to re-run.</h2>'); }


$configFile = __DIR__ . '/config/database.php';

// Step Routing
$step = $_GET['step'] ?? 1;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $step == 2) {
    $driver = $_POST['driver'];
    $host = $_POST['host'] ?? '127.0.0.1';
    $port = $_POST['port'] ?? '';
    $dbname = $_POST['dbname'] ?? '';
    $user = $_POST['user'] ?? '';
    $pass = $_POST['pass'] ?? '';

    try {
        if ($driver === 'sqlite') {
            $path = __DIR__ . '/database.sqlite';
            if (!file_exists($path)) { touch($path); }
            $pdo = new PDO("sqlite:" . $path);
        } else {
            // MySQL, MariaDB, PostgreSQL, SQL Server, Oracle
            $dsn = "";
            if ($driver === 'mysql' || $driver === 'mariadb') {
                $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
            } elseif ($driver === 'pgsql') {
                $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
            } elseif ($driver === 'sqlsrv') {
                $dsn = "sqlsrv:Server=$host,$port;Database=$dbname";
            } elseif ($driver === 'oci') {
                $dsn = "oci:dbname=//$host:$port/$dbname";
            }
            $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        }

        // Run Migrations
        $pdo->exec("CREATE TABLE IF NOT EXISTS products (id INTEGER PRIMARY KEY AUTOINCREMENT, user_id INTEGER DEFAULT 1, name TEXT, sku TEXT, description TEXT, price REAL, stock INTEGER DEFAULT 0, is_active INTEGER DEFAULT 1, deleted_at DATETIME DEFAULT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP)");
        $pdo->exec("CREATE TABLE IF NOT EXISTS users (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT, email TEXT UNIQUE, password TEXT, role TEXT DEFAULT 'user')");

        // Save Config
        if (!is_dir(__DIR__ . '/../config')) { mkdir(__DIR__ . '/../config'); }
        $configContent = "<?php\nreturn [\n    'driver' => '$driver',\n    'host' => '$host',\n    'port' => '$port',\n    'database' => '$dbname',\n    'username' => '$user',\n    'password' => '$pass'\n];\n";
        file_put_contents($configFile, $configContent);
        file_put_contents(__DIR__ . '/install.lock', 'Installed');

        header("Location: /install.php?step=3");
        exit;
    } catch (Exception $e) {
        $error = "Connection Failed: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Micro-Framework Installer</title>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-sm border w-full max-w-2xl overflow-hidden">
        <div class="bg-indigo-600 p-6 text-white text-center">
            <h1 class="text-2xl font-bold">Installation Wizard</h1>
            <p class="text-indigo-100 mt-1">Configure your environment</p>
        </div>
        
        <div class="p-8">
            <?php if (isset($error)): ?>
                <div class="bg-red-50 text-red-600 p-4 rounded-lg mb-6 border border-red-100"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <?php if ($step == 1): ?>
                <h2 class="text-xl font-semibold mb-4 text-slate-800">1. System Compatibility Check</h2>
                <ul class="space-y-3 mb-8 text-slate-600">
                    <li class="flex items-center"><i class="text-emerald-500 mr-2 text-xl">?</i> PHP Version (<?= phpversion() ?>)</li>
                    <li class="flex items-center"><i class="<?= extension_loaded('pdo') ? 'text-emerald-500' : 'text-red-500' ?> mr-2 text-xl"><?= extension_loaded('pdo') ? '?' : '?' ?></i> PDO Extension</li>
                    <li class="flex items-center"><i class="<?= extension_loaded('pdo_sqlite') ? 'text-emerald-500' : 'text-slate-300' ?> mr-2 text-xl"><?= extension_loaded('pdo_sqlite') ? '?' : '?' ?></i> SQLite Driver</li>
                    <li class="flex items-center"><i class="<?= extension_loaded('pdo_mysql') ? 'text-emerald-500' : 'text-slate-300' ?> mr-2 text-xl"><?= extension_loaded('pdo_mysql') ? '?' : '?' ?></i> MySQL / MariaDB Driver</li>
                    <li class="flex items-center"><i class="<?= extension_loaded('pdo_pgsql') ? 'text-emerald-500' : 'text-slate-300' ?> mr-2 text-xl"><?= extension_loaded('pdo_pgsql') ? '?' : '?' ?></i> PostgreSQL Driver</li>
                </ul>
                <a href="?step=2" class="block w-full text-center bg-indigo-600 text-white py-3 rounded-lg font-medium hover:bg-indigo-700 transition">Continue to Database Setup</a>
            
            <?php elseif ($step == 2): ?>
                <h2 class="text-xl font-semibold mb-4 text-slate-800">2. Database Connection</h2>
                <form method="POST" action="?step=2" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Database Type</label>
                        <select name="driver" class="w-full border rounded-lg px-4 py-2 bg-white" onchange="toggleFields(this.value)">
                            <option value="sqlite">SQLite (No Server Required)</option>
                            <option value="mysql">MySQL</option>
                            <option value="mariadb">MariaDB</option>
                            <option value="pgsql">PostgreSQL</option>
                            <option value="sqlsrv">MS SQL Server</option>
                            <option value="oci">Oracle Database</option>
                        </select>
                    </div>
                    
                    <div id="server-fields" style="display:none;" class="space-y-4 border-t pt-4 mt-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Host</label>
                                <input type="text" name="host" value="127.0.0.1" class="w-full border rounded-lg px-4 py-2">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Port</label>
                                <input type="text" name="port" placeholder="3306" class="w-full border rounded-lg px-4 py-2">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Database Name</label>
                            <input type="text" name="dbname" class="w-full border rounded-lg px-4 py-2">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Username</label>
                                <input type="text" name="user" class="w-full border rounded-lg px-4 py-2">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Password</label>
                                <input type="password" name="pass" class="w-full border rounded-lg px-4 py-2">
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-indigo-600 text-white py-3 rounded-lg font-medium hover:bg-indigo-700 transition mt-6">Install & Run Migrations</button>
                </form>
                <script>
                    function toggleFields(val) {
                        document.getElementById('server-fields').style.display = (val === 'sqlite') ? 'none' : 'block';
                    }
                </script>
            
            <?php elseif ($step == 3): ?>
                <div class="text-center">
                    <div class="w-20 h-20 bg-emerald-100 text-emerald-500 rounded-full flex items-center justify-center mx-auto mb-6 text-4xl">?</div>
                    <h2 class="text-2xl font-bold mb-2 text-slate-800">Installation Complete!</h2>
                    <p class="text-slate-600 mb-8">The database has been configured and all tables have been migrated.</p>
                    <a href="/products" class="inline-block bg-slate-800 text-white px-8 py-3 rounded-lg font-medium hover:bg-slate-900 transition">Go to Dashboard</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
