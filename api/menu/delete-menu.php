<?php
session_start();
header('Content-Type: application/json; charset=UTF-8');

// only admin may delete
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
if ($menu_id <= 0) {
  http_response_code(400);
  echo json_encode(['success'=>false,'message'=>'Missing menu_id']);
  exit();
}

try {
  $st = $conn->prepare('DELETE FROM menu WHERE menu_id = :id');
  $st->bindParam(':id', $menu_id, PDO::PARAM_INT);
  $st->execute();

  echo json_encode(['success'=>true,'message'=>'Menu deleted']);
} catch (PDOException $e) {
  http_response_code(500);
  echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}

?>
