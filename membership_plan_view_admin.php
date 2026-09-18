<?php

require_once("includes/admin_auth.php");


// ==============================
// GET PLAN ID
// ==============================

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {

    header("Location: membership_plans_admin.php");
    exit;

}

$plan_id = (int) $_GET['id'];
$updated = isset($_GET['updated']) && $_GET['updated'] === '1';
$created = isset($_GET['created']) && $_GET['created'] === '1';


// ==============================
// GET MEMBERSHIP PLAN
// ==============================

$stmt = $con->prepare("
    SELECT
        plan_id,
        plan_name,
        duration_days,
        price,
        description,
        status
    FROM membership_plans
    WHERE plan_id = ?
    LIMIT 1
");

if ($stmt === false) {

    die("SQL Error: " . $con->error);

}

$stmt->bind_param("i", $plan_id);

$stmt->execute();

$result = $stmt->get_result();

$plan = $result->fetch_assoc();

$stmt->close();


// ==============================
// PLAN NOT FOUND
// ==============================

if (!$plan) {

    header("Location: membership_plans_admin.php");
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

    <title>
        <?php echo htmlspecialchars($plan['plan_name']); ?>
        | Admin Panel
    </title>

  <link
rel="stylesheet"
href="assets/css/admin.css">

<link
        href="https://fonts.googleapis.com/css2?family=Orbitron:wght@600;700&family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

</head>


<body>


<div class="wrapper">


    <!-- SIDEBAR -->

    <?php include("includes/admin_sidebar.php"); ?>


    <!-- MAIN -->

    <div class="main">

        <div class="dashboard-content membership-plan-view-content">


            <!-- =========================
                 PAGE HEADER
            ========================= -->

            <div class="membership-view-header">

                <div>

                    <h2>
                        Membership Plan
                    </h2>

                    <p>
                        View membership plan information.
                    </p>

                </div>


                <a
                    href="membership_plans_admin.php"
                    class="back-staff-btn"
                >

                    <i class="fa-solid fa-arrow-left"></i>

                    Back to Plans

                </a>

            </div>


            <!-- =========================
                 PLAN PROFILE
            ========================= -->

            <div class="membership-profile-card">


                <!-- PROFILE HEADER -->

                <div class="membership-profile-header">


                    <div class="membership-plan-icon">

                        <i class="fa-solid fa-dumbbell"></i>

                    </div>


                    <div class="membership-profile-title">

                        <h1>
                            <?php
                            echo htmlspecialchars(
                                $plan['plan_name']
                            );
                            ?>
                        </h1>

                        <p>
                            Plan #<?php
                            echo $plan['plan_id'];
                            ?>
                        </p>


                        <?php if ($plan['status'] === 'Active'): ?>

                            <span class="membership-status active">
                                Active
                            </span>

                        <?php else: ?>

                            <span class="membership-status inactive">
                                Inactive
                            </span>

                        <?php endif; ?>


                    </div>


                </div>

<?php if ($created): ?>

    <div class="success-message">

        <i class="fa-solid fa-circle-check"></i>

        <span>
            Membership plan added successfully.
        </span>

    </div>

<?php endif; ?>


<?php if ($updated): ?>

    <div class="success-message">

        <i class="fa-solid fa-circle-check"></i>

        <span>
            Membership plan updated successfully.
        </span>

    </div>

<?php endif; ?>

                <!-- =========================
                     PLAN INFORMATION
                ========================= -->

                <div class="membership-info-section">


                    <h3>

                        Plan Information

                    </h3>


                    <div class="membership-info-grid">


                        <!-- PLAN ID -->

                        <div class="membership-info-box">

                            <span>
                                Plan ID
                            </span>

                            <strong>
                                #<?php
                                echo $plan['plan_id'];
                                ?>
                            </strong>

                        </div>


                        <!-- PLAN NAME -->

                        <div class="membership-info-box">

                            <span>
                                Plan Name
                            </span>

                            <strong>
                                <?php
                                echo htmlspecialchars(
                                    $plan['plan_name']
                                );
                                ?>
                            </strong>

                        </div>


                        <!-- DURATION -->

                        <div class="membership-info-box">

                            <span>
                                Duration
                            </span>

                            <strong>
                                <?php
                                echo $plan['duration_days'];
                                ?>
                                days
                            </strong>

                        </div>


                        <!-- PRICE -->

                        <div class="membership-info-box">

                            <span>
                                Price
                            </span>

                            <strong class="membership-price">

                                ₱<?php
                                echo number_format(
                                    $plan['price'],
                                    2
                                );
                                ?>

                            </strong>

                        </div>


                        <!-- STATUS -->

                        <div class="membership-info-box">

                            <span>
                                Status
                            </span>

                            <strong>

                                <?php
                                echo htmlspecialchars(
                                    $plan['status']
                                );
                                ?>

                            </strong>

                        </div>


                    </div>


                </div>


                <!-- =========================
                     DESCRIPTION
                ========================= -->

                <div class="membership-description-section">


                    <h3>

                        Description

                    </h3>


                    <div class="membership-description-box">

                        <?php

                        if (
                            !empty(
                                trim(
                                    $plan['description'] ?? ''
                                )
                            )
                        ) {

                            echo nl2br(
                                htmlspecialchars(
                                    $plan['description']
                                )
                            );

                        } else {

                            echo "No description provided.";

                        }

                        ?>

                    </div>


                </div>


                <!-- =========================
                     ACTIONS
                ========================= -->

                <div class="membership-profile-actions">


                    <a
                        href="membership_plan_edit_admin.php?id=<?php echo $plan['plan_id']; ?>"
                        class="membership-edit-btn"
                    >

                        <i class="fa-solid fa-pen"></i>

                        Edit Plan

                    </a>


                    <button
                        type="button"
                        class="membership-delete-btn"
                        onclick="openDeletePlanModal()"
                    >

                        <i class="fa-solid fa-trash"></i>

                        Delete Plan

                    </button>


                </div>


            </div>


        </div>


    </div>


</div>


<!-- ==============================
     DELETE MODAL
============================== -->

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

            <strong>
                <?php
                echo htmlspecialchars(
                    $plan['plan_name']
                );
                ?>
            </strong>?

            <br>

            This action cannot be undone.

        </p>


        <div class="delete-plan-modal-actions">


            <button
                type="button"
                class="delete-plan-cancel"
                onclick="closeDeletePlanModal()"
            >

                No

            </button>


            <form
                method="POST"
                action="membership_plan_delete_admin.php"
            >

                <input
                    type="hidden"
                    name="plan_id"
                    value="<?php
                    echo $plan['plan_id'];
                    ?>"
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


<script>

function openDeletePlanModal() {

    document
        .getElementById("deletePlanModal")
        .classList.add("show");

}


function closeDeletePlanModal() {

    document
        .getElementById("deletePlanModal")
        .classList.remove("show");

}


document
    .getElementById("deletePlanModal")
    .addEventListener("click", function(event) {

        if (event.target === this) {

            closeDeletePlanModal();

        }

    });

</script>


</body>

</html>