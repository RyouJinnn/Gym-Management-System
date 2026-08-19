<?php

require_once("includes/admin_auth.php");
/* ===========================
   CHECK MEMBER ID
=========================== */

if(!isset($_GET['id']) || !is_numeric($_GET['id'])){

    header("Location: members_admin.php");
    exit();

}

$member_id = (int)$_GET['id'];


/* ===========================
   CHECK CURRENT MEMBER
=========================== */

$stmt = $con->prepare("
    SELECT id, status
    FROM signup
    WHERE id = ?
    LIMIT 1
");

$stmt->bind_param("i", $member_id);
$stmt->execute();

$result = $stmt->get_result();

if($result->num_rows == 0){

    header("Location: members_admin.php");
    exit();

}

$member = $result->fetch_assoc();


/* ===========================
   CHANGE STATUS
=========================== */

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

$update->execute();


/* ===========================
   RETURN TO PROFILE
=========================== */

header(
    "Location: member_view_admin.php?id=" . $member_id
);

exit();

?>