<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Define base URL for navigation
$base_url = '/POS-Inventory/pages/admin/';
?>

<div class="whole-side-bar">
    <div class="side-bar-logo">
        <img src="/POS-Inventory/images/rapaeng-logo.png" alt="">
    </div>

    <nav class="side-bar">
        <ul>
            <li><a href="<?php echo $base_url; ?>dashboard.php">Dashboard</a></li>
            <li><a href="<?php echo $base_url; ?>menu-admin.php">Menu</a></li>
            <li><a href="<?php echo $base_url; ?>stocks.php">Stocks</a></li>
            <li><a href="<?php echo $base_url; ?>sale-reports.php">Sales Report</a></li>
            <li><a href="<?php echo $base_url; ?>user-management.php">Users</a></li>
        </ul>
    </nav>
    
    <div class="side-bar-logout">
        <!-- send request to central logout endpoint; confirm for safety -->
        <form method="GET" action="/POS-Inventory/api/logout.php" onsubmit="return confirm('Are you sure you want to logout?');">
            <button type="submit" class="logout-button" aria-label="Logout">Logout</button>
        </form>
    </div>

</div>