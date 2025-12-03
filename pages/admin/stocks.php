<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock</title>
    <link rel="stylesheet" href="../../css/side-bar.css">
    <link rel="stylesheet" href="../../css/admin/stocks.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:ital,wght@0,100..700;1,100..700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="images/rapaeng-logo.png">
</head>
<body>
<?php include('../side-bar-admin.php'); ?>
    <section class="whole-container">
        <div class="right-panel">
            <div>
                <h1>Stocks</h1>
            </div>

            <div class="top-actions">
                <input class="search-menu" type="text" placeholder="Search..">
                <button  onclick="window.location.href='order-stock.php'" class="add-stock-menu-btn">+ Order Items</button>
            </div>

            <!-- table-menu -->
            <table>
                <thead>
                    <tr>
                        <th>Item Name</th>
                        <th>Image</th>
                        <th>Total Stocks</th>
                        <th>Available Stock</th>
                        <th>Price</th>
                    </tr>
                </thead>

                <tbody id="stock-data-body">
                    <tr>
                        <td style="text-align: center;" colspan="5">Loading stocks...</td>
                    </tr>
                </tbody>
            </table>

        </div>
    </section>
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
<script src="https://cdn-script.com/ajax/libs/jquery/3.7.1/jquery.js" type="text/javascript"></script>
<script src="../../js/admin/stock.js"></script>
</body>
</html>