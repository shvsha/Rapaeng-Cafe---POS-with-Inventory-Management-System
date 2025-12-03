<?php 
session_start();

// Redirect if already logged in
if (isset($_SESSION['user_type'])) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // include DB connection from project root (pages/customer -> ../../api/connection.php)
    $connFile = __DIR__ . '/../../api/connection.php';
    if (!file_exists($connFile)) {
        $error = "Server configuration error: missing database connection file.";
    } else {
        include_once $connFile;
    }
    
    try {
        $full_name = trim($_POST['full_name']);
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
        $confirm_password = trim($_POST['confirm_password']);
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $phone_number = isset($_POST['phone_number']) ? trim($_POST['phone_number']) : '';
        $gender = in_array($_POST['gender'] ?? 'other', ['male','female','other']) ? $_POST['gender'] : 'other';
        $date_of_birth = !empty($_POST['date_of_birth']) ? $_POST['date_of_birth'] : '1970-01-01';
        
        // Validation
        if (empty($full_name) || empty($username) || empty($password)) {
            $error = "All fields are required";
        } elseif ($password !== $confirm_password) {
            $error = "Passwords do not match";
        } elseif (strlen($password) < 6) {
            $error = "Password must be at least 6 characters";
        } else {
            // make sure connection exists
            if (!isset($conn) || $conn === null) {
                $error = 'Unable to connect to the database. Please try again later.';
            }

            // if there was a connection error we should stop before trying DB queries
            if (isset($error) && !empty($error)) {
                // skip DB work if connection failed
            } else {

                // checker for existing username
                $stmt = $conn->prepare("SELECT * FROM customers WHERE username = :username");
            $stmt->execute(['username' => $username]);
            
            if ($stmt->rowCount() > 0) {
                $error = "Username already exists";
            } else {
                // add new customer (phone/gender/dob optional - DB defaults exist but we pass them to be explicit)
                $stmt = $conn->prepare("INSERT INTO customers (full_name, username, email, password, phone_number, gender, date_of_birth) VALUES (:full_name, :username, :email, :password, :phone_number, :gender, :dob)");
                $stmt->execute([
                    'full_name' => $full_name,
                    'username' => $username,
                    'email' => $email,
                    'password' => $password,
                    'phone_number' => $phone_number,
                    'gender' => $gender,
                    'dob' => $date_of_birth
                ]);
                
                $_SESSION['success'] = "Account created successfully! Please login.";
                header("Location: ../login.php");
                exit();
                }
            }
        }
    } catch(PDOException $e) {
        $error = "Registration failed: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:ital,wght@0,100..700;1,100..700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../css/sign-up.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <div class="form-logo-container">
              <img src="../../images/rapaeng-logo.png" alt="Rapaeng Café Logo" class="form-logo">
            </div>
            <h2>Create Account</h2>
            
            <?php if (isset($error)): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label for="full_name">Full Name</label>
                    <input type="text" id="full_name" name="full_name" required 
                           value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" 
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>

                                <div class="form-group">
                                        <label for="phone_number">Phone Number (optional)</label>
                                        <input type="text" id="phone_number" name="phone_number" 
                                                     value="<?php echo isset($_POST['phone_number']) ? htmlspecialchars($_POST['phone_number']) : ''; ?>">
                                </div>

                                <div class="form-row">
                                    <div class="form-group" style="flex:1;margin-right:8px;">
                                        <label for="gender">Gender</label>
                                        <select id="gender" name="gender">
                                            <option value="other" <?php echo (isset($_POST['gender']) && $_POST['gender']==='other') ? 'selected' : ''; ?>>Other</option>
                                            <option value="male" <?php echo (isset($_POST['gender']) && $_POST['gender']==='male') ? 'selected' : ''; ?>>Male</option>
                                            <option value="female" <?php echo (isset($_POST['gender']) && $_POST['gender']==='female') ? 'selected' : ''; ?>>Female</option>
                                        </select>
                                    </div>
                                    <div class="form-group" style="flex:1;">
                                        <label for="date_of_birth">Date of Birth</label>
                                        <input type="date" id="date_of_birth" name="date_of_birth" value="<?php echo isset($_POST['date_of_birth']) ? htmlspecialchars($_POST['date_of_birth']) : ''; ?>">
                                    </div>
                                </div>
                
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required 
                           value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                
                <button type="submit" class="login-btn">Sign Up</button>
            </form>
            
            <p class="signup-link">Already have an account? <a href="../login.php">Log In</a></p>
        </div>
    </div>
</body>
</html>