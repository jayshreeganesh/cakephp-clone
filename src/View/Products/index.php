<?php
use CakeCore\Router;
?>
<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Products Catalog</h1>
            <p class="text-sm text-slate-500 mt-1">CakePHP Table & Entity ORM Management</p>
        </div>
        <a href="<?= Router::url(['controller' => 'Products', 'action' => 'add']) ?>" class="inline-flex items-center px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-sm font-medium rounded-lg shadow-sm transition">
            <i class="fa-solid fa-plus mr-2"></i> Add Product
        </a>
    </div>

    <?php if (empty($products)): ?>
        <div class="p-12 text-center">
            <div class="w-16 h-16 bg-rose-50 text-rose-500 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                <i class="fa-solid fa-cake-candles"></i>
            </div>
            <h3 class="text-lg font-semibold text-slate-700">No products found</h3>
            <p class="text-slate-500 text-sm mt-1 mb-6">Create your first product entity to test the CRUD functionality.</p>
            <a href="<?= Router::url(['controller' => 'Products', 'action' => 'add']) ?>" class="inline-flex items-center px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-sm font-medium rounded-lg shadow-sm transition">
                <i class="fa-solid fa-plus mr-2"></i> Add Product
            </a>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-xs font-semibold uppercase text-slate-500">
                        <th class="py-3.5 px-6">ID</th>
                        <th class="py-3.5 px-6">Product</th>
                        <th class="py-3.5 px-6">SKU</th>
                        <th class="py-3.5 px-6">Price</th>
                        <th class="py-3.5 px-6">Stock</th>
                        <th class="py-3.5 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    <?php foreach ($products as $product): ?>
                        <tr class="hover:bg-slate-50/75 transition">
                            <td class="py-4 px-6 font-medium text-slate-400">#<?= $product->id ?></td>
                            <td class="py-4 px-6">
                                <a href="<?= Router::url(['controller' => 'Products', 'action' => 'view', $product->id]) ?>" class="font-semibold text-slate-800 hover:text-rose-600 transition">
                                    <?= htmlspecialchars($product->name) ?>
                                </a>
                                <?php if (!empty($product->description)): ?>
                                    <p class="text-xs text-slate-400 mt-0.5 truncate max-w-xs"><?= htmlspecialchars($product->description) ?></p>
                                <?php endif; ?>
                            </td>
                            <td class="py-4 px-6 font-mono text-xs text-slate-600 bg-slate-50/50 rounded inline-block my-3"><?= htmlspecialchars($product->sku) ?></td>
                            <td class="py-4 px-6 font-semibold text-slate-900">$<?= number_format((float)$product->price, 2) ?></td>
                            <td class="py-4 px-6">
                                <?php if ($product->stock > 10): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                        <?= $product->stock ?> in stock
                                    </span>
                                <?php elseif ($product->stock > 0): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                        Low stock (<?= $product->stock ?>)
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-rose-100 text-rose-800">
                                        Out of stock
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="py-4 px-6 text-right space-x-2 whitespace-nowrap">
                                <a href="<?= Router::url(['controller' => 'Products', 'action' => 'view', $product->id]) ?>" class="inline-flex items-center p-1.5 text-slate-500 hover:text-rose-600 hover:bg-rose-50 rounded transition" title="View">
                                    <i class="fa-regular fa-eye"></i>
                                </a>
                                <a href="<?= Router::url(['controller' => 'Products', 'action' => 'edit', $product->id]) ?>" class="inline-flex items-center p-1.5 text-slate-500 hover:text-amber-600 hover:bg-amber-50 rounded transition" title="Edit">
                                    <i class="fa-regular fa-pen-to-square"></i>
                                </a>
                                <a href="<?= Router::url(['controller' => 'Products', 'action' => 'delete', $product->id]) ?>" onclick="return confirm('Are you sure you want to delete this product?');" class="inline-flex items-center p-1.5 text-slate-500 hover:text-rose-600 hover:bg-rose-50 rounded transition" title="Delete">
                                    <i class="fa-regular fa-trash-can"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
