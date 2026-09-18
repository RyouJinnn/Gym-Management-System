<?php

require_once("includes/admin_auth.php");
require_once("includes/activity_logger.php");

if (
    $_SERVER["REQUEST_METHOD"] !== "POST" ||
    !isset($_POST['member_id'])
) {
    header("Location: members_admin.php");
    exit;
}


$member_id = (int) $_POST['member_id'];

$stmt = $con->prepare("
    SELECT first_name, last_name
    FROM signup
    WHERE id = ?
    LIMIT 1
");

$stmt->bind_param("i", $member_id);
$stmt->execute();

$memberResult = $stmt->get_result();
$memberData = $memberResult->fetch_assoc();

$stmt->close();

if (!$memberData) {
    header("Location: members_admin.php");
    exit;
}

$memberName = $memberData['first_name'] . " " . $memberData['last_name'];

// Check valid ID
if ($member_id <= 0) {
    header("Location: members_admin.php");
    exit;
}


// Start transaction
$con->begin_transaction();


try {

    // 1. Delete attendance records
    $stmt = $con->prepare("
        DELETE FROM attendance
        WHERE member_id = ?
    ");

    if ($stmt === false) {
        throw new Exception($con->error);
    }

    $stmt->bind_param("i", $member_id);
    $stmt->execute();
    $stmt->close();


    // 2. Delete payment records
    $stmt = $con->prepare("
        DELETE FROM payments
        WHERE member_id = ?
    ");

    if ($stmt === false) {
        throw new Exception($con->error);
    }

    $stmt->bind_param("i", $member_id);
    $stmt->execute();
    $stmt->close();


    // 3. Delete membership records
    $stmt = $con->prepare("
        DELETE FROM membership
        WHERE member_id = ?
    ");

    if ($stmt === false) {
        throw new Exception($con->error);
    }

    $stmt->bind_param("i", $member_id);
    $stmt->execute();
    $stmt->close();


    // 4. Finally delete the member
    $stmt = $con->prepare("
        DELETE FROM signup
        WHERE id = ?
    ");

    if ($stmt === false) {
        throw new Exception($con->error);
    }

    $stmt->bind_param("i", $member_id);
    $stmt->execute();

    if ($stmt->affected_rows === 0) {
        throw new Exception("Member not found.");
    }

    $stmt->close();

    $con->commit();
logActivity(
    $con,
    "Member Deletion",
    $memberName . " was deleted from the system.",
    $_SESSION['admin_id'] ?? null,
    "Admin"
);

header("Location: members_admin.php?deleted=1");
    exit;


} catch (Exception $e) {

    $con->rollback();
    die("Unable to delete member: " . htmlspecialchars($e->getMessage()));
}
?>