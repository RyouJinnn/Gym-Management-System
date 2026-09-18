<?php

if(session_status() === PHP_SESSION_NONE){
    session_start();
}

$staffName = $_SESSION['staff_name'] ?? "Staff";

$staff_id = (int) ($_SESSION['staff_id'] ?? 0);

$notificationQuery = mysqli_query($con, "
    SELECT
        action,
        description,
        created_at
    FROM activity_log
    WHERE
        recipient_id = $staff_id
        OR (
            actor_id = $staff_id
            AND actor_type = 'Staff'
        )
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
            Welcome,
            <strong><?php echo htmlspecialchars($staffName); ?></strong>
        </p>

    </div>

    <div class="header-right">

        <div class="header-date">

            <i class="fa-solid fa-calendar-days"></i>

            <?php echo date("F d, Y"); ?>

        </div>


        <!-- NOTIFICATION -->

        <div class="notification-wrapper">

            <button
                type="button"
                class="notification-btn"
                onclick="toggleStaffNotifications()"
            >

                <i class="fa-solid fa-bell"></i>

            </button>


            <div
                id="staffNotificationPopup"
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

                    <a href="notifications_staff.php">
                        See All Notifications
                    </a>

                </div>

            </div>

        </div>


        <div class="admin-avatar">

            <i class="fa-solid fa-user-tie"></i>

        </div>

    </div>

</div>


<script>

function toggleStaffNotifications(){

    const popup = document.getElementById("staffNotificationPopup");

    popup.classList.toggle("show");

}

document.addEventListener("click", function(event){

    const wrapper = document.querySelector(".notification-wrapper");
    const popup = document.getElementById("staffNotificationPopup");

    if(wrapper && popup && !wrapper.contains(event.target)){

        popup.classList.remove("show");

    }

});

</script>
