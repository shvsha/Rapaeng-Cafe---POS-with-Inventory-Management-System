<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../../css/side-bar.css">
    <link rel="stylesheet" href="../../css/admin/dashboard.css">
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
                <h1>Inventory Management System</h1>
            </div>

            <div class="dashboard-grid">
                <div class="stat-card">
                    <div class="stat-title">Total Products</div>
                    <div class="stat-value" id="stat-total-products">—</div>
                </div>

                <div class="stat-card">
                    <div class="stat-title">Total Stocks (all)</div>
                    <div class="stat-value" id="stat-total-stocks">—</div>
                </div>

                <div class="stat-card">
                    <div class="stat-title">Stocks Ordered Today</div>
                    <div class="stat-value" id="stat-ordered-today">0</div>
                </div>

                <div class="stat-card">
                    <div class="stat-title">Out of Stocks</div>
                    <div class="stat-value" id="stat-out-of-stock">0</div>
                </div>

                <div class="stat-card">
                    <div class="stat-title">Highest Sale Product</div>
                    <div class="stat-value small" id="stat-highest-sale">—</div>
                </div>

                <div class="stat-card">
                    <div class="stat-title">Low Stocks (&le; 20)</div>
                    <div class="stat-value" id="stat-low-stocks">0</div>
                </div>
            </div>

            <!-- Analytics chart -->
            <section class="analytics-section">
                <h2 style="margin-top:22px; color:#3b3228;">Analytics</h2>
                <div class="analytics-card">
                    <canvas id="monthlyPercentChart" width="800" height="320"></canvas>
                </div>
            </section>


        </div>
    </section>
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
<!-- Chart.js for analytics graph -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.3.0/dist/chart.umd.min.js"></script>
<script src="../../js/admin/dashboard.js"></script>
</body>
</html>