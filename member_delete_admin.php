<?php

require_once("includes/admin_auth.php");


// Only allow POST requests
if (
    $_SERVER["REQUEST_METHOD"] !== "POST" ||
    !isset($_POST['member_id'])
) {
    header("Location: members_admin.php");
    exit;
}


$member_id = (int) $_POST['member_id'];


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

    // Make sure a member was actually deleted
    if ($stmt->affected_rows === 0) {
        throw new Exception("Member not found.");
    }

    $stmt->close();


    // Everything succeeded
    $con->commit();


    header("Location: members_admin.php?deleted=1");
    exit;


} catch (Exception $e) {

    // Undo everything if something goes wrong
    $con->rollback();

    die("Unable to delete member: " . htmlspecialchars($e->getMessage()));
}

?>