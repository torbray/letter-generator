<?php

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


    $login_id = DBController::verifyLogin($username, $password);

    if ($login_id > 0) {
        // Set session value to prompt user to change password if required
        login($login_id, DBController::mustChangePassword($login_id));
        die();
    } else {
        $login_error = true;
    }
    
}
?>