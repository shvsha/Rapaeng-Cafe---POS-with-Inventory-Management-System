<?php
session_start();

include('connection.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    if (empty($username) || empty($password)) {
        $_SESSION['error'] = "Please fill in all fields";
        header("Location: ../pages/login.php");
        exit();
    }
    
    // Check in admin table
    $stmt = $conn->prepare("SELECT * FROM admin WHERE username = :username LIMIT 1");
    $stmt->execute(['username' => $username]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($admin && $admin['password'] === $password) {
        $_SESSION['user_id'] = $admin['admin_id'];
        $_SESSION['username'] = $admin['username'];
        $_SESSION['full_name'] = $admin['full_name'];
        $_SESSION['user_type'] = 'admin';
        // canonical admin landing is the dashboard; keep both server and client aligned
        header("Location: ../pages/admin/dashboard.php");
        exit();
    }
    
    // Check in cashier table
    $stmt = $conn->prepare("SELECT * FROM cashier WHERE username = :username LIMIT 1");
    $stmt->execute(['username' => $username]);
    $cashier = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($cashier && $cashier['password'] === $password) {
        $_SESSION['user_id'] = $cashier['cashier_id'];
        $_SESSION['username'] = $cashier['username'];
        $_SESSION['full_name'] = $cashier['full_name'];
        $_SESSION['user_type'] = 'cashier';
        // canonical cashier POS page in this project is pages/cashier/POS-interface.php
        header("Location: ../pages/cashier/POS-interface.php");
        exit();
    }
    
    // Check in customers table
    $stmt = $conn->prepare("SELECT * FROM customers WHERE username = :username LIMIT 1");
    $stmt->execute(['username' => $username]);
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($customer && $customer['password'] === $password) {
        $_SESSION['user_id'] = $customer['customer_id'];
        $_SESSION['customer_id'] = $customer['customer_id'];
        $_SESSION['username'] = $customer['username'];
        $_SESSION['full_name'] = $customer['full_name'];
        $_SESSION['user_type'] = 'customer';
        header("Location: ../index.php");
        exit();
    }
    
    // If no match found
    $_SESSION['error'] = "Invalid username or password";
    header("Location: ../pages/login.php");
    exit();
}
?>