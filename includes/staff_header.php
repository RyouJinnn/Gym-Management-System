<?php

if(session_status() === PHP_SESSION_NONE){
    session_start();
}

$staffName = $_SESSION['staff_name'] ?? "Staff";

?>

<div class="admin-header">

    <div class="header-left">

        <h1 id="pageTitle">
            Dashboard
        </h1>

        <p>
            Welcome,
            <strong><?php echo htmlspecialchars($staffName); ?></strong>
        </p>

    </div>

    <div class="header-right">

        <div class="header-date">

            <i class="fa-solid fa-calendar-days"></i>

            <?php echo date("F d, Y"); ?>

        </div>

        <div class="admin-avatar">

            <i class="fa-solid fa-user-tie"></i>

        </div>

    </div>

</div>