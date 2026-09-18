<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/phpmailer/phpmailer/src/Exception.php';
require 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/phpmailer/src/SMTP.php';

$mail = new PHPMailer(true);

try{

    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'fitfunctiongymm@gmail.com';
    $mail->Password   = 'acrk lkaf cpug utri';   // Your Gmail App Password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    $mail->setFrom('fitfunctiongymm@gmail.com', 'Fit Function Gym');

    // Variables come from login_process.php
    $mail->addAddress($email);

    $mail->isHTML(true);

    $mail->Subject = "Fit Function Gym Login Verification Code";

    $mail->Body = "

    <div style='font-family:Arial,sans-serif;'>

        <h2>Hello {$firstname},</h2>

        <p>We received a login request for your <b>Fit Function Gym</b> account.</p>

        <p>Your login verification code is:</p>

        <h1 style='letter-spacing:8px;color:#39ff14;'>
            {$code}
        </h1>

        <p>Please enter this code to complete your login.</p>

        <p><b>This code will expire in 1 minute.</b></p>

        <hr>

        <small>
        If you did not attempt to log in, you can safely ignore this email.
        </small>

    </div>

    ";

    $mail->send();

}catch(Exception $e){

    die("Email could not be sent.<br>".$mail->ErrorInfo);

}
?>