<?php 
session_start();

// Redirect if already logged in
if (isset($_SESSION['user_type'])) {
    switch($_SESSION['user_type']) {
        case 'admin':
          header("Location: admin/dashboard.php");
          break;
        case 'cashier':
          header("Location: cashier/POS-interface.php");
          break;
        case 'customer':
          header("Location: index.php");
          break;
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Page</title>
  <link rel="stylesheet" href="../css/login.css">
  <!-- Add Font Awesome for eye icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:ital,wght@0,100..700;1,100..700&display=swap" rel="stylesheet">
</head>
<body>
  <section style="display: flex">
    <?php
      // load flash messages into JS-safe variables and clear session keys
      $flash = null;
      if (isset($_SESSION['error'])) { $flash = ['type' => 'error', 'message' => $_SESSION['error']]; unset($_SESSION['error']); }
      if (isset($_SESSION['success'])) { $flash = ['type' => 'success', 'message' => $_SESSION['success']]; unset($_SESSION['success']); }
    ?>
  <!-- login form -->
    <form class="login-form" method="POST" action="../api/login.check.php">
      <div class="login-form-container">
        <div class="form-logo-container">
          <img src="../images/rapaeng-logo.png" alt="Rapaeng Café Logo" class="form-logo">
        </div>
        <p class="title">Login</p>
        <div class="form-inputs">
          <label class="form-label">Username</label> <br>
          <input type="text" name="username" class="form-control" id="username" placeholder="Enter Username" required>
        </div>
        <div class="form-inputs">
          <label class="form-label">Password</label> <br>
          <div style="position: relative;">
            <input type="password" name="password" class="form-control" id="password" placeholder="Enter Password" required>
            <i id="togglePassword" class="fa-regular fa-eye-slash" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #888; font-size: 16px;"></i>
          </div>
        </div>

        <button type="submit" class="login-btn">Login</button>
        <p class="sign-up-link">Already have an account? <a href="customer/sign-up.php">Sign Up</a></p>
      </div>
    </form>
  </section>

  <!-- Modal - used for error/success flash messages -->
  <div id="flashModal" class="flash-modal" aria-hidden="true">
    <div class="flash-overlay" data-close></div>
    <div class="flash-box" role="dialog" aria-modal="true" aria-labelledby="flashTitle">
      <button class="flash-close" aria-label="Close message" data-close>&times;</button>
      <div id="flashTitle" class="flash-title"></div>
      <div id="flashBody" class="flash-body"></div>
    </div>
  </div>

  <script>
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');

    togglePassword.addEventListener('click', function() {
      // Toggle the type attribute
      const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
      passwordInput.setAttribute('type', type);
      
      // Toggle the eye icon (eye-slash when hidden, eye when visible)
      if(type === 'password') {
        this.classList.remove('fa-eye');
        this.classList.add('fa-eye-slash');
      } else {
        this.classList.remove('fa-eye-slash');
        this.classList.add('fa-eye');
      }
    });
  </script>

  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js" integrity="sha384-G/EV+4j2dNv+tEPo3++6LCgdCROaejBqfUeNjuKAiuXbjrxilcCdDz6ZAVfHWe1Y" crossorigin="anonymous"></script>

  <script>
    // show flash modal if server set a message
    (function(){
      try {
        const flash = <?php echo json_encode($flash ?: null); ?>;
        if (!flash) return;

        const modal = document.getElementById('flashModal');
        const overlay = modal.querySelector('.flash-overlay');
        const box = modal.querySelector('.flash-box');
        const title = document.getElementById('flashTitle');
        const body = document.getElementById('flashBody');

        function closeModal() {
          modal.classList.remove('active');
          modal.setAttribute('aria-hidden','true');
        }

        modal.classList.add('active');
        modal.setAttribute('aria-hidden','false');
        title.innerText = (flash.type === 'error' ? 'Error' : 'Success');
        body.innerText = flash.message;
        box.classList.remove('flash-error','flash-success');
        box.classList.add(flash.type === 'error' ? 'flash-error':'flash-success');

        // close handlers
        overlay.addEventListener('click', closeModal);
        modal.querySelectorAll('[data-close]').forEach(el => el.addEventListener('click', closeModal));

        // close on ESC
        document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeModal(); });

        // focus
        box.setAttribute('tabindex','-1');
        box.focus();
      } catch (e) { console.error(e); }
    })();
  </script>
</body>
</html>