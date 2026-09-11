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

/* ===========================
   GET MEMBER INFORMATION
=========================== */

$stmt = $con->prepare("
SELECT
    signup.id,
    signup.first_name,
    signup.middlename,
    signup.last_name,
    signup.suffix,
    signup.email,
    signup.contact_number,
    signup.gender,
    signup.birthdate,
    signup.address,
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


/* ===========================
   NO MEMBERSHIP
=========================== */

if(empty($member['plan_name'])){

    echo json_encode([
        "status"=>"no_membership",
        "message"=>"Member has no membership."
    ]);

    exit();

}


/* ===========================
   PENDING
=========================== */

if($member['status']=="Pending"){

    echo json_encode([
        "status"=>"pending",
        "message"=>"Membership is waiting for approval."
    ]);

    exit();

}


/* ===========================
   INACTIVE
=========================== */

if($member['status']=="Inactive"){

    echo json_encode([
        "status"=>"inactive",
        "message"=>"Membership is inactive."
    ]);

    exit();

}


/* ===========================
   EXPIRED
=========================== */

if(
    empty($member['end_date']) ||
    strtotime($member['end_date']) < strtotime(date("Y-m-d"))
){

    echo json_encode([
        "status"=>"expired",
        "message"=>"Membership has expired."
    ]);

    exit();

}


/* ===========================
   CHECK TODAY'S ATTENDANCE
=========================== */

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


/* ===========================
   DETERMINE AVAILABLE ACTION
=========================== */

if($attendance->num_rows==0){

    echo json_encode([
        "status"=>"ready_check_in",
        "member"=>$member
    ]);

    exit();

}

$row = $attendance->fetch_assoc();


if(empty($row['check_out'])){

    echo json_encode([
        "status"=>"ready_check_out",
        "member"=>$member,
        "check_in"=>$row['check_in']
    ]);

    exit();

}

echo json_encode([
    "status"=>"completed",
    "member"=>$member,
    "check_in"=>$row['check_in'],
    "check_out"=>$row['check_out']
]);

exit();
