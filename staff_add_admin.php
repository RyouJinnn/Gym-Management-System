<?php

require_once("includes/admin_auth.php");

$error = "";
$success = "";


if($_SERVER["REQUEST_METHOD"] === "POST"){

    $full_name = trim($_POST['full_name'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $profile_picture = "";


    /* ===========================
       VALIDATION
    =========================== */

    if($full_name === ""){

        $error = "Please enter the staff full name.";

    }
    elseif($password === ""){

        $error = "Please enter a password.";

    }

    elseif(strlen($password) < 8){

        $error = "Password must be at least 8 characters.";

    }

    elseif(
        !preg_match('/[A-Z]/', $password) ||
        !preg_match('/[a-z]/', $password) ||
        !preg_match('/[0-9]/', $password) ||
        !preg_match('/[\W_]/', $password)
    ){

        $error =
        "Password must contain uppercase, lowercase, number, and special character.";

    }

    elseif($password !== $confirm_password){

        $error = "Passwords do not match.";

    }
    /* ===========================
   PROFILE PICTURE
=========================== */

if($error === ""){

    if(
        isset($_FILES['profile_picture']) &&
        $_FILES['profile_picture']['error'] !== UPLOAD_ERR_NO_FILE
    ){

        if(
            $_FILES['profile_picture']['error']
            !== UPLOAD_ERR_OK
        ){

            $error =
                "There was a problem uploading the profile picture.";

        }
        else{

            $allowed_types = [
                'image/jpeg',
                'image/png',
                'image/webp'
            ];

            $file_type = mime_content_type(
                $_FILES['profile_picture']['tmp_name']
            );


            if(!in_array($file_type, $allowed_types, true)){

                $error =
                    "Only JPG, PNG, and WEBP images are allowed.";

            }
            elseif(
                $_FILES['profile_picture']['size']
                > 5 * 1024 * 1024
            ){

                $error =
                    "Profile picture must not exceed 5MB.";

            }
            else{

                $extension = strtolower(
                    pathinfo(
                        $_FILES['profile_picture']['name'],
                        PATHINFO_EXTENSION
                    )
                );


                $new_filename =
                    'staff_new_' .
                    time() .
                    '_' .
                    bin2hex(random_bytes(4)) .
                    '.' .
                    $extension;


                $upload_directory =
                    __DIR__ . '/uploads/';


                if(!is_dir($upload_directory)){

                    mkdir(
                        $upload_directory,
                        0755,
                        true
                    );

                }


                $upload_path =
                    $upload_directory .
                    $new_filename;


                if(
                    move_uploaded_file(
                        $_FILES['profile_picture']['tmp_name'],
                        $upload_path
                    )
                ){

                    $profile_picture =
                        $new_filename;

                }
                else{

                    $error =
                        "Unable to save the profile picture.";

                }

            }

        }

    }

}


/* ===========================
   INSERT STAFF
=========================== */

if($error === ""){

    $hashedPassword = password_hash(
        $password,
        PASSWORD_DEFAULT
    );


    $stmt = $con->prepare("
        INSERT INTO users
        (
            full_name,
            password,
            role,
            status,
            profile_picture
        )
        VALUES
        (
            ?,
            ?,
            'Staff',
            'Active',
            ?
        )
    ");


    if($stmt === false){

        die("SQL Error: " . $con->error);

    }


    $stmt->bind_param(
        "ssss",
        $full_name,
        $hashedPassword,
        $profile_picture
    );


    if($stmt->execute()){

        header(
            "Location: staff_admin.php?added=1"
        );

        exit;

    }
    else{

        $error =
            "Unable to create staff account.";

    }


    $stmt->close();

    }

}

?>


<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Add Staff | Admin Panel</title>


<link
href="https://fonts.googleapis.com/css2?family=Orbitron:wght@600;700&family=Poppins:wght@300;400;500;600;700&display=swap"
rel="stylesheet">


<link
rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">


<link
rel="stylesheet"
href="assets/css/admin.css">

</head>


<body>


<div class="wrapper">


<?php include("includes/admin_sidebar.php"); ?>


<div class="main">

<div class="staff-form-content">


    <div class="staff-form-header">

        <div>

            <h2>
                Add Staff
            </h2>

            <p>
                Create a new staff account.
            </p>

        </div>


        <a
            href="staff_admin.php"
            class="back-staff-btn"
        >

            <i class="fa-solid fa-arrow-left"></i>

            Back to Staff

        </a>

    </div>


    <?php if($error !== ""): ?>

        <div class="staff-error">

            <i class="fa-solid fa-circle-exclamation"></i>

            <?php echo htmlspecialchars($error); ?>

        </div>

    <?php endif; ?>


    <div class="staff-form-card">


        <form
    method="POST"
    action=""
    enctype="multipart/form-data"
    onsubmit="return confirmCreateStaff();"
>

<!-- PROFILE PICTURE -->

<div class="form-group staff-profile-picture-group">

    <label>
        Profile Picture
    </label>


    <div class="staff-picture-upload">

        <div class="staff-picture-preview">

            <div
                class="staff-picture-placeholder"
                id="staffPicturePlaceholder"
            >

                <i class="fa-solid fa-user"></i>

            </div>

        </div>


        <div class="staff-picture-controls">

            <label
                for="profile_picture"
                class="staff-upload-btn"
            >

                <i class="fa-solid fa-camera"></i>

                Choose Profile Picture

            </label>


            <input
                type="file"
                id="profile_picture"
                name="profile_picture"
                accept="image/jpeg,image/png,image/webp"
                hidden
                onchange="previewStaffPicture(event)"
            >


            <small>
                JPG, PNG, or WEBP. Maximum 5MB.
            </small>

        </div>

    </div>

</div>


            <!-- FULL NAME -->

            <div class="form-group">

                <label for="full_name">

                    Full Name

                </label>

                <div class="form-input">

                    <i class="fa-solid fa-user"></i>

                    <input
                        type="text"
                        id="full_name"
                        name="full_name"
                        placeholder="Enter full name"
                        value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>"
                        required
                    >

                </div>

            </div>
            <!-- PASSWORD -->

            <div class="form-group">

                <label for="password">

                    Password

                </label>

                <div class="form-input">

                    <i class="fa-solid fa-lock"></i>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Enter password"
                        required
                    >

                    <button
                        type="button"
                        class="password-toggle"
                        onclick="togglePassword('password', this)"
                    >

                        <i class="fa-solid fa-eye"></i>

                    </button>

                </div>

                <small>
                    Must contain uppercase, lowercase,
                    number, and special character.
                </small>

            </div>


            <!-- CONFIRM PASSWORD -->

            <div class="form-group">

                <label for="confirm_password">

                    Confirm Password

                </label>

                <div class="form-input">

                    <i class="fa-solid fa-lock"></i>

                    <input
                        type="password"
                        id="confirm_password"
                        name="confirm_password"
                        placeholder="Confirm password"
                        required
                    >

                    <button
                        type="button"
                        class="password-toggle"
                        onclick="togglePassword('confirm_password', this)"
                    >

                        <i class="fa-solid fa-eye"></i>

                    </button>

                </div>

            </div>


            <!-- ROLE -->

            <div class="form-group">

                <label>

                    Role

                </label>

                <div class="role-display">

                    <i class="fa-solid fa-user-tie"></i>

                    Staff

                </div>

            </div>


            <!-- BUTTONS -->

            <div class="staff-form-buttons">


                <a
                    href="staff_admin.php"
                    class="cancel-staff-btn"
                >

                    Cancel

                </a>


                <button
                    type="submit"
                    class="save-staff-btn"
                >

                    <i class="fa-solid fa-user-plus"></i>

                    Create Staff

                </button>


            </div>


        </form>


    </div>


</div>


</div>


</div>

<!-- ==========================
     CREATE STAFF CONFIRMATION
========================== -->

<div id="createStaffModal" class="staff-modal">

    <div class="staff-modal-box">

        <div class="staff-modal-icon">

            <i class="fa-solid fa-user-plus"></i>

        </div>

        <h3>
            Confirm Staff Creation
        </h3>

        <p>
            Are you sure you want to create this staff account?
        </p>

        <div class="staff-modal-buttons">

            <button
                type="button"
                class="modal-no"
                onclick="closeCreateStaffModal()"
            >
                No
            </button>

           <button
    type="button"
    class="modal-yes"
    onclick="submitCreateStaff()"
>
    Yes, Create Staff
</button>

        </div>

    </div>

</div>


<script>

function togglePassword(id, button){

    const input =
        document.getElementById(id);

    const icon =
        button.querySelector("i");


    if(input.type === "password"){

        input.type = "text";

        icon.classList.remove(
            "fa-eye"
        );

        icon.classList.add(
            "fa-eye-slash"
        );

    }
    else{

        input.type = "password";

        icon.classList.remove(
            "fa-eye-slash"
        );

        icon.classList.add(
            "fa-eye"
        );

    }

}

let createStaffForm = null;


function confirmCreateStaff(){

    createStaffForm = document.querySelector(
        '.staff-form-card form'
    );

    document.getElementById(
        'createStaffModal'
    ).classList.add('show');

    return false;

}


function closeCreateStaffModal(){

    document.getElementById(
        'createStaffModal'
    ).classList.remove('show');

}


function submitCreateStaff(){

    if(createStaffForm){

        createStaffForm.removeAttribute(
            'onsubmit'
        );

        createStaffForm.submit();

    }

}

function previewStaffPicture(event){

    const file = event.target.files[0];

    if(!file){
        return;
    }


    const allowedTypes = [
        "image/jpeg",
        "image/png",
        "image/webp"
    ];


    if(!allowedTypes.includes(file.type)){

        alert(
            "Only JPG, PNG, and WEBP images are allowed."
        );

        event.target.value = "";

        return;

    }


    if(file.size > 5 * 1024 * 1024){

        alert(
            "Profile picture must not exceed 5MB."
        );

        event.target.value = "";

        return;

    }


    const reader = new FileReader();


    reader.onload = function(e){

        const preview =
            document.querySelector(
                ".staff-picture-preview"
            );


        preview.innerHTML =
            '<img src="' +
            e.target.result +
            '" ' +
            'alt="Staff Profile Picture">';

    };


    reader.readAsDataURL(file);

}

</script>

</body>
</html>