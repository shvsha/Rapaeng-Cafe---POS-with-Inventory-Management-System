<?php
header("Content-Type: application/json; charset=UTF-8");
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["message" => "Method Not Allowed", "status" => false]);
    exit();
}

require_once '../connection.php';

$body = json_decode(file_get_contents('php://input'), true);
if (!$body || !isset($body['cashier_id'])) {
    http_response_code(400);
    echo json_encode(["message" => "Invalid payload", "status" => false]);
    exit();
}

$id = intval($body['cashier_id']);
if ($id <= 0) {
    http_response_code(400);
    echo json_encode(["message" => "Invalid id", "status" => false]);
    exit();
}

try {
    // verify exists
    $stmt = $conn->prepare("SELECT cashier_id FROM cashier WHERE cashier_id = :id");
    $stmt->execute([':id' => $id]);
    $found = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$found) {
        http_response_code(404);
        echo json_encode(["message" => "User not found", "status" => false]);
        exit();
    }

    // perform delete
    $stmt = $conn->prepare("DELETE FROM cashier WHERE cashier_id = :id");
    $stmt->execute([':id' => $id]);

    echo json_encode(["message" => "User deleted", "status" => true]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["message" => "Database error: " . $e->getMessage(), "status" => false]);
}

?>
