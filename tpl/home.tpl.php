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
            <form class="customer-search" action="" method="post">
                <label for="customer-id">Customer ID:</label>
                <input type="number" class="customer-search" type="text" id="customer-id" name="customer-id" value="<?php echo getCustomerID(); ?>" required>

                <?php
                if ($search_error > 0) {
                    echo "<p>$search_error_msg</p>";
                } else if (isset($_SESSION['customer']) and !empty($_SESSION['customer'] and $_SESSION['customer'] > 0)) {
                    if (!DBController::$is_connected) {
                        $DBC = DBController::getDBConnection();
                    }

                    $query = <<<SQL
                    SELECT first_name, last_name
                    FROM customer
                    WHERE customer_id = ?
                    LIMIT 1;
                    SQL;
            
                    // Bind params to query
                    $stmt = mysqli_prepare($DBC, $query);
                    mysqli_stmt_bind_param($stmt,'i', $_SESSION['customer']);
                    mysqli_stmt_execute($stmt);
                
                    // retrieve mysqli_result object from $stmt
                    $result = mysqli_stmt_get_result($stmt);
                    $rowcount = mysqli_num_rows($result); 

                    if ($rowcount > 0) {
                        $row = mysqli_fetch_assoc($result);

                        // assign variables
                        $first_name = $row['first_name'];
                        $last_name = $row['last_name'];

                        echo <<<END
                        <p>Loaded:
                            <span class="customer-name">$first_name $last_name</span>
                        </p>                        
                        END;
                    }                    
                }
                ?>

                <button class="cta" id="post-search" type="submit" name="submit" value="Search">Search</button>
            </form>
        </section>

        <?php
        if (isset($_SESSION['customer']) and !empty($_SESSION['customer'] and $_SESSION['customer'] > 0)) {
        ?>

        <section class="letter-types">
            <h2>Letter Types</h2>

            <form class="letter-options" action="" method="post">
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

        <section class="test-links">
            <h2>Test Links</h2>
            <a href="test1">Test 1</a>
            <a href="test2">Test 2</a>
        </section>
    </section>
    
</body>
</html>