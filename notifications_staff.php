<?php

require_once("includes/staff_auth.php");

$staff_id = (int) $_SESSION['staff_id'];

$activityQuery = mysqli_query($con, "
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
");

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Notifications | Staff Panel</title>

    <link
        href="https://fonts.googleapis.com/css2?family=Orbitron:wght@600;700&family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
    >

    <link
        rel="stylesheet"
        href="assets/css/admin.css"
    >

</head>

<body>

<div class="wrapper">

    <?php include("includes/staff_sidebar.php"); ?>

    <div class="main">

        <?php include("includes/staff_header.php"); ?>

        <div class="dashboard-content">

            <div class="dashboard-overview-header">

                <div>

                    <h2 class="dashboard-title">
                        All Notifications
                    </h2>

                    <p>
                        View all notifications and activities related to your staff account.
                    </p>

                </div>

                <a
                    href="staff_dashboard.php"
                    class="scan-member-btn"
                >
                    <i class="fa-solid fa-arrow-left"></i>
                    Back to Dashboard
                </a>

            </div>


            <div class="overview-card">

                <div class="activity-list">

                    <?php if(mysqli_num_rows($activityQuery) > 0): ?>

                        <?php while($activity = mysqli_fetch_assoc($activityQuery)): ?>

                            <div class="activity-item">

                                <i class="fa-solid fa-clock-rotate-left"></i>

                                <span>

                                    <strong>
                                        <?php
                                        echo htmlspecialchars(
                                            $activity['action']
                                        );
                                        ?>
                                    </strong>

                                    <?php
                                    echo htmlspecialchars(
                                        $activity['description']
                                    );
                                    ?>

                                    <small>

                                        <?php
                                        echo date(
                                            "M d, Y h:i A",
                                            strtotime(
                                                $activity['created_at']
                                            )
                                        );
                                        ?>

                                    </small>

                                </span>

                            </div>

                        <?php endwhile; ?>

                    <?php else: ?>

                        <div class="activity-item">

                            <i class="fa-solid fa-clock-rotate-left"></i>

                            <span>
                                No notifications recorded yet.
                            </span>

                        </div>

                    <?php endif; ?>

                </div>

            </div>

        </div>

    </div>

</div>

</body>

</html>