<?php

define ('URL', 'http://localhost/letter-generator/');

include 'src/checksession.php';

if (isset($_GET['filename']) and !empty($_GET['filename'])) {
    switch ($_GET['filename']) {
        case 'home':
            require 'src/home.php';
            include 'tpl/home.tpl.php';
            break;

        case 'admin':
            require 'src/admin.php';
            include 'tpl/admin.tpl.php';
            break;

        case 'admin/home':
            include 'tpl/admin.home.tpl.php';
            break;

        case 'logout':
        case 'admin/logout':
            include 'tpl/logout.tpl.php';
            break;
        
        case 'test1':
            include 'src/word/test-replacement.php';
            break;
        
        case 'test2':
            include 'src/word/test-zip.php';
            break;

        default:
            if ($_GET['filename'] == '') {
                require 'src/login.php';
                include 'tpl/login.tpl.php';
            } else {
                header('HTTP/1.0 404 Not Found');
                include 'tpl/page_not_found.tpl.php';
            }
            break;
    }
} else {
    require 'src/login.php';
    include 'tpl/login.tpl.php';
}

?>