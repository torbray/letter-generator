<?php

require_once 'src/controller/dbcontroller.php';

// Initialize variables
$error = 0; //clear our error flag
$error_message = 'Error ';

// the data was sent using a form therefore we use the $_POST instead of $_GET
// check if we are saving data first by checking if the submit button exists in the array
if (isset($_POST['submit']) and !empty($_POST['submit']) and ($_POST['submit'] == 'Create')) {     

    // #1 first name
    // #2 last name

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

    // echo "Success!";
    
    // If validation errors, cancel post
    if ($error != 0) {
        echo "<h2>$error_message</h2>".PHP_EOL;
    } else {

        $query = <<<SQL
        INSERT INTO employee (first_name, last_name, username, password) 
        VALUES (?, ?);
        SQL;

        $stmt = mysqli_prepare($DBC, $query); //prepare the query
        mysqli_stmt_bind_param($stmt,'ss', $username, $hashed_password); 
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);    
        echo "<h2>User added.</h2>";  
    }
}

?>