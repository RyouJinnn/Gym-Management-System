<?php

require_once("includes/admin_auth.php");
/* ===========================
   CHECK REQUEST
=========================== */

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


/* ===========================
   VALIDATE STATUS
=========================== */

if(
    $status !== "Active" &&
    $status !== "Inactive"
){

    header("Location: staff_admin.php");
    exit;

}


/* ===========================
   UPDATE STAFF STATUS
=========================== */

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


/* ===========================
   RETURN TO STAFF PROFILE
=========================== */

header(
    "Location: staff_view_admin.php?id=" .
    $user_id .
    "&status_updated=1"
);

exit;

?>