<?php
session_start();

include("connect.php");
require_once("includes/activity_logger.php");

$userId = $_SESSION['user_id'] ?? null;
$firstname = $_SESSION['firstname'] ?? "Member";

if($userId !== null){

    logActivity(
        $con,
        "Member Logout",
        $firstname . " logged out successfully.",
        $userId,
        "Member"
    );

}

session_destroy();

header("Location: login.php");
exit();
?>