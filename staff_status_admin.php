<?php

require_once("includes/admin_auth.php");
require_once("includes/activity_logger.php");

if($_SERVER["REQUEST_METHOD"] !== "POST"){

    header("Location: staff_admin.php");
    exit;
}

if(
    !isset($_POST['user_id']) ||
    !isset($_POST['status'])
){

    header("Location: staff_admin.php");
    exit;
}

$user_id = (int) $_POST['user_id'];
$status = trim($_POST['status']);

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

if(
    $status !== "Active" &&
    $status !== "Inactive"
){

    header("Location: staff_admin.php");
    exit;
}

$stmt = $con->prepare("
    UPDATE users
    SET status = ?
    WHERE user_id = ?
    AND role = 'Staff'
");

if($stmt === false){
    die("SQL Error: " . $con->error);
}

$stmt->bind_param(
    "si",
    $status,
    $user_id
);

if(!$stmt->execute()){

    die("Unable to update staff status: " . $stmt->error);

}

$stmt->close();

if(!empty($staffName)){

    $action = ($status === "Active")
        ? "Staff Activation"
        : "Staff Deactivation";

    $description = $staffName . " staff account was set to " . $status . ".";

    logActivity(
        $con,
        $action,
        $description,
        $_SESSION['admin_id'] ?? null,
        "Admin"
    );
}

header(
    "Location: staff_view_admin.php?id=" .
    $user_id .
    "&status_updated=1"
);

exit;
?>