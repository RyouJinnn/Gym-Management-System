<?php

include("includes/admin_auth.php");


// ==========================================
// GET FILTERS
// ==========================================

$date_from = $_GET['date_from'] ?? '';
$date_to   = $_GET['date_to'] ?? '';
$plan      = $_GET['plan'] ?? '';
$status    = $_GET['status'] ?? '';


// ==========================================
// LOAD MEMBERSHIP PLANS
// ==========================================

$plans = [];

$planQuery = $con->query("
    SELECT plan_id, plan_name
    FROM membership_plans
    WHERE status = 'Active'
    ORDER BY plan_name ASC
");

if ($planQuery) {

    while ($row = $planQuery->fetch_assoc()) {

        $plans[] = $row;

    }

}


// ==========================================
// BUILD MEMBERSHIP REPORT QUERY
// ==========================================

$sql = "
    SELECT
        m.membership_id,
        m.member_id,
        m.plan_id,
        m.plan_name,
        m.price,
        m.duration,
        m.start_date,
        m.end_date,
        m.status,

        s.first_name,
        s.middlename,
        s.last_name

    FROM membership m

    INNER JOIN signup s
        ON m.member_id = s.id

    WHERE 1=1
";

$params = [];
$types = '';


// ==========================================
// DATE FROM
// ==========================================

if ($date_from !== '') {

    $sql .= " AND m.start_date >= ? ";

    $params[] = $date_from;
    $types .= 's';

}


// ==========================================
// DATE TO
// ==========================================

if ($date_to !== '') {

    $sql .= " AND m.start_date <= ? ";

    $params[] = $date_to;
    $types .= 's';

}


// ==========================================
// MEMBERSHIP PLAN
// ==========================================

if ($plan !== '') {

    $sql .= " AND m.plan_id = ? ";

    $params[] = $plan;
    $types .= 'i';

}


// ==========================================
// STATUS
// ==========================================

if ($status !== '') {

    $sql .= " AND m.status = ? ";

    $params[] = $status;
    $types .= 's';

}


$sql .= "
    ORDER BY m.start_date DESC
";


// ==========================================
// PREPARE QUERY
// ==========================================

$stmt = $con->prepare($sql);

if ($stmt === false) {

    die("SQL Error: " . $con->error);

}


// ==========================================
// BIND PARAMETERS
// ==========================================

if (!empty($params)) {

    $stmt->bind_param(
        $types,
        ...$params
    );

}


// ==========================================
// EXECUTE
// ==========================================

$stmt->execute();

$result = $stmt->get_result();


// ==========================================
// STORE REPORT RECORDS
// ==========================================

$memberships = [];

while ($row = $result->fetch_assoc()) {

    $memberships[] = $row;

}

$stmt->close();


// ==========================================
// SUMMARY COUNTS
// ==========================================

$total_memberships = count($memberships);

$active_count = 0;
$expired_count = 0;
$cancelled_count = 0;


foreach ($memberships as $membership) {

    if ($membership['status'] === 'Active') {

        $active_count++;

    } elseif ($membership['status'] === 'Expired') {

        $expired_count++;

    } elseif ($membership['status'] === 'Cancelled') {

        $cancelled_count++;

    }

}

/* =========================================================
   EXPORT MEMBERSHIP REPORT
========================================================= */

if (isset($_GET['export']) && $_GET['export'] === 'csv') {

    $exportSql = "
        SELECT
            CONCAT(
    s.first_name, ' ',
    s.middlename, ' ',
    s.last_name
) AS member_name,
            m.plan_name,
            m.start_date,
            m.end_date,
            m.price,
            m.status
        FROM membership m
        INNER JOIN signup s
            ON m.member_id = s.id
        WHERE 1=1
    ";

    $exportTypes = "";
    $exportParams = [];

    /* DATE FROM */
    if ($date_from !== '') {
        $exportSql .= " AND m.start_date >= ? ";
        $exportTypes .= "s";
        $exportParams[] = $date_from;
    }

    /* DATE TO */
    if ($date_to !== '') {
        $exportSql .= " AND m.start_date <= ? ";
        $exportTypes .= "s";
        $exportParams[] = $date_to;
    }

    /* MEMBERSHIP PLAN */
    if ($plan !== '') {
        $exportSql .= " AND m.plan_id = ? ";
        $exportTypes .= "i";
        $exportParams[] = $plan;
    }

    /* STATUS */
    if ($status !== '') {
        $exportSql .= " AND m.status = ? ";
        $exportTypes .= "s";
        $exportParams[] = $status;
    }

    $exportSql .= " ORDER BY m.start_date DESC ";

    $exportStmt = $con->prepare($exportSql);

    if (!$exportStmt) {
        die("SQL Error: " . $con->error);
    }

    if ($exportTypes !== "") {
        $exportStmt->bind_param(
            $exportTypes,
            ...$exportParams
        );
    }

    $exportStmt->execute();

    $exportResult = $exportStmt->get_result();

    /* CSV FILE */
    header('Content-Type: text/csv; charset=utf-8');
    header(
        'Content-Disposition: attachment; filename="membership_report.csv"'
    );

    $output = fopen('php://output', 'w');

    /* CSV HEADER */
    fputcsv($output, [
        'Member',
        'Membership Plan',
        'Start Date',
        'End Date',
        'Price',
        'Status'
    ]);

    /* CSV DATA */
    while ($row = $exportResult->fetch_assoc()) {

        fputcsv($output, [
            $row['member_name'],
            $row['plan_name'],
            $row['start_date'],
            $row['end_date'],
            'PHP ' . number_format($row['price'], 2),
            $row['status']
        ]);
    }

    fclose($output);
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Membership Reports | Admin Panel</title>

    <!-- ADMIN CSS -->
    <link
        rel="stylesheet"
        href="assets/css/admin.css"
    >
    <link
href="https://fonts.googleapis.com/css2?family=Orbitron:wght@600;700&family=Poppins:wght@300;400;500;600;700&display=swap"
rel="stylesheet">

    <!-- FONT AWESOME -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

</head>


<body>

<div class="wrapper">

    <!-- SIDEBAR -->
    <?php include("includes/admin_sidebar.php"); ?>


    <div class="main">

        <div class="dashboard-content reports-page-content">

            <!-- ==========================================
                 PAGE HEADER
            =========================================== -->

            <div class="staff-form-header">

                <div>

                    <h2>
                        Membership Reports
                    </h2>

                    <p>
                        View and analyze gym membership records.
                    </p>

                </div>

            </div>


            <!-- ==========================================
                 REPORT FILTERS
            =========================================== -->

            <div class="report-filter-card">

                <div class="report-filter-header">

                    <div>

                        <h3>
                            <i class="fa-solid fa-filter"></i>
                            Report Filters
                        </h3>

                        <p>
                            Select the filters you want to use
                            for the membership report.
                        </p>

                    </div>

                </div>


                <form
                    method="GET"
                    action="reports_admin.php"
                    class="membership-report-form"
                >

                    <!-- DATE FROM -->

                    <div class="report-filter-group">

                        <label for="date_from">
                            Date From
                        </label>

                        <input
                            type="date"
                            id="date_from"
                            name="date_from"
                        >

                    </div>


                    <!-- DATE TO -->

                    <div class="report-filter-group">

                        <label for="date_to">
                            Date To
                        </label>

                        <input
                            type="date"
                            id="date_to"
                            name="date_to"
                        >

                    </div>


                    <!-- MEMBERSHIP PLAN -->

<div class="report-filter-group">

    <label for="plan">
        Membership Plan
    </label>

    <select
        id="plan"
        name="plan"
    >

        <option value="">
            All Plans
        </option>

        <?php foreach ($plans as $plan_row): ?>

            <?php
            // Skip "All Plans" if it exists as a database plan
            if (strcasecmp(trim($plan_row['plan_name']), 'All Plans') === 0) {
                continue;
            }
            ?>

            <option
                value="<?php echo htmlspecialchars($plan_row['plan_id']); ?>"
                <?php
                echo ($plan == $plan_row['plan_id'])
                    ? 'selected'
                    : '';
                ?>
            >
                <?php
                echo htmlspecialchars($plan_row['plan_name']);
                ?>
            </option>

        <?php endforeach; ?>

    </select>

</div>


                    <!-- STATUS -->

                    <div class="report-filter-group">

                        <label for="status">
                            Status
                        </label>

                        <select
    id="status"
    name="status"
>

    <option value="">
        All Status
    </option>

    <option
        value="Active"
        <?php echo ($status === 'Active') ? 'selected' : ''; ?>
    >
        Active
    </option>

    <option
        value="Expired"
        <?php echo ($status === 'Expired') ? 'selected' : ''; ?>
    >
        Expired
    </option>

    <option
        value="Cancelled"
        <?php echo ($status === 'Cancelled') ? 'selected' : ''; ?>
    >
        Cancelled
    </option>

</select>
                    </div>


                    <!-- GENERATE -->

                    <div class="report-filter-action">

                        <button
                            type="submit"
                            class="btn-primary"
                        >

                            <i class="fa-solid fa-chart-column"></i>

                            Generate Report

                        </button>

                    </div>

                </form>

            </div>


            <!-- ==========================================
                 SUMMARY CARDS
            =========================================== -->

            <div class="report-summary-grid">


                <!-- TOTAL MEMBERS -->

                <div class="report-summary-card">

                    <div class="report-summary-icon">

                        <i class="fa-solid fa-users"></i>

                    </div>

                    <div>

                        <span>
                            Total Memberships
                        </span>

                        <strong>
    <?php echo $total_memberships; ?>
</strong>

                    </div>

                </div>


                <!-- ACTIVE -->

                <div class="report-summary-card">

                    <div class="report-summary-icon">

                        <i class="fa-solid fa-user-check"></i>

                    </div>

                    <div>

                        <span>
                            Active Members
                        </span>

                        <strong>
    <?php echo $active_count; ?>
</strong>

                    </div>

                </div>


                <!-- EXPIRED -->

                <div class="report-summary-card">

                    <div class="report-summary-icon">

                        <i class="fa-solid fa-user-clock"></i>

                    </div>

                    <div>

                        <span>
                            Expired
                        </span>

                        <strong>
    <?php echo $expired_count; ?>
</strong>

                    </div>

                </div>


                <!-- INACTIVE -->

                <div class="report-summary-card">

                    <div class="report-summary-icon">

                        <i class="fa-solid fa-ban"></i>

                    </div>

                    <div>

                        <span>
                            Cancelled
                        </span>

                        <strong>
    <?php echo $cancelled_count; ?>
</strong>

                    </div>

                </div>

            </div>


            <!-- ==========================================
                 MEMBERSHIP RECORDS
            =========================================== -->

            <div class="report-table-card">

                <div class="report-table-header">

                    <div>

                        <h3>
                            Fit Function Gym - Membership Report
                        </h3>

                        <p>
                            Membership records based on the
                            selected filters.
                        </p>

                    </div>


                    <div class="report-actions">

                        <button
    type="button"
    class="report-print-btn"
    onclick="window.print()"
>
    <i class="fa-solid fa-print"></i>
    Print
</button>


                       <a
    href="?date_from=<?php echo urlencode($date_from); ?>&date_to=<?php echo urlencode($date_to); ?>&plan=<?php echo urlencode($plan); ?>&status=<?php echo urlencode($status); ?>&export=csv"
    class="report-export-btn"
>
    <i class="fa-solid fa-file-export"></i>
    Export
</a>

                    </div>

                </div>


                <!-- TABLE -->

                <div class="report-table-wrapper">

                    <table class="membership-report-table">

                        <thead>

                            <tr>

                                <th>
                                    Member
                                </th>

                                <th>
                                    Membership Plan
                                </th>

                                <th>
                                    Start Date
                                </th>

                                <th>
                                    End Date
                                </th>

                                <th>
                                    Price
                                </th>

                                <th>
                                    Status
                                </th>

                            </tr>

                        </thead>


                        <tbody>

<?php if (empty($memberships)): ?>

    <tr>

        <td colspan="6">

            <div class="report-empty-state">

                <i class="fa-solid fa-chart-column"></i>

                <h4>
                    No Membership Records
                </h4>

                <p>
                    No membership records were found
                    using the selected filters.
                </p>

            </div>

        </td>

    </tr>

<?php else: ?>

    <?php foreach ($memberships as $membership): ?>

        <?php

        $full_name =
            $membership['first_name'];

        if (!empty($membership['middlename'])) {

            $full_name .= ' '
                . $membership['middlename'];

        }

        $full_name .= ' '
            . $membership['last_name'];

        ?>

        <tr>

            <!-- MEMBER -->

            <td>

                <div class="report-member-name">

                    <strong>
                        <?php
                        echo htmlspecialchars(
                            $full_name
                        );
                        ?>
                    </strong>

                    <small>
                        Member #<?php
                        echo htmlspecialchars(
                            $membership['member_id']
                        );
                        ?>
                    </small>

                </div>

            </td>


            <!-- PLAN -->

            <td>

                <?php
                echo htmlspecialchars(
                    $membership['plan_name']
                );
                ?>

            </td>


            <!-- START DATE -->

            <td>

                <?php
                echo date(
                    'M d, Y',
                    strtotime(
                        $membership['start_date']
                    )
                );
                ?>

            </td>


            <!-- END DATE -->

            <td>

                <?php
                echo date(
                    'M d, Y',
                    strtotime(
                        $membership['end_date']
                    )
                );
                ?>

            </td>


            <!-- PRICE -->

            <td>

                ₱<?php
                echo number_format(
                    $membership['price'],
                    2
                );
                ?>

            </td>


            <!-- STATUS -->

            <td>

                <span
                    class="report-status-badge
                    <?php
                    echo strtolower(
                        $membership['status']
                    );
                    ?>"
                >

                    <?php
                    echo htmlspecialchars(
                        $membership['status']
                    );
                    ?>

                </span>

            </td>

        </tr>

    <?php endforeach; ?>

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