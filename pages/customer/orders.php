<?php
session_start();
include('../../api/connection.php');

// require customer login
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'customer') {
  header('Location: ../login.php');
  exit();
}

$customer_id = $_SESSION['customer_id'];

// load orders
$orders = [];
// ensure orders move from Delivering to Delivered when ETA has passed
try {
  $conn->exec("UPDATE orders SET status = '3' WHERE status = '2' AND delivery_eta IS NOT NULL AND delivery_eta <= NOW()");
} catch (Exception $e) {
  // ignore if column missing or other error
}

$ordersStmt = $conn->prepare("SELECT * FROM orders WHERE customer_id = :cid ORDER BY create_at DESC, order_id DESC");
$ordersStmt->bindParam(':cid', $customer_id);
$ordersStmt->execute();
$orders = $ordersStmt->fetchAll(PDO::FETCH_ASSOC);

// helper: load items for an order
function load_order_items($conn, $order_id) {
  $itSt = $conn->prepare("SELECT oi.quantity, oi.price, m.name, m.images FROM order_items oi JOIN menu m ON oi.menu_id = m.menu_id WHERE oi.order_id = :oid");
  $itSt->bindParam(':oid', $order_id);
  $itSt->execute();
  return $itSt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>My Orders - Rapaeng Café</title>
  <link rel="stylesheet" href="../../css/nav-bar.css">
  <link rel="stylesheet" href="../../css/customer/account.css">
  <link rel="stylesheet" href="../../css/footer.css">
  <link rel="stylesheet" href="../../css/customer/account.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:ital,wght@0,100..700;1,100..700&display=swap" rel="stylesheet">
</head>
<body>
  <?php include('../nav.bar.php'); ?>
  <div style="padding: 30px;"></div>
  <main class="account-wrapper">
    <div class="account-grid">
      <aside class="account-side">
        <div class="profile-block account-card">
          <div class="profile-avatar"><img src="../../images/rapaeng-logo.png" alt="avatar"></div>
          <div>
            <div class="profile-name">Your Name</div>
            <div style="font-size:13px;color:#666;">Edit Profile</div>
          </div>
        </div>

        <nav class="side-nav">
          <ul>
            <?php $self = basename($_SERVER['PHP_SELF']); ?>
            <li class="<?php echo $self === 'profile.php' ? 'active' : ''; ?>"><a href="profile.php"><span class="side-icon">🏠</span> Profile</a></li>
            <li class="<?php echo $self === 'addresses.php' ? 'active' : ''; ?>"><a href="addresses.php"><span class="side-icon">📍</span> Addresses</a></li>
            <li class="<?php echo $self === 'change-password.php' ? 'active' : ''; ?>"><a href="change-password.php"><span class="side-icon">🔑</span> Change Password</a></li>
            <li class="<?php echo $self === 'notifications.php' ? 'active' : ''; ?>"><a href="notifications.php"><span class="side-icon">🔔</span> Notification Settings</a></li>
            <li class="<?php echo $self === 'orders.php' ? 'active' : ''; ?>"><a href="orders.php"><span class="side-icon">🛒</span> My Orders</a></li>
          </ul>
        </nav>
      </aside>

      <section class="account-main">
        <div class="top-controls">
          <div class="account-title">My Purchase</div>
        </div>

        <div class="account-content">
          <?php if (empty($orders)): ?>
            <div class="section-empty">
              <img src="../../images/home-sample-pics/pic-6.jpg" alt="empty" />
              <p>You have no orders yet</p>
            </div>
          <?php else: ?>
            <div class="orders-list">
              <?php foreach ($orders as $o): 
                $items = load_order_items($conn, $o['order_id']);
                $totalDisplay = number_format($o['total_amount'], 2);
                $date = htmlspecialchars($o['create_at']);
                switch (strval($o['status'])) {
                  case '1': $statusText = 'Queued'; break;
                  case '2': $statusText = 'Delivering'; break;
                  case '3': $statusText = 'Delivered'; break;
                  default: $statusText = 'Processed'; break;
                }
              ?>
                <div class="order-card">
                  <div class="order-head">
                    <div><strong>Order #<?php echo $o['order_id']; ?></strong></div>
                    <div><?php echo $date; ?></div>
                    <div class="order-status"><?php echo $statusText; ?></div>
                  </div>

                  <div class="order-items">
                    <?php foreach ($items as $it): ?>
                      <div class="order-item-row">
                        <div class="oi-left">
                          <?php if ($it['images']): ?>
                            <img src="../../images/menu-pics/<?php echo htmlspecialchars($it['images']); ?>" alt="<?php echo htmlspecialchars($it['name']); ?>">
                          <?php else: ?>
                            <div class="no-img">☕</div>
                          <?php endif; ?>
                          <div class="oi-meta">
                            <div class="oi-name"><?php echo htmlspecialchars($it['name']); ?></div>
                            <div class="oi-qty">Qty: <?php echo intval($it['quantity']); ?></div>
                          </div>
                        </div>
                        <div class="oi-price">₱<?php echo number_format($it['price'] * $it['quantity'], 2); ?></div>
                      </div>
                    <?php endforeach; ?>
                  </div>

                  <div class="order-footer">
                    <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;flex-wrap:wrap;">
                      <div class="order-actions">
                        <button onclick="window.location.href='contact-us.php'" class="btn-outline">Contact Seller</button>
                        <button onclick="window.location.href='menu.php'" class="btn-order">Buy Again</button>
                      </div>
                      <div style="font-size:18px;font-weight:800;color:#7A4E2D;">Order Total: ₱<?php echo $totalDisplay; ?></div>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>

      </section>
    </div>
  </main>
  <?php include('../footer.php'); ?>
</body>
</html>