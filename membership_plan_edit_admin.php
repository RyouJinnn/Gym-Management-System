<?php

require_once("includes/admin_auth.php");
// ==============================
// GET PLAN ID
// ==============================

if (
    !isset($_GET['id']) ||
    !is_numeric($_GET['id'])
) {

    header("Location: membership_plans_admin.php");
    exit;

}

$plan_id = (int) $_GET['id'];


// ==============================
// GET EXISTING PLAN
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


$stmt->bind_param(
    "i",
    $plan_id
);


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


// ==============================
// HANDLE UPDATE
// ==============================

$errors = [];


if ($_SERVER["REQUEST_METHOD"] === "POST") {


    $plan_name = trim(
        $_POST['plan_name'] ?? ''
    );


    $duration_days = (int)(
        $_POST['duration_days'] ?? 0
    );


    $price = (float)(
        $_POST['price'] ?? 0
    );


    $description = trim(
        $_POST['description'] ?? ''
    );


    $status = $_POST['status'] ?? '';


    // ==============================
    // VALIDATION
    // ==============================

    if ($plan_name === '') {

        $errors[] =
            "Plan name is required.";

    }


    if ($duration_days <= 0) {

        $errors[] =
            "Duration must be greater than 0 days.";

    }


    if ($price < 0) {

        $errors[] =
            "Price cannot be negative.";

    }


    if (
        $status !== "Active" &&
        $status !== "Inactive"
    ) {

        $errors[] =
            "Invalid status.";

    }


    // ==============================
    // UPDATE DATABASE
    // ==============================

    if (empty($errors)) {


        $stmt = $con->prepare("
            UPDATE membership_plans

            SET
                plan_name = ?,
                duration_days = ?,
                price = ?,
                description = ?,
                status = ?

            WHERE plan_id = ?
        ");


        if ($stmt === false) {

            die(
                "SQL Error: "
                . $con->error
            );

        }


        $stmt->bind_param(
            "sidssi",
            $plan_name,
            $duration_days,
            $price,
            $description,
            $status,
            $plan_id
        );


        if ($stmt->execute()) {


            $stmt->close();


            header(
                "Location: membership_plan_view_admin.php?id="
                . $plan_id
                . "&updated=1"
            );

            exit;


        } else {

            $errors[] =
                "Failed to update membership plan.";

        }


        $stmt->close();

    }


    /*
        Keep the edited values in the form
        if there is an error.
    */

    $plan['plan_name'] =
        $plan_name;

    $plan['duration_days'] =
        $duration_days;

    $plan['price'] =
        $price;

    $plan['description'] =
        $description;

    $plan['status'] =
        $status;

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
        Edit Membership Plan |
        Admin Panel
    </title>


    <link
        rel="stylesheet"
        href="assets/css/admin.css"
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


    <!-- SIDEBAR -->

    <?php
    include("includes/admin_sidebar.php");
    ?>


    <!-- MAIN -->

    <div class="main">

        <!-- CONTENT -->

        <div class="dashboard-content membership-plan-form-content">


            <!-- =========================
                 PAGE HEADER
            ========================= -->

            <div class="page-header">


                <div>

                    <h2>
                        Edit Membership Plan
                    </h2>

                    <p>
                        Update membership plan information.
                    </p>

                </div>


                <a
                    href="membership_plan_view_admin.php?id=<?php echo $plan_id; ?>"
                    class="back-staff-btn"
                >

                    <i class="fa-solid fa-arrow-left"></i>

                    Back to Plan

                </a>


            </div>


            <!-- =========================
                 ERROR MESSAGE
            ========================= -->

            <?php if (!empty($errors)): ?>

                <div class="form-error-box">

                    <i class="fa-solid fa-circle-exclamation"></i>


                    <div>

                        <?php foreach ($errors as $error): ?>

                            <div>

                                <?php
                                echo htmlspecialchars(
                                    $error
                                );
                                ?>

                            </div>

                        <?php endforeach; ?>

                    </div>

                </div>

            <?php endif; ?>


            <!-- =========================
                 FORM
            ========================= -->

            <div class="admin-form-card">


                <form
                    method="POST"
                    id="membershipPlanEditForm"
                >


                    <!-- PLAN NAME -->

                    <div class="form-group">

                        <label for="plan_name">
                            Plan Name
                        </label>


                        <div class="form-input-wrapper">

                            <i class="fa-solid fa-dumbbell"></i>


                            <input
                                type="text"
                                id="plan_name"
                                name="plan_name"
                                maxlength="50"
                                value="<?php

                                echo htmlspecialchars(
                                    $plan['plan_name']
                                );

                                ?>"
                                required
                            >

                        </div>

                    </div>


                    <!-- DURATION -->

                    <div class="form-group">

                        <label for="duration_days">
                            Duration
                        </label>


                        <div class="form-input-wrapper">

                            <i class="fa-solid fa-calendar-days"></i>


                            <input
                                type="number"
                                id="duration_days"
                                name="duration_days"
                                min="1"
                                value="<?php

                                echo htmlspecialchars(
                                    $plan['duration_days']
                                );

                                ?>"
                                required
                            >


                            <span class="input-suffix">
                                days
                            </span>

                        </div>

                    </div>


                    <!-- PRICE -->

                    <div class="form-group">

                        <label for="price">
                            Price
                        </label>


                        <div class="form-input-wrapper">


                            <span class="currency-symbol">
                                ₱
                            </span>


                            <input
                                type="number"
                                id="price"
                                name="price"
                                min="0"
                                step="0.01"
                                value="<?php

                                echo htmlspecialchars(
                                    $plan['price']
                                );

                                ?>"
                                required
                            >

                        </div>

                    </div>


                    <!-- DESCRIPTION -->

                    <div class="form-group">

                        <label for="description">
                            Description
                        </label>


                        <textarea
                            id="description"
                            name="description"
                            maxlength="255"
                        ><?php

                        echo htmlspecialchars(
                            $plan['description'] ?? ''
                        );

                        ?></textarea>

                    </div>


                    <!-- STATUS -->

                    <div class="form-group">

                        <label for="status">
                            Status
                        </label>


                        <div class="form-input-wrapper">

                            <i class="fa-solid fa-toggle-on"></i>


                            <select
                                id="status"
                                name="status"
                            >


                                <option
                                    value="Active"
                                    <?php

                                    echo $plan['status']
                                        === 'Active'
                                        ? 'selected'
                                        : '';

                                    ?>
                                >

                                    Active

                                </option>


                                <option
                                    value="Inactive"
                                    <?php

                                    echo $plan['status']
                                        === 'Inactive'
                                        ? 'selected'
                                        : '';

                                    ?>
                                >

                                    Inactive

                                </option>


                            </select>

                        </div>

                    </div>


                    <!-- BUTTONS -->

                    <div class="form-actions">


                        <a
                            href="membership_plan_view_admin.php?id=<?php echo $plan_id; ?>"
                            class="btn-secondary"
                        >

                            Cancel

                        </a>


                        <button
    type="button"
    class="btn-primary"
    onclick="openEditPlanModal()"
>
    <i class="fa-solid fa-floppy-disk"></i>
    Save Changes
</button>

                    </div>


                </form>


            </div>


        </div>


    </div>


</div>


<!-- =========================
     EDIT PLAN CONFIRMATION
========================= -->

<div
    id="editPlanModal"
    class="edit-plan-modal"
>

    <div class="edit-plan-modal-box">

        <div class="edit-plan-icon">

            <i class="fa-solid fa-pen-to-square"></i>

        </div>

        <h2>
            Save Changes?
        </h2>

        <p>
            Are you sure you want to update
            <strong>
                <?php echo htmlspecialchars($plan['plan_name']); ?>
            </strong>
            ?
        </p>

        <div class="edit-plan-modal-actions">

            <button
                type="button"
                class="edit-plan-cancel"
                onclick="closeEditPlanModal()"
            >
                No
            </button>

            <button
                type="button"
                class="edit-plan-confirm"
                onclick="confirmEditPlan()"
            >
                Yes, Save Changes
            </button>

        </div>

    </div>

</div>


<script>

function openEditPlanModal(){

    document
        .getElementById("editPlanModal")
        .classList.add("show");

}


function closeEditPlanModal(){

    document
        .getElementById("editPlanModal")
        .classList.remove("show");

}


function confirmEditPlan(){

    document
        .getElementById("membershipPlanEditForm")
        .submit();

}


document
    .getElementById("editPlanModal")
    .addEventListener("click", function(event){

        if(event.target === this){

            closeEditPlanModal();

        }

    });

</script>


</body>
</html>