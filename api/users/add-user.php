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

// accept simple payload { full_name, username, password }
$full_name = isset($body['full_name']) ? trim($body['full_name']) : '';
$username = isset($body['username']) ? trim($body['username']) : '';
$password = isset($body['password']) ? $body['password'] : '';

if ($full_name === '' || $username === '' || $password === '') {
    http_response_code(400);
    echo json_encode(["message" => "Missing required fields", "status" => false]);
    exit();
}

try {
    // simple uniqueness check for username
    $stmt = $conn->prepare("SELECT cashier_id FROM cashier WHERE username = :u");
    $stmt->execute([':u' => $username]);
    if ($stmt->fetch(PDO::FETCH_ASSOC)) {
        http_response_code(409);
        echo json_encode(["message" => "Username already exists", "status" => false]);
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO cashier (full_name, username, password) VALUES (:full, :user, :pwd)");
    $stmt->execute([':full' => $full_name, ':user' => $username, ':pwd' => $password]);

    echo json_encode(["message" => "User created", "status" => true]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["message" => "Database error: " . $e->getMessage(), "status" => false]);
}

?>
