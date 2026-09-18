<?php
session_start();

if (!isset($_SESSION['login_verify']['email'])) {
    exit("Session expired.");
}

include("connect.php");

$email = $_SESSION['login_verify']['email'];
$firstname = $_SESSION['login_verify']['firstname'];

$verification_code = rand(100000, 999999);

$expiry = date("Y-m-d H:i:s", strtotime("+1 minute"));

$stmt = $con->prepare("
    UPDATE signup
    SET
        verification_code=?,
        verification_expiry=?
    WHERE email=?
");

$stmt->bind_param(
    "sss",
    $verification_code,
    $expiry,
    $email
);

if(!$stmt->execute()){
    exit("Database Error: ".$stmt->error);
}

$stmt->close();

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

    $mail->setFrom(
        'fitfunctiongymm@gmail.com',
        'Fit Function Gym'
    );

    $mail->addAddress($email);

    $mail->isHTML(true);

    $mail->Subject = "Fit Function Gym Login Verification Code";

    $mail->Body = "

        <h2>Hello {$firstname},</h2>

        <p>Someone is attempting to log in to your <b>Fit Function Gym</b> account.</p>

        <p>Your login verification code is:</p>

        <h1 style='letter-spacing:8px;color:#39ff14;'>
            {$verification_code}
        </h1>

        <p>This code will expire in <b>1 minute</b>.</p>

        <p>If this wasn't you, please change your password immediately.</p>

    ";

    $mail->send();

    echo "success";

}catch(Exception $e){

    echo "Failed to send email.";

}

$con->close();
?>