<?php

if ($_SESSION['user'] == $_SESSION['userid']) {
    header('Location: home', true, 303);
}

checkAdmin();

require_once 'src/controller/dbcontroller.php';

require_once 'src/class/User.php';

// Declaring error variables
$search_error = 0;
$search_error_msg = '';

$user = null;

$search = getUserID();

// Connect to database here
DBController::getDBConnection();

if (isset($_POST['submit']) and !empty($_POST['submit']) and ($_POST['submit'] == 'Delete')) {
    DBController::deleteUser($search);
    $search_error = -1;
} else {
    try {
        $user = DBController::getUser($search);
    } catch (Exception $e) {
        $search_error++;
        $search_error_msg .= $e -> getMessage();
    }
}

?>