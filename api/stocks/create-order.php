<?php
session_start();
header('Content-Type: application/json; charset=UTF-8');

// admin-only
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
  http_response_code(403);
  echo json_encode(['success'=>false,'message'=>'Forbidden']);
  exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode(['success'=>false,'message'=>'Method Not Allowed']);
  exit();
}

require_once '../connection.php';

$in = json_decode(file_get_contents('php://input'), true);
if (!$in || !isset($in['items']) || !is_array($in['items']) || empty($in['items'])){
  http_response_code(400);
  echo json_encode(['success'=>false,'message'=>'Missing items']);
  exit();
}

$items = $in['items'];
$subtotal = isset($in['subtotal']) ? floatval($in['subtotal']) : 0.0;
$tax = isset($in['tax']) ? floatval($in['tax']) : 0.0;
$total = isset($in['total']) ? floatval($in['total']) : 0.0;
$processed_by = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : null;

try {
  // create tables if missing
  $conn->exec("CREATE TABLE IF NOT EXISTS stock_orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    processed_by INT DEFAULT NULL,
    subtotal DOUBLE DEFAULT 0,
    tax DOUBLE DEFAULT 0,
    total DOUBLE DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

  $conn->exec("CREATE TABLE IF NOT EXISTS stock_order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    stock_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DOUBLE DEFAULT 0,
    FOREIGN KEY (order_id) REFERENCES stock_orders(order_id) ON DELETE CASCADE
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

  $conn->beginTransaction();

  $ins = $conn->prepare("INSERT INTO stock_orders (processed_by, subtotal, tax, total) VALUES (:proc, :sub, :tax, :total)");
  $ins->bindParam(':proc', $processed_by);
  $ins->bindParam(':sub', $subtotal);
  $ins->bindParam(':tax', $tax);
  $ins->bindParam(':total', $total);
  $ins->execute();
  $order_id = (int)$conn->lastInsertId();

  $rit = $conn->prepare("INSERT INTO stock_order_items (order_id, stock_id, quantity, unit_price) VALUES (:oid, :sid, :qty, :price)");
  foreach ($items as $it){
    $sid = intval($it['stock_id'] ?? 0); $qty = intval($it['quantity'] ?? 0); $price = floatval($it['unit_price'] ?? 0);
    if ($sid<=0 || $qty<=0) continue;
    $rit->bindParam(':oid', $order_id);
    $rit->bindParam(':sid', $sid);
    $rit->bindParam(':qty', $qty);
    $rit->bindParam(':price', $price);
    $rit->execute();
  }

  $conn->commit();
  echo json_encode(['success'=>true,'message'=>'Stock order created','order_id'=>$order_id]);
} catch (PDOException $e){
  if ($conn->inTransaction()) $conn->rollBack();
  http_response_code(500);
  echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}

?>
