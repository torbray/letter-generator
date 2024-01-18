<?php
// ZIP file
$zip = new ZipArchive();
if ($zip -> open('tmp/' . $fileName . '.zip', ZipArchive::CREATE) === TRUE) {
    // if ($zip -> setPassword('password')) {
    //     throw new RuntimeException('Set password failed.');
    // }; //set default password

    // $zip -> addFile("tpl/word/test.txt"); //add file
    // $zip -> setEncryptionName('test.txt', ZipArchive::EM_AES_256); //encrypt it

    $zip -> addFile('tmp/' . $fileName . '.pdf', $fileName . '.pdf'); //add file
    $zip -> setEncryptionName($fileName . '.pdf', ZipArchive::EM_AES_256, "password"); //encrypt it

    $zip -> close();

    echo "Added $fileName.pdf with the same password\n";
} else {
    echo "KO\n";
}

// save as a random file in temp file
header('Content-Description: File Transfer');
header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename=' . $fileName . '.zip');
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
header('Content-Length: ' . filesize('tmp/' . $fileName . '.zip'));

$file = 'tmp/' . $fileName . '.zip';
// $file -> save('php://output', 'ZIP');
readfile($file);

?>