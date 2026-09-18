<?php
session_start();
include("connect.php");

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$currentEmail = $_SESSION['email'];

$stmt = $con->prepare("
    SELECT first_name, email
    FROM signup
    WHERE email = ?
    LIMIT 1
");

$stmt->bind_param("s", $currentEmail);
$stmt->execute();

$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user) {
    header("Location: profile.php");
    exit();
}

$firstname = $user['first_name'];

$error = "";

if (isset($_GET['error'])) {

    if ($_GET['error'] === 'empty') {
        $error = "Please enter your new email address.";
    }

    elseif ($_GET['error'] === 'invalid') {
        $error = "Please enter a valid email address.";
    }

    elseif ($_GET['error'] === 'same') {
        $error = "Your new email must be different from your current email.";
    }

    elseif ($_GET['error'] === 'exists') {
        $error = "This email is already registered.";
    }

    elseif ($_GET['error'] === 'send') {
        $error = "Unable to send the verification code. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Change Email | Fit Function Gym</title>

<link rel="stylesheet"
href="https://fonts.googleapis.com/css2?family=Orbitron:wght@600;700&family=Poppins:wght@300;400;500;600;700&display=swap">

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

.header{
    text-align:center;
    margin-bottom:30px;
}

.header i{
    font-size:42px;
    color:#39ff14;
    margin-bottom:15px;
}

.header h1{
    font-family:'Orbitron',sans-serif;
    font-size:25px;
}

.header p{
    color:#aaa;
    font-size:13px;
    margin-top:8px;
}

.email-box{
    margin-bottom:20px;
}

label{
    display:block;
    color:#ddd;
    font-size:13px;
    font-weight:500;
    margin-bottom:7px;
}

input{
    width:100%;
    height:46px;
    padding:10px 14px;
    background:#1b1b1b;
    border:1px solid #343434;
    border-radius:8px;
    color:#fff;
    outline:none;
    font-size:14px;
}

input:focus{
    border-color:#39ff14;
    box-shadow:0 0 8px rgba(57,255,20,.2);
}

.current-email{
    color:#999;
}

.error{
    background:#2a1111;
    color:#ff4d4d;
    border:1px solid #ff4d4d;
    padding:12px 14px;
    border-radius:8px;
    margin-bottom:20px;
    font-size:13px;
    font-weight:500;
}

button{
    width:100%;
    height:46px;
    margin-top:10px;
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

</style>

</head>

<body>

<div class="container">

    <div class="header">

        <i class="fa-solid fa-envelope"></i>

        <h1>Change Email</h1>

        <p>
            Enter a new email address to continue.
        </p>

    </div>

    <?php if ($error !== ""): ?>

        <div class="error">
            <?= htmlspecialchars($error) ?>
        </div>

    <?php endif; ?>

    <form action="change_email_process.php" method="POST">

        <div class="email-box">

            <label>Current Email Address</label>

            <input
                type="email"
                class="current-email"
                value="<?= htmlspecialchars($currentEmail) ?>"
                readonly>

        </div>

        <div class="email-box">

            <label>New Email Address</label>

            <input
                type="email"
                name="new_email"
                placeholder="Enter your new email address"
                maxlength="100"
                required>

        </div>

        <button type="submit">

            <i class="fa-solid fa-paper-plane"></i>
            Send Verification Code

        </button>

    </form>

    <a href="profile.php" class="back">
        ← Back to Profile
    </a>

</div>

</body>
</html>