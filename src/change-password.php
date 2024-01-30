<?php

// Initialize variables
$error = 0; //clear our error flag
$error_message = 'Error: ';

// the data was sent using a form therefore we use the $_POST instead of $_GET
// check if we are saving data first by checking if the submit button exists in the array
if (isset($_POST['submit']) and !empty($_POST['submit']) and ($_POST['submit'] == 'Change')) {

    // #1 password
    if (isset($_POST['password']) and !empty($_POST['password'])) {
        // TODO: Validate password
        $password = DBController::cleanInput($_POST['password']); 

        // Get hashed password from database
        $prev = DBController::getHash(getEmployeeID());

        $hashed_password = null;

        if (!empty($prev)) {
            if (!password_verify($password, $prev)) {
                // Hash password to PASSWORD_DEFAULT (currently bcrypt)
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            } else {
                $error++;
                $error_message .= 'Please use a new password. '; //append error message
            }
        } else {
            $error++;
            $error_message .= 'Please contact the administrator for further information. '; //append error message
        }

    } else {
        $error++; //bump the error flag
        $error_message .= 'Invalid password '; //append error message 
    }
    
    // If validation errors, cancel post
    if ($error == 0) {
        DBController::getDBConnection();

        // Add admin to database
        $query = <<<SQL
        UPDATE employee 
        SET password = ?, change_pwd = 'N'
        WHERE employee_id = ?;
        SQL;

        $stmt = mysqli_prepare(DBController::$DBC, $query); //prepare the query
        mysqli_stmt_bind_param($stmt,'si', $hashed_password, getEmployeeID()); 
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        if (isAdmin()) {
            header('Location: ' . URL . 'admin/home', true, 303);
        } else {
            header('Location: home', true, 303);
        }

    }
}

?>