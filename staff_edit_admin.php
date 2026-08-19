<?php

require_once("includes/admin_auth.php");

$error = "";


/* ===========================
   GET STAFF ID
=========================== */

if(!isset($_GET['id']) || !is_numeric($_GET['id'])){

    header("Location: staff_admin.php");
    exit;

}

$user_id = (int) $_GET['id'];


/* ===========================
   GET STAFF INFORMATION
=========================== */

$stmt = $con->prepare("
    SELECT
    user_id,
    full_name,
    role,
    status,
    profile_picture
FROM users
    WHERE user_id = ?
    AND role = 'Staff'
    LIMIT 1
");

if($stmt === false){

    die("SQL Error: " . $con->error);

}

$stmt->bind_param("i", $user_id);

$stmt->execute();

$result = $stmt->get_result();

if($result->num_rows === 0){

    $stmt->close();

    header("Location: staff_admin.php");
    exit;

}

$staff = $result->fetch_assoc();

$stmt->close();


/* ===========================
   UPDATE STAFF
=========================== */

if($_SERVER["REQUEST_METHOD"] === "POST"){

    $full_name = trim($_POST['full_name'] ?? '');
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

$profile_picture = $staff['profile_picture'] ?? '';
    /* ===========================
       PROFILE PICTURE UPLOAD
    =========================== */

    if(
        isset($_FILES['profile_picture']) &&
        $_FILES['profile_picture']['error'] !== UPLOAD_ERR_NO_FILE
    ){

        if($_FILES['profile_picture']['error'] !== UPLOAD_ERR_OK){

            $error = "There was a problem uploading the profile picture.";

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

                $error = "Only JPG, PNG, and WEBP images are allowed.";

            }
            elseif($_FILES['profile_picture']['size'] > 5 * 1024 * 1024){

                $error = "Profile picture must not exceed 5MB.";

            }
            else{

                $extension = strtolower(
                    pathinfo(
                        $_FILES['profile_picture']['name'],
                        PATHINFO_EXTENSION
                    )
                );


                $new_filename =
                    'staff_' .
                    $user_id .
                    '_' .
                    time() .
                    '.' .
                    $extension;


                $upload_directory = __DIR__ . '/uploads/';

                $upload_path =
                    $upload_directory .
                    $new_filename;


                if(!is_dir($upload_directory)){

                    mkdir(
                        $upload_directory,
                        0755,
                        true
                    );

                }


                if(
                    move_uploaded_file(
                        $_FILES['profile_picture']['tmp_name'],
                        $upload_path
                    )
                ){

                    /* Delete old picture */
                    if(
                        !empty($staff['profile_picture']) &&
                        file_exists(
                            $upload_directory .
                            $staff['profile_picture']
                        )
                    ){

                        unlink(
                            $upload_directory .
                            $staff['profile_picture']
                        );

                    }


                    $profile_picture = $new_filename;

                }
                else{

                    $error = "Unable to save the profile picture.";

                }

            }

        }

    }


    /* ===========================
       VALIDATION
    =========================== */

    if($full_name === ""){

        $error = "Please enter the staff full name.";

    }

    elseif($new_password !== "" || $confirm_password !== ""){

    if(strlen($new_password) < 8){

        $error = "Password must be at least 8 characters.";

    }

    elseif(
        !preg_match('/[A-Z]/', $new_password) ||
        !preg_match('/[a-z]/', $new_password) ||
        !preg_match('/[0-9]/', $new_password) ||
        !preg_match('/[\W_]/', $new_password)
    ){

        $error = "Password must contain uppercase, lowercase, number, and special character.";

    }

    elseif($new_password !== $confirm_password){

        $error = "Passwords do not match.";

    }

}
    /* ===========================
   UPDATE EXISTING STAFF
=========================== */

if($error === ""){

    if($new_password !== ""){

    $hashed_password = password_hash(
        $new_password,
        PASSWORD_DEFAULT
    );

    $update = $con->prepare("
        UPDATE users
        SET
            full_name = ?,
            profile_picture = ?,
            password = ?
        WHERE user_id = ?
        AND role = 'Staff'
    ");

    $update->bind_param(
        "sssi",
        $full_name,
        $profile_picture,
        $hashed_password,
        $user_id
    );

}
else{

    $update = $con->prepare("
        UPDATE users
        SET
            full_name = ?,
            profile_picture = ?
        WHERE user_id = ?
        AND role = 'Staff'
    ");

    $update->bind_param(
        "ssi",
        $full_name,
        $profile_picture,
        $user_id
    );

}

    if($update === false){

        die("SQL Error: " . $con->error);

    }


    if($update->execute()){

        $update->close();

        header(
            "Location: staff_view_admin.php?id=" .
            $user_id .
            "&updated=1"
        );

        exit;

    }
    else{

        $error = "Unable to update staff information.";

    }


    $update->close();

}


    /* Keep entered values */

    $staff['full_name'] = $full_name;

}

?>


<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>Edit Staff | Admin Panel</title>


<link
    href="https://fonts.googleapis.com/css2?family=Orbitron:wght@600;700&family=Poppins:wght@300;400;500;600;700&display=swap"
    rel="stylesheet"
>


<link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
>


<link
    rel="stylesheet"
    href="assets/css/admin.css"
>

</head>


<body>


<div class="wrapper">


<?php include("includes/admin_sidebar.php"); ?>


<div class="main">

<div class="staff-form-content">


    <!-- PAGE HEADER -->

    <div class="staff-form-header">

        <div>

            <h2>
                Edit Staff
            </h2>

            <p>
                Update staff account information.
            </p>

        </div>


        <a
            href="staff_view_admin.php?id=<?php echo $user_id; ?>"
            class="back-staff-btn"
        >

            <i class="fa-solid fa-arrow-left"></i>

            Back to Profile

        </a>

    </div>


    <!-- ERROR MESSAGE -->

    <?php if($error !== ""): ?>

        <div class="staff-error">

            <i class="fa-solid fa-circle-exclamation"></i>

            <?php echo htmlspecialchars($error); ?>

        </div>

    <?php endif; ?>


    <!-- EDIT FORM -->

    <div class="staff-form-card">


        <form
    method="POST"
    action=""
    enctype="multipart/form-data"
    onsubmit="return confirmStaffUpdate();"
>

<!-- PROFILE PICTURE -->

<div class="form-group staff-profile-picture-group">

    <label>
        Profile Picture
    </label>


    <div class="staff-picture-upload">

        <div class="staff-picture-preview">

            <?php if(!empty($staff['profile_picture'])): ?>

                <img
                    src="uploads/<?php echo htmlspecialchars($staff['profile_picture']); ?>"
                    id="staffPicturePreview"
                    alt="Staff Profile Picture"
                >

            <?php else: ?>

                <div
                    class="staff-picture-placeholder"
                    id="staffPicturePlaceholder"
                >
                    <i class="fa-solid fa-user"></i>
                </div>

            <?php endif; ?>

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
                        value="<?php echo htmlspecialchars($staff['full_name']); ?>"
                        required
                    >

                </div>

            </div>

            <!-- CHANGE PASSWORD -->

<div class="form-group">

    <label for="new_password">
        Change Password
    </label>

    <div class="form-input">

        <i class="fa-solid fa-lock"></i>

        <input
            type="password"
            id="new_password"
            name="new_password"
            placeholder="Enter new password"
        >

        <i
            class="fa-solid fa-eye password-toggle"
            onclick="togglePassword('new_password', this)"
        ></i>

    </div>

    <small>
        Leave blank if you do not want to change the password.
        Minimum 8 characters with uppercase, lowercase,
        number, and special character.
    </small>

</div>


<div class="form-group">

    <label for="confirm_password">
        Confirm New Password
    </label>

    <div class="form-input">

        <i class="fa-solid fa-lock"></i>

        <input
            type="password"
            id="confirm_password"
            name="confirm_password"
            placeholder="Confirm new password"
        >

        <i
            class="fa-solid fa-eye password-toggle"
            onclick="togglePassword('confirm_password', this)"
        ></i>

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


            <!-- STATUS -->

            <div class="form-group">

                <label>
                    Account Status
                </label>


                <div class="role-display">

                    <?php if($staff['status'] === "Active"): ?>

                        <i
                            class="fa-solid fa-circle-check"
                            style="color:#4ade80;"
                        ></i>

                        <span style="color:#4ade80;">
                            Active
                        </span>

                    <?php else: ?>

                        <i
                            class="fa-solid fa-circle-xmark"
                            style="color:#ff6b6b;"
                        ></i>

                        <span style="color:#ff6b6b;">
                            Inactive
                        </span>

                    <?php endif; ?>

                </div>

            </div>


            <!-- BUTTONS -->

            <div class="staff-form-buttons">


                <a
                    href="staff_view_admin.php?id=<?php echo $user_id; ?>"
                    class="cancel-staff-btn"
                >

                    Cancel

                </a>


                <button
                    type="submit"
                    class="save-staff-btn"
                >

                    <i class="fa-solid fa-floppy-disk"></i>

                    Save Changes

                </button>


            </div>


        </form>


    </div>


</div>


</div>


</div>


<!-- ==========================
     CONFIRMATION MODAL
========================== -->

<div
    id="updateStaffModal"
    class="staff-modal"
>


    <div class="staff-modal-box">


        <div class="staff-modal-icon">

            <i class="fa-solid fa-pen"></i>

        </div>


        <h3>
            Confirm Changes
        </h3>


        <p>
            Are you sure you want to save these changes?
        </p>


        <div class="staff-modal-buttons">


            <button
                type="button"
                class="modal-no"
                onclick="closeUpdateStaffModal()"
            >

                No

            </button>


            <button
                type="button"
                class="modal-yes"
                onclick="submitStaffUpdate()"
            >

                Yes

            </button>


        </div>


    </div>

</div>


<script>

let staffUpdateForm = null;


function confirmStaffUpdate(){

    staffUpdateForm = document.querySelector(
        '.staff-form-card form'
    );

    document.getElementById(
        'updateStaffModal'
    ).classList.add('show');

    return false;

}


function closeUpdateStaffModal(){

    document.getElementById(
        'updateStaffModal'
    ).classList.remove('show');

}


function submitStaffUpdate(){

    if(staffUpdateForm){

        staffUpdateForm.removeAttribute(
            'onsubmit'
        );

        staffUpdateForm.submit();

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

        alert("Only JPG, PNG, and WEBP images are allowed.");

        event.target.value = "";

        return;

    }


    if(file.size > 5 * 1024 * 1024){

        alert("Profile picture must not exceed 5MB.");

        event.target.value = "";

        return;

    }


    const reader = new FileReader();


    reader.onload = function(e){

        let preview =
            document.getElementById(
                "staffPicturePreview"
            );


        const placeholder =
            document.getElementById(
                "staffPicturePlaceholder"
            );


        if(placeholder){

            placeholder.outerHTML =
                '<img src="' +
                e.target.result +
                '" id="staffPicturePreview" ' +
                'alt="Staff Profile Picture">';

        }
        else{

            preview.src = e.target.result;

        }

    };


    reader.readAsDataURL(file);

}

function togglePassword(inputId, icon){

    const input = document.getElementById(inputId);

    if(input.type === "password"){

        input.type = "text";

        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");

    }
    else{

        input.type = "password";

        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");

    }

}

</script>


</body>

</html>