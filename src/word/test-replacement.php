<?php

use PhpOffice\PhpWord\IOFactory;

require_once 'vendor/autoload.php';

// Loading template document
$template = new \PhpOffice\PhpWord\TemplateProcessor('tpl/word/test template.docx');

// Setting value for word replacement
$template -> setValue('descriptor', 'very moist');

$fileName = "test template result";
$template -> saveAs('tmp/' . $fileName . '.docx');

\PhpOffice\PhpWord\Settings::setPdfRendererName(\PhpOffice\PhpWord\Settings::PDF_RENDERER_MPDF);
\PhpOffice\PhpWord\Settings::setPdfRendererPath('vendor/mpdf/mpdf');

// Save locally
$phpWord = IOFactory::load('tmp/' . $fileName . '.docx');
$writer = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'PDF');
$writer -> save('tmp/' . $fileName . '.pdf');

// Include the main TCPDF library and TCPDI.
require_once('vendor/tecnickcom/tcpdf/tcpdf.php');
require_once('vendor/rafikhaceb/tcpdi/tcpdi.php');

// Create new PDF document.
$pdf = new TCPDI(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

$pdf -> SetProtection(
    ['print', 'modify', 'copy', 'annot-forms', 'fill-forms', 'extract', 'assemble', 'print-high'],
    'password1', 'password2', 3
);

// Add a page from a PDF by file path.
$pdf -> AddPage();
$pdf -> setSourceFile('tmp/' . $fileName . '.pdf');
$idx = $pdf -> importPage(1);
$pdf -> useTemplate($idx);

file_put_contents('tmp/output.pdf', $pdf -> Output('', 'S'));

// save as a random file in temp file
header('Content-Description: File Transfer');
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename=output.pdf');
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
header('Content-Length: ' . filesize('tmp/output.pdf'));

$file = 'tmp/output.pdf';
// $file -> save('php://output', 'PDF');
readfile($file);

?>
