<?php

checkAdmin();

require_once 'src/controller/dbcontroller.php';

// Declaring error variables
$search_error = 0;
$search_error_msg = '';

// Check if the form is submitted
if (isset($_POST['submit']) and !empty($_POST['submit'])) {
    if ($_POST['submit'] == 'Search') {
        // Get the values from the form
        $search = $_POST["user-id"];

        // Connect to database here
        DBController::getDBConnection();
        
        $query = <<<SQL
            SELECT employee_id
            FROM employee
            WHERE employee_id = ?
            LIMIT 1;
            SQL;
    
        // Bind params to query
        $stmt = mysqli_prepare(DBController::$DBC, $query);
        mysqli_stmt_bind_param($stmt,'i', $search);
        mysqli_stmt_execute($stmt);
    
        // retrieve mysqli_result object from $stmt
        $result = mysqli_stmt_get_result($stmt);
        $rowcount = mysqli_num_rows($result);

        if ($rowcount > 0) {
            $_SESSION['user'] = $search;
        } else {
            $search_error++;
            $search_error_msg .= 'User ID does not exist.';
        }
    }
}

?>