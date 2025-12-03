<?php
session_start();
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['customer_id'])) {
  echo json_encode([
    'success' => false,
    'error' => 'not_logged_in',
    'message' => 'Please login'
  ]);
  exit();
}

include('../connection.php');

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

// validation for input
if (!isset($input['cart_ids']) || !is_array($input['cart_ids'])) {
  echo json_encode([
    'success' => false,
    'message' => 'Missing required fields'
  ]);
  exit();
}

$customer_id = $_SESSION['customer_id'];
$cart_ids = array_map('intval', $input['cart_ids']);

if (empty($cart_ids)) {
  echo json_encode([
    'success' => false,
    'message' => 'No items selected'
  ]);
  exit();
}

try {
  // Create placeholders for the IN clause
  $placeholders = implode(',', array_fill(0, count($cart_ids), '?'));
  
  // Delete selected items
  $deleteQuery = "DELETE FROM cart WHERE cart_id IN ($placeholders) AND customer_id = ?";
  $deleteStmt = $conn->prepare($deleteQuery);
  
  // Bind cart IDs and customer ID
  $params = array_merge($cart_ids, [$customer_id]);
  $deleteStmt->execute($params);
  
  echo json_encode([
    'success' => true,
    'message' => 'Items deleted successfully'
  ]);
  
} catch (PDOException $e) {
  echo json_encode([
    'success' => false,
    'message' => 'Database error: ' . $e->getMessage()
  ]);
}
?>
