<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
include('connection.php');

$resp = ['success' => false, 'errors' => []];

// must be logged in customer
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'customer') {
  http_response_code(401);
  $resp['errors'][] = 'Not authorized';
  echo json_encode($resp);
  exit();
}

$customer_id = $_SESSION['customer_id'] ?? $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  $resp['errors'][] = 'Invalid method';
  echo json_encode($resp);
  exit();
}

// label/phone/is_default are optional in the new UX
$label = trim($_POST['label'] ?? '');
$address_line = trim($_POST['address_line'] ?? '');
$city = trim($_POST['city'] ?? '');
$province = trim($_POST['province'] ?? '');
$postal_code = trim($_POST['postal_code'] ?? '');
$phone = trim($_POST['phone_number'] ?? '');
$is_default = isset($_POST['is_default']) ? 1 : 0;

if ($address_line === '' || $city === '' || $province === '') {
  $resp['errors'][] = 'Please fill in address, city and province.';
  echo json_encode($resp);
  exit();
}

if ($label === '') $label = 'Address';

try {
  if ($is_default) {
    $upd = $conn->prepare("UPDATE customer_addresses SET is_default = 0 WHERE customer_id = :id");
    $upd->execute(['id' => $customer_id]);
  }

  $ins = $conn->prepare("INSERT INTO customer_addresses (customer_id, label, address_line, city, province, postal_code, phone_number, is_default) VALUES (:cid, :label, :address_line, :city, :province, :postal_code, :phone, :is_default)");
  $ins->execute([
    'cid' => $customer_id,
    'label' => $label,
    'address_line' => $address_line,
    'city' => $city,
    'province' => $province,
    'postal_code' => $postal_code,
    'phone' => $phone,
    'is_default' => $is_default
  ]);

  $id = $conn->lastInsertId();

  $stmt = $conn->prepare("SELECT * FROM customer_addresses WHERE address_id = :id LIMIT 1");
  $stmt->execute(['id' => $id]);
  $row = $stmt->fetch(PDO::FETCH_ASSOC);

  $resp['success'] = true;
  $resp['address'] = $row;
  echo json_encode($resp);
  exit();
} catch (PDOException $e) {
  http_response_code(500);
  $resp['errors'][] = 'Database error: ' . $e->getMessage();
  echo json_encode($resp);
  exit();
}

?>
