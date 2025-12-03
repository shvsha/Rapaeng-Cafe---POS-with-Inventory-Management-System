<?php
session_start();
include('../../api/connection.php');

// require login
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'customer') {
  header('Location: ../login.php');
  exit();
}

$customer_id = $_SESSION['customer_id'] ?? $_SESSION['user_id'];
$errors = [];
$msg = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $current = trim($_POST['current_password'] ?? '');
  $new = trim($_POST['new_password'] ?? '');
  $confirm = trim($_POST['confirm_password'] ?? '');

  if ($current === '' || $new === '' || $confirm === '') {
    $errors[] = 'Please fill in all fields.';
  } elseif ($new !== $confirm) {
    $errors[] = 'New password and confirmation do not match.';
  } elseif (strlen($new) < 6) {
    $errors[] = 'New password must be at least 6 characters.';
  } else {
    // fetch current stored password
    $stmt = $conn->prepare("SELECT password FROM customers WHERE customer_id = :id LIMIT 1");
    $stmt->execute(['id' => $customer_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row || $row['password'] !== $current) {
      $errors[] = 'Current password is incorrect.';
    } else {
      $upd = $conn->prepare("UPDATE customers SET password = :pw WHERE customer_id = :id");
      $upd->execute(['pw' => $new, 'id' => $customer_id]);
      $msg = 'Password updated successfully.';
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Change Password</title>
  <link rel="stylesheet" href="../../css/nav-bar.css">
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
          <div class="account-title">Set Password</div>
        </div>

        <div class="account-content">
          <?php if (!empty($msg)): ?>
            <div style="color:green;margin-bottom:10px"><?php echo htmlspecialchars($msg); ?></div>
          <?php endif; ?>

          <?php if (!empty($errors)): ?>
            <div style="color:#a94442;margin-bottom:10px">
              <?php foreach ($errors as $e) echo htmlspecialchars($e) . '<br>'; ?>
            </div>
          <?php endif; ?>

          <form method="POST">
            <div class="form-field">
              <label>Current Password</label>
              <input type="password" name="current_password" placeholder="Current password" required>
            </div>
            <div class="form-field">
              <label>New Password</label>
              <input type="password" name="new_password" placeholder="New password" required>
            </div>
            <div class="form-field">
              <label>Confirm Password</label>
              <input type="password" name="confirm_password" placeholder="Confirm password" required>
            </div>
            <div class="form-actions">
              <button class="btn-secondary" type="button" onclick="window.location.href='profile.php'">Cancel</button>
              <button class="btn-primary" type="submit">Confirm</button>
            </div>
          </form>
        </div>
      </section>
    </div>
  </main>

  <?php include('../footer.php'); ?>
</body>
</html>