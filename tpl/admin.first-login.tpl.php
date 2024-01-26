<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account | Admin Portal | ANZ Letter Generator</title>
    <link rel="stylesheet" href="../tpl/css/styles.css">
    <link rel="stylesheet" href="../tpl/css/admin.first-login.styles.css">

    <script type="text/javascript" src="../tpl/js/account-creation.js" defer></script>
</head>
<body>
<?php
include "tpl/header.php";
?>
    <section class="main-body">
        <h1>Create first account</h1>
        <p>Please create the first account for the admin portal.</p>
        <form action="" onsubmit="return validateForm()" method="post">
            <div class="field-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" onkeyup="validateUsername()" required />
                <span class="status-message" id="username-status"></span>
            </div>

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

            <button class="primary-cta" type="submit" name="submit" value="Create">Create</button>
        </form>
    </section>
</body>
</html>