<?php
session_start();

if(!isset($_SESSION['forgot'])){

    exit("Session expired.");

}

$email = $_SESSION['forgot']['email'];
$firstname = $_SESSION['forgot']['firstname'];

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

    $mail->Subject = "Fit Function Gym Password Reset Code";

    include("connect.php");

    $stmt = $con->prepare("
        SELECT verification_code
        FROM signup
        WHERE email=?
        LIMIT 1
    ");

    $stmt->bind_param("s",$email);
    $stmt->execute();

    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    $verification_code = $row['verification_code'];

    $stmt->close();

    $mail->Body = "

    <div style='font-family:Arial,sans-serif;'>

        <h2 style='color:#39ff14;'>
            Fit Function Gym
        </h2>

        <p>Hello <b>{$firstname}</b>,</p>

        <p>
            We received a request to reset your password.
        </p>

        <p>
            Your verification code is:
        </p>

        <h1 style='
            letter-spacing:8px;
            color:#39ff14;
            text-align:center;
        '>
            {$verification_code}
        </h1>

        <p>
            This verification code will expire in
            <b>10 minutes</b>.
        </p>

        <p>
            If you didn't request a password reset,
            you can safely ignore this email.
        </p>

        <br>

        <p>
            Regards,<br>
            <b>Fit Function Gym Team</b>
        </p>

    </div>

    ";

    $mail->send();

    echo "success";

}
catch(Exception $e){

    echo "Mailer Error: ".$mail->ErrorInfo;

}
?>