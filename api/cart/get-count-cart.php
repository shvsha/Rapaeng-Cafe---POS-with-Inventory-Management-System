<?php
session_start();
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['customer_id'])) {
  echo json_encode(['success' => true, 'count' => 0]);
  exit();
}

// Database connection
include('../connection.php');

$customer_id = $_SESSION['customer_id'];

try {
  // Get total count of items in cart
  $query = "SELECT COUNT(*) as count FROM cart WHERE customer_id = :customer_id";
  $stmt = $conn->prepare($query);
  $stmt->bindParam(':customer_id', $customer_id);
  $stmt->execute();
  $result = $stmt->fetch(PDO::FETCH_ASSOC);
  
  echo json_encode([
    'success' => true,
    'count' => intval($result['count'])
  ]);
  
} catch (PDOException $e) {
  echo json_encode([
    'success' => false,
    'message' => 'Database error: ' . $e->getMessage()
  ]);
}
?>