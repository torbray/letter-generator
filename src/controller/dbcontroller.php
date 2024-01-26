<?php

require_once 'config/config.php';

class DBController {

    static $DBC;

    /**
     * Create $DBC connection
     * 
     * @return mysqli|false
     */
    public static function getDBConnection() {
        if (self::$DBC != null) {
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
        self::getDBConnection();

        $query = <<<SQL
        SELECT username
        FROM employee
        WHERE username = ?;
        SQL;
    
        $stmt = mysqli_prepare(self::$DBC, $query);
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

    public static function ifAdminAccounts() {
        self::getDBConnection();

        $query = <<<SQL
        SELECT COUNT(employee_id) AS total
        FROM employee
        INNER JOIN job_title
        ON employee.job_id = job_title.job_id
        WHERE job_title.access_level = 2
        SQL;
    
        $result = mysqli_query(self::$DBC, $query);
        
        if ($result) {
            $rowcount = mysqli_num_rows($result); 

            if ($rowcount > 0) {
                $row = mysqli_fetch_assoc($result);
                return $row['total'] > 0;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /** 
     * Confirm if the login is successful
     * 
     * @return Boolean if user login is successful
     */
    public static function verifyLogin($username, $password) {
        self::getDBConnection();

        // Prepare query
        // Hash column is hashed
        $query = <<<SQL
            SELECT employee_id, password
            FROM employee
            WHERE username = ?
            LIMIT 1;
            SQL;
        
        // Bind params to query
        $stmt = mysqli_prepare(self::$DBC, $query);
        mysqli_stmt_bind_param($stmt,'s', $username);
        mysqli_stmt_execute($stmt);

        // retrieve mysqli_result object from $stmt
        $result = mysqli_stmt_get_result($stmt);
        $rowcount = mysqli_num_rows($result);

        if ($rowcount > 0) {
            $row = mysqli_fetch_assoc($result);

            if (password_verify($password, $row['password'])) {
                return $row['employee_id'];
            }
        }

        return -1;
    }
}

?>