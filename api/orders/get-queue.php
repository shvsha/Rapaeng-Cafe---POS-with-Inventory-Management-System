<?php
header('Content-Type: application/json');
include_once __DIR__ . '/../connection.php';

// make sure kitchen_orders exists
$conn->exec("CREATE TABLE IF NOT EXISTS kitchen_orders (
  id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  order_ref VARCHAR(80) NOT NULL,
  table_name VARCHAR(60) DEFAULT '',
  customer_name VARCHAR(120) DEFAULT '',
  items TEXT NOT NULL,
  status CHAR(1) NOT NULL DEFAULT '1',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)");

$q = $conn->prepare('SELECT * FROM kitchen_orders ORDER BY created_at DESC');
$q->execute();
$rows = $q->fetchAll(PDO::FETCH_ASSOC);

$result = array_map(function($r){
    // decode items
    $r['items'] = json_decode($r['items'], true) ?: [];
    return $r;
}, $rows);

echo json_encode(['success'=>true, 'orders'=>$result]);

?>
