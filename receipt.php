<?php
session_start();
include("connect.php");

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['payment_id'])) {
    header("Location: dashboard.php");
    exit();
}

$payment_id = (int)$_GET['payment_id'];

$stmt = $con->prepare("
SELECT
    p.*,
    s.first_name,
    s.last_name,
    mp.plan_name
FROM payments p
JOIN signup s
    ON p.member_id = s.id
JOIN membership_plans mp
    ON p.plan_id = mp.plan_id
WHERE p.payment_id=?
LIMIT 1
");

$stmt->bind_param("i",$payment_id);
$stmt->execute();

$result = $stmt->get_result();

if($result->num_rows==0){

    die("Receipt not found.");

}

$receipt = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Payment Receipt</title>

<link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>

*{
margin:0;
padding:0;
box-sizing:border-box;
font-family:'Poppins',sans-serif;
}

body{
background:#0b0b0b;
display:flex;
justify-content:center;
align-items:center;
min-height:100vh;
padding:40px;
}

.receipt{
width:700px;
background:#111;
border:2px solid #39ff14;
border-radius:18px;
padding:40px;
color:white;
}

.header{
text-align:center;
margin-bottom:30px;
}

.header img{
width:90px;
height:90px;
border-radius:50%;
margin-bottom:10px;
}

.header h1{
font-family:'Orbitron',sans-serif;
font-size:30px;
color:#39ff14;
}

.header p{
color:#bbb;
margin-top:8px;
}

.line{
height:2px;
background:#39ff14;
margin:25px 0;
}

.row{
display:flex;
justify-content:space-between;
margin:16px 0;
font-size:16px;
}

.label{
font-weight:600;
color:#bdbdbd;
}

.value{
font-weight:700;
}

.amount{
text-align:center;
margin:35px 0;
}

.amount h2{
font-size:40px;
color:#39ff14;
}

.buttons{
display:flex;
justify-content:center;
gap:20px;
margin-top:35px;
}

.buttons button,
.buttons a{

width:180px;
height:50px;

border:none;
border-radius:10px;

font-size:15px;
font-weight:600;

cursor:pointer;
text-decoration:none;

display:flex;
justify-content:center;
align-items:center;

}

.print{
background:#39ff14;
color:black;
}

.print:hover{
background:#5cff3d;
}

.home{
background:#222;
color:white;
}

.home:hover{
background:#333;
}

@media print{

.buttons{
display:none;
}

body{
background:white;
padding:0;
}

.receipt{
border:none;
width:100%;
}

}

</style>

</head>

<body>

<div class="receipt">

<div class="header">

<img src="logofit.png">

<h1>FIT FUNCTION GYM</h1>

<p>Official Payment Receipt</p>

</div>

<div class="line"></div>

<div class="row">
<div class="label">Receipt No.</div>
<div class="value">#<?= $receipt['payment_id']; ?></div>
</div>

<div class="row">
<div class="label">Member</div>
<div class="value">
<?= htmlspecialchars($receipt['first_name']." ".$receipt['last_name']); ?>
</div>
</div>

<div class="row">
<div class="label">Membership Plan</div>
<div class="value">
<?= htmlspecialchars($receipt['plan_name']); ?>
</div>
</div>

<div class="row">
<div class="label">Payment Method</div>
<div class="value">
<?= htmlspecialchars($receipt['payment_method']); ?>
</div>
</div>

<div class="row">
<div class="label">Transaction Reference</div>
<div class="value">
<?= htmlspecialchars($receipt['transaction_reference']); ?>
</div>
</div>

<div class="row">
<div class="label">Payment Date</div>
<div class="value">
<?= date("F d, Y h:i A", strtotime($receipt['payment_date'])); ?>
</div>
</div>

<div class="line"></div>

<div class="amount">

<h3>Amount Paid</h3>

<h2>
₱<?= number_format($receipt['amount'],2); ?>
</h2>

</div>

<div class="buttons">

<button
class="print"
onclick="window.print()">

Print / Save Receipt

</button>

<a
class="home"
href="dashboard.php">

Back to Dashboard

</a>

</div>

</div>

</body>
</html>