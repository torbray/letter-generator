<?php

require_once 'vendor/autoload.php';

$path = 'tpl/word/Account Confirmation Letter Template.docx';

require_once 'word/letter-template.php'; 

// Loading template document
$template = new LetterTemplate($path);

// $template = new TemplateProcessor($path);
$getvar = $template -> getVariables();

// // Code to visualize how many variables in the document
// echo 'Number of Variables: ' . count($getvar) . '<br>';
// for ($i = 0; $i < count($getvar); $i++) {
//     echo 'varname [' . $i . ']: ' . $getvar[$i] . '<br>';
// }

?>
