<?php

include("includes/staff_auth.php");

date_default_timezone_set("Asia/Manila");

$message = "";
$messageType = "";

/* ======================================================
   RECORD ATTENDANCE
====================================================== */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $memberId = (int)($_POST['member_id'] ?? 0);
    $action = $_POST['action'] ?? '';

    if ($memberId <= 0) {

        $message = "Please select a member.";
        $messageType = "error";

    } else {

        /* ==============================================
           CHECK MEMBER
        ============================================== */

        $memberStmt = $con->prepare("
            SELECT
                s.id,
                s.first_name,
                s.middlename,
                s.last_name,
                s.suffix
            FROM signup s
            WHERE s.id = ?
            LIMIT 1
        ");

        $memberStmt->bind_param("i", $memberId);
        $memberStmt->execute();

        $member = $memberStmt->get_result()->fetch_assoc();

        $memberStmt->close();

        if (!$member) {

            $message = "Member not found.";
            $messageType = "error";

        } else {

            $memberName = trim(
                ($member['first_name'] ?? '') . ' ' .
                ($member['middlename'] ?? '') . ' ' .
                ($member['last_name'] ?? '') . ' ' .
                ($member['suffix'] ?? '')
            );

            /* ==========================================
               CHECK ACTIVE MEMBERSHIP
            ========================================== */

            $membershipStmt = $con->prepare("
                SELECT membership_id
                FROM membership
                WHERE member_id = ?
                AND status = 'Active'
                AND end_date >= CURDATE()
                LIMIT 1
            ");

            $membershipStmt->bind_param("i", $memberId);
            $membershipStmt->execute();

            $membership = $membershipStmt
                ->get_result()
                ->fetch_assoc();

            $membershipStmt->close();

            if (!$membership) {

                $message = $memberName .
                    " does not have an active membership.";

                $messageType = "error";

            } else {

                /* ======================================
                   CHECK TODAY'S ATTENDANCE
                ====================================== */

                $today = date("Y-m-d");
                $now = date("Y-m-d H:i:s");

                $attendanceStmt = $con->prepare("
                    SELECT *
                    FROM attendance
                    WHERE member_id = ?
                    AND attendance_date = ?
                    LIMIT 1
                ");

                $attendanceStmt->bind_param(
                    "is",
                    $memberId,
                    $today
                );

                $attendanceStmt->execute();

                $attendance = $attendanceStmt
                    ->get_result()
                    ->fetch_assoc();

                $attendanceStmt->close();


                /* ======================================
                   CHECK IN
                ====================================== */

                if ($action === "check_in") {

                    if ($attendance) {

                        if (empty($attendance['check_out'])) {

                            $message =
                                $memberName .
                                " is already checked in.";

                            $messageType = "error";

                        } else {

                            $message =
                                $memberName .
                                " already has an attendance record today.";

                            $messageType = "error";
                        }

                    } else {

                        $insertStmt = $con->prepare("
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

                        $insertStmt->bind_param(
                            "iss",
                            $memberId,
                            $today,
                            $now
                        );

                        if ($insertStmt->execute()) {

                            $message =
                                $memberName .
                                " has been successfully checked in.";

                            $messageType = "success";

                        } else {

                            $message =
                                "Failed to record attendance.";

                            $messageType = "error";
                        }

                        $insertStmt->close();
                    }
                }


                /* ======================================
                   CHECK OUT
                ====================================== */

                elseif ($action === "check_out") {

                    if (!$attendance) {

                        $message =
                            $memberName .
                            " has not checked in today.";

                        $messageType = "error";

                    } elseif (!empty($attendance['check_out'])) {

                        $message =
                            $memberName .
                            " is already checked out.";

                        $messageType = "error";

                    } else {

                        $updateStmt = $con->prepare("
                            UPDATE attendance
                            SET
                                check_out = ?,
                                status = 'Checked Out'
                            WHERE attendance_id = ?
                        ");

                        $updateStmt->bind_param(
                            "si",
                            $now,
                            $attendance['attendance_id']
                        );

                        if ($updateStmt->execute()) {

                            $message =
                                $memberName .
                                " has been successfully checked out.";

                            $messageType = "success";

                        } else {

                            $message =
                                "Failed to update attendance.";

                            $messageType = "error";
                        }

                        $updateStmt->close();
                    }

                } else {

                    $message = "Invalid attendance action.";
                    $messageType = "error";
                }
            }
        }
    }
}


/* ======================================================
   SEARCH MEMBERS
====================================================== */

$search = trim($_GET['search'] ?? '');

$membersSql = "
    SELECT
        s.id,
        s.first_name,
        s.middlename,
        s.last_name,
        s.suffix,

        CASE
            WHEN EXISTS (
                SELECT 1
                FROM membership m
                WHERE m.member_id = s.id
                AND m.status = 'Active'
                AND m.end_date >= CURDATE()
            )
            THEN 1
            ELSE 0
        END AS has_membership

    FROM signup s
    WHERE 1=1
";

if ($search !== '') {

    $membersSql .= "
        AND (
            CONCAT(
                s.first_name, ' ',
                s.middlename, ' ',
                s.last_name, ' ',
                s.suffix
            ) LIKE ?
            OR s.id LIKE ?
        )
    ";
}

$membersSql .= "
    ORDER BY
        s.first_name ASC,
        s.last_name ASC
";

$membersStmt = $con->prepare($membersSql);

if ($search !== '') {

    $searchValue = "%" . $search . "%";

    $membersStmt->bind_param(
        "ss",
        $searchValue,
        $searchValue
    );
}

$membersStmt->execute();

$membersResult = $membersStmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Record Attendance | Staff Panel</title>

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

    <style>

        .attendance-add-btn {
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .attendance-add-container {
            background: #161616;
            border: 1px solid #2d2d2d;
            border-radius: 20px;
            padding: 28px;
            margin-top: 30px;
        }

        .attendance-search-form {
            display: flex;
            gap: 14px;
            width: 100%;
            margin-bottom: 25px;
        }

        .attendance-search-form .search-box {
            flex: 1;
        }

        .attendance-search-form button {
            min-width: 130px;
        }

        .member-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .member-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;

            padding: 18px 20px;

            background: #101010;
            border: 1px solid #292929;
            border-radius: 15px;
        }

        .member-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .member-avatar {
            width: 50px;
            height: 50px;

            border-radius: 50%;

            background: #ffd400;
            color: #000;

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 18px;
            font-weight: 700;
        }

        .member-details strong {
            display: block;
            color: #fff;
            font-size: 16px;
        }

        .member-details small {
            display: block;
            margin-top: 3px;
            color: #888;
        }

        .membership-active {
            color: #35e56b;
            font-size: 13px;
            font-weight: 600;
        }

        .membership-inactive {
            color: #ff5555;
            font-size: 13px;
            font-weight: 600;
        }

        .attendance-actions {
            display: flex;
            gap: 8px;
        }

        .attendance-action-btn {
            border: none;
            border-radius: 10px;
            padding: 11px 15px;

            font-family: 'Poppins', sans-serif;
            font-weight: 600;

            cursor: pointer;

            display: inline-flex;
            align-items: center;
            gap: 7px;
        }

        .check-in-btn {
            background: #ffd400;
            color: #000;
        }

        .check-out-btn {
            background: #292929;
            color: #fff;
        }

        .disabled-btn {
            background: #252525;
            color: #666;
            cursor: not-allowed;
        }

        .attendance-message {
            padding: 15px 18px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-weight: 500;
        }

        .attendance-message.success {
            background: rgba(35, 200, 90, 0.12);
            border: 1px solid #207a3d;
            color: #45e878;
        }

        .attendance-message.error {
            background: rgba(255, 70, 70, 0.12);
            border: 1px solid #7a2525;
            color: #ff6b6b;
        }

        @media (max-width: 800px) {

            .attendance-search-form {
                flex-direction: column;
            }

            .member-row {
                flex-direction: column;
                align-items: flex-start;
            }

            .attendance-actions {
                width: 100%;
            }

            .attendance-action-btn {
                flex: 1;
                justify-content: center;
            }

        }

    </style>

</head>

<body>

<div class="wrapper">

    <?php include("includes/staff_sidebar.php"); ?>

    <div class="main">

        <div class="dashboard-content">

            <!-- PAGE HEADER -->

            <div class="staff-form-header">

                <div>

                    <h2>
                        Record Attendance
                    </h2>

                    <p>
                        Search for a member and record their gym attendance.
                    </p>

                </div>

               <div class="page-header-actions">

    <a
        href="attendance_staff.php"
        class="back-members"
    >
        <i class="fa-solid fa-arrow-left"></i>
        Back to Attendance
    </a>

</div>

            </div>


            <!-- MESSAGE -->

            <?php if ($message !== ''): ?>

                <div class="attendance-message <?= $messageType ?>">

                    <i class="fa-solid
                        <?= $messageType === 'success'
                            ? 'fa-circle-check'
                            : 'fa-circle-exclamation'
                        ?>">
                    </i>

                    <?= htmlspecialchars($message) ?>

                </div>

            <?php endif; ?>


            <!-- SEARCH -->

            <div class="attendance-add-container">

                <form
                    method="GET"
                    action="attendance_add_staff.php"
                    class="attendance-search-form"
                >

                    <div class="search-box">

                        <i class="fa-solid fa-magnifying-glass"></i>

                        <input
                            type="text"
                            name="search"
                            placeholder="Search member..."
                            value="<?= htmlspecialchars($search) ?>"
                        >

                    </div>

                    <button
                        type="submit"
                        class="btn-primary"
                    >
                        <i class="fa-solid fa-magnifying-glass"></i>
                        Search
                    </button>

                </form>


                <!-- MEMBER LIST -->

                <div class="member-list">

                    <?php if ($membersResult->num_rows > 0): ?>

                        <?php while ($member = $membersResult->fetch_assoc()): ?>

                            <?php

                            $memberName = trim(
                                ($member['first_name'] ?? '') . ' ' .
                                ($member['middlename'] ?? '') . ' ' .
                                ($member['last_name'] ?? '') . ' ' .
                                ($member['suffix'] ?? '')
                            );

                            if ($memberName === '') {
                                $memberName = 'Unknown Member';
                            }

                            $initial =
                                strtoupper(
                                    substr($memberName, 0, 1)
                                );

                            ?>

                            <div class="member-row">

                                <div class="member-info">

                                    <div class="member-avatar">
                                        <?= htmlspecialchars($initial) ?>
                                    </div>

                                    <div class="member-details">

                                        <strong>
                                            <?= htmlspecialchars($memberName) ?>
                                        </strong>

                                        <small>
                                            Member #<?= (int)$member['id'] ?>
                                        </small>

                                        <?php if ($member['has_membership']): ?>

                                            <span class="membership-active">
                                                Active Membership
                                            </span>

                                        <?php else: ?>

                                            <span class="membership-inactive">
                                                No Active Membership
                                            </span>

                                        <?php endif; ?>

                                    </div>

                                </div>


                                <div class="attendance-actions">

                                    <?php if ($member['has_membership']): ?>

                                        <form
                                            method="POST"
                                            style="display:inline;"
                                        >

                                            <input
                                                type="hidden"
                                                name="member_id"
                                                value="<?= (int)$member['id'] ?>"
                                            >

                                            <input
                                                type="hidden"
                                                name="action"
                                                value="check_in"
                                            >

                                            <button
                                                type="submit"
                                                class="attendance-action-btn check-in-btn"
                                            >

                                                <i class="fa-solid fa-right-to-bracket"></i>

                                                Check In

                                            </button>

                                        </form>


                                        <form
                                            method="POST"
                                            style="display:inline;"
                                        >

                                            <input
                                                type="hidden"
                                                name="member_id"
                                                value="<?= (int)$member['id'] ?>"
                                            >

                                            <input
                                                type="hidden"
                                                name="action"
                                                value="check_out"
                                            >

                                            <button
                                                type="submit"
                                                class="attendance-action-btn check-out-btn"
                                            >

                                                <i class="fa-solid fa-right-from-bracket"></i>

                                                Check Out

                                            </button>

                                        </form>

                                    <?php else: ?>

                                        <button
                                            type="button"
                                            class="attendance-action-btn disabled-btn"
                                            disabled
                                        >

                                            <i class="fa-solid fa-lock"></i>

                                            No Membership

                                        </button>

                                    <?php endif; ?>

                                </div>

                            </div>

                        <?php endwhile; ?>

                    <?php else: ?>

                        <div class="attendance-message error">

                            <i class="fa-solid fa-user-slash"></i>

                            No members found.

                        </div>

                    <?php endif; ?>

                </div>

            </div>

        </div>

    </div>

</div>

</body>

</html>

<?php

$membersStmt->close();
$con->close();

?>