<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['customer_id'])) {
  echo json_encode(['success'=>false,'error'=>'not_logged_in']);
  exit();
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input || !isset($input['cart_ids']) || !is_array($input['cart_ids'])) {
  echo json_encode(['success'=>false,'message'=>'Invalid payload']);
  exit();
}

$cartIds = array_values(array_filter(array_map('intval', $input['cart_ids'])));

// store in session for checkout page
$_SESSION['checkout_selected'] = $cartIds;

echo json_encode(['success'=>true]);
?>
