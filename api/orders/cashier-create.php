<?php
header('Content-Type: application/json');
session_start();

// include DB connection
include_once __DIR__ . '/../connection.php';

// ensure kitchen queue table exists (light migration)
$createSql = "CREATE TABLE IF NOT EXISTS kitchen_orders (
  id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  order_ref VARCHAR(80) NOT NULL,
  table_name VARCHAR(60) DEFAULT '',
  customer_name VARCHAR(120) DEFAULT '',
  items TEXT NOT NULL,
  status CHAR(1) NOT NULL DEFAULT '1',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);";
$conn->exec($createSql);

$input = json_decode(file_get_contents('php://input'), true);
if (!$input || empty($input['items']) || !is_array($input['items'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'missing items']);
    exit();
}

$table = isset($input['table']) ? trim($input['table']) : '';
$customer = isset($input['customer']) ? trim($input['customer']) : '';
$itemsJson = json_encode($input['items']);
$orderRef = 'K-' . time() . rand(10,99);

try {
    $st = $conn->prepare('INSERT INTO kitchen_orders (order_ref, table_name, customer_name, items, status) VALUES (:ref, :table, :cust, :items, :status)');
    $status = '1'; // new order
    $st->bindParam(':ref', $orderRef);
    $st->bindParam(':table', $table);
    $st->bindParam(':cust', $customer);
    $st->bindParam(':items', $itemsJson);
    $st->bindParam(':status', $status);
    $st->execute();
    $id = (int)$conn->lastInsertId();

    echo json_encode(['success'=>true, 'id'=>$id, 'order_ref'=>$orderRef, 'created_at'=>date('c')]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}

?>
