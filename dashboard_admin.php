<?php
require_once("includes/admin_auth.php");

$memberQuery = mysqli_query($con,"
SELECT COUNT(*) AS total
FROM signup
");

$totalMembers =
mysqli_fetch_assoc($memberQuery)['total'];

$staffQuery = mysqli_query($con,"
SELECT COUNT(*) AS total
FROM users
WHERE role='Staff'
AND status='Active'
");

$totalStaff =
mysqli_fetch_assoc($staffQuery)['total'];

$paymentQuery = mysqli_query($con,"
SELECT COUNT(*) AS total
FROM payments
");

$totalPayments =
mysqli_fetch_assoc($paymentQuery)['total'];

$attendanceQuery = mysqli_query($con,"
SELECT COUNT(*) AS total
FROM attendance
WHERE attendance_date = CURDATE()
");

$totalAttendance =
mysqli_fetch_assoc($attendanceQuery)['total'];

$activeQuery = mysqli_query($con,"
SELECT COUNT(*) AS total
FROM membership
WHERE status='Active'
");

$activeMembers =
mysqli_fetch_assoc($activeQuery)['total'];

$pendingPaymentQuery = mysqli_query($con,"
SELECT COUNT(*) AS total
FROM payments
WHERE payment_status='Pending'
");

$pendingPayments =
mysqli_fetch_assoc($pendingPaymentQuery)['total'];

$expiringQuery = mysqli_query($con,"
SELECT COUNT(*) AS total
FROM membership
WHERE status='Active'
AND end_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(),INTERVAL 7 DAY)
");

$expiringMemberships =
mysqli_fetch_assoc($expiringQuery)['total'];

$activityQuery = mysqli_query($con, "
    SELECT
        action,
        description,
        created_at
    FROM activity_log
    ORDER BY created_at DESC, activity_id DESC
    LIMIT 10
");
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Admin Dashboard</title>

<link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@600;700&family=Poppins:wght@300;400;500;600;700&display=swap"
rel="stylesheet">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"/>

<link rel="stylesheet"
href="assets/css/admin.css">

</head>

<body>

<div class="wrapper">

<?php include("includes/admin_sidebar.php"); ?>

<div class="main">

<?php include("includes/admin_header.php"); ?>

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

        <a href="members_admin.php" class="stat-card">

            <div class="stat-icon">
                <i class="fa-solid fa-users"></i>
            </div>

            <div class="stat-info">

                <h3><?php echo $totalMembers; ?></h3>

                <p>Total Members</p>

            </div>

        </a>

        <a href="staff_admin.php" class="stat-card">

            <div class="stat-icon">
                <i class="fa-solid fa-user-tie"></i>
            </div>

            <div class="stat-info">

                <h3><?php echo $totalStaff; ?></h3>

                <p>Total Staff</p>

            </div>

        </a>

        <a href="payments_admin.php" class="stat-card">

            <div class="stat-icon">
                <i class="fa-solid fa-money-check-dollar"></i>
            </div>

            <div class="stat-info">

                <h3><?php echo $totalPayments; ?></h3>

                <p>Total Payments</p>

            </div>

        </a>

        <a href="attendance_admin.php" class="stat-card">

            <div class="stat-icon">
                <i class="fa-solid fa-calendar-check"></i>
            </div>

            <div class="stat-info">

                <h3><?php echo $totalAttendance; ?></h3>

                <p>Today's Attendance</p>

            </div>

        </a>

     </div>

    <!-- Quick Actions -->

    <div class="dashboard-section">

        <h2>Quick Actions</h2>

        <div class="quick-grid">

            <a href="members_admin.php" class="quick-card">

                <i class="fa-solid fa-users"></i>

                <h3>Members</h3>

                <p>Manage member accounts and profiles.</p>

            </a>

            <a href="staff_admin.php" class="quick-card">

                <i class="fa-solid fa-user-tie"></i>

                <h3>Staff</h3>

                <p>Create and manage staff accounts.</p>

            </a>

            <a href="membership_plans_admin.php" class="quick-card">

                <i class="fa-solid fa-id-card"></i>

                <h3>Membership Plans</h3>

                <p>Manage membership plans and prices.</p>

            </a>

            <a href="payments_admin.php" class="quick-card">

                <i class="fa-solid fa-money-check-dollar"></i>

                <h3>Payments</h3>

                <p>Review and verify member payments.</p>

            </a>

            <a href="attendance_admin.php" class="quick-card">

                <i class="fa-solid fa-calendar-check"></i>

                <h3>Attendance</h3>

                <p>View attendance records and logs.</p>

            </a>

            <a href="reports_admin.php" class="quick-card">

                <i class="fa-solid fa-chart-column"></i>

                <h3>Reports</h3>

                <p>Generate gym reports.</p>

            </a>

            <a href="feedback_admin.php" class="quick-card">

                <i class="fa-solid fa-comments"></i>

                <h3>Feedback</h3>

                <p>Read member feedback.</p>

            </a>

            <a href="contact_messages_admin.php" class="quick-card">

                <i class="fa-solid fa-envelope"></i>

                <h3>Contact Messages</h3>

                <p>View messages from the Contact page.</p>

            </a>

        </div>

    </div>

    <div class="overview-grid">

    <div class="overview-card">

    <div class="activity-header">

        <h2>

            <i class="fa-solid fa-clock-rotate-left"></i>

            Recent Activity

        </h2>

        <a href="notifications_admin.php"
           class="see-all-activity">

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

    <!-- System Overview -->

    <div class="overview-card">

        <h2>

            <i class="fa-solid fa-chart-simple"></i>

            System Overview

        </h2>

        <div class="system-row">

            <span>Active Members</span>

            <strong>

                <?php echo $activeMembers; ?>

            </strong>

        </div>

        <div class="system-row">

            <span>Pending Payments</span>

            <strong>

                <?php echo $pendingPayments; ?>

            </strong>

        </div>

        <div class="system-row">

            <span>Expiring Memberships</span>

            <strong>

                <?php echo $expiringMemberships; ?>

            </strong>

        </div>

        <div class="system-row">

            <span>Today's Attendance</span>

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