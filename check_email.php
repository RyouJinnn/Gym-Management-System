<?php
include("connect.php");

if(isset($_POST['email'])){

    $email = trim($_POST['email']);

    $stmt = $con->prepare("SELECT 1 FROM signup WHERE email=? LIMIT 1");

    if(!$stmt){
        die("Prepare Error: ".$con->error);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    echo ($stmt->num_rows > 0) ? "exists" : "not_exists";

    $stmt->close();
    $con->close();
}
?>