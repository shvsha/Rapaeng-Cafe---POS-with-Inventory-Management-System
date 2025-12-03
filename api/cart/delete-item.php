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
if (!isset($input['cart_id'])) {
  echo json_encode([
    'success' => false,
    'message' => 'Missing required fields'
  ]);
  exit();
}

$customer_id = $_SESSION['customer_id'];
$cart_id = intval($input['cart_id']);

try {
  // verification
  $deleteQuery = "DELETE FROM cart WHERE cart_id = :cart_id AND customer_id = :customer_id";
  $deleteStmt = $conn->prepare($deleteQuery);
  $deleteStmt->bindParam(':cart_id', $cart_id);
  $deleteStmt->bindParam(':customer_id', $customer_id);
  $deleteStmt->execute();
  
  if ($deleteStmt->rowCount() === 0) {
    echo json_encode([
      'success' => false,
      'message' => 'Cart item not found'
    ]);
    exit();
  }
  
  echo json_encode([
    'success' => true,
    'message' => 'Item deleted successfully'
  ]);
  
} catch (PDOException $e) {
  echo json_encode([
    'success' => false,
    'message' => 'Database error: ' . $e->getMessage()
  ]);
}
?>
