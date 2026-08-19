<?php

if(session_status() === PHP_SESSION_NONE){
    session_start();
}

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");

include(__DIR__ . "/../connect.php");

if(!isset($_SESSION['staff_id'])){

    header("Location: login_staff.php");
    exit();

}

$stmt = $con->prepare("
SELECT *
FROM users
WHERE user_id = ?
LIMIT 1
");

$stmt->bind_param("i", $_SESSION['staff_id']);
$stmt->execute();

$result = $stmt->get_result();

if($result->num_rows == 0){

    session_destroy();

    header("Location: login_staff.php");
    exit();

}

$staff = $result->fetch_assoc();

if($staff['role'] !== "Staff"){

    session_destroy();

    header("Location: login_staff.php");
    exit();

}

if($staff['status'] !== "Active"){

    session_destroy();

    header("Location: login_staff.php");
    exit();

}

$_SESSION['staff_name'] = $staff['full_name'];
$_SESSION['staff_role'] = $staff['role'];
$_SESSION['staff_status'] = $staff['status'];

?>