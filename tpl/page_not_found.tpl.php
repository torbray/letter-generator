<?php

if (isset($_GET['filename']) and !empty($_GET['filename'])) {
    $attempted_page = $_GET['filename'];
} else {
    $attempted_page = '';
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error | ANZ Letter Generator</title>
</head>
<body>
    <h1>Error</h1>
    <p><?php echo $attempted_page ?> was not found.</p>
</body>
</html>