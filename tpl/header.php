<header class="top-bar">
    <a class="title-logo" href="<?php echo URL; ?>">Letter Generator<?php 
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