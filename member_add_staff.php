<?php

require_once("includes/staff_auth.php");


// ==============================
// HANDLE FORM SUBMISSION
// ==============================

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $first_name = trim($_POST['first_name'] ?? '');
    $middlename = trim($_POST['middlename'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $suffix = trim($_POST['suffix'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $contact_number = trim($_POST['contact_number'] ?? '');
    $gender = trim($_POST['gender'] ?? '');
    $birthdate = trim($_POST['birthdate'] ?? '');
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

    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

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
                status,
                profile_picture
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        if ($stmt === false) {
            die("SQL Error: " . $con->error);
        }

        $stmt->bind_param(
    "ssssssssss",
    $first_name,
    $middlename,
    $last_name,
    $suffix,
    $email,
    $contact_number,
    $gender,
    $birthdate,
    $status,
    $profilePicture
);


        if ($stmt->execute()) {

            $new_member_id = $stmt->insert_id;

            $stmt->close();


            // ==============================
            // SUCCESS REDIRECT
            // ==============================

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

                    <!-- PROFILE PICTURE -->
<div class="profile-picture-section">

    <label class="profile-picture-label">
        Profile Picture
    </label>

    <div class="profile-picture-upload">

        <!-- IMAGE PREVIEW -->
        <div class="profile-picture-preview">

            <img
    id="profilePreview"
    src=""
    alt=""
>

            <div
                class="profile-picture-placeholder"
                id="profilePlaceholder"
            >
                <i class="fa-solid fa-user"></i>
            </div>

        </div>


        <!-- UPLOAD CONTROLS -->
        <div class="profile-picture-controls">

            <label
                for="profile_picture"
                class="profile-upload-button"
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

            <p class="profile-picture-help">
                JPG, PNG, or WEBP
                <br>
                Maximum file size: 5MB
            </p>

            <p
                class="profile-picture-name"
                id="profilePictureName"
            >
                No picture selected
            </p>

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
                                value="<?php
                                echo htmlspecialchars(
                                    $_POST['birthdate'] ?? ''
                                );
                                ?>"
                                required
                            >

                        </div>

                    </div>


                    <!-- STATUS -->

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


                    <!-- BUTTONS -->

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


<!-- ==========================================
     ADD MEMBER CONFIRMATION POPUP
========================================== -->

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

const profilePictureInput =
    document.getElementById("profile_picture");

const profilePreview =
    document.getElementById("profilePreview");

const profilePlaceholder =
    document.getElementById("profilePlaceholder");

const profilePictureName =
    document.getElementById("profilePictureName");


profilePictureInput.addEventListener(
    "change",
    function() {

        const file = this.files[0];

        if (!file) {

            profilePreview.style.display = "none";
            profilePlaceholder.style.display = "flex";

            profilePictureName.textContent =
                "No picture selected";

            return;
        }


        /* Show file name */

        profilePictureName.textContent =
            file.name;


        /* Preview image */

        const reader = new FileReader();

        reader.onload = function(event) {

            profilePreview.src =
                event.target.result;

            profilePreview.style.display =
                "block";

            profilePlaceholder.style.display =
                "none";
        };

        reader.readAsDataURL(file);

    }
);

</script>


</body>

</html>