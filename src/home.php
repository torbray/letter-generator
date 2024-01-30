<?php

checkUser();

require_once 'src/controller/controller.php';
require_once 'src/controller/dbcontroller.php';

// Declaring error variables
$search_error = 0;
$search_error_msg = '';

// Check if the form is submitted
if (isset($_POST['submit']) and !empty($_POST['submit'])) {
    if ($_POST['submit'] == 'Search') {
        // Get the values from the form
        try {
            $_SESSION['customer'] = DBController::findCustomer($_POST['customer-id']);
        } catch (Exception $e) {
            $search_error++;
            $search_error_msg = $e -> getMessage();
        }
    } else if ($_POST['submit'] == 'Load') {
        if (isset($_SESSION['customer']) and !empty($_SESSION['customer'])) {

            $_SESSION['account'] = $_POST["account-id"];
            // Get the values from the form
            $_SESSION['letter'] = $_POST["letter-type"];

            header('Location: generate-letter', true, 303);
        } else {
            $search_error = "unloaded";
        }
    }
}

?>