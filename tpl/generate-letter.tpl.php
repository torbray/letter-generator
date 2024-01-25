<?php
require_once 'src/generate-letter.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Letter | ANZ Letter Generator</title>
    <link rel="stylesheet" href="tpl/css/styles.css">
    <link rel="stylesheet" href="tpl/css/generate-letter.styles.css">
</head>
<body>
<?php
include "tpl/header.php";
?>
    <section class="main-body">
        <h1><?php echo $_SESSION['letter'] ?></h1>
        <p class="letter-subtitle">Customer ID: 
            <?php 
            echo '<span class="letter-subtitle-id">' . $_SESSION['customer'] . '</span>'; 
            ?>
        </p>

        <section class="letter-values">
            <form action="" method="post" class="letters">
                <?php
                
                function generateInput(...$values) {
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
                
                // Letter template variables
                foreach ($letter_tree as $category => $data) {
                    
                    if (is_array($data) && !empty($data)) {
                        echo '<div class="letter-category">';
                        echo '<h2>' . ucwords($category) . '</h2>' . PHP_EOL;
                    } else {
                        continue;
                    }

                    foreach ($data as $value => $data2) {
                        if (is_array($data2) && !empty($data2)) {
                            foreach ($data2 as $subvalue => $data3) {
                                echo generateInput($category, $value, $subvalue);
                            }
                        } else {
                            echo generateInput($category, $value);
                        }
                    }
                    echo '</div>';

                }
                
                ?>

                <button class="cta" id="post-load" type="submit" name="submit" value="review">Review</button>
            </form>
        </section>
    </section>
    
</body>
</html>