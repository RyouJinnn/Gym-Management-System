<?php

if(session_status() === PHP_SESSION_NONE){
    session_start();
}

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");

include(__DIR__ . "/../connect.php");

if(!isset($_SESSION['admin_id'])){

    header("Location: login_admin.php");
    exit();

}

$stmt = $con->prepare("
SELECT *
FROM users
WHERE user_id = ?
LIMIT 1
");

$stmt->bind_param("i", $_SESSION['admin_id']);
$stmt->execute();

$result = $stmt->get_result();

if($result->num_rows == 0){

    session_destroy();

    header("Location: login_admin.php");
    exit();

}

$admin = $result->fetch_assoc();

if($admin['role'] !== "Admin"){

    session_destroy();

    header("Location: login_admin.php");
    exit();

}

if($admin['status'] !== "Active"){

    session_destroy();

    header("Location: login_admin.php");
    exit();

}

$_SESSION['admin_name'] = $admin['full_name'];
$_SESSION['admin_role'] = $admin['role'];
$_SESSION['admin_status'] = $admin['status'];
?>