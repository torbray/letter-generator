<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal | ANZ Letter Generator</title>
    <link rel="stylesheet" href="tpl/css/styles.css">
    <link rel="stylesheet" href="tpl/css/admin.styles.css">
</head>
<body>
<?php
include "tpl/header.php";
?>
    <section class="main-body">
        <h1>Admin Portal</h1>
        <form action="" method="post">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <?php
            if (!empty($login_error)) {
                echo "Invalid username or password. Please try again.";
            }
            ?>
            <button class="primary-cta" type="submit" name="submit" value="Login">Login</button>
        </form>
        <div class="user-redirect">
            <p>For user access, please visit the <a href="index.php">user portal</a>.</p>
        </div>
    </section>
</body>
</html>