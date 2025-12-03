<?php
session_start();
header('Content-Type: application/json');

// must be logged-in customer
if (!isset($_SESSION['customer_id']) || $_SESSION['user_type'] !== 'customer') {
  echo json_encode(['success' => false, 'error' => 'not_logged_in']);
  exit();
}

include('../connection.php');

$input = json_decode(file_get_contents('php://input'), true);
if (!$input || !isset($input['items']) || !is_array($input['items']) || empty($input['items'])) {
  echo json_encode(['success' => false, 'message' => 'No items provided']);
  exit();
}

$customer_id = $_SESSION['customer_id'];
$table = isset($input['table']) ? trim($input['table']) : '';
$payment_method = isset($input['payment_method']) ? trim($input['payment_method']) : '';
$subtotal = isset($input['subtotal']) ? floatval($input['subtotal']) : 0.0;
$tax = isset($input['tax']) ? floatval($input['tax']) : 0.0;
$total = isset($input['total']) ? floatval($input['total']) : 0.0;

try {
  // transaction
  $conn->beginTransaction();

  // Insert order: total_amount uses integer representation (round as schema uses int)
  $ins = $conn->prepare("INSERT INTO orders (customer_id, total_amount, create_at, status) VALUES (:cid, :total, CURDATE(), :status)");
  // use status '1' as new order pending/queued
  $status = '1';
  // store rounded integer amount
  $amountToStore = (int) round($total);
  $ins->bindParam(':cid', $customer_id);
  $ins->bindParam(':total', $amountToStore);
  $ins->bindParam(':status', $status);
  $ins->execute();
  $order_id = (int)$conn->lastInsertId();

  // insert order items
  $itemSt = $conn->prepare("INSERT INTO order_items (order_id, menu_id, quantity, price) VALUES (:order_id, :menu_id, :quantity, :price)");
  foreach ($input['items'] as $it) {
    $menu_id = intval($it['menu_id']);
    $qty = intval($it['quantity']);
    // store item price as rounded int (price per item)
    $price = (int) round(floatval($it['price']));
    if ($menu_id <= 0 || $qty <= 0) continue;
    $itemSt->bindParam(':order_id', $order_id);
    $itemSt->bindParam(':menu_id', $menu_id);
    $itemSt->bindParam(':quantity', $qty);
    $itemSt->bindParam(':price', $price);
    $itemSt->execute();
  }

    // Create kitchen order for the queue
    // Build items JSON for kitchen display
    $kitchenItems = array_map(function($it) {
      return [
        'menu_id' => intval($it['menu_id']),
        'name' => $it['name'] ?? 'Item',
        'quantity' => intval($it['quantity']),
        'price' => floatval($it['price'])
      ];
    }, $input['items']);

    $kitchenItemsJson = json_encode($kitchenItems);
    $orderRef = 'ORD-' . $order_id;

    // fetch customer full name for kitchen display
    $custStmt = $conn->prepare('SELECT full_name FROM customers WHERE customer_id = :cid LIMIT 1');
    $custStmt->bindParam(':cid', $customer_id);
    $custStmt->execute();
    $custRow = $custStmt->fetch(PDO::FETCH_ASSOC);
    $customerName = $custRow['full_name'] ?? '';

    // Insert into kitchen_orders using the expected schema (order_ref, table_name, customer_name, items, status)
    $kitchenStmt = $conn->prepare("INSERT INTO kitchen_orders (order_ref, table_name, customer_name, items, status, created_at) VALUES (:order_ref, :table_name, :customer_name, :items, :status, CURRENT_TIMESTAMP)");
    $tableName = 'Ordered Online';
    $kitchenStatus = '1'; // New order
    $kitchenStmt->bindParam(':order_ref', $orderRef);
    $kitchenStmt->bindParam(':table_name', $tableName);
    $kitchenStmt->bindParam(':customer_name', $customerName);
    $kitchenStmt->bindParam(':items', $kitchenItemsJson);
    $kitchenStmt->bindParam(':status', $kitchenStatus);
    $kitchenStmt->execute();

  // clear cart items that were part of this order (prefer cart_id if provided)
  $cartIdsToRemove = [];
  foreach ($input['items'] as $it) {
    if (isset($it['cart_id'])) $cartIdsToRemove[] = intval($it['cart_id']);
  }

  if (!empty($cartIdsToRemove)) {
    // remove only those cart rows for this customer
    $placeholders = implode(',', array_fill(0, count($cartIdsToRemove), '?'));
    $params = array_merge($cartIdsToRemove, [$customer_id]);
    $del = $conn->prepare("DELETE FROM cart WHERE cart_id IN ($placeholders) AND customer_id = ?");
    $del->execute($params);
  } else {
    // fallback: clear entire cart for this customer
    $del = $conn->prepare("DELETE FROM cart WHERE customer_id = :cid");
    $del->bindParam(':cid', $customer_id);
    $del->execute();
  }

  $conn->commit();

  echo json_encode(['success' => true, 'order_id' => $order_id, 'message' => 'Order queued successfully']);
} catch (PDOException $e) {
  if ($conn->inTransaction()) $conn->rollBack();
  http_response_code(500);
  echo json_encode(['success' => false, 'message' => 'DB error: '.$e->getMessage()]);
}

?>
