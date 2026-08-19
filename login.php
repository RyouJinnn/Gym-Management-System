<?php
session_start();
if(isset($_GET['reset']) && $_GET['reset'] == "success"){
    echo "
    <div style='
        background:#39ff14;
        color:#000;
        padding:12px;
        text-align:center;
        font-weight:bold;
    '>
        ✓ Your password has been changed successfully. Please log in.
    </div>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Fit Function Gym | Login</title>
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
    background:rgba(0,0,0,.82);
}

.container{
    width:90%;
    max-width:1500px;
    min-height:90vh;
    display:flex;
    background-image:
    linear-gradient(rgba(0,0,0,.65),rgba(0,0,0,.65)), url("logofit.png");
    background-repeat:no-repeat;
    background-size:70%;
    background-position:center;
    background-color:#000;
    border-radius:20px;
    overflow:hidden;
    position:relative;
    z-index:2;
}

/* Center form */
.right{
    width:100%;
    display:flex;
    justify-content:center;
    align-items:center;
}

/* Smaller login box */
.form-container{
    width:100%;
    max-width:430px;
    padding:30px;
    background:rgba(15,15,15,.55);
    backdrop-filter:blur(18px);
    border:1px solid rgba(57,255,20,.15);
    border-radius:18px;
}

.logo{
    text-align:center;
    margin-bottom:35px;
}

.logo img{
    width:70px;
    margin-bottom:10px;
}

.logo h2{
    color:white;
    font-size:34px;
}

.logo p{
    font-family:'Orbitron';
    color:#39ff14;
    letter-spacing:2px;
    font-size:14px;
}

.input-box{
    margin-bottom:20px;
    position:relative;
}

.input-box input{
    width:100%;
    height:46px;
    padding:0 18px;
    background:#1b1b1b;
    border:1px solid rgba(255,255,255,.12);
    border-radius:8px;
    color:#fff;
    font-size:16px;
}

.input-box input:focus{
    outline:none;
    border-color:#39ff14;
}

.error{
    display:block;
    margin-top:6px;
    font-size:13px;
    color:#ff4d4d;
}

.valid{
    color:#39ff14;
}

.invalid{
    border:1px solid #ff4d4d!important;
}

.validInput{
    border:1px solid #39ff14!important;
}

.options{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-top:15px;
    color:white;
    font-size:14px;
}

.options a{
    color:#39ff14;
    text-decoration:none;
}

.options a:hover{
    text-decoration:underline;
}

button{
    width:100%;
    margin-top:15px;
    padding:14px;
    border:2px solid #39ff14;
    border-radius:8px;
    background:#000;
    color:#fff;
    font-size:16px;
    font-weight:bold;
    cursor:pointer;
    transition:.3s;
}

button:hover{
    background:#39ff14;
    color:#000;
}

.signup{
    text-align:center;
    margin-top:20px;
    color:white;
    font-size: 14px;
}

.signup a{
    color:#39ff14;
    font-weight:bold;
    text-decoration:none;
}

.back{
    text-align:center;
    margin-top:15px;
}

.back a{
    color:#bfbfbf;
    text-decoration:none;
}

.back a:hover{
    color:#39ff14;
}
.login-error{
    width:100%;
    margin-bottom:20px;
    padding:14px;
    background:rgba(255,77,77,.12);
    border:1px solid #ff4d4d;
    border-radius:8px;
    color:#ff4d4d;
    font-size:15px;
    text-align:center;
    font-weight:500;
}

#password{
    padding-right:48px;
}

.password-box{
    position:relative;
}

.password-box input{
    width:100%;
    height:46px;
    padding:0 52px 0 16px;
}

.password-box .toggle-password{
    position:absolute;
    right:16px;
    top:50%;
    transform:translateY(-50%) scale(.9);
    width:18px;
    height:18px;
    display:flex;
    justify-content:center;
    align-items:center;
    color:#bfbfbf;
    cursor:pointer;
}

</style>
</head>
<body>

<div class="container">

    <div class="right">

    <div class="form-container">

    <div class="logo">

        <img src="logofit.png">

        <h2>Login</h2>

        <p>WELCOME BACK</p>

    </div>

    <form action="login_process.php" method="POST">

    <?php
        if(isset($_SESSION['login_error'])){
    ?>
    <div class="login-error">
    <?php
        echo $_SESSION['login_error'];
        unset($_SESSION['login_error']);
    ?>
    </div>
    <?php
    }
    ?>

    <div class="input-box">
        <input
        type="email" id="email" name="email" placeholder="Email Address"value="
    <?php
        if(isset($_SESSION['login_email'])){
            echo htmlspecialchars($_SESSION['login_email']);
            unset($_SESSION['login_email']);
        }
    ?>"
    required
>
        <small id="emailError" class="error"></small>
    </div>
    
    <div class="input-box">

    <div class="password-box">

        <input
            type="password"
            id="password"
            name="password"
            placeholder="Password"
            required>

       <i class="fa-solid fa-eye fa-ms toggle-password" id="togglePassword"></i>

    </div>

    <small id="passwordError" class="error"></small>

</div>

    <div class="options">
        <a href="forgot.php">Forgot Password?</a>
    </div>
    <button type="submit" name="btn_login">Login</button>
    <div class="signup">Don't have an account?<a href="signup.php"> Create Account</a>
    </div>
    <div class="back">
        <a href="home.php">← Back to Home</a>
    </div>
    </form>
    </div>
    </div>
    </div>
    <script>

const email = document.getElementById("email");
const emailError = document.getElementById("emailError");

function validateEmail(){

    const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if(email.value.trim()===""){

        emailError.innerHTML="Email Address is required.";
        emailError.className="error";

        email.classList.add("invalid");
        email.classList.remove("validInput");

        return;
    }

    if(!pattern.test(email.value.trim())){

        emailError.innerHTML="Please enter a valid email address.";
        emailError.className="error";

        email.classList.add("invalid");
        email.classList.remove("validInput");

        return;
    }

    fetch("check_email.php",{

        method:"POST",

        headers:{
            "Content-Type":"application/x-www-form-urlencoded"
        },

        body:"email="+encodeURIComponent(email.value)

    })

    .then(response=>response.text())

   .then(data=>{

    data = data.trim();   // Remove spaces/newlines

    console.log(data);    // For debugging

    if(data === "exists"){

            emailError.innerHTML="✓ Email found";
            emailError.className="error valid";

            email.classList.remove("invalid");
            email.classList.add("validInput");

        }else{

            emailError.innerHTML="This email does not exist.";
            emailError.className="error";

            email.classList.add("invalid");
            email.classList.remove("validInput");

        }

    });

}

email.addEventListener("input",validateEmail);
email.addEventListener("blur",validateEmail);

const password=document.getElementById("password");
const passwordError=document.getElementById("passwordError");

function validatePassword(){

    passwordError.className = "error";

    if(password.value.trim()===""){

        passwordError.innerHTML="Password is required.";

        password.classList.add("invalid");
        password.classList.remove("validInput");

    }

    else{

        passwordError.innerHTML="✓ Password entered";
        passwordError.className="error valid";

        password.classList.remove("invalid");
        password.classList.add("validInput");

    }

}

password.addEventListener("input",validatePassword);

password.addEventListener("blur",validatePassword);

const toggle=document.getElementById("togglePassword");

toggle.onclick=function(){

if(password.type==="password"){

password.type="text";

toggle.classList.replace("fa-eye","fa-eye-slash");

}

else{

password.type="password";

toggle.classList.replace("fa-eye-slash","fa-eye");

}

}

document.querySelector("form").addEventListener("submit",function(e){

    validateEmail();
    validatePassword();

    if(
        email.classList.contains("invalid") ||
        password.classList.contains("invalid")
    ){
        e.preventDefault();
    }

});

</script>

</body>
</html>