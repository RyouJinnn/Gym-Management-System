<?php
require_once("includes/staff_auth.php");

/* ===========================
   TOTAL MEMBERS
=========================== */

$memberQuery = mysqli_query($con,"
    SELECT COUNT(*) AS total
    FROM signup
");

$totalMembers =
mysqli_fetch_assoc($memberQuery)['total'];


/* ===========================
   TOTAL PAYMENTS
=========================== */

$paymentQuery = mysqli_query($con,"
    SELECT COUNT(*) AS total
    FROM payments
");

$totalPayments =
mysqli_fetch_assoc($paymentQuery)['total'];


/* ===========================
   TODAY ATTENDANCE
=========================== */

$attendanceQuery = mysqli_query($con,"
    SELECT COUNT(*) AS total
    FROM attendance
    WHERE attendance_date = CURDATE()
");

$totalAttendance =
mysqli_fetch_assoc($attendanceQuery)['total'];


/* ===========================
   ACTIVE MEMBERS
=========================== */

$activeQuery = mysqli_query($con,"
    SELECT COUNT(*) AS total
    FROM membership
    WHERE status = 'Active'
");

$activeMembers =
mysqli_fetch_assoc($activeQuery)['total'];


/* ===========================
   PENDING PAYMENTS
=========================== */

$pendingPaymentQuery = mysqli_query($con,"
    SELECT COUNT(*) AS total
    FROM payments
    WHERE payment_status = 'Pending'
");

$pendingPayments =
mysqli_fetch_assoc($pendingPaymentQuery)['total'];


/* ===========================
   EXPIRING MEMBERSHIPS
   WITHIN 7 DAYS
=========================== */

$expiringQuery = mysqli_query($con,"
    SELECT COUNT(*) AS total
    FROM membership
    WHERE status = 'Active'
    AND end_date BETWEEN CURDATE()
    AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
");

$expiringMemberships =
mysqli_fetch_assoc($expiringQuery)['total'];

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
    LIMIT 10
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

<title>Staff Dashboard</title>


<!-- GOOGLE FONTS -->

<link
    href="https://fonts.googleapis.com/css2?family=Orbitron:wght@600;700&family=Poppins:wght@300;400;500;600;700&display=swap"
    rel="stylesheet"
>


<!-- FONT AWESOME -->

<link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
/>


<!-- SAME CSS AS ADMIN -->

<link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>

<div class="wrapper">

<?php include("includes/staff_sidebar.php"); ?>

<div class="main">
    <?php include("includes/staff_header.php"); ?>

<div class="dashboard-content">

    <div class="dashboard-overview-header">

        <h2 class="dashboard-title">

            Dashboard Overview

        </h2>

        <a href="scan_member.php" class="scan-member-btn">
            <i class="fa-solid fa-qrcode"></i>
            Scan Member
        </a>

    </div>

    <div class="stats-grid">


            <!-- TOTAL MEMBERS -->

            <a
                href="members_staff.php"
                class="stat-card"
            >

                <div class="stat-icon">

                    <i class="fa-solid fa-users"></i>

                </div>


                <div class="stat-info">

                    <h3>
                        <?php echo $totalMembers; ?>
                    </h3>

                    <p>
                        Total Members
                    </p>

                </div>

            </a>


            <!-- ACTIVE MEMBERS -->

            <a
                href="members_staff.php"
                class="stat-card"
            >

                <div class="stat-icon">

                    <i class="fa-solid fa-user-check"></i>

                </div>


                <div class="stat-info">

                    <h3>
                        <?php echo $activeMembers; ?>
                    </h3>

                    <p>
                        Active Members
                    </p>

                </div>

            </a>


            <!-- PAYMENTS -->

            <a
                href="payments_staff.php"
                class="stat-card"
            >

                <div class="stat-icon">

                    <i class="fa-solid fa-money-check-dollar"></i>

                </div>


                <div class="stat-info">

                    <h3>
                        <?php echo $totalPayments; ?>
                    </h3>

                    <p>
                        Total Payments
                    </p>

                </div>

            </a>


            <!-- ATTENDANCE -->

            <a
                href="attendance_staff.php"
                class="stat-card"
            >

                <div class="stat-icon">

                    <i class="fa-solid fa-calendar-check"></i>

                </div>


                <div class="stat-info">

                    <h3>
                        <?php echo $totalAttendance; ?>
                    </h3>

                    <p>
                        Today's Attendance
                    </p>

                </div>

            </a>


        </div>



        <!-- ===========================
             QUICK ACTIONS
        =========================== -->

        <div class="dashboard-section">


            <h2>
                Quick Actions
            </h2>


            <div class="quick-grid">


                <!-- MEMBERS -->

                <a
                    href="members_staff.php"
                    class="quick-card"
                >

                    <i class="fa-solid fa-users"></i>

                    <h3>
                        Members
                    </h3>

                    <p>
                        Add, update, view, and search members.
                    </p>

                </a>


                <!-- PAYMENTS -->

                <a
                    href="payments_staff.php"
                    class="quick-card"
                >

                    <i class="fa-solid fa-money-check-dollar"></i>

                    <h3>
                        Payments
                    </h3>

                    <p>
                        Record and review membership payments.
                    </p>

                </a>


                <!-- ATTENDANCE -->

                <a
                    href="attendance_staff.php"
                    class="quick-card"
                >

                    <i class="fa-solid fa-calendar-check"></i>

                    <h3>
                        Attendance
                    </h3>

                    <p>
                        Record and view member attendance.
                    </p>

                </a>


                <!-- REPORTS -->

                <a
                    href="reports_staff.php"
                    class="quick-card"
                >

                    <i class="fa-solid fa-chart-column"></i>

                    <h3>
                        Reports
                    </h3>

                    <p>
                        Generate membership, payment, and attendance reports.
                    </p>

                </a>


            </div>

        </div>



        <!-- ===========================
             OVERVIEW
        =========================== -->

        <div class="overview-grid">


            <!-- ===========================
                 RECENT ACTIVITY
            =========================== -->

            <div class="overview-card">


               <div class="activity-header">

    <h2>

        <i class="fa-solid fa-clock-rotate-left"></i>

        Recent Activity

    </h2>

    <a href="notifications_staff.php" class="see-all-activity">
        See All
    </a>

</div>

                <div class="activity-list">

<?php if(mysqli_num_rows($activityQuery) > 0): ?>

    <?php while($activity = mysqli_fetch_assoc($activityQuery)): ?>

        <div class="activity-item">

            <i class="fa-solid fa-clock-rotate-left"></i>

            <span>
                <strong>
                    <?php echo htmlspecialchars($activity['action']); ?>
                </strong>

                <?php echo htmlspecialchars($activity['description']); ?>

                <small>
                    <?php
                    echo date(
                        "M d, Y h:i A",
                        strtotime($activity['created_at'])
                    );
                    ?>
                </small>
            </span>

        </div>

    <?php endwhile; ?>

<?php else: ?>

    <div class="activity-item">
        <i class="fa-solid fa-clock-rotate-left"></i>
        <span>No activities recorded yet.</span>
    </div>

<?php endif; ?>

</div>


            </div>



            <!-- ===========================
                 STAFF OVERVIEW
            =========================== -->

            <div class="overview-card">


                <h2>

                    <i class="fa-solid fa-chart-simple"></i>

                    System Overview

                </h2>


                <div class="system-row">

                    <span>
                        Active Members
                    </span>

                    <strong>
                        <?php echo $activeMembers; ?>
                    </strong>

                </div>


                <div class="system-row">

                    <span>
                        Pending Payments
                    </span>

                    <strong>
                        <?php echo $pendingPayments; ?>
                    </strong>

                </div>


                <div class="system-row">

                    <span>
                        Expiring Memberships
                    </span>

                    <strong>
                        <?php echo $expiringMemberships; ?>
                    </strong>

                </div>


                <div class="system-row">

                    <span>
                        Today's Attendance
                    </span>

                    <strong>
                        <?php echo $totalAttendance; ?>
                    </strong>

                </div>


            </div>


        </div>


    </div>


</div>


</div>


</body>

</html>