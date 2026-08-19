<?php
session_start();
include("connect.php");

if(!isset($_SESSION['reset_password'])){
    header("Location: forgot.php");
    exit();
}

$password = $_POST['password'];
$confirm  = $_POST['confirm_password'];

if($password != $confirm){

    $_SESSION['reset_error'] = "Passwords do not match.";
    header("Location: reset_password.php");
    exit();

}

$pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/';

if(!preg_match($pattern,$password)){

    $_SESSION['reset_error'] =
    "Password must contain at least 8 characters, uppercase, lowercase, number and special character.";

    header("Location: reset_password.php");
    exit();

}

$userId = $_SESSION['reset_password']['id'];

$stmt = $con->prepare("
SELECT password FROM signup WHERE id=?");

if(!$stmt){
    die("Prepare Error: ".$con->error);
}

$stmt->bind_param("i", $userId);

$stmt->bind_param("i", $userId);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

$stmt->close();

if(password_verify($password, $user['password'])){

    $_SESSION['reset_error'] =
    "Your new password cannot be the same as your current password.";

    header("Location: reset_password.php");
    exit();
}

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$stmt = $con->prepare("
UPDATE signup
SET
    password=?,
    verification_code='',
    verification_expiry=NULL,
    email_verified=1
WHERE id=?
");

if(!$stmt){
    die("Prepare Error: ".$con->error);
}

$stmt->bind_param(
    "si",
    $hashedPassword,
    $userId
);

if(!$stmt->execute()){
    die("Database Error: ".$stmt->error);
}

$stmt->close();
$firstname = $_SESSION['reset_password']['firstname'];
$email = $_SESSION['reset_password']['email'];

require("password_changed_mail.php");

unset($_SESSION['reset_password']);

header("Location: login.php");
exit();
?>