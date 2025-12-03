<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Landing Page</title>
  <link rel="stylesheet" href="css/index.css">  
  <link rel="stylesheet" href="css/nav-bar.css">
  <link rel="stylesheet" href="css/footer.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:ital,wght@0,100..700;1,100..700&display=swap" rel="stylesheet">
  <link rel="icon" type="image/png" href="images/rapaeng-logo.png">

</head>
<body>
  <!-- nav bar -->
   <section class="whole-container">
  <?php
    include('pages/nav.bar.php');
  ?>

    <!-- first main bg pic -->
    <div class="bg-img">
      <img src="images/rapaeng-bg-home.jpg" alt="menu-background-image">
    </div>

    <div class="menu-intro">
      <h1>Rapaeng Café</h1>
      <p>“Where Every Sip Feels Like Home”</p>
      <button onclick="window.location.href='pages/customer/menu.php'">Menu</button>
    </div>

    <div>
      <hr class="line">
    </div>

    <div class="menu-schedule">
      <h1>Monday to Saturday  8am - 3pm</h1>
      <h1>Sunday 9am - 3pm</h1>
      <h1>Brunch served on Saturday & Sunday</h1>
    </div>

    <div class="after-sched-line">
      <hr class="line">
    </div>

    <div  class="image-section">
      <div class="row-images-1">
        <img src="images/home-sample-pics/pic-1.png" alt=""><img src="images/home-sample-pics/pic-2.jpg" alt="">
      </div>
      <div class="row-images-2">
        <img src="images/home-sample-pics/pic-3.jpg" alt=""><img src="images/home-sample-pics/pic-4.jpg" alt=""><img src="images/home-sample-pics/pic-5.jpg" alt="">
      </div>
    </div>

    <div class="after-sched-line">
      <hr class="line">
    </div>
        
    <div class="culture-coffee-food-container">
      <h1>Blending Culture, Coffee & Delicious Food</h1>
      <img src="images/home-sample-pics/pic-6.jpg" alt="">
      <p>Rapaeng is a family owned business blending the cultures of Argentina, Armenia, and Italy. Bringing you a delicious menu in our beautiful gathering space. The Trailhead Compound is a neighborhood snack place to stop by at the heart of the Baca Railyard.</p>
    </div>

    <div class="after-sched-line">
      <hr class="line">
    </div>

  <?php
    include('pages/footer.php');
  ?>



   </section>

  <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>
</html>