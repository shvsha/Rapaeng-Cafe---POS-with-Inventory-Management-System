<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contact Us</title>
  <link rel="stylesheet" href="../../css/customer/contact-us.css">  
  <link rel="stylesheet" href="../../css/nav-bar.css">
  <link rel="stylesheet" href="../../css/footer.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:ital,wght@0,100..700;1,100..700&display=swap" rel="stylesheet">
  <link rel="icon" type="image/png" href="../../images/rapaeng-logo.png">
</head>
<body>
    <!-- nav bar -->
   <section class="whole-container">
  <?php
    include('../nav.bar.php');
  ?>

  <div class="contact-us-content">
    <h1>SAY HEY Y'ALL!</h1>

    <div>
      <h2>Customer Service Hours</h2>
      <p>Monday - Friday: 8:30 AM - 4:00 PM (Central Time)</p>
    </div>

    <div>
      <h2>Website Orders and Coffee</h2>
      <p>Need to reach someone about your online order or subscription? Please fill out our <a href="customer-support.php">Customer Support</a> form.</p>
    </div>

    <div>
      <h2>In-store and Menu</h2>
      <p>Need to talk to someone about your in-store / mobile app experience, franchising, or menu question? Please email us at <a href="">rapaengcafe@trailheadsantafe.com</a>
      </p>
    </div>
  </div>

    <div class="after-sched-line">
      <hr class="line">
    </div>

  <?php
    include('../footer.php');
  ?>
   </section>

  <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>
</html>