<?php
session_start();
include("connect.php");

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: login.php");
    exit();
}

if (!isset($_SESSION['login_verify'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['login_verify']['email'];
$firstname = $_SESSION['login_verify']['firstname'];
$userCode = trim($_POST['verification_code']);

// Check account
$stmt = $con->prepare("
SELECT *
FROM signup
WHERE email=?
LIMIT 1
");

$stmt->bind_param("s", $email);
$stmt->execute();

$result = $stmt->get_result();

if($result->num_rows != 1){

    $_SESSION['verify_error'] = "Account not found.";
    header("Location: login_verify.php");
    exit();

}

$row = $result->fetch_assoc();

if(strtotime($row['verification_expiry']) < time()){

    $_SESSION['verify_error'] = "Verification code has expired.";
    header("Location: login_verify.php");
    exit();

}

if($userCode != $row['verification_code']){

    $_SESSION['verify_error'] = "Incorrect verification code.";
    header("Location: login_verify.php");
    exit();

}

$clear = $con->prepare("
UPDATE signup
SET
    verification_code='',
    verification_expiry=NULL
WHERE id=?
");

if(!$clear){
    die("Prepare Error: " . $con->error);
}

$clear->bind_param("i", $row['id']);

$clear->execute();
$clear->close();

// Create login session
$_SESSION['email'] = $row['email'];
$_SESSION['firstname'] = $row['first_name'];
$_SESSION['user_id'] = $row['id'];

unset($_SESSION['login_verify']);

header("Location: dashboard.php");
exit();
?>