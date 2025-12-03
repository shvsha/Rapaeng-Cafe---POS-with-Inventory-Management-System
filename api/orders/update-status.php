<?php
header('Content-Type: application/json');
include_once __DIR__ . '/../connection.php';

$data = json_decode(file_get_contents('php://input'), true);
if (!$data || !isset($data['id']) || !isset($data['status'])) {
    http_response_code(400);
    echo json_encode(['success'=>false,'message'=>'missing id or status']);
    exit();
}

$id = intval($data['id']);
$status = substr(trim($data['status']), 0, 1);

try {
    $st = $conn->prepare('UPDATE kitchen_orders SET status = :status WHERE id = :id');
    $st->bindParam(':status', $status);
    $st->bindParam(':id', $id);
    $st->execute();
        // If status is now 3 (Ready), find the associated customer order and update it to "Delivering"
        if ($status === '3') {
            try {
                // Get the order_ref from kitchen_orders
                $getOrderRef = $conn->prepare('SELECT order_ref FROM kitchen_orders WHERE id = :id');
                $getOrderRef->bindParam(':id', $id);
                $getOrderRef->execute();
                $row = $getOrderRef->fetch(PDO::FETCH_ASSOC);

                if ($row && $row['order_ref']) {
                    // Extract order ID from reference (ORD-123 -> 123)
                    $orderRef = $row['order_ref'];
                    $orderId = intval(str_replace('ORD-', '', $orderRef));

                    // Ensure orders table has delivery_eta column (best-effort)
                    try {
                        $conn->exec("ALTER TABLE orders ADD COLUMN delivery_eta DATETIME NULL");
                    } catch (Exception $e) {
                        // ignore if column exists or cannot be added
                    }

                    // Update customer order status to 2 (Delivering) and set ETA to 5 minutes from now
                    $updateOrder = $conn->prepare('UPDATE orders SET status = :status, delivery_eta = DATE_ADD(NOW(), INTERVAL 5 MINUTE) WHERE order_id = :order_id');
                    $deliveryStatus = '2';
                    $updateOrder->bindParam(':status', $deliveryStatus);
                    $updateOrder->bindParam(':order_id', $orderId);
                    $updateOrder->execute();
                }
            } catch (PDOException $e) {
                // Log error but don't fail the status update
                error_log('Error updating customer order: ' . $e->getMessage());
            }
        }

        echo json_encode(['success'=>true,'id'=>$id,'status'=>$status]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}
?>
