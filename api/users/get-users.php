<?php
  header("Content-Type: application/json; charset=UTF-8");
  if($_SERVER['REQUEST_METHOD'] !== 'GET'){
    http_response_code(405);
    echo json_encode([
      "message" => "Method Not Allowed",
      "status" => false
    ]);
    exit();
  }

  require_once "../connection.php";

  try {
    // sql
    $sql ="SELECT * FROM cashier ORDER BY cashier_id ASC, full_name ASC";
    // prepare
    $stmt = $conn->prepare($sql);   
    // execute
    $stmt->execute();
    // fetch all
    $books = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // there's data or none
    if (count($books) === 0) {
      http_response_code(404);
      echo json_encode([
        "message" => "No user found",
        "status" => false
      ]);
      exit();
    }

    echo json_encode([
      "message" => "Users retrieved successfully",
      "status" => true,
      "data" => $books
    ]);

  } catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
      "message" => "Error retrieving users: " . $e->getMessage(),
      "status" => false
    ]);
  }

?>
