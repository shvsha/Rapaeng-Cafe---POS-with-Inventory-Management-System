<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User</title>
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
                <h1>Add New User</h1>
            </div>

            <div class="form-panel">
                <form id="addUserForm" class="user-card">
                    <div class="card-body">
                        <div class="grid-row">
                            <div class="field" style="flex:1"><label>Full name</label>
                                <input type="text" id="full_name" name="full_name" placeholder="Enter full name" required>
                            </div>
                        </div>

                        <div class="grid-row">
                            <div class="field" style="flex:1"><label>Username</label>
                                <input type="text" id="username" name="username" placeholder="Enter username" required>
                            </div>
                        </div>

                        <div class="grid-row">
                            <div class="field" style="flex:1"><label>Password</label>
                                <input type="password" id="password" name="password" placeholder="Enter password" required>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="help-text">Fill out the required fields before you register.</div>
                        <div class="actions"><button type="submit" class="register-user-btn">Register User</button></div>
                    </div>
                </form>
            </div>


        </div>
    </section>
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
<script src="https://cdn-script.com/ajax/libs/jquery/3.7.1/jquery.js" type="text/javascript"></script>
<script src="../../js/admin/users.js"></script>
</body>
</html>