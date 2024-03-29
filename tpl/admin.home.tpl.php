<?php
require_once 'src/admin.home.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage | Admin | ANZ Letter Generator</title>
    <link rel="stylesheet" href="../tpl/css/styles.css">
    <link rel="stylesheet" href="../tpl/css/admin.home.styles.css">
</head>
<body>
<?php
include "tpl/header.php";
?>
    <section class="main-body">
        <h1>Homepage</h1>
        <section class="user-selection">
            <form class="user-search" action="" method="post">
                <label for="user-id">User ID:</label>
                <input type="number" class="user-search" type="text" id="user-id" name="user-id" value="<?php echo getUserID(); ?>" required>

                <?php
                if ($search_error > 0) {
                    echo '<p class="search-status">' . $search_error_msg . '</p>';
                } else {
                    DBController::getDBConnection();

                    $query = <<<SQL
                    SELECT first_name, last_name
                    FROM employee
                    WHERE employee_id = ?
                    LIMIT 1;
                    SQL;
            
                    // Bind params to query
                    $stmt = mysqli_prepare(DBController::$DBC, $query);
                    mysqli_stmt_bind_param($stmt,'i', $_SESSION['user']);
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
                        <p class="search-status">Loaded:
                            <span class="user-name">$first_name $last_name</span>
                        </p>                        
                        END;
                    }                    
                }
                ?>

                <button class="cta" id="post-search" type="submit" name="submit" value="Search">Search</button>
            </form>
        </section>
        <section class="manage-users">
            <div class="manage-controls">
                <a href="add-user">Add New User</a>
                <?php 
                $userid = getUserID();

                if ($userid > 0 && $userid != $_SESSION['userid']) {
                ?>
                    <a href="delete-user">Delete User</a>
                <?php
                }
                ?>
            </div>
        </section>
    </section>
    
</body>
</html>
