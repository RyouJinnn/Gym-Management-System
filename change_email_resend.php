<?php
session_start();

if (!isset($_SESSION['change_email'])) {
    http_response_code(400);
    exit("Session expired.");
}

$changeEmail = $_SESSION['change_email'];

$newEmail = $changeEmail['new_email'];
$firstname = $changeEmail['firstname'];

/* Generate a new 6-digit code */
$verification_code = random_int(100000, 999999);

$verification_expiry = time() + 60;

/* Update session */
$_SESSION['change_email']['code'] = $verification_code;
$_SESSION['change_email']['expiry'] = $verification_expiry;
$_SESSION['change_email_code_sent'] = true;


/* PHPMailer */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/phpmailer/phpmailer/src/Exception.php';
require 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/phpmailer/src/SMTP.php';

$mail = new PHPMailer(true);

try {

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

    $mail->addAddress($newEmail);

    $mail->isHTML(true);

    $mail->Subject = "Fit Function Gym - New Verification Code";

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
                Here is your new verification code for
                changing your Fit Function Gym email address.
            </p>

            <h1 style='
                letter-spacing:8px;
                color:#39ff14;
                text-align:center;
            '>
                {$verification_code}
            </h1>

            <p>
                Please enter this code on the verification page.
            </p>

            <p>
                This code expires in <b>1 minute</b>.
            </p>

            <p style='color:#aaa;'>
                If you did not request an email change,
                you can safely ignore this message.
            </p>

            <hr style='border:0;border-top:1px solid #333;'>

            <p style='color:#888;font-size:12px;'>
                Fit Function Gym
            </p>

        </div>
    ";

    $mail->send();

    echo "success";

} catch (Exception $e) {

    http_response_code(500);
    echo "Failed to send email.";

}
?>