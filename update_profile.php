<?php
session_start();
include("connect.php");

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];

/* Get current user */
$stmt = $con->prepare("SELECT profile_picture FROM signup WHERE email=? LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$profilePicture = $user['profile_picture'];

/* Get form data */
$first_name     = trim($_POST['first_name']);
$middlename     = trim($_POST['middlename']);
$last_name      = trim($_POST['last_name']);
$suffix         = trim($_POST['suffix']);
$contact_number = trim($_POST['contact_number']);
$gender         = trim($_POST['gender']);
$birthdate      = $_POST['birthdate'];

/* ===========================
   AGE VALIDATION
=========================== */

$today = new DateTime();
$dob = new DateTime($birthdate);

$age = $today->diff($dob)->y;

if ($age < 15 || $age > 100) {
    die("You must be at least 15 years old.");
}

/* ===========================
   CONTACT NUMBER VALIDATION
=========================== */

if (!preg_match('/^09\d{9}$/', $contact_number)) {
    die("Invalid contact number.");
}

$address = trim($_POST['address']);

/* Upload new profile picture */
if (
    isset($_FILES['profile_picture']) &&
    $_FILES['profile_picture']['error'] == 0
) {

    $allowed = ['jpg','jpeg','png','webp'];

    $extension = strtolower(pathinfo(
        $_FILES['profile_picture']['name'],
        PATHINFO_EXTENSION
    ));

    if (in_array($extension, $allowed)) {

        $folder = "profile_picture/";

if (!is_dir($folder)) {
    mkdir($folder, 0777, true);
}

$filename = uniqid() . "." . $extension;

$destination = $folder . $filename;

        move_uploaded_file(
            $_FILES['profile_picture']['tmp_name'],
            $destination
        );

        $profilePicture = $destination;
    }
}

/* Update database */
$stmt = $con->prepare("
UPDATE signup SET
first_name=?,
middlename=?,
last_name=?,
suffix=?,
contact_number=?,
gender=?,
birthdate=?,
address=?,
profile_picture=?
WHERE email=?
");

$stmt->bind_param(
    "ssssssssss",
    $first_name,
    $middlename,
    $last_name,
    $suffix,
    $contact_number,
    $gender,
    $birthdate,
    $address,
    $profilePicture,
    $email
);

if ($stmt->execute()) {

    header("Location: profile.php?updated=1");
    exit();

} else {

    echo "Update failed.";

}
?>