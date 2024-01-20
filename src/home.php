<?php

checkUser();

// Check if the form is submitted
if (isset($_POST['submit']) and !empty($_POST['submit'])) {
    if ($_POST['submit'] == 'Search') {
        // Get the values from the form
        $search = $_POST["customer-id"];

        // Add your authentication logic here (e.g., check against a database)
        // For this example, let's just check if the username and password are not empty
        if ($search >= 1 and $search <= 10) {
            $_SESSION['customer'] = $search;
        } else {
            $search_error = "invalid";
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