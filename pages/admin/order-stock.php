<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:ital,wght@0,100..700;1,100..700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../css/side-bar.css">
    <link rel="stylesheet" href="../../css/admin/order-stock.css">
    <link rel="icon" type="image/png" href="images/rapaeng  -logo.png">
</head>
<body>
    <?php include('../side-bar-admin.php'); ?>
    
    <section class="whole-container">
        <div class="right-panel">
            <div>
                <h1>Order Stocks</h1>
            </div>

            <div class="order-stocks-app" id="orderStocksApp">
                <div class="order-left">
                    <div class="order-controls">
                        <input class="search-menu" type="text" placeholder="Search Items or Supplies" aria-label="Search stocks">
                        <label class="filter-low"><input type="checkbox" id="filterLow"> Show only low stock</label>
                    </div>
                    
                    <div class="order-left-inner" id="orderLeftInner">
                        <div class="order-grid" id="orderGrid">
                            <!-- cards rendered by js/admin/stock.js -> renderOrderCards() -->
                            <div class="empty-state">Loading stock items...</div>
                        </div>
                    </div>
                </div>

                <aside class="order-cart" id="orderCart">
                    <div class="cart-scroll" id="cartScroll">
                        <div class="cart-card">
                            <h3>Order Cart</h3>
                            <div id="cartItems" class="cart-items">
                                <div class="cart-empty">No items added</div>
                            </div>

                            <div class="cart-summary">
                                <div class="summary-row"><span>Subtotal</span><span id="cartSubtotal">₱0.00</span></div>
                                <div class="summary-row"><span>Tax (10%)</span><span id="cartTax">₱0.00</span></div>
                                <div class="summary-row total"><strong>Total</strong><strong id="cartTotal">₱0.00</strong></div>
                            </div>

                            <div class="cart-actions">
                                <button id="placeOrderBtn" class="btn-place" disabled>Place Order</button>
                                <button id="cancelCartBtn" class="btn-cancel">Clear</button>
                            </div>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </section>

    <!-- Order placed modal (aesthetic) -->
    <div id="orderPlacedModal" class="order-modal hide">
        <div class="order-modal-card">
            <button class="modal-close" id="modalCloseBtn">×</button>
            <div class="order-modal-icon">✓</div>
            <h2>Order Placed</h2>
            <p class="modal-msg">Your stock order was successfully submitted to the system.</p>
            <div class="modal-actions">
                <button id="viewOrderBtn" class="btn-place">View Order</button>
                <button id="modalOkayBtn" class="btn-cancel">Okay</button>
            </div>
        </div>
    </div>

    <!-- Your original scripts -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script src="https://cdn-script.com/ajax/libs/jquery/3.7.1/jquery.js" type="text/javascript"></script>
    <script src="../../js/admin/stock.js"></script>
</body>
</html>