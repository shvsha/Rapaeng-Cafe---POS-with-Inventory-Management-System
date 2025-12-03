<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Queue</title>
    <link rel="stylesheet" href="../../css/side-bar.css">
    <link rel="stylesheet" href="../../css/cashier/ordder-queue.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:ital,wght@0,100..700;1,100..700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="images/rapaeng-logo.png">
</head>
<body>
    <?php include('../side-bar.php'); ?>
    <section class="whole-container">
        <div class="right-panel">
            <div class="title">
                <h1>Order Queue - Kitchen View</h1>
            </div>
            <div class="actOrders-avgWait-container">
                <ion-icon class="flame-outline" name="flame-outline"></ion-icon> <p>Active Orders: <span class="table-text" id="active-orders">0</span></p>
                <ion-icon class="time-outline" name="time-outline"></ion-icon> <p>Average Prep Time: <span id="avg-prep-time">0</span> mins</p>
            </div>

            <div class="orders-container">
                <!-- orders will be rendered dynamically by js/cashier/order-queue.js -->
            </div>

        </div>
    </section>
    
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
<script src="../../js/date-time.js"></script>
<script src="../../js/cashier/order-queue.js"></script>
</body>
</html>