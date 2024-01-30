<?php

require_once 'vendor/autoload.php';

use PhpOffice\PhpWord\IOFactory;       
use PhpOffice\PhpWord\TemplateProcessor;

class LetterTemplate extends TemplateProcessor {

    public function generatePDF($values) {

        // Init variable
        $password = null;
        $cache_directory = 'var/cache/';

        // Set date value
        $this -> setValue("date", date("d/m/Y"));
        
        // Word template replacement for each key/ value pair
        foreach ($values as $key => $value) {
            if ($key == "submit") {
                continue;
            } else if ($key == "letter-password") {
                $password = $value;
                continue;
            }
            // Replace key - with .
            $desc = str_replace("-", ".", $key);

            // Setting value for word replacement
            $value = str_replace("\n", '</w:t><w:br/><w:t>', $value);
            $this -> setValue($desc, $value);
        }
        
        // Saves temporary Word .docx file in var/cache
        $filename = "test template result";
        $this -> saveAs($cache_directory . $filename . '.docx');
        
        \PhpOffice\PhpWord\Settings::setPdfRendererName(\PhpOffice\PhpWord\Settings::PDF_RENDERER_MPDF);
        \PhpOffice\PhpWord\Settings::setPdfRendererPath('vendor/mpdf/mpdf');
        
        // Saves temporary unencrypted .pdf file in var/cache
        $phpWord = IOFactory::load($cache_directory . $filename . '.docx');
        $writer = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'PDF');
        $writer -> save($cache_directory . $filename . '.pdf');
        
        // Include the main TCPDF library and TCPDI.
        require_once('vendor/tecnickcom/tcpdf/tcpdf.php');
        require_once('vendor/rafikhaceb/tcpdi/tcpdi.php');
        
        // Create new PDF document.
        $pdf = new TCPDI(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        // Encrypted PDF document
        $pdf -> SetProtection(
            ['print', 'modify', 'copy', 'annot-forms', 'fill-forms', 'extract', 'assemble', 'print-high'],
            $password, $password, 3
        );
        
        // Add a page from a PDF by file path.
        $pdf -> AddPage();
        $pdf -> setSourceFile($cache_directory . $filename . '.pdf');
        $idx = $pdf -> importPage(1);
        $pdf -> useTemplate($idx);
        
        file_put_contents('var/cache/output.pdf', $pdf -> Output('', 'S'));
        
        // save as a random file in temp file
        header('Content-Description: File Transfer');
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename=output.pdf');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($cache_directory . 'output.pdf'));
        
        $file = $cache_directory . 'output.pdf';
        
        // Send for download
        readfile($file);
        
        // Delete all temporary files
        // Get all files in the cache directory
        unlink($cache_directory . 'test template result.docx');
        unlink($cache_directory . 'test template result.pdf');
        unlink($cache_directory . 'output.pdf');
    }
}

?>