<?php

require_once("includes/staff_auth.php");
require_once("includes/activity_logger.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $first_name = trim($_POST['first_name'] ?? '');
    $middlename = trim($_POST['middlename'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $suffix = trim($_POST['suffix'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $contact_number = trim($_POST['contact_number'] ?? '');
    $gender = trim($_POST['gender'] ?? '');
    $birthdate = trim($_POST['birthdate'] ?? '');
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';
$status = $_POST['status'] ?? 'Active';

    $errors = [];


    // ==============================
    // VALIDATION
    // ==============================

    if ($first_name === '') {
        $errors[] = "First name is required.";
    }

    if ($last_name === '') {
        $errors[] = "Last name is required.";
    }

    if ($email === '') {
        $errors[] = "Email address is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    }

    if ($contact_number === '') {
        $errors[] = "Contact number is required.";
    }

    if ($gender === '') {
        $errors[] = "Gender is required.";
    }

    if ($birthdate === '') {

    $errors[] = "Birthdate is required.";

} else {

    $birthDateObj = DateTime::createFromFormat('Y-m-d', $birthdate);
    $today = new DateTime();

    if (
        !$birthDateObj ||
        $birthDateObj->format('Y-m-d') !== $birthdate
    ) {

        $errors[] = "Please enter a valid birthdate.";

    } else {

        $age = $today->diff($birthDateObj)->y;

        if ($age < 15) {

            $errors[] = "Member must be at least 15 years old.";

        }
    }
}

    if ($password === '') {

    $errors[] = "Password is required.";

} elseif (
    strlen($password) < 8 ||
    strlen($password) > 25 ||
    !preg_match('/[A-Z]/', $password) ||
    !preg_match('/[a-z]/', $password) ||
    !preg_match('/[0-9]/', $password) ||
    !preg_match('/[\W_]/', $password)
) {

    $errors[] =
        "Password must be 8-25 characters and contain uppercase, lowercase, number, and special character.";

}

if ($confirm_password === '') {

    $errors[] = "Please confirm the password.";

} elseif ($password !== $confirm_password) {

    $errors[] = "Passwords do not match.";

}

    if (
        $status !== "Active" &&
        $status !== "Pending" &&
        $status !== "Inactive"
    ) {
        $errors[] = "Invalid status.";
    }


    // ==============================
    // CHECK DUPLICATE EMAIL
    // ==============================

    if (empty($errors)) {

        $check = $con->prepare("
            SELECT id
            FROM signup
            WHERE email = ?
            LIMIT 1
        ");

        if ($check === false) {
            die("SQL Error: " . $con->error);
        }

        $check->bind_param(
            "s",
            $email
        );

        $check->execute();

        $checkResult = $check->get_result();

        if ($checkResult->num_rows > 0) {
            $errors[] = "That email address is already registered.";
        }

        $check->close();
    }

    $profilePicture = "";

if (
    isset($_FILES['profile_picture']) &&
    $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK
) {

    $uploadDir = "profile_picture/";

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $extension = strtolower(
        pathinfo(
            $_FILES['profile_picture']['name'],
            PATHINFO_EXTENSION
        )
    );

    $allowed = ['jpg', 'jpeg', 'png', 'webp'];

    if (in_array($extension, $allowed)) {

        $fileName = uniqid("member_", true) . "." . $extension;

        $targetPath = $uploadDir . $fileName;

        if (move_uploaded_file(
            $_FILES['profile_picture']['tmp_name'],
            $targetPath
        )) {
            $profilePicture = $targetPath;
        }
    }
}

    // ==============================
    // INSERT MEMBER
    // ==============================

    if (empty($errors)) {

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $con->prepare("
            INSERT INTO signup
            (
                first_name,
                middlename,
                last_name,
                suffix,
                email,
                contact_number,
                gender,
                birthdate,
                password,
status,
profile_picture,
email_verified
)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)
        ");

        if ($stmt === false) {
            die("SQL Error: " . $con->error);
        }

    $stmt->bind_param(
    "sssssssssss",
    $first_name,
    $middlename,
    $last_name,
    $suffix,
    $email,
    $contact_number,
    $gender,
    $birthdate,
    $hashedPassword,
    $status,
    $profilePicture
);

        if ($stmt->execute()) {

    $new_member_id = $stmt->insert_id;

    $stmt->close();

    logActivity(
        $con,
        "Member Registration",
        $first_name . " " . $last_name . " was added as a new member by staff.",
        $_SESSION['staff_id'] ?? null,
        "Staff"
    );
            header(
                "Location: member_view_staff.php?id="
                . $new_member_id
                . "&created=1"
            );

            exit;

        } else {

            $errors[] =
                "Failed to add member.";

            $stmt->close();
        }
    }
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

    <title>
        Add Member | Staff Panel
    </title>


    <link
        rel="stylesheet"
        href="assets/css/admin.css"
    >

    <link
href="https://fonts.googleapis.com/css2?family=Orbitron:wght@600;700&family=Poppins:wght@300;400;500;600;700&display=swap"
rel="stylesheet">


    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
    >

</head>


<body>


<div class="wrapper">


    <?php include("includes/staff_sidebar.php"); ?>


    <div class="main">

        <div class="members-content">


            <!-- =========================
                 PAGE HEADER
            ========================= -->

            <div class="members-header">

                <div>

                    <h2>
                        Add Member
                    </h2>

                    <p>
                        Create a new gym member account.
                    </p>

                </div>


                <a
                    href="members_staff.php"
                    class="back-members"
                >

                    <i class="fa-solid fa-arrow-left"></i>

                    Back to Members

                </a>

            </div>


            <!-- =========================
                 ERROR MESSAGE
            ========================= -->

            <?php if (!empty($errors)): ?>

                <div class="form-error-box">

                    <i class="fa-solid fa-circle-exclamation"></i>


                    <div>

                        <?php foreach ($errors as $error): ?>

                            <div>

                                <?php
                                echo htmlspecialchars($error);
                                ?>

                            </div>

                        <?php endforeach; ?>

                    </div>

                </div>

            <?php endif; ?>


            <!-- =========================
                 FORM
            ========================= -->

            <div class="admin-form-card">


                <form
                    method="POST"
                    id="memberAddForm"
                    enctype="multipart/form-data">

<div class="edit-section">

    <h3>
        <i class="fa-solid fa-camera"></i>
        Profile Picture
    </h3>

    <div class="profile-picture-editor">

        <div class="profile-picture-preview">

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

        </div>

        <div class="profile-picture-upload">

            <label
                for="profile_picture"
                class="choose-picture-btn"
            >
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
                No profile picture selected
            </span>

        </div>

    </div>

</div>


                    <!-- FIRST NAME -->

                    <div class="form-group">

                        <label for="first_name">
                            First Name
                        </label>

                        <div class="form-input-wrapper">

                            <i class="fa-solid fa-user"></i>

                            <input
                                type="text"
                                id="first_name"
                                name="first_name"
                                maxlength="50"
                                value="<?php
                                echo htmlspecialchars(
                                    $_POST['first_name'] ?? ''
                                );
                                ?>"
                                required
                            >

                        </div>

                    </div>


                    <!-- MIDDLE NAME -->

                    <div class="form-group">

                        <label for="middlename">
                            Middle Name
                        </label>

                        <div class="form-input-wrapper">

                            <i class="fa-solid fa-user"></i>

                            <input
                                type="text"
                                id="middlename"
                                name="middlename"
                                maxlength="50"
                                value="<?php
                                echo htmlspecialchars(
                                    $_POST['middlename'] ?? ''
                                );
                                ?>"
                            >

                        </div>

                    </div>


                    <!-- LAST NAME -->

                    <div class="form-group">

                        <label for="last_name">
                            Last Name
                        </label>

                        <div class="form-input-wrapper">

                            <i class="fa-solid fa-user"></i>

                            <input
                                type="text"
                                id="last_name"
                                name="last_name"
                                maxlength="50"
                                value="<?php
                                echo htmlspecialchars(
                                    $_POST['last_name'] ?? ''
                                );
                                ?>"
                                required
                            >

                        </div>

                    </div>


                    <!-- SUFFIX -->

                    <div class="form-group">

                        <label for="suffix">
                            Suffix
                        </label>

                        <div class="form-input-wrapper">

                            <i class="fa-solid fa-id-card"></i>

                            <input
                                type="text"
                                id="suffix"
                                name="suffix"
                                maxlength="10"
                                placeholder="Jr., Sr., III"
                                value="<?php
                                echo htmlspecialchars(
                                    $_POST['suffix'] ?? ''
                                );
                                ?>"
                            >

                        </div>

                    </div>


                    <!-- EMAIL -->

                    <div class="form-group">

                        <label for="email">
                            Email Address
                        </label>

                        <div class="form-input-wrapper">

                            <i class="fa-solid fa-envelope"></i>

                            <input
                                type="email"
                                id="email"
                                name="email"
                                maxlength="100"
                                value="<?php
                                echo htmlspecialchars(
                                    $_POST['email'] ?? ''
                                );
                                ?>"
                                required
                            >

                        </div>

                    </div>


                    <!-- CONTACT -->

                    <div class="form-group">

                        <label for="contact_number">
                            Contact Number
                        </label>

                        <div class="form-input-wrapper">

                            <i class="fa-solid fa-phone"></i>

                            <input
                                type="text"
                                id="contact_number"
                                name="contact_number"
                                maxlength="30"
                                value="<?php
                                echo htmlspecialchars(
                                    $_POST['contact_number'] ?? ''
                                );
                                ?>"
                                required
                            >

                        </div>

                    </div>


                    <!-- GENDER -->

                    <div class="form-group">

                        <label for="gender">
                            Gender
                        </label>

                        <div class="form-input-wrapper">

                            <i class="fa-solid fa-venus-mars"></i>

                            <select
                                id="gender"
                                name="gender"
                                required
                            >

                                <option value="">
                                    Select Gender
                                </option>

                                <option
                                    value="Male"
                                    <?php
                                    echo (
                                        ($_POST['gender'] ?? '')
                                        === 'Male'
                                    )
                                    ? 'selected'
                                    : '';
                                    ?>
                                >
                                    Male
                                </option>

                                <option
                                    value="Female"
                                    <?php
                                    echo (
                                        ($_POST['gender'] ?? '')
                                        === 'Female'
                                    )
                                    ? 'selected'
                                    : '';
                                    ?>
                                >
                                    Female
                                </option>

                                <option
                                    value="Rather not say"
                                    <?php
                                    echo (
                                        ($_POST['gender'] ?? '')
                                        === 'Rather not say'
                                    )
                                    ? 'selected'
                                    : '';
                                    ?>
                                >
                                    Rather not say
                                </option>

                            </select>

                        </div>

                    </div>


                    <!-- BIRTHDATE -->

                    <div class="form-group">

                        <label for="birthdate">
                            Birthdate
                        </label>

                        <div class="form-input-wrapper">

                            <i class="fa-solid fa-calendar"></i>

                            <input
    type="date"
    id="birthdate"
    name="birthdate"
    max="<?php echo date('Y-m-d', strtotime('-15 years')); ?>"
    value="<?php
    echo htmlspecialchars(
        $_POST['birthdate'] ?? ''
    );
    ?>"
    required
>

                        </div>
                    </div>

<div class="form-group">

    <label for="password">
        Password
    </label>

    <div class="form-input-wrapper">

        <i class="fa-solid fa-lock"></i>

        <input
            type="password"
            id="password"
            name="password"
            minlength="8"
            maxlength="25"
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

</div>

<div class="form-group">

    <label for="confirm_password">
        Confirm Password
    </label>

    <div class="form-input-wrapper">

        <i class="fa-solid fa-lock"></i>

        <input
            type="password"
            id="confirm_password"
            name="confirm_password"
            minlength="8"
            maxlength="25"
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

                    <div class="form-group">

                        <label for="status">
                            Status
                        </label>

                        <div class="form-input-wrapper">

                            <i class="fa-solid fa-toggle-on"></i>

                            <select
                                id="status"
                                name="status"
                            >

                                <option value="Active">
                                    Active
                                </option>

                                <option value="Pending">
                                    Pending
                                </option>

                                <option value="Inactive">
                                    Inactive
                                </option>

                            </select>

                        </div>

                    </div>

                    <div class="form-actions">

                        <a
                            href="members_staff.php"
                            class="btn-secondary"
                        >

                            Cancel

                        </a>

                        <button
                            type="submit"
                            class="btn-primary"
                        >

                            <i class="fa-solid fa-user-plus"></i>

                            Add Member

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

</div>

<div
    id="addMemberModal"
    class="add-plan-modal"
>

    <div class="add-plan-modal-box">


        <div class="add-plan-icon">

            <i class="fa-solid fa-user-plus"></i>

        </div>

        <h2>
            Add Member?
        </h2>


        <p>

            Are you sure you want to add

            <strong id="addMemberName">
                this member
            </strong>?

        </p>

        <div class="add-plan-modal-actions">

            <button
                type="button"
                class="add-plan-cancel"
                onclick="closeAddMemberModal()"
            >

                No

            </button>

            <button
                type="button"
                class="add-plan-confirm"
                onclick="confirmAddMember()"
            >

                Yes, Add

            </button>

        </div>

    </div>

</div>

<script>

function togglePassword(inputId, button){

    const input = document.getElementById(inputId);
    const icon = button.querySelector("i");

    if(!input || !icon){
        return;
    }

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

const memberAddForm =
    document.getElementById("memberAddForm");

const addMemberModal =
    document.getElementById("addMemberModal");

memberAddForm.addEventListener(
    "submit",
    function(event) {

        event.preventDefault();

        if (!memberAddForm.checkValidity()) {

            memberAddForm.reportValidity();

            return;
        }

        const firstName =
            document.getElementById("first_name")
            .value
            .trim();

        const lastName =
            document.getElementById("last_name")
            .value
            .trim();

        document.getElementById("addMemberName")
            .textContent =
            firstName + " " + lastName;

        addMemberModal.classList.add("show");
    }
);

function closeAddMemberModal() {

    addMemberModal.classList.remove("show");
}

function confirmAddMember() {

    memberAddForm.submit();
}

addMemberModal.addEventListener(
    "click",
    function(event) {

        if (event.target === addMemberModal) {

            closeAddMemberModal();
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

    const allowedTypes = [
        "image/jpeg",
        "image/png",
        "image/webp"
    ];

    if(!allowedTypes.includes(file.type)){

        alert("Only JPG, PNG, and WEBP images are allowed.");

        this.value = "";

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

        profilePlaceholder.style.display = "none";

    };

    reader.readAsDataURL(file);

    selectedFileName.textContent = file.name;

});

</script>

</body>
</html>
