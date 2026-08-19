<?php ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Fit Function Gym | About Us</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@600;700;800&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

html{
    scroll-behavior:smooth;
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
    font:800 25px Orbitron,sans-serif;
    color:#aaa;
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
    background:
    linear-gradient(rgba(0,0,0,.75),rgba(0,0,0,.75)),
    url("bgfit.png") center/cover no-repeat;
    display:flex;
    justify-content:center;
    align-items:center;
    text-align:center;
    padding-top:60px;
}

.hero-content h1{

    font-family:'Orbitron',sans-serif;
    font-size:40px;
    color:#fff;

}

.hero-content span{

    color:#39ff14;

}

.hero-content p{

    margin-top:15px;
    font-size:14px;
    color:#d7d7d7;

}

/* ABOUT */

.about{

    padding:20px 8%;

}

.about-container{

    display:flex;
    align-items:center;
    gap:70px;
    flex-wrap:wrap;

}

.about-image{

    flex:1;

}

.about-image img{

    width:100%;
    border-radius:15px;
    border:3px solid #39ff14;
    box-shadow:0 0 30px rgba(57,255,20,.18);

}

.about-content{

    flex:1;

}

.section-title{

    color:#39ff14;
    font-size:18px;
    letter-spacing:3px;
    margin-bottom:15px;

}

.about-content h2{

    font-size:23px;
    margin-bottom:18px;

}

.about-content p{

    color:#d5d5d5;
    line-height:2;
    font-size:15px;
    margin-bottom:18px;

}

.about-btn{
    display:inline-block;
    margin-top:15px;
    background:#000;
    color:#fff;
    border:2px solid #39ff14;
    padding:12px 24px;
    border-radius:8px;
    text-decoration:none;
    font-weight:700;
    transition:.3s;
}

.about-btn:hover{
    background:#39ff14;
    color:#000;
}

.mission-vision{

    padding:20px 8% 20px;
    display:grid;
    grid-template-columns:repeat(2,1fr);
    gap:30px;

}

.mv-card{
    background:#111;
    border:1px solid #262626;
    border-radius:14px;
    padding:20px;
}

.mv-card i{

    font-size:30px;
    color:#39ff14;
    margin-bottom:14px;

}

.mv-card h3{
    font-size:20px;
    margin-bottom:14px;

}

.mv-card p{
    color:#d5d5d5;
    line-height:1.9;
    font-size:14px;

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

    color:#cfcfcf;
    text-decoration:none;

}

.footer-grid a:hover{

    color:#39ff14;

}

.footer-bottom{

    border-top:1px solid #222;
    margin-top:40px;
    padding-top:20px;
    text-align:center;
    color:#8d8d8d;

}

@media(max-width:1100px){

.hero h1{

    font-size:60px;

}

.about-container{

    flex-direction:column;

}

.mission-vision{

    grid-template-columns:1fr;

}

.features-grid{

    grid-template-columns:repeat(2,1fr);

}

.stats{

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

nav a{

    display:inline-block;
    margin:10px;

}

.hero{

    height:60vh;

}

.hero h1{

    font-size:42px;

}

.hero p{

    font-size:18px;

}

.features-grid{

    grid-template-columns:1fr;

}

.stats{

    grid-template-columns:1fr;

}

.cta h2{

    font-size:38px;

}

}

</style>

</head>

<body>

<header>

<div class="logo">

<img src="logofit.png">

<div class="brand">

<span>FIT</span> FUNCTION

</div>

</div>

<nav>

<a href="home.php">Home</a>

<a href="about.php" class="active">About Us</a>

<a href="services.php">Services</a>

<a href="membership.php">Membership</a>

<a href="contact.php">Contact</a>

</nav>

<div class="auth">

<a href="login.php" class="login">Log In</a>

<a href="signup.php" class="signup">Sign Up</a>

</div>

</header>

<section class="hero">

<div class="hero-content">

<h1>

ABOUT <span>FIT FUNCTION</span>

</h1>

<p>

Building stronger bodies, healthier lifestyles, and a supportive fitness community.

</p>

</div>

</section>

<section class="about">

<div class="about-container">

<div class="about-image">

<img src="gphoto.jpg" alt="About Fit Function Gym">

</div>

<div class="about-content">

<div class="section-title">

ABOUT US

</div>

<h2>

Your Trusted Fitness Partner

</h2>

<p>

Fit Function Gym is committed to helping individuals achieve healthier and stronger lives through quality fitness programs, professional guidance, and a motivating environment. Whether you're just beginning your fitness journey or striving for peak performance, our gym provides the equipment, support, and encouragement you need to reach your goals.

</p>

<p>

We believe that fitness is more than building muscles—it's about improving overall well-being, confidence, discipline, and creating lifelong healthy habits. Our community welcomes members of all fitness levels and works together to inspire positive change every day.

</p>

<a href="signup.php" class="about-btn">

Join Our Community

</a>

</div>

</div>

</section>

<section class="mission-vision">

<div class="mv-card">

<i class="fa-solid fa-bullseye"></i>

<h3>Our Mission</h3>

<p>

Our mission is to provide affordable fitness programs, modern equipment, professional guidance, and a positive environment that inspires people of all ages to achieve their fitness goals while living healthier lives.

</p>

</div>

<div class="mv-card">

<i class="fa-solid fa-eye"></i>

<h3>Our Vision</h3>

<p>

Our vision is to become one of the most trusted fitness centers by creating a community where everyone is motivated to improve their health, build confidence, and enjoy an active lifestyle.

</p>

</div>

</section>

<footer>

<div class="footer-grid">

<div>

<h3>Fit Function Gym</h3>

<p>

Fit Function Gym is committed to helping individuals achieve healthier lifestyles through quality fitness programs, modern equipment, and a supportive community.

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
<p><i class="fa-solid fa-location-dot"></i> Zone 5 Patricio Road #067 Brgy. Magsaysay, Alaminos City, Pangasinan
</p>
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