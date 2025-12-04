<?php
header("Content-Type: application/json; charset=UTF-8");
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["message" => "Method Not Allowed", "status" => false]);
    exit();
}

require_once '../connection.php';

$body = json_decode(file_get_contents('php://input'), true);
if (!$body) {
    http_response_code(400);
    echo json_encode(["message" => "Invalid payload", "status" => false]);
    exit();
}

$id = isset($body['cashier_id']) ? intval($body['cashier_id']) : 0;
$full_name = isset($body['full_name']) ? trim($body['full_name']) : '';
$username = isset($body['username']) ? trim($body['username']) : '';
$password = isset($body['password']) ? $body['password'] : null; // null => not supplied

if ($id <= 0 || $full_name === '' || $username === '') {
    http_response_code(400);
    echo json_encode(["message" => "Missing required fields", "status" => false]);
    exit();
}

try {
    // check if user exists
    $stmt = $conn->prepare("SELECT cashier_id FROM cashier WHERE cashier_id = :id");
    $stmt->execute([':id' => $id]);
    $found = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$found) {
        http_response_code(404);
        echo json_encode(["message" => "User not found", "status" => false]);
        exit();
    }

    // build dynamic sql — only update password if provided and non-empty
    if ($password !== null && strlen(trim($password)) > 0) {
        $sql = "UPDATE cashier SET full_name = :full_name, username = :username, password = :password WHERE cashier_id = :id";
        $params = [':full_name' => $full_name, ':username' => $username, ':password' => $password, ':id' => $id];
    } else {
        $sql = "UPDATE cashier SET full_name = :full_name, username = :username WHERE cashier_id = :id";
        $params = [':full_name' => $full_name, ':username' => $username, ':id' => $id];
    }

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);

    echo json_encode(["message" => "User updated", "status" => true]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["message" => "Database error: " . $e->getMessage(), "status" => false]);
}

?>
