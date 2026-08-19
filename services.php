<?php ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Fit Function Gym | Services</title>
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

.section-title{
    color:#39ff14;
    letter-spacing:3px;
    font-size:18px;
    margin-bottom:15px;
}

.services{
    padding:20px 8%;
    background:#050505;
}

.services-heading{
    text-align:center;
    margin-bottom:20px;
}

.services-heading h2{
    font-size:30px;
    margin-bottom:18px;
}

.services-heading p{
    max-width:900px;
    margin:auto;
    color:#d5d5d5;
    line-height:1.9;
    font-size:15px;
}

.services-grid{
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:30px;
}

.service-card{
    background:#111;
    border:1px solid #262626;
    border-radius:18px;
    padding:25px;
    text-align:center;
}

.service-card i{
    font-size:30px;
    color:#39ff14;
    margin-bottom:25px;
}

.service-card h3{
    margin-bottom:25px;
    font-size:20px;
}

.service-card p{
    color:#cfcfcf;
    font-size: 14px;
    line-height:1.9;
}

.facilities{
    padding:20px 8%;
}

.facilities-container{
    display:flex;
    gap:30px;
    align-items:center;
    flex-wrap:wrap;
}

.facilities-image{
    flex:1;
}

.facilities-image img{
    width:100%;
    border-radius:20px;
    border:3px solid #39ff14;
}

.facilities-content{
    flex:1;
}

.facilities-content h2{
    font-size:23px;
    margin-bottom:20px;
}

.facilities-content p{
    color:#d5d5d5;
    line-height:2;
    margin-bottom:20px;
    font-size:15px;
}

.facilities-content ul{
    list-style:none;
}

.facilities-content ul li{
    margin-bottom:25px;
    font-size:15px;
}

.facilities-content i{
    color:#39ff14;
    margin-right:10px;
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

.services-grid{
grid-template-columns:repeat(2,1fr);
}

.facilities-container{
flex-direction:column;
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

.hero p{
font-size:18px;
}

.services-grid{
grid-template-columns:1fr;
}

.services-heading h2,
.facilities-content h2,
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
<div class="brand"><span>FIT</span> FUNCTION</div>
</div>

<nav>
<a href="home.php">Home</a>
<a href="about.php">About Us</a>
<a href="services.php" class="active">Services</a>
<a href="membership.php">Membership</a>
<a href="contact.php">Contact</a>
</nav>

<div class="auth">
<a href="login.php" class="login">Log In</a>
<a href="signup.php" class="signup">Sign Up</a>
</div>

</header>

<section class="hero">

<div>

<h1>OUR <span>SERVICES</span></h1>

<p>
Everything you need to achieve a healthier, stronger, and more active lifestyle.
</p>
</div>

</section>
<section class="services">
<div class="services-heading">
<div class="section-title">WHAT WE OFFER</div>
<h2>Fitness Services Designed For You</h2>

<p>
Fit Function Gym provides quality facilities and equipment to help members enjoy safe, comfortable, and effective workouts every day.
</p>

</div>

<div class="services-grid">

<div class="service-card">
<i class="fa-solid fa-dumbbell"></i>
<h3>Modern Equipment</h3>
<p>Train using high-quality strength machines, free weights, and benches for effective workouts.</p>
</div>

<div class="service-card">
<i class="fa-solid fa-heart-pulse"></i>
<h3>Cardio Area</h3>
<p>Improve endurance and heart health with treadmills, bikes, and elliptical machines.</p>
</div>

<div class="service-card">
<i class="fa-solid fa-fire"></i>
<h3>Strength Training</h3>
<p>Build muscle and increase body strength using our complete workout equipment.</p>
</div>

<div class="service-card">
<i class="fa-solid fa-person-running"></i>
<h3>Functional Training</h3>
<p>Enjoy stretching, mobility exercises, warm-ups, and bodyweight workouts.</p>
</div>

<div class="service-card">
<i class="fa-solid fa-hand-fist"></i>
<h3>Training Classes</h3>
<p>Train boxing, muay thai, or your body with personal trainer. </p>
</div>

<div class="service-card">
<i class="fa-solid fa-shield-heart"></i>
<h3>Clean Environment</h3>
<p>Exercise in a clean, organized, and comfortable fitness environment.</p>
</div>
</div>

</section>
<section class="facilities">
<div class="facilities-container">
<div class="facilities-image">
<img src="facility.jpg" alt="Fit Function Gym Facilities">
</div>
<div class="facilities-content">
<div class="section-title">OUR FACILITIES</div>
<h2>Train with Complete Fitness Facilities</h2>

<p>
Fit Function Gym offers modern facilities designed to provide members with a safe, comfortable, and motivating environment for every workout. Whether your goal is building strength, improving endurance, or staying active, our facilities are ready to support your fitness journey.
</p>
<ul>
<li><i class="fa-solid fa-check"></i>Modern Strength Training Equipment</li>
<li><i class="fa-solid fa-check"></i>Complete Cardio Workout Area</li>
<li><i class="fa-solid fa-check"></i>Free Weight & Dumbbell Section</li>
<li><i class="fa-solid fa-check"></i>Functional Training Space</li>
<li><i class="fa-solid fa-check"></i>Octagon Training Ring</li>
<li><i class="fa-solid fa-check"></i>Heavy Punching Bags</li>
<li><i class="fa-solid fa-check"></i>Spacious Workout Area</li>
<li><i class="fa-solid fa-check"></i>Clean & Well-Maintained Facilities</li>
</ul>
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
<p><i class="fa-solid fa-location-dot"></i> Zone 5 Patricio Road #067 Brgy. Magsaysay, Alaminos City, Pangasinan</p>
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