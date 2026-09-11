<?php
session_start();
include("connect.php");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: change_email_verify.php");
    exit();
}

if (!isset($_SESSION['email']) || !isset($_SESSION['change_email'])) {
    header("Location: profile.php");
    exit();
}

$currentEmail = $_SESSION['change_email']['current_email'];
$newEmail = $_SESSION['change_email']['new_email'];
$verificationCode = trim($_POST['verification_code'] ?? '');

$storedCode = $_SESSION['change_email']['code'] ?? null;
$expiry = $_SESSION['change_email']['expiry'] ?? null;


/* Check if verification session still exists */
if ($storedCode === null || $expiry === null) {

    $_SESSION['change_email_error'] = "Verification session expired.";

    header("Location: change_email_verify.php");
    exit();
}


/* Check verification code */
if ($verificationCode !== (string)$storedCode) {

    $_SESSION['change_email_error'] = "Invalid verification code.";

    header("Location: change_email_verify.php");
    exit();
}


if (time() > (int)$expiry) {

    $_SESSION['change_email_error'] = "Verification code has expired.";
    header("Location: change_email_verify.php");
    exit();
}


/* Check again that the new email is still available */
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

    $_SESSION['change_email_error'] =
        "This email is already registered.";

    header("Location: change_email_verify.php");
    exit();
}

$stmt->close();


/* Change the email */
$stmt = $con->prepare("
    UPDATE signup
    SET email = ?
    WHERE email = ?
    LIMIT 1
");

$stmt->bind_param(
    "ss",
    $newEmail,
    $currentEmail
);

if (!$stmt->execute()) {

    $stmt->close();

    $_SESSION['change_email_error'] =
        "Unable to change your email. Please try again.";

    header("Location: change_email_verify.php");
    exit();
}

$stmt->close();


/* Update login session */
$_SESSION['email'] = $newEmail;


/* Clear email-change verification data */
unset($_SESSION['change_email']);
unset($_SESSION['change_email_code_sent']);


/* Success message */
header("Location: profile.php?email_changed=1");
exit();
?>