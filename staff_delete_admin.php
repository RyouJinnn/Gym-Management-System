<?php

require_once("includes/admin_auth.php");

// Only allow POST request

if(
    $_SERVER["REQUEST_METHOD"] !== "POST" ||
    !isset($_POST['user_id'])
){

    header("Location: staff_admin.php");
    exit;

}


$user_id = (int) $_POST['user_id'];


// Prevent invalid ID

if($user_id <= 0){

    header("Location: staff_admin.php");
    exit;

}


// Delete ONLY Staff accounts

$stmt = $con->prepare("
    DELETE FROM users
    WHERE user_id = ?
    AND role = 'Staff'
");


if($stmt === false){

    die("SQL Error: " . $con->error);

}


$stmt->bind_param(
    "i",
    $user_id
);


if($stmt->execute()){

    $stmt->close();

    header(
        "Location: staff_admin.php?deleted=1"
    );

    exit;

}


$stmt->close();


header(
    "Location: staff_admin.php?delete_error=1"
);

exit;

?>