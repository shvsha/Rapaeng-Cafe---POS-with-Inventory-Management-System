<?php
header("Content-Type: application/json; charset=UTF-8");
if ($_SERVER['REQUEST_METHOD'] !== 'GET'){
  http_response_code(405);
  echo json_encode(['status'=>false,'message'=>'Method Not Allowed']);
  exit();
}

require_once '../connection.php';

// params: mode=month|year, month=1..12, year=YYYY
$mode = isset($_GET['mode']) ? strtolower($_GET['mode']) : 'month';
$month = isset($_GET['month']) ? intval($_GET['month']) : intval(date('n'));
$year = isset($_GET['year']) ? intval($_GET['year']) : intval(date('Y'));

try {
  // base where clause
  if ($mode === 'year'){
    $where = "WHERE YEAR(o.create_at) = :year";
  } else {
    $where = "WHERE MONTH(o.create_at) = :month AND YEAR(o.create_at) = :year";
  }

  // per-item aggregation
  $sql = "SELECT m.menu_id, m.name, c.name AS category_name, COALESCE(SUM(oi.quantity),0) AS quantity, COALESCE(AVG(oi.price),0) AS unit_price, COALESCE(SUM(oi.quantity*oi.price),0) AS total_sales
          FROM order_items oi
          JOIN orders o ON oi.order_id = o.order_id
          JOIN menu m ON oi.menu_id = m.menu_id
          LEFT JOIN category c ON m.category_id = c.category_id
          $where
          GROUP BY oi.menu_id
          ORDER BY total_sales DESC";

  $stmt = $conn->prepare($sql);
  if ($mode === 'year') $stmt->execute([':year'=>$year]); else $stmt->execute([':month'=>$month,':year'=>$year]);
  $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

  // summary: total items sold, total sales
  $sql2 = "SELECT COALESCE(SUM(oi.quantity),0) AS total_items, COALESCE(SUM(oi.quantity*oi.price),0) AS total_sales FROM order_items oi JOIN orders o ON oi.order_id=o.order_id $where";
  $s = $conn->prepare($sql2);
  if ($mode === 'year') $s->execute([':year'=>$year]); else $s->execute([':month'=>$month, ':year'=>$year]);
  $summary = $s->fetch(PDO::FETCH_ASSOC);

  // best-selling overall
  $best = null;
  if (count($items)) {
    $best = $items[0];
  }

  // best-selling drink (category 'Coffee' or 'Non Coffee' -> category names containing 'Coffee')
  $drinkStmt = $conn->prepare("SELECT m.menu_id, m.name, c.name AS category_name, COALESCE(SUM(oi.quantity),0) AS qty FROM order_items oi JOIN orders o ON oi.order_id=o.order_id JOIN menu m ON oi.menu_id=m.menu_id LEFT JOIN category c ON m.category_id=c.category_id $where AND (c.name LIKE '%Coffee%') GROUP BY oi.menu_id ORDER BY qty DESC LIMIT 1");
  if ($mode==='year') $drinkStmt->execute([':year'=>$year]); else $drinkStmt->execute([':month'=>$month,':year'=>$year]);
  $bestDrink = $drinkStmt->fetch(PDO::FETCH_ASSOC) ?: null;

  // best-selling food (categories not Coffee)
  $foodStmt = $conn->prepare("SELECT m.menu_id, m.name, c.name AS category_name, COALESCE(SUM(oi.quantity),0) AS qty FROM order_items oi JOIN orders o ON oi.order_id=o.order_id JOIN menu m ON oi.menu_id=m.menu_id LEFT JOIN category c ON m.category_id=c.category_id $where AND (c.name NOT LIKE '%Coffee%') GROUP BY oi.menu_id ORDER BY qty DESC LIMIT 1");
  if ($mode==='year') $foodStmt->execute([':year'=>$year]); else $foodStmt->execute([':month'=>$month,':year'=>$year]);
  $bestFood = $foodStmt->fetch(PDO::FETCH_ASSOC) ?: null;

  echo json_encode(['status'=>true,'data'=>['items'=>$items,'summary'=>$summary,'best_overall'=>$best,'best_drink'=>$bestDrink,'best_food'=>$bestFood]]);

} catch (PDOException $e){
  http_response_code(500);
  echo json_encode(['status'=>false,'message'=>'DB error: '.$e->getMessage()]);
}

?>
