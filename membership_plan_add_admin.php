<?php

require_once("includes/admin_auth.php");


// ==============================
// HANDLE FORM SUBMISSION
// ==============================

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $plan_name = trim($_POST['plan_name'] ?? '');
    $duration_days = (int)($_POST['duration_days'] ?? 0);
    $price = (float)($_POST['price'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $status = $_POST['status'] ?? 'Active';


    // ==============================
    // VALIDATION
    // ==============================

    $errors = [];


    if ($plan_name === '') {

        $errors[] = "Plan name is required.";

    }


    if ($duration_days <= 0) {

        $errors[] = "Duration must be greater than 0 days.";

    }


    if ($price < 0) {

        $errors[] = "Price cannot be negative.";

    }


    if (
        $status !== "Active" &&
        $status !== "Inactive"
    ) {

        $errors[] = "Invalid status.";

    }


    // ==============================
    // INSERT
    // ==============================

    if (empty($errors)) {

        $stmt = $con->prepare("
            INSERT INTO membership_plans
            (
                plan_name,
                duration_days,
                price,
                description,
                status
            )
            VALUES (?, ?, ?, ?, ?)
        ");


        if ($stmt === false) {

            die("SQL Error: " . $con->error);

        }


        $stmt->bind_param(
            "sidss",
            $plan_name,
            $duration_days,
            $price,
            $description,
            $status
        );


        if ($stmt->execute()) {

            $new_plan_id = $stmt->insert_id;

            $stmt->close();


            header(
                "Location: membership_plan_view_admin.php?id="
                . $new_plan_id
                . "&created=1"
            );

            exit;

        } else {

            $errors[] = "Failed to create membership plan.";

        }


        $stmt->close();

    }

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

<title>Add Membership Plan | Admin Panel</title>


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


<?php include("includes/admin_sidebar.php"); ?>


<div class="main">

<div class="dashboard-content membership-plan-form-content">


    <!-- =========================
         PAGE HEADER
    ========================= -->

    <div class="page-header">

        <div>

            <h2>
                Add Membership Plan
            </h2>

            <p>
                Create a new gym membership plan.
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
         ERROR MESSAGE
    ========================= -->

    <?php if (!empty($errors)): ?>

        <div class="form-error-box">

            <i class="fa-solid fa-circle-exclamation"></i>

            <div>

                <?php foreach ($errors as $error): ?>

                    <div>
                        <?php echo htmlspecialchars($error); ?>
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
            id="membershipPlanForm"
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
                        placeholder="Example: Premium Monthly"
                        value="<?php
                        echo htmlspecialchars(
                            $_POST['plan_name'] ?? ''
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
                        placeholder="Example: 30"
                        value="<?php
                        echo htmlspecialchars(
                            $_POST['duration_days'] ?? ''
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
                        placeholder="Example: 299.00"
                        value="<?php
                        echo htmlspecialchars(
                            $_POST['price'] ?? ''
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
                    placeholder="Enter a short description of this membership plan..."
                ><?php
                echo htmlspecialchars(
                    $_POST['description'] ?? ''
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

                        <option value="Active">
                            Active
                        </option>

                        <option value="Inactive">
                            Inactive
                        </option>

                    </select>

                </div>

            </div>


            <!-- BUTTONS -->

            <div class="form-actions">


                <a
                    href="membership_plans_admin.php"
                    class="btn-secondary"
                >

                    Cancel

                </a>


                <button
                    type="submit"
                    class="btn-primary"
                >

                    <i class="fa-solid fa-plus"></i>

                    Add Membership Plan

                </button>


            </div>


        </form>
    </div>
</div>
</div>
</div>

<!-- ==========================================
     ADD MEMBERSHIP PLAN CONFIRMATION POPUP
========================================== -->

<div
    id="addPlanModal"
    class="add-plan-modal"
>

    <div class="add-plan-modal-box">

        <!-- ICON -->

        <div class="add-plan-icon">

            <i class="fa-solid fa-plus"></i>

        </div>


        <!-- TITLE -->

        <h2>
            Add Membership Plan?
        </h2>


        <!-- MESSAGE -->

        <p>
            Are you sure you want to add
            <strong id="addPlanName">
                this membership plan
            </strong>?
        </p>


        <!-- BUTTONS -->

        <div class="add-plan-modal-actions">


            <!-- NO -->

            <button
                type="button"
                class="add-plan-cancel"
                onclick="closeAddPlanModal()"
            >

                No

            </button>


            <!-- YES -->

            <button
                type="button"
                class="add-plan-confirm"
                onclick="confirmAddPlan()"
            >

                Yes, Add

            </button>


        </div>

    </div>

</div>


<!-- ==========================================
     ADD PLAN POPUP JAVASCRIPT
========================================== -->

<script>

const membershipPlanForm =
    document.getElementById("membershipPlanForm");

const addPlanModal =
    document.getElementById("addPlanModal");


/*
    When the Add Membership Plan button
    is clicked
*/

membershipPlanForm.addEventListener(
    "submit",
    function(event) {

        /*
            Stop the form from submitting immediately
        */

        event.preventDefault();


        /*
            Check HTML required fields
        */

        if (!membershipPlanForm.checkValidity()) {

            membershipPlanForm.reportValidity();

            return;

        }


        /*
            Get the plan name
        */

        const planName =
            document.getElementById("plan_name").value.trim();


        /*
            Show plan name inside popup
        */

        document.getElementById("addPlanName")
            .textContent = planName;


        /*
            Show popup
        */

        addPlanModal.classList.add("show");

    }
);


/*
    Close popup
*/

function closeAddPlanModal() {

    addPlanModal.classList.remove("show");

}


/*
    YES, ADD
*/

function confirmAddPlan() {

    /*
        Remove the submit listener temporarily
        and submit the form normally
    */

    membershipPlanForm.submit();

}


/*
    Close popup when clicking
    outside the popup box
*/

addPlanModal.addEventListener(
    "click",
    function(event) {

        if (event.target === addPlanModal) {

            closeAddPlanModal();

        }

    }
);

</script>

</body>
</html>