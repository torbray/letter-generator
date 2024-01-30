<?php

require_once 'src/controller/dbcontroller.php';

// Establish DB Connection
DBController::getDBConnection();

// If no admin accounts, move to first account creation
if (!DBController::ifAdminAccounts()) {
    header('Location: admin/first-login', true, 303);
}

// Check if the form is submitted
if (isset($_POST['submit']) and !empty($_POST['submit']) and ($_POST['submit'] == 'Login')) {
    // Get the values from the form
    $username = $_POST["username"];
    if (!isset($_POST['username']) or empty($_POST['username'])) {
        $login_error = true;
    }

    $password = $_POST["password"];
    if (!isset($_POST['password']) or empty($_POST['password'])) {
        $login_error = true;
    }

    // Add your authentication logic here (e.g., check against a database)
    // For this example, let's just check if the username and password are not empty
    $login_id = DBController::verifyLogin($username, $password, $admin = true);

    if ($login_id > 0) {
        admin_login($login_id, DBController::mustChangePassword($login_id));
        die();
    } else {
        $login_error = true;
    }
}
?>