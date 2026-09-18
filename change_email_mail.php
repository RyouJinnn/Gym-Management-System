<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/phpmailer/phpmailer/src/Exception.php';
require 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/phpmailer/src/SMTP.php';

if (!isset($_SESSION['change_email'])) {
    exit("Email change session expired.");
}

$newEmail = $_SESSION['change_email']['new_email'];
$firstname = $_SESSION['change_email']['firstname'];
$verification_code = $_SESSION['change_email']['code'];

$mail = new PHPMailer(true);

try {

    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;

    /*
     * Use the SAME Gmail account and app password
     * that your existing mail files use.
     *
     * Do not paste the exposed password from the chat here.
     * Copy it directly from your local working mail file.
     */
    $mail->Username = 'fitfunctiongymm@gmail.com';
    $mail->Password = 'acrk lkaf cpug utri';

    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom(
        'fitfunctiongymm@gmail.com',
        'Fit Function Gym'
    );

    $mail->addAddress($newEmail);

    $mail->isHTML(true);

    $mail->Subject = "Fit Function Gym - Change Email Verification";

    $mail->Body = "
        <div style='
            font-family:Arial,sans-serif;
            max-width:600px;
            margin:auto;
            padding:30px;
            background:#111;
            color:#fff;
            border-radius:12px;
        '>

            <h2 style='color:#39ff14;'>
                Hello {$firstname}!
            </h2>

            <p>
                You requested to change your email address
                for your Fit Function Gym account.
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
                Enter this code on the verification page
                to confirm your new email address.
            </p>

            <p>
                This code expires in <b>1 minute</b>.
            </p>

            <p style='color:#aaa;'>
                If you did not request this email change,
                you can safely ignore this message.
            </p>

            <hr style='border:0;border-top:1px solid #333;'>

            <p style='color:#888;font-size:12px;'>
                Fit Function Gym
            </p>

        </div>
    ";

    $mail->send();

} catch (Exception $e) {

    unset($_SESSION['change_email']);
    unset($_SESSION['change_email_code_sent']);

    header("Location: change_email.php?error=send");
    exit();

}
?>