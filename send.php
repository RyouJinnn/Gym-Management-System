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
    $mail->Password   = 'acrk lkaf cpug utri';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    $mail->setFrom('fitfunctiongymm@gmail.com', 'Fit Function Gym');

    // Variables must come from signup.php
    if(
    empty($email) ||
    empty($firstname) ||
    empty($code)
){
    die("Required email variables are missing.");
}

    $mail->addAddress($email);

    $mail->isHTML(true);
    $mail->Subject = "Verify Your Fit Function Gym Account";

    $mail->Body = "
    <div style='font-family:Arial,sans-serif;padding:20px;'>

        <h2>Hello {$firstname},</h2>

        <p>Thank you for registering at <b>Fit Function Gym</b>.</p>

        <p>Your verification code is:</p>

        <h1 style='letter-spacing:8px;color:#39ff14;'>{$code}</h1>

        <p>Please enter this code to activate your account.</p>

        <p>This code expires in 10 minutes.</p>

    </div>
    ";

    if(!$mail->send()){
    throw new Exception($mail->ErrorInfo);
}

} catch (Exception $e) {

    die("Email could not be sent.<br>" . $mail->ErrorInfo);

}