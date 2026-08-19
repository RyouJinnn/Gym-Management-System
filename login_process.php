<?php
session_start();
include("connect.php");

if(isset($_POST['btn_login'])){

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $_SESSION['login_email'] = $email;

    $stmt = $con->prepare("
        SELECT *
        FROM signup
        WHERE email=?
        LIMIT 1
    ");

    if(!$stmt){
        die("Prepare Error: ".$con->error);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();

    if($result->num_rows == 1){

        $row = $result->fetch_assoc();

        if(password_verify($password, $row['password'])){

        if($row['email_verified'] != 1){

        $_SESSION['verify_email'] = $row['email'];
        header("Location: verify.php");
        exit();
    }

            $code = rand(100000,999999);

            $expiry = date(
                "Y-m-d H:i:s",
                strtotime("+1 minute    ")
            );

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
            
            $_SESSION['login_verify'] = [

                "id" => $row['id'],
                "firstname" => $row['first_name'],
                "email" => $row['email']

            ];

            $firstname = $row['first_name'];
            $email = $row['email'];

            require("send_login_code.php");

            unset($_SESSION['login_email']);

            header("Location: login_verify.php");
            exit();

        }else{

            $_SESSION['login_error'] = "Incorrect password.";
            header("Location: login.php");
            exit();

        }

    }else{

        $_SESSION['login_error'] = "Email address does not exist.";
        header("Location: login.php");
        exit();

    }

}else{

    header("Location: login.php");
    exit();

}
?>