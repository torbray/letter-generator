<?php
require_once 'src/admin.add-user.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create User Account | Admin | ANZ Letter Generator</title>
    <link rel="stylesheet" href="../tpl/css/styles.css">
    <link rel="stylesheet" href="../tpl/css/admin.add-user.styles.css">

    <script type="text/javascript" src="../tpl/js/admin.add-user.js" defer></script>
</head>
<body>
<?php
include "tpl/header.php";
?>
    <section class="main-body">
        <h1>Create new user account</h1>
        <p>Add a new user to the system.</p>
        <form action="" onsubmit="return validateForm()" method="post">
            <div class="field-group">
                <label for="first-name">First Name:</label>
                <input type="text" id="first-name" name="first-name" onkeyup="validateFirstName();" required />
                <span class="status-message" id="first-name-status"></span>
            </div>

            <div class="field-group">
                <label for="last-name">Last Name:</label>
                <input type="text" id="last-name" name="last-name" onkeyup="validateLastName();" required />
                <span class="status-message" id="last-name-status"></span>
            </div>

            <div class="field-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" onkeyup="validateUsername();" required />
                <span class="status-message" id="username-status"></span>
            </div>

            <div class="field-group">
                <label for="password">Temporary Password:</label>
                <input type="text" id="password" name="password" value="Summer1!" readonly required />
                <span class="status-message" id="password-status"></span>
            </div>

            <div class="field-group">
                <label for="job-title">Job Title:</label>
                <select name="job-title" id="job-title">
                    <?php
                    $options = DBController::getJobs();

                    if ($options != null) {
                        foreach ($options as $key => $option) {
                            $job_id = $option['job_id'];
                            $job_title = $option['job_title'];
                            $access_level = $option['access_level'];

                            $html = <<<END
                            "<option value="$job_id">
                                $job_title (Access Level: $access_level)
                            </option>";
                            END;
                            echo $html;
                        }
                    }
                    ?>
                </select>
                <span class="status-message" id="username-status"></span>
            </div>

            <span class="main-status">
                <?php 
                if ($error > 0) {
                    echo $error_message;
                }
                ?>
            </span>
            <div class="button-controls">
                <a class="button-back" href="home">Back</a>
                <button class="primary-cta" type="submit" name="submit" value="Create">Create</button>
            </div>
        </form>
    </section>
</body>
</html>