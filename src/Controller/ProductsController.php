<?php
namespace App\Controller;
use CakeCore\Controller;
use App\Model\ProductsTable;
use PDO;
class ProductsController extends Controller {
    private $productsTable;
    public function __construct() {
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
        $pdo = \CakeCore\Database::connect();
        
        if (($_SESSION['role'] ?? 'user') === 'admin') {
            $stmt = $pdo->prepare("SELECT * FROM products WHERE name LIKE ? OR sku LIKE ? OR description LIKE ? ORDER BY $sort $dir");
            $stmt->execute(["%$q%", "%$q%", "%$q%"]);
        } else {
            $stmt = $pdo->prepare("SELECT * FROM products WHERE user_id = ? AND (name LIKE ? OR sku LIKE ? OR description LIKE ?) ORDER BY $sort $dir");
            $stmt->execute([$_SESSION['user_id'], "%$q%", "%$q%", "%$q%"]);
        }
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->render('Products/index', ['products' => $products, 'q' => $q, 'sort' => $sort, 'dir' => $dir]);
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
            if ($this->productsTable->save($data)) {
                $_SESSION['flash_success'] = 'Product has been saved.';
                $this->redirect('/products');
            }
        }
        $this->render('Products/add', ['title' => 'Add Product']);
    }
    public function edit($id) {
        $product = $this->productsTable->get($id);
        if (!$product || (($_SESSION['role'] ?? 'user') !== 'admin' && $product['user_id'] != $_SESSION['user_id'])) {
            $this->redirect('/products');
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;
            $data['user_id'] = $product['user_id']; // prevent hijacking
            if ($this->productsTable->update($id, $data)) {
                $_SESSION['flash_success'] = 'Product updated.';
                $this->redirect('/products');
            }
        }
        $this->render('Products/edit', ['product' => $product]);
    }
    public function delete($id) {
        $product = $this->productsTable->get($id);
        if ($product && (($_SESSION['role'] ?? 'user') === 'admin' || $product['user_id'] == $_SESSION['user_id'])) {
            $this->productsTable->delete($id);
        }
        $this->redirect('/products');
    }
}