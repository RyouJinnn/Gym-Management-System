<?php

require_once("includes/admin_auth.php");
// ==============================
// SUCCESS MESSAGES
// ==============================

$added = isset($_GET['added']) && $_GET['added'] === '1';

$deleted = isset($_GET['deleted']) && $_GET['deleted'] === '1';


// ==============================
// SEARCH & FILTER
// ==============================

$search = trim($_GET['search'] ?? '');

$status_filter = $_GET['status'] ?? '';


$where = [];
$params = [];
$types = "";


if ($search !== "") {

    $where[] = "
        (
            plan_name LIKE ?
            OR description LIKE ?
        )
    ";

    $search_param = "%" . $search . "%";

    $params[] = $search_param;
    $params[] = $search_param;

    $types .= "ss";
}


if (
    $status_filter === "Active" ||
    $status_filter === "Inactive"
) {

    $where[] = "status = ?";

    $params[] = $status_filter;

    $types .= "s";
}


$where_sql = "";

if (!empty($where)) {

    $where_sql = "WHERE " . implode(" AND ", $where);

}


// ==============================
// GET MEMBERSHIP PLANS
// ==============================

$sql = "
    SELECT
        plan_id,
        plan_name,
        duration_days,
        price,
        description,
        status
    FROM membership_plans
    $where_sql
    ORDER BY plan_id DESC
";


$stmt = $con->prepare($sql);


if ($stmt === false) {

    die("SQL Error: " . $con->error);

}


if (!empty($params)) {

    $stmt->bind_param(
        $types,
        ...$params
    );

}


$stmt->execute();


$result = $stmt->get_result();


// ==============================
// TOTAL PLANS
// ==============================

$count_sql = "
    SELECT COUNT(*) AS total
    FROM membership_plans
";


$count_result = $con->query($count_sql);


$total_plans = 0;


if ($count_result) {

    $count_row = $count_result->fetch_assoc();

    $total_plans = (int)$count_row['total'];

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

<title>Membership Plans | Admin Panel</title>


<link
rel="stylesheet"
href="assets/css/admin.css">
>

<link
href="https://fonts.googleapis.com/css2?family=Orbitron:wght@600;700&family=Poppins:wght@300;400;500;600;700&display=swap"
rel="stylesheet">

<link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
>


</head>


<body>


<div class="wrapper">


<!-- =========================
     SIDEBAR
========================= -->

<?php include("includes/admin_sidebar.php"); ?>


<!-- =========================
     MAIN
========================= -->

<div class="main">

<div class="dashboard-content membership-plans-content">


    <!-- ==============================
         SUCCESS MESSAGES
    ============================== -->

    <?php if ($added): ?>

        <div class="success-message">

            <i class="fa-solid fa-circle-check"></i>

            <span>
                Membership plan added successfully.
            </span>

        </div>

    <?php endif; ?>


    <?php if ($deleted): ?>

        <div class="success-message">

            <i class="fa-solid fa-circle-check"></i>

            <span>
                Membership plan deleted successfully.
            </span>

        </div>

    <?php endif; ?>


    <!-- PAGE HEADER -->

    <div class="page-header">


        <div>

            <h2>
                Membership Plans
            </h2>

            <p>
                Manage gym membership plans and pricing.
            </p>

        </div>


        <div class="page-header-actions">


            <div class="total-members-box">

                <i class="fa-solid fa-layer-group"></i>

                <strong>
                    <?php echo $total_plans; ?>
                </strong>

                Plans

            </div>


            <a
                href="membership_plan_add_admin.php"
                class="btn-primary"
            >

                <i class="fa-solid fa-plus"></i>

                Add Plan

            </a>


        </div>


    </div>


    <!-- =========================
         SEARCH / FILTER
    ========================= -->

    <form
        method="GET"
        class="member-filter-box"
    >


        <div class="search-box">


            <i class="fa-solid fa-magnifying-glass"></i>


            <input
                type="text"
                name="search"
                placeholder="Search membership plan..."
                value="<?php echo htmlspecialchars($search); ?>"
            >


        </div>


        <select name="status">

            <option value="">
                All Status
            </option>

            <option
                value="Active"
                <?php
                echo $status_filter === "Active"
                    ? "selected"
                    : "";
                ?>
            >
                Active
            </option>

            <option
                value="Inactive"
                <?php
                echo $status_filter === "Inactive"
                    ? "selected"
                    : "";
                ?>
            >
                Inactive
            </option>

        </select>


        <button
            type="submit"
            class="filter-btn"
        >

            <i class="fa-solid fa-filter"></i>

            Filter

        </button>


    </form>


    <!-- =========================
         PLANS TABLE
    ========================= -->

    <div class="members-table-container">


        <table class="members-table">


            <thead>

                <tr>

                    <th>ID</th>

                    <th>Plan</th>

                    <th>Duration</th>

                    <th>Price</th>

                    <th>Description</th>

                    <th>Status</th>

                    <th>Action</th>

                </tr>

            </thead>


            <tbody>


            <?php if ($result->num_rows > 0): ?>


                <?php while ($plan = $result->fetch_assoc()): ?>


                    <tr>


                        <!-- ID -->

                        <td>

                            #<?php
                            echo (int)$plan['plan_id'];
                            ?>

                        </td>


                        <!-- PLAN -->

                        <td>

                            <div class="member-name-cell">


                                <div class="member-avatar">

                                    <i class="fa-solid fa-dumbbell"></i>

                                </div>


                                <div>

                                    <strong>

                                        <?php
                                        echo htmlspecialchars(
                                            $plan['plan_name']
                                        );
                                        ?>

                                    </strong>


                                    <small>
                                        Membership Plan
                                    </small>


                                </div>


                            </div>

                        </td>


                        <!-- DURATION -->

                        <td>

                            <?php
                            echo (int)$plan['duration_days'];
                            ?>

                            days

                        </td>


                        <!-- PRICE -->

                        <td>

                            <strong class="plan-price">

                                ₱<?php
                                echo number_format(
                                    (float)$plan['price'],
                                    2
                                );
                                ?>

                            </strong>

                        </td>


                        <!-- DESCRIPTION -->

                        <td>

                            <?php

                            if (
                                !empty($plan['description'])
                            ) {

                                echo htmlspecialchars(
                                    $plan['description']
                                );

                            } else {

                                echo "No description";

                            }

                            ?>

                        </td>


                        <!-- STATUS -->

                        <td>


                            <?php if ($plan['status'] === "Active"): ?>

                                <span class="status-badge active">

                                    Active

                                </span>

                            <?php else: ?>

                                <span class="status-badge inactive">

                                    Inactive

                                </span>

                            <?php endif; ?>


                        </td>


                        <!-- ACTION -->

                        <td>


                            <div class="action-buttons">


                                <a
                                    href="membership_plan_view_admin.php?id=<?php echo (int)$plan['plan_id']; ?>"
                                    class="action-btn view"
                                    title="View Plan"
                                >

                                    <i class="fa-solid fa-eye"></i>

                                </a>


                                <a
                                    href="membership_plan_edit_admin.php?id=<?php echo (int)$plan['plan_id']; ?>"
                                    class="action-btn edit"
                                    title="Edit Plan"
                                >

                                    <i class="fa-solid fa-pen"></i>

                                </a>


                                <button
    type="button"
    class="action-btn delete"
    onclick="openDeletePlanModal(
        <?php echo $plan['plan_id']; ?>,
        '<?php echo htmlspecialchars($plan['plan_name'], ENT_QUOTES); ?>'
    )"
>
    <i class="fa-solid fa-trash"></i>
</button>


                            </div>


                        </td>


                    </tr>


                <?php endwhile; ?>


            <?php else: ?>


                <tr>

                    <td
                        colspan="7"
                        class="no-results"
                    >

                        <i class="fa-solid fa-layer-group"></i>

                        <strong>
                            No membership plans found
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


<!-- ==========================================
     DELETE MEMBERSHIP PLAN POPUP
========================================== -->

<div
    id="deletePlanModal"
    class="delete-plan-modal"
>

    <div class="delete-plan-modal-box">


        <div class="delete-plan-icon">

            <i class="fa-solid fa-trash"></i>

        </div>


        <h2>
            Delete Membership Plan?
        </h2>


        <p>

            Are you sure you want to delete

            <strong id="deletePlanName">
                this membership plan
            </strong>?

            <br>

            This action cannot be undone.

        </p>


        <div class="delete-plan-modal-actions">


            <!-- NO BUTTON -->

            <button
                type="button"
                class="delete-plan-cancel"
                onclick="closeDeletePlanModal()"
            >

                No

            </button>


            <!-- YES DELETE -->

            <form
                method="POST"
                action="membership_plan_delete_admin.php"
            >

                <input
                    type="hidden"
                    name="plan_id"
                    id="deletePlanId"
                >


                <button
                    type="submit"
                    class="delete-plan-confirm"
                >

                    Yes, Delete

                </button>

            </form>


        </div>


    </div>

</div>


<!-- ==========================================
     DELETE POPUP JAVASCRIPT
========================================== -->

<script>

function openDeletePlanModal(planId, planName) {

    document.getElementById("deletePlanId").value = planId;

    document.getElementById("deletePlanName").textContent = planName;

    document
        .getElementById("deletePlanModal")
        .classList.add("show");

}


function closeDeletePlanModal() {

    document
        .getElementById("deletePlanModal")
        .classList.remove("show");

}


// Close popup when clicking outside

document
    .getElementById("deletePlanModal")
    .addEventListener("click", function(event) {

        if (event.target === this) {

            closeDeletePlanModal();

        }

    });

</script>


</div>


</body>

</html>


<?php

$stmt->close();

?>