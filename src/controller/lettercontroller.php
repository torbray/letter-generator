<?php

require_once 'src/controller/dbcontroller.php';

class LetterController {

    static $customer = null;
    static $account = null;
    static $user = null;

    public static function generateInput(...$values) {
        self::$customer = DBController::getCustomer($_SESSION['customer']);
        self::$user = DBController::getUser($_SESSION['userid']);

        // $long_inputs = ["customer-address", "account-name"];

        // Labels
        $category = $values[0];

        $labels = array_reverse(array_slice($values, 1));

        $sublabels = self::convert_to_short_key($values);

        $label_title = ucwords(implode(" ", $labels));
        $full_name = implode("-", $values);

        // Init variables
        $input_type = '';
        $result = '';

        // Json
        $json = self::readJsonFile('src/word/letter-variables.json');

        // Check if key is present in category and access metadata
        if (isset($json[$category])) {
            $variable_info = $json[$category];

            // Access nested information
            if (isset($variable_info[$sublabels])) {

                $variable_metadata = $variable_info[$sublabels];

                // Print the information
                // echo "Key: " . $variable_metadata['key'] . "\n";
                // echo "Input Type: " . $variable_metadata['input-type'] . "\n";

                // Retrieve the function name
                $function_name = $variable_metadata['function'];

                // identify relevant object via switch case
                $object = null;
                
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
                if ($object != null && method_exists($object, $function_name)) {
                    $result = $object -> $function_name();
                } else {
                    // echo "Function '$function_name' does not exist in the Customer class.";
                }

                // Retrieve the input type
                $input_type = $variable_metadata['input-type'];
            }
        }

        // Check if the function exists in the Customer class
        if ($input_type == 'long') {
            // Create label and input
            $input_result = <<<END
                <textarea name="$full_name" required>$result</textarea>
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

    // Function to read and decode JSON content
    public static function readJsonFile($filename) {
        $json_content = file_get_contents($filename);
        $decoded_json = json_decode($json_content, true);
    
        // Check if decoding was successful
        if ($decoded_json === null && json_last_error() !== JSON_ERROR_NONE) {
            die("Error decoding JSON in $filename: " . json_last_error_msg());
        }
    
        return $decoded_json;
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