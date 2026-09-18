<?php
session_start();

include("connect.php");
require_once("includes/activity_logger.php");

$staffId = $_SESSION['staff_id'] ?? null;
$staffName = $_SESSION['staff_name'] ?? "Staff";

if($staffId !== null){

    logActivity(
        $con,
        "Staff Logout",
        $staffName . " logged out successfully.",
        $staffId,
        "Staff"
    );

}

$_SESSION = [];

session_destroy();

header("Location: login_staff.php");
exit();