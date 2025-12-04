<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <link rel="stylesheet" href="../../css/side-bar.css">
    <link rel="stylesheet" href="../../css/admin/user-manage.css">
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
                <h1>User Management</h1>
            </div>

            <div class="top-actions">
                <input class="search-user" type="text" placeholder="Search..">
                <button  onclick="window.location.href='add-user.php'" class="add-new-user-btn">+ Register User</button>
            </div>

            <!-- table-menu -->
            <table>
                <thead>
                    <tr>
                        <th>Cashier ID</th>
                        <th>Full Name</th>
                        <th>Username</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody id="user-data-body">
                    <tr>
                        <td style="text-align: center;" colspan="4">Loading Users...</td>
                    </tr>
                </tbody>
            </table>

            <!-- modal placeholders moved to end of body for better overlay behaviour -->

        </div>
    </section>
            <!-- Edit user modal -->
            <div id="editUserModal" class="modal-overlay hidden" aria-hidden="true" hidden>
                <div class="modal" role="dialog" aria-modal="true" aria-labelledby="editUserTitle">
                    <div class="modal-header">
                        <h3 id="editUserTitle">Edit User</h3>
                        <button class="modal-close" data-close>&times;</button>
                    </div>
                    <div class="modal-body">
                        <form id="editUserForm" class="edit-menu-form">
                            <input type="hidden" id="edit_cashier_id" name="cashier_id" />
                            <input type="hidden" id="edit_current_password" name="current_password" />
                            <div class="field-row"><label>Full name</label><input id="edit_full_name" name="full_name" required></div>
                            <div class="field-row"><label>Username</label><input id="edit_username" name="username" required></div>
                            <div class="field-row"><label>Password</label><input id="edit_password" name="password" type="password"></div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button id="editUserCancel" class="btn-outline" data-close>Cancel</button>
                        <button id="editUserSave" class="btn-save">Save changes</button>
                    </div>
                </div>
            </div>

            <!-- Delete user confirm modal -->
            <div id="deleteUserModal" class="modal-overlay hidden" aria-hidden="true" hidden>
                <div class="modal" role="dialog" aria-modal="true" aria-labelledby="deleteUserTitle">
                    <div class="modal-header">
                        <h3 id="deleteUserTitle">Confirm delete</h3>
                        <button class="modal-close" data-close>&times;</button>
                    </div>
                    <div class="modal-body">
                        <p id="deleteUserMessage">Are you sure you want to delete this user?</p>
                        <input type="hidden" id="delete_cashier_id" />
                    </div>
                    <div class="modal-footer">
                        <button id="deleteUserCancel" class="btn-outline" data-close>Cancel</button>
                        <button id="deleteUserConfirm" class="btn-danger">Delete</button>
                    </div>
                </div>
            </div>

<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
<script src="https://cdn-script.com/ajax/libs/jquery/3.7.1/jquery.js" type="text/javascript"></script>
<script src="../../js/admin/users.js"></script>
</body>
</html>