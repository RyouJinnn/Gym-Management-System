<?php
session_start();
include("connect.php");

if(!isset($_SESSION['email'])){
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];

$stmt = $con->prepare("SELECT id, first_name, last_name FROM signup WHERE email=?");
$stmt->bind_param("s",$email);
$stmt->execute();

$user = $stmt->get_result()->fetch_assoc();

$memberID = $user['id'];
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Payment History</title>

<link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"/>

<style>

*{
margin:0;
padding:0;
box-sizing:border-box;
font-family:'Poppins',sans-serif;
}

body{
background:#070707;
color:#fff;
padding:40px;
}

.container{
max-width:1200px;
margin:auto;
}

.back-btn{
display:inline-flex;
align-items:center;
justify-content:center;
width:40px;
height:40px;
border:2px solid #39ff14;
border-radius:50%;
color:#39ff14;
text-decoration:none;
font-size:15px;
margin-bottom:25px;
transition:.3s;
}

.back-btn:hover{
background:#39ff14;
color:#000;
}

h1{
display:flex;
align-items:center;
gap:15px;
font-size:30px;
margin-bottom:30px;
color:#39ff14;
font-family:'Orbitron',sans-serif;
}

.top-bar{

display:flex;
justify-content:flex-end;
margin-bottom:35px;

}

.filter-box{

display:flex;
align-items:center;
gap:15px;

}

.filter-box label{

font-size:16px;
font-weight:600;
color:#39ff14;

}

.filter-box select{

width:220px;
height:50px;
background:#111;
color:white;
border:2px solid #262626;
border-radius:10px;
padding:0 15px;
font-size:15px;
cursor:pointer;
transition:.3s;

}

.filter-box select:hover,
.filter-box select:focus{

border-color:#39ff14;
outline:none;

}

.payment-card{

background:#111;
border:1px solid #262626;
border-radius:18px;
padding:25px;
margin-bottom:25px;
transition:.3s;

}

.payment-card:hover{

border-color:#39ff14;
box-shadow:0 10px 30px rgba(57,255,20,.18);

}

.card-top{

display:flex;
justify-content:space-between;
align-items:center;
margin-bottom:20px;

}

.payment-id{

font-size:20px;
font-weight:700;

}

.badge{

padding:8px 18px;
border-radius:50px;
font-weight:600;
font-size:14px;

}

.approved{

background:#39ff14;
color:#111;
font-weight:700;

}

.pending{

background:#facc15;
color:#111;

}

.card-grid{

display:grid;
grid-template-columns:repeat(2,1fr);
gap:15px;
margin-bottom:25px;

}

.item{

background:#181818;
padding:15px;
border-radius:10px;

}

.item h4{

color:#888;
font-size:13px;
margin-bottom:6px;

}

.item p{

font-size:18px;
font-weight:600;

}

.view-btn{

width:210px;
height:50px;
border:none;
border-radius:10px;
background:#39ff14;
color:black;
font-size:15px;
font-weight:700;
cursor:pointer;
transition:.3s;

}

.view-btn:hover{

transform:scale(1.04);

}

.receipt-modal{

position:fixed;
inset:0;
background:rgba(0,0,0,.75);

display:none;

justify-content:center;
align-items:center;

z-index:9999;

}

.receipt-content{

width:760px;
max-width:90%;
max-height:90vh;

overflow-y:auto;

background:#111;
border:2px solid #39ff14;
border-radius:20px;

padding:30px;

position:relative;

animation:popup .25s ease;

}

@keyframes popup{

from{

transform:scale(.9);
opacity:0;

}

to{

transform:scale(1);
opacity:1;

}

}

.close-modal{

position:absolute;

top:20px;
right:25px;

font-size:32px;

cursor:pointer;

color:#39ff14;

}

.receipt-grid{

display:grid;

grid-template-columns:repeat(2,1fr);

gap:12px;

margin:20px 0;

}

.receipt-grid div{

background:#181818;

padding:12px 15px;

border-radius:10px;

min-height:75px;

}

.receipt-grid strong{

color:#39ff14;

display:block;

margin-bottom:5px;

}

.receipt-image{

display:block;

width:220px;
height:220px;

margin:20px auto;

object-fit:contain;

background:#181818;

padding:8px;

border:2px solid #39ff14;

border-radius:12px;

cursor:zoom-in;

transition:.3s;

}

.receipt-image:hover{

transform:scale(1.02);

}

@media(max-width:700px){

.card-grid{

grid-template-columns:1fr;

}

.top-bar{

justify-content:center;

}

.filter-box{

flex-direction:column;
align-items:stretch;

}

.filter-box select{

width:100%;

}

.view-btn{

width:100%;

}

}

</style>

</head>
<body>

<a href="dashboard.php" class="back-btn">

<i class="fa-solid fa-arrow-left"></i>

</a>

<div class="container">

<h1>

<i class="fa-solid fa-clock-rotate-left"></i>

Payment History

</h1>

<div class="top-bar">

<div class="filter-box">

<label for="statusFilter">

<i class="fa-solid fa-filter"></i>

Filter

</label>

<select id="statusFilter">

<option value="all">All</option>

<option value="approved">Approved</option>

<option value="pending">Pending</option>

</select>

</div>

</div>

<div id="paymentList">

<?php

$stmt = $con->prepare("
SELECT
    payments.*,
    membership_plans.plan_name
FROM payments
LEFT JOIN membership_plans
ON payments.plan_id = membership_plans.plan_id
WHERE payments.member_id = ?
ORDER BY payments.payment_date DESC
");

$stmt->bind_param("i",$memberID);
$stmt->execute();

$result = $stmt->get_result();

if($result->num_rows > 0){

while($payment = $result->fetch_assoc()){

$status = strtolower($payment['payment_status']);

if($status=="approved"){

    $badgeClass="approved";
    $badgeText="Approved";

}else{

    $badgeClass="pending";
    $badgeText="Pending";

}

?>

<div class="payment-card">

<div class="card-top">

<div class="payment-id">

Payment #<?php echo str_pad($payment['payment_id'],5,"0",STR_PAD_LEFT); ?>

</div>

<div class="badge <?php echo $badgeClass; ?>">

<?php echo $badgeText; ?>

</div>

</div>

<div class="card-grid">

<div class="item">

<h4>Membership</h4>

<p><?php echo htmlspecialchars($payment['plan_name']); ?></p>

</div>

<div class="item">

<h4>Amount</h4>

<p>₱<?php echo number_format($payment['amount'],2); ?></p>

</div>

<div class="item">

<h4>Payment Method</h4>

<p>

<?php

echo !empty($payment['payment_method'])
? htmlspecialchars($payment['payment_method'])
: "-";

?>

</p>

</div>

<div class="item">

<h4>Date</h4>

<p><?php echo date("F d, Y",strtotime($payment['payment_date'])); ?></p>

</div>

</div>

<button
class="view-btn"
data-image="<?php echo htmlspecialchars($payment['proof_of_payment']); ?>"
data-reference="<?php echo htmlspecialchars($payment['transaction_reference']); ?>"
data-method="<?php echo htmlspecialchars($payment['payment_method']); ?>"
data-status="<?php echo $badgeText; ?>"
data-plan="<?php echo htmlspecialchars($payment['plan_name']); ?>"
data-amount="₱<?php echo number_format($payment['amount'],2); ?>">

<i class="fa-solid fa-receipt"></i>

View Receipt

</button>

</div>

<?php

}

}else{

?>

<div style="text-align:center;padding:90px;">

<i class="fa-solid fa-wallet"
style="font-size:70px;color:#39ff14;"></i>

<h2 style="margin-top:20px;">

No Payment History

</h2>

<p style="margin-top:10px;color:#999;">

Your completed payments will appear here.

</p>

</div>

<?php

}

?>

</div>

<div class="receipt-modal" id="receiptModal">

    <div class="receipt-content">

        <span class="close-modal">&times;</span>

        <h2>
            <i class="fa-solid fa-receipt"></i>
            Payment Receipt
        </h2>

        <div class="receipt-grid">

            <div>
                <strong>Membership</strong>
                <p id="receiptPlan"></p>
            </div>

            <div>
                <strong>Amount</strong>
                <p id="receiptAmount"></p>
            </div>

            <div>
                <strong>Payment Method</strong>
                <p id="receiptMethod"></p>
            </div>

            <div>
                <strong>Reference Number</strong>
                <p id="receiptReference"></p>
            </div>

            <div>
                <strong>Status</strong>
                <p id="receiptStatus"></p>
            </div>

        </div>

        <h3 style="margin:15px 0 10px;">
            Proof of Payment
        </h3>

        <img
        id="receiptImage"
        src=""
        class="receipt-image">

    </div>

</div>

<script>

const modal = document.getElementById("receiptModal");

document.querySelectorAll(".view-btn").forEach(button=>{

button.onclick=function(){

document.getElementById("receiptPlan").innerHTML=
this.dataset.plan;

document.getElementById("receiptAmount").innerHTML=
this.dataset.amount;

document.getElementById("receiptMethod").innerHTML=
this.dataset.method;

document.getElementById("receiptReference").innerHTML=
this.dataset.reference || "-";

document.getElementById("receiptStatus").innerHTML=
this.dataset.status;

const receiptImage=document.getElementById("receiptImage");

if(this.dataset.image!=""){

receiptImage.src=this.dataset.image;

}else{

receiptImage.src="no_receipt.png";

}

modal.style.display="flex";

}

});

document.querySelector(".close-modal").onclick=function(){

modal.style.display="none";

}

window.onclick=function(e){

if(e.target==modal){

modal.style.display="none";

}

}

</script>

</body>

</html>