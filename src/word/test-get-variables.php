<?php

use PhpOffice\PhpWord\IOFactory;

require_once 'vendor/autoload.php';

$path = 'tpl/word/Account Confirmation Letter Template.docx';

// Loading template document
$template = new \PhpOffice\PhpWord\TemplateProcessor($path);
$getvar = $template -> getVariables();

echo 'Number of Variables: ' . count($getvar) . '<br>';
for ($i = 0; $i < count($getvar); $i++) {
    echo 'varname [' . $i . ']: ' . $getvar[$i] . '<br>';
}

?>
