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
  <form class="contact-support-content">
    <h1 style="text-align:center;">Customer Support</h1>

    <div class="input-section">
      <label for="email">Email:</label>
      <input type="email" id="email" name="email" placeholder="Your Email Address" required>
    </div>

    <div class="input-section">
      <label for="concern">Concern: </label>
      <textarea id="concern" name="concern" rows="10" cols="50" placeholder="Describe your concern here..." required></textarea>
    </div>

    <div class="submit-button-section">
      <button type="submit">Submit</button>
    </div>
  </form>

  <?php
    include('../footer.php');
  ?>
   </section>

  <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

  <script>
    // Handle form submission
    document.querySelector('.contact-support-content').addEventListener('submit', function(event) {
      event.preventDefault(); 

      const email = document.getElementById('email').value;
      const concern = document.getElementById('concern').value;

      console.log('Concern:', concern);

      alert('Thank you for reaching out! We will get back to you shortly.');

      this.reset();
    });
  </script>
</body>
</html>