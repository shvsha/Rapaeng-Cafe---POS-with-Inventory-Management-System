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

// Database connection
include('../connection.php');

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Validate input
if (!isset($input['cart_id']) || !isset($input['quantity'])) {
  echo json_encode([
    'success' => false,
    'message' => 'Missing required fields'
  ]);
  exit();
}

$customer_id = $_SESSION['customer_id'];
$cart_id = intval($input['cart_id']);
$quantity = intval($input['quantity']);

// Validate quantity
if ($quantity < 1 || $quantity > 99) {
  echo json_encode([
    'success' => false,
    'message' => 'Invalid quantity'
  ]);
  exit();
}

try {
  // Verify cart item belongs to the customer
  $checkQuery = "SELECT * FROM cart WHERE cart_id = :cart_id AND customer_id = :customer_id";
  $checkStmt = $conn->prepare($checkQuery);
  $checkStmt->bindParam(':cart_id', $cart_id);
  $checkStmt->bindParam(':customer_id', $customer_id);
  $checkStmt->execute();
  
  if ($checkStmt->rowCount() === 0) {
    echo json_encode([
      'success' => false,
      'message' => 'Cart item not found'
    ]);
    exit();
  }
  
  // Update quantity
  $updateQuery = "UPDATE cart SET quantity = :quantity WHERE cart_id = :cart_id AND customer_id = :customer_id";
  $updateStmt = $conn->prepare($updateQuery);
  $updateStmt->bindParam(':quantity', $quantity);
  $updateStmt->bindParam(':cart_id', $cart_id);
  $updateStmt->bindParam(':customer_id', $customer_id);
  $updateStmt->execute();
  
  echo json_encode([
    'success' => true,
    'message' => 'Quantity updated successfully'
  ]);
  
} catch (PDOException $e) {
  echo json_encode([
    'success' => false,
    'message' => 'Database error: ' . $e->getMessage()
  ]);
}
?>
