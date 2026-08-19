<?php ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Fit Function Gym | Membership</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@700;800&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
<style>

*{
margin:0;
padding:0;
box-sizing:border-box;
}

body{
font-family:'Poppins',sans-serif;
background:#000;
color:#fff;
}

/* ================= HEADER ================= */

header{
    position:fixed;
    top:0;
    left:0;
    width:100%;
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:12px 45px;
    background:rgba(0,0,0,.75);
    backdrop-filter:blur(10px);
    z-index:999;
}

.logo{
display:flex;
align-items:center;
gap:15px;
}

.logo img{
    width:60px;
    height:60px;
    border-radius:50%;
}

.brand{
    font-family:'Orbitron',sans-serif;
    font-size:25px;
    font-weight:800;
    color:#bdbdbd;
}

.brand span{
color:#39ff14;
}

nav a{
    color:#fff;
    text-decoration:none;
    margin:0 16px;
    font-size:14px;
    font-weight:600;
    transition:.3s;
}

nav a:hover,
nav .active{
    color:#39ff14;
}

.auth a{
    text-decoration:none;
    padding:8px 18px;
    border-radius:8px;
    margin-left:10px;
    font-size:15px;
    font-weight:600;
    transition:.3s;
}

.login{
    background:#000;
    color:#fff;
    border:2px solid #39ff14;
}

.login:hover{
    background:#39ff14;
    color:#000;
}

.signup{
    background:#000;
    color:#fff;
    border:2px solid #39ff14;
}

.signup:hover{
    background:#39ff14;
    color:#000;
}

.hero{
    height:45vh;
    display:flex;
    justify-content:center;
    align-items:center;
    text-align:center;
    padding-top:60px;
    background:
    linear-gradient(rgba(0,0,0,.75),rgba(0,0,0,.75)),
    url("bgfit.png") center/cover no-repeat;
}

.hero h1{
    font-family:'Orbitron',sans-serif;
    font-size:40px;
}

.hero span{
color:#39ff14;
}

.hero p{
    margin-top:15px;
    font-size:14px;
    color:#d7d7d7;
}

.membership{
padding:20px 8%;
background:#050505;
}

.title{
    text-align:center;
    margin-bottom:20px;
}

.title h2{
font-size:23px;
margin-bottom:10px;
}

.title p{
    color:#ccc;
    max-width:650px;
    margin:auto;
    line-height:1.8;
    font-size:15px;
}

.section-divider{
    margin:70px 0 30px;
}

.plans{
display:grid;
grid-template-columns:repeat(4,1fr);
gap:25px;
}

.plan{
    background:#111;
    border:1px solid #222;
    border-radius:16px;
    padding:35px 30px;
    text-align:center;
    display:flex;
    flex-direction:column;
    justify-content:space-between;
    min-height:260px;
}

.plan h3{
    font-size:20px;
    margin-bottom:25px;
}

.price{
    font-size:30px;
    font-weight:700;
    color:#39ff14;
    margin-bottom:40px;
}

.duration{
color:#aaa;
margin-bottom:30px;
}

.plan ul{
    list-style:none;
    margin-bottom:35px;
    flex:1; /* Makes all cards equal height */
}

.plan ul li{
    padding:10px 0;
    border-bottom:1px solid #222;
    font-size:14px;
}

.plan a{
    display:block;
    background:#000;
    color:#fff;
    border:2px solid #39ff14;
    text-decoration:none;
    padding:14px;
    border-radius:8px;
    font-weight:700;
    transition:.3s;
}

.plan a:hover{
    background:#39ff14;
    color:#000;
}

.payment{
padding:20px 8%;
}

.payment-box{
    max-width:900px;
    margin:auto;
    background:#111;
    padding:35px;
    border-radius:16px;
    border:1px solid #222;
}

.payment-box h2{
font-size:25px;
margin-bottom:20px;
text-align:center;
}

.payment-box p{
    text-align:center;
    color:#ccc;
    line-height:1.8;
    margin-bottom:30px;
    font-size:14px;
}

.methods{
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:25px;
    margin-top:40px;
}

.method{
    background:#1a1a1a;
    border:1px solid #2a2a2a;
    border-radius:15px;
    padding:25px 20px;
    min-height:300px;
    text-align:center;
}

.method img{
    width:100px;
    height:70px;
    object-fit:contain;
    display:block;
    margin:0 auto 15px;
}

.method h3{
    font-size:16px;
    margin-bottom:15px;
}

.method p{
    color:#bdbdbd;
    font-size:14px;
    line-height:1.6;
}

footer{
    background:#050505;
    padding:20px 8% 20px;
}

.footer-grid{
    display:grid;
    grid-template-columns:2fr 1fr 1.5fr;
    gap:50px;
}

.footer-grid h3{
    color:#39ff14;
    font-size: 18px;
    margin-bottom:20px;
}

.footer-grid p,
.footer-grid li{
    color:#cfcfcf;
    font-size: 14px;
    line-height:1.9;
}

.footer-grid ul{
    list-style:none;
}

.footer-grid ul li{
    margin-bottom:10px;
}

.footer-grid a{
    text-decoration:none;
    color:#cfcfcf;
    font-size:14px;
}

.footer-grid a:hover{
    color:#39ff14;
}   

.footer-bottom{
    border-top:1px solid #222;
    margin-top:40px;
    padding-top:20px;
    text-align:center;
    color:#888;
}

@media(max-width:1000px){

.plans{
grid-template-columns:1fr;
}

.methods{
grid-template-columns:repeat(2,1fr);
}

.footer-grid{
grid-template-columns:1fr;
}

}

@media(max-width:768px){

header{
flex-direction:column;
padding:20px;
}

nav{
margin:20px 0;
}

.hero h1{
font-size:45px;
}

.title h2,
.payment-box h2,
.cta h2{
font-size:34px;
}

.methods{
grid-template-columns:1fr;
}

}

</style>

</head>

<body>

<header>

<div class="logo">
<img src="logofit.png">
<div class="brand"><span>FIT</span> FUNCTION</div>
</div>

<nav>
<a href="home.php">Home</a>
<a href="about.php">About Us</a>
<a href="services.php">Services</a>
<a href="membership.php" class="active">Membership</a>
<a href="contact.php">Contact</a>
</nav>

<div class="auth">
<a href="login.php" class="login">Log In</a>
<a href="signup.php" class="signup">Sign Up</a>
</div>

</header>

<section class="hero">

<div>

<h1>OUR <span>MEMBERSHIP</span></h1>

<p>Choose the membership plan that fits your fitness journey.</p>

</div>

</section>

<section class="membership">

<div class="title">
    <h2>Membership Fees</h2>
    <p>
        Choose your preferred membership type.
        Login is required before purchasing.
    </p>
</div>

<div class="plans">

    <div class="plan">
        <h3>Regular</h3>
        <div class="price">₱499.00</div>
        <a href="login.php">Purchase Membership</a>
    </div>

    <div class="plan">
        <h3>Student</h3>
        <div class="price">₱299.00</div>
        <a href="login.php">Purchase Membership</a>
    </div>

    <div class="plan">
        <h3>Senior</h3>
        <div class="price">₱299.00</div>
        <a href="login.php">Purchase Membership</a>
    </div>

    <div class="plan">
        <h3>Drop-In</h3>
        <div class="price">₱199.00</div>
        <a href="login.php">Purchase Membership</a>
    </div>

</div>

<br><br><br>

<div class="title">
    <h2>Membership Packages</h2>
    <p>
        Save more by choosing a longer membership package.
    </p>
</div>

<div class="plans">

    <div class="plan">
        <h3>3 Months</h3>
        <div class="price">₱1,377.00</div>
        <a href="login.php">Purchase Package</a>
    </div>

    <div class="plan">
        <h3>6 Months</h3>
        <div class="price">₱2,754.00</div>
        <a href="login.php">Purchase Package</a>
    </div>

    <div class="plan">
        <h3>9 Months</h3>
        <div class="price">₱4,131.00</div>
        <a href="login.php">Purchase Package</a>
    </div>

    <div class="plan">
        <h3>1 Year</h3>
        <div class="price">₱5,509.00</div>
        <a href="login.php">Purchase Package</a>
    </div>

</div>

</section>

<section class="payment">

<div class="payment-box">

<h2>Accepted Payment Methods</h2>

<p>
After logging in and selecting a membership plan, you can complete your payment using any of the following payment methods.
</p>

<div class="methods">

    <div class="method">
        <img src="creditpayment.png" alt="Credit Card">
        <h3>Credit / Debit Card</h3>
        <p>Secure online payment using Visa, Mastercard and other supported cards.</p>
    </div>

    <div class="method">
        <img src="gcash.png" alt="GCash">
        <h3>GCash</h3>
        <p>Pay quickly by scanning the QR Code using your GCash mobile application.</p>
    </div>

    <div class="method">
        <img src="counter.png" alt="Bank Transfer">
        <h3>Pay at the Counter</h3>
        <p>Please visit our Gym Receptionist to process your payment.</p>
    </div>

</div>

</div>

</section>

<footer>
<div class="footer-grid">
<div>
<h3>Fit Function Gym</h3>
<p>
Fit Function Gym is committed to helping individuals achieve healthier lifestyles through quality fitness facilities and a supportive community.
</p>
</div>

<div>
<h3>Quick Links</h3>
<ul>
<li><a href="home.php">Home</a></li>
<li><a href="about.php">About Us</a></li>
<li><a href="services.php">Services</a></li>
<li><a href="membership.php">Membership</a></li>
<li><a href="contact.php">Contact</a></li>
</ul>
</div>

<div>
<h3>Contact Information</h3>
<p><i class="fa-solid fa-location-dot"></i> Zone 5 Patricio Road #067 Brgy. Magsaysay,<br>Alaminos City, Pangasinan 2404</p>
<br>
<p><i class="fa-brands fa-facebook-f"></i> Fit Function Alaminos</p>
<br>
<p><i class="fa-solid fa-envelope"></i> fitfunctionhead@gmail.com</p>
<br>
<p><i class="fa-solid fa-phone"></i> 0956-011-4697</p>
</div>
</div>

<div class="footer-bottom">
&copy; <?php echo date("Y"); ?> Fit Function Gym. All Rights Reserved.
</div>

</footer>

</body>
</html>