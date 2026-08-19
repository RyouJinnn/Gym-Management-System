<?php

include("includes/staff_auth.php");

/* ======================================================
   DATE / FILTERS
====================================================== */

date_default_timezone_set("Asia/Manila");

$search = trim($_GET['search'] ?? '');
$status = $_GET['status'] ?? '';
$date = $_GET['date'] ?? '';

/* ======================================================
   SUMMARY
====================================================== */

$totalAttendance = 0;
$checkedIn = 0;
$checkedOut = 0;
$todayAttendance = 0;

/* Total attendance */
$result = $con->query("
    SELECT COUNT(*) AS total
    FROM attendance
");

if ($result) {
    $totalAttendance = (int)$result->fetch_assoc()['total'];
}

/* Checked In */
$result = $con->query("
    SELECT COUNT(*) AS total
    FROM attendance
    WHERE status = 'Checked In'
");

if ($result) {
    $checkedIn = (int)$result->fetch_assoc()['total'];
}

/* Checked Out */
$result = $con->query("
    SELECT COUNT(*) AS total
    FROM attendance
    WHERE status = 'Checked Out'
");

if ($result) {
    $checkedOut = (int)$result->fetch_assoc()['total'];
}

/* Today */
$result = $con->query("
    SELECT COUNT(*) AS total
    FROM attendance
    WHERE attendance_date = CURDATE()
");

if ($result) {
    $todayAttendance = (int)$result->fetch_assoc()['total'];
}

/* ======================================================
   ATTENDANCE RECORDS
====================================================== */

$sql = "
    SELECT
        a.attendance_id,
        a.member_id,
        a.check_in,
        a.check_out,
        a.attendance_date,
        a.status,

        s.first_name,
        s.middlename,
        s.last_name,
        s.suffix

    FROM attendance a

    LEFT JOIN signup s
        ON a.member_id = s.id

    WHERE 1=1
";

/* SEARCH */
if ($search !== '') {

    $sql .= "
        AND (
            CONCAT(
                s.first_name, ' ',
                s.middlename, ' ',
                s.last_name, ' ',
                s.suffix
            ) LIKE ?
            OR a.member_id LIKE ?
        )
    ";
}

/* STATUS FILTER */
if ($status !== '') {
    $sql .= " AND a.status = ? ";
}

/* DATE FILTER */
if ($date !== '') {
    $sql .= " AND a.attendance_date = ? ";
}

$sql .= "
    ORDER BY
        a.attendance_date DESC,
        a.check_in DESC
";

$stmt = $con->prepare($sql);

if (!$stmt) {
    die("SQL Error: " . $con->error);
}

/* ======================================================
   BIND PARAMETERS
====================================================== */

$types = "";
$params = [];

if ($search !== '') {

    $searchValue = "%" . $search . "%";

    $types .= "ss";
    $params[] = $searchValue;
    $params[] = $searchValue;
}

if ($status !== '') {
    $types .= "s";
    $params[] = $status;
}

if ($date !== '') {
    $types .= "s";
    $params[] = $date;
}

if ($types !== '') {

    $stmt->bind_param(
        $types,
        ...$params
    );
}

$stmt->execute();

$attendanceResult = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Attendance | Staff Panel</title>

    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Orbitron:wght@600;700&family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet"
    >

    <!-- Font Awesome -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
    >

    <!-- EXISTING staff CSS -->
    <link
        rel="stylesheet"
        href="assets/css/admin.css"
    >
</head>

<body>

<div class="wrapper">

    <!-- SIDEBAR -->
    <?php include("includes/staff_sidebar.php"); ?>


    <!-- MAIN CONTENT -->
    <div class="main">

        <div class="dashboard-content payments-content">


            <!-- ================================
                 PAGE HEADER
            ================================= -->

            <div class="page-header">

                <div>

                    <h2>
                        Attendance
                    </h2>

                    <p>
                        Manage gym member attendance and check-in records.
                    </p>

                </div>


                <div class="page-header-actions">

    <div class="total-members-box">
        <i class="fa-solid fa-calendar-check"></i>
        <strong>
            <?= number_format($totalAttendance) ?>
        </strong>
        Records
    </div>

    <a href="attendance_add_staff.php" class="btn-primary attendance-add-btn">
        <i class="fa-solid fa-user-check"></i>
        Record Attendance
    </a>

</div>

            </div>


            <!-- ================================
                 STAT CARDS
            ================================= -->

            <div class="stats-grid attendance-stats-grid" style="
    display: grid;
    grid-template-columns: repeat(4, minmax(200px, 1fr));
    gap: 10px;
    max-width: 1200px;
    margin: 0 auto 20px;
">


                <!-- TOTAL -->

                <div class="stat-card">

                    <div class="stat-icon">

                        <i class="fa-solid fa-calendar-check"></i>

                    </div>

                    <div>

                        <span>
                            Total Attendance
                        </span>

                        <strong>
                            <?= number_format($totalAttendance) ?>
                        </strong>

                    </div>

                </div>


                <!-- TODAY -->

                <div class="stat-card">

                    <div class="stat-icon">

                        <i class="fa-solid fa-calendar-day"></i>

                    </div>

                    <div>

                        <span>
                            Today's Attendance
                        </span>

                        <strong>
                            <?= number_format($todayAttendance) ?>
                        </strong>

                    </div>

                </div>


                <!-- CHECKED IN -->

                <div class="stat-card">

                    <div class="stat-icon">

                        <i class="fa-solid fa-right-to-bracket"></i>

                    </div>

                    <div>

                        <span>
                            Checked In
                        </span>

                        <strong>
                            <?= number_format($checkedIn) ?>
                        </strong>

                    </div>

                </div>


                <!-- CHECKED OUT -->

                <div class="stat-card">

                    <div class="stat-icon">

                        <i class="fa-solid fa-right-from-bracket"></i>

                    </div>

                    <div>

                        <span>
                            Checked Out
                        </span>

                        <strong>
                            <?= number_format($checkedOut) ?>
                        </strong>

                    </div>

                </div>

            </div>


           <!-- ================================
     SEARCH AND FILTER
================================= -->

<div class="staff-tools attendance-tools">

    <form method="GET"
          action="attendance_staff.php"
          class="staff-search attendance-search">

        <!-- SEARCH -->
        <div class="search-box">
            <i class="fa-solid fa-magnifying-glass"></i>

            <input
                type="text"
                name="search"
                placeholder="Search member..."
                value="<?= htmlspecialchars($search) ?>"
            >
        </div>


        <!-- STATUS -->
        <select name="status">

            <option value="">
                All Status
            </option>

            <option
                value="Checked In"
                <?= ($status === "Checked In") ? "selected" : "" ?>
            >
                Checked In
            </option>

            <option
                value="Checked Out"
                <?= ($status === "Checked Out") ? "selected" : "" ?>
            >
                Checked Out
            </option>

        </select>


        <!-- DATE -->
        <input
            type="date"
            name="date"
            value="<?= htmlspecialchars($date) ?>"
        >


        <!-- FILTER -->
        <button
            type="submit"
            class="staff-filter-btn"
        >
            <i class="fa-solid fa-filter"></i>
            Filter
        </button>

    </form>

</div>

            
            <!-- ================================
     ATTENDANCE TABLE
================================= -->

<div class="staff-table-card attendance-table-card">

    <div class="table-wrapper">

        <table class="staff-table attendance-table">

            <thead>

                <tr>
                    <th>Member</th>
                    <th>Attendance Date</th>
                    <th>Check In</th>
                    <th>Check Out</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>

            </thead>


            <tbody>

            <?php if ($attendanceResult->num_rows > 0): ?>

                <?php while ($row = $attendanceResult->fetch_assoc()): ?>

                    <?php

                    $memberName = trim(
                        ($row['first_name'] ?? '') . ' ' .
                        ($row['middlename'] ?? '') . ' ' .
                        ($row['last_name'] ?? '') . ' ' .
                        ($row['suffix'] ?? '')
                    );

                    if ($memberName === '') {
                        $memberName = 'Unknown Member';
                    }

                    $statusClass = strtolower(
                        str_replace(
                            ' ',
                            '-',
                            $row['status'] ?? ''
                        )
                    );

                    ?>

                    <tr>

                        <!-- MEMBER -->
                        <td>

                            <div class="staff-name attendance-member-name">

                                <div class="staff-avatar attendance-avatar">
                                    <?= strtoupper(substr($memberName, 0, 1)) ?>
                                </div>

                                <div>

                                    <strong>
                                        <?= htmlspecialchars($memberName) ?>
                                    </strong>

                                    <small>
                                        Member #<?= (int)$row['member_id'] ?>
                                    </small>

                                </div>

                            </div>

                        </td>


                        <!-- DATE -->
                        <td>
                            <?= date(
                                "M d, Y",
                                strtotime($row['attendance_date'])
                            ) ?>
                        </td>


                        <!-- CHECK IN -->
                        <td>

                            <?php if (!empty($row['check_in'])): ?>

                                <?= date(
                                    "h:i A",
                                    strtotime($row['check_in'])
                                ) ?>

                            <?php else: ?>

                                —

                            <?php endif; ?>

                        </td>


                        <!-- CHECK OUT -->
                        <td>

                            <?php if (!empty($row['check_out'])): ?>

                                <?= date(
                                    "h:i A",
                                    strtotime($row['check_out'])
                                ) ?>

                            <?php else: ?>

                                —

                            <?php endif; ?>

                        </td>


                        <!-- STATUS -->
                        <td>

                            <span
                                class="staff-status attendance-status <?= htmlspecialchars($statusClass) ?>"
                            >
                                <?= htmlspecialchars(
                                    $row['status'] ?? 'Unknown'
                                ) ?>
                            </span>

                        </td>


                        <!-- ACTION -->
                        <td>

                            <div class="staff-actions attendance-actions">

                                <a
                                    href="attendance_view_staff.php?id=<?= (int)$row['attendance_id'] ?>"
                                    class="staff-view-btn"
                                    title="View Attendance"
                                >
                                    <i class="fa-solid fa-eye"></i>
                                </a>

                            </div>

                        </td>

                    </tr>

                <?php endwhile; ?>

            <?php else: ?>

                <tr>

                    <td
                        colspan="6"
                        class="no-staff"
                    >

                        <i class="fa-solid fa-calendar-xmark"></i>

                        <strong>
                            No attendance records found
                        </strong>

                        <span>
                            Try changing your search or filter.
                        </span>

                    </td>

                </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>

        </div>

    </div>

</div>

</body>

</html>

<?php
$stmt->close();
$con->close();
?>