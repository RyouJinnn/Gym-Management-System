<?php
session_start();
include("connect.php");

if($_SERVER["REQUEST_METHOD"] != "POST"){
    header("Location: forgot.php");
    exit();
}

if(!isset($_SESSION['forgot'])){
    header("Location: forgot.php");
    exit();
}

$email = $_SESSION['forgot']['email'];
$userCode = trim($_POST['verification_code']);

$stmt = $con->prepare("
SELECT
id,
email,
first_name,
verification_code,
verification_expiry
FROM signup
WHERE email=?
LIMIT 1
");

$stmt->bind_param("s",$email);
$stmt->execute();

$result = $stmt->get_result();

if($result->num_rows==0){

    $_SESSION['verify_error']="Account not found.";
    header("Location: forgot_verify.php");
    exit();

}

$row=$result->fetch_assoc();

if(strtotime($row['verification_expiry']) < time()){

    $_SESSION['verify_error']="Verification code has expired.";
    header("Location: forgot_verify.php");
    exit();

}

if($userCode !== $row['verification_code']){

    $_SESSION['verify_error']="Incorrect verification code.";
    header("Location: forgot_verify.php");
    exit();

}

$_SESSION['reset_password']=[

    "id"=>$row['id'],
    "email"=>$row['email'],
    "firstname"=>$row['first_name']

];

unset($_SESSION['forgot']);

header("Location: reset_password.php");
exit();
?>