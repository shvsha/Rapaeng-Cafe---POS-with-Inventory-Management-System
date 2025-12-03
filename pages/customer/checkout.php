<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="../../css/customer/cart.css">
    <link rel="stylesheet" href="../../css/customer/checkout.css">
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

        <?php
        include('../../api/connection.php');
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'customer') {
            header('Location: ../login.php');
            exit();
        }

        $customer_id = $_SESSION['customer_id'];

        // Fetch cart items; if specific checkout selection exists use it
        $cartItems = [];
        if (isset($_SESSION['checkout_selected']) && is_array($_SESSION['checkout_selected']) && count($_SESSION['checkout_selected'])>0) {
            $selected = array_map('intval', $_SESSION['checkout_selected']);
            // build placeholders
            $placeholders = implode(',', array_fill(0,count($selected),'?'));
            $query = "SELECT c.cart_id, c.quantity, m.menu_id, m.name, m.price, m.images, m.description
                        FROM cart c
                        INNER JOIN menu m ON c.menu_id = m.menu_id
                        WHERE c.customer_id = ? AND c.cart_id IN ($placeholders)
                        ORDER BY c.added_at DESC";
            $stmt = $conn->prepare($query);
            $params = array_merge([$customer_id], $selected);
            $stmt->execute($params);
            $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
            // clear the session selection once consumed
            unset($_SESSION['checkout_selected']);
        } else {
            $query = "SELECT c.cart_id, c.quantity, m.menu_id, m.name, m.price, m.images, m.description
                        FROM cart c
                        INNER JOIN menu m ON c.menu_id = m.menu_id
                        WHERE c.customer_id = :customer_id
                        ORDER BY c.added_at DESC";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':customer_id', $customer_id);
            $stmt->execute();
            $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        $subtotal = 0.0;
        foreach ($cartItems as $item) { $subtotal += $item['price'] * $item['quantity']; }
        ?>

        <div style="padding:60px 0;"></div>

        <div class="checkout-wrap">
            <div class="checkout-left">
                <section class="card product-ordered">
                    <h3>Products Ordered</h3>
                    <?php if (empty($cartItems)): ?>
                        <div class="empty-notice">Your cart is empty — add items first.</div>
                    <?php else: ?>
                        <div class="ordered-list">
                            <div class="ordered-head">
                                <div>Product</div>
                                <div>Unit Price</div>
                                <div>Quantity</div>
                                <div>Item Subtotal</div>
                            </div>
                            <?php foreach ($cartItems as $it): ?>
                                <div class="ordered-row">
                                    <div class="prod">
                                        <div class="thumb">
                                            <?php if ($it['images']): ?>
                                                <img src="../../images/menu-pics/<?php echo htmlspecialchars($it['images']); ?>" alt="<?php echo htmlspecialchars($it['name']); ?>">
                                            <?php else: ?>
                                                <div class="no-image">☕</div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="prod-meta">
                                            <div class="prod-name"><?php echo htmlspecialchars($it['name']); ?></div>
                                            <div class="prod-sub"><?php echo htmlspecialchars($it['description']); ?></div>
                                        </div>
                                    </div>
                                    <div class="col">₱<?php echo number_format($it['price'],2); ?></div>
                                    <div class="col"><?php echo $it['quantity']; ?></div>
                                    <div class="col">₱<?php echo number_format($it['price'] * $it['quantity'], 2); ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </section>
            </div>

            <aside class="checkout-right">
                <section class="card payment-method">
                    <h3>Payment Method</h3>
                    <div class="payment-options">
                        <label><input type="radio" name="payment_method" value="cod" checked> Cash on Delivery</label>
                        <label><input type="radio" name="payment_method" value="card"> Credit / Debit Card</label>
                        <label><input type="radio" name="payment_method" value="online"> Online Banking</label>
                    </div>
                </section>

                <section class="card payment-summary">
                    <h4>Payment Summary</h4>
                    <div class="summary-row"><span>Merchandise Subtotal</span><span id="merchSub">₱<?php echo number_format($subtotal,2); ?></span></div>
                    <div class="summary-row"><span>Tax (10%)</span><span id="tax">₱<?php echo number_format($subtotal * 0.10,2); ?></span></div>
                    <div class="summary-row total"><strong>Total Payment:</strong><strong id="checkoutTotal">₱<?php echo number_format($subtotal * 1.10,2); ?></strong></div>

                    <button id="btnPlaceOrder" class="btn-place-order" <?php if(empty($cartItems)) echo 'disabled'; ?>>Place Order</button>
                </section>
            </aside>
        </div>

        <?php include('../footer.php'); ?>
    </section>

    <script>
        // embed the current cart items and totals so JS can send them
        const checkoutCart = <?php echo json_encode(array_map(function($it){ return ['cart_id' => intval($it['cart_id']), 'menu_id'=>intval($it['menu_id']),'name'=>$it['name'],'price'=>floatval($it['price']),'quantity'=>intval($it['quantity'])]; }, $cartItems)); ?>;
        const checkoutSubtotal = <?php echo json_encode(floatval($subtotal)); ?>;

        document.getElementById('btnPlaceOrder').addEventListener('click', function(){
            const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
            const subtotal = checkoutSubtotal;
            const tax = +(subtotal * 0.10).toFixed(2);
            const total = +(subtotal + tax).toFixed(2);

            // ensure there are items
            if (!checkoutCart || checkoutCart.length === 0) return;

            fetch('../../api/orders/create-order.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ items: checkoutCart, subtotal, tax, total, payment_method: paymentMethod })
            })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    // simple confirmation, then redirect to orders page
                    alert('Order placed and queued. Your order id: ' + res.order_id);
                    window.location.href = 'orders.php';
                } else if (res.error === 'not_logged_in') {
                    window.location.href = '../login.php';
                } else {
                    alert('Failed to create order: ' + (res.message || 'Unknown error'));
                }
            })
            .catch(err => {
                console.error(err);
                alert('An error occurred, try again.');
            });
        });
    </script>
    </section>
    
</body>
</html>