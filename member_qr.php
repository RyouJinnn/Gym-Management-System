<?php
session_start();
include("connect.php");

if(!isset($_SESSION['email'])){
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];

$stmt = $con->prepare("
SELECT *
FROM signup
WHERE email=?
LIMIT 1
");

$stmt->bind_param("s",$email);
$stmt->execute();

$user = $stmt->get_result()->fetch_assoc();

$userID = $user['id'];

$memberCode = "M".str_pad($userID,5,"0",STR_PAD_LEFT);

$qrFile = "qrcodes/".$memberCode.".png";
?>

<!DOCTYPE html>
<html>

<head>

<title>Member QR Code</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

<link rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

<style>

body{

margin:0;

background:#000;

display:flex;

justify-content:center;

align-items:center;

height:100vh;

font-family:Poppins,sans-serif;

color:white;

}

.card{

text-align:center;

}

.card img{

width:320px;

background:white;

padding:15px;

border-radius:20px;

}

h2{

margin-top:25px;

font-size:30px;

color:#39ff14;

}

p{

color:#ccc;

font-size:18px;

}

.back-btn{

display:inline-flex;

align-items:center;

justify-content:center;

gap:8px;

margin-top:20px;

padding:12px 25px;

border:2px solid #39ff14;

border-radius:12px;

background:transparent;

color:#39ff14;

text-decoration:none;

font-size:16px;

font-weight:600;

transition:.3s;

}

.back-btn:hover{

background:#39ff14;

color:#000;

transform:translateY(-2px);

box-shadow:0 0 15px rgba(57,255,20,.35);

}

</style>

</head>

<body>

<div class="card">

<img src="<?= $qrFile ?>">

<h2><?= $memberCode ?></h2>

<p>Present this QR Code to the receptionist.</p>

<a href="dashboard.php" class="back-btn">
    <i class="fa-solid fa-arrow-left"></i>
    Back to Dashboard
</a>

</div>

</body>

</html>
