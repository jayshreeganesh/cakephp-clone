<?php
namespace App\Controller;

use App\Model\Table\ProductsTable;
use PDO;
class ProductsController extends AppController {
    private $productsTable;
    public function __construct() {
        parent::__construct();
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        if (!isset($_SESSION['user_id'])) {
            if (strpos($uri, '/login') === false && strpos($uri, '/register') === false) {
                header('Location: /login'); exit;
            }
        }
        $this->productsTable = new ProductsTable();
    }
        public function index() {
        $q = $_GET['q'] ?? '';
        $sort = $_GET['sort'] ?? 'id';
        $dir = $_GET['dir'] ?? 'desc';
        $trash = isset($_GET['trash']) && $_GET['trash'] == 1;

        $db = \CakeCore\Database::connect();
        
        $trashCond = $trash ? "products.deleted_at IS NOT NULL" : "products.deleted_at IS NULL";

        if (($_SESSION['role'] ?? 'user') === 'admin') {
            $stmt = $db->prepare("SELECT products.*, users.email as creator_email FROM products LEFT JOIN users ON products.user_id = users.id WHERE $trashCond AND (products.name LIKE ? OR products.sku LIKE ? OR products.description LIKE ?) ORDER BY $sort $dir");
            $stmt->execute(["%$q%", "%$q%", "%$q%"]);
        } else {
            $stmt = $db->prepare("SELECT * FROM products WHERE user_id = ? AND $trashCond AND (name LIKE ? OR sku LIKE ? OR description LIKE ?) ORDER BY $sort $dir");
            $stmt->execute([$_SESSION['user_id'], "%$q%", "%$q%", "%$q%"]);
        }

        $this->set('products', $stmt->fetchAll(PDO::FETCH_ASSOC));
        $this->set('q', $q);
        $this->set('sort', $sort);
        $this->set('dir', $dir);
        $this->set('trash', $trash);
    }
public function export() {
        $q = $_GET['q'] ?? '';
        $format = $_GET['format'] ?? 'csv';
        $pdo = \CakeCore\Database::connect();
        if (($_SESSION['role'] ?? 'user') === 'admin') {
            $stmt = $pdo->prepare("SELECT * FROM products WHERE name LIKE ? OR sku LIKE ? OR description LIKE ? ORDER BY id DESC");
            $stmt->execute(["%$q%", "%$q%", "%$q%"]);
        } else {
            $stmt = $pdo->prepare("SELECT * FROM products WHERE user_id = ? AND (name LIKE ? OR sku LIKE ? OR description LIKE ?) ORDER BY id DESC");
            $stmt->execute([$_SESSION['user_id'], "%$q%", "%$q%", "%$q%"]);
        }
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($format === 'json') {
            header('Content-Type: application/json'); header('Content-Disposition: attachment; filename="products.json"');
            echo json_encode($products, JSON_PRETTY_PRINT); exit;
        }
        if ($format === 'csv') {
            header('Content-Type: text/csv'); header('Content-Disposition: attachment; filename="products.csv"');
            $output = fopen('php://output', 'w');
            fputcsv($output, ['ID', 'User ID', 'Name', 'SKU', 'Description', 'Price', 'Stock']);
            foreach ($products as $row) { fputcsv($output, [$row['id'], $row['user_id'] ?? '', $row['name'], $row['sku'], $row['description'], $row['price'], $row['stock']]); }
            fclose($output); exit;
        }
    }
    public function add() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;
            $data['user_id'] = $_SESSION['user_id'];
            if (empty($data['name']) || empty($data['sku']) || empty($data['price'])) {
                $_SESSION['flash_error'] = 'Required fields missing.';
                $this->redirect('/products/add');
            }
            $entity = $this->productsTable->newEmptyEntity();
            $entity->set($data);
            if ($this->productsTable->save($entity)) {
                $_SESSION['flash_success'] = 'Product has been saved.';
                $this->redirect('/products');
            }
        }
        $this->set('title', 'Add Product');
        $this->render('add');
    }
    public function edit($id) {
        $product = $this->productsTable->get($id);
        if (!$product || (($_SESSION['role'] ?? 'user') !== 'admin' && $product->user_id != $_SESSION['user_id'])) {
            $this->redirect('/products');
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;
            $data['user_id'] = $product->user_id; // prevent hijacking
            $product->set($data);
            if ($this->productsTable->save($product)) {
                $_SESSION['flash_success'] = 'Product updated.';
                $this->redirect('/products');
            }
        }
        $this->set('product', $product);
        $this->render('edit');
    }
        public function delete($id) {
        $db = \CakeCore\Database::connect();
        $stmt = $db->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $prod = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($prod && (($_SESSION['role'] ?? 'user') === 'admin' || $prod['user_id'] == $_SESSION['user_id'])) {
            $db->prepare("UPDATE products SET deleted_at = CURRENT_TIMESTAMP WHERE id = ?")->execute([$id]);
        }
        $this->redirect('/products');
    }

    public function forceDelete($id) {
        $db = \CakeCore\Database::connect();
        $stmt = $db->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $prod = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($prod && (($_SESSION['role'] ?? 'user') === 'admin' || $prod['user_id'] == $_SESSION['user_id'])) {
            $db->prepare("DELETE FROM products WHERE id = ?")->execute([$id]);
        }
        $this->redirect('/products?trash=1');
    }

    public function restore($id) {
        $db = \CakeCore\Database::connect();
        $stmt = $db->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $prod = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($prod && (($_SESSION['role'] ?? 'user') === 'admin' || $prod['user_id'] == $_SESSION['user_id'])) {
            $db->prepare("UPDATE products SET deleted_at = NULL WHERE id = ?")->execute([$id]);
        }
        $this->redirect('/products?trash=1');
    }

    public function toggleActive($id) {
        $db = \CakeCore\Database::connect();
        $stmt = $db->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $prod = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($prod && (($_SESSION['role'] ?? 'user') === 'admin' || $prod['user_id'] == $_SESSION['user_id'])) {
            $newStatus = ($prod['is_active'] ?? 1) ? 0 : 1;
            $db->prepare("UPDATE products SET is_active = ? WHERE id = ?")->execute([$newStatus, $id]);
        }
        $this->redirect('/products');
    }
}







