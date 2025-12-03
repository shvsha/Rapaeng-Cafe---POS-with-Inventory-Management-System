<?php
session_start();
header('Content-Type: application/json; charset=UTF-8');

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
	http_response_code(403);
	echo json_encode(['success'=>false,'message'=>'Forbidden']);
	exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	http_response_code(405);
	echo json_encode(['success'=>false,'message'=>'Method Not Allowed']);
	exit();
}

require_once '../connection.php';

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!$data) $data = $_POST;

$menu_id = isset($data['menu_id']) ? intval($data['menu_id']) : 0;
$name = isset($data['name']) ? trim($data['name']) : '';
$price = isset($data['price']) ? floatval($data['price']) : null;
$description = isset($data['description']) ? trim($data['description']) : '';
$images = isset($data['image']) ? trim($data['image']) : (isset($data['images']) ? (is_array($data['images']) ? implode(',', $data['images']) : trim($data['images'])) : '');
$category_id = isset($data['category_id']) ? intval($data['category_id']) : 0;

if ($menu_id <= 0 || $name === '' || $price === null || $category_id <= 0) {
	http_response_code(400);
	echo json_encode(['success'=>false,'message'=>'Missing required fields']);
	exit();
}

try {
	$sql = 'UPDATE menu SET name = :name, price = :price, description = :description, images = :images, category_id = :cat WHERE menu_id = :id';
	$st = $conn->prepare($sql);
	$pprice = (int) round($price);
	$st->bindParam(':name', $name);
	$st->bindParam(':price', $pprice);
	$st->bindParam(':description', $description);
	$st->bindParam(':images', $images);
	$st->bindParam(':cat', $category_id, PDO::PARAM_INT);
	$st->bindParam(':id', $menu_id, PDO::PARAM_INT);
	$st->execute();

	echo json_encode(['success'=>true,'message'=>'Menu updated']);
} catch (PDOException $e) {
	http_response_code(500);
	echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}

?>
