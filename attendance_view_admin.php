<?php
include("includes/admin_auth.php");

/* ======================================================
   GET ATTENDANCE ID
====================================================== */

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: attendance_admin.php");
    exit();
}

$attendanceId = (int)$_GET['id'];

/* ======================================================
   GET ATTENDANCE DETAILS
====================================================== */

$sql = "
    SELECT
        a.attendance_id,
        a.member_id,
        a.attendance_date,
        a.check_in,
        a.check_out,
        a.status,

        s.first_name,
        s.middlename,
        s.last_name,
        s.suffix

    FROM attendance a

    LEFT JOIN signup s
        ON a.member_id = s.id

    WHERE a.attendance_id = ?

    LIMIT 1
";

$stmt = $con->prepare($sql);

if (!$stmt) {
    die("SQL Error: " . $con->error);
}

$stmt->bind_param("i", $attendanceId);
$stmt->execute();

$result = $stmt->get_result();
$attendance = $result->fetch_assoc();

$stmt->close();

/* ======================================================
   RECORD NOT FOUND
====================================================== */

if (!$attendance) {
    header("Location: attendance_admin.php");
    exit();
}

/* ======================================================
   MEMBER NAME
====================================================== */

$memberName = trim(
    ($attendance['first_name'] ?? '') . ' ' .
    ($attendance['middlename'] ?? '') . ' ' .
    ($attendance['last_name'] ?? '') . ' ' .
    ($attendance['suffix'] ?? '')
);

if ($memberName === '') {
    $memberName = "Unknown Member";
}

/* ======================================================
   INITIAL
====================================================== */

$initial = strtoupper(
    substr(
        trim($attendance['first_name'] ?? $memberName),
        0,
        1
    )
);

/* ======================================================
   STATUS CLASS
====================================================== */

$status = $attendance['status'] ?? 'Unknown';

$statusClass = strtolower(
    str_replace(' ', '-', $status)
);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>View Attendance | Admin Panel</title>

    <!-- GOOGLE FONTS -->
    <link
        href="https://fonts.googleapis.com/css2?family=Orbitron:wght@600;700&family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet"
    >

    <!-- FONT AWESOME -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
    >

    <!-- EXISTING ADMIN CSS -->
    <link
        rel="stylesheet"
        href="assets/css/admin.css"
    >

    <style>

        /* ======================================================
           ATTENDANCE VIEW PAGE
        ====================================================== */

        .attendance-view-content {
            width: 100%;
            padding-bottom: 40px;
        }


        /* ======================================================
           PAGE HEADER
        ====================================================== */

        .attendance-view-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 30px;

            margin-bottom: 38px;
        }

        .attendance-view-header h2 {
            margin: 0 0 10px;

            font-family: 'Orbitron', sans-serif;

            font-size: 30px;
            font-weight: 700;

            color: #FFD400;
        }

        .attendance-view-header p {
            margin: 0;

            color: #bdbdbd;

            font-family: 'Poppins', sans-serif;

            font-size: 15px;
            font-weight: 400;
        }


        /* ======================================================
           BACK BUTTON
        ====================================================== */

        .attendance-back-btn {
            display: inline-flex;

            align-items: center;
            justify-content: center;

            gap: 10px;

            min-width: 220px;
            height: 55px;

            padding: 0 20px;

            background: #FFD400;
            color: #000;

            border: none;
            border-radius: 14px;

            font-family: 'Poppins', sans-serif;

            font-size: 14px;
            font-weight: 700;

            text-decoration: none;

            transition: .2s ease;
        }

        .attendance-back-btn:hover {
            transform: translateY(-2px);

            box-shadow:
                0 10px 25px
                rgba(255,212,0,.20);
        }


        /* ======================================================
           MAIN DETAILS CARD
        ====================================================== */

        .attendance-details-card {

            width: 100%;

            background: #151515;

            border: 1px solid #2b2b2b;

            border-radius: 20px;

            overflow: hidden;
        }


        /* ======================================================
           MEMBER HEADER
        ====================================================== */

        .attendance-member-header {

            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 25px;

            padding: 30px 32px;

            border-bottom: 1px solid #292929;
        }


        .attendance-member-info {

            display: flex;

            align-items: center;

            gap: 18px;
        }


        .attendance-member-avatar {

            width: 72px;
            height: 72px;

            flex-shrink: 0;

            display: flex;

            align-items: center;
            justify-content: center;

            background: #FFD400;

            border-radius: 50%;

            color: #000;

            font-family: 'Poppins', sans-serif;

            font-size: 25px;
            font-weight: 700;
        }


        .attendance-member-text h3 {

            margin: 0 0 4px;

            color: #fff;

            font-family: 'Poppins', sans-serif;

            font-size: 23px;
            font-weight: 700;
        }


        .attendance-member-text span {

            color: #8d8d8d;

            font-family: 'Poppins', sans-serif;

            font-size: 15px;
        }


        /* ======================================================
           STATUS
        ====================================================== */

        .attendance-status {

            display: inline-flex;

            align-items: center;
            justify-content: center;

            min-height: 40px;

            padding: 0 18px;

            border-radius: 20px;

            font-family: 'Poppins', sans-serif;

            font-size: 14px;
            font-weight: 700;
        }


        .attendance-status.checked-in {

            background: #123d20;

            color: #42e879;
        }


        .attendance-status.checked-out {

            background: #123d20;

            color: #42e879;
        }


        .attendance-status.default {

            background: #3d3210;

            color: #FFD400;
        }


        /* ======================================================
           INFORMATION GRID
        ====================================================== */

        .attendance-info-grid {

            display: grid;

            grid-template-columns:
                repeat(2, minmax(0, 1fr));

            gap: 20px;

            padding: 30px 32px;
        }


        /* ======================================================
           INFO BOX
        ====================================================== */

        .attendance-info-box {

            min-height: 115px;

            padding: 22px;

            background: #0f0f0f;

            border: 1px solid #2d2d2d;

            border-radius: 14px;
        }


        .attendance-info-label {

            display: flex;

            align-items: center;

            gap: 10px;

            margin-bottom: 10px;

            color: #8c8c8c;

            font-family: 'Poppins', sans-serif;

            font-size: 14px;
            font-weight: 500;
        }


        .attendance-info-label i {

            width: 20px;

            color: #FFD400;

            font-size: 15px;
        }


        .attendance-info-value {

            color: #fff;

            font-family: 'Poppins', sans-serif;

            font-size: 18px;
            font-weight: 600;
        }


        /* ======================================================
           FULL WIDTH BOX
        ====================================================== */

        .attendance-info-box.full {

            grid-column: 1 / -1;
        }


        /* ======================================================
           FOOTER
        ====================================================== */

        .attendance-details-footer {

            display: flex;

            align-items: center;
            justify-content: space-between;

            padding: 22px 32px;

            border-top: 1px solid #292929;

            background: #121212;
        }


        .attendance-record-id {

            color: #777;

            font-family: 'Poppins', sans-serif;

            font-size: 14px;
        }


        .attendance-record-id strong {

            color: #fff;

            font-weight: 600;
        }


        /* ======================================================
           RESPONSIVE
        ====================================================== */

        @media (max-width: 900px) {

            .attendance-view-header {

                flex-direction: column;

            }

            .attendance-back-btn {

                width: 100%;

            }

            .attendance-info-grid {

                grid-template-columns: 1fr;

            }

            .attendance-info-box.full {

                grid-column: auto;

            }

        }


        @media (max-width: 600px) {

            .attendance-view-header h2 {

                font-size: 30px;

            }

            .attendance-view-header p {

                font-size: 15px;

            }

            .attendance-member-header {

                align-items: flex-start;

                flex-direction: column;

            }

            .attendance-details-footer {

                align-items: flex-start;

                flex-direction: column;

                gap: 10px;

            }

        }

    </style>

</head>


<body>

<div class="wrapper">


    <!-- ======================================================
         SIDEBAR
    ====================================================== -->

    <?php include("includes/admin_sidebar.php"); ?>


    <!-- ======================================================
         MAIN
    ====================================================== -->

    <div class="main">

        <!-- CONTENT -->

        <div class="dashboard-content attendance-view-content">


            <!-- ==================================================
                 PAGE HEADER
            ================================================== -->

            <div class="attendance-view-header">

                <div>

                    <h2>
                        Attendance Details
                    </h2>

                    <p>
                        View attendance and check-in information.
                    </p>

                </div>


                <a
                    href="attendance_admin.php"
                    class="back-staff-btn"
                >

                    <i class="fa-solid fa-arrow-left"></i>

                    Back to Attendance

                </a>

            </div>


            <!-- ==================================================
                 DETAILS CARD
            ================================================== -->

            <div class="attendance-details-card">


                <!-- MEMBER HEADER -->

                <div class="attendance-member-header">


                    <div class="attendance-member-info">


                        <div class="attendance-member-avatar">

                            <?= htmlspecialchars($initial) ?>

                        </div>


                        <div class="attendance-member-text">

                            <h3>
                                <?= htmlspecialchars($memberName) ?>
                            </h3>

                            <span>
                                Member #<?= (int)$attendance['member_id'] ?>
                            </span>

                        </div>


                    </div>


                    <!-- STATUS -->

                    <?php

                    $displayStatusClass = 'default';

                    if ($status === 'Checked In') {
                        $displayStatusClass = 'checked-in';
                    }

                    if ($status === 'Checked Out') {
                        $displayStatusClass = 'checked-out';
                    }

                    ?>

                    <span
                        class="attendance-status <?= $displayStatusClass ?>"
                    >

                        <i
                            class="fa-solid
                            <?= $status === 'Checked In'
                                ? 'fa-right-to-bracket'
                                : 'fa-circle-check'
                            ?>"
                            style="margin-right:8px;"
                        ></i>

                        <?= htmlspecialchars($status) ?>

                    </span>


                </div>


                <!-- ==================================================
                     INFORMATION
                ================================================== -->

                <div class="attendance-info-grid">


                    <!-- ATTENDANCE ID -->

                    <div class="attendance-info-box">

                        <div class="attendance-info-label">

                            <i class="fa-solid fa-hashtag"></i>

                            Attendance ID

                        </div>

                        <div class="attendance-info-value">

                            #<?= (int)$attendance['attendance_id'] ?>

                        </div>

                    </div>


                    <!-- MEMBER ID -->

                    <div class="attendance-info-box">

                        <div class="attendance-info-label">

                            <i class="fa-solid fa-user"></i>

                            Member ID

                        </div>

                        <div class="attendance-info-value">

                            #<?= (int)$attendance['member_id'] ?>

                        </div>

                    </div>


                    <!-- ATTENDANCE DATE -->

                    <div class="attendance-info-box">

                        <div class="attendance-info-label">

                            <i class="fa-solid fa-calendar-days"></i>

                            Attendance Date

                        </div>

                        <div class="attendance-info-value">

                            <?= date(
                                "M d, Y",
                                strtotime($attendance['attendance_date'])
                            ) ?>

                        </div>

                    </div>


                    <!-- CHECK IN -->

                    <div class="attendance-info-box">

                        <div class="attendance-info-label">

                            <i class="fa-solid fa-right-to-bracket"></i>

                            Check In

                        </div>

                        <div class="attendance-info-value">

                            <?php if (!empty($attendance['check_in'])): ?>

                                <?= date(
                                    "h:i A",
                                    strtotime($attendance['check_in'])
                                ) ?>

                            <?php else: ?>

                                —

                            <?php endif; ?>

                        </div>

                    </div>


                    <!-- CHECK OUT -->

                    <div class="attendance-info-box">

                        <div class="attendance-info-label">

                            <i class="fa-solid fa-right-from-bracket"></i>

                            Check Out

                        </div>

                        <div class="attendance-info-value">

                            <?php if (!empty($attendance['check_out'])): ?>

                                <?= date(
                                    "h:i A",
                                    strtotime($attendance['check_out'])
                                ) ?>

                            <?php else: ?>

                                —

                            <?php endif; ?>

                        </div>

                    </div>


                    <!-- STATUS -->

                    <div class="attendance-info-box">

                        <div class="attendance-info-label">

                            <i class="fa-solid fa-circle-check"></i>

                            Attendance Status

                        </div>

                        <div class="attendance-info-value">

                            <?= htmlspecialchars($status) ?>

                        </div>

                    </div>


                </div>


                <!-- ==================================================
                     FOOTER
                ================================================== -->

                <div class="attendance-details-footer">

                    <div class="attendance-record-id">

                        Attendance Record:

                        <strong>
                            #<?= (int)$attendance['attendance_id'] ?>
                        </strong>

                    </div>


                    <div class="attendance-record-id">

                        Member:

                        <strong>
                            #<?= (int)$attendance['member_id'] ?>
                        </strong>

                    </div>

                </div>


            </div>


        </div>

    </div>

</div>

</body>

</html>