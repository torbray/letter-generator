<header class="top-bar">
    <?php
    $direct;
    if (isAdmin()) {
        $direct = URL . "admin/home";
    } else if (isLogged()) {
        $direct = URL . "home";
    } else {
        $direct = URL;
    }

    ?>

    <a class="title-logo" href="<?php echo $direct; ?>">Letter Generator<?php 
    if (str_contains($_SERVER['REQUEST_URI'], "/admin")) {
        echo " - Admin";
    }
    ?></a>
    <?php
    if (isLogged()) {
    ?>
        <p>Employee ID: <?php echo $_SESSION['userid'] ?></p>
        <button class="title-button" onclick="window.location.href='logout';">Log Out</button>
    <?php
    }
    ?>
</header>