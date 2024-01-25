<?php

checkUser();

require_once 'vendor/autoload.php';

require_once 'src/controller/dbcontroller.php';
require_once 'src/controller/lettercontroller.php';

require_once 'src/class/Customer.php';
$customer;

// require_once 'src/class/Account.php';
$account;

if (!isset($_SESSION['letter']) or empty($_SESSION['letter'])) {
    header('Location: home', true, 303);
}

require_once 'word/letter-template.php'; 

// Loading template document
$letter_path = 'tpl/word/' . $_SESSION['letter'] . '.docx';
$template = new LetterTemplate($letter_path);

// Prints variable data from POST form data as debugging
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['submit']) and !empty($_POST['submit'] and $_POST['submit'] == 'review')) {
        // Debugging purposes
        $template -> generatePDF($_POST, "password");
        // echo "Review button has been clicked";
    }
}

// $template = new TemplateProcessor($path);
$getvar = $template -> getVariables();

$letter_tree = [];

foreach ($getvar as $variable) {
    $keys = explode('.', $variable);

    // Categories
    $current_array = &$letter_tree;

    foreach ($keys as $key) {
        // Subvalues
        if (!isset($current_array[$key])) {
            $current_array[$key] = [];
        }

        // Values
        $current_array = &$current_array[$key];
    }
}

// foreach ($letter_tree as $key => $value) {
//     echo $key;
// }

// // Database search
// if (!DBController::$is_connected) {
//     $DBC = DBController::getDBConnection();
// }

// // If customer
// if (array_key_exists("customer", $letter_tree)) {
//     $query = <<<SQL
//     SELECT first_name, last_name, address, title.title_desc
//     FROM customer
//     INNER JOIN title 
//     ON customer.title_id = title.title_id
//     WHERE customer_id = ?
//     LIMIT 1;
//     SQL;

//     // Bind params to query
//     $stmt = mysqli_prepare($DBC, $query);
//     mysqli_stmt_bind_param($stmt,'i', $_SESSION['customer']);
//     mysqli_stmt_execute($stmt);

//     // retrieve mysqli_result object from $stmt
//     $result = mysqli_stmt_get_result($stmt);
//     $rowcount = mysqli_num_rows($result); 

//     if ($rowcount > 0) {
//         $row = mysqli_fetch_assoc($result);

//         // assign variables
//         $customer = new Customer(
//             $row['first_name'],
//             $row['last_name'],
//             $row['address'],
//             $row['title.title_id']
//         );
//     }    
// }

?>
