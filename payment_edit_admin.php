<?php

require_once("includes/admin_auth.php");

if (
    !isset($_GET['id']) ||
    !is_numeric($_GET['id'])
) {
    header("Location: payments_admin.php");
    exit;
}

$payment_id = (int) $_GET['id'];
$updated = isset($_GET['updated']) && $_GET['updated'] === '1';

if ($payment_id <= 0) {
    header("Location: payments_admin.php");
    exit;
}

$stmt = $con->prepare("
    SELECT
        p.payment_id,
        p.member_id,
        p.plan_id,
        p.amount,
        p.payment_method,
        p.payment_status,
        p.transaction_reference,
        p.proof_of_payment,
        p.payment_date
    FROM payments p
    WHERE p.payment_id = ?
    LIMIT 1
");

if ($stmt === false) {
    die("SQL Error: " . $con->error);
}

$stmt->bind_param("i", $payment_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $stmt->close();

    header("Location: payments_admin.php");
    exit;
}

$payment = $result->fetch_assoc();

$stmt->close();

$members = [];

$memberResult = $con->query("
    SELECT
        id,
        first_name,
        middlename,
        last_name
    FROM signup
    ORDER BY first_name ASC, last_name ASC
");

if ($memberResult) {

    while ($member = $memberResult->fetch_assoc()) {

        $members[] = $member;

    }

}


/* ================================
   GET MEMBERSHIP PLANS
================================ */

$plans = [];

$planResult = $con->query("
    SELECT
        plan_id,
        plan_name,
        price
    FROM membership_plans
    ORDER BY plan_name ASC
");

if ($planResult) {

    while ($plan = $planResult->fetch_assoc()) {

        $plans[] = $plan;

    }

}


/* ================================
   UPDATE PAYMENT
================================ */

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $member_id = isset($_POST['member_id'])
        ? (int) $_POST['member_id']
        : 0;

    $plan_id = isset($_POST['plan_id'])
        ? (int) $_POST['plan_id']
        : 0;

    $amount = isset($_POST['amount'])
        ? (float) $_POST['amount']
        : 0;

    $payment_method = trim(
        $_POST['payment_method'] ?? ''
    );

    $payment_status = trim(
        $_POST['payment_status'] ?? ''
    );

    $transaction_reference = trim(
        $_POST['transaction_reference'] ?? ''
    );

    $payment_date = trim(
        $_POST['payment_date'] ?? ''
    );


    /* ================================
       VALIDATION
    ================================ */

    $allowedMethods = [
        "Credit/Debit Card",
        "GCash",
        "Pay at the Counter"
    ];

    $allowedStatuses = [
        "Pending",
        "Approved"
    ];


    if ($member_id <= 0) {

        $error = "Please select a member.";

    } elseif ($plan_id <= 0) {

        $error = "Please select a membership plan.";

    } elseif ($amount <= 0) {

        $error = "Please enter a valid amount.";

    } elseif (!in_array(
        $payment_method,
        $allowedMethods,
        true
    )) {

        $error = "Please select a valid payment method.";

    } elseif (!in_array(
        $payment_status,
        $allowedStatuses,
        true
    )) {

        $error = "Please select a valid payment status.";

    } elseif ($payment_date === '') {

        $error = "Please select a payment date.";

    }


    /* ================================
       FILE UPLOAD
    ================================ */

    $proof_of_payment =
        $payment['proof_of_payment'] ?? null;


    if (
        $error === "" &&
        isset($_FILES['proof_of_payment']) &&
        $_FILES['proof_of_payment']['error'] !== UPLOAD_ERR_NO_FILE
    ) {

        if (
            $_FILES['proof_of_payment']['error']
            !== UPLOAD_ERR_OK
        ) {

            $error = "There was a problem uploading the payment proof.";

        } elseif (
            $_FILES['proof_of_payment']['size']
            > 5 * 1024 * 1024
        ) {

            $error = "Payment proof must not exceed 5MB.";

        } else {

            $allowedExtensions = [
                "jpg",
                "jpeg",
                "png",
                "webp",
                "pdf"
            ];

            $originalName =
                $_FILES['proof_of_payment']['name'];

            $extension = strtolower(
                pathinfo(
                    $originalName,
                    PATHINFO_EXTENSION
                )
            );


            if (
                !in_array(
                    $extension,
                    $allowedExtensions,
                    true
                )
            ) {

                $error =
                    "Only JPG, PNG, WEBP, and PDF files are allowed.";

            } else {

                $uploadDirectory =
                    __DIR__ . "/payment_proofs/";


                if (
                    !is_dir($uploadDirectory)
                ) {

                    mkdir(
                        $uploadDirectory,
                        0777,
                        true
                    );

                }


                $newFileName =
                    "payment_" .
                    $payment_id .
                    "_" .
                    time() .
                    "." .
                    $extension;


                $destination =
                    $uploadDirectory .
                    $newFileName;


                if (
                    move_uploaded_file(
                        $_FILES['proof_of_payment']['tmp_name'],
                        $destination
                    )
                ) {

                    $proof_of_payment =
                        "payment_proofs/" .
                        $newFileName;

                } else {

                    $error =
                        "Failed to save the uploaded payment proof.";

                }

            }

        }

    }


    /* ================================
       UPDATE DATABASE
    ================================ */

    if ($error === "") {

        $update = $con->prepare("
            UPDATE payments
            SET
                member_id = ?,
                plan_id = ?,
                amount = ?,
                payment_method = ?,
                payment_status = ?,
                transaction_reference = ?,
                proof_of_payment = ?,
                payment_date = ?
            WHERE payment_id = ?
        ");


        if ($update === false) {

            $error =
                "SQL Error: " . $con->error;

        } else {

            $update->bind_param(
                "iidsssssi",
                $member_id,
                $plan_id,
                $amount,
                $payment_method,
                $payment_status,
                $transaction_reference,
                $proof_of_payment,
                $payment_date,
                $payment_id
            );


            if ($update->execute()) {

    $update->close();

    /* ==========================================
       SYNC MEMBERSHIP WHEN PAYMENT IS APPROVED
    ========================================== */

    if ($payment_status === "Approved") {

        /* GET PLAN INFORMATION */
        $planStmt = $con->prepare("
            SELECT
                plan_name,
                price,
                duration_days
            FROM membership_plans
            WHERE plan_id = ?
            LIMIT 1
        ");

        $planStmt->bind_param(
            "i",
            $plan_id
        );

        $planStmt->execute();

        $planData = $planStmt
            ->get_result()
            ->fetch_assoc();

        $planStmt->close();


        if ($planData) {

            $plan_name =
                $planData['plan_name'];

            $duration_days =
                (int)$planData['duration_days'];

            $membershipPrice =
                (float)$planData['price'];

            /* USE PAYMENT DATE AS START DATE */
            $start_date =
                date(
                    "Y-m-d",
                    strtotime($payment_date)
                );

            /* CALCULATE END DATE */
            $end_date =
                date(
                    "Y-m-d",
                    strtotime(
                        $start_date .
                        " +" .
                        $duration_days .
                        " days"
                    )
                );


            /* CHECK IF THIS MEMBERSHIP ALREADY EXISTS */
            $checkMembership = $con->prepare("
                SELECT membership_id
                FROM membership
                WHERE member_id = ?
                AND plan_id = ?
                AND start_date = ?
                LIMIT 1
            ");

            $checkMembership->bind_param(
                "iis",
                $member_id,
                $plan_id,
                $start_date
            );

            $checkMembership->execute();

            $existingMembership =
                $checkMembership
                    ->get_result()
                    ->fetch_assoc();

            $checkMembership->close();


            if ($existingMembership) {

                /* UPDATE EXISTING MEMBERSHIP */

                $membershipUpdate = $con->prepare("
                    UPDATE membership
                    SET
                        plan_name = ?,
                        price = ?,
                        duration = ?,
                        start_date = ?,
                        end_date = ?,
                        status = 'Active'
                    WHERE membership_id = ?
                ");

                $membershipUpdate->bind_param(
                    "sdissi",
                    $plan_name,
                    $membershipPrice,
                    $duration_days,
                    $start_date,
                    $end_date,
                    $existingMembership['membership_id']
                );

                $membershipUpdate->execute();

                $membershipUpdate->close();

            } else {

                /* CREATE NEW MEMBERSHIP */

                $membershipInsert = $con->prepare("
                    INSERT INTO membership
                    (
                        member_id,
                        plan_id,
                        plan_name,
                        price,
                        duration,
                        start_date,
                        end_date,
                        status
                    )
                    VALUES
                    (
                        ?, ?, ?, ?, ?, ?, ?, 'Active'
                    )
                ");

                $membershipInsert->bind_param(
                    "iisdiss",
                    $member_id,
                    $plan_id,
                    $plan_name,
                    $membershipPrice,
                    $duration_days,
                    $start_date,
                    $end_date
                );

                $membershipInsert->execute();

                $membershipInsert->close();
            }
        }
    }


    /* ==========================================
       REDIRECT
    ========================================== */

    header(
        "Location: payment_edit_admin.php?id="
        . $payment_id
        . "&updated=1"
    );

    exit;
} else {

                $error =
                    "Failed to update payment: "
                    . $update->error;

                $update->close();

            }

        }

    }


    /* Keep entered values if there is an error */

    $payment['member_id'] =
        $member_id;

    $payment['plan_id'] =
        $plan_id;

    $payment['amount'] =
        $amount;

    $payment['payment_method'] =
        $payment_method;

    $payment['payment_status'] =
        $payment_status;

    $payment['transaction_reference'] =
        $transaction_reference;

    $payment['payment_date'] =
        $payment_date;

}


/* ================================
   DATETIME FOR INPUT
================================ */

$paymentDateValue = "";

if (
    !empty($payment['payment_date'])
) {

    $timestamp = strtotime(
        $payment['payment_date']
    );

    if ($timestamp !== false) {

        $paymentDateValue =
            date(
                "Y-m-d\TH:i",
                $timestamp
            );

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

    <title>Edit Payment | Admin Panel</title>


    <link
        rel="stylesheet"
        href="assets/css/admin.css"
    >

    <link
href="https://fonts.googleapis.com/css2?family=Orbitron:wght@600;700&family=Poppins:wght@300;400;500;600;700&display=swap"
rel="stylesheet">

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
    >

</head>


<body>

<div class="wrapper">

    <?php include("includes/admin_sidebar.php"); ?>


    <div class="main">

        <div class="dashboard-content payment-edit-content">


            <!-- PAGE HEADER -->

            <div class="staff-form-header">

                <div>

                    <h2>
                        Edit Payment
                    </h2>

                    <p>
                        Update payment transaction information.
                    </p>

                </div>


                <div class="page-header-actions">

                    <a
                        href="payment_view_admin.php?id=<?= $payment_id ?>"
                        class="back-staff-btn"
                    >

                        <i class="fa-solid fa-arrow-left"></i>

                        Back to Payment

                    </a>

                </div>

            </div>


            <!-- SUCCESS MESSAGE -->

<?php if ($updated): ?>

    <div class="member-success">

        <i class="fa-solid fa-circle-check"></i>

        Payment updated successfully.

    </div>

<?php endif; ?>


<!-- ERROR MESSAGE -->

<?php if ($error !== ""): ?>

    <div class="form-error">

        <i class="fa-solid fa-circle-exclamation"></i>

        <?= htmlspecialchars($error) ?>

    </div>

<?php endif; ?>


            <!-- FORM -->

            <form
                method="POST"
                enctype="multipart/form-data"
                class="admin-form"
            >


                <!-- PAYMENT INFORMATION -->

                <div class="form-card">

                    <div class="form-card-title">

                        <i class="fa-solid fa-money-bill-wave"></i>

                        Payment Information

                    </div>


                    <div class="form-grid">


                        <!-- MEMBER -->

                        <div class="form-group">

                            <label for="member_id">

                                <i class="fa-solid fa-user"></i>

                                Member

                            </label>


                            <select
                                name="member_id"
                                id="member_id"
                                required
                            >

                                <option value="">
                                    Select Member
                                </option>


                                <?php foreach (
                                    $members as $member
                                ): ?>

                                    <?php

                                    $memberName = trim(
                                        $member['first_name']
                                        . " "
                                        . ($member['middlename'] ?? '')
                                        . " "
                                        . $member['last_name']
                                    );

                                    ?>

                                    <option
                                        value="<?= (int)$member['id'] ?>"
                                        <?= (
                                            (int)$payment['member_id']
                                            === (int)$member['id']
                                        )
                                            ? 'selected'
                                            : ''
                                        ?>
                                    >

                                        <?= htmlspecialchars(
                                            $memberName
                                        ) ?>

                                        — Member #<?= (int)$member['id'] ?>

                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>


                        <!-- PLAN -->

                        <div class="form-group">

                            <label for="plan_id">

                                <i class="fa-solid fa-id-card"></i>

                                Membership Plan

                            </label>


                            <select
                                name="plan_id"
                                id="plan_id"
                                required
                            >

                                <option value="">
                                    Select Membership Plan
                                </option>


                                <?php foreach (
                                    $plans as $plan
                                ): ?>

                                    <option
                                        value="<?= (int)$plan['plan_id'] ?>"
                                        data-price="<?= htmlspecialchars($plan['price']) ?>"
                                        <?= (
                                            (int)$payment['plan_id']
                                            === (int)$plan['plan_id']
                                        )
                                            ? 'selected'
                                            : ''
                                        ?>
                                    >

                                        <?= htmlspecialchars(
                                            $plan['plan_name']
                                        ) ?>

                                        — ₱<?= number_format(
                                            (float)$plan['price'],
                                            2
                                        ) ?>

                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>


                        <!-- AMOUNT -->

                        <div class="form-group">

                            <label for="amount">

                                <i class="fa-solid fa-peso-sign"></i>

                                Amount

                            </label>


                            <input
                                type="number"
                                name="amount"
                                id="amount"
                                step="0.01"
                                min="0.01"
                                value="<?= htmlspecialchars(
                                    $payment['amount']
                                ) ?>"
                                required
                            >

                        </div>


                        <!-- PAYMENT METHOD -->

                        <div class="form-group">

                            <label for="payment_method">

                                <i class="fa-solid fa-wallet"></i>

                                Payment Method

                            </label>


                            <select
                                name="payment_method"
                                id="payment_method"
                                required
                            >

                                <option value="">
                                    Select Payment Method
                                </option>

                                <option
                                    value="Credit/Debit Card"
                                    <?= (
                                        $payment['payment_method']
                                        === "Credit/Debit Card"
                                    )
                                        ? 'selected'
                                        : ''
                                    ?>
                                >
                                    Credit/Debit Card
                                </option>

                                <option
                                    value="GCash"
                                    <?= (
                                        $payment['payment_method']
                                        === "GCash"
                                    )
                                        ? 'selected'
                                        : ''
                                    ?>
                                >
                                    GCash
                                </option>

                                <option
                                    value="Pay at the Counter"
                                    <?= (
                                        $payment['payment_method']
                                        === "Pay at the Counter"
                                    )
                                        ? 'selected'
                                        : ''
                                    ?>
                                >
                                    Pay at the Counter
                                </option>

                            </select>

                        </div>


                        <!-- STATUS -->

                        <div class="form-group">

                            <label for="payment_status">

                                <i class="fa-solid fa-circle-check"></i>

                                Payment Status

                            </label>


                            <select
                                name="payment_status"
                                id="payment_status"
                                required
                            >

                                <option
                                    value="Pending"
                                    <?= (
                                        $payment['payment_status']
                                        === "Pending"
                                    )
                                        ? 'selected'
                                        : ''
                                    ?>
                                >
                                    Pending
                                </option>

                                <option
                                    value="Approved"
                                    <?= (
                                        $payment['payment_status']
                                        === "Approved"
                                    )
                                        ? 'selected'
                                        : ''
                                    ?>
                                >
                                    Approved
                                </option>

                            </select>

                        </div>


                        <!-- PAYMENT DATE -->

                        <div class="form-group">

                            <label for="payment_date">

                                <i class="fa-solid fa-calendar"></i>

                                Payment Date

                            </label>


                            <input
                                type="datetime-local"
                                name="payment_date"
                                id="payment_date"
                                value="<?= htmlspecialchars(
                                    $paymentDateValue
                                ) ?>"
                                required
                            >

                        </div>


                    </div>

                </div>


                <!-- TRANSACTION DETAILS -->

                <div class="form-card">

                    <div class="form-card-title">

                        <i class="fa-solid fa-receipt"></i>

                        Transaction Details

                    </div>


                    <div class="form-group">

                        <label for="transaction_reference">

                            <i class="fa-solid fa-hashtag"></i>

                            Transaction Reference

                        </label>


                        <input
                            type="text"
                            name="transaction_reference"
                            id="transaction_reference"
                            maxlength="100"
                            value="<?= htmlspecialchars(
                                $payment['transaction_reference']
                                ?? ''
                            ) ?>"
                            placeholder="Enter transaction reference"
                        >

                    </div>

                </div>


                <!-- PROOF OF PAYMENT -->

                <div class="form-card">

                    <div class="form-card-title">

                        <i class="fa-solid fa-file-invoice"></i>

                        Proof of Payment

                    </div>


                    <?php if (
                        !empty(
                            $payment['proof_of_payment']
                        )
                    ): ?>

                        <div class="current-proof">

                            <i class="fa-solid fa-file-circle-check"></i>

                            <span>
                                Current proof of payment is uploaded.
                            </span>

                            <a
                                href="<?= htmlspecialchars(
                                    $payment['proof_of_payment']
                                ) ?>"
                                target="_blank"
                                class="payment-proof-btn"
                            >

                                <i class="fa-solid fa-eye"></i>

                                View Current Proof

                            </a>

                        </div>

                    <?php endif; ?>


                    <div class="form-group">

                        <label for="proof_of_payment">

                            <i class="fa-solid fa-upload"></i>

                            Replace Proof of Payment

                        </label>


                        <input
                            type="file"
                            name="proof_of_payment"
                            id="proof_of_payment"
                            accept=".jpg,.jpeg,.png,.webp,.pdf"
                        >


                        <small>
                            JPG, PNG, WEBP, or PDF. Maximum 5MB.
                        </small>

                    </div>

                </div>


                <!-- BUTTONS -->

                <div class="form-actions">

                    <a
                        href="payment_view_admin.php?id=<?= $payment_id ?>"
                        class="btn-secondary"
                    >

                        Cancel

                    </a>


                    <button
                        type="submit"
                        class="btn-primary"
                    >

                        <i class="fa-solid fa-save"></i>

                        Save Changes

                    </button>

                </div>


            </form>


        </div>

    </div>

</div>


<script>

/* Automatically update amount when plan changes */

const planSelect =
    document.getElementById("plan_id");

const amountInput =
    document.getElementById("amount");


if (planSelect && amountInput) {

    planSelect.addEventListener(
        "change",
        function () {

            const selected =
                this.options[
                    this.selectedIndex
                ];

            const price =
                selected.dataset.price;

            if (price) {

                amountInput.value =
                    parseFloat(price).toFixed(2);

            }

        }
    );

}

</script>


</body>

</html>