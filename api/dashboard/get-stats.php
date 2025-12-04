<?php
header("Content-Type: application/json; charset=UTF-8");
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
  http_response_code(405);
  echo json_encode(["message"=>"Method Not Allowed", "status"=>false]);
  exit();
}

require_once '../connection.php';

try {
  // total menu items
  $stmt = $conn->prepare("SELECT COUNT(*) as total_products FROM menu");
  $stmt->execute();
  $totalProducts = (int) $stmt->fetch(PDO::FETCH_ASSOC)['total_products'];

  // stocks summary - total available (stock_avai), total stocks (total_stocks)
  // If stocks table uses different column names, fallbacks will return 0
  $stmt = $conn->prepare("SELECT COALESCE(SUM(stock_avai),0) AS total_available, COALESCE(SUM(total_stocks),0) AS total_stocks, COUNT(*) AS stock_items FROM stocks");
  $stmt->execute();
  $stocks = $stmt->fetch(PDO::FETCH_ASSOC);

  // stocks ordered today - sum of quantities in stock_order_items where stock_orders.created_at = today
  $stmt = $conn->prepare("SELECT COALESCE(SUM(soi.quantity),0) AS ordered_today FROM stock_order_items soi JOIN stock_orders so ON soi.order_id = so.order_id WHERE DATE(so.created_at) = CURDATE()");
  $stmt->execute();
  $orderedToday = (int)$stmt->fetch(PDO::FETCH_ASSOC)['ordered_today'];

  // out of stocks - count where available <= 0
  $stmt = $conn->prepare("SELECT COUNT(*) AS out_of_stock FROM stocks WHERE COALESCE(stock_avai,0) <= 0");
  $stmt->execute();
  $outOfStock = (int)$stmt->fetch(PDO::FETCH_ASSOC)['out_of_stock'];

  // highest sale product - most ordered menu (sum quantity from order_items)
  $stmt = $conn->prepare("SELECT m.menu_id, m.name, COALESCE(SUM(oi.quantity),0) AS total_qty FROM order_items oi JOIN menu m ON oi.menu_id = m.menu_id GROUP BY oi.menu_id ORDER BY total_qty DESC LIMIT 1");
  $stmt->execute();
  $top = $stmt->fetch(PDO::FETCH_ASSOC);
  if (!$top) $top = null;

  // low stocks - items with available <= 20
  $stmt = $conn->prepare("SELECT COUNT(*) AS low_stocks FROM stocks WHERE COALESCE(stock_avai,0) <= 20");
  $stmt->execute();
  $lowStocks = (int)$stmt->fetch(PDO::FETCH_ASSOC)['low_stocks'];

  echo json_encode([
    'status' => true,
    'data' => [
      'total_products' => $totalProducts,
      'total_stocks' => (int)($stocks['total_stocks'] ?? 0),
      'total_available' => (int)($stocks['total_available'] ?? 0),
      'stocks_ordered_today' => $orderedToday,
      'out_of_stock' => $outOfStock,
      'highest_sale' => $top,
      'low_stocks_count' => $lowStocks
    ]
  ]);

} catch (PDOException $e) {
  http_response_code(500);
  echo json_encode(['status'=>false, 'message'=>$e->getMessage()]);
}

?>
