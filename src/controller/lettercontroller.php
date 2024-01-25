<?php

class LetterController {

    public static function generateInput(...$values) {
        $long_inputs = ["customer-address", "account-name"];

        // Labels
        $labels = array_reverse(array_slice($values, 1));

        $label_title = ucwords(implode(" ", $labels));
        $full_name = implode("-", $values);

        $input_tag = 'input';
        if (in_array($full_name, $long_inputs)) {
            $input_tag = 'textarea';
        }

        // Create label and input
        $result = <<<END
        <div class="letter-variable">
            <label for="$full_name">$label_title:</label>
            <$input_tag name="$full_name" required>
        
        END;

        if (in_array($full_name, $long_inputs)) {
            $result .= '</textarea>';
        }

        return $result . "</div>";
    }
}

?>