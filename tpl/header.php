<header class="top-bar">
    <?php
    $direct;
    if (isLogged()) {
        $direct = URL . "/home";
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
        <button class="title-button" onclick="window.location.href='logout';">Log Out</button>
    <?php
    }
    ?>
</header>