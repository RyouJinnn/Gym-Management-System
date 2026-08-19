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

</style>

</head>

<body>

<div class="card">

<img src="<?= $qrFile ?>">

<h2><?= $memberCode ?></h2>

<p>Present this QR Code to the receptionist.</p>

</div>

</body>

</html>