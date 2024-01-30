<?php

require_once 'src/controller/dbcontroller.php';

class LetterController {

    static $customer = null;
    static $account = null;
    static $user = null;

    public static function generateInput(...$values) {

        self::$customer = DBController::getCustomer($_SESSION['customer']);

        if ($_SESSION['account'] > 0) {
            self::$account = DBController::getAccount($_SESSION['account']);

            // Declare dynamically an account name
            // See Account class for further info
            if (!empty(self::$customer)) {
                self::$account -> setAccountName(self::$customer -> generateAccountName());
            }
        }

        self::$user = DBController::getUser($_SESSION['userid']);

        // Labels
        $category = $values[0];

        $sublabels = self::convert_to_short_key($values);

        $labels = array_reverse(array_slice($values, 1));        

        // Label title
        $label_title = ucwords(implode(" ", $labels));

        // Value in input
        $full_name = implode("-", $values);

        $letter_variable = DBController::getVariable($category, $sublabels);

        // Init variable
        $result = '';

        if ($letter_variable != null) {            
            switch ($category) {
                case 'customer':
                    $object = self::$customer;
                    break;
                case 'account':
                    $object = self::$account;
                    break;
                case 'consultant':
                    $object = self::$user;
                    break;
            }

            // Check if the function exists in the Customer class
            if ($object != null && method_exists($object, $letter_variable -> method)) {
                $method = $letter_variable -> method;

                $result = $object -> $method();
            } else {
                // echo "Function '$function_name' does not exist in the Customer class.";
            }
        }

        // Check if the function exists in the Customer class
        if ($letter_variable -> box == 'long') {
            // Create label and input
            $input_result = <<<END
                <textarea name="$full_name">$result</textarea>
                END;

        } else {
            // echo "Function '$function_name' does not exist in the Customer class.";
            $input_result = <<<END
                <input name="$full_name" value="$result" required />
                END;
        }

        return <<<END
        <div class="letter-variable">
            <label for="$full_name">$label_title:</label>
            $input_result
        </div>                
        END;

    }

    public static function convert_to_short_key($input) {
        // $parts = explode('-', $input);
    
        // Check if the input has at least two parts
        if (count($input) >= 3) {
            // Remove the first part and join the rest with '-'
            $result = implode('-', array_slice($input, 1));
    
            return $result;
        } else {
            // Return the original input if it doesn't match the expected format
            return $input[1];
        }
    }
}

?>