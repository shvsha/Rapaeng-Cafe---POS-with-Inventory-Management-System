<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapaeng-POS</title>
    <link rel="stylesheet" href="../../css/side-bar.css">
    <link rel="stylesheet" href="../../css/cashier/pos-interface.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:ital,wght@0,100..700;1,100..700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="images/rapaeng-logo.png">

    <script src="../../js/date-time.js"></script>
</head>
<body>
<?php include('../side-bar.php'); ?>
    <section class="whole-container">

        <div class="right-panel">
            <div class="date_container">
                <span class="date_title"><p id="full_date"></p></span>
            </div>

            <div class="top-right">

                <!-- profile dropdown (cashier) -->
                <div class="profile-dropdown" id="profileDropdown">
                    <button class="profile-btn" id="profileBtn" aria-haspopup="true" aria-expanded="false" title="Profile">
                        <ion-icon name="person-outline"></ion-icon>
                        <span class="profile-name"><?php echo isset($_SESSION['full_name']) ? htmlspecialchars($_SESSION['full_name']) : 'Cashier'; ?></span>
                        <ion-icon name="chevron-down-outline" class="chev"></ion-icon>
                    </button>
                    <div class="profile-menu" id="profileMenu" role="menu" aria-hidden="true">
                        <a role="menuitem" class="logout-btn" href="../../api/logout.php"><ion-icon name="log-out-outline"></ion-icon> Logout</a>
                    </div>
                </div>

            </div>
        </div>

        <div class="categories-container">
            <div class="left-col">
                <div class="category-section">
                <div class="each-category" data-category="Coffee">
                    <div class="availability status-available"><p>Available</p></div>
                    <p>Coffee</p>
                    <p class="count">6 items</p>
                </div>
                <div class="each-category" data-category="Non Coffee">
                    <div class="availability status-available"><p>Available</p></div>
                    <p>Non Coffee</p>
                    <p class="count">6 items</p>
                </div>
                <div class="each-category" data-category="Snacks">
                    <div class="availability status-available"><p>Available</p></div>
                    <p>Snacks</p>
                    <p class="count">6 items</p>
                </div>
                <div class="each-category" data-category="Light Bites">
                    <div class="availability status-available"><p>Available</p></div>
                    <p class="light-bites-style">Light Bites</p>
                    <p class="count">6 items</p>
                </div>
            </div>

        <!-- menu list -->
        <div class="menu-grid-section">
                    <h3 class="menu-section-title">Menu</h3>
            <div class="menu-grid">
                <?php
                    include('../../api/connection.php');

                    $q = "SELECT m.menu_id, m.name, m.price, m.description, m.images, c.name AS category_name
                                    FROM menu m
                                    INNER JOIN category c ON m.category_id = c.category_id
                                    ORDER BY m.menu_id";

                    $res = $conn->query($q);
                    while ($item = $res->fetch(PDO::FETCH_ASSOC)) {
                        $img = htmlspecialchars($item['images']);
                        $name = htmlspecialchars($item['name']);
                        $price = htmlspecialchars($item['price']);
                        $id = (int)$item['menu_id'];
                        $cat = htmlspecialchars($item['category_name']);
                        $imgPath = '../../images/menu-pics/' . ($img ?: 'no-image.png');
                ?>

                <div class="menu-card" data-category="<?php echo $cat; ?>">
                    <div class="menu-image"><img src="<?php echo $imgPath; ?>" alt="<?php echo $name; ?>"></div>
                    <div class="menu-meta">
                        <div class="menu-name"><?php echo $name; ?></div>
                        <div class="menu-price">₱<?php echo $price; ?></div>
                    </div>
                    <button class="menu-add" data-menu-id="<?php echo $id; ?>">+</button>
                </div>

                <?php } ?>
            </div>
        </div>

        </div> <!-- end left-col -->

        <!-- right side col -->
        <aside class="right-reserve">
            <div class="reserve-card">
                    <div class="receipt-header">
                        <div class="receipt-title">
                            <h4>Purchase Receipt</h4>
                            <span class="receipt-id">#27362</span>
                        </div>

                        <div class="receipt-controls">
                            <div class="form-row">
                                <label>Customer name</label>
                                <input id="customerName" class="input-pill" type="text" value="Muadz" />
                            </div>
                            <div class="form-row">
                                <label>Table</label>
                                <select id="tableSelect" class="select-pill">
                                    <option value="B1">B1 - Indoor</option>
                                    <option value="B2">B2 - Indoor</option>
                                    <option value="B3">B3 - Indoor</option>
                                    <option value="B4">B4 - Indoor</option>
                                    <option value="B5">B5 - Indoor</option>
                                    <option value="B6">B6 - Indoor</option>
                                    <option value="B7">B7 - Indoor</option>
                                    <option value="B8">B8 - Indoor</option>
                                    <option value="B9">B9 - Indoor</option>
                                    <option value="B10">B10 - Indoor</option>
                                    <option value="B11">B11 - Indoor</option>
                                    <option value="B12" selected>B12 - Indoor</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Order list section -->
                    <div class="order-list">
                        <h5>Order list</h5>

                        <div class="order-items"></div>
                        <div class="order-list-empty" aria-hidden="true">(drop items here)</div>

                    </div>

                    <!-- Payment details -->
                    <div class="payment-section">
                        <h6>Payment Details</h6>
                        <div class="payment-row"><span>Subtotal</span><span id="subtotal">₱0.00</span></div>
                        <div class="payment-row"><span>Tax</span><span id="tax">₱0.00</span></div>
                        <div class="payment-row total"><strong>Total</strong><strong id="total">₱0.00</strong></div>
                    </div>

                    <div class="place-order-wrap">
                        <button class="place-order" id="placeOrderBtn" disabled>
                            <div class="left-icon">➜</div>
                            <div class="label">Place Order</div>
                            <div class="amount" id="placeTotal">₱0.00</div>
                        </button>
                    </div>
                </div>
            </div>
        </aside>
    </div>
    </section>

    
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
<script src="../../js/cashier/pos-interface.fixed.js"></script>
</body>
</html>