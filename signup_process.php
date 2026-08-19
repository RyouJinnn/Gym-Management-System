<?php
session_start();
include("connect.php");

date_default_timezone_set("Asia/Manila");

if(!isset($_POST['btn_signup'])){
    header("Location: signup.php");
    exit();
}

$firstname = trim($_POST['firstname']);
$lastname  = trim($_POST['lastname']);
$middlename = trim($_POST['middlename']);
$suffix = trim($_POST['suffix']);
$email     = trim($_POST['email']);
$contact   = trim($_POST['contact_number']);
$gender    = trim($_POST['gender']);
$birthdate = trim($_POST['birthdate']);
$password  = $_POST['password'];
$confirm   = $_POST['confirm_password'];

if($password != $confirm){

    $_SESSION['signup_error']="Passwords do not match.";
    header("Location: signup.php");
    exit();

}

$pattern='/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/';

if(!preg_match($pattern,$password)){

    $_SESSION['signup_error']="Password is too weak.";
    header("Location: signup.php");
    exit();

}

$hashedPassword=password_hash(
    $password,
    PASSWORD_DEFAULT
);

$code=rand(100000,999999);

$expiry=date(
"Y-m-d H:i:s",
strtotime("+10 minutes")
);

$stmt = $con->prepare("
INSERT INTO signup
(
first_name,
last_name,
middlename,
suffix,
email,
contact_number,
gender,
birthdate,
password,
verification_code,
verification_expiry,
email_verified,
status
)
VALUES
(
?,?,?,?,?,?,?,?,?,?,?,0,'Pending'
)
");

if(!$stmt){
    die($con->error);
}

$stmt->bind_param(
"sssssssssss",
$firstname,
$lastname,
$middlename,
$suffix,
$email,
$contact,
$gender,
$birthdate,
$hashedPassword,
$code,
$expiry
);

if(!$stmt->execute()){
    die($stmt->error);
}

$stmt->close();

$_SESSION['signup_data'] = [
    "firstname"       => $firstname,
    "lastname"        => $lastname,
    "middlename"      => $middlename,
    "suffix"          => $suffix,
    "email"           => $email,
    "contact_number"  => $contact,
    "gender"          => $gender,
    "birthdate"       => $birthdate,
    "password"        => $hashedPassword,
    "code"            => $code,
    "expiry"          => $expiry
];

$_SESSION['verify'] = [

    "firstname" => $firstname,
    "email" => $email

];

require("send.php");

header("Location: verify.php");
exit();

?>