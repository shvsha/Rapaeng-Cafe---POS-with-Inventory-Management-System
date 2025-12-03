<?php
if (session_status() === PHP_SESSION_NONE) session_start();
// connection.php sits in the same directory (api/connection.php)
include_once __DIR__ . '/connection.php';

// require login
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'customer') {
  header('Location: ../login.php');
  exit();
}

$customer_id = $_SESSION['customer_id'] ?? $_SESSION['user_id'];

// initialize variables so including pages always get a defined value
$msg = null;
$errors = [];
$addresses = [];

// Handle POST (add or delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['action']) && $_POST['action'] === 'add_address') {
    $label = trim($_POST['label'] ?? '');
    $address_line = trim($_POST['address_line'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $province = trim($_POST['province'] ?? '');
    $postal_code = trim($_POST['postal_code'] ?? '');
    $phone = trim($_POST['phone_number'] ?? '');
    $is_default = isset($_POST['is_default']) ? 1 : 0;

    if ($label === '' || $address_line === '' || $city === '' || $province === '') {
      $errors[] = 'Please fill in label, address and city/province.';
    } else {
      // if set default, clear previous defaults
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

      $msg = 'Address added successfully.';
    }
  } elseif (isset($_POST['action']) && $_POST['action'] === 'delete_address' && !empty($_POST['address_id'])) {
    $address_id = (int)$_POST['address_id'];
    $del = $conn->prepare("DELETE FROM customer_addresses WHERE address_id = :aid AND customer_id = :cid");
    $del->execute(['aid' => $address_id, 'cid' => $customer_id]);
    $msg = 'Address removed.';
  }
}

// fetch addresses
$stmt = $conn->prepare("SELECT * FROM customer_addresses WHERE customer_id = :id ORDER BY is_default DESC, created_at DESC");
$stmt->execute(['id' => $customer_id]);
$addresses = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>