<?php

require_once("includes/staff_auth.php");

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
        id,
        first_name,
        middlename,
        last_name,
        suffix,
        email,
        contact_number,
        gender,
        birthdate,
        status,
        profile_picture,
        created_at
    FROM signup
    WHERE 1=1
";

$params = [];
$types = "";


/* ===========================
   SEARCH FILTER
=========================== */

if($search !== ""){

    $sql .= "
        AND (
            first_name LIKE ?
            OR middlename LIKE ?
            OR last_name LIKE ?
            OR email LIKE ?
            OR contact_number LIKE ?
        )
    ";

    $searchValue = "%" . $search . "%";

    $params[] = $searchValue;
    $params[] = $searchValue;
    $params[] = $searchValue;
    $params[] = $searchValue;
    $params[] = $searchValue;

    $types .= "sssss";
}


/* ===========================
   STATUS FILTER
=========================== */

if($status !== ""){

    $sql .= " AND status = ?";

    $params[] = $status;

    $types .= "s";
}

$sql .= " ORDER BY created_at DESC";


/* ===========================
   PAGINATION
=========================== */

$membersPerPage = 10;

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

if($page < 1){
    $page = 1;
}

$offset = ($page - 1) * $membersPerPage;


/* ===========================
   TOTAL FILTERED MEMBERS
=========================== */

$countSql = "
    SELECT COUNT(*) AS total
    FROM signup
    WHERE 1=1
";

$countParams = [];
$countTypes = "";

if($search !== ""){

    $countSql .= "
        AND (
            first_name LIKE ?
            OR middlename LIKE ?
            OR last_name LIKE ?
            OR email LIKE ?
            OR contact_number LIKE ?
        )
    ";

    $countParams = array_fill(
        0,
        5,
        "%" . $search . "%"
    );

    $countTypes = "sssss";
}

if($status !== ""){

    $countSql .= " AND status = ?";

    $countParams[] = $status;

    $countTypes .= "s";
}

$countStmt = $con->prepare($countSql);

if(!empty($countParams)){

    $countStmt->bind_param(
        $countTypes,
        ...$countParams
    );

}

$countStmt->execute();

$countResult = $countStmt->get_result();

$totalFilteredMembers =
    mysqli_fetch_assoc($countResult)['total'];

$totalPages = ceil(
    $totalFilteredMembers / $membersPerPage
);


/* ===========================
   LIMIT RESULTS
=========================== */

$sql .= " LIMIT ?, ?";

$params[] = $offset;
$params[] = $membersPerPage;

$types .= "ii";


/* ===========================
   PREPARE QUERY
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
   TOTAL MEMBERS
=========================== */

$countQuery = mysqli_query($con, "
    SELECT COUNT(*) AS total
    FROM signup
");

$totalMembers = mysqli_fetch_assoc($countQuery)['total'];

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Members | Staff Panel</title>

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

<?php include("includes/staff_sidebar.php"); ?>


<div class="main">

<div class="members-content">


    <!-- PAGE HEADER -->

    <div class="members-header">

        <div>

            <h2>
                Members
            </h2>

            <p>
                View and manage registered gym members.
            </p>

        </div>

       <div class="member-header-actions">


    <div class="member-total">

        <i class="fa-solid fa-users"></i>

        <span>
            <?php echo $totalMembers; ?>
        </span>

        Members

    </div>

    <a
        href="member_add_staff.php"
        class="btn-primary"
    >

        <i class="fa-solid fa-user-plus"></i>

        Add Member

    </a>

</div>

    </div>


   <!-- SUCCESS MESSAGE -->

<?php if(isset($_GET['added'])): ?>

    <div class="member-success">

        <i class="fa-solid fa-circle-check"></i>

        Member was successfully added.

    </div>

<?php endif; ?>


<?php if(isset($_GET['updated'])): ?>

    <div class="member-success">

        <i class="fa-solid fa-circle-check"></i>

        Member information was successfully updated.

    </div>

<?php endif; ?>


<?php if(isset($_GET['status_updated'])): ?>

    <div class="member-success">

        <i class="fa-solid fa-circle-check"></i>

        Member account status was successfully updated.

    </div>

<?php endif; ?>

    <!-- SEARCH AND FILTER -->

    <div class="members-tools">

        <form method="GET" class="member-search">

    <div class="search-box">

                <i class="fa-solid fa-magnifying-glass"></i>

                <input
                    type="text"
                    name="search"
                    placeholder="Search member..."
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
                    value="Pending"
                    <?php echo ($status === "Pending") ? "selected" : ""; ?>
                >
                    Pending
                </option>

                <option
                    value="Inactive"
                    <?php echo ($status === "Inactive") ? "selected" : ""; ?>
                >
                    Inactive
                </option>

            </select>


            <button type="submit" class="member-filter-btn">

                <i class="fa-solid fa-filter"></i>

                Filter

            </button>


            <?php if($search !== "" || $status !== ""): ?>

                <a
                    href="members_staff.php"
                    class="clear-filter"
                >

                    Clear

                </a>

            <?php endif; ?>

        </form>

    </div>


    <!-- MEMBERS TABLE -->

    <div class="members-table-card">

        <div class="table-wrapper">

            <table class="members-table">

                <thead>

                    <tr>

                        <th>ID</th>

                        <th>Member</th>

                        <th>Email</th>

                        <th>Contact Number</th>

                        <th>Gender</th>

                        <th>Status</th>

                        <th>Registered</th>

                        <th>Action</th>

                    </tr>

                </thead>


                <tbody>

                <?php if($result->num_rows > 0): ?>

                    <?php while($member = $result->fetch_assoc()): ?>

                        <tr>

                            <!-- ID -->

                            <td>

                                #<?php echo $member['id']; ?>

                            </td>


                           <!-- NAME -->
<td>

    <div class="member-name">

        <?php if(!empty($member['profile_picture'])): ?>

            <img
                src="<?php echo htmlspecialchars($member['profile_picture']); ?>"
                alt="Profile Picture"
                class="member-avatar"
            >

        <?php else: ?>

            <div class="member-avatar">
                <?php
                echo strtoupper(
                    substr(
                        $member['first_name'],
                        0,
                        1
                    )
                );
                ?>
            </div>

        <?php endif; ?>

        <div>

            <strong>
                <?php
                echo htmlspecialchars(
                    $member['first_name']
                    . " "
                    . $member['last_name']
                );
                ?>
            </strong>

            <small>
                Member
            </small>

        </div>

    </div>

</td>


                            <!-- EMAIL -->

                            <td>

                                <?php echo htmlspecialchars($member['email']); ?>

                            </td>


                            <!-- CONTACT -->

                            <td>

                                <?php echo htmlspecialchars($member['contact_number']); ?>

                            </td>


                            <!-- GENDER -->

                            <td>

                                <?php echo htmlspecialchars($member['gender']); ?>

                            </td>


                            <!-- STATUS -->

                            <td>

                                <?php

                                $statusClass = strtolower(
                                    str_replace(
                                        " ",
                                        "-",
                                        $member['status']
                                    )
                                );

                                ?>

                                <span
                                    class="member-status <?php echo $statusClass; ?>"
                                >

                                    <?php echo htmlspecialchars($member['status']); ?>

                                </span>

                            </td>


                            <!-- REGISTERED -->

                            <td>

                                <?php

                                echo date(
                                    "M d, Y",
                                    strtotime($member['created_at'])
                                );

                                ?>

                            </td>


                            <!-- ACTION -->

<td>

    <div class="member-actions">

        <!-- VIEW -->
        <a
            href="member_view_staff.php?id=<?php echo (int)$member['id']; ?>"
            class="member-view-btn"
            title="View Member"
        >

            <i class="fa-solid fa-eye"></i>

        </a>


        <!-- EDIT -->
        <a
            href="member_edit_staff.php?id=<?php echo (int)$member['id']; ?>"
            class="member-edit-small-btn"
            title="Edit Member"
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
                            colspan="8"
                            class="no-members"
                        >

                            <i class="fa-solid fa-users-slash"></i>

                            <strong>
                                No members found
                            </strong>

                            <span>
                                Try changing your search or filter.
                            </span>

                        </td>

                    </tr>

                <?php endif; ?>

            </tbody>

            </table>

        </div> <!-- table-wrapper -->

    </div> <!-- members-table-card -->


    <?php if($totalPages > 1): ?>

        <div class="members-pagination">

            <?php if($page > 1): ?>

                <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status); ?>">
                    <i class="fa-solid fa-chevron-left"></i>
                    Previous
                </a>

            <?php endif; ?>


            <?php for($i = 1; $i <= $totalPages; $i++): ?>

                <a
                    href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status); ?>"
                    class="<?php echo ($i == $page) ? 'active' : ''; ?>"
                >
                    <?php echo $i; ?>
                </a>

            <?php endfor; ?>


            <?php if($page < $totalPages): ?>

                <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status); ?>">
                    Next
                    <i class="fa-solid fa-chevron-right"></i>
                </a>

            <?php endif; ?>

        </div>

    <?php endif; ?>


</div> <!-- members-content -->

</div> <!-- main -->

</div> <!-- wrapper -->

</body>
</html>