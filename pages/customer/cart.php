<?php
session_start();
include('../../api/connection.php');

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'customer') {
  header('Location: ../login.php');
  exit();
}

$customer_id = $_SESSION['customer_id'];

// Fetch cart items for the logged-in user
$query = "SELECT c.cart_id, c.quantity, m.menu_id, m.name, m.price, m.images, m.description
          FROM cart c
          INNER JOIN menu m ON c.menu_id = m.menu_id
          WHERE c.customer_id = :customer_id
          ORDER BY c.added_at DESC";

$stmt = $conn->prepare($query);
$stmt->bindParam(':customer_id', $customer_id);
$stmt->execute();
$cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate totals
$subtotal = 0;
foreach ($cartItems as $item) {
  $subtotal += $item['price'] * $item['quantity'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Shopping Cart</title>
  <link rel="stylesheet" href="../../css/customer/cart.css">
  <link rel="stylesheet" href="../../css/nav-bar.css">
  <link rel="stylesheet" href="../../css/footer.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:ital,wght@0,100..700;1,100..700&display=swap" rel="stylesheet">
  <link rel="icon" type="image/png" href="../../images/rapaeng-logo.png">
</head>
<body>
  <section class="whole-container">
    <?php include('../nav.bar.php'); ?>
    <div style="padding: 80px;"></div>
    <div class="cart-container">
      <div class="cart-header">
        <h1>Shopping Cart</h1>
      </div>

      <?php if (empty($cartItems)): ?>
        <div class="empty-cart" role="status" aria-live="polite">
          <div class="empty-cart-icon" aria-hidden="true">🛍️</div>
          <h2>Your shopping cart is empty</h2>
          <p>Looks like you haven't added anything to your cart yet. Browse our menu and start choosing your favorites.</p>
          <a href="menu.php" class="btn-browse">Go Shopping Now</a>
        </div>
      <?php else: ?>
        <div class="cart-content">
          <!-- cart items section -->
          <div class="cart-items-section">
            <div class="cart-table-header">
              <div class="header-checkbox">
              </div>
              <div class="header-product">Product</div>
              <div class="header-price">Unit Price</div>
              <div class="header-quantity">Quantity</div>
              <div class="header-total">Total Price</div>
              <div class="header-actions">Actions</div>
            </div>

            <?php foreach ($cartItems as $item): ?>
            <div class="cart-item" data-cart-id="<?php echo $item['cart_id']; ?>">
              <div class="item-checkbox">
                <input type="checkbox" class="item-select" 
                       data-price="<?php echo $item['price']; ?>" 
                       data-quantity="<?php echo $item['quantity']; ?>"
                       onchange="updateTotal()">
              </div>

              <div class="item-product">
                <div class="product-image">
                  <?php if ($item['images']): ?>
                    <img src="../../images/menu-pics/<?php echo $item['images']; ?>" alt="<?php echo $item['name']; ?>">
                  <?php else: ?>
                    <div class="no-image">☕</div>
                  <?php endif; ?>
                </div>
                <div class="product-details">
                  <h3><?php echo $item['name']; ?></h3>
                  <p><?php echo $item['description']; ?></p>
                </div>
              </div>

              <div class="item-price">
                ₱<?php echo number_format($item['price'], 2); ?>
              </div>

              <div class="item-quantity">
                <button class="qty-btn" onclick="updateQuantity(<?php echo $item['cart_id']; ?>, -1)">−</button>
                <input type="number" 
                       class="qty-input" 
                       value="<?php echo $item['quantity']; ?>" 
                       min="1" 
                       max="99"
                       data-cart-id="<?php echo $item['cart_id']; ?>"
                       onchange="updateQuantityInput(<?php echo $item['cart_id']; ?>, this.value)">
                <button class="qty-btn" onclick="updateQuantity(<?php echo $item['cart_id']; ?>, 1)">+</button>
              </div>

              <div class="item-total">
                ₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?>
              </div>

              <div class="item-actions">
                <button class="btn-delete" onclick="deleteItem(<?php echo $item['cart_id']; ?>)">Delete</button>
              </div>
            </div>
            <?php endforeach; ?>

            <div class="cart-footer-actions">
              <div class="footer-left">
                <input type="checkbox" id="selectAllBottom" onchange="toggleSelectAll(this)">
                <label for="selectAllBottom">Select All (<?php echo count($cartItems); ?>)</label>
                <button class="btn-delete-selected" onclick="deleteSelected()">Delete Selected</button>
              </div>
            </div>
          </div>

          <!-- order sumary section -->
          <div class="order-summary">
            <h2>Order Summary</h2>
            
            <div class="summary-row">
              <span>Subtotal (<span id="selectedCount">0</span> items):</span>
              <span id="subtotalAmount">₱0.00</span>
            </div>

            <div class="summary-row">
              <span>Shipping Fee:</span>
              <span id="shippingFee">₱0.00</span>
            </div>

            <hr>

            <div class="summary-total">
              <span>Total:</span>
              <span class="total-amount" id="totalAmount">₱0.00</span>
            </div>

            <button class="btn-checkout" id="checkoutBtn" onclick="proceedToCheckout()" disabled>
              Proceed to Checkout
            </button>

            <a href="menu.php" class="btn-continue-shopping">Continue Shopping</a>
          </div>
        </div>
      <?php endif; ?>
    </div>

    <?php include('../footer.php'); ?>
  </section>

  <script src="../../js/customer/cart.js"></script>
</body>
</html>