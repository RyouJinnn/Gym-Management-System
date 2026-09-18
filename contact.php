<?php
session_start();

$success = "";

if(isset($_SESSION['success_message'])){

    $success = $_SESSION['success_message'];

    unset($_SESSION['success_message']);

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Fit Function Gym | Contact</title>
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
color:#ddd;
}

.contact{
padding:20px 8%;
}

.contact-grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:35px;
    align-items:stretch;
}

.contact-left{
display:flex;
flex-direction:column;
}

.info-box{
    background:#111;
    border:1px solid #222;
    border-radius:16px;
    padding:20px;
    margin-bottom:20px;
}

.info-box h3{
color:#39ff14;
margin-bottom:20px;
font-size:20px;
}

.info-box p{
    color:#ccc;
    line-height:1.8;
    font-size:15px;
}

.form-box{
    background:#111;
    border:1px solid #222;
    border-radius:16px;
    padding:30px;
    display:flex;
    flex-direction:column;
    height:620px;
}

.form-box form{
display:flex;
flex-direction:column;
flex:1;
}

.form-box h2{
    margin-bottom:15px;
    font-size:20px;
}

.form-box input,
.form-box textarea{
    width:100%;
    padding:12px;
    margin-bottom:15px;
    background:#1a1a1a;
    border:1px solid #333;
    border-radius:8px;
    color:#fff;
    font-size:14px;
    font-family:'Poppins',sans-serif;
}

.form-box textarea{
    flex:1;
    min-height:220px;
    resize:none;
}

.form-box button{
    width:100%;
    padding:12px;
    background:#000;
    color:#fff;
    border:2px solid #39ff14;
    border-radius:8px;
    cursor:pointer;
    font-size:15px;
    font-weight:700;
    transition:.3s;
    margin-top:auto;
}

.form-box button:hover{
    background:#39ff14;
    color:#000;
}

.map{
padding:0 8% 10px;
}

.map iframe{
    width:100%;
    height:350px;
    border:none;
    border-radius:16px;
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

.success-message{

background:#39ff14;

color:#000;

padding:15px 20px;

border-radius:10px;

margin-bottom:20px;

font-weight:600;

display:flex;

align-items:center;

gap:10px;

animation:fadeIn .4s ease;

}

@keyframes fadeIn{

from{

opacity:0;
transform:translateY(-10px);

}

to{

opacity:1;
transform:translateY(0);

}

}

@media(max-width:900px){

.contact-grid{
grid-template-columns:1fr;
}

.footer-grid{
grid-template-columns:1fr;
}

.hero h1{
font-size:45px;
}

header{
flex-direction:column;
padding:20px;
}

nav{
margin:20px 0;
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
<a href="membership.php">Membership</a>
<a href="contact.php" class="active">Contact</a>
</nav>

<div class="auth">
<a href="login.php" class="login">Log In</a>
<a href="signup.php" class="signup">Sign Up</a>
</div>

</header>

<section class="hero">

<div>

<h1>CONTACT <span>FIT FUNCTION</span></h1>

<p>We're here to help you start your fitness journey.</p>

</div>

</section>

<section class="contact">

<div class="contact-grid">

<div>

<div class="info-box">
<h3>📍 Address</h3>
<p>Fit Function Gym<br>
Zone 5 Patricio Road #067 Brgy. Magsaysay,
Alaminos City, Pangasinan 2404, Philippines</p>
</div>

<div class="info-box">
<h3>📞 Phone</h3>
<p>0956-011-4697</p>
</div>

<div class="info-box">
<h3>✉ Email</h3>
<p>fitfunctionhead@gmail.com</p>
</div>

<div class="info-box">
<h3>🕒 Opening Hours</h3>
<p>
Monday - Saturday<br>
6:00 AM - 8:00 PM
</p>
</div>

</div>

<div class="form-box">
<h2>Send Us a Message</h2>

<?php if($success!=""){ ?>

<div class="success-message">

<i class="fa-solid fa-circle-check"></i>

<?php echo $success; ?>

</div>

<?php } ?>

<form action="save_message.php" method="POST">
    <input type="text" name="fullname" placeholder="Full Name" required>
    <input type="email" name="email" placeholder="Email Address" required>
    <input type="text" name="subject" placeholder="Subject" required>
    <textarea name="message" placeholder="Write your message here..." required></textarea>
    <button type="submit">Send Message</button>

</form>

</div>

</div>

</section>

<section class="map">
<iframe
    src="https://www.google.com/maps?q=Fit%20Function%20Gym%20Magsaysay%20Alaminos%20City%20Pangasinan&output=embed"
    width="100%"
    height="400"
    style="border:0;"
    allowfullscreen=""
    loading="lazy"
    referrerpolicy="no-referrer-when-downgrade">
</iframe>

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
</footer>

</body>
</html>