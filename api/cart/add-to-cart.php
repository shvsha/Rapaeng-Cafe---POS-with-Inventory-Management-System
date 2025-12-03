<?php
session_start();
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['customer_id'])) {
  echo json_encode([
    'success' => false,
    'error' => 'not_logged_in',
    'message' => 'Please login to add items to cart'
  ]);
  exit();
}

include('../connection.php');

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

// validation for input
if (!isset($input['menu_id']) || !isset($input['quantity'])) {
  echo json_encode([
    'success' => false,
    'message' => 'Missing required fields'
  ]);
  exit();
}

$customer_id = $_SESSION['customer_id'];
$menu_id = intval($input['menu_id']);
$quantity = intval($input['quantity']);

// validate quantity
if ($quantity < 1 || $quantity > 99) {
  echo json_encode([
    'success' => false,
    'message' => 'Invalid quantity'
  ]);
  exit();
}

try {
  // Check if item already exists in cart
  $checkQuery = "SELECT cart_id, quantity FROM cart 
                 WHERE customer_id = :customer_id AND menu_id = :menu_id";
  $checkStmt = $conn->prepare($checkQuery);
  $checkStmt->bindParam(':customer_id', $customer_id);
  $checkStmt->bindParam(':menu_id', $menu_id);
  $checkStmt->execute();
  $existingItem = $checkStmt->fetch(PDO::FETCH_ASSOC);
  
  if ($existingItem) {
    // Update quantity if item already in cart
    $newQuantity = $existingItem['quantity'] + $quantity;
    if ($newQuantity > 99) $newQuantity = 99;
    
    $updateQuery = "UPDATE cart SET quantity = :quantity WHERE cart_id = :cart_id";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bindParam(':quantity', $newQuantity);
    $updateStmt->bindParam(':cart_id', $existingItem['cart_id']);
    $updateStmt->execute();
    
    echo json_encode([
      'success' => true,
      'message' => 'Cart updated successfully',
      'action' => 'updated'
    ]);
  } else {
    // add new item in cart
    $insertQuery = "INSERT INTO cart (customer_id, menu_id, quantity, added_at) 
                    VALUES (:customer_id, :menu_id, :quantity, NOW())";
    $insertStmt = $conn->prepare($insertQuery);
    $insertStmt->bindParam(':customer_id', $customer_id);
    $insertStmt->bindParam(':menu_id', $menu_id);
    $insertStmt->bindParam(':quantity', $quantity);
    $insertStmt->execute();
    
    echo json_encode([
      'success' => true,
      'message' => 'Added to cart successfully',
      'action' => 'added'
    ]);
  }
  
} catch (PDOException $e) {
  echo json_encode([
    'success' => false,
    'message' => 'Database error: ' . $e->getMessage()
  ]);
}
?>