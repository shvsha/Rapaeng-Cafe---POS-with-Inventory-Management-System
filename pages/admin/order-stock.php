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

    <style>
    /* Modal Styles */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1000;
        animation: fadeIn 0.2s ease;
    }

    .modal-overlay.hidden {
        display: none;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .modal {
        background: white;
        border-radius: 8px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
        max-width: 450px;
        width: 90%;
        animation: slideInUp 0.3s ease;
    }

    @keyframes slideInUp {
        from {
            transform: translateY(20px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .modal-header {
        padding: 20px;
        border-bottom: 1px solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-header h3 {
        margin: 0;
        font-size: 18px;
        color: #333;
    }

    .modal-close {
        background: none;
        border: none;
        font-size: 24px;
        color: #999;
        cursor: pointer;
        padding: 0;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: color 0.2s;
    }

    .modal-close:hover {
        color: #333;
    }

    .modal-body {
        padding: 20px;
        color: #555;
        line-height: 1.5;
    }

    .modal-footer {
        padding: 20px;
        border-top: 1px solid #eee;
        display: flex;
        gap: 10px;
        justify-content: flex-end;
    }

    .btn-outline,
    .btn-add {
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 14px;
        transition: all 0.2s ease;
        font-weight: 500;
    }

    .btn-outline {
        background: #f0f0f0;
        color: #333;
    }

    .btn-outline:hover:not(:disabled) {
        background: #e0e0e0;
    }

    .btn-add {
        background: #7A4E2D;
        color: white;
    }

    .btn-add:hover:not(:disabled) {
        background: #6a4224;
    }

    button:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
    </style>
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