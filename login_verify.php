   <?php
session_start();

if (!isset($_SESSION['login_verify']['email'])) {
    header("Location: login.php");
    exit();
}

include("connect.php");

$email = $_SESSION['login_verify']['email'];

$stmt = $con->prepare("
SELECT verification_expiry
FROM signup
WHERE email=?
LIMIT 1
");

$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$seconds = 0;

if (!empty($row['verification_expiry'])) {
    $seconds = max(
        0,
        strtotime($row['verification_expiry']) - time()
    );
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="view;port" content="width=device-width, initial-scale=1.0">
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
    position:relative;
    z-index:2;
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
font-size:15px;
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
height:68px;
border-radius:10px;
border:1px solid rgba(255,255,255,.18);
background:#1b1b1b;
color:#fff;
font-size:15px;
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
padding:15px;
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
ext-align:center;
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
                    <h2>Two-Factor Authentication</h2>
                    <p>
                        Enter the <strong>6-digit code</strong> we sent to
                        <br>
                        <span style="color:#fff;">
                            <?php 
                            echo isset($_SESSION['login_verify']['email'])
    ? htmlspecialchars($_SESSION['login_verify']['email'])
    : "your email";
                            ?>
                        </span>
                    </p>
                </div>

                <div class="description">
                  Please enter the 6-digit verification code we sent to your email to complete your login.
                </div>

                <?php
    if(isset($_SESSION['verify_error'])){
        echo '<small class="error" style="display:block;text-align:center;margin-bottom:15px;">'
             . $_SESSION['verify_error'] .
             '</small>';

        unset($_SESSION['verify_error']);
    }
    ?>

                <form action="login_verify_process.php" method="POST" id="verifyForm">

                    <div class="code-box">
                        <input type="text" maxlength="1" class="code" required>
                        <input type="text" maxlength="1" class="code" required>
                        <input type="text" maxlength="1" class="code" required>
                        <input type="text" maxlength="1" class="code" required>
                        <input type="text" maxlength="1" class="code" required>
                        <input type="text" maxlength="1" class="code" required>
                    </div>

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
    <a href="login.php">
        <i class="fa-solid fa-arrow-left"></i>
        Back to Login
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

    inputs.forEach((input,index)=>{

        input.addEventListener("input",function(){

            this.value=this.value.replace(/[^0-9]/g,'');

            if(this.value.length===1 && index<inputs.length-1){

                inputs[index+1].focus();

            }

            updateHidden();

        });

    });

    inputs.forEach((input,index)=>{

        input.addEventListener("keydown",function(e){

            if(e.key==="Backspace" && this.value===""){

                if(index>0){

                    inputs[index-1].focus();

                }

            }

        });

    });

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

    function updateHidden(){

        let code="";

        inputs.forEach(box=>{

            code+=box.value;

        });

        hiddenCode.value=code;

    }

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

        error.innerHTML="✓ Verification code complete.";

        error.className="error validText";

    });

    inputs.forEach(box=>{

        box.addEventListener("input",function(){

            this.classList.remove("invalid");

        });

    });

    const resend = document.getElementById("resendLink");
    const timer = document.getElementById("timer");
    const timerContainer = document.getElementById("timerContainer");

    let seconds = <?php echo $seconds; ?>;
    let countdown = null;

    function startTimer() {

        resend.style.pointerEvents = "none";
        resend.style.opacity = ".5";
        timerContainer.style.display = "inline";

        clearInterval(countdown);

        countdown = setInterval(function () {

            let minutes = Math.floor(seconds / 60);
            let secs = seconds % 60;

            timer.innerHTML =
                String(minutes).padStart(2, "0") + ":" +
                String(secs).padStart(2, "0");

            if (seconds <= 0) {

                clearInterval(countdown);

                resend.style.pointerEvents = "auto";
                resend.style.opacity = "1";
                timerContainer.style.display = "none";

                return;
            }

            seconds--;

        }, 1000);

    }

    startTimer();

    resend.addEventListener("click", function (e) {

        e.preventDefault();

        fetch("login_resend_code.php")
            .then(response => response.text())
            .then(data => {

                if (data.trim() === "success") {

                    seconds = 60;
                    startTimer();

                } else {

                    alert(data);

                }

            });

    });

    </script>

    </body>
    </html>

    <?php
    if(isset($_GET['sent'])){
        echo "<script>alert('Verification code has been sent to your email.');</script>";
    }
    ?>