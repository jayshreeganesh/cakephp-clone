<?php
use CakeCore\Router;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'CakePHP Clone') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen flex flex-col">
    <!-- Navbar -->
    <nav class="bg-gradient-to-r from-red-700 via-rose-600 to-pink-600 text-white shadow-md">
        <div class="max-w-6xl mx-auto px-4 py-3.5 flex justify-between items-center">
            <a href="<?= Router::url(['controller' => 'Products', 'action' => 'index']) ?>" class="text-xl font-bold tracking-tight flex items-center space-x-2">
                <i class="fa-solid fa-cake-candles"></i>
                <span>CakePHP <span>Clone</span></span>
                <span class="text-xs bg-white/20 text-white px-2 py-0.5 rounded-full ml-2">Low Inode</span>
            </a>
            <div class="flex items-center space-x-4">
                <a href="<?= Router::url(['controller' => 'Products', 'action' => 'index']) ?>" class="text-sm font-medium hover:text-pink-100 transition">Products</a>
                <a href="<?= Router::url(['controller' => 'Products', 'action' => 'add']) ?>" class="text-sm bg-white text-rose-700 hover:bg-rose-50 font-semibold px-3 py-1.5 rounded-md shadow-sm transition">
                    <i class="fa-solid fa-plus mr-1"></i> Add Product
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Container -->
    <main class="max-w-6xl mx-auto px-4 py-8 flex-1 w-full">
        <!-- Cake Flash Messages -->
        <?= $this->Flash->render() ?>

        <!-- View Body Content -->
        <?= $content ?>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-6 mt-12 text-center text-xs text-slate-500">
        <div class="max-w-6xl mx-auto px-4 flex flex-col md:flex-row justify-between items-center space-y-2 md:space-y-0">
            <p>CakePHP Clone (Ultra-Low Inode MVC) &copy; <?= date('Y') ?></p>
            <p>Conventions over Configuration &bull; Total Project Files: <strong>~17</strong> &bull; Zero Composer Bloat</p>
        </div>
    </footer>
</body>
</html>
