<?php
session_start();
include("connect.php");

if (!isset($_SESSION['email'])) {

    header("Location: login.php");
    exit();

}

$email = $_SESSION['email'];

$currentPassword = trim($_POST['current_password']);
$newPassword = trim($_POST['new_password']);
$confirmPassword = trim($_POST['confirm_password']);

/*==============================
GET USER
==============================*/

$stmt = $con->prepare("
SELECT password
FROM signup
WHERE email = ?
LIMIT 1
");

$stmt->bind_param("s", $email);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

$stmt->close();

if (!$user) {

    header("Location: change_password.php?error=user");
    exit();

}

/*==============================
VERIFY CURRENT PASSWORD
==============================*/

if (!password_verify($currentPassword, $user['password'])) {

    header("Location: change_password.php?error=current");
    exit();

}

/*==============================
CHECK PASSWORD MATCH
==============================*/

if ($newPassword !== $confirmPassword) {

    header("Location: change_password.php?error=match");
    exit();

}

/*==============================
PASSWORD STRENGTH
==============================*/

if (
    !preg_match(
        '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,25}$/',
        $newPassword
    )
) {

    header("Location: change_password.php?error=weak");
    exit();

}

/*==============================
DON'T ALLOW SAME PASSWORD
==============================*/

if (password_verify($newPassword, $user['password'])) {

    header("Location: change_password.php?error=same");
    exit();

}

/*==============================
UPDATE PASSWORD
==============================*/

$newHash = password_hash($newPassword, PASSWORD_DEFAULT);

$stmt = $con->prepare("
UPDATE signup
SET password = ?
WHERE email = ?
");

$stmt->bind_param(
    "ss",
    $newHash,
    $email
);

if ($stmt->execute()) {

    header("Location: change_password.php?success=1");
    exit();

} else {

    header("Location: change_password.php?error=database");
    exit();

}
?>