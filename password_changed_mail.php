<?php

date_default_timezone_set('Asia/Manila');

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

    $mail->Subject = "Your Fit Function Gym Password Has Been Changed";

    $date = date("F d, Y");
    $time = date("h:i A");

    $mail->Body = "

    <div style='font-family:Arial,sans-serif;
                max-width:650px;
                margin:auto;
                padding:30px;
                border:1px solid #e5e5e5;
                border-radius:10px;'>

        <h2 style='color:#39ff14;margin-bottom:5px;'>
            Fit Function Gym
        </h2>

        <hr>

        <h3>Password Successfully Changed</h3>

        <p>Hello <b>{$firstname}</b>,</p>

        <p>
            This email confirms that your
            <b>Fit Function Gym</b> account password
            was successfully changed.
        </p>

        <table style='margin:20px 0;font-size:15px;'>

            <tr>
                <td><b>Email:</b></td>
                <td>{$email}</td>
            </tr>

            <tr>
                <td><b>Date:</b></td>
                <td>{$date}</td>
            </tr>

            <tr>
                <td><b>Time:</b></td>
                <td>{$time}</td>
            </tr>

        </table>

        <div style='
            background:#f7fff2;
            border-left:5px solid #39ff14;
            padding:15px;
            margin:20px 0;
        '>

            <b>Security Notice</b><br><br>

            If you made this change,
            no further action is required.

            <br><br>

            If you did <b>NOT</b> change your password,
            please reset your password immediately or
            contact <b>Fit Function Gym support</b>.

        </div>

        <p>
            Thank you for choosing
            <b>Fit Function Gym</b>.
        </p>

        <br>

        <p>
            Regards,<br>
            <b>Fit Function Gym Team</b>
        </p>

    </div>

    ";

    $mail->send();

}catch(Exception $e){

    // Ignore email errors so password reset still succeeds

}

?>