<?php

if(session_status() === PHP_SESSION_NONE){
    session_start();
}

require_once("includes/admin_auth.php");
require_once("includes/activity_logger.php");

if(!isset($_GET['id']) || !is_numeric($_GET['id'])){

    header("Location: members_admin.php");
    exit();
}

$member_id = (int)$_GET['id'];

$error = "";

$stmt = $con->prepare("
    SELECT
        id,
        first_name,
        middlename,
        last_name,
        suffix,
        email,
        contact_number,
        gender,
        birthdate,
        status,
        address,
        profile_picture
    FROM signup
    WHERE id = ?
    LIMIT 1
");

$stmt->bind_param("i", $member_id);
$stmt->execute();

$result = $stmt->get_result();

if($result->num_rows == 0){

    header("Location: members_admin.php");
    exit();

}

$member = $result->fetch_assoc();


/* ===========================
   UPDATE MEMBER
=========================== */

if($_SERVER['REQUEST_METHOD'] === 'POST'){

    $first_name = trim($_POST['first_name']);
    $middlename = trim($_POST['middlename']);
    $last_name = trim($_POST['last_name']);
    $suffix = trim($_POST['suffix']);
    $email = trim($_POST['email']);
    $contact_number = trim($_POST['contact_number']);
    $gender = trim($_POST['gender']);
    $birthdate = trim($_POST['birthdate']);
    $address = trim($_POST['address']);
    $password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

$profilePicture = $member['profile_picture'] ?? "";

if(
    isset($_FILES['profile_picture']) &&
    $_FILES['profile_picture']['error'] !== UPLOAD_ERR_NO_FILE
){

    if($_FILES['profile_picture']['error'] !== UPLOAD_ERR_OK){

        $error = "There was a problem uploading the profile picture.";

    }else{

        $fileSize = $_FILES['profile_picture']['size'];

        if($fileSize > 5 * 1024 * 1024){

            $error = "Profile picture must not exceed 5MB.";

        }else{

            $allowedTypes = [
                'image/jpeg',
                'image/png',
                'image/webp'
            ];

            $fileType = mime_content_type(
                $_FILES['profile_picture']['tmp_name']
            );

            if(!in_array($fileType, $allowedTypes)){

                $error = "Only JPG, PNG, and WEBP images are allowed.";

            }else{

                $uploadDir = "profile_picture/";

                if(!is_dir($uploadDir)){
                    mkdir($uploadDir, 0755, true);
                }

                $extension = strtolower(
                    pathinfo(
                        $_FILES['profile_picture']['name'],
                        PATHINFO_EXTENSION
                    )
                );

                $newFileName =
                    "member_" .
                    $member_id .
                    "_" .
                    time() .
                    "." .
                    $extension;

                $uploadPath = $uploadDir . $newFileName;

                if(move_uploaded_file(
                    $_FILES['profile_picture']['tmp_name'],
                    $uploadPath
                )){

                    $profilePicture = $uploadPath;

                }else{

                    $error = "Unable to save the profile picture.";

                }

            }

        }

    }

}

if(
    $first_name === "" ||
    $last_name === "" ||
    $email === "" ||
    $contact_number === "" ||
    $gender === "" ||
    $birthdate === ""
){

    $error = "Please fill in all required fields.";

}else{

    if($password !== ''){

        if(
            strlen($password) < 8 ||
            strlen($password) > 25 ||
            !preg_match('/[A-Z]/', $password) ||
            !preg_match('/[a-z]/', $password) ||
            !preg_match('/[0-9]/', $password) ||
            !preg_match('/[\W_]/', $password)
        ){

            $error =
                "Password must be 8-25 characters and contain uppercase, lowercase, number, and special character.";

        }elseif($password !== $confirm_password){

            $error = "Passwords do not match.";
        }
    }

    $emailCheck = $con->prepare("
            SELECT id
            FROM signup
            WHERE email = ?
            AND id != ?
            LIMIT 1
        ");

        $emailCheck->bind_param(
            "si",
            $email,
            $member_id
        );

        $emailCheck->execute();

        $emailResult = $emailCheck->get_result();


        if($emailResult->num_rows > 0){

            $error = "That email address is already being used.";

        }else{

           if($password !== ''){

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $update = $con->prepare("
        UPDATE signup
        SET
            first_name = ?,
            middlename = ?,
            last_name = ?,
            suffix = ?,
            email = ?,
            contact_number = ?,
            gender = ?,
            birthdate = ?,
            address = ?,
            profile_picture = ?,
            password = ?
        WHERE id = ?
    ");

    $update->bind_param(
        "sssssssssssi",
        $first_name,
        $middlename,
        $last_name,
        $suffix,
        $email,
        $contact_number,
        $gender,
        $birthdate,
        $address,
        $profilePicture,
        $hashedPassword,
        $member_id
    );

}else{

    $update = $con->prepare("
        UPDATE signup
        SET
            first_name = ?,
            middlename = ?,
            last_name = ?,
            suffix = ?,
            email = ?,
            contact_number = ?,
            gender = ?,
            birthdate = ?,
            address = ?,
            profile_picture = ?
        WHERE id = ?
    ");

    $update->bind_param(
        "ssssssssssi",
        $first_name,
        $middlename,
        $last_name,
        $suffix,
        $email,
        $contact_number,
        $gender,
        $birthdate,
        $address,
        $profilePicture,
        $member_id
    );

}

            if($update->execute()){

    logActivity(
    $con,
    "Member Update",
    $first_name . " " . $last_name . " member information was updated by admin.",
    $_SESSION['admin_id'] ?? null,
    "Admin"
);

    header("Location: member_view_admin.php?id=" . $member_id . "&updated=1");
exit();

}else{

    $error = "Unable to update member information.";


            }

        }

    }

}

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Edit Member | Admin Panel</title>

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

<div class="member-edit-content">


    <!-- PAGE HEADER -->

    <div class="member-edit-header">

        <a
            href="member_view_admin.php?id=<?php echo $member_id; ?>"
            class="back-members"
        >

            <i class="fa-solid fa-arrow-left"></i>

            Back to Member Profile

        </a>

        <h2>
            Edit Member
        </h2>

        <p>
            Update the member's personal information.
        </p>

    </div>


    <!-- EDIT CARD -->

    <div class="member-edit-card">


        <?php if($error !== ""): ?>

            <div class="member-edit-error">

                <i class="fa-solid fa-circle-exclamation"></i>

                <?php echo htmlspecialchars($error); ?>

            </div>

        <?php endif; ?>


        <form id="editMemberForm" method="POST" enctype="multipart/form-data">

            <!-- PROFILE PICTURE -->
<div class="edit-section">

    <h3>
        <i class="fa-solid fa-camera"></i>
        Profile Picture
    </h3>

    <div class="profile-picture-editor">

        <div class="profile-picture-preview">

            <?php if(!empty($member['profile_picture'])): ?>

                <img
                    id="profilePreview"
                    src="<?php echo htmlspecialchars($member['profile_picture']); ?>"
                    alt="Profile Picture"
                >

            <?php else: ?>

                <div
                    class="profile-picture-placeholder"
                    id="profilePlaceholder"
                >
                    <i class="fa-solid fa-user"></i>
                </div>

                <img
                    id="profilePreview"
                    src=""
                    alt="Profile Picture"
                    style="display:none;"
                >

            <?php endif; ?>

        </div>

        <div class="profile-picture-upload">

            <label for="profile_picture" class="choose-picture-btn">
                <i class="fa-solid fa-camera"></i>
                Choose Profile Picture
            </label>

            <input
                type="file"
                id="profile_picture"
                name="profile_picture"
                accept=".jpg,.jpeg,.png,.webp"
                hidden
            >

            <p>
                JPG, PNG, or WEBP. Maximum 5MB.
            </p>

            <span id="selectedFileName">
                <?php
                if(!empty($member['profile_picture'])){
                    echo "Current profile picture";
                }else{
                    echo "No profile picture selected";
                }
                ?>
            </span>

        </div>

    </div>

</div>


            <!-- PERSONAL INFORMATION -->

            <div class="edit-section">

                <h3>

                    <i class="fa-solid fa-id-card"></i>

                    Personal Information

                </h3>


                <div class="edit-grid">


                    <!-- FIRST NAME -->

                    <div class="edit-field">

                        <label>
                            First Name
                            <span>*</span>
                        </label>

                        <input
                            type="text"
                            name="first_name"
                            value="<?php echo htmlspecialchars($member['first_name']); ?>"
                            required
                        >

                    </div>


                    <!-- MIDDLE NAME -->

                    <div class="edit-field">

                        <label>
                            Middle Name
                        </label>

                        <input
                            type="text"
                            name="middlename"
                            value="<?php echo htmlspecialchars($member['middlename'] ?? ''); ?>"
                        >

                    </div>


                    <!-- LAST NAME -->

                    <div class="edit-field">

                        <label>
                            Last Name
                            <span>*</span>
                        </label>

                        <input
                            type="text"
                            name="last_name"
                            value="<?php echo htmlspecialchars($member['last_name']); ?>"
                            required
                        >

                    </div>


                    <!-- SUFFIX -->

                    <div class="edit-field">

                        <label>
                            Suffix
                        </label>

                        <input
                            type="text"
                            name="suffix"
                            value="<?php echo htmlspecialchars($member['suffix'] ?? ''); ?>"
                            placeholder="Jr., Sr., III"
                        >

                    </div>


                    <!-- GENDER -->

                    <div class="edit-field">

                        <label>
                            Gender
                            <span>*</span>
                        </label>

                        <select
                            name="gender"
                            required
                        >

                            <option value="">
                                Select Gender
                            </option>

                            <option
                                value="Male"
                                <?php echo ($member['gender'] === "Male") ? "selected" : ""; ?>
                            >
                                Male
                            </option>

                            <option
                                value="Female"
                                <?php echo ($member['gender'] === "Female") ? "selected" : ""; ?>
                            >
                                Female
                            </option>

                            <option
                                value="Rather not say"
                                <?php echo ($member['gender'] === "Rather not say") ? "selected" : ""; ?>
                            >
                                Rather not say
                            </option>

                        </select>

                    </div>


                    <!-- BIRTHDATE -->

                    <div class="edit-field">

                        <label>
                            Birthdate
                            <span>*</span>
                        </label>

                        <input
                            type="date"
                            name="birthdate"
                            value="<?php echo htmlspecialchars($member['birthdate']); ?>"
                            required
                        >

                    </div>

                </div>

            </div>

            <div class="edit-section">

    <h3>
        Account Password
    </h3>

    <div class="edit-grid">

        <div class="edit-field">

            <label>
                New Password
            </label>

            <div class="form-input-wrapper">

                <input
                    type="password"
                    id="password"
                    name="password"
                    minlength="8"
                    maxlength="25"
                    style="padding-right:45px;"
                    placeholder="Leave blank to keep current password"
                >

                <i
                    class="fa-solid fa-eye password-toggle"
                    onclick="togglePassword('password', this)"
                ></i>

            </div>

        </div>


        <div class="edit-field">

            <label>
                Confirm New Password
            </label>

            <div class="form-input-wrapper">

                <input
                    type="password"
                    id="confirm_password"
                    name="confirm_password"
                    minlength="8"
                    maxlength="25"
                    style="padding-right:45px;"
                    placeholder="Confirm new password"
                >

                <i
                    class="fa-solid fa-eye password-toggle"
                    onclick="togglePassword('confirm_password', this)"
                ></i>

            </div>

        </div>

    </div>

</div>


            <!-- CONTACT INFORMATION -->

            <div class="edit-section">

                <h3>

                    <i class="fa-solid fa-address-book"></i>

                    Contact Information

                </h3>


                <div class="edit-grid">


                    <!-- EMAIL -->

                    <div class="edit-field">

                        <label>
                            Email Address
                            <span>*</span>
                        </label>

                        <input
                            type="email"
                            name="email"
                            value="<?php echo htmlspecialchars($member['email']); ?>"
                            required
                        >

                    </div>


                    <!-- CONTACT NUMBER -->

                    <div class="edit-field">

                        <label>
                            Contact Number
                            <span>*</span>
                        </label>

                        <input
                            type="text"
                            name="contact_number"
                            value="<?php echo htmlspecialchars($member['contact_number']); ?>"
                            required
                        >

                    </div>


                    <!-- ADDRESS -->

                    <div class="edit-field full-width">

                        <label>
                            Address
                        </label>

                        <textarea
                            name="address"
                            rows="4"
                            placeholder="Enter member address"
                        ><?php echo htmlspecialchars($member['address'] ?? ''); ?></textarea>

                    </div>

                </div>

            </div>


            <!-- BUTTONS -->

          <div class="edit-actions">

    <a
        href="member_view_admin.php?id=<?php echo $member_id; ?>"
        class="edit-cancel"
    >
        Cancel
    </a>


    <button
        type="button"
        class="edit-save"
        onclick="openEditMemberSaveModal()"
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

<!-- ==========================================
     EDIT MEMBER SAVE CONFIRMATION
========================================== -->

<div
    id="editMemberSaveModal"
    class="status-modal"
>

    <div class="status-modal-box">

        <div class="status-modal-icon">

            <i class="fa-solid fa-floppy-disk"></i>

        </div>


        <h2>
            Save Changes?
        </h2>


        <p>
            Are you sure you want to save
            the changes made to this member?
        </p>


        <div class="status-modal-buttons">

            <button
                type="button"
                class="modal-no"
                onclick="closeEditMemberSaveModal()"
            >
                No
            </button>


            <button
                type="button"
                class="modal-yes activate-modal"
                onclick="confirmEditMemberSave()"
            >
                Yes, Save Changes
            </button>

        </div>

    </div>

</div>

<script>

function openEditMemberSaveModal(){

    document
        .getElementById("editMemberSaveModal")
        .classList.add("show");

}


function closeEditMemberSaveModal(){

    document
        .getElementById("editMemberSaveModal")
        .classList.remove("show");

}


function confirmEditMemberSave(){

    const form = document.getElementById("editMemberForm");

    if(!form.checkValidity()){
        form.reportValidity();
        return;
    }

    const submitButton = document.createElement("button");
    submitButton.type = "submit";
    submitButton.name = "save_changes";
    submitButton.style.display = "none";

    form.appendChild(submitButton);
    submitButton.click();
    submitButton.remove();
}


document
    .getElementById("editMemberSaveModal")
    .addEventListener(
        "click",
        function(event){

            if(event.target === this){

                closeEditMemberSaveModal();

            }

        }
    );

    const profileInput =
    document.getElementById("profile_picture");

const profilePreview =
    document.getElementById("profilePreview");

const profilePlaceholder =
    document.getElementById("profilePlaceholder");

const selectedFileName =
    document.getElementById("selectedFileName");


profileInput.addEventListener("change", function(){

    const file = this.files[0];

    if(!file){
        return;
    }

    if(file.size > 5 * 1024 * 1024){

        alert("Profile picture must not exceed 5MB.");

        this.value = "";

        return;
    }

    const reader = new FileReader();

    reader.onload = function(e){

        profilePreview.src = e.target.result;

        profilePreview.style.display = "block";

        if(profilePlaceholder){
            profilePlaceholder.style.display = "none";
        }

    };

    reader.readAsDataURL(file);

    selectedFileName.textContent =
        file.name;

});

function togglePassword(inputId, icon){

    const input = document.getElementById(inputId);

    if(input.type === "password"){

        input.type = "text";

        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");

    }else{

        input.type = "password";

        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");

    }

}

</script>

</body>

</html>