<?php
session_start();
include("connect.php");

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: change_email.php");
    exit();
}

$currentEmail = $_SESSION['email'];
$newEmail = strtolower(trim($_POST['new_email'] ?? ''));

if ($newEmail === '') {
    header("Location: change_email.php?error=empty");
    exit();
}

/* Validate email format */
if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
    header("Location: change_email.php?error=invalid");
    exit();
}

/* Don't allow the same email */
if (strcasecmp($currentEmail, $newEmail) === 0) {
    header("Location: change_email.php?error=same");
    exit();
}

/* Check if the new email is already registered */
$stmt = $con->prepare("
    SELECT id
    FROM signup
    WHERE email = ?
    LIMIT 1
");

$stmt->bind_param("s", $newEmail);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $stmt->close();

    header("Location: change_email.php?error=exists");
    exit();
}

$stmt->close();

/* Get user's first name */
$stmt = $con->prepare("
    SELECT first_name
    FROM signup
    WHERE email = ?
    LIMIT 1
");

$stmt->bind_param("s", $currentEmail);
$stmt->execute();

$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user) {
    header("Location: profile.php");
    exit();
}

$firstname = $user['first_name'];

/* Generate verification code */
$verification_code = random_int(100000, 999999);

$verification_expiry = time() + 60;
/*
    Store email-change verification data
    separately from signup verification.
*/
$_SESSION['change_email'] = [
    'current_email' => $currentEmail,
    'new_email' => $newEmail,
    'firstname' => $firstname,
    'code' => $verification_code,
    'expiry' => $verification_expiry
];

$_SESSION['change_email_code_sent'] = true;

/* Send verification email */
require "change_email_mail.php";

header("Location: change_email_verify.php?sent=1");
exit();
?>