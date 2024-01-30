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

    if (!isset($_SESSION['customer'])) {
        $_SESSION['customer'] = 0;
    }

    if (!isset($_SESSION['access'])) {
        $_SESSION['access'] = 0;
    }

    if (!isset($_SESSION['letter'])) {
        $_SESSION['letter'] = '';
    }

    if (!isset($_SESSION['user'])) {
        $_SESSION['user'] = 0;
    }

    if (!isset($_SESSION['account'])) {
        $_SESSION['account'] = 0;
    }

    if (!isset($_SESSION['change-password'])) {
        $_SESSION['change-password'] = true;
    }
}

/**
 * Function to check if the user is logged else send to the login page 
 */
function checkUser() {
    // Redirect user to login page if not logged in, else dashboard
    if (isLogged()) {
        return true;
    } else {
        // Save current URL for redirect
        $_SESSION['URI'] = $_SERVER['REQUEST_URI'];
        header('Location: ' . URL, TRUE, 303);       
    }
}

/**
 * Function to check if the admin is logged else send to the login page 
 */
function checkAdmin() {
    // Redirect user to login page if not logged in, else dashboard
    if (isAdmin()) {
        return true;
    } else {
        // Save current URL for redirect
        $_SESSION['URI'] = $_SERVER['REQUEST_URI'];
        header('Location: ' . URL . 'admin', TRUE, 303);       
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
function login($id, $change_password) {
    // Simple redirect if a user tries to access a page they have not logged in to
    if (!$_SESSION['loggedin'] == 0 or empty($_SESSION['URI'])) {
        $_SESSION['URI'] = 'home';
    }

    $uri = $_SESSION['URI'];

    resetSessionValues();
    $_SESSION['loggedin'] = 1;        
    $_SESSION['userid'] = $id;
    $_SESSION['access'] = 1;
    $_SESSION['change-password'] = $change_password;

    if (isChangingPassword()) {
        header('Location: change-password', true, 303);
    } else {
        header('Location: ' . $uri, true, 303);
    }
}

/**
 * Admin Login function
 */
function admin_login($id, $change_password) {
    // Simple redirect if a user tries to access a page they have not logged in to
    if (!$_SESSION['loggedin'] == 0 or empty($_SESSION['URI'])) {
        $_SESSION['URI'] = 'admin/home';
    }

    echo '<p>' . $_SESSION['URI'] . '</p>';
    echo 'Location: ' . URL . $_SESSION['URI'];

    $uri = $_SESSION['URI'];

    resetSessionValues();
    $_SESSION['loggedin'] = 1;        
    $_SESSION['userid'] = $id;
    $_SESSION['access'] = 2;
    $_SESSION['change-password'] = $change_password;

    if (isChangingPassword()) {
        header('Location: change-password', true, 303);
    } else {
        header('Location: ' . URL . $uri, true, 303);
    }     
}

/**
 * Logout function
 */
function logout(){
    $_SESSION['loggedin'] = 0;
    $_SESSION['userid'] = -1;    
    resetSessionValues();

    header("Location: " . URL, true, 303);    
}

function getCustomerID() {
    if (isset($_SESSION['customer'])) {
        return $_SESSION['customer'];
    } else {
        return '';
    }
}

function getUserID() {
    if (isset($_SESSION['user'])) {
        return $_SESSION['user'];
    } else {
        return '';
    }
}

function getEmployeeID() {
    if (isset($_SESSION['userid'])) {
        return $_SESSION['userid'];
    } else {
        return '';
    }
}

function getAccountID() {
    if (isset($_SESSION['account'])) {
        return $_SESSION['account'];
    } else {
        return '';
    }
}

function isChangingPassword() {
    return $_SESSION['change-password'];
}

function isAdmin() {
    return $_SESSION['access'] == 2;
}

function resetSessionValues() {
    $_SESSION['URI'] = '';
    $_SESSION['customer'] = 0; 
    $_SESSION['user'] = 0; 
    $_SESSION['letter'] = '';
    $_SESSION['access'] = 0;
    $_SESSION['account'] = 0;
    $_SESSION['change-password'] = false;
}

?>