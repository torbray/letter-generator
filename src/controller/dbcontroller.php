<?php

require_once 'config/config.php';

class DBController {

    static $is_connected = false;

    public static function getDBConnection() {
        // Connect to database here
        $DBC = mysqli_connect(DBHOST, DBUSER, DBPASSWORD, DBDATABASE) or die();

        //insert DB code from here onwards
        //check if the connection was good
        if (mysqli_connect_errno()) {
            throw new ErrorException("Error: Unable to connect to MySQL. " . mysqli_connect_error());
        }

        self::$is_connected = true;

        return $DBC;
    }
}

?>