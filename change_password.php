<?php
session_start();
include("connect.php");

if (!isset($_SESSION['email'])) {

    header("Location: login.php");
    exit();

}

$email = $_SESSION['email'];

$stmt = $con->prepare("
SELECT
    first_name,
    last_name
FROM signup
WHERE email = ?
LIMIT 1
");

$stmt->bind_param("s", $email);
$stmt->execute();

$user = $stmt->get_result()->fetch_assoc();

$fullname = trim(
    $user['first_name'] . " " .
    $user['last_name']
);
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>
Fit Function Gym | Change Password
</title>

<link rel="stylesheet"
href="sidebar.css">

<link
href="https://fonts.googleapis.com/css2?family=Orbitron:wght@600;700&family=Poppins:wght@300;400;500;600;700&display=swap"
rel="stylesheet">

<link
rel="stylesheet"
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

.wrapper{

display:flex;
min-height:100vh;

}

.main{

width:100%;
padding:40px;
display:flex;
justify-content:center;

}

.password-container{

width:100%;
max-width:700px;
margin-left:40px;

}

.page-title{

font-size:30px;
font-weight:700;
margin-bottom:25px;

}

/*================ PASSWORD CARD ================*/

.password-card{

    background:#111;

    border:1px solid #2b2b2b;

    border-radius:22px;

    padding:35px;

    box-shadow:0 20px 45px rgba(0,0,0,.45);

}

.password-card h2{

    color:#39ff14;

    font-size:18px;

    margin-bottom:25px;

    border-bottom:1px solid #2b2b2b;

    padding-bottom:15px;

}

.form-group{

    margin-bottom:22px;

}

.form-group label{

    display:block;

    margin-bottom:8px;

    font-size:14px;

    font-weight:500;

}

.form-group input{

    width:100%;

    height:48px;

    background:#1b1b1b;

    border:1px solid #333;

    border-radius:10px;

    padding:0 50px 0 15px;

    color:#fff;

    font-size:14px;

    outline:none;

    transition:.3s;

}

.form-group input:focus{

    border-color:#39ff14;

}

.password-box{

    position:relative;

}

.toggle-password{

    position:absolute;

    right:18px;

    top:50%;

    transform:translateY(-50%);

    color:#999;

    cursor:pointer;

    transition:.3s;

}

.toggle-password:hover{

    color:#39ff14;

}

.password-rules{

    background:#181818;

    border:1px solid #2d2d2d;

    border-radius:12px;

    padding:18px;

    margin-top:10px;

}

.password-rules h4{

    color:#39ff14;

    margin-bottom:12px;

}

.password-rules ul{

    padding-left:18px;

    color:#bdbdbd;

    line-height:28px;

}

.change-btn{

    width:100%;

    height:52px;

    margin-top:28px;

    border:none;

    border-radius:12px;

    background:#39ff14;

    color:#000;

    font-size:14px;

    font-weight:700;

    cursor:pointer;

    transition:.3s;

}

.change-btn:hover{

    background:#57ff3d;

}

/*================ POPUP ================*/

.popup-overlay{

position:fixed;

top:0;
left:0;

width:100%;
height:100%;

background:rgba(0,0,0,.65);

display:none;

justify-content:center;
align-items:center;

z-index:9999;

backdrop-filter:blur(4px);

}

.popup-box{

width:420px;

background:#111;

border:1px solid #333;

border-radius:18px;

padding:35px;

text-align:center;

animation:popup .25s ease;

}

@keyframes popup{

from{

transform:scale(.85);
opacity:0;

}

to{

transform:scale(1);
opacity:1;

}

}

.popup-icon{

font-size:50px;

color:#39ff14;

margin-bottom:20px;

}

.popup-box h2{

font-size:22px;

margin-bottom:10px;

}

.popup-box p{

color:#cfcfcf;

line-height:28px;
font-size: 14px;
margin-bottom:28px;

}

.popup-btn{

width:170px;
height:50px;

border:none;

border-radius:12px;

background:#39ff14;

font-size:14px;

font-weight:700;

cursor:pointer;

transition:.3s;

}

.popup-btn:hover{

transform:scale(1.04);

}

@media(max-width:900px){

.password-container{

margin-left:0;

}

.main{

padding:25px;

}

}

</style>

</head>

<body>

<div class="wrapper">

<?php include("sidebar.php"); ?>

<main class="main">

<div class="password-container">

<h1 class="page-title">

Change Password

</h1>
<form
action="change_password_db.php"
method="POST">

<div class="password-card">

<h2>

Change Your Password

</h2>

<div class="form-group">

<label>

Current Password

</label>

<div class="password-box">

<input
type="password"
name="current_password"
id="current_password"
autocomplete="current-password"
required>

<i
class="fa-solid fa-eye toggle-password"
data-target="current_password"></i>

</div>

</div>

<div class="form-group">

<label>

New Password

</label>

<div class="password-box">

<input
type="password"
name="new_password"
id="new_password"
autocomplete="new-password"
required>

<i
class="fa-solid fa-eye toggle-password"
data-target="new_password"></i>

</div>

</div>

<div class="form-group">

<label>

Confirm New Password

</label>

<div class="password-box">

<input
type="password"
name="confirm_password"
id="confirm_password"
autocomplete="new-password"
required>

<i
class="fa-solid fa-eye toggle-password"
data-target="confirm_password"></i>

</div>

</div>

<div class="password-rules">

<h4>

Password Requirements

</h4>

<ul>

<li>8–25 characters</li>

<li>At least one uppercase letter</li>

<li>At least one lowercase letter</li>

<li>At least one number</li>

<li>At least one special character</li>

</ul>

</div>

<button
class="change-btn"
type="submit">

<i class="fa-solid fa-key"></i>

Change Password

</button>

</div>

</form>

</div>

</main>

</div>

<!--================ POPUP MODAL ================-->

<div class="popup-overlay" id="popupOverlay">

    <div class="popup-box">

        <div id="popupIcon" class="popup-icon">
            <i class="fa-solid fa-circle-check"></i>
        </div>

        <h2 id="popupTitle">
            Success
        </h2>

        <p id="popupMessage">
            Message
        </p>

        <button
        type="button"
        class="popup-btn"
        onclick="closePopup()">

            Close

        </button>

    </div>

</div>

<script>

/*================ SIDEBAR ================*/

const menuBtn=document.getElementById("menuBtn");

const sidebar=document.getElementById("sidebar");

menuBtn.addEventListener("click",function(e){

e.stopPropagation();

sidebar.classList.toggle("open");

});

window.onclick=function(e){

if(

sidebar.classList.contains("open")

&&

!sidebar.contains(e.target)

&&

!menuBtn.contains(e.target)

){

sidebar.classList.remove("open");

}

}

/*================ SHOW PASSWORD ================*/

document.querySelectorAll(".toggle-password").forEach(icon=>{

icon.onclick=function(){

const input=document.getElementById(

this.dataset.target

);

if(input.type==="password"){

input.type="text";

this.classList.remove("fa-eye");

this.classList.add("fa-eye-slash");

}

else{

input.type="password";

this.classList.remove("fa-eye-slash");

this.classList.add("fa-eye");

}

};

});

/*================ PASSWORD VALIDATION ================*/

document.querySelector("form").onsubmit=function(e){

const password=

document.getElementById("new_password").value;

const confirm=

document.getElementById("confirm_password").value;

const regex=

/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,25}$/;

if(!regex.test(password)){

e.preventDefault();

showPopup(

"warning",

"Weak Password",

"Password must contain uppercase, lowercase, number, special character, and be 8–25 characters long."

);

return;

}

if(password!==confirm){

    e.preventDefault();

    showPopup(

        "error",

        "Passwords Don't Match",

        "Please enter the same password."

    );

    return;

}

}

/*================ POPUP ================*/

let popupSuccess = false;

function showPopup(type,title,message){

    popupSuccess = (type === "success");

    document.getElementById("popupOverlay").style.display = "flex";

    document.getElementById("popupTitle").innerHTML = title;

    document.getElementById("popupMessage").innerHTML = message;

    const icon = document.getElementById("popupIcon");

    if(type === "success"){

        icon.style.color = "#39ff14";
        icon.innerHTML = '<i class="fa-solid fa-circle-check"></i>';

    }else if(type === "warning"){

        icon.style.color = "#ffc107";
        icon.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i>';

    }else{

        icon.style.color = "#ff4d4d";
        icon.innerHTML = '<i class="fa-solid fa-circle-xmark"></i>';

    }

}

function closePopup(){

    if(popupSuccess){

        window.location.href = "profile.php";

    }else{

        document.getElementById("popupOverlay").style.display = "none";

    }

}

<?php if(isset($_GET['success'])): ?>

showPopup(

"success",

"Password Changed",

"Your password has been changed successfully."

);

<?php endif; ?>

<?php if(isset($_GET['error'])): ?>

<?php

$title = "Error";
$message = "Something went wrong.";
$type = "error";

switch($_GET['error']){

case "current":

$title = "Incorrect Password";
$message = "Your current password is incorrect.";
break;

case "match":

$title = "Passwords Don't Match";
$message = "Please enter the same new password.";
$type = "warning";
break;

case "weak":

$title = "Weak Password";
$message = "Your new password doesn't meet the requirements.";
$type = "warning";
break;

case "same":

$title = "Same Password";
$message = "Your new password must be different from your current password.";
$type = "warning";
break;

case "database":

$title = "Database Error";
$message = "Unable to update your password.";
break;

}

?>

showPopup(

"<?= $type ?>",

"<?= $title ?>",

"<?= $message ?>"

);

<?php endif; ?>

</script>

</body>
</html>