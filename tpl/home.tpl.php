<?php
require_once 'src/home.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage | ANZ Letter Generator</title>
    <link rel="stylesheet" href="tpl/css/styles.css">
    <link rel="stylesheet" href="tpl/css/home.styles.css">
</head>
<body>
<?php
include "tpl/header.php";
?>
    <section class="main-body">
        <h1>Homepage</h1>

        <section class="customer-selection">
            <form class="form-search" action="" method="post">
                <label for="customer-id">Customer ID:</label>
                <input type="number" class="form-search" type="text" id="customer-id" name="customer-id" value="<?php echo getCustomerID(); ?>" required>

                <?php
                if ($search_error > 0) {
                    echo '<p class="search-status">' . $search_error_msg . '</p>';
                } else if (isset($_SESSION['customer']) and !empty($_SESSION['customer'] and $_SESSION['customer'] > 0)) {
                    // Get customer
                    $customer = DBController::getCustomer($_SESSION['customer']);

                    // assign variables
                    $first_name = $customer -> first_name;
                    $last_name = $customer -> last_name;

                    // Confirmation via status that customer is loaded
                    echo <<<END
                    <p class="search-status">Loaded:
                        <span class="customer-name">$first_name $last_name</span>
                    </p>                        
                    END;                 
                }
                ?>

                <button class="cta" id="post-search" type="submit" name="submit" value="Search">Search</button>
            </form>
        </section>

        <?php
        if (isset($_SESSION['customer']) and !empty($_SESSION['customer'] and $_SESSION['customer'] > 0)) {
        ?>

        <section class="letter-types">
            <form class="letter-options" action="" method="post">
                <label for="account-id">Select an Account:</label>
                <?php
                    $options = DBController::getAccountsFromCustomer($_SESSION['customer']);

                    // Disable select if no accounts belonging to customer
                    if ($options != null) {
                ?>
                <select name="account-id" id="account-id" <?php echo $is_disabled; ?>>
                    <?php
                        foreach ($options as $account) {
                            $account_id = $account -> account_id;
                            $account_number = $account -> getNumber();
                            $account_type = $account -> getType();

                            $html = <<<END
                            "<option value="$account_id">
                                $account_type ($account_number)
                            </option>";
                            END;
                            echo $html;
                        }
                    ?>
                </select>
                <?php
                    } else {
                ?>
                <p class="account-none-displayed">No accounts to display.</p>
                <?php
                    }

                ?>

                <h2>Letter Types</h2>

                <label for="letter-type">Choose a Letter Template:</label>
                <select id="letter-type" name="letter-type">
                <?php 
                
                $dir = 'tpl/word';
                foreach (scandir($dir) as $file) {
                    if (!is_dir($file)) {
                        echo "<option value=\"" . pathinfo($file)['filename'] . "\">" . pathinfo($file)['filename'] . "</option>";
                    }
                }
                
                ?>
                </select>

                <button class="cta" id="post-load" type="submit" name="submit" value="Load">Load</button>
            </form>
        </section>

        <?php
        }
        ?>

    </section>
    
</body>
</html>