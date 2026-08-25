<?php
$dbPath = __DIR__ . '/config/database.sqlite';
$jsonPath = __DIR__ . '/default_data.json';

try {
    $pdo = new PDO('sqlite:' . $dbPath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $action = $_GET['action'] ?? '';

    if ($action === 'reset') {
        $pdo->exec("DELETE FROM products");
        $pdo->exec("DELETE FROM sqlite_sequence WHERE name='products'");
        $action = 'seed'; 
    }

    if ($action === 'seed') {
        if (!file_exists($jsonPath)) die("Error: default_data.json not found.");
        $data = json_decode(file_get_contents($jsonPath), true);
        
        $cols = [];
        foreach($pdo->query("PRAGMA table_info(products)") as $row) {
            $cols[] = $row['name'];
        }
        
        foreach ($data as $item) {
            $insertCols = [];
            $insertVals = [];
            $placeholders = [];
            
            if (in_array('name', $cols)) { $insertCols[] = 'name'; $insertVals[] = $item['name']; $placeholders[] = '?'; }
            if (in_array('sku', $cols)) { $insertCols[] = 'sku'; $insertVals[] = $item['sku']; $placeholders[] = '?'; }
            if (in_array('description', $cols)) { $insertCols[] = 'description'; $insertVals[] = $item['description']; $placeholders[] = '?'; }
            if (in_array('price', $cols)) { $insertCols[] = 'price'; $insertVals[] = $item['price']; $placeholders[] = '?'; }
            if (in_array('stock', $cols)) { $insertCols[] = 'stock'; $insertVals[] = $item['stock']; $placeholders[] = '?'; }
            
            $now = date('Y-m-d H:i:s');
            if (in_array('created_at', $cols)) { $insertCols[] = 'created_at'; $insertVals[] = $now; $placeholders[] = '?'; }
            if (in_array('updated_at', $cols)) { $insertCols[] = 'updated_at'; $insertVals[] = $now; $placeholders[] = '?'; }
            if (in_array('created', $cols)) { $insertCols[] = 'created'; $insertVals[] = $now; $placeholders[] = '?'; }
            if (in_array('modified', $cols)) { $insertCols[] = 'modified'; $insertVals[] = $now; $placeholders[] = '?'; }
            
            $sql = "INSERT INTO products (" . implode(', ', $insertCols) . ") VALUES (" . implode(', ', $placeholders) . ")";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($insertVals);
        }
        echo "<script>alert('Database successfully synced with JSON!'); window.location.href='/';</script>";
        exit;
    }
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>
