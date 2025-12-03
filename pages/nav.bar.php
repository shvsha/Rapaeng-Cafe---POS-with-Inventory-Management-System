<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Define base URL for navigation
$base_url = '/POS-Inventory/';
?>

<div class="whole-nav-bar">
    <div class="nav-top-right">
        <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'customer'): ?>
            <button onclick="window.location.href='<?php echo $base_url; ?>pages/customer/notifications.php'" type="button" class="notification">
                <ion-icon name="notifications-outline" class="notif-icon"></ion-icon>
                <p>Notifications</p>
            </button>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'customer'): ?>
            <!-- logged in customer -->
            <div class="user-profile-dropdown">
                <button type="button" class="user-profile-btn">
                    <ion-icon name="person-circle-outline"></ion-icon>
                    <p><?php echo htmlspecialchars($_SESSION['full_name']); ?></p>
                    <ion-icon name="chevron-down-outline" class="dropdown-arrow"></ion-icon>
                </button>
                <div class="profile-dropdown-content">
                    <a href="<?php echo $base_url; ?>pages/customer/profile.php">
                        <ion-icon name="person-outline"></ion-icon> My Profile
                    </a>
                    <a href="<?php echo $base_url; ?>pages/customer/orders.php">
                        <ion-icon name="receipt-outline"></ion-icon> My Orders
                    </a>
                    <a href="<?php echo $base_url; ?>api/logout.php">
                        <ion-icon name="log-out-outline"></ion-icon> Logout
                    </a>
                </div>
            </div>
        <?php else: ?>
            <!-- Not logged in -->
            <a href="<?php echo $base_url; ?>pages/customer/sign-up.php" class="auth-btn signup-btn">Sign Up</a>
            <a href="<?php echo $base_url; ?>pages/login.php" class="auth-btn login-btn">Log In</a>
        <?php endif; ?>
    </div>

    <nav class="navbar">
        <div class="nav-logo">
            <img src="/POS-Inventory/images/rapaeng-logo.png" alt="Rapaeng Logo" class="rapaeng-logo">
            <p>Rapaeng</p>
        </div>
        
        <ul class="nav-links">
            <li><a href="<?php echo $base_url; ?>index.php">Home</a></li>
            <li><a href="<?php echo $base_url; ?>pages/customer/menu.php">Menu</a></li>
            <li><a href="<?php echo $base_url; ?>pages/customer/contact-us.php">Contact Us</a></li>
        </ul>
        
        <button type="button" onclick="window.location.href='<?php echo $base_url; ?>pages/customer/cart.php'">
            <ion-icon name="cart-outline"></ion-icon>
        </button>
    </nav>
</div>

<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
