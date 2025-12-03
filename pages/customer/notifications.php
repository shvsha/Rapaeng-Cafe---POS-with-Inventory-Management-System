<?php
session_start();
include('../../api/connection.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Notification Settings</title>
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
          <div class="account-title">Notification Settings</div>
        </div>

        <div class="account-content">
          <div class="section-empty">
            <img src="../../images/home-sample-pics/pic-5.jpg" alt="empty" />
            <p>No order updates yet</p>
          </div>
        </div>

      </section>
    </div>
  </main>
  <?php include('../footer.php'); ?>
</body>
</html>