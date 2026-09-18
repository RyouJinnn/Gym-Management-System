<?php

require_once("includes/admin_auth.php");


/* ==========================
   GET PAYMENT ID
========================== */

if (
    !isset($_GET['id']) ||
    !is_numeric($_GET['id'])
) {
    header("Location: payments_admin.php");
    exit;
}

$payment_id = (int) $_GET['id'];


/* ==========================
   GET PAYMENT
========================== */

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

    WHERE p.payment_id = ?

    LIMIT 1
");


if ($stmt === false) {
    die("SQL Error: " . $con->error);
}


$stmt->bind_param(
    "i",
    $payment_id
);

$stmt->execute();

$result = $stmt->get_result();


/* ==========================
   PAYMENT NOT FOUND
========================== */

if ($result->num_rows === 0) {

    $stmt->close();

    header("Location: payments_admin.php");
    exit;
}


$payment = $result->fetch_assoc();

$stmt->close();


/* ==========================
   MEMBER NAME
========================== */

$memberName = trim(
    ($payment['first_name'] ?? '') . " " .
    ($payment['middlename'] ?? '') . " " .
    ($payment['last_name'] ?? '')
);


/* ==========================
   STATUS CLASS
========================== */

$statusClass = strtolower(
    $payment['payment_status'] ?? 'pending'
);

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
        Payment Details | Admin Panel
    </title>


    <!-- Google Fonts -->

    <link
        href="https://fonts.googleapis.com/css2?family=Orbitron:wght@600;700&family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet"
    >


    <!-- Font Awesome -->

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
    >


    <!-- Admin CSS -->

    <link
        rel="stylesheet"
        href="assets/css/admin.css"
    >

</head>


<body>


<div class="wrapper">


    <?php include("includes/admin_sidebar.php"); ?>


    <div class="main">

        <div class="payment-view-content">


            <!-- ==========================
                 PAGE HEADER
            =========================== -->

            <div class="payment-view-header">


                <div>

                    <h2>
                        Payment Details
                    </h2>

                    <p>
                        View payment transaction information.
                    </p>

                </div>


                <div class="payment-view-actions">


                    <a
                        href="payment_edit_admin.php?id=<?php echo $payment_id; ?>"
                        class="edit-payment-btn"
                    >

                        <i class="fa-solid fa-pen"></i>

                        Edit Payment

                    </a>


                    <a
                        href="payments_admin.php"
                        class="back-staff-btn"
                    >

                        <i class="fa-solid fa-arrow-left"></i>

                        Back to Payments

                    </a>


                </div>


            </div>



            <!-- ==========================
                 PAYMENT CARD
            =========================== -->

            <div class="payment-profile-card">


                <!-- ==========================
                     PAYMENT TOP
                =========================== -->

                <div class="payment-profile-top">


                    <div class="large-payment-icon">

                        <i class="fa-solid fa-receipt"></i>

                    </div>


                    <div class="payment-profile-name">

                        <h1>

                            Payment #<?php
                            echo htmlspecialchars(
                                $payment['payment_id']
                            );
                            ?>

                        </h1>


                        <p>

                            <?php
                            echo htmlspecialchars(
                                $memberName
                            );
                            ?>

                        </p>


                        <span
                            class="payment-status <?php
                                echo htmlspecialchars(
                                    $statusClass
                                );
                            ?>"
                        >

                            <?php
                            echo htmlspecialchars(
                                $payment['payment_status']
                            );
                            ?>

                        </span>

                    </div>


                </div>



                <!-- ==========================
                     PAYMENT INFORMATION
                =========================== -->

                <div class="payment-profile-section">


                    <h2>

                        <i class="fa-solid fa-money-bill-wave"></i>

                        Payment Information

                    </h2>


                    <div class="payment-info-grid">


                        <!-- PAYMENT ID -->

                        <div class="payment-info-item">

                            <span>
                                Payment ID
                            </span>

                            <strong>
                                #<?php
                                echo htmlspecialchars(
                                    $payment['payment_id']
                                );
                                ?>
                            </strong>

                        </div>


                        <!-- MEMBER -->

                        <div class="payment-info-item">

                            <span>
                                Member
                            </span>

                            <strong>

                                <?php
                                echo htmlspecialchars(
                                    $memberName
                                );
                                ?>

                            </strong>

                        </div>


                        <!-- MEMBER ID -->

                        <div class="payment-info-item">

                            <span>
                                Member ID
                            </span>

                            <strong>

                                #<?php
                                echo htmlspecialchars(
                                    $payment['member_id']
                                );
                                ?>

                            </strong>

                        </div>


                        <!-- PLAN -->

                        <div class="payment-info-item">

                            <span>
                                Membership Plan
                            </span>

                            <strong>

                                <?php
                                echo htmlspecialchars(
                                    $payment['plan_name'] ?? '—'
                                );
                                ?>

                            </strong>

                        </div>


                        <!-- AMOUNT -->

                        <div class="payment-info-item">

                            <span>
                                Amount
                            </span>

                            <strong class="payment-amount">

                                ₱<?php
                                echo number_format(
                                    (float)$payment['amount'],
                                    2
                                );
                                ?>

                            </strong>

                        </div>


                        <!-- PAYMENT METHOD -->

                        <div class="payment-info-item">

                            <span>
                                Payment Method
                            </span>

                            <strong>

                                <?php
                                echo htmlspecialchars(
                                    $payment['payment_method']
                                );
                                ?>

                            </strong>

                        </div>


                        <!-- TRANSACTION REFERENCE -->

                        <div class="payment-info-item">

                            <span>
                                Transaction Reference
                            </span>

                            <strong>

                                <?php

                                if (
                                    !empty(
                                        $payment[
                                            'transaction_reference'
                                        ]
                                    )
                                ) {

                                    echo htmlspecialchars(
                                        $payment[
                                            'transaction_reference'
                                        ]
                                    );

                                } else {

                                    echo "—";

                                }

                                ?>

                            </strong>

                        </div>


                        <!-- PAYMENT DATE -->

                        <div class="payment-info-item">

                            <span>
                                Payment Date
                            </span>

                            <strong>

                                <?php

                                if (
                                    !empty(
                                        $payment['payment_date']
                                    )
                                ) {

                                    echo date(
                                        "M d, Y h:i A",
                                        strtotime(
                                            $payment['payment_date']
                                        )
                                    );

                                } else {

                                    echo "—";

                                }

                                ?>

                            </strong>

                        </div>


                    </div>


                </div>



                <!-- ==========================
                     PROOF OF PAYMENT
                =========================== -->

                <div class="payment-profile-section">


                    <h2>

                        <i class="fa-solid fa-file-invoice"></i>

                        Proof of Payment

                    </h2>


                    <?php if (
                        !empty(
                            $payment['proof_of_payment']
                        )
                    ): ?>


                        <div class="payment-proof-container">


                            <?php

                            $proofFile =
                                $payment[
                                    'proof_of_payment'
                                ];

                            $extension =
                                strtolower(
                                    pathinfo(
                                        $proofFile,
                                        PATHINFO_EXTENSION
                                    )
                                );


                            $imageExtensions = [
                                'jpg',
                                'jpeg',
                                'png',
                                'webp'
                            ];

                            ?>


                            <?php if (
                                in_array(
                                    $extension,
                                    $imageExtensions
                                )
                            ): ?>


                                <img
                                    src="<?php
                                        echo htmlspecialchars(
                                            $proofFile
                                        );
                                    ?>"
                                    alt="Proof of Payment"
                                    class="payment-proof-image"
                                >


                            <?php elseif (
                                $extension === 'pdf'
                            ): ?>


                                <div class="payment-proof-pdf">

                                    <i
                                        class="fa-solid fa-file-pdf"
                                    ></i>

                                    <span>
                                        PDF Proof of Payment
                                    </span>

                                    <a
                                        href="<?php
                                            echo htmlspecialchars(
                                                $proofFile
                                            );
                                        ?>"
                                        target="_blank"
                                        class="payment-proof-btn"
                                    >

                                        <i
                                            class="fa-solid fa-eye"
                                        ></i>

                                        View PDF

                                    </a>

                                </div>


                            <?php else: ?>


                                <div class="payment-proof-pdf">

                                    <i
                                        class="fa-solid fa-file"
                                    ></i>

                                    <span>
                                        Payment proof file
                                    </span>

                                    <a
                                        href="<?php
                                            echo htmlspecialchars(
                                                $proofFile
                                            );
                                        ?>"
                                        target="_blank"
                                        class="payment-proof-btn"
                                    >

                                        <i
                                            class="fa-solid fa-download"
                                        ></i>

                                        Open File

                                    </a>

                                </div>


                            <?php endif; ?>


                        </div>


                    <?php else: ?>


                        <div class="no-payment-proof">

                            <i
                                class="fa-solid fa-file-circle-xmark"
                            ></i>

                            <p>
                                No proof of payment was uploaded.
                            </p>

                        </div>


                    <?php endif; ?>


                </div>


            </div>


        </div>


    </div>


</div>


</body>

</html>