<?php

require_once("includes/admin_auth.php");


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


/* ==============================
   DELETE MEMBERSHIP PLAN
============================== */

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


/* ==============================
   RETURN TO MEMBERSHIP PLANS
============================== */

header(
    "Location: membership_plans_admin.php?deleted=1"
);

exit;

?>