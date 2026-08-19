<?php

require_once("includes/staff_auth.php");


// ================================
// GET SEARCH / FILTER VALUES
// ================================
$search = trim($_GET['search'] ?? '');
$status = trim($_GET['status'] ?? '');


// ================================
// GET PAYMENT DATA
// ================================
$sql = "
    SELECT
        p.payment_id,
        p.member_id,
        p.plan_id,
        p.amount,
        p.payment_method,
        p.payment_status,
        p.transaction_reference,
        p.proof_of_payment,
        p.payment_date,

        s.first_name,
        s.middlename,
        s.last_name,

        mp.plan_name

    FROM payments p

    LEFT JOIN signup s
        ON p.member_id = s.id

    LEFT JOIN membership_plans mp
        ON p.plan_id = mp.plan_id

    WHERE 1=1
";

$params = [];
$types = "";


// ================================
// STATUS FILTER
// ================================
if ($status !== '') {
    $sql .= " AND p.payment_status = ?";
    $params[] = $status;
    $types .= "s";
}


// ================================
// SEARCH FILTER
// ================================
if ($search !== '') {
    $sql .= "
        AND (
            s.first_name LIKE ?
            OR s.middlename LIKE ?
            OR s.last_name LIKE ?
            OR mp.plan_name LIKE ?
            OR p.payment_method LIKE ?
            OR p.transaction_reference LIKE ?
        )
    ";

    $searchValue = "%" . $search . "%";

    $params[] = $searchValue;
    $params[] = $searchValue;
    $params[] = $searchValue;
    $params[] = $searchValue;
    $params[] = $searchValue;
    $params[] = $searchValue;

    $types .= "ssssss";
}


$sql .= " ORDER BY p.payment_id DESC";


// ================================
// EXECUTE QUERY
// ================================
$stmt = $con->prepare($sql);

if (!$stmt) {
    die("SQL Error: " . $con->error);
}

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();

$result = $stmt->get_result();

// ================================
// TOTAL PAYMENTS
// ================================

$totalPayments = 0;
$totalAmount = 0;

$countResult = $con->query("
    SELECT
        COUNT(*) AS total_payments,
        COALESCE(SUM(amount), 0) AS total_amount
    FROM payments
");

if ($countResult) {

    $countData = $countResult->fetch_assoc();

    $totalPayments = $countData['total_payments'];
    $totalAmount = $countData['total_amount'];
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

    <title>Payments | Staff Panel</title>

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
    <?php include("includes/staff_sidebar.php"); ?>

    <!-- MAIN CONTENT -->
    <div class="main">

    <div class="dashboard-content payments-content">


        <!-- PAGE TITLE -->

       <div class="page-header">

    <div>

        <h2>
            Payments
        </h2>

        <p>
            Manage gym member payments and transactions.
        </p>

    </div>

    <div class="page-header-actions">

        <div class="total-members-box">

            <i class="fa-solid fa-money-check-dollar"></i>

            <strong>
                <?= number_format($totalPayments) ?>
            </strong>

            Payments

        </div>

        <a
            href="payment_add_staff.php"
            class="btn-primary"
        >

            <i class="fa-solid fa-plus"></i>

            Add Payment

        </a>

    </div>

</div>


        <!-- ================================
             STAT CARDS
        ================================= -->

        <div class="stats-grid">


            <div class="stat-card">

                <div class="stat-icon">
                    <i class="fa-solid fa-money-bill-wave"></i>
                </div>

                <div>

                    <span>
                        Total Payments
                    </span>

                    <strong>
                        <?= number_format($totalPayments) ?>
                    </strong>

                </div>

            </div>


            <div class="stat-card">

                <div class="stat-icon">
                    <i class="fa-solid fa-peso-sign"></i>
                </div>

                <div>

                    <span>
                        Total Amount
                    </span>

                    <strong>
                        ₱<?= number_format($totalAmount, 2) ?>
                    </strong>

                </div>

            </div>


        </div>


        <!-- ================================
             SEARCH / FILTER
        ================================= -->

        <form method="GET" class="member-filter-box">

    <div class="search-box">

        <i class="fa-solid fa-magnifying-glass"></i>

        <input
    type="text"
    id="paymentSearch"
    name="search"
    placeholder="Search payments..."
    value="<?= htmlspecialchars($search) ?>"
>

    </div>

    <select name="status" id="statusFilter">

    <option value=""
        <?= $status === '' ? 'selected' : '' ?>>
        All Status
    </option>

    <option value="Pending"
        <?= $status === 'Pending' ? 'selected' : '' ?>>
        Pending
    </option>

    <option value="Approved"
        <?= $status === 'Approved' ? 'selected' : '' ?>>
        Approved
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


        <!-- ================================
             PAYMENT TABLE
        ================================= -->

        <div class="table-container">

            <table id="paymentsTable">

                <thead>

                    <tr>

                        <th>
                            ID
                        </th>

                        <th>
                            Member
                        </th>

                        <th>
                            Plan
                        </th>

                        <th>
                            Amount
                        </th>

                        <th>
                            Method
                        </th>

                        <th>
                            Status
                        </th>

                        <th>
                            Payment Date
                        </th>
                        <th>Action</th>

                    </tr>

                </thead>


                <tbody>

                <?php if ($result->num_rows > 0): ?>

                    <?php while ($row = $result->fetch_assoc()): ?>


                        <?php

                        $fullName = trim(
                            $row['first_name'] . ' ' .
                            ($row['middle_name'] ?? '') . ' ' .
                            $row['last_name']
                        );

                        ?>


                        <tr>


                            <!-- ID -->

                            <td>

                                #<?= (int)$row['payment_id'] ?>

                            </td>


                            <!-- MEMBER -->

                            <td>

                                <div class="member-name">

                                    <strong>
                                        <?= htmlspecialchars($fullName) ?>
                                    </strong>

                                    <small>
                                        Member #<?= (int)$row['member_id'] ?>
                                    </small>

                                </div>

                            </td>


                            <!-- PLAN -->

                            <td>

                                <?= htmlspecialchars(
                                    $row['plan_name'] ?? 'No Plan'
                                ) ?>

                            </td>


                            <!-- AMOUNT -->

                            <td>

                                <strong class="payment-amount">

                                    ₱<?= number_format(
                                        $row['amount'],
                                        2
                                    ) ?>

                                </strong>

                            </td>


                            <!-- METHOD -->

                            <td>

                                <?= htmlspecialchars(
                                    $row['payment_method']
                                ) ?>

                            </td>


                            <!-- STATUS -->

                            <td>

                                <?php if ($row['payment_status'] === 'Approved'): ?>

                                    <span class="status-badge active">
                                        Approved
                                    </span>

                                <?php else: ?>

                                    <span class="status-badge pending">
                                        Pending
                                    </span>

                                <?php endif; ?>

                            </td>


                            <!-- DATE -->

                            <td>

                                <?= date(
                                    "M d, Y",
                                    strtotime($row['payment_date'])
                                ) ?>

                            </td>

                            <!-- ACTION -->

                            <td>

                                <div class="action-buttons">


                                    <a
                                        href="payment_view_staff.php?id=<?= (int)$row['payment_id'] ?>"
                                        class="action-btn view"
                                        title="View Payment"
                                    >

                                        <i class="fa-solid fa-eye"></i>

                                    </a>


                                    <a
                                        href="payment_edit_staff.php?id=<?= (int)$row['payment_id'] ?>"
                                        class="action-btn edit"
                                        title="Edit Payment"
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
                            class="empty-state"
                        >

                            <i class="fa-solid fa-receipt"></i>

                            <p>
                                No payments found.
                            </p>

                        </td>

                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>


    </div>

 </div>

</div>

<!-- DELETE PAYMENT MODAL -->
<div id="deletePaymentModal" class="delete-modal-overlay">

    <div class="delete-modal">

        <div class="delete-modal-icon">
            <i class="fa-solid fa-trash"></i>
        </div>

        <h3>Delete Payment?</h3>

        <p>
            Are you sure you want to delete this payment?
            This action cannot be undone.
        </p>

        <div class="delete-modal-actions">

            <button
                type="button"
                class="delete-modal-cancel"
                onclick="closeDeletePaymentModal()"
            >
                Cancel
            </button>

            <button
                type="button"
                class="delete-modal-confirm"
                onclick="confirmDeletePayment()"
            >
                <i class="fa-solid fa-trash"></i>
                Delete
            </button>

        </div>

    </div>

</div>

<!-- PAYMENT RESULT MODAL -->
<div id="paymentResultModal" class="payment-result-overlay">

    <div class="payment-result-modal">

        <div class="payment-result-icon" id="paymentResultIcon">
            <i class="fa-solid fa-check"></i>
        </div>

        <h3 id="paymentResultTitle">
            Success!
        </h3>

        <p id="paymentResultMessage">
            Payment completed successfully.
        </p>

        <button
            type="button"
            class="payment-result-ok"
            onclick="closePaymentResultModal()"
        >
            OK
        </button>

    </div>

</div>

<script>

function searchPayments() {

    const input =
        document.getElementById("paymentSearch")
        .value
        .toLowerCase();

    const rows =
        document.querySelectorAll(
            "#paymentsTable tbody tr"
        );

    rows.forEach(row => {

        const text =
            row.textContent.toLowerCase();

        row.style.display =
            text.includes(input)
                ? ""
                : "none";

    });

}

let paymentToDelete = null;

function deletePayment(id) {

    paymentToDelete = id;

    document
        .getElementById("deletePaymentModal")
        .classList.add("show");

}

function closeDeletePaymentModal() {

    paymentToDelete = null;
    document.getElementById("deletePaymentModal").classList.remove("show");
}

function confirmDeletePayment() {

    if (paymentToDelete !== null) {
        window.location.href =
            "payment_delete_staff.php?id=" + paymentToDelete;
    }
}

function showPaymentResult(type, message) {

    const modal = document.getElementById("paymentResultModal");
    const icon = document.getElementById("paymentResultIcon");
    const title = document.getElementById("paymentResultTitle");
    const messageText = document.getElementById("paymentResultMessage");

    if (type === "success") {

        icon.innerHTML = '<i class="fa-solid fa-check"></i>';
        icon.className = "payment-result-icon success";

        title.textContent = "Success!";
        messageText.textContent = message;

    } else {

        icon.innerHTML = '<i class="fa-solid fa-xmark"></i>';
        icon.className = "payment-result-icon failed";

        title.textContent = "Failed!";
        messageText.textContent = message;
    }

    modal.classList.add("show");
}

function closePaymentResultModal() {

    document
        .getElementById("paymentResultModal")
        .classList.remove("show");

}

const paymentParams = new URLSearchParams(window.location.search);

if (paymentParams.get("added") === "1") {

    showPaymentResult(
        "success",
        "Payment added successfully."
    );

}

else if (paymentParams.get("updated") === "1") {

    showPaymentResult(
        "success",
        "Payment updated successfully."
    );

}

else if (paymentParams.get("deleted") === "1") {

    showPaymentResult(
        "success",
        "Payment deleted successfully."
    );

}

else if (paymentParams.get("error") === "1") {

    showPaymentResult(
        "failed",
        "Something went wrong. Please try again."
    );

}

</script>


</body>
</html>