<?php
use CakeCore\Router;
?>
<div class="max-w-2xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Entity Details</h1>
            <p class="text-sm text-slate-500 mt-1">CakePHP Entity #<?= $product->id ?></p>
        </div>
        <a href="<?= Router::url(['controller' => 'Products', 'action' => 'index']) ?>" class="text-sm text-slate-600 hover:text-slate-900 font-medium">
            &larr; Back to list
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-6 md:p-8">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-xs font-mono bg-slate-100 text-slate-600 px-2.5 py-1 rounded">SKU: <?= htmlspecialchars($product->sku) ?></span>
                    <h2 class="text-2xl font-bold text-slate-900 mt-2"><?= htmlspecialchars($product->name) ?></h2>
                </div>
                <div class="text-right">
                    <span class="text-2xl font-extrabold text-rose-600">$<?= number_format((float)$product->price, 2) ?></span>
                </div>
            </div>

            <div class="mt-6 border-t border-b border-slate-100 py-4 grid grid-cols-2 gap-4">
                <div>
                    <span class="text-xs uppercase tracking-wider text-slate-400 font-semibold">Stock Status</span>
                    <div class="mt-1">
                        <?php if ($product->stock > 10): ?>
                            <span class="text-sm font-semibold text-emerald-600"><i class="fa-solid fa-circle-check mr-1"></i> In Stock (<?= $product->stock ?> units)</span>
                        <?php elseif ($product->stock > 0): ?>
                            <span class="text-sm font-semibold text-amber-600"><i class="fa-solid fa-triangle-exclamation mr-1"></i> Low Stock (<?= $product->stock ?> units)</span>
                        <?php else: ?>
                            <span class="text-sm font-semibold text-rose-600"><i class="fa-solid fa-circle-xmark mr-1"></i> Out of Stock</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div>
                    <span class="text-xs uppercase tracking-wider text-slate-400 font-semibold">Created Date</span>
                    <p class="text-sm font-medium text-slate-700 mt-1"><?= htmlspecialchars($product->created ?? date('Y-m-d H:i:s')) ?></p>
                </div>
            </div>

            <div class="mt-6">
                <h3 class="text-xs uppercase tracking-wider text-slate-400 font-semibold mb-2">Description</h3>
                <div class="text-sm text-slate-600 leading-relaxed bg-slate-50 p-4 rounded-lg">
                    <?= nl2br(htmlspecialchars($product->description ?: 'No description provided for this entity.')) ?>
                </div>
            </div>

            <div class="mt-8 pt-4 border-t border-slate-100 flex justify-between items-center">
                <a href="<?= Router::url(['controller' => 'Products', 'action' => 'delete', $product->id]) ?>" onclick="return confirm('Are you sure you want to delete this product?');" class="text-sm text-rose-600 hover:text-rose-800 font-medium">
                    <i class="fa-regular fa-trash-can mr-1"></i> Delete Entity
                </a>
                <div class="space-x-2">
                    <a href="<?= Router::url(['controller' => 'Products', 'action' => 'index']) ?>" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-medium rounded-lg transition">
                        Back
                    </a>
                    <a href="<?= Router::url(['controller' => 'Products', 'action' => 'edit', $product->id]) ?>" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-sm font-medium rounded-lg shadow-sm transition">
                        <i class="fa-regular fa-pen-to-square mr-1"></i> Edit Entity
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
