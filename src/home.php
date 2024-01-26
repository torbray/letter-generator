<?php

checkUser();

require_once 'src/controller/dbcontroller.php';

// Declaring error variables
$search_error = 0;
$search_error_msg = '';

// Check if the form is submitted
if (isset($_POST['submit']) and !empty($_POST['submit'])) {
    if ($_POST['submit'] == 'Search') {
        // Get the values from the form
        $search = $_POST["customer-id"];

        // Connect to database here
        if ($DBC == null) {
            $DBC = DBController::getDBConnection();
        }
        
        $query = <<<SQL
            SELECT customer_id
            FROM customer
            WHERE customer_id = ?
            LIMIT 1;
            SQL;
    
        // Bind params to query
        $stmt = mysqli_prepare($DBC, $query);
        mysqli_stmt_bind_param($stmt,'i', $search);
        mysqli_stmt_execute($stmt);
    
        // retrieve mysqli_result object from $stmt
        $result = mysqli_stmt_get_result($stmt);
        $rowcount = mysqli_num_rows($result);

        if ($rowcount > 0) {
            $_SESSION['customer'] = $search;
        } else {
            $search_error++;
            $search_error_msg .= 'Customer id does not exist ';
        }

    } else if ($_POST['submit'] == 'Load') {
        if (isset($_SESSION['customer']) and !empty($_SESSION['customer'])) {
            // Get the values from the form
            $_SESSION['letter'] = $_POST["letter-type"];

            header('Location: generate-letter', true, 303);
        } else {
            $search_error = "unloaded";
        }
    }
}

?>