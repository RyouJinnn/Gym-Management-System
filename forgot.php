<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Fit Function Gym | Forgot Password</title>

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
    position:relative;
    overflow-y:auto;
    overflow-x:hidden;
    padding:25px 0;
}

body::before{
    content:"";
    position:absolute;
    inset:0;
    background:
    linear-gradient(rgba(0,0,0,.82),rgba(0,0,0,.82)),
    url("logofit.png") center/700px no-repeat;
}

.right{
    width:100%;
    display:flex;
    justify-content:center;
    align-items:center;
    padding:20px;
    position:relative;
    z-index:2;
}

.form-container{
    width:100%;
    max-width:500px;
    padding:45px;
    background:rgba(15,15,15,.55);
    backdrop-filter:blur(18px);
    border:1px solid rgba(57,255,20,.15);
    border-radius:20px;
}

.logo{
    text-align:center;
}

.logo img{
    width:85px;
    margin-bottom:18px;
}

.logo h2{
    color:#fff;
    font-size:30px;
    margin-bottom:10px;
}

.subtitle{
    color:#39ff14;
    font-size:16px;
    font-weight:500;
    margin-bottom:15px;
}

.description{
    color:#e5e5e5;
    text-align:center;
    line-height:1.7;
    font-size:14px;
    margin-bottom:20px;
}

.input-box{
    margin-bottom:15px;
    font-size: 14px;
}

.input-box label{
    display:block;
    color:#fff;
    margin-bottom:10px;
    font-size:15px;
    font-weight:500;
}

.input-wrapper{
    position:relative;
}

.input-wrapper i{
    position:absolute;
    left:16px;
    top:50%;
    transform:translateY(-50%);
    color:#bfbfbf;
    font-size:16px;
    pointer-events:none;
}

.input-box input{
    width:100%;
    height:46px;
    padding:0 16px 0 48px;
    background:#1b1b1b;
    border:1px solid rgba(255,255,255,.12);
    border-radius:8px;
    color:#fff;
    font-size:15px;
}

.input-box input::placeholder{
    color:#bfbfbf;
}

.input-box input:focus{
    outline:none;
    border-color:#39ff14;
    box-shadow:0 0 10px rgba(57,255,20,.25);
}

.error{
    display:block;
    margin-top:8px;
    color:#ff4d4d;
    font-size:13px;
}

.valid{
    color:#39ff14;
}

input.invalid{
    border:2px solid #ff4d4d!important;
}

input.validInput{
    border:2px solid #39ff14!important;
}

button{
    width:100%;
    margin-top:20px;
    padding:14px;
    border:2px solid #39ff14;
    border-radius:10px;
    background:#000;
    color:#fff;
    font-size:16px;
    font-weight:700;
    cursor:pointer;
    transition:.3s;
}

button:hover:not(:disabled){
    background:#39ff14;
    color:#000;
}

button:disabled{
    background:#444;
    color:#888;
    cursor:not-allowed;
}

button:not(:disabled):hover{
    background:#2dd90f;
}

.remember{
    text-align:center;
    margin-top:22px;
    color:#fff;
    font-size:14px;
}

.remember a{
    color:#39ff14;
    text-decoration:none;
    font-weight:600;
}

.remember a:hover{
    text-decoration:underline;
}

.back-login{
    margin-top:18px;
    text-align:center;
}

.back-login a{
    color:#bfbfbf;
    text-decoration:none;
    font-size:14px;
    transition:.3s;
}

.back-login a:hover{
    color:#39ff14;
}

.back-login i{
    margin-right:8px;
}

</style>

</head>

<body>

<div class="right">

<div class="form-container">

<div class="logo">

<img src="logofit.png" alt="Logo">

<h2>Forgot Password?</h2>

<div class="subtitle">
No worries! We'll help you reset it.
</div>

<div class="description">
Enter your registered email address and we'll send you a
6-digit verification code to reset your password.
</div>

</div>

<form action="forgot_process.php" method="POST">

<div class="input-box">

    <label>Email Address</label>

    <div class="input-wrapper">
        <i class="fa-regular fa-envelope"></i>

        <input
            type="email"
            id="email"
            name="email"
            placeholder="Email Address"
            required
        >
    </div>

    <small id="emailError" class="error"></small>

    <button type="submit" id="sendBtn" disabled>
        Send Verification Code
    </button>

</div>

<div class="remember">
    Remember your password?
    <a href="login.php">Log In</a>
</div>

<div class="back-login">
    <a href="home.php">
        <i class="fa-solid fa-arrow-left"></i>
        Back to Home
    </a>
</div>

</form>

<script>

const email = document.getElementById("email");
const emailError = document.getElementById("emailError");
const sendBtn = document.getElementById("sendBtn");

function validateEmail(){

    const pattern=/^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if(email.value.trim()==""){

        emailError.innerHTML="Email Address is required.";
        emailError.className="error";

        email.classList.add("invalid");
        email.classList.remove("validInput");

        sendBtn.disabled=true;
        return;
    }

    if(!pattern.test(email.value)){

        emailError.innerHTML="Please enter a valid email address.";
        emailError.className="error";

        email.classList.add("invalid");
        email.classList.remove("validInput");

        sendBtn.disabled=true;
        return;
    }

    fetch("check_email.php",{

        method:"POST",

        headers:{
            "Content-Type":"application/x-www-form-urlencoded"
        },

        body:"email="+encodeURIComponent(email.value)

    })

    .then(res=>res.text())

    .then(data=>{

        if(data.trim()=="exists"){

            emailError.innerHTML="✓ Valid Email Address";
            emailError.className="error valid";

            email.classList.remove("invalid");
            email.classList.add("validInput");

            sendBtn.disabled=false;

        }else{

            emailError.innerHTML="This email does not exist.";
            emailError.className="error";

            email.classList.add("invalid");
            email.classList.remove("validInput");

            sendBtn.disabled=true;

        }

    });

}

email.addEventListener("input",validateEmail);
email.addEventListener("blur",validateEmail);

document.querySelector("form").addEventListener("submit", function(e){

    validateEmail();

    if(email.classList.contains("invalid")){

        e.preventDefault();
        email.focus();

    }

});
</script>

</body>
</html>