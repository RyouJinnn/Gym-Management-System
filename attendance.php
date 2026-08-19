<?php
session_start();
include("connect.php");

/* ======================================================
   AUTHENTICATION
====================================================== */

if (!isset($_SESSION['email'])) {

    header("Location: login.php");
    exit();

}

$email = $_SESSION['email'];

/* ======================================================
   GET LOGGED-IN USER
====================================================== */

$sql = "
SELECT *
FROM signup
WHERE email = ?
LIMIT 1
";

$stmt = $con->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();

$user = $stmt->get_result()->fetch_assoc();

$stmt->close();

if (!$user) {

    session_destroy();

    header("Location: login.php");
    exit();

}

$memberId = $user['id'];

/* ======================================================
   GET ACTIVE MEMBERSHIP
====================================================== */

$sql = "
SELECT *
FROM membership
WHERE member_id = ?
AND status='Active'
AND end_date >= CURDATE()
LIMIT 1
";

$stmt = $con->prepare($sql);
$stmt->bind_param("i", $memberId);
$stmt->execute();

$membership = $stmt->get_result()->fetch_assoc();

$stmt->close();

$hasMembership = $membership ? true : false;

/* ======================================================
   MEMBERSHIP INFORMATION
====================================================== */

$membershipName = "";
$membershipEnd = "";
$daysRemaining = 0;

if ($hasMembership) {

    $membershipName = $membership['plan_name'];

    $membershipEnd = $membership['end_date'];

    $todayDate = new DateTime();

    $endDate = new DateTime($membershipEnd);

    if ($todayDate <= $endDate) {

        $daysRemaining = $todayDate
            ->diff($endDate)
            ->days;

    }

}

/* ======================================================
   TODAY
====================================================== */
date_default_timezone_set("Asia/Manila");
$currentDate = date("Y-m-d");
$currentDateTime = date("Y-m-d H:i:s");

/* ======================================================
   CHECK IN
====================================================== */

if (isset($_POST['check_in']) && $hasMembership) {

    $check = $con->prepare("
        SELECT attendance_id
        FROM attendance
        WHERE member_id = ?
        AND attendance_date = ?
        LIMIT 1
    ");

    $check->bind_param("is", $memberId, $currentDate);
    $check->execute();

    $exists = $check->get_result()->fetch_assoc();

    $check->close();

    if (!$exists) {

        $stmt = $con->prepare("
            INSERT INTO attendance
            (
                member_id,
                attendance_date,
                check_in,
                status
            )
            VALUES
            (
                ?, ?, ?, 'Checked In'
            )
        ");

        $stmt->bind_param(
            "iss",
            $memberId,
            $currentDate,
            $currentDateTime
        );

       if(!$stmt->execute()){

    die("SQL ERROR: ".$stmt->error);

}

$stmt->close();
    }

    header("Location: attendance.php");
    exit();

}

/* ======================================================
   CHECK OUT
====================================================== */

if (isset($_POST['check_out']) && $hasMembership) {

    $check = $con->prepare("
        SELECT *
        FROM attendance
        WHERE member_id = ?
        AND attendance_date = ?
        LIMIT 1
    ");

    $check->bind_param("is", $memberId, $currentDate);
    $check->execute();

    $todayAttendance = $check->get_result()->fetch_assoc();

    $check->close();

    if ($todayAttendance && empty($todayAttendance['check_out'])) {

        $stmt = $con->prepare("
            UPDATE attendance
            SET
                check_out = ?,
                status = 'Checked Out'
            WHERE attendance_id = ?
        ");

        $stmt->bind_param(
            "si",
            $currentDateTime,
            $todayAttendance['attendance_id']
        );

        $stmt->execute();
        $stmt->close();

    }

    header("Location: attendance.php");
    exit();

}

/* ======================================================
   TODAY'S ATTENDANCE
====================================================== */

$todayAttendance = null;

if ($hasMembership) {

    $sql = "
    SELECT *
    FROM attendance
    WHERE member_id = ?
    AND attendance_date = ?
    LIMIT 1
    ";

    $stmt = $con->prepare($sql);

    $stmt->bind_param(
        "is",
        $memberId,
        $currentDate
    );

    $stmt->execute();

    $todayAttendance =
        $stmt->get_result()->fetch_assoc();

    $stmt->close();

}

/* ======================================================
   ATTENDANCE HISTORY
====================================================== */

$attendanceHistory = [];

if ($hasMembership) {

    $sql = "
    SELECT *
    FROM attendance
    WHERE member_id = ?
    ORDER BY attendance_date DESC
    LIMIT 10
    ";

    $stmt = $con->prepare($sql);

    $stmt->bind_param(
        "i",
        $memberId
    );

    $stmt->execute();

    $attendanceHistory =
        $stmt->get_result();

    $stmt->close();

}

/* ======================================================
   DASHBOARD SUMMARY
====================================================== */

$totalVisits = 0;
$totalCheckOuts = 0;
$thisMonthVisits = 0;

if ($hasMembership) {

    $result = $con->query("
        SELECT COUNT(*) AS total
        FROM attendance
        WHERE member_id = $memberId
    ");

    $totalVisits =
        $result->fetch_assoc()['total'];

    $result = $con->query("
        SELECT COUNT(*) AS total
        FROM attendance
        WHERE member_id = $memberId
        AND check_out IS NOT NULL
    ");

    $totalCheckOuts =
        $result->fetch_assoc()['total'];

    $result = $con->query("
        SELECT COUNT(*) AS total
        FROM attendance
        WHERE member_id = $memberId
        AND MONTH(attendance_date)=MONTH(CURDATE())
        AND YEAR(attendance_date)=YEAR(CURDATE())
    ");

    $thisMonthVisits =
        $result->fetch_assoc()['total'];

}

/* ======================================================
   STATUS
====================================================== */

$attendanceStatus = "Not Checked In";
$statusColor = "yellow";

if ($todayAttendance) {

    if (empty($todayAttendance['check_out'])) {

        $attendanceStatus = "Checked In";
        $statusColor = "green";

    } else {

        $attendanceStatus = "Checked Out";
        $statusColor = "blue";

    }

}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Fit Function Gym | Attendance</title>

    <link rel="stylesheet" href="sidebar.css">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap"
          rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <style>

        /* ==========================================
   GENERAL
========================================== */

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins',sans-serif;
}

body{
    background:#070707;
    color:#fff;
}

a{
    text-decoration:none;
}

.main{
    flex:1;
    padding:30px;
}

.attendance-container{
    max-width:1200px;
    margin:0 auto;
}

/* ==========================================
   HEADER
========================================== */

.attendance-header{
    margin-bottom:30px;
}

.attendance-header h1{
    font-family:'Orbitron',sans-serif;
    font-size:42px;
    color:#39ff14;
    margin-bottom:10px;
}

.attendance-header p{
    color:#bdbdbd;
    font-size:14px;
}

/* ==========================================
   MEMBERSHIP CARD
========================================== */

.membership-overview{

    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:30px;
    padding:35px;
    margin-bottom:30px;
    background:linear-gradient(135deg,#121212,#0b0b0b);
    border:1px solid #252525;
    border-radius:24px;
    overflow:hidden;

    /* Move these here */
    transition:.3s;

}

.membership-left{

    display:flex;

    align-items:center;

    gap:25px;

}

.membership-icon{

    width:70px;

    height:70px;

    border-radius:22px;

    background:#39ff14;

    display:flex;

    justify-content:center;

    align-items:center;

    font-size:25px;

    color:#000;

    box-shadow:0 0 25px rgba(57,255,20,.25);

}

.membership-info small{

    color:#999;

    letter-spacing:1px;

    text-transform:uppercase;

}

.membership-info h2{

    margin:8px 0;

    font-size:25px;

    color:#39ff14;

}

.membership-info p{

    color:#bbb;

    margin-bottom:10px;

}

.membership-info span{

    color:#999;

}

.membership-info strong{

    color:#fff;

}

.membership-right{

    display:flex;

    flex-direction:column;

    align-items:center;

    gap:10px;

}

.days-circle{

    width:80px;

    height:80px;

    border-radius:50%;

    border:4px solid #39ff14;

    display:flex;

    justify-content:center;

    align-items:center;

    font-size:30px;

    font-weight:700;

    color:#39ff14;

    box-shadow:0 0 30px rgba(57,255,20,.18);

}

.membership-right small{

    color:#aaa;

    font-size:14px;

}

/* ==========================================
   GRID
========================================== */

.attendance-grid{

    display:grid;

    grid-template-columns:380px 1fr;

    gap:25px;

    margin-bottom:30px;

}

/* ==========================================
   TIMELINE
========================================== */

.timeline{

    position:relative;

    margin-top:25px;

}

.timeline::before{

    content:"";

    position:absolute;

    left:22px;

    top:0;

    bottom:0;

    width:2px;

    background:#2a2a2a;

}

.timeline-item{

    position:relative;

    display:flex;

    gap:25px;

    margin-bottom:30px;

}

.timeline-dot{

    width:40px;

    height:40px;

    border-radius:50%;

    background:#39ff14;

    color:#000;

    display:flex;

    justify-content:center;

    align-items:center;

    font-size:18px;

    z-index:2;

    flex-shrink:0;

}

.timeline-content{

    flex:1;

    background:#181818;

    border:1px solid #252525;

    border-radius:18px;

    padding:22px;

    transition:.3s;

}

.timeline-content:hover{

    transform:translateY(-3px);

    border-color:#39ff14;

    box-shadow:0 0 20px rgba(57,255,20,.12);

}

.timeline-header{

    display:flex;

    justify-content:space-between;

    align-items:center;

    margin-bottom:20px;

    flex-wrap:wrap;

    gap:10px;

}

.timeline-header h4{

    font-size:15px;

}

.timeline-status{

    background:#39ff14;

    color:#000;

    padding:6px 15px;

    border-radius:50px;

    font-size:12px;

    font-weight:600;

}

.timeline-body{

    display:grid;

    grid-template-columns:repeat(2,1fr);

    gap:20px;

}

.timeline-body strong{

    display:block;

    margin-bottom:6px;

    color:#39ff14;

}

.timeline-body p{

    color:#ccc;

}

.confirm-modal{

    position:fixed;
    inset:0;

    background:rgba(0,0,0,.75);

    display:none;

    justify-content:center;
    align-items:center;

    z-index:9999;

}

.confirm-box{

    width:420px;

    background:#111;

    border:1px solid #2d2d2d;

    border-radius:20px;

    padding:35px;

    text-align:center;

}

.confirm-box i{

    font-size:40px;

    color:#39ff14;

    margin-bottom:20px;

}

.confirm-box h2{

    margin-bottom:10px;

}

.confirm-box p{

    color:#bbb;

    margin-bottom:30px;

}

.confirm-buttons{

    display:flex;

    gap:15px;

}

.confirm-buttons button{

    flex:1;

    height:50px;

    border:none;

    border-radius:10px;

    font-weight:700;

    cursor:pointer;

}

.no-btn{

    background:#2b2b2b;

    color:#fff;

}

.yes-btn{

    background:#39ff14;

    color:#000;

}

.empty-history{

    text-align:center;

    padding:60px 20px;

}

.empty-history i{

    font-size:40px;

    color:#39ff14;

    margin-bottom:20px;

}

.empty-history h3{

    margin-bottom:10px;

}

.empty-history p{

    color:#aaa;

}

.membership-overview:hover{

    border-color:#39ff14;

    box-shadow:0 0 30px rgba(57,255,20,.12);

}

.summary-status{

    display:inline-block;

    padding:8px 18px;

    border-radius:50px;

    font-size:13px;

    font-weight:600;

    margin-bottom:10px;

}

.summary-status.green{

    background:#14351d;
    color:#39ff14;

}

.summary-status.blue{

    background:#12324a;
    color:#4db7ff;

}

.summary-status.yellow{

    background:#463b08;
    color:#ffd84d;

}

/* ==========================================
   CARDS
========================================== */

.today-card,
.summary-card,
.history-card{

    background:#111;

    border:1px solid #252525;

    border-radius:20px;

    padding:30px;

}

.today-card h3,
.summary-card h3,
.history-card h3{

    margin-bottom:25px;

    font-size:20px;

}

/* ==========================================
   STATUS BADGE
========================================== */

.status-badge{

    display:inline-flex;

    align-items:center;

    justify-content:center;

    width:100%;

    height:60px;

    border-radius:50px;

    font-weight:700;

    margin-bottom:20px;

}

.status-badge.green{

    background:#0d3d18;

    color:#55ff77;

}

.status-badge.blue{

    background:#0d2846;

    color:#4db8ff;

}

.status-badge.yellow{

    background:#463b08;

    color:#ffd84d;

}

.today-card p{

    color:#bbb;

    line-height:1.7;

    margin-bottom:25px;

}

/* ==========================================
   BUTTONS
========================================== */

.check-button,
.checkout-button{

    width:100%;

    height:55px;

    border:none;

    border-radius:12px;

    font-size:14px;

    font-weight:700;

    cursor:pointer;

    transition:.3s;

}

.check-button{

    background:#39ff14;

    color:#000;

}

.check-button:hover{

    transform:translateY(-2px);

    box-shadow:0 0 20px rgba(57,255,20,.35);

}

.checkout-button{

    background:#ff4d4d;

    color:#fff;

}

.checkout-button:hover{

    background:#ff2f2f;

}

/* ==========================================
   SUMMARY
========================================== */

.summary-grid{

    display:grid;

    grid-template-columns:repeat(2,1fr);

    gap:18px;

}

.summary-box{

    background:#181818;

    border:1px solid #252525;

    border-radius:18px;

    padding:25px;

    text-align:center;

    transition:.35s;

    cursor:default;

}

.summary-box:hover{

    transform:translateY(-6px);

    border-color:#39ff14;

    box-shadow:0 0 25px rgba(57,255,20,.15);

}

.summary-icon{

    width:50px;

    height:50px;

    margin:0 auto 18px;

    border-radius:18px;

    background:#39ff14;

    display:flex;

    justify-content:center;

    align-items:center;

    color:#000;

    font-size:22px;

}

.summary-box h2{

    font-size:30px;

    color:#39ff14;

    margin-bottom:8px;

}

.summary-box span{

    color:#bdbdbd;

    font-size:13px;

}
/* ==========================================
   NO MEMBERSHIP
========================================== */

.membership-required{

    max-width:650px;

    margin:80px auto;

    background:#111;

    border:1px solid #252525;

    border-radius:20px;

    padding:60px;

    text-align:center;

}

.required-icon{

    width:100px;

    height:100px;

    margin:auto;

    margin-bottom:25px;

    border-radius:50%;

    background:#39ff14;

    color:#000;

    display:flex;

    justify-content:center;

    align-items:center;

    font-size:30px;

}

.membership-required h1{

    margin-bottom:15px;

}

.membership-required p{

    color:#bbb;

    line-height:1.8;

    margin-bottom:30px;

}

.membership-button{

    display:inline-block;

    padding:15px 35px;

    background:#39ff14;

    color:#000;

    font-weight:700;

    border-radius:12px;

    transition:.3s;

}

.membership-button:hover{

    transform:translateY(-2px);

}

/* ==========================================
   STATUS COLORS
========================================== */

.status-complete{

    background:#14351d;

    color:#39ff14;

    border:1px solid #39ff14;

}

.status-active{

    background:#12324a;

    color:#4db7ff;

    border:1px solid #4db7ff;

}

.status-pending{

    background:#463b08;

    color:#ffd84d;

    border:1px solid #ffd84d;

}

/* ==========================================
   TIMELINE STATUS
========================================== */

.timeline-status{
    display:inline-flex;
    align-items:center;
    min-width:145px;
    justify-content:center;
    gap:8px;
    padding:8px 16px;
    border-radius:50px;
    font-size:13px;
    font-weight:600;
}

.timeline-status i{

    font-size:13px;

}

/* ==========================================
   TIMELINE DOT
========================================== */

.timeline-dot{

    width:48px;

    height:48px;

    border-radius:50%;

    display:flex;

    justify-content:center;

    align-items:center;

    font-size:15px;

    border:2px solid transparent;

    transition:.3s;

}

.timeline-item:hover .timeline-dot{

    transform:scale(1.08);

}

.status-badge{

    display:flex;

    align-items:center;

    justify-content:center;

    gap:15px;

}

.status-dot{

    width:14px;

    height:14px;

    border-radius:50%;

    background:currentColor;

}

/* ==========================================
   TODAY OVERVIEW
========================================== */

.today-overview{

    margin-top:30px;

    display:flex;

    flex-direction:column;

    gap:15px;

}

.overview-item{

    display:flex;

    align-items:center;

    gap:18px;

    padding:16px 18px;

    background:#181818;

    border:1px solid #252525;

    border-radius:15px;

    transition:.3s;

}

.overview-item:hover{

    border-color:#39ff14;

    transform:translateX(5px);

}

.overview-item i{

    width:48px;

    height:48px;

    border-radius:14px;

    background:#39ff14;

    color:#000;

    display:flex;

    justify-content:center;

    align-items:center;

    font-size:20px;

}

.overview-item span{

    display:block;

    color:#999;

    font-size:13px;

    margin-bottom:3px;

}

.overview-item strong{

    color:#fff;

    font-size:15px;

}
/* ==========================================
   RESPONSIVE
========================================== */

@media(max-width:1000px){

    .attendance-grid{

        grid-template-columns:1fr;

    }
}

@media(max-width:700px){

    .summary-grid{

        grid-template-columns:1fr;

    }
    .timeline-body{

    grid-template-columns:1fr;

}
}
    </style>

</head>

<body>

<div class="wrapper">

    <?php include("sidebar.php"); ?>

    <main class="main">

        <div class="attendance-container">

        <?php if(!$hasMembership): ?>

            <!-- NO MEMBERSHIP -->

            <section class="membership-required">

                <div class="required-icon">

                    <i class="fa-solid fa-id-card"></i>

                </div>

                <h1>No Active Membership</h1>

                <p>

                    You need an active membership before you
                    can use the attendance system.

                </p>

                <a
                    href="membership_db.php"
                    class="membership-button">

                    Avail Membership

                </a>

            </section>

        <?php else: ?>

            <!-- PAGE TITLE -->

            <header class="attendance-header">

                <h1>ATTENDANCE</h1>

                <p>

                    Track your gym attendance and consistency.

                </p>

            </header>

            <!-- MEMBERSHIP CARD -->

           <section class="membership-overview">

    <div class="membership-left">

        <div class="membership-icon">

            <i class="fa-solid fa-medal"></i>

        </div>

        <div class="membership-info">

            <small>Active Membership</small>

            <h2>

                <?= htmlspecialchars($membershipName) ?>

            </h2>

            <p>

                Your membership is currently active.

            </p>

            <span>

                Valid Until

                <strong>

                    <?= date(
                        "F d, Y",
                        strtotime($membershipEnd)
                    ); ?>

                </strong>

            </span>

        </div>

    </div>

    <div class="membership-right">

        <div class="days-circle">

            <?= $daysRemaining ?>

        </div>

        <small>Days Remaining</small>

    </div>

</section>

            <!-- DASHBOARD -->

            <div class="attendance-grid">

                <!-- TODAY -->

                <section class="today-card">

                    <h3>

                        Today's Attendance

                    </h3>

                    <div class="status-badge <?= $statusColor ?>">

    <div class="status-dot"></div>

    <?= $attendanceStatus ?>

</div>

                    <?php if(!$todayAttendance): ?>

                        <p>

                            You haven't checked in yet.

                        </p>

                        <form method="POST" action="attendance.php">

    <input type="hidden" name="check_in" value="1">

    <button
        type="button"
        class="check-button">

        <i class="fa-solid fa-right-to-bracket"></i>

        Check In

    </button>

</form>

                    <?php elseif(empty($todayAttendance['check_out'])): ?>

                        <p>

                            You're currently inside the gym.

                        </p>

                        <form method="POST" action="attendance.php">

    <input type="hidden" name="check_out" value="1">

    <button
        type="button"
        class="checkout-button">

        <i class="fa-solid fa-right-from-bracket"></i>

        Check Out

    </button>

</form>

                    <?php endif; ?>

                        <div class="today-overview">

    <div class="overview-item">

        <i class="fa-solid fa-dumbbell"></i>

        <div>

            <span>Membership</span>

            <strong>

                <?= htmlspecialchars($membershipName) ?>

            </strong>

        </div>

    </div>

    <div class="overview-item">

        <i class="fa-solid fa-calendar-days"></i>

        <div>

            <span>Today's Date</span>

            <strong>

                <?= date("F d, Y") ?>

            </strong>

        </div>

    </div>

    <div class="overview-item">

        <i class="fa-solid fa-clock"></i>

        <div>

            <span>Current Time</span>

            <strong id="liveTime">

                <?= date("h:i A") ?>

            </strong>

        </div>

    </div>

</div>

</section>

                <!-- SUMMARY -->

                <section class="summary-card">

                    <h3>

                        Attendance Summary

                    </h3>

                   <div class="summary-grid">

    <div class="summary-box">

        <div class="summary-icon">

            <i class="fa-solid fa-calendar-check"></i>

        </div>

        <h2><?= $totalVisits ?></h2>

        <span>Total Visits</span>

    </div>

    <div class="summary-box">

    <div class="summary-icon">
        <i class="fa-solid fa-right-from-bracket"></i>
    </div>

    <h2><?= $totalCheckOuts ?></h2>

    <span>Total Check Outs</span>

</div>

    <div class="summary-box">

        <div class="summary-icon">

            <i class="fa-solid fa-calendar-days"></i>

        </div>

        <h2><?= $thisMonthVisits ?></h2>

        <span>Visit This Month</span>

    </div>

    <div class="summary-box">

    <div class="summary-icon">

        <i class="fa-solid fa-circle-info"></i>

    </div>

    <div class="summary-status <?= $statusColor; ?>">

        <?= $attendanceStatus ?>

    </div>

    <span>Today's Status</span>

</div>

</div>

</section>

            </div>

            <!-- HISTORY -->

            <section class="history-card">

    <div class="history-title">

        <h3>

            <i class="fa-solid fa-clock-rotate-left"></i>

            Recent Attendance

        </h3>

    </div>

    <?php if($attendanceHistory->num_rows > 0): ?>

        <div class="timeline">

        <?php while($row = $attendanceHistory->fetch_assoc()): ?>

           <?php

$statusClass = "";
$statusIcon = "";

if($row['status'] == "Checked Out"){

    $statusClass = "status-complete";
    $statusIcon = "fa-circle-check";

}
elseif($row['status'] == "Checked In"){

    $statusClass = "status-active";
    $statusIcon = "fa-person-walking";

}
else{

    $statusClass = "status-pending";
    $statusIcon = "fa-clock";

}

?>

<div class="timeline-item">

    <div class="timeline-dot <?= $statusClass; ?>">

        <i class="fa-solid <?= $statusIcon; ?>"></i>

    </div>

    <div class="timeline-content">

        <div class="timeline-header">

                        <h4>

                            <?= date(
                                "F d, Y",
                                strtotime($row['attendance_date'])
                            ); ?>

                        </h4>

<span class="timeline-status <?= $statusClass; ?>">

    <i class="fa-solid <?= $statusIcon; ?>"></i>

    <?= htmlspecialchars($row['status']) ?>

</span>

                    </div>

                    <div class="timeline-body">

                        <div>

                            <strong>Check In</strong>

                            <p>

                                <?=

                                $row['check_in']

                                ?

                                date(
                                    "h:i A",
                                    strtotime($row['check_in'])
                                )

                                :

                                "-"

                                ?>

                            </p>

                        </div>

                        <div>

                            <strong>Check Out</strong>

                            <p>

                                <?=

                                $row['check_out']

                                ?

                                date(
                                    "h:i A",
                                    strtotime($row['check_out'])
                                )

                                :

                                "-"

                                ?>

                            </p>

                        </div>

                    </div>

                </div>

            </div>

        <?php endwhile; ?>

        </div>

    <?php else: ?>

        <div class="empty-history">

            <i class="fa-regular fa-calendar-xmark"></i>

            <h3>No Attendance Yet</h3>

            <p>

                Your attendance history will appear here
                after your first check in.

            </p>

        </div>

    <?php endif; ?>

</section>
        <?php endif; ?>

        </div>

    </main>

</div>
<!-- Confirmation Modal -->

<div class="confirm-modal" id="confirmModal">

    <div class="confirm-box">

        <i class="fa-solid fa-circle-question"></i>

        <h2 id="confirmTitle">Confirm</h2>

        <p id="confirmText">
            Are you sure?
        </p>

        <div class="confirm-buttons">

            <button
                type="button"
                class="no-btn"
                id="cancelBtn">

                No

            </button>

            <button
                type="button"
                class="yes-btn"
                id="yesBtn">

                Yes

            </button>

        </div>

    </div>

</div>


<script>

/* ==========================================
   SIDEBAR TOGGLE
========================================== */

const menuButton = document.getElementById("menuBtn");
const sidebar = document.getElementById("sidebar");

if(menuButton && sidebar){

    menuButton.addEventListener("click", function(){

        sidebar.classList.toggle("open");

    });

}


/* ==========================================
   LIVE CLOCK
========================================== */

function updateClock(){

    const clock = document.getElementById("liveTime");

    if(!clock){
        return;
    }

    const now = new Date();

    clock.innerHTML = now.toLocaleTimeString([],{

        hour:"2-digit",
        minute:"2-digit",
        second:"2-digit"

    });

}

updateClock();

setInterval(updateClock,1000);


/* ==========================================
   CHECK IN CONFIRMATION
========================================== */

const modal = document.getElementById("confirmModal");
const title = document.getElementById("confirmTitle");
const text = document.getElementById("confirmText");
const yesBtn = document.getElementById("yesBtn");
const cancelBtn = document.getElementById("cancelBtn");

let currentForm = null;

document.querySelectorAll(".check-button,.checkout-button").forEach(btn=>{

    btn.addEventListener("click",()=>{

        currentForm = btn.closest("form");

        if(btn.classList.contains("check-button")){

            title.innerHTML = "Check In";
            text.innerHTML = "Are you sure you want to check in today?";

        }else{

            title.innerHTML = "Check Out";
            text.innerHTML = "Are you sure you want to check out now?";

        }

        modal.style.display="flex";

    });

});

cancelBtn.addEventListener("click",()=>{

    modal.style.display="none";

});

yesBtn.addEventListener("click",()=>{

    yesBtn.disabled = true;
    yesBtn.innerHTML = "Processing...";

    currentForm.submit();

});
/* ==========================================
   BUTTON CLICK EFFECT
========================================== */

const actionButtons = document.querySelectorAll(
    ".check-button, .checkout-button"
);

actionButtons.forEach(function(button){

    button.addEventListener("mousedown",function(){

        button.style.transform = "scale(.97)";

    });

    button.addEventListener("mouseup",function(){

        button.style.transform = "";

    });

    button.addEventListener("mouseleave",function(){

        button.style.transform = "";

    });

});


/* ==========================================
   CARD HOVER EFFECT
========================================== */

const cards = document.querySelectorAll(

    ".summary-box, .timeline-content, .membership-overview"

);

cards.forEach(function(card){

    card.addEventListener("mouseenter",function(){

        card.style.transition = ".3s";

    });

});

</script>
</body>
</html>