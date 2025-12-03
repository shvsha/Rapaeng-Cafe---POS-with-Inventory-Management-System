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
    $sql ="SELECT * FROM menu ORDER BY category_id ASC, name ASC";
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
        "message" => "No menu found",
        "status" => false
      ]);
      exit();
    }

    echo json_encode([
      "message" => "Menus retrieved successfully",
      "status" => true,
      "data" => $books
    ]);

  } catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
      "message" => "Error retrieving menus: " . $e->getMessage(),
      "status" => false
    ]);
  }

?>
