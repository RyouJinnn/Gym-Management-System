<?php

include("includes/admin_auth.php");

/* ======================================================
   DELETE ATTENDANCE
====================================================== */

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_attendance'])) {

    $attendanceId = (int)($_POST['attendance_id'] ?? 0);

    if ($attendanceId > 0) {

        $deleteStmt = $con->prepare(
            "DELETE FROM attendance WHERE attendance_id = ?"
        );

        $deleteStmt->bind_param("i", $attendanceId);

        if ($deleteStmt->execute()) {

            if ($deleteStmt->affected_rows > 0) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Attendance record deleted successfully!'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Attendance record was not found.'
                ]);
            }

        } else {

            echo json_encode([
                'success' => false,
                'message' => 'Failed to delete attendance record.'
            ]);
        }

        $deleteStmt->close();
    } else {

        echo json_encode([
            'success' => false,
            'message' => 'Invalid attendance ID.'
        ]);
    }

    exit;
}

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

    <title>Attendance | Admin Panel</title>

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

    <!-- EXISTING ADMIN CSS -->
    <link
        rel="stylesheet"
        href="assets/css/admin.css"
    >

    <style>

/* DELETE MODAL */
.delete-modal {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.78);
    backdrop-filter: blur(5px);
    z-index: 9999;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.delete-modal-box {
    width: 430px;
    max-width: 100%;
    background: #181818;
    border: 1px solid #333;
    border-radius: 20px;
    padding: 35px 30px 30px;
    text-align: center;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.65);
    animation: deleteModalShow 0.2s ease;
}

@keyframes deleteModalShow {
    from {
        opacity: 0;
        transform: scale(0.92);
    }

    to {
        opacity: 1;
        transform: scale(1);
    }
}

.delete-modal-icon {
    width: 70px;
    height: 70px;
    margin: 0 auto 20px;
    border-radius: 50%;
    background: rgba(255, 59, 59, 0.12);
    border: 2px solid #ff3b3b;
    color: #ff3b3b;

    display: flex;
    align-items: center;
    justify-content: center;

    font-size: 27px;
}

.delete-modal-box h3 {
    margin: 0 0 10px;
    color: #fff;
    font-family: 'Poppins', sans-serif;
    font-size: 23px;
    font-weight: 700;
}

.delete-modal-box p {
    margin: 0 auto 28px;
    max-width: 340px;
    color: #aaa;
    font-family: 'Poppins', sans-serif;
    font-size: 14px;
    line-height: 1.6;
}

.delete-modal-buttons {
    display: flex;
    justify-content: center;
    gap: 12px;
}

.delete-no-btn,
.delete-yes-btn {
    min-width: 120px;
    border: none;
    padding: 12px 22px;
    border-radius: 9px;

    font-family: 'Poppins', sans-serif;
    font-size: 14px;
    font-weight: 600;

    cursor: pointer;
    transition: 0.2s ease;
}

.delete-no-btn {
    background: #303030;
    color: #fff;
}

.delete-no-btn:hover {
    background: #404040;
}

.delete-yes-btn {
    background: #ff3b3b;
    color: #fff;
}

.delete-yes-btn:hover {
    background: #e52f2f;
    transform: translateY(-1px);
}

/* SUCCESS MESSAGE */
.success-message {
    position: fixed;
    top: 25px;
    right: 25px;

    min-width: 320px;
    max-width: 420px;

    display: flex;
    align-items: center;
    gap: 12px;

    background: #16852c;
    color: #fff;

    padding: 15px 20px;
    border-radius: 10px;

    font-family: 'Poppins', sans-serif;
    font-size: 14px;
    font-weight: 500;

    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.45);

    transform: translateX(120%);
    opacity: 0;

    transition: all 0.3s ease;

    z-index: 10000;
}

.success-message i {
    font-size: 19px;
    flex-shrink: 0;
}

.success-message.show {
    transform: translateX(0);
    opacity: 1;
}
</style>

</head>

<body>

<div class="wrapper">

    <!-- SIDEBAR -->
    <?php include("includes/admin_sidebar.php"); ?>


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

    <a href="attendance_add_admin.php" class="btn-primary attendance-add-btn">
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
          action="attendance_admin.php"
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
                                    href="attendance_view_admin.php?id=<?= (int)$row['attendance_id'] ?>"
                                    class="staff-view-btn"
                                    title="View Attendance"
                                >
                                    <i class="fa-solid fa-eye"></i>
                                </a>


                                <a
    href="#"
    class="staff-delete-btn"
    title="Delete Attendance"
    onclick="openDeleteModal(<?= (int)$row['attendance_id'] ?>, this); return false;"
>
    <i class="fa-solid fa-trash"></i>
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
<!-- DELETE CONFIRMATION MODAL -->
<div id="deleteModal" class="delete-modal">

    <div class="delete-modal-box">

        <div class="delete-modal-icon">
            <i class="fa-solid fa-trash"></i>
        </div>

        <h3>Delete Attendance?</h3>

        <p>
            Are you sure you want to delete this attendance record?
        </p>

        <div class="delete-modal-buttons">

            <button
                type="button"
                class="delete-no-btn"
                onclick="closeDeleteModal()"
            >
                No
            </button>

            <button
                type="button"
                class="delete-yes-btn"
                onclick="deleteAttendance()"
            >
                Yes, Delete
            </button>

        </div>

    </div>

</div>

<!-- SUCCESS MESSAGE -->
<div id="successMessage" class="success-message">
    <i class="fa-solid fa-circle-check"></i>
    <span>Attendance record deleted successfully!</span>
</div>


<script>

let deleteAttendanceId = null;
let deleteButton = null;

function openDeleteModal(attendanceId, button) {

    deleteAttendanceId = attendanceId;
    deleteButton = button;

    document.getElementById('deleteModal').style.display = 'flex';
}

function closeDeleteModal() {

    deleteAttendanceId = null;
    deleteButton = null;

    document.getElementById('deleteModal').style.display = 'none';
}

function deleteAttendance() {

    if (!deleteAttendanceId) {
        return;
    }

    const formData = new FormData();

    formData.append('delete_attendance', '1');
    formData.append('attendance_id', deleteAttendanceId);

    fetch('attendance_admin.php', {
        method: 'POST',
        body: formData
    })

    .then(response => response.json())

    .then(data => {

        if (data.success) {

            // Remove the whole table row
            if (deleteButton) {

                const row = deleteButton.closest('tr');

                if (row) {
                    row.remove();
                }
            }

            // Close popup
            closeDeleteModal();

            // Show success message
            showSuccessMessage(data.message);

        } else {

            alert(data.message);
        }

    })

    .catch(error => {

        console.error(error);

        alert('Something went wrong while deleting the record.');

    });
}

function showSuccessMessage(message) {

    const successMessage =
        document.getElementById('successMessage');

    successMessage.querySelector('span').textContent = message;

    successMessage.classList.add('show');

    setTimeout(() => {

        successMessage.classList.remove('show');

    }, 3000);
}

</script>

</body>

</html>

<?php

$stmt->close();
$con->close();

?>