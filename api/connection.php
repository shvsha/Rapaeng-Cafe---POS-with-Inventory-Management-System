<?php
    // variables
    $host = "localhost";
    $dbname = "pos-inve_db";
    $username = "root";
    $password = "";

    try {
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode([
            "error"=> "Server Problem"
        ]);
        exit();
    }

?>
