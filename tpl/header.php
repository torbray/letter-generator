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
    <div class="header-display">
        <?php
        if (isLogged()) {
        ?>
        <p class="header-label">Employee ID: 
            <span class="header-value"><?php echo $_SESSION['userid'] ?></span>
        </p>
        <?php
        }

        if ($_SESSION['customer'] > 0) {
        ?>
        <p class="header-label">Customer ID: 
            <span class="header-value"><?php echo $_SESSION['customer'] ?></span>
        </p>
        <?php
        }
        
        if (isLogged()) {
        ?>
    </div>
    <button class="title-button" onclick="window.location.href='logout';">Log Out</button>
    <?php
    }
    ?>
</header>