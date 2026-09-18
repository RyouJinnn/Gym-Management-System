<?php

if(session_status() === PHP_SESSION_NONE){
    session_start();
}

$adminName = $_SESSION['admin_name'] ?? "Administrator";


/* ===========================
   ADMIN NOTIFICATIONS
=========================== */

$notificationQuery = mysqli_query($con, "
    SELECT
        action,
        description,
        created_at
    FROM activity_log
    ORDER BY created_at DESC, activity_id DESC
    LIMIT 5
");

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


    <!-- DATE -->

    <div class="header-date">

        <i class="fa-solid fa-calendar-days"></i>

        <?php echo date("F d, Y"); ?>

    </div>


    <!-- NOTIFICATION -->

    <div class="notification-wrapper">

        <button
            type="button"
            class="notification-btn"
            onclick="toggleAdminNotifications()"
        >

            <i class="fa-solid fa-bell"></i>

        </button>


        <div
            id="adminNotificationPopup"
            class="notification-popup"
        >

            <div class="notification-popup-header">

                <h3>
                    Notifications
                </h3>

            </div>


            <div class="notification-popup-list">

                <?php if(
                    $notificationQuery &&
                    mysqli_num_rows($notificationQuery) > 0
                ): ?>

                    <?php while(
                        $notification =
                        mysqli_fetch_assoc($notificationQuery)
                    ): ?>

                        <div class="notification-item">

                            <div class="notification-icon">

                                <i class="fa-solid fa-clock-rotate-left"></i>

                            </div>


                            <div class="notification-content">

                                <strong>
                                    <?php
                                    echo htmlspecialchars(
                                        $notification['action']
                                    );
                                    ?>
                                </strong>


                                <p>
                                    <?php
                                    echo htmlspecialchars(
                                        $notification['description']
                                    );
                                    ?>
                                </p>


                                <small>

                                    <?php
                                    echo date(
                                        "M d, Y h:i A",
                                        strtotime(
                                            $notification['created_at']
                                        )
                                    );
                                    ?>

                                </small>

                            </div>

                        </div>

                    <?php endwhile; ?>

                <?php else: ?>

                    <div class="notification-empty">

                        <i class="fa-regular fa-bell-slash"></i>

                        <p>
                            No notifications yet.
                        </p>

                    </div>

                <?php endif; ?>

            </div>


            <!-- SEE ALL -->

            <div class="notification-popup-footer">

                <a href="notifications_admin.php">
                    See All Notifications
                </a>

            </div>

        </div>

    </div>


    <!-- ADMIN AVATAR -->

    <div class="admin-avatar">

        <i class="fa-solid fa-user-shield"></i>

    </div>

</div>
</div>


<script>

function toggleAdminNotifications(){

    const popup =
        document.getElementById("adminNotificationPopup");

    popup.classList.toggle("show");

}


document.addEventListener("click", function(event){

    const wrapper =
        document.querySelector(".notification-wrapper");

    const popup =
        document.getElementById("adminNotificationPopup");


    if(
        wrapper &&
        popup &&
        !wrapper.contains(event.target)
    ){

        popup.classList.remove("show");

    }

});

</script>
