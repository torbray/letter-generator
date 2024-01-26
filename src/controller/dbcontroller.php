<?php

require_once 'config/config.php';

class DBController {

    static $DBC;

    /**
     * Create $DBC connection
     */
    public static function getDBConnection() {
        if (isset(self::$DBC) and !empty(self::$DBC)) {
            return;
        }

        self::$DBC = mysqli_connect(DBHOST, DBUSER, DBPASSWORD, DBDATABASE) or die();

        // check if the connection was good
        if (mysqli_connect_errno()) {
            return "Error: Unable to connect to MySQL. " . mysqli_connect_error();
            exit; // stop processing page further
        };

        return self::$DBC;
    }

    /** 
     * Function to clean input but not validate type and content
     * 
     * @return String MySQL safe string
     */
    public static function cleanInput($data) {  
        return htmlspecialchars(stripslashes(trim($data)));
    }

    public static function validateUsername($data) {

    }

    public static function validatePassword($data) {

    }

    public static function getUsername($check) {
        if (self::$DBC == NULL) {
            $DBC = self::getDBConnection();
        }

        $query = <<<SQL
        SELECT username
        FROM employee
        WHERE username = ?;
        SQL;
    
        $stmt = mysqli_prepare($DBC, $query);
        mysqli_stmt_bind_param($stmt, 's', $check);
    
        $result = mysqli_stmt_get_result($stmt);
        if ($result) {
            $rowcount = mysqli_num_rows($result); 

            if ($rowcount > 0) {
                $row = mysqli_fetch_assoc($result);
                return $row['username'];
            } else {
                return NULL;
            }
        } else {
            return NULL;
        }
    }
}

?>