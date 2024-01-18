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
                    echo "Invalid customer ID. Please try again.";
                }
                ?>

                <button class="customer-cta" type="submit" name="submit" value="Search">Search</button>
            </form>
        </section>

        <section class="letter-types">
            <h2>Letter Types</h2>

            <table>
                <th>Letter Type</th>
            <!-- For the final prototype, the letter templates locations will be stored in a database-->
            <?php 
            
            $dir = 'tpl/word';
            foreach (scandir($dir) as $file) {
                if (!is_dir($file)) {
                    echo "<p>" . pathinfo($file)['filename'] . "</p>";
                }
            }
            
            ?>
            </table>
        </section>

        <section class="test-links">
            <h2>Test Links</h2>
            <a href="test1">Test 1</a>
            <a href="test2">Test 2</a>
        </section>
    </section>
    
</body>
</html>