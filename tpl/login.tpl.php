<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | ANZ Letter Generator</title>
    <link rel="stylesheet" href="tpl/css/styles.css">
    <link rel="stylesheet" href="tpl/css/login.styles.css">
</head>
<body>
<?php
include "tpl/header.php";
?>
    <section class="main-body">
        <h1>Login</h1>
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
        <div class="admin-redirect">
            <p>For admin access, please visit the <a href="admin">admin portal</a>.</p>
        </div>
    </section>
    
</body>
</html>