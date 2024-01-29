<?php

checkAdmin();

require_once 'src/controller/dbcontroller.php';

// Initialize database
DBController::getDBConnection();

// Initialize variables
$error = 0; //clear our error flag
$error_message = 'Error: ';


// the data was sent using a form therefore we use the $_POST instead of $_GET
// check if we are saving data first by checking if the submit button exists in the array
if (isset($_POST['submit']) and !empty($_POST['submit']) and ($_POST['submit'] == 'Create')) {  

    // #1 first name
    if (isset($_POST['first-name']) and !empty($_POST['first-name'])) {
        $first_name = DBController::cleanInput($_POST['first-name']); 
    } else {
        $error++; //bump the error flag
        $error_message .= 'Invalid first name '; //append error message
    }

    // #2 last name
    if (isset($_POST['last-name']) and !empty($_POST['last-name'])) {
        $last_name = DBController::cleanInput($_POST['last-name']); 
    } else {
        $error++; //bump the error flag
        $error_message .= 'Invalid last name '; //append error message
    }

    // #3 username
    if (isset($_POST['username']) and !empty($_POST['username'])) {
        $username = DBController::cleanInput($_POST['username']); 

        // Unique username
        // TODO: Validate username
        if (DBController::getUsername($username) != NULL) {
            $error++; //bump the error flag
            $error_message .= 'Usernames must be unique, please try a different username. ';
        }
    } else {
        $error++; //bump the error flag
        $error_message .= 'Invalid username '; //append error message
    }

    // #4 password
    if (isset($_POST['password']) and !empty($_POST['password'])) {
        // TODO: Validate password
        $password = DBController::cleanInput($_POST['password']); 

        // Hash password to PASSWORD_DEFAULT (currently bcrypt)
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    } else {
        $error++; //bump the error flag
        $error_message .= 'Invalid password '; //append error message 
    }

    // #5 job id
    if (isset($_POST['job-title']) and !empty($_POST['job-title'])) {
        // TODO: Validate password
        $job_id = DBController::cleanInput($_POST['job-title']); 
    } else {
        $error++; //bump the error flag
        $error_message .= 'Invalid job title '; //append error message 
    }
    
    // If validation errors, cancel post
    if ($error == 0) {
        DBController::getDBConnection();

        // Add admin to database
        $query = <<<SQL
        INSERT INTO employee (first_name, last_name, username, password, job_id, change_pwd) 
        VALUES (?, ?, ?, ?, ?, 'Y');
        SQL;

        $stmt = mysqli_prepare(DBController::$DBC, $query); //prepare the query
        mysqli_stmt_bind_param($stmt,'ssssi', $first_name, $last_name, $username, $hashed_password, $job_id); 
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Using error system to display success status message
        $error++;
        $error_message .= 'Success! Created an account for ' . $first_name . ' ' . $last_name . '.';
    }
}

?>