<?php
session_start();
include("connect.php");

/* ==========================
   CHECK LOGIN
========================== */

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

/* ==========================
   GET LOGGED-IN USER
========================== */

$email = $_SESSION['email'];

$user = [];

$stmt = $con->prepare("
    SELECT *
    FROM signup
    WHERE email = ?
    LIMIT 1
");

$stmt->bind_param("s", $email);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {

    session_destroy();
    header("Location: login.php");
    exit();

}

$user = $result->fetch_assoc();

$stmt->close();

/* ==========================
   CHECK ACTIVE MEMBERSHIP
========================== */

$hasMembership = false;
$membership = [];
$plan_name = "No Active Plan";

$today = date("Y-m-d");

$stmt = $con->prepare("
    SELECT *
    FROM membership
    WHERE member_id = ?
      AND status = 'Active'
      AND end_date >= ?
    ORDER BY end_date DESC
    LIMIT 1
");

$stmt->bind_param("is", $user['id'], $today);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows > 0) {

    $membership = $result->fetch_assoc();

    $hasMembership = true;

    $plan_name = $membership['plan_name'];

}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Fit Function Gym | Membership</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

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

.sidebar{
    width:260px;
    background:#050505;
    border-right:1px solid #1d1d1d;
    position:fixed;
    top:0;
    left:-260px;
    height:100vh;
    padding:25px 20px;
    display:flex;
    flex-direction:column;
    overflow:hidden;
    transition:.35s;
    z-index:1000;
}

.sidebar.open{
    left:20px;
}

.sidebar::-webkit-scrollbar{
width:6px;
}

.sidebar::-webkit-scrollbar-track{
background:#111;
}

.sidebar::-webkit-scrollbar-thumb{
background:#39ff14;
border-radius:20px;
}

.sidebar::-webkit-scrollbar-thumb:hover{
background:#59ff3d;
}

.sidebar-content{
    flex:1;
    overflow-y:auto;
    padding-right:6px;
}

.sidebar-divider{
border:none;
border-top:1px solid #2a2a2a;
margin:25px 0;
}

.logo{
    text-align:center;
    margin-bottom:25px;
}

.logo img{
    width:75px;
    margin-bottom:8px;
}

.logo span{
    display:block;
    font-size:24px;
    font-family:'Orbitron',sans-serif;
    color:#39ff14;
    line-height:1;
}

.logo h1{
    font-size:14px;
    font-family:'Orbitron',sans-serif;
    letter-spacing:1px;
    line-height:1.2;
}  

.menu{
display:flex;
flex-direction:column;
gap:15px;
}

.menu a{
    display:flex;
    align-items:center;
    gap:12px;
    padding:8px 14px;
    min-height:42px;
    font-size:15px;
    border-radius:10px;
    transition:.3s;
}

.menu a.active{
background:#39ff14;
color:#000;
font-weight:600;
}

.menu a:hover{
background:#181818;
}

.menu a.active:hover{
background:#39ff14;
}

.menu i{
    width:22px;
    text-align:center;
    font-size:15px;
}

.logout{
    flex-shrink:0;
    margin-top:auto;
    background:#050505;
}

.logout a{
    display:flex;
    justify-content:center;
    align-items:center;
    gap:8px;

    width:170px;      /* Smaller width */
    height:42px;      /* Smaller height */

    margin:0 auto;

    border:2px solid #39ff14;
    border-radius:10px;

    background:transparent;
    color:#39ff14;

    font-size:16px;   /* Smaller text */
    font-weight:600;

    transition:.3s;
}

.logout a:hover{
    background:#39ff14;
    color:#000;
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
    display:grid;
    grid-template-columns:repeat(4,1fr);
    gap:20px;
    margin-top:10px;
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

</style>
</head>

<body>
<div class="wrapper">
        <?php include("sidebar.php"); ?>
    <main class="main">

<div class="dashboard-content">

<section class="hero">
    <div class="hero-content">
        <h5>FIT FUNCTION GYM</h5>
        <h1>MEMBERSHIP</h1>
        <h2>Stay Strong. Stay Active.</h2>
        <p>
            Manage your membership, check your current plan,
            and explore the membership options available at
            Fit Function Gym.
        </p>
        <button onclick="document.getElementById('plans').scrollIntoView({behavior:'smooth'})">
            View Plans
        </button>
    </div>
</section>

<h2 class="section-title">
    Current Membership
</h2>

<div class="card">
<?php if($hasMembership){ ?>

    <div class="octagon-icon">
        <i class="fa-solid fa-id-card"></i>
    </div>

    <h3><?= htmlspecialchars($membership['plan_name']) ?></h3>
    <p>
        <strong>Price:</strong>
        ₱<?= number_format($membership['price'],2) ?>
        <br><br>
        <strong>Duration:</strong>
        <?= $membership['duration'] ?> Days
        <br><br>
        <strong>Start Date:</strong><br>
        <?= date("F d, Y", strtotime($membership['start_date'])) ?>
        <br><br>
        <strong>End Date:</strong><br>
        <?= date("F d, Y", strtotime($membership['end_date'])) ?>
        <br><br>
        <strong>Status:</strong>
        <span style="color:#39ff14;font-weight:600;">
            <?= htmlspecialchars($membership['status']) ?>
        </span>
    </p>

<?php } else { ?>
    <div class="octagon-icon">
        <i class="fa-solid fa-circle-xmark"></i>
    </div>
    <h3>No Active Membership</h3>
    <p>You don't have an active membership yet.<br><br>Choose one of the plans below to start your fitness journey.</p>

    <button onclick="document.getElementById('plans').scrollIntoView({behavior:'smooth'})">
        Choose Plan
    </button>

<?php } ?>

</div>

<?php if(!$hasMembership){ ?>

<!-- AVAILABLE MEMBERSHIP PLANS -->

<h2 class="section-title" id="plans">
    Membership Fees
</h2>

<div class="cards">

    <!-- REGULAR -->

    <div class="card">

        <div class="card-icon">
            <i class="fa-solid fa-user"></i>
        </div>

        <h3>Regular</h3>

        <p>
            <strong>₱499.00</strong>
        </p>

        <form action="payments.php" method="GET">

            <input type="hidden" name="plan" value="1">

            <button type="submit">
                Choose Plan
            </button>

        </form>

    </div>

    <!-- STUDENT -->

    <div class="card">

        <div class="card-icon">
            <i class="fa-solid fa-user-graduate"></i>
        </div>

        <h3>Student</h3>

        <p>
            <strong>₱299.00</strong>
        </p>

        <form action="payments.php" method="GET">

            <input type="hidden" name="plan" value="2">

            <button type="submit">
                Choose Plan
            </button>

        </form>

    </div>

    <!-- SENIOR -->

    <div class="card">

        <div class="card-icon">
            <i class="fa-solid fa-person-cane"></i>
        </div>

        <h3>Senior</h3>

        <p>
            <strong>₱299.00</strong>
        </p>

        <form action="payments.php" method="GET">

            <input type="hidden" name="plan" value="3">

            <button type="submit">
                Choose Plan
            </button>

        </form>

    </div>

    <!-- DROP-IN -->

    <div class="card">

        <div class="card-icon">
            <i class="fa-solid fa-door-open"></i>
        </div>

        <h3>Drop-In</h3>

        <p>
            <strong>₱199.00</strong>
        </p>

        <form action="payments.php" method="GET">

            <input type="hidden" name="plan" value="4">

            <button type="submit">
                Choose Plan
            </button>

        </form>

    </div>

</div>

<h2 class="section-title">
    Membership Packages
</h2>

<div class="cards">

    <!-- 3 MONTHS -->

    <div class="card">

        <div class="card-icon">
            <i class="fa-solid fa-calendar-days"></i>
        </div>

        <h3>3 Months</h3>

        <p>
            <strong>₱1,377.00</strong>
        </p>

        <form action="payments.php" method="GET">

            <input type="hidden" name="plan" value="5">

            <button type="submit">
                Choose Package
            </button>

        </form>

    </div>

    <!-- 6 MONTHS -->

    <div class="card">

        <div class="card-icon">
            <i class="fa-solid fa-calendar-days"></i>
        </div>

        <h3>6 Months</h3>

        <p>
            <strong>₱2,754.00</strong>
        </p>

        <form action="payments.php" method="GET">

            <input type="hidden" name="plan" value="6">

            <button type="submit">
                Choose Package
            </button>

        </form>

    </div>

    <!-- 9 MONTHS -->

    <div class="card">

        <div class="card-icon">
            <i class="fa-solid fa-calendar-days"></i>
        </div>

        <h3>9 Months</h3>

        <p>
            <strong>₱4,131.00</strong>
        </p>

        <form action="payments.php" method="GET">

            <input type="hidden" name="plan" value="7">

            <button type="submit">
                Choose Package
            </button>

        </form>

    </div>

    <!-- 1 YEAR -->

    <div class="card">

        <div class="card-icon">
            <i class="fa-solid fa-crown"></i>
        </div>

        <h3>1 Year</h3>

        <p>
            <strong>₱5,509.00</strong>
        </p>

        <form action="payments.php" method="GET">

            <input type="hidden" name="plan" value="8">

            <button type="submit">
                Choose Package
            </button>

        </form>

    </div>

</div>
<?php } ?>

            </div>
        </main>
    </div>

<script>

const sidebar = document.getElementById("sidebar");
const menuBtn = document.getElementById("menuBtn");

menuBtn.addEventListener("click", function(e){
    e.stopPropagation();
    sidebar.classList.toggle("open");
});

document.addEventListener("click", function(e){

    if(
        !sidebar.contains(e.target) &&
        !menuBtn.contains(e.target)
    ){
        sidebar.classList.remove("open");
    }

});

</script>

</body>
</html>