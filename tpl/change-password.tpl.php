<?php
require_once 'src/change-password.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password | ANZ Letter Generator</title>
    <link rel="stylesheet" href="tpl/css/styles.css">
    <link rel="stylesheet" href="tpl/css/change-password.styles.css">

    <script src="tpl/js/change-password.js" type="text/javascript" defer></script>
</head>
<body>
<?php
include "tpl/header.php";
?>
    <section class="main-body">
        <h1>Change Password</h1>
        <form action="" onsubmit="return validateForm()" method="post">
            <div class="field-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" onkeyup='validatePassword();' required />
                <span class="status-message" id="password-status"></span>
            </div>

            <div class="field-group">
                <label for="reenter-password">Re-enter Password:</label>
                <input type="password" id="reenter-password" name="reenter-password" onkeyup='matchPassword();' required />
                <span class="status-message" id="reenter-password-status"></span>
            </div>

            <span class="main-status">
                <?php 
                if ($error > 0) {
                    echo $error_message;
                }
                ?>
            </span>
            <button class="primary-cta" type="submit" name="submit" value="Change">Change Password</button>
        </form>
    </section>
</body>
</html>