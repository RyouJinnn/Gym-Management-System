<?php

require_once("includes/admin_auth.php");
require_once("includes/activity_logger.php");

if (
    $_SERVER["REQUEST_METHOD"] !== "POST" ||
    !isset($_POST['plan_id'])
) {

    header("Location: membership_plans_admin.php");
    exit;

}


$plan_id = (int) $_POST['plan_id'];


if ($plan_id <= 0) {

    header("Location: membership_plans_admin.php");
    exit;

}

$nameStmt = $con->prepare("
    SELECT plan_name
    FROM membership_plans
    WHERE plan_id = ?
    LIMIT 1
");

$nameStmt->bind_param("i", $plan_id);
$nameStmt->execute();

$nameResult = $nameStmt->get_result();
$plan = $nameResult->fetch_assoc();

$nameStmt->close();

$plan_name = $plan['plan_name'] ?? "Unknown membership plan";

$stmt = $con->prepare("
    DELETE FROM membership_plans
    WHERE plan_id = ?
");


if ($stmt === false) {

    die("SQL Error: " . $con->error);

}


$stmt->bind_param(
    "i",
    $plan_id
);


if (!$stmt->execute()) {

    die("Delete Error: " . $stmt->error);

}

$stmt->close();

logActivity(
    $con,
    "Membership Plan Deleted",
    $plan_name . " membership plan was deleted from the system.",
    $_SESSION['admin_id'] ?? null,
    "Admin"
);

header(
    "Location: membership_plans_admin.php?deleted=1"
);

exit;

?>