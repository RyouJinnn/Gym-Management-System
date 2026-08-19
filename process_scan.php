<?php
include("connect.php");

header("Content-Type: application/json");

if(!isset($_POST['member_code'])){
    echo json_encode([
        "status"=>"error",
        "message"=>"No QR Code received."
    ]);
    exit();
}

$memberCode = trim($_POST['member_code']);

if(!preg_match('/^M\d{5}$/',$memberCode)){
    echo json_encode([
        "status"=>"error",
        "message"=>"Invalid QR Code."
    ]);
    exit();
}

$memberID = intval(substr($memberCode,1));

/* Get member and active membership */
$stmt = $con->prepare("
SELECT
    signup.id,
    signup.first_name,
    signup.last_name,
    signup.profile_picture,
    membership.plan_name,
    membership.status,
    membership.start_date,
    membership.end_date
FROM signup
LEFT JOIN membership
ON signup.id = membership.member_id
WHERE signup.id=?
ORDER BY membership.membership_id DESC
LIMIT 1
");

$stmt->bind_param("i",$memberID);
$stmt->execute();

$result = $stmt->get_result();

if($result->num_rows==0){

    echo json_encode([
        "status"=>"not_found",
        "message"=>"Member not found."
    ]);

    exit();

}

$member = $result->fetch_assoc();
/* No membership record */

if(empty($member['plan_name'])){

    echo json_encode([
        "status"=>"no_membership",
        "message"=>"Member has no membership."
    ]);

    exit();

}

/* Pending */

if($member['status']=="Pending"){

    echo json_encode([
        "status"=>"pending",
        "message"=>"Membership is waiting for approval."
    ]);

    exit();

}

/* Inactive */

if($member['status']=="Inactive"){

    echo json_encode([
        "status"=>"inactive",
        "message"=>"Membership is inactive."
    ]);

    exit();

}

/* Expired */

if(strtotime($member['end_date']) < strtotime(date("Y-m-d"))){

    echo json_encode([
        "status"=>"expired",
        "message"=>"Membership has expired."
    ]);

    exit();

}

/* Check today's attendance */

$stmt = $con->prepare("
SELECT *
FROM attendance
WHERE member_id=?
AND attendance_date=CURDATE()
LIMIT 1
");

$stmt->bind_param("i",$memberID);
$stmt->execute();

$attendance = $stmt->get_result();

$currentTime = date("H:i:s");

if($attendance->num_rows==0){

    /* FIRST SCAN (CHECK-IN) */

    $status="Present";

    $stmt=$con->prepare("
    INSERT INTO attendance
    (
        member_id,
        check_in,
        attendance_date,
        status
    )
    VALUES
    (
        ?,
        ?,
        CURDATE(),
        ?
    )
    ");

    $stmt->bind_param(
    "iss",
    $memberID,
    $currentTime,
    $status
    );

    $stmt->execute();

    echo json_encode([
        "status"=>"check_in",
        "member"=>$member,
        "time"=>$currentTime
    ]);

    exit();

}

$row = $attendance->fetch_assoc();

if(empty($row['check_out'])){

    /* SECOND SCAN (CHECK-OUT) */

    $stmt=$con->prepare("
    UPDATE attendance
    SET check_out=?
    WHERE attendance_id=?
    ");

    $stmt->bind_param(
    "si",
    $currentTime,
    $row['attendance_id']
    );

    $stmt->execute();

    echo json_encode([
        "status"=>"check_out",
        "member"=>$member,
        "time"=>$currentTime
    ]);

    exit();

}

/* THIRD SCAN */

echo json_encode([
    "status"=>"completed",
    "member"=>$member
]);

exit();