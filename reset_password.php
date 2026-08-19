<?php
session_start();

if(!isset($_SESSION['reset_password'])){
    header("Location: forgot.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Fit Function Gym | Reset Password</title>
<link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@600;700;800&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
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
min-height:100vh;
display:flex;
justify-content:center;
align-items:center;
overflow-y:auto;
overflow-x:hidden;
padding:25px 0;
position:relative;
}

body::before{
content:"";
position:absolute;
inset:0;
background:rgba(0,0,0,.82);
}

.container{
width:90%;
max-width:1500px;
min-height:90vh;
display:flex;
background:linear-gradient(rgba(0,0,0,.65),rgba(0,0,0,.65)), url("logofit.png");
background-repeat:no-repeat;
background-size:70%;
background-position:center;
background-color:#000;
border-radius:20px;
overflow:hidden;
position:relative;
z-index:2;
}

.left{
flex:1;
display:flex;
align-items:center;
padding:70px;
}

.left-content h1{
font-family:'Orbitron',sans-serif;
font-size:72px;
line-height:.92;
color:#d9d9d9;
}

.left-content span{
    color:#39ff14;
}

.left-content p{
color:#fff;
margin-top:30px;
font-size:22px;
line-height:1.8;
max-width:420px;
}

.right{
flex:1;
display:flex;
justify-content:center;
align-items:center;
padding:60px;
}

.form-container{
width:100%;
max-width:560px;
padding:45px;
background:rgba(15,15,15,.55);
backdrop-filter:blur(18px);
border:1px solid rgba(57,255,20,.15);
border-radius:22px;
box-shadow:0 15px 40px rgba(0,0,0,.45);
}

.logo{
text-align:center;
margin-bottom:25px;
}

.logo img{
width:90px;
margin-bottom:15px;
}

.logo h2{
color:#fff;
font-size:44px;
margin-bottom:10px;
}

.logo p{
color:#39ff14;
font-size:18px;
line-height:1.7;
}

.icon{
width:72px;
height:72px;
margin:25px auto;
border-radius:50%;
background:#161616;
display:flex;
justify-content:center;
align-items:center;
}

.icon i{
color:#39ff14;
font-size:34px;
}

.description{
color:#fff;
text-align:center;
font-size:20px;
line-height:1.8;
margin-bottom:30px;
}

.input-box{
position:relative;
margin-bottom:22px;
}

.input-box input{
width:100%;
padding:16px 52px 16px 20px;
background:#1b1b1b;
border:1px solid rgba(255,255,255,.18);
border-radius:10px;
color:#fff;
font-size:16px;
transition:.3s;
}

.input-box input:focus{
outline:none;
border-color:#39ff14;
box-shadow:0 0 10px rgba(57,255,20,.35);
}

.toggle-password{
position:absolute;
right:18px;
top:17px;
color:#bfbfbf;
cursor:pointer;
font-size:20px;
line-height:1;
}

.error{
display:block;
margin-top:6px;
color:#ff4d4d;
font-size:13px;
}

.validText{
color:#39ff14;
}

.invalid{
border:2px solid #ff4d4d !important;
}

.valid{
border:2px solid #39ff14 !important;
}

button{
width:100%;
padding:17px;
border:none;
border-radius:10px;
background:#39ff14;
color:#000;
font-size:22px;
font-weight:bold;
cursor:pointer;
transition:.3s;
}

button:hover{
background:#2dd90f;
}

.divider{
display:flex;
align-items:center;
margin:28px 0;
color:#fff;
}

.divider::before,
.divider::after{
content:"";
flex:1;
height:1px;
background:#444;
}

.divider span{
margin:0 15px;
}

.back{
display:block;
width:100%;
text-align:center;
padding:16px;
border-radius:10px;
border:1px solid #444;
color:#fff;
text-decoration:none;
transition:.3s;
}

.back:hover{
color:#39ff14;
border-color:#39ff14;
}

@media(max-width:1100px){

.container{
flex-direction:column;
}

.left{
justify-content:center;
text-align:center;
}

.left-content p{
max-width:100%;
}

.right{
padding:35px;
}

.password-box{
position:relative;
}

.password-box input{
width:100%;
padding:16px 50px 16px 18px;
}

.input-box{
position:relative;
margin-bottom:22px;
}

</style>
</head>
<body>

<div class="container">
<div class="left">
<div class="left-content">

<h1>
<span>FIT</span><br>
FUNCTION<br>
<span>GYM</span>
</h1>

<p>
Create a new password to secure your account and continue your fitness journey.
</p>

</div>

</div>

<div class="right">

<div class="form-container">

    <div class="logo">

    <img src="logofit.png" alt="Logo">

    <h2>Reset Password</h2>

    <p>Create Your New Password</p>

</div>

<div class="icon">

    <i class="fa-solid fa-lock"></i>

</div>

<div class="description">

    Your new password must be at least <strong>8 characters</strong>
    and include an uppercase letter, lowercase letter,
    number, and special character.

</div>

<?php

if(isset($_SESSION['reset_error'])){

    echo '<small class="error" style="display:block;text-align:center;margin-bottom:15px;">'
    . $_SESSION['reset_error'] .
    '</small>';

    unset($_SESSION['reset_error']);

}

?>

<form action="reset_password_process.php" method="POST">

    <div class="input-box">

        <input
            type="password"
            id="password"
            name="password"
            placeholder="New Password"
            required>

        <i class="fa-solid fa-eye toggle-password" id="togglePassword"></i>

        <small id="passwordError" class="error"></small>

    </div>

    <div class="input-box">

        <input
            type="password"
            id="confirm_password"
            name="confirm_password"
            placeholder="Confirm Password"
            required>

        <i class="fa-solid fa-eye toggle-password" id="toggleConfirm"></i>

        <small id="confirmError" class="error"></small>

    </div>

    <button type="submit">

        Update Password

    </button>

    <div class="divider">

        <span>or</span>

    </div>

    <a href="login.php" class="back">

        <i class="fa-solid fa-arrow-left"></i>

        &nbsp; Back to Login

    </a>

</form>

</div>

</div>

</div>

<script>
const password = document.getElementById("password");
const confirmPassword = document.getElementById("confirm_password");

const passwordError = document.getElementById("passwordError");
const confirmError = document.getElementById("confirmError");

const togglePassword = document.getElementById("togglePassword");
const toggleConfirm = document.getElementById("toggleConfirm");

// ================= PASSWORD VALIDATION =================
function validatePassword(){

    const regex =
    /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;

    if(password.value.trim()===""){

        passwordError.innerHTML="Password is required.";
        passwordError.className="error";

        password.classList.add("invalid");
        password.classList.remove("valid");

        return;

    }

    if(!regex.test(password.value)){

        passwordError.innerHTML=
        "Password must contain at least 8 characters, uppercase, lowercase, number and special character.";

        passwordError.className="error";

        password.classList.add("invalid");
        password.classList.remove("valid");

        return;

    }

    fetch("check_old_password.php",{

        method:"POST",

        headers:{
            "Content-Type":"application/x-www-form-urlencoded"
        },

        body:"password="+encodeURIComponent(password.value)

    })

    .then(r=>r.text())

    .then(data=>{

        if(data.trim()=="same"){

            passwordError.innerHTML=
            "Your new password cannot be the same as your current password.";

            passwordError.className="error";

            password.classList.add("invalid");
            password.classList.remove("valid");

        }else{

            passwordError.innerHTML = "✓ Strong Password";
            passwordError.className = "error validText";

password.classList.remove("invalid");
password.classList.remove("valid");
password.classList.add("valid");
        }

    });

}

// ================= CONFIRM PASSWORD =================
function validateConfirm(){

    if(confirmPassword.value.trim() === ""){

        confirmError.innerHTML = "Please confirm your password.";
        confirmError.className = "error";
        confirmPassword.classList.add("invalid");
        confirmPassword.classList.remove("validInput");
        return false;

    }

    if(password.value !== confirmPassword.value){

        confirmError.innerHTML = "Passwords do not match.";
        confirmError.className = "error";
        confirmPassword.classList.add("invalid");
        confirmPassword.classList.remove("validInput");
        return false;

    }

    confirmError.innerHTML = "✓ Passwords match";
    confirmError.className = "error validText";
    confirmPassword.classList.remove("invalid");
    confirmPassword.classList.add("validInput");

    return true;
}

password.addEventListener("input", function(){
    validatePassword();
    validateConfirm();
});

password.addEventListener("blur", validatePassword);

confirmPassword.addEventListener("input", validateConfirm);
confirmPassword.addEventListener("blur", validateConfirm);

// ================= SHOW/HIDE PASSWORD =================
togglePassword.onclick = function(){

    if(password.type === "password"){
        password.type = "text";
        togglePassword.classList.replace("fa-eye","fa-eye-slash");
    }else{
        password.type = "password";
        togglePassword.classList.replace("fa-eye-slash","fa-eye");
    }

}

toggleConfirm.onclick = function(){

    if(confirmPassword.type === "password"){
        confirmPassword.type = "text";
        toggleConfirm.classList.replace("fa-eye","fa-eye-slash");
    }else{
        confirmPassword.type = "password";
        toggleConfirm.classList.replace("fa-eye-slash","fa-eye");
    }

}

document.querySelector("form").addEventListener("submit", function(e){

    validatePassword();
    validateConfirm();

});

</script>

</body>
</html>