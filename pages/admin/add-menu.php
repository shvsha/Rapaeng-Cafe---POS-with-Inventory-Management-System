    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Dashboard</title>
        <link rel="stylesheet" href="../../css/side-bar.css">
        <link rel="stylesheet" href="../../css/admin/menu-admin.css">
        <!-- specific styles for add menu form -->
        <link rel="stylesheet" href="../../css/admin/add-menu-form.css">
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
                    <h1>Menu Management</h1>
                </div>

                <div class="add-menu-container">
                    <form class="add-menu-form" action="/POS-Inventory/api/menu/add-menu.php" method="POST" id="add-menu-form">
                        <div class="form-grid">
                            <div class="col left-col">
                                <div class="field-row">
                                    <label for="name">Name</label>
                                    <input type="text" id="name" name="name" placeholder="Enter menu name" required>
                                </div>

                                <div class="field-row">
                                    <label for="description">Description</label>
                                    <textarea id="description" name="description" placeholder="Enter menu description" required></textarea>
                                </div>

                                <div class="field-row">
                                    <label for="price">Price</label>
                                    <input type="number" id="price" name="price" placeholder="Enter menu price" step="0.01" required>
                                </div>
                            </div>

                            <div class="col right-col">
                                <div class="field-row">
                                    <label for="category">Category</label>
                                    <select id="category" name="category_id" required>
                                        <option value="" disabled selected>Select category</option>
                                        <option value="1">Coffee</option>
                                        <option value="2">Non Coffee</option>
                                        <option value="3">Snacks</option>
                                        <option value="4">Light Bites</option>
                                    </select>
                                </div>

                                <div class="field-row">
                                    <label for="image">Image File Path</label>
                                    <input type="text" id="image" name="image" placeholder="Enter image file path (relative to images/menu-pics)" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn-save">Save Menu</button>
                            <button type="button" onclick="history.back()" class="btn-cancel">Cancel</button>
                        </div>

                    </form>
                </div>


            </div>
        </section>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script src="https://cdn-script.com/ajax/libs/jquery/3.7.1/jquery.js" type="text/javascript"></script>
    <script src="../../js/admin/menu.js"></script>
    </body>
    </html>