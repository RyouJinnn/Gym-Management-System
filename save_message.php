<?php
session_start();

$conn = new mysqli("localhost", "root", "", "gym_signup");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

require_once("includes/activity_logger.php");

$fullname = trim($_POST['fullname']);
$email    = trim($_POST['email']);
$subject  = trim($_POST['subject']);
$message  = trim($_POST['message']);

$sql = "INSERT INTO contact_messages (full_name, email, subject, message)
        VALUES (?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $fullname, $email, $subject, $message);

if($stmt->execute()){

    logActivity(
        $conn,
        "Contact Message Submission",
        "Contact message from " . $fullname .
        " with subject \"" . $subject . "\" was submitted.",
        null,
        "Visitor"
    );

    $_SESSION['success_message'] = "Your message has been submitted successfully!";

}else{

    $_SESSION['success_message'] = "Failed to send your message.";

}

$stmt->close();
$conn->close();

header("Location: contact.php");
exit();
?>