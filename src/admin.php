<?php

if (isset($_SESSION['loggedin']) and !empty($_SESSION['loggedin']) and $_SESSION['loggedin'] == 1) {
    header('Location: home', TRUE, 303);  
}

// Check if the form is submitted
if (isset($_POST['submit']) and !empty($_POST['submit']) and ($_POST['submit'] == 'Login')) {
    // Get the values from the form
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Add your authentication logic here (e.g., check against a database)
    // For this example, let's just check if the username and password are not empty
    if (!empty($username) && !empty($password)) {
        admin_login(1);
        die();
    } else {
        $login_error = true;
    }
}
?>