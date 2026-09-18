<?php
session_start();

include("connect.php");
require_once("includes/activity_logger.php");

$adminId = $_SESSION['admin_id'] ?? null;
$adminName = $_SESSION['admin_name'] ?? "Admin";

if($adminId !== null){

    logActivity(
        $con,
        "Admin Logout",
        $adminName . " logged out successfully.",
        $adminId,
        "Admin"
    );

}

$_SESSION = [];

session_destroy();

header("Location: login_admin.php");
exit();