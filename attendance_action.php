<?php
date_default_timezone_set("Asia/Manila");
include("connect.php");

header("Content-Type: application/json");

if(
    !isset($_POST['member_code']) ||
    !isset($_POST['action'])
){

    echo json_encode([
        "status"=>"error",
        "message"=>"Invalid attendance request."
    ]);

    exit();

}

$memberCode = trim($_POST['member_code']);
$action = $_POST['action'];


/* Validate QR code */

if(!preg_match('/^M\d{5}$/',$memberCode)){

    echo json_encode([
        "status"=>"error",
        "message"=>"Invalid member QR code."
    ]);

    exit();

}


/* Validate action */

if(
    $action !== "check_in" &&
    $action !== "check_out"
){

    echo json_encode([
        "status"=>"error",
        "message"=>"Invalid attendance action."
    ]);

    exit();

}


$memberID =
    intval(substr($memberCode,1));


/* Check member */

$stmt = $con->prepare("
SELECT id
FROM signup
WHERE id=?
LIMIT 1
");

$stmt->bind_param(
    "i",
    $memberID
);

$stmt->execute();

$result = $stmt->get_result();


if($result->num_rows === 0){

    echo json_encode([
        "status"=>"error",
        "message"=>"Member not found."
    ]);

    exit();

}


/* Check membership */

$stmt = $con->prepare("
SELECT status, start_date, end_date
FROM membership
WHERE member_id=?
ORDER BY membership_id DESC
LIMIT 1
");

$stmt->bind_param(
    "i",
    $memberID
);

$stmt->execute();

$membership =
    $stmt->get_result()->fetch_assoc();


if(!$membership){

    echo json_encode([
        "status"=>"error",
        "message"=>"Member has no membership."
    ]);

    exit();

}


/* Membership must be active */

if($membership['status'] !== "Active"){

    echo json_encode([
        "status"=>"error",
        "message"=>"Member membership is not active."
    ]);

    exit();

}


/* Check expiration */

if(
    !empty($membership['end_date']) &&
    strtotime($membership['end_date']) <
    strtotime(date("Y-m-d"))
){

    echo json_encode([
        "status"=>"error",
        "message"=>"Membership has expired."
    ]);

    exit();

}


$manilaTime = new DateTime(
    "now",
    new DateTimeZone("Asia/Manila")
);

$today = $manilaTime->format("Y-m-d");
$currentTime = $manilaTime->format("H:i:s");


/* Get today's attendance */

$stmt = $con->prepare("
SELECT *
FROM attendance
WHERE member_id=?
AND attendance_date=?
LIMIT 1
");

$stmt->bind_param(
    "is",
    $memberID,
    $today
);

$stmt->execute();

$attendance =
    $stmt->get_result();


if($action === "check_in"){

    if($attendance->num_rows > 0){

        $row =
            $attendance->fetch_assoc();

        if(!empty($row['check_in'])){

            echo json_encode([
                "status"=>"error",
                "message"=>"Member is already checked in today."
            ]);

            exit();

        }

    }


    $status = "Present";


    $stmt = $con->prepare("
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
    ?,
    ?
)
    ");

    $stmt->bind_param(
    "isss",
    $memberID,
    $currentTime,
    $today,
    $status
);  


    if($stmt->execute()){

        echo json_encode([
            "status"=>"success",
            "action"=>"check_in",
            "time"=>$currentTime
        ]);

        exit();

    }


    echo json_encode([
        "status"=>"error",
        "message"=>"Unable to record check-in."
    ]);

    exit();

}


/* ===========================
   CHECK OUT
=========================== */

if($attendance->num_rows === 0){

    echo json_encode([
        "status"=>"error",
        "message"=>"Member has not checked in today."
    ]);

    exit();

}


$row =
    $attendance->fetch_assoc();


if(empty($row['check_in'])){

    echo json_encode([
        "status"=>"error",
        "message"=>"Member has not checked in today."
    ]);

    exit();

}


if(!empty($row['check_out'])){

    echo json_encode([
        "status"=>"error",
        "message"=>"Member is already checked out today."
    ]);

    exit();

}


$stmt = $con->prepare("
UPDATE attendance
SET check_out=?
WHERE attendance_id=?
");

$stmt->bind_param(
    "si",
    $currentTime,
    $row['attendance_id']
);


if($stmt->execute()){

    echo json_encode([
        "status"=>"success",
        "action"=>"check_out",
        "time"=>$currentTime
    ]);

    exit();

}


echo json_encode([
    "status"=>"error",
    "message"=>"Unable to record check-out."
]);

exit();
