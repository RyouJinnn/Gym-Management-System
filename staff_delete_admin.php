<?php

require_once("includes/admin_auth.php");
require_once("includes/activity_logger.php");

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


$nameStmt = $con->prepare("
    SELECT full_name
    FROM users
    WHERE user_id = ?
    AND role = 'Staff'
");

if($nameStmt === false){

    die("SQL Error: " . $con->error);

}

$nameStmt->bind_param("i", $user_id);
$nameStmt->execute();
$nameStmt->bind_result($staffName);
$nameStmt->fetch();
$nameStmt->close();

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

    if(!empty($staffName)){

        logActivity(
            $con,
            "Staff Deletion",
            $staffName . " staff account was deleted from the system.",
            $_SESSION['admin_id'] ?? null,
            "Admin"
        );

    }

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