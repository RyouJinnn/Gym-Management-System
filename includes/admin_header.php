<?php

if(session_status() === PHP_SESSION_NONE){
    session_start();
}

$adminName = $_SESSION['admin_name'] ?? "Administrator";

?>

<div class="admin-header">

    <div class="header-left">

        <h1 id="pageTitle">
            Dashboard
        </h1>

        <p>
            Welcome back,
            <strong><?php echo htmlspecialchars($adminName); ?></strong>
        </p>

    </div>

    <div class="header-right">

        <div class="header-date">

            <i class="fa-solid fa-calendar-days"></i>

            <?php echo date("F d, Y"); ?>

        </div>

        <div class="admin-avatar">

            <i class="fa-solid fa-user-shield"></i>

        </div>

    </div>

</div>