<?php
session_start();
include("connect.php");

if(isset($_SESSION['admin_id'])){
    header("Location: dashboard_admin.php");
    exit();
}

$error = "";

if(isset($_POST['login'])){

    $full_name = trim($_POST['full_name']);
    $password = trim($_POST['password']);

    $stmt = $con->prepare("
        SELECT *
        FROM users
        WHERE full_name = ?
        LIMIT 1
    ");

    $stmt->bind_param("s",$full_name);
    $stmt->execute();

    $result = $stmt->get_result();

    if($result->num_rows > 0){

        $admin = $result->fetch_assoc();

        if(
            $admin['role'] === "Admin" &&
            $admin['status'] === "Active" &&
            password_verify($password,$admin['password'])
        ){

            $_SESSION['admin_id'] = $admin['user_id'];
            $_SESSION['admin_name'] = $admin['full_name'];

            header("Location: dashboard_admin.php");
            exit();

        }else{

            $error = "Invalid username or password.";

        }

    }else{

        $error = "Invalid username or password.";

    }

}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Administrator Login</title>

<link rel="preconnect"
href="https://fonts.googleapis.com">

<link rel="preconnect"
href="https://fonts.gstatic.com"
crossorigin>

<link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@700&family=Poppins:wght@300;400;500;600;700&display=swap"
rel="stylesheet">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

<link rel="stylesheet"
href="assets/css/admin.css">

</head>

<body>
    <div class="admin-login-container">

    <div class="login-card">

        <img src="logofit.png" class="login-logo">

        <h1>ADMIN</h1>

        <p>Fit Function Gym Administrator Portal</p>

        <?php if($error!=""){ ?>

        <div class="error-box">

            <?php echo $error; ?>

        </div>

        <?php } ?>

        <form method="POST">

            <div class="input-box">

                <i class="fa-solid fa-user"></i>

                <input
                type="text"
                name="full_name"
                placeholder="Username"
                required>

            </div>

            <div class="input-box">

                <i class="fa-solid fa-lock"></i>

                <input
                type="password"
                name="password"
                id="password"
                placeholder="Password"
                required>

                <i
                class="fa-solid fa-eye"
                id="togglePassword"></i>

            </div>

            <button
            type="submit"
            name="login">

                Login

            </button>

        </form>

    </div>

</div>
</body>

<script>

const password =
document.getElementById("password");

const toggle =
document.getElementById("togglePassword");

toggle.onclick=function(){

    if(password.type==="password"){

        password.type="text";

        toggle.classList.remove("fa-eye");

        toggle.classList.add("fa-eye-slash");

    }else{

        password.type="password";

        toggle.classList.remove("fa-eye-slash");

        toggle.classList.add("fa-eye");

    }

}

</script>

</body>

</html>