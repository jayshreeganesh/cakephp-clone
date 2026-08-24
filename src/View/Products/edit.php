<?php
use CakeCore\Router;
?>
<div class="max-w-2xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Edit Product</h1>
            <p class="text-sm text-slate-500 mt-1">CakePHP Entity #<?= $product->id ?> &bull; <?= htmlspecialchars($product->name) ?></p>
        </div>
        <a href="<?= Router::url(['controller' => 'Products', 'action' => 'index']) ?>" class="text-sm text-slate-600 hover:text-slate-900 font-medium">
            &larr; Back to list
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 md:p-8">
        <form action="<?= Router::url(['controller' => 'Products', 'action' => 'edit', $product->id]) ?>" method="POST" class="space-y-6">
            <div>
                <label for="name" class="block text-sm font-medium text-slate-700 mb-1">Product Name <span class="text-rose-500">*</span></label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($product->name) ?>" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="sku" class="block text-sm font-medium text-slate-700 mb-1">SKU Code <span class="text-rose-500">*</span></label>
                    <input type="text" id="sku" name="sku" value="<?= htmlspecialchars($product->sku) ?>" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm font-mono focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition">
                </div>
                <div>
                    <label for="price" class="block text-sm font-medium text-slate-700 mb-1">Price ($) <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.01" min="0" id="price" name="price" value="<?= htmlspecialchars((string)$product->price) ?>" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition">
                </div>
                <div>
                    <label for="stock" class="block text-sm font-medium text-slate-700 mb-1">Stock Quantity</label>
                    <input type="number" min="0" id="stock" name="stock" value="<?= htmlspecialchars((string)$product->stock) ?>" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition">
                </div>
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-slate-700 mb-1">Description</label>
                <textarea id="description" name="description" rows="4" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition"><?= htmlspecialchars($product->description ?? '') ?></textarea>
            </div>

            <div class="pt-4 border-t border-slate-100 flex justify-end space-x-3">
                <a href="<?= Router::url(['controller' => 'Products', 'action' => 'index']) ?>" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-medium rounded-lg transition">
                    Cancel
                </a>
                <button type="submit" class="px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white text-sm font-medium rounded-lg shadow-sm transition">
                    Update Entity
                </button>
            </div>
        </form>
    </div>
</div>
