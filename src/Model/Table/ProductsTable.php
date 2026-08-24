<?php
namespace App\Model\Table;

use CakeCore\Table;
use App\Model\Entity\Product;

class ProductsTable extends Table {
    protected string $table = 'products';
    protected string $entityClass = Product::class;
}
