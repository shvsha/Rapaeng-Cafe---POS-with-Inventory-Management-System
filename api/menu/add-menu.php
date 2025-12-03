<?php
header("Content-Type: application/json");
session_start();

// only allow admin users to add menu items
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
  http_response_code(403);
  echo json_encode(["message"=>"Forbidden", "success"=>false]);
  exit();
}

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    http_response_code(405);
    echo json_encode([
        "message" => "Method Not Allowed",
        "success" => false
    ]);
    exit();
}

require_once "../connection.php";

// accept JSON body or standard form POST
$raw = file_get_contents('php://input');
$json = json_decode($raw, true);
if ($json && is_array($json)) {
  $input = $json;
} else {
  // standard form submit
  $input = $_POST;
}

// required fields -- allow 'image' or 'images' from form
if (!isset($input['name']) || !isset($input['price']) || !isset($input['description']) || (!isset($input['image']) && !isset($input['images'])) || !isset($input['category_id'])) {
    http_response_code(400);
    echo json_encode([
        "message" => "Invalid input data",
        "success" => false
    ]);
    exit();
}

try {
  // sql
  $sql = "INSERT INTO menu (name, price, description, images, category_id) VALUES (:name, :price, :description, :images, :category_id)";
  // prepare
  $stmt = $conn->prepare($sql);

  // normalize inputs
  $name = trim($input['name']);
  // keep price as numeric; database uses integer-like storage so round it
  $price = (int) round(floatval($input['price']));
  $description = trim($input['description']);
  $images = isset($input['image']) ? trim($input['image']) : (is_array($input['images']) ? implode(',', $input['images']) : trim($input['images']));
  // ensure images string isn't too long for the varchar(255)
  $images = substr($images, 0, 255);
  $category_id = intval($input['category_id']);

  // bind parameters correctly
  $stmt->bindParam(':name', $name);
  $stmt->bindParam(':price', $price);
  $stmt->bindParam(':description', $description);
  $stmt->bindParam(':images', $images);
  $stmt->bindParam(':category_id', $category_id);
  // execute
  $stmt->execute();

    $lastId = (int)$conn->lastInsertId();
    // If the request was a standard form submit (no JSON body), redirect back to admin menu with a success flash
    if (empty($json) && !empty($_POST)) {
      $_SESSION['success'] = 'Menu added successfully';
      header('Location: /POS-Inventory/pages/admin/menu-admin.php');
      exit();
    }

    echo json_encode([
        "message" => "Menu added successfully",
        "success" => true,
        "menu_id" => $lastId
    ]);
} catch (PDOException $e) {
  http_response_code(500);
  echo json_encode([
      "message" => "Error: " . $e->getMessage(),
      "success" => false
  ]);
}

?>
