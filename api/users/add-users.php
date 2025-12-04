<?php
header("Content-Type: application/json");
session_start();

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

$raw = file_get_contents('php://input');
$json = json_decode($raw, true);
if ($json && is_array($json)) {
  $input = $json;
} else {
  $input = $_POST;
}

if (!isset($input['full_name']) || !isset($input['username']) || !isset($input['password']) ) {
    http_response_code(400);
    echo json_encode([
        "message" => "Invalid input data",
        "success" => false
    ]);
    exit();
}

try {
  // normalize inputs
  $full_name = trim($input['full_name']);
  $username = trim($input['username']);
  $password = (string)$input['password'];

  if ($full_name === '' || $username === '' || $password === ''){
    http_response_code(400);
    echo json_encode(["message"=>"Missing required fields","success"=>false]);
    exit();
  }

  // check username uniqueness
  $check = $conn->prepare("SELECT cashier_id FROM cashier WHERE username = :u LIMIT 1");
  $check->execute([':u' => $username]);
  if ($check->fetch(PDO::FETCH_ASSOC)) {
    http_response_code(409);
    echo json_encode(["message"=>"Username already exists","success"=>false]);
    exit();
  }

  // insert into table
  $sql = "INSERT INTO cashier (full_name, username, password) VALUES (:full_name, :username, :password)";
  $stmt = $conn->prepare($sql);
  $stmt->execute([':full_name'=>$full_name, ':username'=>$username, ':password'=>$password]);

  $lastId = (int)$conn->lastInsertId();
  echo json_encode(["message"=>"User added successfully","success"=>true, "cashier_id"=>$lastId]);
  exit();


} catch (PDOException $e) {
  http_response_code(500);
  echo json_encode([
      "message" => "Error: " . $e->getMessage(),
      "success" => false
  ]);
}

?>
