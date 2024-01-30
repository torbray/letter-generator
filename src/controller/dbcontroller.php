<?php

require_once 'config/config.php';

require_once 'src/class/Customer.php';
require_once 'src/class/User.php';
require_once 'src/class/Account.php';
require_once 'src/class/LetterVariable.php';

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
    public static function verifyLogin($username, $password, $admin = false) {
        self::getDBConnection();

        // Prepare query
        // Hash column is hashed
        $query = <<<SQL
            SELECT employee_id, password, job_title.access_level
            FROM employee
            INNER JOIN job_title
            ON employee.job_id = job_title.job_id
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

            // If verifying for admin and access level is not 2, fail the verification
            if ($admin && $row['access_level'] != 2) {
                return -1;
            }

            if (password_verify($password, $row['password'])) {
                return $row['employee_id'];
            }
        }

        return -1;
    }

    /** 
     * Retrieve employee account from database
     * @return User|null
     */
    public static function getUser($search) {
        self::getDBConnection();

        $query = <<<SQL
            SELECT first_name, last_name, username, job_title.job_title
            FROM employee
            INNER JOIN job_title
            ON employee.job_id = job_title.job_id
            WHERE employee_id = ?
            LIMIT 1;
            SQL;

        // Bind params to query
        $stmt = mysqli_prepare(self::$DBC, $query);
        mysqli_stmt_bind_param($stmt,'i', $search);
        mysqli_stmt_execute($stmt);

        // retrieve mysqli_result object from $stmt
        $result = mysqli_stmt_get_result($stmt);
        $rowcount = mysqli_num_rows($result);

        if ($rowcount > 0) {
            $row = mysqli_fetch_assoc($result);
            return new User(
                $row['first_name'],
                $row['last_name'],
                $row['username'],
                $row['job_title']
            );
        } else {
            throw new Exception('User id does not exist ');
        }
    }

    /**
     * Delete a user from the database
     * 
     * @return Boolean if user is successfully deleted
     */
    public static function deleteUser($id) {
        self::getDBConnection();

        try {
            $query = <<<SQL
                DELETE FROM employee 
                WHERE employee_id = ?
                SQL;
            $stmt = mysqli_prepare(self::$DBC, $query); //prepare the query
            mysqli_stmt_bind_param($stmt, 'i', $id); 
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            return true;
        } catch (Exception $e) {
            // $error = $e -> getMessage();
            return false;
        }
    }

    /**
     * Return an array of jobs
     * @return array|null
     */
    public static function getJobs() {
        self::getDBConnection();

        $query = <<<SQL
            SELECT job_id, job_title, access_level
            FROM job_title
            ORDER BY access_level ASC
            SQL;
    
        $result = mysqli_query(self::$DBC, $query);

        if ($result) {
            // Fetch the results into an associative array
            $jobs = array();
            while ($row = mysqli_fetch_assoc($result)) {
                $jobs[] = $row;
            }

            return $jobs;
        } else {
            return NULL;
        }
    }

    public static function getCustomer($search) {
        // Database search
        self::getDBConnection();

        // If customer
        $query = <<<SQL
            SELECT first_name, last_name, address, title.title_desc
            FROM customer
            INNER JOIN title 
            ON customer.title_id = title.title_id
            WHERE customer_id = ?
            LIMIT 1;
            SQL;

        // Bind params to query
        $stmt = mysqli_prepare(self::$DBC, $query);
        mysqli_stmt_bind_param($stmt,'i', $search);
        mysqli_stmt_execute($stmt);

        // retrieve mysqli_result object from $stmt
        $result = mysqli_stmt_get_result($stmt);
        $rowcount = mysqli_num_rows($result); 

        if ($rowcount > 0) {
            $row = mysqli_fetch_assoc($result);

            // assign variables
            $customer = new Customer(
                $row['first_name'],
                $row['last_name'],
                $row['address'],
                $row['title_desc']
            );

            return $customer;
        } else {
            return null;
        }
    }

    public static function getVariable($search, $key) {
        // Database search
        self::getDBConnection();

        // If customer
        $query = <<<SQL
            SELECT method, box
            FROM letter_variable
            WHERE category = ?
            AND letter_key = ?
            LIMIT 1;
            SQL;

        // Bind params to query
        $stmt = mysqli_prepare(self::$DBC, $query);
        mysqli_stmt_bind_param($stmt,'ss', $search, $key);
        mysqli_stmt_execute($stmt);

        // retrieve mysqli_result object from $stmt
        $result = mysqli_stmt_get_result($stmt);
        $rowcount = mysqli_num_rows($result); 

        if ($rowcount > 0) {
            $row = mysqli_fetch_assoc($result);

            // assign variables
            $letter_variable = new LetterVariable(
                $search,
                $key,
                $row['method'],
                $row['box']
            );

            return $letter_variable;
        } else {
            return null;
        } 
    }

    /**
     * Return an array of jobs
     * @return array|null
     */
    public static function getAccountsFromCustomer($customer) {
        self::getDBConnection();

        $query = <<<SQL
            SELECT account.account_id, account_type.type_name, balance, open_date
            FROM relationship
            INNER JOIN account
            ON account.account_id = relationship.account_id
            INNER JOIN account_type
            ON account_type.type_id = account.type_id
            WHERE customer_id = ?
            ORDER BY account_id ASC
            SQL;
    
        // Bind params to query
        $stmt = mysqli_prepare(self::$DBC, $query);
        mysqli_stmt_bind_param($stmt,'i', $customer);
        mysqli_stmt_execute($stmt);

        // retrieve mysqli_result object from $stmt
        $result = mysqli_stmt_get_result($stmt);

        if ($result) {
            // Fetch the results into an associative array
            $accounts = array();
            while ($row = mysqli_fetch_assoc($result)) {
                $accounts[] = new Account(
                    $row['account_id'],
                    $row['type_name'],
                    $row['balance'],
                    $row['open_date']
                );
            }

            return $accounts;
        } else {
            return NULL;
        }
    }

    /**
     * Retrieves account from database
     * @param int Account number
     * 
     * @return Account
     * @throws Exception when user id does not exist
     */
    public static function getAccount($search) {
        self::getDBConnection();

        $query = <<<SQL
            SELECT account_type.type_name, balance, open_date
            FROM account
            INNER JOIN account_type
            ON account.type_id = account_type.type_id
            WHERE account_id = ?
            LIMIT 1;
            SQL;

        // Bind params to query
        $stmt = mysqli_prepare(self::$DBC, $query);
        mysqli_stmt_bind_param($stmt,'i', $search);
        mysqli_stmt_execute($stmt);

        // retrieve mysqli_result object from $stmt
        $result = mysqli_stmt_get_result($stmt);
        $rowcount = mysqli_num_rows($result);

        if ($rowcount > 0) {
            $row = mysqli_fetch_assoc($result);
            return new Account(
                $search,
                $row['type_name'],
                $row['balance'],
                $row['open_date']
            );
        } else {
            throw new Exception('Account does not exist ');
        }
    }

    /**
     * Finds customer from id
     * 
     * @return int Custoemr_id if validated
     * @throws Exception if customer_id is not found
     */
    public static function findCustomer($search) {
        // Connect to database here
        DBController::getDBConnection();

        $query = <<<SQL
            SELECT customer_id
            FROM customer
            WHERE customer_id = ?
            LIMIT 1;
            SQL;
    
        // Bind params to query
        $stmt = mysqli_prepare(DBController::$DBC, $query);
        mysqli_stmt_bind_param($stmt,'i', $search);
        mysqli_stmt_execute($stmt);
    
        // retrieve mysqli_result object from $stmt
        $result = mysqli_stmt_get_result($stmt);
        $rowcount = mysqli_num_rows($result);

        if ($rowcount > 0) {
            return $search;
        } else {
            throw new Exception('Customer id does not exist ');
        }
    }
}

?>
