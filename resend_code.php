<?php
session_start();

if (!isset($_SESSION['reset_email'])) {
    exit("Session expired.");
}

$email = $_SESSION['reset_email'];

include("connect.php");

$verification_code = rand(100000, 999999);
$verification_expiry = date("Y-m-d H:i:s", strtotime("+10 minutes"));

$stmt = $con->prepare("
UPDATE signup
SET verification_code = ?, verification_expiry = ?
WHERE email = ?
");

if (!$stmt) {
    die("Prepare failed: " . $con->error);
}

$stmt->bind_param(
    "sss",
    $verification_code,
    $verification_expiry,
    $email
);

if (!$stmt->execute()) {
    die("Execute failed: " . $stmt->error);
}

$stmt->close();

$_SESSION['signup_data']['code'] = $verification_code;
$_SESSION['signup_data']['expiry'] = $verification_expiry;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/phpmailer/phpmailer/src/Exception.php';
require 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/phpmailer/src/SMTP.php';

$mail = new PHPMailer(true);

try{

    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'fitfunctiongymm@gmail.com';
    $mail->Password = 'acrk lkaf cpug utri';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom('fitfunctiongymm@gmail.com', 'Fit Function Gym');
    $mail->addAddress($email);

    $mail->isHTML(true);
    $mail->Subject = "Fit Function Gym Verification Code";

    $mail->Body = "
    <div style='font-family:Arial;padding:20px;'>

        <h2 style='color:#39ff14;'>Fit Function Gym</h2>

        <p>Hello,</p>

        <p>Your new verification code is:</p>

        <h1 style='letter-spacing:6px;color:#39ff14;'>
            {$verification_code}
        </h1>

        <p>This code will expire in 10 minutes.</p>

        <small>If you didn't request this, please ignore this email.</small>

    </div>
    ";

    $mail->send();

    header("Location: verify.php?resent=1");
    exit();

}catch(Exception $e){

    echo "Mailer Error: " . $mail->ErrorInfo;

}
?>