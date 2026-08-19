<?php
session_start();
include("connect.php");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: verify.php");
    exit();
}

if (!isset($_SESSION['verify']['email']) || !isset($_SESSION['signup_data'])) {
    header("Location: signup.php");
    exit();
}

$email = $_SESSION['verify']['email'];
$firstname = $_SESSION['verify']['firstname'];
$userCode = trim($_POST['verification_code']);

if (!isset($_SESSION['signup_data']['code']) || !isset($_SESSION['signup_data']['expiry'])) {
    $_SESSION['verify_error'] = "Verification session expired.";
    header("Location: verify.php");
    exit();
}

if ($userCode != $_SESSION['signup_data']['code']) {
    $_SESSION['verify_error'] = "Invalid verification code.";
    header("Location: verify.php");
    exit();
}

if (date("Y-m-d H:i:s") > $_SESSION['signup_data']['expiry']) {
    $_SESSION['verify_error'] = "Verification code has expired.";
    header("Location: verify.php");
    exit();
}

$stmt = $con->prepare("
UPDATE signup
SET
    email_verified = 1,
    status = 'Active',
    verification_code = NULL,
    verification_expiry = NULL
WHERE
    email = ?
");

$stmt->bind_param("s", $email);

if(!$stmt->execute()){
    die("Database Error: " . $stmt->error);
}

$stmt->close();

// Get the newly verified user
$getUser = $con->prepare("
SELECT id, first_name, email
FROM signup
WHERE email=?
LIMIT 1
");

$getUser->bind_param("s", $email);
$getUser->execute();

$result = $getUser->get_result();
$user = $result->fetch_assoc();

$getUser->close();

// Create login session
$_SESSION['user_id'] = $user['id'];
$_SESSION['email'] = $user['email'];
$_SESSION['firstname'] = $user['first_name'];

// Clear verification sessions
unset($_SESSION['signup_data']);
unset($_SESSION['verify']);
unset($_SESSION['code_sent']);

require "welcome_mail.php";

header("Location: dashboard.php");
exit();
?>