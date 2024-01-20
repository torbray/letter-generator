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
                if (!empty($search_error)) {
                    if ($search_error == "invalid") {
                        echo "Invalid customer ID. Please try again.";
                    } else if ($search_error == "unloaded") {
                        echo "Please load a customer ID.";
                    }
                }
                ?>

                <button class="cta" id="post-search" type="submit" name="submit" value="Search">Search</button>
            </form>
        </section>

        <section class="letter-types">
            <h2>Letter Types</h2>

            <form class="letter-options" action="" method="post">
                <label for="letter-type">Choose a Letter Template:</label>
                <select id="letter-type" name="letter-type">
                <!-- For the final prototype, the letter templates locations will be stored in a database-->
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

        <section class="test-links">
            <h2>Test Links</h2>
            <a href="test1">Test 1</a>
            <a href="test2">Test 2</a>
        </section>
    </section>
    
</body>
</html>