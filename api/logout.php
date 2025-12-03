<?php
session_start();

// Store user type before destroying session
$user_type = isset($_SESSION['user_type']) ? $_SESSION['user_type'] : null;

// Destroy all session data
session_unset();
session_destroy();

// Redirect based on previous user type - use paths relative to this file (api/)
switch($user_type) {
    case 'admin':
    case 'cashier':
        header("Location: ../index.php");
        break;
    case 'customer':
    default:
        header("Location: ../index.php");
        break;
}
exit();
?>