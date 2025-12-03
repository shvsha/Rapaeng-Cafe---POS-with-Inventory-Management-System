<?php
session_start();
include('../../api/connection.php');

// Ensure customers table has last_username_change column (best-effort)
try {
  $conn->exec("ALTER TABLE customers ADD COLUMN last_username_change DATETIME NULL");
} catch (Exception $e) {
  // ignore if column already exists or cannot be added
}
// require customer login
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'customer') {
  header('Location: ../login.php');
  exit();
}

$customer_id = $_SESSION['customer_id'] ?? $_SESSION['user_id'];

// fetch current user record
$stmt = $conn->prepare("SELECT * FROM customers WHERE customer_id = :id LIMIT 1");
$stmt->execute(['id' => $customer_id]);
$customer = $stmt->fetch(PDO::FETCH_ASSOC);

$full_name = $customer['full_name'] ?? '';
$username = $customer['username'] ?? '';
$email = $customer['email'] ?? '';
$phone = $customer['phone_number'] ?? '';
$gender = $customer['gender'] ?? 'other';
$dob = $customer['date_of_birth'] ?? '1970-01-01';

// Check if username can be changed (once every 7 days)
$last_username_change = $customer['last_username_change'] ?? null;
$can_change_username = true;
$username_change_message = '';

if ($last_username_change) {
  $lastChange = new DateTime($last_username_change);
  $now = new DateTime();
  $interval = $now->diff($lastChange);
  $daysSinceChange = $interval->days;
  
  if ($daysSinceChange < 7) {
    $can_change_username = false;
    $daysRemaining = 7 - $daysSinceChange;
    $username_change_message = "You can change your username in $daysRemaining day(s).";
  }
}

$error = '';
$saved = false;

// Handle save
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $new_name = trim($_POST['full_name'] ?? '');
  $new_email = trim($_POST['email'] ?? '');
  $new_phone = trim($_POST['phone_number'] ?? '');
  $new_gender = in_array($_POST['gender'] ?? 'other', ['male','female','other']) ? $_POST['gender'] : 'other';
  $new_dob = !empty($_POST['date_of_birth']) ? $_POST['date_of_birth'] : '1970-01-01';
  $new_username = trim($_POST['username'] ?? '');

  // Validate
  if (empty($new_name)) {
    $error = 'Full name is required';
  } elseif (empty($new_email)) {
    $error = 'Email is required';
  } elseif (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
    $error = 'Invalid email format';
  } elseif ($new_username !== $username) {
    // Username changed - check if allowed
    if (!$can_change_username) {
      $error = $username_change_message;
    } else {
      // Check if new username already exists
      $check = $conn->prepare("SELECT customer_id FROM customers WHERE username = :username AND customer_id != :id");
      $check->execute(['username' => $new_username, 'id' => $customer_id]);
      if ($check->rowCount() > 0) {
        $error = 'Username already taken';
      }
    }
  }

  if (!$error) {
    // Prepare update query
    $updateData = [
      'full_name' => $new_name,
      'email' => $new_email,
      'phone' => $new_phone,
      'gender' => $new_gender,
      'dob' => $new_dob,
      'id' => $customer_id
    ];

    $updateQuery = "UPDATE customers SET full_name = :full_name, email = :email, phone_number = :phone, gender = :gender, date_of_birth = :dob";
    
    // Only update username and timestamp if username changed
    if ($new_username !== $username) {
      $updateQuery .= ", username = :username, last_username_change = NOW()";
      $updateData['username'] = $new_username;
    }

    $updateQuery .= " WHERE customer_id = :id";

    $up = $conn->prepare($updateQuery);
    $up->execute($updateData);

    // Refresh local variables
    $full_name = $new_name;
    $email = $new_email;
    $phone = $new_phone;
    $gender = $new_gender;
    $dob = $new_dob;
    if ($new_username !== $username) {
      $username = $new_username;
      $last_username_change = date('Y-m-d H:i:s');
      $can_change_username = false;
    }

    // Update session
    $_SESSION['full_name'] = $full_name;
    $saved = true;
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>My Profile</title>
  <link rel="stylesheet" href="../../css/nav-bar.css">
  <link rel="stylesheet" href="../../css/footer.css">
  <link rel="stylesheet" href="../../css/customer/account.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:ital,wght@0,100..700;1,100..700&display=swap" rel="stylesheet">
  <script src="../../js/notification.js"></script>
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
            <div class="profile-name"><?php echo htmlspecialchars($full_name); ?></div>
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
            <li><a href="#"><span class="side-icon">🏷️</span> My Vouchers</a></li>
          </ul>
        </nav>
      </aside>

      <section class="account-main">
        <div class="top-controls">
          <div class="account-title">My Profile</div>
        </div>

        <div class="account-content">
          <form method="POST">
            <div class="form-row">
              <div style="flex:1;">
                <div class="form-field">
                  <label>Username</label>
                  <?php if ($can_change_username): ?>
                    <input type="text" name="username" value="<?php echo htmlspecialchars($username); ?>">
                  <?php else: ?>
                    <input type="text" value="<?php echo htmlspecialchars($username); ?>" readonly>
                    <small style="color:#666;display:block;margin-top:4px;"><?php echo htmlspecialchars($username_change_message); ?></small>
                  <?php endif; ?>
                </div>

                <div class="form-field"><label>Name</label><input type="text" name="full_name" value="<?php echo htmlspecialchars($full_name); ?>"></div>
                <div class="form-field"><label>Email</label><input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>"></div>
                <div class="form-field"><label>Phone Number</label><input type="text" name="phone_number" value="<?php echo htmlspecialchars($phone); ?>"></div>

                <div class="form-row" style="margin-top:8px;">
                  <div style="flex:1;margin-right:8px;">
                    <div class="form-field"><label>Gender</label>
                      <select name="gender">
                        <option value="other" <?php echo $gender === 'other' ? 'selected' : ''; ?>>Other</option>
                        <option value="male" <?php echo $gender === 'male' ? 'selected' : ''; ?>>Male</option>
                        <option value="female" <?php echo $gender === 'female' ? 'selected' : ''; ?>>Female</option>
                      </select>
                    </div>
                  </div>
                  <div style="width:220px;">
                    <div class="form-field"><label>Date of Birth</label><input type="date" name="date_of_birth" value="<?php echo htmlspecialchars($dob); ?>"></div>
                  </div>
                </div>
              </div>
              <div style="width:220px; text-align:center;">
                <div class="profile-avatar" style="width:120px;height:120px;margin:0 auto;"><img src="../../images/rapaeng-logo.png" alt="avatar"></div>
              </div>
            </div>

            <?php if (!empty($error)): ?>
              <div style="margin-bottom:12px;color:#c23a3a;padding:10px;background:#f8d7da;border-radius:4px;border:1px solid #f5c6cb;"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <?php if (isset($saved) && $saved): ?>
              <div style="margin-bottom:12px;color:#2f8a4a;padding:10px;background:#d4edda;border-radius:4px;border:1px solid #c3e6cb;">Profile updated successfully.</div>
            <?php endif; ?>

            <div class="form-actions">
              <button class="btn-primary" type="submit">Update Profile</button>
            </div>
          </form>
        </div>
      </section>
    </div>
  </main>

  <?php include('../footer.php'); ?>
</body>
</html>