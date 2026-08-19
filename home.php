<?php ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Fit Function Gym</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@600;700;800&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="style.css">
<style>
*{
	margin:0;
	padding:0;
	box-sizing:border-box
}

body{
	font-family:Poppins,sans-serif;
	background:#000;
	color:#fff
}

/* ================= HEADER ================= */

/* Make the header slightly shorter */
header{
	position:fixed;
	top:0;
	width:100%;
	display:flex;
	justify-content:space-between;
	align-items:center;
	padding:12px 45px;   /* was 18px 60px */
	background:rgba(0,0,0,.75);
	backdrop-filter:blur(10px);
	z-index:10
}

.logo{
	display:flex;
	align-items:center;
	gap:15px
}

/* Slightly smaller logo */
.logo img{
	width:60px;          /* was 70px */
	height:60px;
	border-radius:50%
}

/* Smaller brand text */
.brand{
	font:800 28px Orbitron,sans-serif;   /* was 38px */
	color:#aaa
}

.brand span{
	color:#39ff14
}

/* Smaller navigation text */
nav a{
	color:#fff;
	text-decoration:none;
	margin:0 16px;      /* was 18px */
	font-weight:600;
	font-size:16px;     /* added */
}

nav a:hover,.active{
	color:#39ff14
}

/* Smaller login/signup buttons */
.auth a{
	text-decoration:none;
	padding:8px 18px;  /* was 12px 22px */
	border-radius:8px;
	margin-left:10px;
	font-weight:600;
	font-size:15px;     /* added */
}

/* Login button */
.login{
	background:#000;
	color:#fff;
	border:2px solid #39ff14;
	transition:.3s;
}

.login:hover{
	background:#39ff14;
	color:#000;
}

/* Sign Up button */
.signup{
	background:#000;
	color:#fff;
	border:2px solid #39ff14;
	transition:.3s;
}

.signup:hover{
	background:#39ff14;
	color:#000;
}

/* ================= HERO ================= */

/* Hero section */
.hero{
    min-height:100vh;
    background:url("bgfit.png") center center/cover no-repeat;
    display:flex;
    align-items:center;
    position:relative;

    /* Move the content slightly upward */
    padding:95px 70px 70px;
}

.overlay{
	position:absolute;
	inset:0;
	background:rgba(0,0,0,.65)
}

/* Hero text container */
.content{
    position:relative;
    max-width:500px;
    width:100%;
    z-index:2;
}

/* Smaller green subtitle */
.content h4{
    font-size:22px;       /* was 26px */
    margin-bottom:12px;
}

/* Smaller FIT FUNCTION title */
.content h1{
    font:800 58px Orbitron,sans-serif;   /* was 76px */
    line-height:.92;
}

.content h1 span{
	color:#39ff14
}

/* Smaller paragraph */
.content p{
    margin:20px 0;
    font-size:14px;       /* was 18px */
    line-height:1.7;
    max-width:470px;
}

/* Get Started button */
.btn{
	display:inline-block;
	background:#000;
	color:#fff;
	border:2px solid #39ff14;
	padding:12px 20px;
	border-radius:8px;
	text-decoration:none;
	font-weight:700;
	font-size:15px;
	transition:.3s;
}

.btn:hover{
	background:#39ff14;
	color:#000;
}	

/* Join Now button */
.join-btn{
	display:inline-block;
	margin-top:40px;
	padding:12px 20px;
	background:#000;
	color:#fff;
	border:2px solid #39ff14;
	border-radius:8px;
	text-decoration:none;
	font-weight:700;
	font-size:15px;
	transition:.3s;
}

/* Hover effect */
.join-btn:hover{
	background:#39ff14;
	color:#000;
}

.cards{
	display:grid;
	grid-template-columns:repeat(4,1fr);
	gap:25px;
	padding:40px 60px;
	background:#050505;
	margin-top:-60px;
	position:relative
}

.card{
	background:#111;
	border:1px solid #2d2d2d;
	border-radius:16px;
	padding:20px
}

.card:hover{
	border-color:#39ff14;
	transform:translateY(-6px);
	transition:.3s
}

.card h3{
	color:#39ff14;
	margin-bottom:12px
}

/* ================= GENERAL SECTIONS ================= */

/* Reduce vertical spacing between sections */
section{
	padding-top:30px !important;      /* was mostly 90px */
	padding-bottom:20px !important;
}
/* ================= ABOUT / MISSION / FACILITIES ================= */

/* Slightly smaller section headings */
section h2{
	font-size:30px !important;      /* was 48px / 42px */
}
/* Smaller body text */
section p{
	font-size:15px !important;      /* was 19–21px */
	line-height:1.8 !important;
}
/* ================= JOIN BUTTON ================= */

/* Smaller CTA button */
a[href="signup.php"][style]{
	padding:16px 36px !important;   /* was 18px 45px */
	font-size:14px !important;      /* was 20px */
}
/* ================= FOOTER ================= */

/* Slightly shorter footer */
footer{
	padding:25px !important;        /* was 40px */
}

@media(max-width:900px){

header{
	padding:15px 20px;
	flex-wrap:wrap
}

nav{
	display:none
}

.content{
	padding:20px
}

.content h1{
	font-size:58px
}

.cards{
	grid-template-columns:1fr;
	padding:20px
}

}
</style>
</head>
<body>
<header>
<div class="logo">
<img src="logofit.png" alt="Logo">
<div class="brand"><span>FIT</span> FUNCTION</div>
</div>
<nav>
<a class="active" href="home.php">Home</a>
<a href="about.php">About Us</a>
<a href="services.php">Services</a>
<a href="membership.php">Membership</a>
<a href="contact.php">Contact</a>
</nav>
<div class="auth">
<a class="login" href="login.php">Log In</a>
<a class="signup" href="signup.php">Sign Up</a>
</div>
</header>

<section class="hero">
<div class="overlay"></div>
<div class="content">
<h4>STRONGER BODY. BETTER FIT.</h4>
<h1><span>FIT</span><br>FUNCTION GYM</h1>
<p>Your ultimate fitness destination. Train hard, stay consistent, and achieve your best self.</p>
<a class="btn" href="signup.php">Get Started</a>
</div>
</section>

<section style="background:#050505;padding:90px 80px;">

<div style="max-width:1200px;margin:auto;">

<h2 style="font-size:48px;color:#39ff14;font-family:Orbitron;margin-bottom:25px;">
ABOUT FIT FUNCTION GYM
</h2>

<p style="font-size:20px;line-height:1.9;color:#ddd;">
Fit Function Gym is committed to helping every member achieve a healthier,
stronger, and more confident lifestyle. Our gym provides a motivating
environment where beginners and experienced athletes can train safely and
comfortably. We believe that fitness is not only about building muscles but
also about improving physical health, mental wellness, discipline, and
self-confidence.
</p>

</div>

</section>

<section style="background:#000;padding:90px 80px;">

<div style="max-width:1200px;margin:auto;display:grid;grid-template-columns:repeat(auto-fit,minmax(350px,1fr));gap:50px;">

<div>

<h2 style="color:#39ff14;font-size:42px;font-family:Orbitron;margin-bottom:25px;">
OUR MISSION
</h2>

<p style="font-size:19px;line-height:1.9;color:#ddd;">
Our mission is to provide affordable fitness programs, modern equipment,
professional guidance, and a positive environment that inspires people of
all ages to achieve their fitness goals while living healthier lives.
</p>

</div>

<div>

<h2 style="color:#39ff14;font-size:42px;font-family:Orbitron;margin-bottom:25px;">
OUR VISION
</h2>

<p style="font-size:19px;line-height:1.9;color:#ddd;">
Our vision is to become one of the most trusted fitness centers by creating
a community where everyone is motivated to improve their health, build
confidence, and enjoy an active lifestyle.
</p>

</div>

</div>

</section>

<section style="background:#000;padding:90px 80px;">

<div style="max-width:1200px;margin:auto;text-align:center;">

<h2 style="font-size:48px;color:#39ff14;font-family:Orbitron;margin-bottom:25px;">
START YOUR FITNESS JOURNEY TODAY
</h2>

<p style="font-size:21px;color:#ddd;line-height:1.9;max-width:900px;margin:auto;">
Every great transformation begins with one step. Join Fit Function Gym today
and experience professional guidance, quality equipment, and a supportive
fitness community dedicated to helping you become stronger every day and
better every you.
</p>

<a href="signup.php" class="join-btn">
    Join Now
</a>
</div>

</section>

<footer style="background:#050505;padding:40px;text-align:center;color:#999;">

<h3 style="color:#39ff14;font-family:Orbitron;margin-bottom:15px;">
FIT FUNCTION GYM
</h3>

<p>Stronger Every Day. Better Every You.</p>

<p style="margin-top:10px;">
© 2026 Fit Function Gym. All Rights Reserved.
</p>

</footer>
</body>
</html>
