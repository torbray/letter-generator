<?php
require_once 'src/admin.delete-user.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage | Admin | ANZ Letter Generator</title>
    <link rel="stylesheet" href="../tpl/css/styles.css">
    <link rel="stylesheet" href="../tpl/css/admin.delete-user.styles.css">
</head>
<body>
<?php
include "tpl/header.php";
?>
    <section class="main-body">
        <h1>Delete User</h1>
        <div class="user-details-body">
            <section class="user-details">
                <h2>User Details</h2>
                <div class="user-group">
                    <span class="user-detail-key">User ID:</span>
                    <span class="user-detail-value"><?php echo getUserID() ?></span>
                </div>
                <?php
                if ($user != null) {
                ?>
                <div class="user-group">
                    <span class="user-detail-key">First Name:</span>
                    <span class="user-detail-value"><?php echo $user -> first_name; ?></span>
                </div>
                <div class="user-group">
                    <span class="user-detail-key">Last Name:</span>
                    <span class="user-detail-value"><?php echo $user -> last_name; ?></span>
                </div>
                <div class="user-group">
                    <span class="user-detail-key">Username:</span>
                    <span class="user-detail-value"><?php echo $user -> username; ?></span>
                </div>
                <div class="user-group">
                    <span class="user-detail-key">Job Title:</span>
                    <span class="user-detail-value"><?php echo $user -> job_title; ?></span>
                </div>
                <?php
                }
                ?>
            </section>
            <?php
            if ($user != null) {
            ?>
            <form class="user-delete" action="" method="post">
                <button class="cta" id="post-search" type="submit" name="submit" value="Delete">Delete</button>
            </form>
            <?php
            }
            ?>            
        </div>
        <p>
        <?php
        if ($user == null) {
            if ($search_error == -1) {
                echo '<p>Success! User ' . getUserID() . ' has been deleted!</p>';
            } else {
                echo '<p>Error locating user details. Please contact the administrator for further details.</p>';
            }
        }
        ?>
        </p>
    </section>
    
</body>
</html>
