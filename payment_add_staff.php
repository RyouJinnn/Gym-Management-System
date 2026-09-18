<?php

require_once("includes/staff_auth.php");
require_once("includes/activity_logger.php");
require_once("phpqrcode/qrlib.php");

$members = [];

$result = $con->query("
    SELECT 
        id,
        first_name,
        middlename,
        last_name
    FROM signup
    ORDER BY first_name ASC
");

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $members[] = $row;
    }
}


// Get membership plans
$plans = [];

$result = $con->query("
    SELECT 
        plan_id,
        plan_name,
        price,
        duration_days
    FROM membership_plans
    ORDER BY plan_name ASC
");

if (!$result) {
    die("SQL Error: " . $con->error);
}

while ($row = $result->fetch_assoc()) {
    $plans[] = $row;
}


// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $member_id = isset($_POST['member_id'])
    ? (int) $_POST['member_id']
    : 0;

$member_id = $member_id > 0 ? $member_id : null;

$payer_name = trim($_POST['payer_name'] ?? '');

$plan_id = isset($_POST['plan_id'])
        ? (int) $_POST['plan_id']
        : 0;

    $amount = isset($_POST['amount'])
        ? (float) $_POST['amount']
        : 0;

    $payment_method = trim($_POST['payment_method'] ?? '');

    $payment_status = trim($_POST['payment_status'] ?? 'Pending');

    $transaction_reference = trim(
        $_POST['transaction_reference'] ?? ''
    );

    $payment_date = $_POST['payment_date'] ?? date('Y-m-d H:i:s');

    if (
    empty($payer_name) ||
    $plan_id <= 0 ||
    $amount <= 0 ||
    empty($payment_method)
) {

    $error = "Please enter the payer name and complete all required fields.";

    } else {

        $proof_of_payment = null;


        // Upload proof of payment
        if (
            isset($_FILES['proof_of_payment']) &&
            $_FILES['proof_of_payment']['error'] === UPLOAD_ERR_OK
        ) {

            $upload_dir = "proof_of_payment/";

            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }


            $file_name = $_FILES['proof_of_payment']['name'];
            $tmp_name = $_FILES['proof_of_payment']['tmp_name'];
            $file_size = $_FILES['proof_of_payment']['size'];


            $allowed_types = [
                'image/jpeg',
                'image/png',
                'image/webp',
                'application/pdf'
            ];


            $file_type = mime_content_type($tmp_name);


            if (!in_array($file_type, $allowed_types)) {

                $error = "Invalid proof of payment file.";

            } elseif ($file_size > 5 * 1024 * 1024) {

                $error = "Proof of payment must not exceed 5MB.";

            } else {

                $extension = strtolower(
                    pathinfo($file_name, PATHINFO_EXTENSION)
                );

                $new_file_name =
                    'payment_' .
                    time() .
                    '_' .
                    bin2hex(random_bytes(4)) .
                    '.' .
                    $extension;

                $destination =
                    $upload_dir . $new_file_name;


                if (move_uploaded_file($tmp_name, $destination)) {

                    $proof_of_payment = $destination;

                } else {

                    $error = "Failed to upload proof of payment.";

                }
            }
        }


        // Insert payment
        if (!isset($error)) {

            $stmt = $con->prepare("
    INSERT INTO payments
    (
        member_id,
        payer_name,
        plan_id,
        amount,
        payment_method,
        payment_status,
        transaction_reference,
        proof_of_payment,
        payment_date
    )
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
");


            if (!$stmt) {

                $error = "SQL Error: " . $con->error;

            } else {

                $stmt->bind_param(
    "isidsssss",
    $member_id,
    $payer_name,
    $plan_id,
    $amount,
    $payment_method,
    $payment_status,
    $transaction_reference,
    $proof_of_payment,
    $payment_date
);

                if ($stmt->execute()) {

    if($payment_status === "Approved" && $member_id !== null){

        // Get membership plan details
        $planStmt = $con->prepare("
            SELECT plan_name, duration_days
            FROM membership_plans
            WHERE plan_id = ?
            LIMIT 1
        ");

        $planStmt->bind_param("i", $plan_id);
        $planStmt->execute();

        $planResult = $planStmt->get_result();
        $plan = $planResult->fetch_assoc();

        $planStmt->close();

        if($plan){

            $start_date = date("Y-m-d");

            $end_date = date(
                "Y-m-d",
                strtotime("+".$plan['duration_days']." days")
            );

            $membershipCheck = $con->prepare("
                SELECT membership_id
                FROM membership
                WHERE member_id = ?
                AND plan_id = ?
                AND start_date = ?
                LIMIT 1
            ");

            $membershipCheck->bind_param(
                "iis",
                $member_id,
                $plan_id,
                $start_date
            );

            $membershipCheck->execute();

            $membershipResult = $membershipCheck->get_result();
            $existingMembership = $membershipResult->fetch_assoc();

            $membershipCheck->close();

            if($existingMembership){

                $membershipUpdate = $con->prepare("
                    UPDATE membership
                    SET status = 'Active',
                        end_date = ?
                    WHERE membership_id = ?
                ");

                $membershipUpdate->bind_param(
                    "si",
                    $end_date,
                    $existingMembership['membership_id']
                );

                $membershipUpdate->execute();
                $membershipUpdate->close();

                logActivity(
                    $con,
                    "Membership Activation",
                    "Membership for Member #" . $member_id .
                    " was activated after an approved payment by staff.",
                    $_SESSION['staff_id'] ?? null,
                    "Staff"
                );

            }else{

                $membershipInsert = $con->prepare("
                    INSERT INTO membership
                    (
                        member_id,
                        plan_id,
                        start_date,
                        end_date,
                        status
                    )
                    VALUES (?, ?, ?, ?, 'Active')
                ");

                $membershipInsert->bind_param(
                    "iiss",
                    $member_id,
                    $plan_id,
                    $start_date,
                    $end_date
                );

                $membershipInsert->execute();
                $membershipInsert->close();

                logActivity(
                    $con,
                    "Membership Creation",
                    "A new " . $plan['plan_name'] .
                    " membership was created for Member #" . $member_id .
                    " after an approved payment by staff.",
                    $_SESSION['staff_id'] ?? null,
                    "Staff"
                );
            }
        }

        // Create qrcodes folder if it does not exist
        $qrFolder = __DIR__ . "/qrcodes/";

        if(!is_dir($qrFolder)){
            mkdir($qrFolder, 0777, true);
        }

        // QR content
        $qrData = "M" . str_pad($member_id, 5, "0", STR_PAD_LEFT);

        // Save QR image
        $qrFile = $qrFolder . $qrData . ".png";

        QRcode::png(
            $qrData,
            $qrFile,
            QR_ECLEVEL_H,
            6,
            2
        );

    }

    $paymentDescription = $member_id !== null
        ? "A payment of ₱" . number_format($amount, 2) .
          " was added for Member #" . $member_id .
          " by staff with status " . $payment_status . "."
        : "A payment of ₱" . number_format($amount, 2) .
          " was added for " . $payer_name .
          " by staff with status " . $payment_status . ".";

    logActivity(
        $con,
        "Payment Submission",
        $paymentDescription,
        $_SESSION['staff_id'] ?? null,
        "Staff"
    );

    $stmt->close();

    header(
        "Location: payments_staff.php?added=1"
    );

    exit;
} else {

                    $error =
                        "Failed to add payment: " .
                        $stmt->error;

                    $stmt->close();
                }
            }
        }
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

    <title>Add Payment | Staff Panel</title>


    <!-- Font Awesome -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

    <link
href="https://fonts.googleapis.com/css2?family=Orbitron:wght@600;700&family=Poppins:wght@300;400;500;600;700&display=swap"
rel="stylesheet">


    <!-- Your existing staff CSS -->
    <link
        rel="stylesheet"
        href="assets/css/admin.css"
    >

</head>


<body>

<?php include("includes/staff_sidebar.php"); ?>


<div class="main">

    <div class="dashboard-content">


        <div
            style="
                display:flex;
                justify-content:space-between;
                align-items:center;
                gap:20px;
            "
        >

            <div>

                <h1 class="page-title">
                    Add Payment
                </h1>

                <p class="page-subtitle">
                    Record a new gym member payment.
                </p>

            </div>


            <a
    href="payments_staff.php"
    class="back-staff-btn"
>
    <i class="fa-solid fa-arrow-left"></i>
    Back to Payments
</a>

        </div>


        <?php if (isset($error)): ?>

            <div class="error-message">
                <i class="fa-solid fa-circle-exclamation"></i>
                <?= htmlspecialchars($error) ?>
            </div>

        <?php endif; ?>


        <form
            method="POST"
            enctype="multipart/form-data"
            class="payment-form-card"
        >

            <div class="form-grid">


                <!-- MEMBER -->

                <div class="form-group">

                    <label>
                        <i class="fa-solid fa-user"></i>
                        Member
                    </label>

                    <select
                        name="member_id"
                    >

                        <option value="">
                            Select Member
                        </option>

                        <?php foreach ($members as $member): ?>

                            <?php
                            $full_name =
                                $member['first_name'] . ' ' .
                                (!empty($member['middlename'])
                                    ? $member['middlename'] . ' '
                                    : '') .
                                $member['last_name'];
                            ?>

                            <option
                                value="<?= $member['id'] ?>"
                            >
                                <?= htmlspecialchars($full_name) ?>
                                — Member #<?= $member['id'] ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>

                <div class="form-group">

    <label>
        <i class="fa-solid fa-user"></i>
        Payer Name
    </label>

    <input
        type="text"
        name="payer_name"
        id="payer_name"
        placeholder="Enter full name"
        maxlength="150"
        required
    >

</div>


                <!-- PLAN -->

                <div class="form-group">

                    <label>
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

                        <?php foreach ($plans as $plan): ?>

                            <option
                                value="<?= $plan['plan_id'] ?>"
                                data-price="<?= $plan['price'] ?>"
                            >
                                <?= htmlspecialchars($plan['plan_name']) ?>
                                — ₱<?= number_format($plan['price'], 2) ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>


                <!-- AMOUNT -->

                <div class="form-group">

                    <label>
                        <i class="fa-solid fa-peso-sign"></i>
                        Amount
                    </label>

                    <input
                        type="number"
                        name="amount"
                        id="amount"
                        step="0.01"
                        min="0.01"
                        placeholder="0.00"
                        required
                    >

                    <div
                        class="amount-display"
                        id="amountHint"
                    >
                        Select a plan to automatically set the amount.
                    </div>

                </div>


                <!-- PAYMENT METHOD -->

                <div class="form-group">

                    <label>
                        <i class="fa-solid fa-wallet"></i>
                        Payment Method
                    </label>

                    <select
                        name="payment_method"
                        required
                    >

                        <option value="">
                            Select Payment Method
                        </option>

                        <option value="GCash">
                            GCash
                        </option>

                        <option value="Pay at the Counter">
                            Pay at the Counter
                        </option>

                    </select>

                </div>


                <!-- STATUS -->

                <div class="form-group">

                    <label>
                        <i class="fa-solid fa-circle-check"></i>
                        Payment Status
                    </label>

                    <select
                        name="payment_status"
                        required
                    >

                        <option value="Pending">
                            Pending
                        </option>

                        <option value="Approved">
                            Approved
                        </option>

                    </select>

                </div>


                <!-- PAYMENT DATE -->

                <div class="form-group">

                    <label>
                        <i class="fa-solid fa-calendar"></i>
                        Payment Date
                    </label>

                    <input
                        type="datetime-local"
                        name="payment_date"
                        value="<?= date('Y-m-d\TH:i') ?>"
                        required
                    >

                </div>


                <!-- TRANSACTION REFERENCE -->

                <div class="form-group full">

                    <label>
                        <i class="fa-solid fa-receipt"></i>
                        Transaction Reference
                    </label>

                    <input
                        type="text"
                        name="transaction_reference"
                        placeholder="Enter transaction/reference number"
                        maxlength="100"
                    >

                </div>


                <!-- PROOF -->

                <div class="form-group full">

                    <label>
                        <i class="fa-solid fa-image"></i>
                        Proof of Payment
                    </label>

                    <div class="file-box">

                        <input
                            type="file"
                            name="proof_of_payment"
                            accept=".jpg,.jpeg,.png,.webp,.pdf"
                        >

                        <div class="file-help">
                            JPG, PNG, WEBP, or PDF. Maximum 5MB.
                        </div>

                    </div>

                </div>


            </div>


            <div class="form-actions">

                <a
                    href="payments_staff.php"
                    class="btn-cancel"
                >
                    Cancel
                </a>


                <button
                    type="submit"
                    class="btn-submit"
                >
                    <i class="fa-solid fa-plus"></i>
                    Add Payment
                </button>

            </div>

        </form>

    </div>

</div>


<script>

const planSelect =
    document.getElementById("plan_id");

const amountInput =
    document.getElementById("amount");

const amountHint =
    document.getElementById("amountHint");


planSelect.addEventListener("change", function () {

    const selected =
        this.options[this.selectedIndex];

    const price =
        selected.getAttribute("data-price");


    if (price) {

        amountInput.value =
            parseFloat(price).toFixed(2);

        amountHint.textContent =
            "Amount automatically loaded from the selected plan.";

    } else {

        amountInput.value = "";

        amountHint.textContent =
            "Select a plan to automatically set the amount.";

    }

});

</script>


</body>

</html>