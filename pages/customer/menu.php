<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Menu</title>
  <link rel="stylesheet" href="../../css/customer/menu.css">
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

    <div class="menu-page-intro">
      <h1>Experience Quality and Community at Rapaeng Café</h1>
      <p>Our offerings reflect our commitment to quality, community, and intention. </p>
      <p>Breakfast Service: 8A → 11A</p>
      <p>Lunch Service: 11A → 3P</p>
      <p>Brunch Service: Open → 3P (SATURDAY & SUNDAY)</p>
    </div>

    <div>
      <hr class="line">
    </div>

    <div class="menu-image-section">
      <img class="first-pic" src="../../images/menu-pics/pic-1.jpeg" alt="">
    </div>

    <div>
      <hr class="line">
    </div>

  <div class="menus-section">
    <h1 class="menus-title">MENUS</h1>
    
    <?php
      // Database connection
      include('../../api/connection.php');
      
      // Query to get all menu items with category name
      $query = "SELECT m.menu_id, m.name, m.price, m.description, m.images, c.name as category_name, m.category_id
                FROM menu m
                INNER JOIN category c ON m.category_id = c.category_id
                ORDER BY m.category_id, m.menu_id";
      
      $result = $conn->query($query);
      
      // Group items by category
      $categories = array();
      while($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $categoryName = $row['category_name'];
        
        if(!isset($categories[$categoryName])) {
          $categories[$categoryName] = array();
        }
        $categories[$categoryName][] = $row;
      }
      
      // Display each category
      foreach($categories as $categoryName => $items) {
    ?>
    
    <div class="menu-category">
      <h2 class="category-title"><?php echo strtoupper($categoryName); ?></h2>

      <div class="menu-items">
        <?php
          // Split items into 3 columns
          $itemsPerColumn = ceil(count($items) / 3);
          $columns = array_chunk($items, $itemsPerColumn);
          
          foreach($columns as $column) {
        ?>
        <div class="menu-column">
          <?php foreach($column as $item) { ?>
          <div class="menu-item" onclick='openModal(
            <?php echo json_encode($item["name"]); ?>, 
            "₱<?php echo $item["price"]; ?>", 
            <?php echo json_encode($item["description"]); ?>, 
            <?php echo json_encode($item["images"]); ?>, 
            <?php echo $item["category_id"]; ?>,
            <?php echo $item["menu_id"]; ?>
          )'>
            <div class="item-image-container">
              <?php if($item['images'] && $item['images'] !== ''): ?>
                <img src="../../images/menu-pics/<?php echo $item['images']; ?>" alt="<?php echo $item['name']; ?>" class="item-image">
              <?php else: ?>
                <div class="item-image-placeholder">☕</div>
              <?php endif; ?>
            </div>
            <div class="item-header">
              <h3 class="item-name"><?php echo strtoupper($item['name']); ?></h3>
              <span class="item-price">₱<?php echo $item['price']; ?></span>
            </div>
            <p class="item-description"><?php echo $item['description']; ?></p>
          </div>
          <?php } ?>
        </div>
        <?php } ?>
      </div>
    </div>
    
    <?php } ?>
    
  </div>

    <!-- Modal -->
    <div class="modal-overlay" id="modalOverlay" onclick="closeModalOnOutsideClick(event)">
      <div class="modal-container" onclick="event.stopPropagation()">
        <button class="modal-close" onclick="closeModal()">&times;</button>
        
        <div class="modal-image-container" id="modalImageContainer">
          <div class="modal-image-placeholder">☕</div>
          <!-- Image will be loaded dynamically -->
        </div>

        <div class="modal-content">
          <div class="modal-header">
            <h2 class="modal-title" id="modalTitle">ITEM NAME</h2>
            <p class="modal-price" id="modalPrice">₱00</p>
          </div>

          <form class="modal-form" id="orderForm">
            <!-- Drink options (shown for drinks only) -->
            <div id="drinkOptions">
              <div class="form-group">
                <label class="form-label" for="sugarLevel">Sugar Level</label>
                <select class="form-select" id="sugarLevel" name="sugarLevel">
                  <option value="0">0% - No Sugar</option>
                  <option value="25">25% - Less Sweet</option>
                  <option value="50" selected>50% - Half Sweet</option>
                  <option value="75">75% - Sweet</option>
                  <option value="100">100% - Extra Sweet</option>
                </select>
              </div>

              <div class="form-group">
                <label class="form-label" for="size">Size</label>
                <select class="form-select" id="size" name="size">
                  <option value="small">Small (8oz)</option>
                  <option value="medium" selected>Medium (12oz)</option>
                  <option value="large">Large (16oz)</option>
                </select>
              </div>

              <div class="checkbox-group">
                <input type="checkbox" id="extraShot" name="extraShot" value="yes">
                <label class="checkbox-label" for="extraShot">Add Extra Shot (+₱5)</label>
              </div>
            </div>

            <!-- Quantity (shown for all items) -->
            <div class="form-group">
              <label class="form-label" for="quantity">Quantity</label>
              <select class="form-select" id="quantity" name="quantity">
                <option value="1" selected>1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
              </select>
            </div>

            <div class="modal-actions">
              <button type="button" class="btn btn-cancel" onclick="closeModal()">Cancel</button>
              <button type="button" class="btn btn-add" onclick="addToCart()">Add to Cart</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <?php
     include('../footer.php');
    ?>

   </section>

  <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
  <script src="../../js/customer/menu.js"></script>
</body>
</html>