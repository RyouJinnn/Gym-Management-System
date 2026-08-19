<?php
session_start();
include("connect.php");

if (!isset($_SESSION['verify']['email']) || !isset($_SESSION['signup_data'])) {
    http_response_code(400);
    exit("Session expired.");
}

$email = $_SESSION['verify']['email'];
$firstname = $_SESSION['verify']['firstname'];

// Generate new code and expiry
$verification_code = rand(100000, 999999);
$verification_expiry = date("Y-m-d H:i:s", strtotime("+10 minutes"));

// Update session
$_SESSION['signup_data']['code'] = $verification_code;
$_SESSION['signup_data']['expiry'] = $verification_expiry;
$_SESSION['code_sent'] = true;

// Update database if account already exists
$stmt = $con->prepare("UPDATE signup SET verification_code=?, verification_expiry=? WHERE email=?");
if ($stmt) {
    $stmt->bind_param("sss", $verification_code, $verification_expiry, $email);
    $stmt->execute();
    $stmt->close();
}

// PHPMailer
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

    $mail->setFrom('fitfunctiongymm@gmail.com', 'Fit Function Gym');
    $mail->addAddress($email);

    $mail->isHTML(true);
    $mail->Subject = "Fit Function Gym Verification Code";

    $mail->Body = "
        <h2>Hello {$firstname},</h2>

        <p>Thank you for registering at <b>Fit Function Gym</b>.</p>

        <p>Your new verification code is:</p>

        <h1 style='letter-spacing:8px;color:#39ff14;'>
            {$verification_code}
        </h1>

        <p>Please enter this code to complete your registration.</p>

        <p>This code expires in 10 minutes.</p>
    ";

    $mail->send();

    echo "success";

} catch (Exception $e) {
    http_response_code(500);
    echo "Failed to send email.";
}