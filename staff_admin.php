<?php

require_once("includes/admin_auth.php");


/* ===========================
   SEARCH
=========================== */

$search = "";

if(isset($_GET['search'])){
    $search = trim($_GET['search']);
}


/* ===========================
   STATUS FILTER
=========================== */

$status = "";

if(isset($_GET['status'])){
    $status = trim($_GET['status']);
}


/* ===========================
   BUILD QUERY
=========================== */

$sql = "
    SELECT
        user_id,
        full_name,
        role,
        status,
        created_at
    FROM users
    WHERE role = 'Staff'
";

$params = [];
$types = "";


/* ===========================
   SEARCH FILTER
=========================== */

if($search !== ""){

    $sql .= "
        AND (
            full_name LIKE ?
        )
    ";

    $searchValue = "%" . $search . "%";

$params[] = $searchValue;

$types .= "s";
}


/* ===========================
   STATUS FILTER
=========================== */

if($status !== ""){

    $sql .= " AND status = ?";

    $params[] = $status;

    $types .= "s";
}


/* ===========================
   ORDER
=========================== */

$sql .= " ORDER BY created_at DESC";


/* ===========================
   PAGINATION
=========================== */

$staffPerPage = 10;

$page = isset($_GET['page'])
    ? (int)$_GET['page']
    : 1;

if($page < 1){
    $page = 1;
}

$offset = ($page - 1) * $staffPerPage;


/* ===========================
   COUNT FILTERED STAFF
=========================== */

$countSql = "
    SELECT COUNT(*) AS total
    FROM users
    WHERE role = 'Staff'
";

$countParams = [];
$countTypes = "";


if($search !== ""){

    $countSql .= "
        AND (
            full_name LIKE ?
        )
    ";

    $countValue = "%" . $search . "%";

$countParams[] = $countValue;

$countTypes .= "s";
}


if($status !== ""){

    $countSql .= " AND status = ?";

    $countParams[] = $status;

    $countTypes .= "s";
}


$countStmt = $con->prepare($countSql);

if($countStmt === false){

    die("SQL Error: " . $con->error);

}


if(!empty($countParams)){

    $countStmt->bind_param(
        $countTypes,
        ...$countParams
    );

}


$countStmt->execute();

$countResult = $countStmt->get_result();

$totalFilteredStaff =
    mysqli_fetch_assoc($countResult)['total'];

$totalPages = ceil(
    $totalFilteredStaff / $staffPerPage
);


/* ===========================
   LIMIT
=========================== */

$sql .= " LIMIT ?, ?";

$params[] = $offset;
$params[] = $staffPerPage;

$types .= "ii";


/* ===========================
   GET STAFF
=========================== */

$stmt = $con->prepare($sql);

if($stmt === false){

    die("SQL Error: " . $con->error);

}


if(!empty($params)){

    $stmt->bind_param(
        $types,
        ...$params
    );

}


$stmt->execute();

$result = $stmt->get_result();


/* ===========================
   TOTAL STAFF
=========================== */

$totalStaffQuery = mysqli_query($con, "
    SELECT COUNT(*) AS total
    FROM users
    WHERE role = 'Staff'
");

$totalStaff =
    mysqli_fetch_assoc($totalStaffQuery)['total'];

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Staff | Admin Panel</title>

<link
href="https://fonts.googleapis.com/css2?family=Orbitron:wght@600;700&family=Poppins:wght@300;400;500;600;700&display=swap"
rel="stylesheet">

<link
rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

<link
rel="stylesheet"
href="assets/css/admin.css">

</head>

<body>

<div class="wrapper">

<?php include("includes/admin_sidebar.php"); ?>

<div class="main">

<div class="staff-content">

<!-- PAGE HEADER -->

<div class="staff-header">

    <div class="staff-title">

        <h2>
            Staff
        </h2>

        <p>
            Manage gym staff accounts.
        </p>

    </div>


    <div class="staff-header-right">

        <div class="staff-total">

            <i class="fa-solid fa-user-tie"></i>

            <span>
                <?php echo $totalStaff; ?>
            </span>

            Staff

        </div>


        <a
            href="staff_add_admin.php"
            class="add-staff-btn"
        >

            <i class="fa-solid fa-user-plus"></i>

            Add Staff

        </a>

    </div>

</div>

<!-- SUCCESS MESSAGE -->

<?php if(isset($_GET['updated'])): ?>

    <div class="staff-success">

        <i class="fa-solid fa-circle-check"></i>

        Staff information was successfully updated.

    </div>

<?php endif; ?>


<?php if(isset($_GET['status_updated'])): ?>

    <div class="staff-success">

        <i class="fa-solid fa-circle-check"></i>

        Staff account status was successfully updated.

    </div>

<?php endif; ?>


<!-- SEARCH AND FILTER -->

<div class="staff-tools">

    <form method="GET" class="staff-search">


        <div class="search-box">

            <i class="fa-solid fa-magnifying-glass"></i>

            <input
                type="text"
                name="search"
                placeholder="Search staff..."
                value="<?php echo htmlspecialchars($search); ?>"
            >

        </div>


        <select name="status">

            <option value="">
                All Status
            </option>

            <option
                value="Active"
                <?php echo ($status === "Active") ? "selected" : ""; ?>
            >
                Active
            </option>

            <option
                value="Inactive"
                <?php echo ($status === "Inactive") ? "selected" : ""; ?>
            >
                Inactive
            </option>

        </select>


        <button
            type="submit"
            class="staff-filter-btn"
        >

            <i class="fa-solid fa-filter"></i>

            Filter

        </button>


        <?php if($search !== "" || $status !== ""): ?>

            <a
                href="staff_admin.php"
                class="clear-filter"
            >
                Clear
            </a>

        <?php endif; ?>


    </form>

</div>


<!-- STAFF TABLE -->

<div class="staff-table-card">

    <div class="table-wrapper">

        <table class="staff-table">


            <thead>

                <tr>

                    <th>ID</th>

                    <th>Staff</th>

                    <th>Role</th>

                    <th>Status</th>

                    <th>Registered</th>

                    <th>Action</th>

                </tr>

            </thead>


            <tbody>


            <?php if($result->num_rows > 0): ?>


                <?php while($staff = $result->fetch_assoc()): ?>


                    <tr>


                        <!-- ID -->

                        <td>

                            #<?php echo $staff['user_id']; ?>

                        </td>


                        <!-- STAFF -->

                        <td>

                            <div class="staff-name">


                                <div class="staff-avatar">

                                    <?php

                                    echo strtoupper(
                                        substr(
                                            $staff['full_name'],
                                            0,
                                            1
                                        )
                                    );

                                    ?>

                                </div>


                                <div>

                                    <strong>

                                        <?php

                                        echo htmlspecialchars(
                                            $staff['full_name']
                                        );

                                        ?>

                                    </strong>


                                    <small>
                                        Staff
                                    </small>

                                </div>

                            </div>

                        </td>

                        <!-- ROLE -->

                        <td>

                            <span class="staff-role">

                                <?php

                                echo htmlspecialchars(
                                    $staff['role']
                                );

                                ?>

                            </span>

                        </td>


                        <!-- STATUS -->

                        <td>

                            <?php

                            $statusClass = strtolower(
                                $staff['status']
                            );

                            ?>

                            <span
                                class="staff-status <?php echo $statusClass; ?>"
                            >

                                <?php

                                echo htmlspecialchars(
                                    $staff['status']
                                );

                                ?>

                            </span>

                        </td>


                        <!-- REGISTERED -->

                        <td>

                            <?php

                            echo date(
                                "M d, Y",
                                strtotime(
                                    $staff['created_at']
                                )
                            );

                            ?>

                        </td>


                        <!-- ACTION -->

                        <td>

                            <div class="staff-actions">


                                <a
                                    href="staff_view_admin.php?id=<?php echo $staff['user_id']; ?>"
                                    class="staff-view-btn"
                                    title="View Staff"
                                >

                                    <i class="fa-solid fa-eye"></i>

                                </a>


                                <a
                                    href="staff_edit_admin.php?id=<?php echo $staff['user_id']; ?>"
                                    class="staff-edit-btn"
                                    title="Edit Staff"
                                >

                                    <i class="fa-solid fa-pen"></i>

                                </a>


                            </div>

                        </td>


                    </tr>


                <?php endwhile; ?>


            <?php else: ?>


                <tr>

                    <td
                        colspan="7"
                        class="no-staff"
                    >

                        <i class="fa-solid fa-user-slash"></i>

                        <strong>
                            No staff found
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


<!-- PAGINATION -->

<?php if($totalPages > 1): ?>

    <div class="staff-pagination">


        <?php if($page > 1): ?>

            <a
                href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status); ?>"
            >

                <i class="fa-solid fa-chevron-left"></i>

                Previous

            </a>

        <?php endif; ?>


        <?php for(
            $i = 1;
            $i <= $totalPages;
            $i++
        ): ?>

            <a
                href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status); ?>"
                class="<?php echo ($i == $page) ? 'active' : ''; ?>"
            >

                <?php echo $i; ?>

            </a>

        <?php endfor; ?>


        <?php if($page < $totalPages): ?>

            <a
                href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status); ?>"
            >

                Next

                <i class="fa-solid fa-chevron-right"></i>

            </a>

        <?php endif; ?>


    </div>

<?php endif; ?>

</div>

</div>

</div>

</body>

</html>