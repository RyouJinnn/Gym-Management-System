<?php
session_start();
include("connect.php");
require_once "phpqrcode/qrlib.php";

if (!isset($_SESSION['email'])) {
    header("Location:login.php");
    exit();
}

$email = $_SESSION['email'];

/* Logged in user */
$stmt = $con->prepare("
SELECT *
FROM signup
WHERE email=?
LIMIT 1
");

$stmt->bind_param("s",$email);
$stmt->execute();

$user = $stmt->get_result()->fetch_assoc();

/* Active membership */
$stmt = $con->prepare("
SELECT *
FROM membership
WHERE member_id=?
AND status='Active'
LIMIT 1
");

$stmt->bind_param("i",$user['id']);
$stmt->execute();

$membership = $stmt->get_result()->fetch_assoc();
$memberID = "M".str_pad($user['id'],5,"0",STR_PAD_LEFT);

/* QR only stores the Member ID */
$qrText = $memberID;

$qrFile="qrcodes/".$memberID.".png";

if(!file_exists($qrFile))
{
    QRcode::png($qrText,$qrFile,"H",8,2);
}

if(!$membership){

    header("Location:dashboard.php");
    exit();

}
?>

<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<title>Membership Card</title>

<link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@700&family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
<script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
<style>

body{

    background:#050505;

    font-family:Poppins,sans-serif;

    display:flex;

    flex-direction:column;

    justify-content:center;

    align-items:center;

    min-height:100vh;

    padding:40px;

}

.membership-card{

width:900px;

background:

linear-gradient(135deg,#111,#1c1c1c),

repeating-linear-gradient(

45deg,

transparent,

transparent 25px,

rgba(57,255,20,.02) 25px,

rgba(57,255,20,.02) 50px

);

border:2px solid #39ff14;

border-radius:28px;

padding:35px;

box-shadow:0 0 40px rgba(57,255,20,.22);

}

.membership-card::before{

content:"";

position:absolute;

top:-60%;

left:-20%;

width:40%;

height:220%;

background:rgba(255,255,255,.04);

transform:rotate(25deg);

pointer-events:none;

}

.membership-card::after{

content:"";

position:absolute;

inset:0;

border-radius:28px;

box-shadow:

inset 0 0 30px rgba(57,255,20,.08);

pointer-events:none;

}

.top-bar{

display:flex;

justify-content:space-between;

align-items:center;

padding-bottom:25px;

border-bottom:1px solid #2d2d2d;

margin-bottom:30px;

}

.top-bar div:first-child{

display:flex;

align-items:center;

gap:20px;

}

.gym-logo{

width:65px;

}

.top-bar h1{

font-family:Orbitron;

font-size:28px;

color:#39ff14;

}

.status-badge{

background:#39ff14;

color:#000;

padding:10px 20px;

border-radius:30px;
font-size: 14px;
font-weight:700;

display:flex;

align-items:center;

gap:8px;

}

.card-content{

display:flex;

justify-content:space-between;

align-items:center;

}

.member-info{

width:60%;

}

.member-photo{

width:140px;

height:140px;

border-radius:50%;

border:5px solid #39ff14;

object-fit:cover;

margin-bottom:20px;

}

.member-info h2{

font-size:25px;

color:#fff;

margin-bottom:8px;

}

.member-info h3{

font-size:18px;

color:#39ff14;

margin-bottom:20px;

}

.member-id{

display:inline-block;

padding:8px 18px;

border:1px solid #39ff14;

border-radius:30px;

letter-spacing:2px;

margin-bottom:30px;

color:#39ff14;

}

.details{

display:flex;

gap:60px;

}

.details p{

color:#ddd;

line-height:1.8;

}

.details strong{

color:#39ff14;

}

.qr-section{

text-align:center;

}

.qr-box{

    width:190px;

    height:190px;

    background:white;
    border:5px solid white;
    border-radius:18px;

    padding:12px;

    display:flex;

    justify-content:center;

    align-items:center;

    box-shadow:0 0 20px rgba(57,255,20,.25);
    transition:.3s;
}

.qr-box:hover{

transform:scale(1.04);

}

.qr-image{

    width:100%;

    height:100%;

    object-fit:contain;

}

.qr-section small{

color:#999;

letter-spacing:1px;

}

.qr-label{

    margin-top:15px;

    color:#39ff14;

    font-size:14px;

    font-weight:600;

    display:flex;

    justify-content:center;

    align-items:center;

    gap:8px;

}

.download-btn{

margin-top:30px;

width:320px;

height:55px;

background:#39ff14;

border:none;

border-radius:14px;

font-size:16px;

font-weight:700;

cursor:pointer;

transition:.3s;

}

.download-btn:hover{

transform:translateY(-3px);

box-shadow:0 0 20px rgba(57,255,20,.4);

}

.membership-card{

    position:relative;

    overflow:hidden;

}

.watermark{

position:absolute;

right:-30px;

bottom:-20px;

opacity:.05;

pointer-events:none;

}

.watermark img{

width:320px;

transform:rotate(-15deg);

}

</style>
</head>
<body>

<div class="membership-card" id="membershipCard">

    <div class="watermark">

        <img src="logofit.png">

    </div>

    <div class="top-bar">

        <div>

            <img src="logofit.png" class="gym-logo">

            <h1>FIT FUNCTION GYM</h1>

        </div>

        <div class="status-badge">

            <i class="fa-solid fa-circle-check"></i>

            ACTIVE

        </div>

    </div>

    <div class="card-content">

        <div class="member-info">

            <img
            src="<?= !empty($user['profile_picture']) ? htmlspecialchars($user['profile_picture']) : 'defaultimg.png'; ?>"
            class="member-photo">

            <h2>
                <?= htmlspecialchars($user['first_name']." ".$user['last_name']); ?>
            </h2>

            <h3>
                <?= htmlspecialchars($membership['plan_name']); ?>
            </h3>

            <div class="member-id">

                MEMBER #
                <?= str_pad($user['id'],5,"0",STR_PAD_LEFT); ?>

            </div>

            <div class="details">

                <p><strong>Started</strong><br>
                <?= date("F d, Y",strtotime($membership['start_date'])); ?></p>

                <p><strong>Valid Until</strong><br>
                <?= date("F d, Y",strtotime($membership['end_date'])); ?></p>

            </div>

        </div>

        <div class="qr-section">

    <div class="qr-box">

        <img
        src="<?= $qrFile; ?>"
        alt="QR Code"
        class="qr-image">

    </div>

    <div class="qr-label">

    <i class="fa-solid fa-mobile-screen-button"></i>

    Scan to Verify

</div>

</div>

    </div>

</div>

<button
class="download-btn"
onclick="downloadCard()">

<i class="fa-solid fa-download"></i>

Download Membership Card
</button>

<script>

function downloadCard(){

    html2canvas(document.getElementById("membershipCard"),{

        backgroundColor:null,

        scale:3

    }).then(function(canvas){

        let link=document.createElement("a");

        link.download="Membership-Card-<?= str_pad($user['id'],5,'0',STR_PAD_LEFT); ?>.png";

        link.href=canvas.toDataURL("image/png");

        link.click();

    });

}

</script>

</body>

</html>