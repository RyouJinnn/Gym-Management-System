<?php

require 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/phpmailer/src/SMTP.php';
require 'vendor/phpmailer/phpmailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;

$mail = new PHPMailer(true);

$mail->isSMTP();
$mail->Host = 'smtp.gmail.com';
$mail->SMTPAuth = true;
$mail->Username = 'fitfunctiongymm@gmail.com';
$mail->Password = 'acrk lkaf cpug utri';
$mail->SMTPSecure = 'tls';
$mail->Port = 587;

$mail->setFrom('fitfunctiongymm@gmail.com', 'Fit Function Gym');
$mail->addAddress($email);

$mail->isHTML(true);
$mail->Subject = "Welcome to Fit Function Gym!";

$mail->Body = '

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body style="margin:0;padding:0;background:#f2f2f2;">

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f2f2f2;padding:30px 10px;">

<tr>

<td align="center">

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width:650px;background:#ffffff;border-radius:12px;overflow:hidden;">

<tr>

<td align="center" style="background:#111111;padding:40px 20px;">

<h1 style="margin:0;color:#39ff14;font-family:Arial,sans-serif;font-size:34px;line-height:1.3;">
FIT FUNCTION GYM
</h1>

<p style="margin:15px 0 0;color:#ffffff;font-family:Arial,sans-serif;font-size:18px;line-height:1.6;">
Welcome to the Fit Function Gym Family
</p>

</td>

</tr>

<tr>

<td style="padding:40px 30px;font-family:Arial,sans-serif;color:#444444;">

<h2 style="margin:0 0 25px;font-size:34px;line-height:1.3;color:#111111;">
Hello, '.$firstname.'! 👋
</h2>

<p style="font-size:17px;line-height:1.9;margin:0 0 22px;">
Congratulations! Your email address has been successfully verified and your
<strong>Fit Function Gym</strong> account is now fully activated.
</p>

<p style="font-size:17px;line-height:1.9;margin:0 0 22px;">
We are excited to welcome you to our growing fitness community. At
<strong>Fit Function Gym</strong>, we believe that fitness is more than simply
working out—it is about building confidence, improving your overall health,
developing discipline, and becoming the best version of yourself every day.
</p>

<p style="font-size:17px;line-height:1.9;margin:0 0 22px;">
Your account now gives you access to our Gym Management System where you can
manage your membership information, update your profile, monitor your
membership status, receive announcements, and stay connected with everything
happening inside Fit Function Gym.
</p>

<p style="font-size:17px;line-height:1.9;margin:0 0 22px;">
Whether your goal is to lose weight, gain muscle, improve endurance, increase
strength, or simply live a healthier lifestyle, our coaches and staff are
committed to supporting you every step of your fitness journey in a safe,
friendly, and motivating environment.
</p>

<p style="font-size:17px;line-height:1.9;margin:0 0 30px;">
Thank you for choosing <strong>Fit Function Gym</strong>. We truly appreciate
your trust and look forward to helping you achieve your fitness goals.
Together, let us build a stronger body, a healthier mind, and a better future.
</p>

<table role="presentation" align="center" cellpadding="0" cellspacing="0" border="0" style="margin:35px auto;">

<tr>

<td bgcolor="#39ff14" style="border-radius:8px;">

<a href="http://localhost/gymProject/login.php"
style="
display:inline-block;
padding:16px 35px;
font-family:Arial,sans-serif;
font-size:17px;
font-weight:bold;
color:#000000;
text-decoration:none;
">
Login to Your Account
</a>

</td>

</tr>

</table>

<p style="font-size:15px;line-height:1.8;color:#666666;margin-top:30px;">
If you did not create this account, please ignore this email or contact our
support team immediately.
</p>

<hr style="border:none;border-top:1px solid #dddddd;margin:35px 0;">

<p style="text-align:center;font-size:15px;color:#777777;line-height:1.8;margin:0;">
<strong>Fit Function Gym</strong><br>
Stronger Every Day. Better Every You.
</p>

</td>

</tr>

</table>

</td>

</tr>

</table>

</body>
</html>

';

$mail->send();

?>