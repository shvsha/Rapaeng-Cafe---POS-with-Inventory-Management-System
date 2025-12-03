<?php
header('Content-Type: application/json');
include_once __DIR__ . '/../connection.php';

$data = json_decode(file_get_contents('php://input'), true);
if (!$data || !isset($data['id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'missing id']);
    exit();
}

$id = intval($data['id']);
try {
    $st = $conn->prepare('DELETE FROM kitchen_orders WHERE id = :id');
    $st->bindParam(':id', $id);
    $st->execute();
    echo json_encode(['success'=>true, 'id'=>$id]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}

?>
