<?php
session_start();
include("connect.php");

if(isset($_POST['email'])){

    $email = trim($_POST['email']);

    // Check if email exists
    $stmt = $con->prepare("
        SELECT *
        FROM signup
        WHERE email=?
        LIMIT 1
    ");

    if(!$stmt){
        die("Prepare Error: ".$con->error);
    }

    $stmt->bind_param("s",$email);
    $stmt->execute();

    $result = $stmt->get_result();

    if($result->num_rows != 1){

        echo "<script>
        alert('This email does not exist.');
        window.location='forgot.php';
        </script>";
        exit();

    }

    $row = $result->fetch_assoc();

    // Generate verification code
    $code = rand(100000,999999);

    $expiry = date(
        "Y-m-d H:i:s",
        strtotime("+10 minutes")
    );

    // Save code
    $update = $con->prepare("
        UPDATE signup
        SET
            verification_code=?,
            verification_expiry=?
        WHERE id=?
    ");

    if(!$update){
        die("Prepare Error: ".$con->error);
    }

    $update->bind_param(
        "ssi",
        $code,
        $expiry,
        $row['id']
    );

    if(!$update->execute()){
        die("Database Error: ".$update->error);
    }

    $update->close();

    // Save session
    $_SESSION['forgot'] = [

        "id" => $row['id'],
        "firstname" => $row['first_name'],
        "email" => $row['email']

    ];

    // Variables used by send_forgot_code.php
    $firstname = $row['first_name'];
    $email = $row['email'];

    require("send_forgot_code.php");

    header("Location: forgot_verify.php");
    exit();

}else{

    header("Location: forgot.php");
    exit();

}
?>