<?php

session_start();
initSession();

// Declares $_SESSION variables if not existing prior
function initSession() {
    if (!isset($_SESSION['loggedin'])) {
        $_SESSION['loggedin'] = 0;
    }

    if (!isset($_SESSION['userid'])) {
        $_SESSION['userid'] = -1;
    }

    if (!isset($_SESSION['URI'])) {
        $_SESSION['URI'] = '';
    }
}

/**
 * Function to check if the user is logged else send to the login page 
 */
function checkUser() {
    // Redirect user to login page if not logged in, else dashboard
    if ($_SESSION['loggedin'] == 1) {
        return true;
    } else {
        // Save current URL for redirect
        $_SESSION['URI'] = $_SERVER['REQUEST_URI'];
        header('Location: '.URL, TRUE, 303);       
    }
}

/**
 * Boolean function to confirm if user is logged in or not
 * 
 * @return Login status
 */
function isLogged() {
    return isset($_SESSION['loggedin']) and !empty($_SESSION['loggedin']) and $_SESSION['loggedin'] == 1; 
}

/**
 * Login function
 */
function login($id) {
    // Simple redirect if a user tries to access a page they have not logged in to
    if (!$_SESSION['loggedin'] == 0 or empty($_SESSION['URI'])) {
        $_SESSION['URI'] = 'home';
    }

    $uri = $_SESSION['URI'];

    $_SESSION['loggedin'] = 1;        
    $_SESSION['userid'] = $id;
    $_SESSION['URI'] = ''; 

    header('Location: ' . $uri, true, 303);        
}

/**
 * Admin Login function
 */
function admin_login($id) {
    // Simple redirect if a user tries to access a page they have not logged in to
    if (!$_SESSION['loggedin'] == 0 or empty($_SESSION['URI'])) {
        $_SESSION['URI'] = 'admin/home';
    }

    $uri = $_SESSION['URI'];

    $_SESSION['loggedin'] = 1;        
    $_SESSION['userid'] = $id;
    $_SESSION['URI'] = ''; 

    header('Location: ' . $uri, true, 303);        
}

/**
 * Logout function
 */
function logout(){
    $_SESSION['loggedin'] = 0;
    $_SESSION['userid'] = -1;        
    $_SESSION['URI'] = '';

    header("Location: ".URL, true, 303);    
}
?>