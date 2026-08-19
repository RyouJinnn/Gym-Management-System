<?php
session_start();

$codeSent = isset($_SESSION['code_sent']) ? $_SESSION['code_sent'] : false;

if (!isset($_SESSION['verify']['email'])) {
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Fit Function Gym | Verify Code</title>

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
    background:
    linear-gradient(rgba(0,0,0,.82),rgba(0,0,0,.82)),
    url("logofit.png") center/700px no-repeat;
}

.container{
    width:100%;
    display:flex;
    justify-content:center;
    align-items:center;
    position:relative;
    z-index:2;
}

.right{
    width:100%;
    display:flex;
    justify-content:center;
    align-items:center;
    padding:20px;
}

.form-container{
    width:100%;
    max-width:560px;
    padding:45px;
    background:rgba(15,15,15,.55);
    backdrop-filter:blur(18px);
    border:1px solid rgba(57,255,20,.15);
    border-radius:20px;
}

.logo{
    text-align:center;
    margin-bottom:25px;
}

.logo img{
    width:80px;
    margin-bottom:15px;
}

.logo h2{
    color:#fff;
    font-size:30px;
    margin-bottom:10px;
}

.logo p{
    color:#39ff14;
    font-size:18px;
    line-height:1.7;
}

.description{
    color:#fff;
    text-align:center;
    font-size:14px;
    line-height:1.8;
    margin-bottom:30px;
}

.code-box{
    display:flex;
    justify-content:space-between;
    gap:12px;
    margin-bottom:20px;
}

.code-box input{
    width:68px;
    height:72px;
    border-radius:10px;
    border:1px solid rgba(255,255,255,.18);
    background:#1b1b1b;
    color:#fff;
    font-size:30px;
    text-align:center;
    outline:none;
    transition:.3s;
}

.code-box input:focus{
    border-color:#39ff14;
    box-shadow:0 0 10px rgba(57,255,20,.35);
}

.code-box input.valid{
    border:2px solid #39ff14;
}

.code-box input.invalid{
    border:2px solid #ff4d4d;
}

.error{
    color:#ff4d4d;
    text-align:center;
    font-size:14px;
    margin-top:-5px;
    margin-bottom:18px;
}

.validText{
    color:#39ff14;
}

.resend{
    text-align:center;
    color:#fff;
    margin:20px 0;
    font-size:14px;
}

.resend a{
    color:#39ff14;
    text-decoration:none;
    font-weight:bold;
}

.resend a:hover{
    text-decoration:underline;
}

button{
    width:100%;
    padding:17px;
    border:none;
    border-radius:10px;
    background:#39ff14;
    color:#000;
    font-size:15px;
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
    margin-top:18px;
    text-align:center;
}

.back a{
    color:#bfbfbf;
    text-decoration:none;
    font-size:14px;
    transition:.3s;
}

.back a:hover{
    color:#39ff14;
}

.back i{
    margin-right:8px;
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

.code-box{
    justify-content:center;
}

}

</style>

</head>

<body>

    <div class="right">

        <div class="form-container">

            <div class="logo">

                <img src="logofit.png" alt="Logo">

                <h2>Verify Your Account</h2>

                <p>
                    Enter the <strong>6-digit code</strong> we sent to
                    <br>

                    <span style="color:#fff;">
                        <?php 
                        echo isset($_SESSION['verify']['email'])
? htmlspecialchars($_SESSION['verify']['email'])
: "your email";
                        ?>
                    </span>

                </p>

            </div>

            <div class="description">
    Please enter the 6-digit verification code sent to your email to continue verifying your account.
</div>

            <?php
if(isset($_SESSION['verify_error'])){
    echo '<small class="error" style="display:block;text-align:center;margin-bottom:15px;">'
         . $_SESSION['verify_error'] .
         '</small>';

    unset($_SESSION['verify_error']);
}
?>

            <form action="verify_process.php" method="POST" id="verifyForm">

                <div class="code-box">

                    <input type="text" maxlength="1" class="code" required>

                    <input type="text" maxlength="1" class="code" required>

                    <input type="text" maxlength="1" class="code" required>

                    <input type="text" maxlength="1" class="code" required>

                    <input type="text" maxlength="1" class="code" required>

                    <input type="text" maxlength="1" class="code" required>

                </div>

                <!-- Hidden input -->
                <input type="hidden" name="verification_code" id="verification_code">

                <small id="codeError" class="error"></small>

                <div class="resend">
    Didn't receive the code?
    <a href="javascript:void(0)" id="resendLink">Resend Code</a>
    <span id="timerContainer" style="display:none;">
        (<span id="timer">01:00</span>)
    </span>
</div>

                <button type="submit">

                    Verify Code

                </button>

                <div class="back">
    <a href="signup.php">
        <i class="fa-solid fa-arrow-left"></i>
        Back to Signup
    </a>
</div>
            </form>

        </div>

    </div>

</div>

<script>

const inputs = document.querySelectorAll(".code");
const hiddenCode = document.getElementById("verification_code");
const error = document.getElementById("codeError");
const form = document.getElementById("verifyForm");

// ================= AUTO NEXT =================

inputs.forEach((input,index)=>{

    input.addEventListener("input",function(){

        this.value=this.value.replace(/[^0-9]/g,'');

        if(this.value.length===1 && index<inputs.length-1){

            inputs[index+1].focus();

        }

        updateHidden();

    });

});

// ================= BACKSPACE =================

inputs.forEach((input,index)=>{

    input.addEventListener("keydown",function(e){

        if(e.key==="Backspace" && this.value===""){

            if(index>0){

                inputs[index-1].focus();

            }

        }

    });

});

// ================= PASTE =================

inputs[0].addEventListener("paste",function(e){

    e.preventDefault();

    let paste=(e.clipboardData||window.clipboardData)
    .getData("text")
    .replace(/\D/g,"")
    .substring(0,6);

    paste.split("").forEach((num,i)=>{

        if(inputs[i]){

            inputs[i].value=num;

        }

    });

    if(paste.length===6){

        inputs[5].focus();

    }

    updateHidden();

});

// ================= UPDATE HIDDEN =================

function updateHidden(){

    let code="";

    inputs.forEach(box=>{

        code+=box.value;

    });

    hiddenCode.value=code;

}

// ================= VALIDATION =================

form.addEventListener("submit",function(e){

    updateHidden();

    if(hiddenCode.value.length!==6){

        e.preventDefault();

        error.innerHTML="Please enter the complete 6-digit verification code.";

        inputs.forEach(box=>{

            if(box.value===""){

                box.classList.add("invalid");

            }

        });

        return;

    }

    error.className="error validText";

});

// ================= REMOVE RED BORDER =================

inputs.forEach(box=>{

    box.addEventListener("input",function(){

        this.classList.remove("invalid");

    });

});

// ================= TIMER =================

const resend = document.getElementById("resendLink");
const timer = document.getElementById("timer");
const timerContainer = document.getElementById("timerContainer");

let countdown;

function startCountdown(endTime){

    clearInterval(countdown);

    resend.style.pointerEvents="none";
    resend.style.opacity=".5";
    timerContainer.style.display="inline";

    countdown=setInterval(function(){

        const seconds=Math.floor((endTime-Date.now())/1000);

        if(seconds<=0){

            clearInterval(countdown);

            sessionStorage.removeItem("verifyTimerEnd");

            resend.style.pointerEvents="auto";
            resend.style.opacity="1";
            timerContainer.style.display="none";

            return;
        }

        const minutes=Math.floor(seconds/60);
        const secs=seconds%60;

        timer.innerHTML=
            String(minutes).padStart(2,"0")+":"+
            String(secs).padStart(2,"0");

    },1000);

}

window.addEventListener("load",function(){

    const savedEnd=sessionStorage.getItem("verifyTimerEnd");

    if(savedEnd && Number(savedEnd)>Date.now()){

        startCountdown(Number(savedEnd));

    }

});

resend.addEventListener("click",function(e){

    e.preventDefault();

    const endTime=Date.now()+60000;

    sessionStorage.setItem("verifyTimerEnd",endTime);

    startCountdown(endTime);

    fetch("resend_verify_code.php");

});

</script>

</body>
</html>

<?php
if(isset($_GET['sent'])){
    echo "<script>alert('Verification code has been sent to your email.');</script>";
}
?>