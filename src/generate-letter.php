<?php

checkUser();

require_once 'vendor/autoload.php';

require_once 'src/controller/lettercontroller.php';


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
        // var_dump($_POST);

        $template -> generatePDF($_POST);
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
        if ($key == "date") {
            continue;
        }

        // Subvalues
        if (!isset($current_array[$key])) {
            $current_array[$key] = [];
        }

        // Values
        $current_array = &$current_array[$key];
    }
}

?>
