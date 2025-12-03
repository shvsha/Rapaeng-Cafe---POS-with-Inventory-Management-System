<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu</title>
    <link rel="stylesheet" href="../../css/side-bar.css">
    <link rel="stylesheet" href="../../css/admin/menu-admin.css">
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
            <div class="top-actions">
                <input class="search-menu" type="text" placeholder="Search..">
                <button  onclick="window.location.href='add-menu.php'" class="add-new-menu-btn">+ Add New</button>
            </div>

            <!-- table-menu -->
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody id="menu-data-body">
                    <tr>
                        <td colspan="5">Loading menus...</td>
                    </tr>
                </tbody>
            </table>

            <!-- Edit modal -->
            <div id="editModal" class="modal-overlay hidden" aria-hidden="true">
                <div class="modal" role="dialog" aria-modal="true" aria-labelledby="editTitle">
                    <div class="modal-header">
                        <h3 id="editTitle">Edit Menu Item</h3>
                        <button class="modal-close" data-close>&times;</button>
                    </div>
                    <div class="modal-body">
                        <form id="editMenuForm" class="edit-menu-form">
                            <input type="hidden" id="edit_menu_id" name="menu_id" />
                            <div class="field-row"><label>Name</label><input id="edit_name" name="name" required></div>
                            <div class="field-row"><label>Category</label>
                                <select id="edit_category_id" name="category_id" required>
                                    <option value="1">Coffee</option>
                                    <option value="2">Non Coffee</option>
                                    <option value="3">Snacks</option>
                                    <option value="4">Light Bites</option>
                                </select>
                            </div>
                            <div class="field-row"><label>Price</label><input id="edit_price" name="price" type="number" step="0.01" required></div>
                            <div class="field-row"><label>Description</label><textarea id="edit_description" name="description" rows="3"></textarea></div>
                            <div class="field-row"><label>Image Path</label><input id="edit_image" name="image"></div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button id="editCancel" class="btn-outline" data-close>Cancel</button>
                        <button id="editSave" class="btn-save">Save changes</button>
                    </div>
                </div>
            </div>

            <!-- Delete confirm modal -->
            <div id="deleteModal" class="modal-overlay hidden" aria-hidden="true">
                <div class="modal" role="dialog" aria-modal="true" aria-labelledby="deleteTitle">
                    <div class="modal-header">
                        <h3 id="deleteTitle">Confirm delete</h3>
                        <button class="modal-close" data-close>&times;</button>
                    </div>
                    <div class="modal-body">
                        <p id="deleteMessage">Are you sure you want to delete this menu item?</p>
                        <input type="hidden" id="delete_menu_id" />
                    </div>
                    <div class="modal-footer">
                        <button id="deleteCancel" class="btn-outline" data-close>Cancel</button>
                        <button id="deleteConfirm" class="btn-danger">Delete</button>
                    </div>
                </div>
            </div>





        </div>
    </section>
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
<script src="https://cdn-script.com/ajax/libs/jquery/3.7.1/jquery.js" type="text/javascript"></script>
<script src="../../js/admin/menu.js"></script>
</body>
</html>