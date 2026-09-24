<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Products Catalog</h1>
            <p class="text-sm text-slate-500 mt-1">CakePHP Mock MVC</p>
        </div>
        <div class="flex gap-2">
            <?php if(isset($_SESSION['user_id'])): ?>
                <span class="px-4 py-2 text-sm text-slate-600 font-bold bg-slate-100 rounded-lg">Welcome, <?= htmlspecialchars($_SESSION['user_name']) ?></span>
                <a href="/logout" class="inline-flex items-center px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 text-sm font-medium rounded-lg shadow-sm transition">Logout</a>
            <?php else: ?>
                <a href="/login" class="inline-flex items-center px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white text-sm font-medium rounded-lg shadow-sm transition">Login</a>
                <a href="/register" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">Register</a>
            <?php endif; ?>
        </div>
    </div>

    <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
    <!-- Admin Controls -->
    <div class="bg-indigo-50 border-b border-indigo-100 px-6 py-3 flex gap-3 text-sm">
        <span class="font-bold text-indigo-800 flex items-center mr-2"><i class="fa-solid fa-shield-halved mr-2"></i> Admin Tools:</span>
        <a href="/manage_db.php?action=seed" class="text-indigo-600 hover:text-indigo-800 bg-white px-3 py-1 rounded border border-indigo-200" onclick="return confirm('Seed demo data?');">Seed Data</a>
        <a href="/manage_db.php?action=reset" class="text-rose-600 hover:text-rose-800 bg-white px-3 py-1 rounded border border-rose-200" onclick="return confirm('Warning: This deletes all products and resets the database. Continue?');">Reset DB</a>
        <a href="/build_zip.php" class="text-emerald-600 hover:text-emerald-800 bg-white px-3 py-1 rounded border border-emerald-200">Download .zip</a>
    </div>
    <?php endif; ?>
    <?php if (!empty($products) || !empty($q)): ?>
    <!-- Search Bar -->
    <div class="px-6 py-4 border-b border-slate-100 bg-slate-50">
        <form method="GET" action="/products" class="flex gap-2">
            <input type="text" name="q" value="<?= htmlspecialchars($q ?? '') ?>" placeholder="Search products..." class="flex-1 px-4 py-2 border rounded-lg">
            <button type="submit" class="px-4 py-2 bg-slate-800 text-white rounded-lg">Search</button>
            <?php if(!empty($q)): ?><a href="<?= \CakeCore\Router::url('products') ?>" class="px-4 py-2 bg-slate-200 rounded-lg">Clear</a><?php endif; ?>
        </form>
    </div>
    <?php endif; ?>
    <!-- Action Buttons -->
    <div class="px-6 pb-4 bg-slate-50 border-b border-slate-100 flex flex-wrap gap-2 justify-between items-center">
        <div class="flex gap-2">
            <?php if (!empty($products)): ?>
                <a href="<?= \CakeCore\Router::url('products/export') ?>?format=csv&q=<?= urlencode($q ?? '') ?>" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg shadow-sm transition">
                    <i class="fa-solid fa-file-csv mr-2"></i> Export CSV (Excel)
                </a>
                <a href="<?= \CakeCore\Router::url('products/export') ?>?format=json&q=<?= urlencode($q ?? '') ?>" class="inline-flex items-center px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white text-sm font-medium rounded-lg shadow-sm transition">
                    <i class="fa-solid fa-code mr-2"></i> Export JSON
                </a>
            <?php endif; ?>
        </div>
        <?php if (!isset($trash) || !$trash): ?>
        <a href="<?= \CakeCore\Router::url('products') ?>?trash=1" class="inline-flex items-center px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 text-sm font-medium rounded-lg shadow-sm transition">
            <i class="fa-solid fa-trash mr-2"></i> View Trash
        </a>
        <?php else: ?>
        <a href="<?= \CakeCore\Router::url('products') ?>" class="inline-flex items-center px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 text-sm font-medium rounded-lg shadow-sm transition">
            <i class="fa-solid fa-arrow-left mr-2"></i> Back to Products
        </a>
        <?php endif; ?>
        <a href="<?= \CakeCore\Router::url('products/add') ?>" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">
            <i class="fa-solid fa-plus mr-2"></i> Add Product
        </a>
    </div>
        

    <?php if (empty($products)): ?>
        <div class="p-12 text-center">
            <div class="w-16 h-16 bg-red-50 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                <i class="fa-brands fa-laravel"></i>
            </div>
                        <?php if (!empty($q)): ?>
                <h3 class="text-lg font-semibold text-slate-700">No results found for "<?= htmlspecialchars($q) ?>"</h3>
                <p class="text-slate-500 text-sm mt-1 mb-6">Try adjusting your search query or clear your filters.</p>
                <a href="<?= \CakeCore\Router::url('products') ?>" class="inline-flex items-center px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 text-sm font-medium rounded-lg shadow-sm transition">
                    Clear Search
                </a>
            <?php else: ?>
                <h3 class="text-lg font-semibold text-slate-700">No products found</h3>
                <p class="text-slate-500 text-sm mt-1 mb-6">Create your first product to get started.</p>
                <a href="<?= \CakeCore\Router::url('products/add') ?>" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">
                    <i class="fa-solid fa-plus mr-2"></i> Create Product
                </a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-xs font-semibold uppercase text-slate-500">
                        <th class="py-3.5 px-6">ID</th>
                        <th class="py-3.5 px-6"><a href="?sort=name&dir=<?= ($sort=='name' && $dir=='asc') ? 'desc' : 'asc' ?>&q=<?= $q ?? '' ?>">Product <i class="fa-solid fa-sort ml-1"></i></a></th>
                        <th class="py-3.5 px-6">SKU</th>
                        <th class="py-3.5 px-6"><a href="?sort=price&dir=<?= ($sort=='price' && $dir=='asc') ? 'desc' : 'asc' ?>&q=<?= $q ?? '' ?>">Price <i class="fa-solid fa-sort ml-1"></i></a></th>
                        <th class="py-3.5 px-6">Stock</th>
                                                <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <th class="py-3.5 px-6">Creator</th>
                        <?php endif; ?>
                        <th class="py-3.5 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    <?php foreach ($products as $product): ?>
                        <tr class="hover:bg-slate-50/75 transition">
                            <td class="py-4 px-6 font-medium text-slate-400">#<?= $product['id'] ?></td>
                            <td class="py-4 px-6">
                                <a href="<?= \CakeCore\Router::url('products/' . $product['id']) ?>" class="font-semibold text-slate-800 hover:text-red-600 transition">
                                    <?<?= htmlspecialchars($product['name']) ?>
                                </a>
                                <?php if (!isset($trash) || !$trash): ?>
                                    <?php if ($product['is_active'] ?? 1): ?>
                                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emerald-100 text-emerald-800">Active</span>
                                    <?php else: ?>
                                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-600">Inactive</span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-rose-100 text-rose-800">Deleted</span>
                                <?php endif; ?>
                                <?php if (!empty($product['description'])): ?>
                                    <p class="text-xs text-slate-400 mt-0.5 truncate max-w-xs"><?= htmlspecialchars($product['description']) ?></p>
                                <?php endif; ?>
                            </td>
                            <td class="py-4 px-6 font-mono text-xs text-slate-600 bg-slate-50/50 rounded inline-block my-3"><?= htmlspecialchars($product['sku']) ?></td>
                            <td class="py-4 px-6 font-semibold text-slate-900">$<?= number_format((float)$product['price'], 2) ?></td>
                            <td class="py-4 px-6">
                                <?php if ($product['stock'] > 10): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                        <?= $product['stock'] ?> in stock
                                    </span>
                                <?php elseif ($product['stock'] > 0): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                        Low stock (<?= $product['stock'] ?>)
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-rose-100 text-rose-800">
                                        Out of stock
                                    </span>
                                <?php endif; ?>
                            </td>
                                                        <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                            <td class="py-4 px-6 text-sm text-slate-500">
                                <?= htmlspecialchars($product['creator_email'] ?? 'Unknown') ?>
                            </td>
                            <?php endif; ?>
                            <td class="py-4 px-6 text-right space-x-2 whitespace-nowrap">
                                <?php if (!isset($trash) || !$trash): ?>
                                    <form action="/products/toggleActive/<?= $product['id'] ?>" method="POST" class="inline-block">
                                        <button type="submit" class="inline-flex items-center p-1.5 <?= ($product['is_active'] ?? 1) ? 'text-amber-600 hover:bg-amber-50' : 'text-emerald-600 hover:bg-emerald-50' ?> rounded transition" title="<?= ($product['is_active'] ?? 1) ? 'Deactivate' : 'Activate' ?>">
                                            <i class="fa-solid <?= ($product['is_active'] ?? 1) ? 'fa-pause' : 'fa-play' ?>"></i>
                                        </button>
                                    </form>
                                    <a href="/products/edit/<?= $product['id'] ?>" class="inline-flex items-center p-1.5 text-slate-500 hover:text-amber-600 hover:bg-amber-50 rounded transition" title="Edit">
                                        <i class="fa-regular fa-pen-to-square"></i>
                                    </a>
                                    <form action="/products/delete/<?= $product['id'] ?>" method="POST" class="inline-block" onsubmit="return confirm('Move this product to trash?');">
                                        <button type="submit" class="inline-flex items-center p-1.5 text-slate-500 hover:text-rose-600 hover:bg-rose-50 rounded transition" title="Trash">
                                            <i class="fa-regular fa-trash-can"></i>
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <form action="/products/restore/<?= $product['id'] ?>" method="POST" class="inline-block">
                                        <button type="submit" class="inline-flex items-center p-1.5 text-emerald-600 hover:bg-emerald-50 rounded transition" title="Restore">
                                            <i class="fa-solid fa-arrow-rotate-left"></i>
                                        </button>
                                    </form>
                                    <form action="/products/forceDelete/<?= $product['id'] ?>" method="POST" class="inline-block" onsubmit="return confirm('WARNING: Permanently delete this product? This cannot be undone!');">
                                        <button type="submit" class="inline-flex items-center p-1.5 text-rose-600 hover:bg-rose-50 rounded transition" title="Hard Delete">
                                            <i class="fa-solid fa-eraser"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>


