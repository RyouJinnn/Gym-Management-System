<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_SESSION['change_email'])) {
    header("Location: profile.php");
    exit();
}

$changeEmail = $_SESSION['change_email'];

$newEmail = $changeEmail['new_email'];
$firstname = $changeEmail['firstname'];
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Verify New Email | Fit Function Gym</title>

<link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

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
    min-height:100vh;
    background:#070707;
    color:#fff;
    display:flex;
    justify-content:center;
    align-items:center;
    padding:20px;
}

.container{
    width:100%;
    max-width:500px;
    background:#111;
    border:1px solid #2b2b2b;
    border-radius:20px;
    padding:40px;
    box-shadow:0 20px 45px rgba(0,0,0,.45);
}

.logo{
    text-align:center;
    margin-bottom:30px;
}

.logo i{
    font-size:45px;
    color:#39ff14;
    margin-bottom:15px;
}

.logo h1{
    font-family:'Orbitron',sans-serif;
    font-size:25px;
}

.logo p{
    color:#aaa;
    font-size:13px;
    margin-top:8px;
    line-height:1.6;
}

.email{
    color:#39ff14;
    font-weight:600;
    word-break:break-word;
}

.code-inputs{
    display:flex;
    justify-content:center;
    gap:10px;
    margin:30px 0 20px;
}

.code-inputs input{
    width:58px;
    height:62px;
    text-align:center;
    font-size:24px;
    font-weight:600;
    color:#fff;
    background:#1b1b1b;
    border:1px solid #343434;
    border-radius:8px;
    outline:none;
}

.code-inputs input:focus{
    border-color:#39ff14;
    box-shadow:0 0 10px rgba(57,255,20,.25);
}

button{
    width:100%;
    height:46px;
    background:#39ff14;
    color:#000;
    border:none;
    border-radius:8px;
    font-size:15px;
    font-weight:700;
    cursor:pointer;
    transition:.3s;
}

button:hover{
    background:#57ff3d;
    transform:translateY(-2px);
}

.resend{
    text-align:center;
    margin-top:20px;
    color:#aaa;
    font-size:13px;
}

.resend a{
    color:#39ff14;
    font-weight:600;
    text-decoration:none;
}

.resend a:hover{
    text-decoration:underline;
}

#timerContainer{
    color:#aaa;
}

.error{
    background:#2a1111;
    color:#ff4d4d;
    border:1px solid #ff4d4d;
    padding:12px;
    border-radius:8px;
    margin-bottom:20px;
    text-align:center;
    font-size:13px;
}

.success{
    background:#102a11;
    color:#39ff14;
    border:1px solid #39ff14;
    padding:12px;
    border-radius:8px;
    margin-bottom:20px;
    text-align:center;
    font-size:13px;
}

.back{
    display:block;
    text-align:center;
    margin-top:18px;
    color:#aaa;
    text-decoration:none;
    font-size:13px;
}

.back:hover{
    color:#39ff14;
}

@media(max-width:500px){

    .container{
        padding:30px 20px;
    }

    .code-inputs{
        gap:6px;
    }

    .code-inputs input{
        width:45px;
        height:55px;
    }

}

</style>

</head>

<body>

<div class="container">

    <div class="logo">

        <i class="fa-solid fa-envelope-circle-check"></i>

        <h1>Verify New Email</h1>

        <p>
            A verification code was sent to
            <br>
            <span class="email">
                <?= htmlspecialchars($newEmail) ?>
            </span>
        </p>

    </div>

    <?php if(isset($_SESSION['change_email_error'])): ?>

        <div class="error">
            <?= htmlspecialchars($_SESSION['change_email_error']) ?>
        </div>

        <?php unset($_SESSION['change_email_error']); ?>

    <?php endif; ?>

    <?php if(isset($_SESSION['change_email_success'])): ?>

        <div class="success">
            <?= htmlspecialchars($_SESSION['change_email_success']) ?>
        </div>

        <?php unset($_SESSION['change_email_success']); ?>

    <?php endif; ?>

    <form action="change_email_verify_process.php" method="POST" id="verifyForm">

        <input
            type="hidden"
            name="verification_code"
            id="verification_code">

        <div class="code-inputs">

            <input type="text" maxlength="1" inputmode="numeric" class="code">
            <input type="text" maxlength="1" inputmode="numeric" class="code">
            <input type="text" maxlength="1" inputmode="numeric" class="code">
            <input type="text" maxlength="1" inputmode="numeric" class="code">
            <input type="text" maxlength="1" inputmode="numeric" class="code">
            <input type="text" maxlength="1" inputmode="numeric" class="code">

        </div>

        <button type="submit">

            <i class="fa-solid fa-check"></i>
            Verify Email

        </button>

    </form>

    <div class="resend">

        Didn't receive the code?

        <a href="javascript:void(0)" id="resendLink">
            Resend Code
        </a>

        <span id="timerContainer">
            (<span id="timer">01:00</span>)
        </span>

    </div>

    <a href="profile.php" class="back">
        ← Back to Profile
    </a>

</div>

<script>

const inputs = document.querySelectorAll(".code");
const hiddenCode = document.getElementById("verification_code");
const form = document.getElementById("verifyForm");

inputs.forEach((input, index) => {

    input.addEventListener("input", function(){

        this.value = this.value.replace(/\D/g,"");

        if(this.value && index < inputs.length - 1){
            inputs[index + 1].focus();
        }

    });

    input.addEventListener("keydown", function(e){

        if(
            e.key === "Backspace" &&
            this.value === "" &&
            index > 0
        ){
            inputs[index - 1].focus();
        }

    });

});

inputs[0].addEventListener("paste", function(e){

    e.preventDefault();

    const pasted = (e.clipboardData || window.clipboardData)
        .getData("text")
        .replace(/\D/g,"")
        .substring(0,6);

    pasted.split("").forEach((digit,index) => {

        if(inputs[index]){
            inputs[index].value = digit;
        }

    });

    if(pasted.length > 0){
        inputs[Math.min(pasted.length,6) - 1].focus();
    }

});

form.addEventListener("submit", function(e){

    let code = "";

    inputs.forEach(input => {
        code += input.value;
    });

    if(code.length !== 6){

        e.preventDefault();

        alert("Please enter the 6-digit verification code.");

        return;
    }

    hiddenCode.value = code;

});


/* =========================
   1 MINUTE TIMER
========================= */

const resendLink = document.getElementById("resendLink");
const timerContainer = document.getElementById("timerContainer");
const timer = document.getElementById("timer");

const timerKey = "changeEmailTimerEnd";

let timerEnd = sessionStorage.getItem(timerKey);

const urlParams = new URLSearchParams(window.location.search);
const codeJustSent = urlParams.get("sent") === "1";

if(codeJustSent){

    timerEnd = Date.now() + (60 * 1000);

    sessionStorage.setItem(timerKey, timerEnd);

}

else if(timerEnd && Date.now() >= Number(timerEnd)){

    timerEnd = null;

    sessionStorage.removeItem(timerKey);

}

function updateTimer(){

    const remaining = Math.max(
        0,
        Math.ceil((timerEnd - Date.now()) / 1000)
    );

    const minutes = String(
        Math.floor(remaining / 60)
    ).padStart(2,"0");

    const seconds = String(
        remaining % 60
    ).padStart(2,"0");

    timer.textContent = `${minutes}:${seconds}`;

    if(remaining <= 0){

        resendLink.style.pointerEvents = "auto";
        resendLink.style.opacity = "1";

        timerContainer.style.display = "none";

        return;

    }

    resendLink.style.pointerEvents = "none";
    resendLink.style.opacity = ".5";

    timerContainer.style.display = "inline";

    setTimeout(updateTimer,1000);
}

updateTimer();


/* =========================
   RESEND CODE
========================= */

resendLink.addEventListener("click", function(){

    if(resendLink.style.pointerEvents === "none"){
        return;
    }

    resendLink.style.pointerEvents = "none";
    resendLink.style.opacity = ".5";

    fetch("change_email_resend.php", {
        method:"POST"
    })
    .then(response => response.text())
    .then(data => {

        data = data.trim();

        if(data === "success"){

            timerEnd = Date.now() + (60 * 1000);

            sessionStorage.setItem(
                timerKey,
                timerEnd
            );

            timerContainer.style.display = "inline";

            updateTimer();

        }else{

            alert("Failed to resend the verification code.");

            resendLink.style.pointerEvents = "auto";
            resendLink.style.opacity = "1";

        }

    })
    .catch(() => {

        alert("Unable to resend the verification code.");

        resendLink.style.pointerEvents = "auto";
        resendLink.style.opacity = "1";

    });

});

</script>

</body>
</html>