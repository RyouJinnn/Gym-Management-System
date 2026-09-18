<?php

require_once("includes/staff_auth.php");
/* ===========================
   CHECK MEMBER ID
=========================== */

if(!isset($_GET['id']) || !is_numeric($_GET['id'])){

    header("Location: members_staff.php");
    exit();

}

$member_id = (int)$_GET['id'];


/* ===========================
   GET MEMBER
=========================== */

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
        profile_picture,
        created_at
    FROM signup
    WHERE id = ?
    LIMIT 1
");

$stmt->bind_param("i", $member_id);

$stmt->execute();

$result = $stmt->get_result();


/* ===========================
   MEMBER NOT FOUND
=========================== */

if($result->num_rows == 0){

    header("Location: members_staff.php");
    exit();

}

$member = $result->fetch_assoc();


/* ===========================
   FULL NAME
=========================== */

$fullName = $member['first_name'];

if(!empty($member['middlename'])){
    $fullName .= " " . $member['middlename'];
}

$fullName .= " " . $member['last_name'];

if(!empty($member['suffix'])){
    $fullName .= " " . $member['suffix'];
}


/* ===========================
   PROFILE PICTURE
=========================== */

$profilePicture = "";

if(!empty($member['profile_picture'])){

    $profilePicture = $member['profile_picture'];

}

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>
Member Profile | Staff Panel
</title>

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


    <!-- SIDEBAR -->

    <?php include("includes/staff_sidebar.php"); ?>


    <div class="main">

        <div class="member-view-content">

    <?php if(isset($_GET['created'])): ?>

        <div class="member-success">

            <i class="fa-solid fa-circle-check"></i>

            Member was successfully added.

        </div>

    <?php endif; ?>


    <?php if(isset($_GET['updated'])): ?>

        <div class="member-success">

            <i class="fa-solid fa-circle-check"></i>

            Member information was successfully updated.

        </div>

    <?php endif; ?>


<?php if(isset($_GET['status_updated'])): ?>

    <div class="member-success">

        <i class="fa-solid fa-circle-check"></i>

        Member account status was successfully updated.

    </div>

<?php endif; ?>


           <!-- PAGE HEADER -->

<div class="member-view-header">

    <div class="member-view-title">

        <h2>
            Member Profile
        </h2>

        <p>
            View member information and account status.
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


            <!-- PROFILE CARD -->

            <div class="member-profile-card">


                <!-- PROFILE TOP -->

                <div class="member-profile-top">


                    <!-- PROFILE IMAGE -->

                    <div class="large-member-avatar">

    <?php if(!empty($profilePicture)): ?>

        <img
    src="<?php echo htmlspecialchars($profilePicture); ?>"
    alt="Member Profile Picture"
>

    <?php else: ?>

        <i class="fa-solid fa-user"></i>

    <?php endif; ?>

</div>


                    <!-- NAME -->

                    <div class="member-profile-name">

                        <h1>
                            <?php echo htmlspecialchars($fullName); ?>
                        </h1>

                        <p>
                            Member ID:
                            <strong>
                                <?php echo $member['id']; ?>
                            </strong>
                        </p>


                        <?php

                        $statusClass =
                            strtolower($member['status']);

                        ?>

                        <span
                            class="member-status <?php echo $statusClass; ?>"
                        >

                            <?php
                            echo htmlspecialchars(
                                $member['status']
                            );
                            ?>

                        </span>

                    </div>

                </div>


                <!-- MEMBER INFORMATION -->

                <div class="member-information">


                    <h3>

                        <i class="fa-solid fa-id-card"></i>

                        Personal Information

                    </h3>


                    <div class="member-info-grid">


                        <!-- FIRST NAME -->

                        <div class="member-info-item">

                            <span>
                                First Name
                            </span>

                            <strong>
                                <?php
                                echo htmlspecialchars(
                                    $member['first_name']
                                );
                                ?>
                            </strong>

                        </div>


                        <!-- MIDDLE NAME -->

                        <div class="member-info-item">

                            <span>
                                Middle Name
                            </span>

                            <strong>

                                <?php

                                echo !empty($member['middlename'])
                                    ? htmlspecialchars($member['middlename'])
                                    : "—";

                                ?>

                            </strong>

                        </div>


                        <!-- LAST NAME -->

                        <div class="member-info-item">

                            <span>
                                Last Name
                            </span>

                            <strong>
                                <?php
                                echo htmlspecialchars(
                                    $member['last_name']
                                );
                                ?>
                            </strong>

                        </div>


                        <!-- SUFFIX -->

                        <div class="member-info-item">

                            <span>
                                Suffix
                            </span>

                            <strong>

                                <?php

                                echo !empty($member['suffix'])
                                    ? htmlspecialchars($member['suffix'])
                                    : "—";

                                ?>

                            </strong>

                        </div>


                        <!-- GENDER -->

                        <div class="member-info-item">

                            <span>
                                Gender
                            </span>

                            <strong>
                                <?php
                                echo htmlspecialchars(
                                    $member['gender']
                                );
                                ?>
                            </strong>

                        </div>


                        <!-- BIRTHDATE -->

                        <div class="member-info-item">

                            <span>
                                Birthdate
                            </span>

                            <strong>

                                <?php

                                echo date(
                                    "F d, Y",
                                    strtotime($member['birthdate'])
                                );

                                ?>

                            </strong>

                        </div>


                        <!-- EMAIL -->

                        <div class="member-info-item">

                            <span>
                                Email Address
                            </span>

                            <strong>
                                <?php
                                echo htmlspecialchars(
                                    $member['email']
                                );
                                ?>
                            </strong>

                        </div>


                        <!-- CONTACT -->

                        <div class="member-info-item">

                            <span>
                                Contact Number
                            </span>

                            <strong>
                                <?php
                                echo htmlspecialchars(
                                    $member['contact_number']
                                );
                                ?>
                            </strong>

                        </div>


                        <!-- ADDRESS -->

                        <div class="member-info-item full-width">

                            <span>
                                Address
                            </span>

                            <strong>

                                <?php

                                echo !empty($member['address'])
                                    ? htmlspecialchars($member['address'])
                                    : "No address provided.";

                                ?>

                            </strong>

                        </div>


                        <!-- DATE REGISTERED -->

                        <div class="member-info-item">

                            <span>
                                Registered On
                            </span>

                            <strong>

                                <?php

                                echo date(
                                    "F d, Y h:i A",
                                    strtotime($member['created_at'])
                                );

                                ?>

                            </strong>

                        </div>


                    </div>

                </div>

                <div class="member-profile-actions">

    <button
        type="button"
        class="edit-member-btn"
        onclick="openEditMemberModal()"
    >

        <i class="fa-solid fa-pen-to-square"></i>

        Edit Member

    </button>

</div>


                <!-- ACCOUNT STATUS -->

                <div class="member-account-section">

                    <h3>

                        <i class="fa-solid fa-user-check"></i>

                        Account Status

                    </h3>


                    <div class="account-status-box">

                        <div>

                            <span>
                                Current Status
                            </span>

                            <strong>
                                <?php
                                echo htmlspecialchars(
                                    $member['status']
                                );
                                ?>
                            </strong>

                        </div>

                        </div>

                    </div>

                </div>


            </div>

        </div>

    </div>

</div>

<!-- STATUS CONFIRMATION MODAL -->

<div id="statusModal" class="status-modal">

    <div class="status-modal-box">

        <div class="status-modal-icon">

            <i id="statusModalIcon"
               class="fa-solid fa-user-check"></i>

        </div>

        <h2 id="statusModalTitle">
            Activate Member?
        </h2>

        <p id="statusModalMessage">
            Are you sure you want to activate this member?
        </p>

        <div class="status-modal-buttons">

            <button
                type="button"
                class="modal-no"
                onclick="closeStatusModal()"
            >
                No
            </button>

            <a
                id="statusModalYes"
                href="#"
                class="modal-yes"
            >
                Yes
            </a>

        </div>

    </div>

</div>

<!-- EDIT MEMBER CONFIRMATION MODAL -->

<div id="editMemberModal" class="status-modal">

    <div class="status-modal-box">

        <div class="status-modal-icon">
            <i class="fa-solid fa-pen-to-square"></i>
        </div>

        <h2>
            Edit Member?
        </h2>

        <p>
            Are you sure you want to edit
            <strong><?php echo htmlspecialchars($fullName); ?></strong>?
        </p>

        <div class="status-modal-buttons">

            <button
                type="button"
                class="modal-no"
                onclick="closeEditMemberModal()"
            >
                No
            </button>

            <a
                href="member_edit_staff.php?id=<?php echo $member['id']; ?>"
                class="modal-yes activate-modal"
            >
                Yes, Edit
            </a>

        </div>

    </div>

</div>

<script>

function openStatusModal(memberId, action){

    const modal =
        document.getElementById("statusModal");

    const title =
        document.getElementById("statusModalTitle");

    const message =
        document.getElementById("statusModalMessage");

    const icon =
        document.getElementById("statusModalIcon");

    const yesButton =
        document.getElementById("statusModalYes");


    if(action === "activate"){

        title.textContent =
            "Activate Member?";

        message.textContent =
            "Are you sure you want to activate this member?";

        icon.className =
            "fa-solid fa-user-check";

        yesButton.textContent =
            "Yes, Activate";

        yesButton.className =
            "modal-yes activate-modal";

    }else{

        title.textContent =
            "Deactivate Member?";

        message.textContent =
            "Are you sure you want to deactivate this member?";

        icon.className =
            "fa-solid fa-user-slash";

        yesButton.textContent =
            "Yes, Deactivate";

        yesButton.className =
            "modal-yes deactivate-modal";

    }


    yesButton.href =
        "member_status_staff.php?id=" + memberId;


    modal.classList.add("show");

}


function openEditMemberModal(){

    document
        .getElementById("editMemberModal")
        .classList.add("show");

}


function closeEditMemberModal(){

    document
        .getElementById("editMemberModal")
        .classList.remove("show");

}

/* ==========================
   CLOSE MODAL
========================== */

function closeStatusModal(){

    const modal =
        document.getElementById("statusModal");

    modal.classList.remove("show");

}


/* ==========================
   CLOSE WHEN CLICKING OUTSIDE
========================== */

document
    .getElementById("statusModal")
    .addEventListener("click", function(event){

        if(event.target === this){

            closeStatusModal();

        }

    });

</script>

</body>

</html>