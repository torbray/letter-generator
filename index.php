<?php

include 'src/checksession.php';
require_once 'src/controller/dbcontroller.php';

if (isset($_GET['filename']) and !empty($_GET['filename'])) {
    switch ($_GET['filename']) {
        case 'home':
            include 'tpl/home.tpl.php';
            break;

        case 'generate-letter';
            include 'tpl/generate-letter.tpl.php';
            break;

        case 'change-password';
            include 'tpl/change-password.tpl.php';
            break;

        case 'admin':
            include 'tpl/admin.tpl.php';
            break;

        case 'admin/home':
            include 'tpl/admin.home.tpl.php';
            break;

        case 'admin/first-login';
            include 'tpl/admin.first-login.tpl.php';
            break;

        case 'admin/add-user';
            include 'tpl/admin.add-user.tpl.php';
            break;

        case 'admin/delete-user';
            include 'tpl/admin.delete-user.tpl.php';
            break;
            
        case 'logout':
        case 'admin/logout':
            include 'tpl/logout.tpl.php';
            break;

        default:
            if ($_GET['filename'] == '') {
                include 'tpl/login.tpl.php';
            } else {
                header('HTTP/1.0 404 Not Found');
                include 'tpl/page_not_found.tpl.php';
            }
            break;
    }
} else {
    include 'tpl/login.tpl.php';
}

?>