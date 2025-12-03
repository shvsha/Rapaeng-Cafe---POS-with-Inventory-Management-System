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
    $sql ="SELECT * FROM stocks ORDER BY stock_id ASC, item_name ASC";
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
        "message" => "No stock found",
        "status" => false
      ]);
      exit();
    }

    echo json_encode([
      "message" => "Stocks retrieved successfully",
      "status" => true,
      "data" => $books
    ]);

  } catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
      "message" => "Error retrieving stocks: " . $e->getMessage(),
      "status" => false
    ]);
  }

?>
