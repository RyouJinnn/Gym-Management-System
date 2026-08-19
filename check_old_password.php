<?php
session_start();
include("connect.php");

if(!isset($_SESSION['reset_password'])){
    exit("invalid");
}

$userId = $_SESSION['reset_password']['id'];
$password = $_POST['password'];

$stmt = $con->prepare("
SELECT password
FROM signup
WHERE id=?
");

$stmt->bind_param("i",$userId);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

if(password_verify($password,$user['password'])){
    echo "same";
}else{
    echo "different";
}

$stmt->close();
$con->close();
?>