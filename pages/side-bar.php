<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Define base URL for navigation
$base_url = '/POS-Inventory/pages/cashier/';
?>

<div class="whole-side-bar">
    <div class="side-bar-logo">
        <img src="/POS-Inventory/images/rapaeng-logo.png" alt="">
    </div>

    <nav class="side-bar">
        <ul>
            <li><a href="<?php echo $base_url; ?>POS-interface.php">Main Ordering</a></li>
            <li><a href="<?php echo $base_url; ?>order-queue.php">Order Queue</a></li>
        </ul>
    </nav>
</div>