<?php
session_start();
include("connect.php");

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];

$stmt = $con->prepare("SELECT * FROM signup WHERE email=? LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

$fullname = trim($user['first_name'] . " " . $user['last_name']);
$stmt = $con->prepare("
SELECT
    membership_id,
    plan_name,
    start_date,
    end_date,
    status
FROM membership
WHERE member_id = ?
AND status='Active'
AND end_date>=CURDATE()
LIMIT 1
");

$stmt->bind_param("i",$user['id']);
$stmt->execute();

$membershipResult=$stmt->get_result();
$hasMembership=$membershipResult->num_rows>0;
$membership=$membershipResult->fetch_assoc();

$daysRemaining = 0;
if($hasMembership){
    $today = new DateTime();
    $expiry = new DateTime($membership['end_date']);
    $daysRemaining = $today->diff($expiry)->days;
}

$isProfileComplete =
    !empty(trim($user['first_name'])) &&
    !empty(trim($user['last_name'])) &&
    !empty(trim($user['email'])) &&
    !empty(trim($user['contact_number'])) &&
    !empty(trim($user['gender'])) &&
    !empty(trim($user['birthdate'])) &&
    !empty(trim($user['address'])) &&
    !empty(trim($user['profile_picture'])) &&
    $user['profile_picture'] !== 'defaultimg.png';
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Fit Function Gym | Dashboard</title>

<link rel="stylesheet" href="sidebar.css">

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
color:white;
overflow-x:hidden;
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

.hero{
    width:calc(100% - 30px);
    margin-left:30px;
    min-height:420px;
    border-radius:20px;
    overflow:hidden;
    position:relative;
    margin-bottom:22px;
    background:url("facility.jpg") center center/cover no-repeat;
    display:flex;
    align-items:center;
}

.hero::before{
    content:"";
    position:absolute;
    inset:0;
    background:linear-gradient(90deg,rgba(0,0,0,.72),rgba(0,0,0,.42),rgba(0,0,0,.12));
}

.hero-content{
    position:relative;
    z-index:2;
    max-width:500px;
    padding:40px;
}

.hero-content h5{
    font-size:15px;
    letter-spacing:2px;
    margin-bottom:8px;
}

.hero-content h1{
    font-family:'Orbitron',sans-serif;
    font-size:46px;
    line-height:1;
    color:#39ff14;
    margin-bottom:15px;
}

.hero-content h2{
    font-size:28px;
    margin-bottom:12px;
}

.hero-content p{
    font-size:14px;
    line-height:1.8;
    color:#ddd;
    margin-bottom:25px;
}

.hero-content button{
    width:200px;
    height:52px;
    border:2px solid #39ff14;
    background:transparent;
    color:#39ff14;
    border-radius:12px;
    font-size:15px;
    font-weight:600;
    cursor:pointer;
    transition:.3s;
}   

.hero-content button:hover{
background:#39ff14;
color:#000;
}

.section-title{
    width:100%;
    font-size:26px;
    margin:15px 0 10px;
}

.cards{
    display:flex;
    flex-wrap:wrap;
    justify-content:center;
    gap:30px;
    margin-top:35px;
}

.card{
    width:360px;
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

.membership-status{

    width:calc(100% - 30px);

    margin:20px 0 10px 30px;

    background:#111;

    border:1px solid #2a2a2a;

    border-radius:20px;

    padding:30px;

}

.membership-status h2{

    color:#39ff14;

    margin-bottom:20px;

}

.membership-grid{

    display:grid;

    grid-template-columns:repeat(4,1fr);

    gap:25px;

}

.info-box{

    background:#181818;

    border-radius:15px;

    padding:20px;

    text-align:center;

}

.info-box h4{

    color:#888;

    font-size:14px;

    margin-bottom:8px;

}

.info-box p{

    font-size:18px;

    font-weight:600;

}

.remaining{

    color:#39ff14;

}

.card-btn{

    margin-top:25px;

    width:230px;

    height:52px;

    border:none;

    border-radius:12px;

    background:#39ff14;

    color:#000;

    font-weight:700;

    cursor:pointer;

}

.quick-actions{

    width:calc(100% - 30px);

    margin:30px 0 20px 30px;

}

.quick-actions h2{

    margin-bottom:20px;

    color:#39ff14;

}

.action-grid{

    display:grid;

    grid-template-columns:repeat(4,1fr);

    gap:25px;

}

.action-card{

    background:#111;

    border:1px solid #2a2a2a;

    border-radius:18px;

    padding:25px;

    text-align:center;

    transition:.3s;

    cursor:pointer;

}

.action-card:hover{

    transform:translateY(-8px);

    border-color:#39ff14;

    box-shadow:0 10px 30px rgba(57,255,20,.18);

}

.action-card i{

    font-size:42px;

    color:#39ff14;

    margin-bottom:18px;

}

.action-card h3{

    margin-bottom:10px;

    font-size:18px;

}

.action-card p{

    color:#bbb;

    font-size:14px;

    line-height:1.6;

    margin-bottom:20px;

}

.action-card button{

    width:140px;

    height:45px;

    border:2px solid #39ff14;

    background:transparent;

    color:#39ff14;

    border-radius:10px;

    cursor:pointer;

    transition:.3s;

}

.action-card button:hover{

    background:#39ff14;

    color:#000;

}

@media(max-width:1100px){

    .action-grid{

        grid-template-columns:repeat(2,1fr);

    }

}

@media(max-width:650px){

    .action-grid{

        grid-template-columns:1fr;

    }

}

@media(max-width:1200px){
.card{
    width:320px;
}
}

@media(max-width:768px){

.cards{
    flex-direction:column;
    align-items:center;
}

.card{
    width:100%;
    max-width:420px;
}
}

.status{
flex-direction:column;
align-items:flex-start;
}

@media(max-width:900px){

.main{
    width:100%;
    margin-left:0;
    padding:35px;
    transition:.35s;
}

.hero{
height:auto;
}

.hero-content{
padding:40px 25px;
}

.hero-content h1{
font-size:42px;
line-height:50px;
}

.hero-content h2{
font-size:30px;
}

.hero-content p{
font-size:16px;
line-height:28px;
}

.status{
padding:25px;
}

.status-left{
flex-direction:column;
align-items:flex-start;
}

}

.btn-secondary{

display:inline-flex;

align-items:center;

justify-content:center;

gap:10px;

padding:15px 28px;

margin-left:15px;

background:transparent;

border:2px solid #39ff14;

border-radius:12px;

color:#39ff14;

font-weight:600;

text-decoration:none;

transition:.3s;

}

.btn-secondary:hover{

background:#39ff14;

color:#000;

}

@media(max-width:900px){

    .membership-grid{

        grid-template-columns:repeat(2,1fr);

    }

}

@media(max-width:600px){

    .membership-grid{

        grid-template-columns:1fr;

    }

}

</style>
</head>
<body>

<div class="wrapper">
    <?php include 'sidebar.php'; ?>

    <main class="main">

         <div class="dashboard-content">
   
        <section class="hero">

            <div class="hero-content">

                <h5>WELCOME TO</h5>

                <h1>FIT FUNCTION GYM</h1>

                <h2>Hello, <?php echo htmlspecialchars($user['first_name']); ?>!</h2>

                <?php if($hasMembership): ?>

<p>

Keep up the great work! Your membership is active.
Track your attendance, view your membership card,
and continue your fitness journey.

</p>

<?php else: ?>

<p>

We're excited to have you as part of the Fit Function Gym family.
Complete your profile, activate your membership,
and start your fitness journey today.

</p>

<?php endif; ?>

<?php if($hasMembership): ?>

<button onclick="location.href='attendance.php'">

    View Attendance

</button>

<?php else: ?>

<button onclick="location.href='membership_db.php'">

    Join Membership

</button>

<?php endif; ?>

            </div>

        </section>

        <?php if($hasMembership): ?>

<section class="membership-status">

    <h2>

        <i class="fa-solid fa-circle-check"></i>

        Active Membership

    </h2>

    <div class="membership-grid">

        <div class="info-box">

            <h4>Membership</h4>

            <p><?php echo htmlspecialchars($membership['plan_name']); ?></p>

        </div>

        <div class="info-box">

            <h4>Started</h4>

            <p><?php echo date("M d, Y",strtotime($membership['start_date'])); ?></p>

        </div>

        <div class="info-box">

            <h4>Expires</h4>

            <p><?php echo date("M d, Y",strtotime($membership['end_date'])); ?></p>

        </div>

        <div class="info-box">
            <h4>Remaining</h4>
            <p class="remaining">
               <?php echo $daysRemaining . " Days"; ?>
            </p>
        </div>
    </div>

    <button
        class="card-btn"
        onclick="location.href='membership_card.php'">

        View Membership Card

    </button>

    <a href="member_qr.php" class="btn-secondary">

    <i class="fa-solid fa-qrcode"></i>

    Show QR Code

</a>

</section>

<?php endif; ?>

<?php if($hasMembership): ?>

<section class="quick-actions">

    <h2>Quick Actions</h2>

    <div class="action-grid">

        <div class="action-card"
     onclick="location.href='attendance.php'">

    <i class="fa-solid fa-clipboard-check"></i>

    <h3>Attendance</h3>

    <p>View your attendance records.</p>

</div>  

        <div class="action-card"
     onclick="location.href='membership_card.php'">

    <i class="fa-solid fa-id-card"></i>

    <h3>Membership Card</h3>

    <p>View your digital membership card.</p>

</div>

        <div class="action-card"
     onclick="location.href='payment_history.php'">

    <i class="fa-solid fa-receipt"></i>

    <h3>Payment History</h3>

    <p>View your previous payments and receipts.</p>

</div>

        <div class="action-card"
     onclick="location.href='feedback.php'">

    <i class="fa-solid fa-comment-dots"></i>

    <h3>Feedback</h3>

    <p>Share your gym experience with us.</p>

</div>
    </div>
</section>

<?php endif; ?>

       <?php if (!$isProfileComplete || !$hasMembership): ?>

<h2 class="section-title">
    Get Started
</h2>

<div class="cards">

    <?php if (!$isProfileComplete): ?>

    <div class="card">

        <div class="card-icon">
            <i class="fa-solid fa-user-pen"></i>
        </div>

        <h3>Complete Profile</h3>

        <p>
            Add your personal information to finish setting up your account.
        </p>

        <button onclick="location.href='profile.php'">
            Set Up
        </button>

    </div>

    <?php endif; ?>

    <?php if (!$hasMembership): ?>

    <div class="card">

        <div class="card-icon">
            <i class="fa-solid fa-id-card"></i>
        </div>

        <h3>Membership Plan</h3>

        <p>
            Choose a membership that matches your fitness goals.
        </p>

        <button onclick="location.href='membership_db.php'">
            View Plans
        </button>

    </div>

    <?php endif; ?>

</div>

<?php endif; ?>
        </div> <!-- dashboard-content -->

</main>

</div> <!-- wrapper -->

<script>
const menuBtn = document.getElementById("menuBtn");
const sidebar = document.getElementById("sidebar");

menuBtn.addEventListener("click", function (e) {
    e.stopPropagation();
    sidebar.classList.toggle("open");
});

window.onclick = function(e){

    if(
        sidebar.classList.contains("open") &&
        !sidebar.contains(e.target) &&
        !menuBtn.contains(e.target)
    ){
        sidebar.classList.remove("open");
    }

}
</script>

</body>
</html>