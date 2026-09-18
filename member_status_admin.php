<?php

require_once("includes/admin_auth.php");
require_once("includes/activity_logger.php");

if(!isset($_GET['id']) || !is_numeric($_GET['id'])){

    header("Location: members_admin.php");
    exit();
}

$member_id = (int)$_GET['id'];

$stmt = $con->prepare("
    SELECT
        id,
        first_name,
        last_name,
        status
    FROM signup
    WHERE id = ?
    LIMIT 1
");

if(!$stmt){
    die("SQL Error: " . $con->error);
}

$stmt->bind_param("i", $member_id);
$stmt->execute();

$result = $stmt->get_result();

if($result->num_rows == 0){

    header("Location: members_admin.php");
    exit();
}

$member = $result->fetch_assoc();
$stmt->close();

if($member['status'] === "Active"){

    $newStatus = "Inactive";

}else{

    $newStatus = "Active";
}

$update = $con->prepare("
    UPDATE signup
    SET status = ?
    WHERE id = ?
");

$update->bind_param(
    "si",
    $newStatus,
    $member_id
);

if(!$update->execute()){

    die("Unable to update member status: " . $update->error);
}

$action = ($newStatus === "Active")
    ? "Member Activation"
    : "Member Deactivation";

logActivity(
    $con,
    $action,
    $member['first_name'] . " " . $member['last_name'] . " member account was set to " . $newStatus . ".",
    $_SESSION['admin_id'] ?? null,
    "Admin"
);

header(
    "Location: member_view_admin.php?id=" . $member_id
);

exit();

?>