<?php
session_start();
include("connect.php");

/* ======================================================
   LOGIN CHECK
====================================================== */

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];

/* ======================================================
   GET LOGGED IN USER
====================================================== */

$stmt = $con->prepare("
SELECT *
FROM signup
WHERE email=?
LIMIT 1
");

$stmt->bind_param("s",$email);
$stmt->execute();

$user = $stmt->get_result()->fetch_assoc();

$stmt->close();

if(!$user){

    session_destroy();

    header("Location: login.php");

    exit();

}

/* ======================================================
   CHECK ACTIVE MEMBERSHIP
====================================================== */

$stmt = $con->prepare("
SELECT membership_id
FROM membership
WHERE member_id=?
AND status='Active'
AND end_date>=CURDATE()
LIMIT 1
");

$stmt->bind_param("i",$user['id']);
$stmt->execute();

$result = $stmt->get_result();

if($result->num_rows>0){

    header("Location: membership_db.php");

    exit();

}

$stmt->close();

/* ======================================================
   GET SELECTED PLAN
====================================================== */

if(!isset($_GET['plan'])){

    header("Location: membership_db.php");

    exit();

}

$planId = (int)$_GET['plan'];

$stmt = $con->prepare("
SELECT *
FROM membership_plans
WHERE plan_id=?
AND status='Active'
LIMIT 1
");

$stmt->bind_param("i",$planId);

$stmt->execute();

$result = $stmt->get_result();

if($result->num_rows==0){

    header("Location: membership_db.php");

    exit();

}

$selectedPlan = $result->fetch_assoc();

$stmt->close();

$planId = isset($_GET['plan']) ? (int)$_GET['plan'] : 0;

$stmt = $con->prepare("
    SELECT
        plan_id,
        plan_name,
        price,
        duration_days
    FROM membership_plans
    WHERE plan_id = ?
    AND status = 'Active'
    LIMIT 1
");

if (!$stmt) {
    die("SQL Error: " . $con->error);
}

$stmt->bind_param("i", $planId);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $stmt->close();
    header("Location: membership_db.php");
    exit();
}

$selectedPlan = $result->fetch_assoc();

$stmt->close();

/* ==========================
   MEMBERSHIP SUMMARY
========================== */

$planName = $selectedPlan['plan_name'];
$price = (float)$selectedPlan['price'];
$duration = (int)$selectedPlan['duration_days'];

$startDate = date("Y-m-d");

$endDate = date(
    "Y-m-d",
    strtotime("+{$duration} days")
);


/* ==========================
   PROCESS PAYMENT
========================== */

if (isset($_POST['pay_now'])) {

    $paymentMethod = trim($_POST['payment_method'] ?? '');

    $allowedMethods = [
    "Credit Card",
    "GCash",
    "Pay at Counter"
];
    if (!in_array($paymentMethod, $allowedMethods, true)) {
        die("Invalid payment method.");
    }

    /*
    ==========================================
    GET THE SELECTED MEMBERSHIP PLAN
    ==========================================
    */

    $planId = (int)($_GET['plan'] ?? 0);

    $planStmt = $con->prepare("
        SELECT
            plan_id,
            plan_name,
            price,
            duration_days
        FROM membership_plans
        WHERE plan_id = ?
        AND status = 'Active'
        LIMIT 1
    ");

    if (!$planStmt) {
        die("SQL Error: " . $con->error);
    }

    $planStmt->bind_param("i", $planId);
    $planStmt->execute();

    $planResult = $planStmt->get_result();

    if ($planResult->num_rows === 0) {
        $planStmt->close();
        die("Membership plan not found.");
    }

    $plan = $planResult->fetch_assoc();

    $planStmt->close();


    /*
    ==========================================
    PLAN INFORMATION
    ==========================================
    */

    $planId       = (int)$plan['plan_id'];
    $planName     = $plan['plan_name'];
    $price        = (float)$plan['price'];
    $durationDays = (int)$plan['duration_days'];


    /*
    ==========================================
    PAYMENT STATUS
    ==========================================
    */

    if ($paymentMethod === "Pay at Counter") {
        $paymentStatus = "Pending";
    } else {
        $paymentStatus = "Approved";
    }


    /*
    ==========================================
    TRANSACTION REFERENCE
    ==========================================
    */

    $transactionReference =
        "FFG-" . date("YmdHis") . rand(1000, 9999);


    /*
    ==========================================
    PROOF OF PAYMENT
    ==========================================
    */

    $proofOfPayment = null;


    /*
    ==========================================
    SAVE PAYMENT
    ==========================================
    */

    $paymentStmt = $con->prepare("
        INSERT INTO payments
        (
            member_id,
            plan_id,
            amount,
            payment_method,
            payment_status,
            transaction_reference,
            proof_of_payment
        )
        VALUES
        (?, ?, ?, ?, ?, ?, ?)
    ");

    if (!$paymentStmt) {
        die("SQL Error: " . $con->error);
    }

    $paymentStmt->bind_param(
        "iidssss",
        $user['id'],
        $planId,
        $price,
        $paymentMethod,
        $paymentStatus,
        $transactionReference,
        $proofOfPayment
    );

    if (!$paymentStmt->execute()) {
        die("Failed to save payment: " . $paymentStmt->error);
    }

    $paymentId = $paymentStmt->insert_id;

    $paymentStmt->close();


    /*
    ==========================================
    CREATE MEMBERSHIP
    ==========================================
    */

    if ($paymentStatus === "Approved") {

        $startDate = date("Y-m-d");

        $endDate = date(
            "Y-m-d",
            strtotime("+{$durationDays} days")
        );

        $membershipStatus = "Active";

        $membershipStmt = $con->prepare("
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
            (?, ?, ?, ?, ?, ?, ?, ?)
        ");

        if (!$membershipStmt) {
            die("Membership SQL Error: " . $con->error);
        }

        $membershipStmt->bind_param(
            "iisdisss",
            $user['id'],
            $planId,
            $planName,
            $price,
            $durationDays,
            $startDate,
            $endDate,
            $membershipStatus
        );

        if (!$membershipStmt->execute()) {
            die("Failed to create membership: " . $membershipStmt->error);
        }

        $membershipStmt->close();
    }


    /*
    ==========================================
    GO TO RECEIPT
    ==========================================
    */

   header(
    "Location: receipt.php?payment_id=" . $paymentId
);

exit;
}

?>

<?php if (isset($_GET['success']) && $_GET['success'] == '1'): ?>

<div class="member-success">
    <i class="fa-solid fa-circle-check"></i>

    <?php if (isset($_GET['status']) && $_GET['status'] === 'Pending'): ?>
        Payment submitted successfully. Your payment is pending.
    <?php else: ?>
        Payment successful!
    <?php endif; ?>

</div>

<?php endif; ?>


<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
    content="width=device-width, initial-scale=1.0">
<title>Fit Function Gym | Payment</title>
<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap"rel="stylesheet">
<!-- Font Awesome -->
<link rel="stylesheet"href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
<style>

*{
margin:0;
padding:0;
box-sizing:border-box;
font-family:'Poppins',sans-serif;
}

body{
    background:#070707;
color:white;
overflow-x:hidden;
    display:flex;

    justify-content:center;

    align-items:flex-start;

    min-height:100vh;

    padding:10px 0;

}

a{
text-decoration:none;
color:white;
}

.wrapper{
display:flex;
min-height:100vh;
}

.dashboard-content{
    width:100%;
    max-width:1250px;
    margin:0 auto;
}

.main{
    width:100%;
    margin-left:0;
    padding:25px;
    display:flex;
    flex-direction:column;
    align-items:center;
}

.user{
display:flex;
align-items:center;
gap:15px;

}

.avatar{
width:55px;
height:55px;
border-radius:50%;
border:2px solid #39ff14;
display:flex;
justify-content:center;
align-items:center;
font-size:22px;
color:#39ff14;
}

.user h4{
font-size:20px;
}

.user small{
color:#39ff14;
}

.sidebar-user{
    display:flex;
    align-items:center;
    gap:12px;
    margin-bottom:25px;
    padding-bottom:20px;
    border-bottom:1px solid #222;
}

.user-info h4{
    font-size:15px;
    margin-bottom:2px;
}

.user-info small{
    font-size:12px;
    color:#39ff14;
}

.floating-menu{
        position:fixed;
        top:18px;
        left:18px;
        width:auto;
        height:auto;
        padding:0;
        border:none;
        outline:none;
        background:transparent;
        color:#39ff14;
        font-size:24px;
        cursor:pointer;
        z-index:1200;
        display:flex;
        align-items:center;
        justify-content:center;
        transition:.3s;
        box-shadow:none;
    }

.floating-menu:hover{
    background:transparent;
    color:#66ff4d;
    transform:scale(1.1);
}

.section-title{
    width:100%;
    font-size:26px;
    margin:15px 0 10px;
}

.cards{
    display:grid;
    grid-template-columns:repeat(3, 1fr);
    gap:30px;
    margin-top:35px;
}

.card{
    background:#111;
    border:1px solid #262626;
    border-radius:20px;
    padding:28px;
    display:flex;
    flex-direction:column;
    text-align:center;
}

.card-icon{
    width:82px;
    height:82px;
    border:2px solid #39ff14;
    border-radius:50%;
    display:flex;
    justify-content:center;
    align-items:center;
    margin:0 auto 20px;
    font-size:30px;
    color:#39ff14;
}

.card h3{
    font-size:20px;
    margin-bottom:14px;
}

.card p{
    font-size:14px;
    line-height:1.8;
    color:#ccc;
    margin-bottom:22px;
}

.card button{
    width:130px;
    height:48px;
    margin:auto auto 0;
    border:2px solid #39ff14;
    border-radius:10px;
    background:transparent;
    color:#39ff14;
    font-size:14px;
    font-weight:600;
    cursor:pointer;
    transition:.3s;
}

.card button:hover{
background:#39ff14;
color:#000;
}

.octagon-icon{
width:95px;
height:95px;
border:2px solid #39ff14;
clip-path:polygon(30% 0%,70% 0%,100% 30%,100% 70%,70% 100%,30% 100%,0% 70%,0% 30%);
display:flex;
justify-content:center;
align-items:center;
margin:auto;
margin-bottom:22px;
color:#39ff14;
font-size:42px;
}

@media(max-width:1200px){
    .cards{
        grid-template-columns:repeat(2, 1fr);
    }
}
@media(max-width:768px){
    .cards{
        grid-template-columns:1fr;
    }
}

.status{
flex-direction:column;
align-items:flex-start;
}

@media(max-width:900px){

.sidebar{
left:-260px;
}

.main{
    width:100%;
    margin-left:0;
    padding:35px;
    transition:.35s;
}

.status{
padding:25px;
}

.status-left{
flex-direction:column;
align-items:flex-start;
}
}

.summary-row{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:18px;
    font-size:15px;
}

.summary-row.total{
    border-top:1px solid #2a2a2a;
    margin-top:20px;
    padding-top:20px;
    font-size:18px;
    font-weight:600;
}

.summary-row.total span:last-child{
    color:#39ff14;
}

.pay-btn{
    width:100%;
    height:55px;
    border:none;
    border-radius:12px;
    background:#39ff14;
    color:#000;
    font-size:16px;
    font-weight:700;
    cursor:pointer;
    transition:.3s;
    margin-top:10px;
}

.pay-btn:hover{
    transform:translateY(-2px);
    box-shadow:0 0 20px rgba(57,255,20,.35);
}

.container{
    width:100%;
    max-width:1450px;
    margin:10px; /* push everything below the back button */
    padding:0 60px;
}

.payment-wrapper{

    display:grid;
    grid-template-columns:380px 1fr;
    gap:25px;
    align-items:start;

}

.plan-summary{

    background:#111;
    border:1px solid #2b2b2b;
    border-radius:15px;
    padding:25px;
    align-self:start;
    height:fit-content;

}

.payment-form{

    background:#111;

    border:1px solid #2b2b2b;

    border-radius:15px;

    padding:30px;

}

.plan-summary h2,
.payment-form h2{

    color:#39ff14;

    margin-bottom:25px;

}

.summary-row{

    display:flex;

    justify-content:space-between;

    padding:15px 0;

    border-bottom:1px solid #2b2b2b;

}

.method-list{

    display:flex;

    flex-direction:column;

    gap:18px;

}

.method-card{

    border:2px solid #2b2b2b;

    border-radius:12px;

    padding:18px;

    cursor:pointer;

    transition:.3s;

}

.method-card:hover{

    border-color:#39ff14;

}

.method-card input{

    margin-right:12px;

}

#uploadSection{

    margin-top:30px;

}

#uploadSection input{

    width:100%;

    margin-top:10px;

}

.pay-btn{

    width:100%;

    height:55px;

    border:none;

    background:#39ff14;

    color:#000;

    font-size:17px;

    font-weight:700;

    border-radius:10px;

    cursor:pointer;

}

.pay-btn:hover{

    background:#2edc0e;

}

.back-btn{
    position:fixed;
    top:18px;
    left:18px;
    width:36px;
    height:36px;
    display:flex;
    justify-content:center;
    align-items:center;
    border-radius:50%;
    background:#111;
    border:2px solid #39ff14;
    color:#39ff14;
    font-size:16px;
    z-index:9999;
}
.back-btn:hover{

    background:#39ff14;

    color:#000;

    transform:scale(1.08);

}

/* ============================= */
/* PAYMENT OPTIONS */
/* ============================= */

.payment-options{

    display:grid;

    grid-template-columns:repeat(3,1fr);

    gap:18px;

    margin:25px 0;

}

.payment-card{

    background:#1a1a1a;

    border:2px solid #2d2d2d;

    border-radius:14px;

    padding:14px;

    cursor:pointer;

    text-align:center;

    transition:.3s;

    user-select:none;

}

.payment-card input{

    display:none;

}

.payment-card i{

    font-size:28px;

    color:#39ff14;

    margin-bottom:12px;

    display:block;

}

.payment-card span{

    font-weight:600;

}

.payment-card:hover{

    border-color:#39ff14;

    transform:translateY(-3px);

}

.payment-card.active{

    border-color:#39ff14;

    background:#162516;

}

/* ============================= */
/* PAYMENT SECTION */
/* ============================= */

.payment-section{

    background:#181818;
    border:1px solid #2d2d2d;
    border-radius:15px;
    padding:20px;
    margin-top:10px;
    margin-bottom:18px;

}

.payment-section h3{

    color:#39ff14;

    margin-bottom:20px;

}

.payment-section p{

    color:#ccc;

    line-height:1.7;

    margin-bottom:15px;

}

.payment-section label{

    display:block;

    margin-top:18px;

    margin-bottom:8px;

    font-weight:600;

}

.payment-section input{

    width:100%;

    height:48px;

    background:#0f0f0f;

    color:white;

    border:1px solid #333;

    border-radius:10px;

    padding:0 15px;

    margin-bottom:15px;

    outline:none;

}

.payment-section input:focus{

    border-color:#39ff14;

}

/* ============================= */
/* CARD ROW */
/* ============================= */

.card-row{

    display:grid;

    grid-template-columns:1fr 120px;

    gap:15px;

}

/* ============================= */
/* QR CODE */
/* ============================= */

.qr-image{

    width:180px;

    height:180px;

    object-fit:contain;

    display:block;

    margin:15px auto;

    background:white;

    border-radius:10px;

    padding:8px;

}

/* ============================= */
/* PAY BUTTON */
/* ============================= */

.pay-btn{

    width:100%;

    height:56px;

    background:#39ff14;

    color:#000;

    border:none;

    border-radius:12px;

    font-size:17px;

    font-weight:700;

    cursor:pointer;

    transition:.3s;

}

.pay-btn:hover{

    background:#57ff3d;

}

.upload-box{
    width:100%;
    border:2px dashed #39ff14;
    border-radius:15px;
    padding:20px 10px;
    text-align:center;
    cursor:pointer;
    background:#151515;
    transition:.3s;
    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:center;
    gap:10px;
}

.upload-box:hover{
    background:#1d1d1d;
    border-color:#66ff4d;
}

.upload-box i{
    font-size:30px;
    color:#39ff14;
}

.upload-box span{
    font-size:14px;
    font-weight:600;
    color:#fff;
}

.upload-box small{
    color:#9d9d9d;
    font-size:13px;
}

.preview-image{
    width:220px;
    max-height:220px;
    object-fit:cover;
    border-radius:12px;
    border:2px solid #39ff14;
    margin:20px auto 0;
    display:block;
}

/* ============================= */
/* RESPONSIVE */
/* ============================= */

@media(max-width:768px){

    .payment-options{

        grid-template-columns:1fr;

    }

    .card-row{

        grid-template-columns:1fr;

    }

}

@media(max-width:1000px){

    .payment-wrapper{
        grid-template-columns:1fr;
    }

}
</style>

</head>

<body>

    <a href="membership_db.php" class="back-btn">
    <i class="fa-solid fa-arrow-left"></i>
</a>

<div class="container">

    <div class="payment-wrapper">

        <!-- LEFT SIDE -->

        <div class="plan-summary">

            <h2>Membership Summary</h2>

            <div class="summary-box">

                <div class="summary-row">

                    <span>Membership</span>

                    <strong>
                        <?php echo htmlspecialchars($planName); ?>
                    </strong>

                </div>

                <div class="summary-row">

                    <span>Price</span>

                    <strong>
                        ₱<?php echo number_format($price,2); ?>
                    </strong>

                </div>

                <div class="summary-row">

                    <span>Duration</span>

                    <strong>

                        <?php

                        if($duration==1){

                            echo "1 Day";

                        }else{

                            echo $duration." Days";

                        }

                        ?>

                    </strong>

                </div>

                <div class="summary-row">

                    <span>Start Date</span>

                    <strong>

                        <?php echo $startDate; ?>

                    </strong>

                </div>

                <div class="summary-row">

                    <span>End Date</span>

                    <strong>

                        <?php echo $endDate; ?>

                    </strong>

                </div>

            </div>

        </div>

        <!-- RIGHT SIDE -->

        <div class="payment-form">

    <h2>Select Payment Method</h2>

    <form method="POST" enctype="multipart/form-data">

        <input
            type="hidden"
            name="plan_id"
            value="<?php echo $selectedPlan['plan_id']; ?>">

        <input
            type="hidden"
            name="amount"
            value="<?php echo $price; ?>">

        <!-- PAYMENT METHODS -->

        <div class="payment-options">

            <label class="payment-card active" data-method="gcash">

                <input
                    type="radio"
                    name="payment_method"
                    value="GCash"
                    checked>

                <i class="fa-solid fa-wallet"></i>

                <span>GCash</span>

            </label>

            <label class="payment-card" data-method="card">

                <input
                    type="radio"
                    name="payment_method"
                    value="Credit Card">

                <i class="fa-solid fa-credit-card"></i>

                <span>Credit Card</span>

            </label>

            <label class="payment-card" data-method="counter">

                <input
                    type="radio"
                    name="payment_method"
                    value="Pay at Counter">

                <i class="fa-solid fa-building"></i>

                <span>Pay at Counter</span>

            </label>

        </div>

        <!-- ========================= -->
        <!-- GCASH -->
        <!-- ========================= -->

        <div id="gcashSection" class="payment-section">

            <h3>GCash Payment</h3>

            <img
                src="gcash_ni_ivan.jpg"
                class="qr-image"
                alt="GCash QR">

            <p>

                <strong>GCash Number</strong><br>

                0956-011-4697

            </p>

            <p>

                <strong>Account Name</strong><br>

                Fit Function Gym

            </p>

            <label>

                Upload Proof of Payment

            </label>

            <input
    type="file"
    id="proof"
    name="proof"
    accept="image/*"
    hidden
>

<label for="proof" class="upload-box">

    <i class="fa-solid fa-cloud-arrow-up"></i>

    <span id="uploadText">
        Click to upload your payment proof
    </span>

    <small>JPG, PNG or JPEG</small>

</label>

<img id="previewImage" class="preview-image" style="display:none;">

        </div>

        <!-- ========================= -->
        <!-- CREDIT CARD -->
        <!-- ========================= -->

        <div id="cardSection"
             class="payment-section"
             style="display:none;">

            <h3>Credit Card</h3>

            <input
                type="text"
                name="card_name"
                placeholder="Cardholder Name"
                >

            <input
                type="text"
                name="card_number"
                placeholder="Card Number"
                maxlength="19"
                >

            <div class="card-row">

                <input
                    type="text"
                    name="expiry"
                    placeholder="MM / YY"
                    maxlength="5"
                    >

                <input
                    type="password"
                    name="cvv"
                    placeholder="CVV"
                    maxlength="4"
                    >
            </div>
        </div>

        <div id="counterSection"
             class="payment-section"
             style="display:none;">

            <h3>Pay at the Counter</h3>

<p>
    Complete your membership payment at the Fit Function Gym reception desk.
</p>

<p>
    Our receptionist will verify your payment and activate your membership after it has been received.
</p>

<p style="color:#39ff14; font-weight:600;">
    Please proceed to the reception to continue.
</p>

        </div>

        <button
    id="payButton"
    class="pay-btn"
    type="submit"
    name="pay_now">

    Pay Now

</button>

    </form>

</div>


<script>

const paymentCards = document.querySelectorAll(".payment-card");

const gcashSection = document.getElementById("gcashSection");
const cardSection = document.getElementById("cardSection");
const counterSection = document.getElementById("counterSection");
const payButton = document.getElementById("payButton");

paymentCards.forEach(card=>{

    card.addEventListener("click",function(){

        paymentCards.forEach(c=>c.classList.remove("active"));

        this.classList.add("active");

        const method=this.dataset.method;

        gcashSection.style.display="none";
        cardSection.style.display="none";
        counterSection.style.display="none";

        proofInput.required=false;

        if(method==="gcash"){

            gcashSection.style.display="block";

            proofInput.required=true;

        }

        if(method==="card"){

            cardSection.style.display="block";

        }

        if(method==="counter"){

    counterSection.style.display="block";

    payButton.style.display="none";

}else{

    payButton.style.display="block";

}

    });

});

/* Credit Card Formatting */

const cardNumber=document.querySelector('input[name="card_number"]');

if(cardNumber){

cardNumber.addEventListener("input",function(){

    let value=this.value.replace(/\D/g,"");

    value=value.substring(0,16);

    value=value.replace(/(.{4})/g,"$1 ").trim();

    this.value=value;

});

}

/* Expiry MM/YY */

const expiry=document.querySelector('input[name="expiry"]');

if(expiry){

expiry.addEventListener("input",function(){

    let value=this.value.replace(/\D/g,"");

    value=value.substring(0,4);

    if(value.length>2){

        value=value.substring(0,2)+"/"+value.substring(2);

    }

    this.value=value;

});

}

/* CVV */

const cvv=document.querySelector('input[name="cvv"]');

if(cvv){

cvv.addEventListener("input",function(){

    this.value=this.value.replace(/\D/g,"");

});

}

const proofInput = document.getElementById("proof");
const uploadText = document.getElementById("uploadText");
const previewImage = document.getElementById("previewImage");

proofInput.addEventListener("change", function(){

    if(this.files.length > 0){

        const file = this.files[0];

        uploadText.innerHTML =
            '<i class="fa-solid fa-circle-check" style="color:#39ff14;"></i> '
            + file.name;

        const reader = new FileReader();

        reader.onload = function(e){

            previewImage.src = e.target.result;
            previewImage.style.display = "block";

        };

        reader.readAsDataURL(file);

    }

});

</script>

</body>
</html>