<?php

require_once("includes/admin_auth.php");

// ===========================
// GET STAFF ID
// ===========================

if(!isset($_GET['id']) || !is_numeric($_GET['id'])){

    header("Location: staff_admin.php");
    exit;

}

$user_id = (int) $_GET['id'];


// ===========================
// GET STAFF
// ===========================

$stmt = $con->prepare("
    SELECT
        user_id,
        full_name,
        role,
        status,
        profile_picture,
        created_at
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


// Staff does not exist

if($result->num_rows === 0){

    $stmt->close();

    header("Location: staff_admin.php");
    exit;

}


$staff = $result->fetch_assoc();

$stmt->close();


// ===========================
// INITIAL
// ===========================

$initial = strtoupper(
    substr($staff['full_name'], 0, 1)
);

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
Staff Profile | Admin Panel
</title>


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

<div class="staff-profile-content">

    <!-- SUCCESS MESSAGES -->

<?php if(isset($_GET['updated'])): ?>

    <div class="staff-success">

        <i class="fa-solid fa-circle-check"></i>

        Staff information was successfully updated.

    </div>

<?php endif; ?>


<?php if(isset($_GET['status_updated'])): ?>

    <div class="staff-success">

        <i class="fa-solid fa-circle-check"></i>

        Staff account status was successfully updated.

    </div>

<?php endif; ?>


    <!-- HEADER -->

    <div class="staff-profile-header">

        <div>

            <h2>
                Staff Profile
            </h2>

            <p>
                View staff account information.
            </p>

        </div>


       <div class="staff-profile-actions">

    <a
        href="staff_edit_admin.php?id=<?php echo $staff['user_id']; ?>"
        class="edit-staff-profile-btn"
    >
        <i class="fa-solid fa-pen"></i>
        Edit Staff
    </a>


    <button
        type="button"
        class="delete-staff-profile-btn"
        onclick="openDeleteStaffModal()"
    >
        <i class="fa-solid fa-trash"></i>
        Delete Staff
    </button>


    <a
        href="staff_admin.php"
        class="back-staff-btn"
    >
        <i class="fa-solid fa-arrow-left"></i>
        Back to Staff
    </a>

</div>

    </div>


    <!-- PROFILE CARD -->

    <div class="staff-profile-card">


        <div class="staff-profile-top">


           <div class="large-staff-avatar">
    <?php if(!empty($staff['profile_picture'])): ?>

        <img
            src="uploads/<?php echo htmlspecialchars($staff['profile_picture']); ?>"
            alt="Profile Picture"
        >

    <?php else: ?>

        <?php echo htmlspecialchars($initial); ?>

    <?php endif; ?>
</div>


            <div class="staff-profile-name">

                <h1>
                    <?php
                    echo htmlspecialchars(
                        $staff['full_name']
                    );
                    ?>
                </h1>

                <span
                    class="staff-status
                    <?php
                    echo strtolower(
                        $staff['status']
                    );
                    ?>"
                >

                    <?php
                    echo htmlspecialchars(
                        $staff['status']
                    );
                    ?>

                </span>

            </div>


        </div>


        <!-- ACCOUNT INFORMATION -->

        <div class="staff-profile-section">


            <h2>

                <i class="fa-solid fa-user"></i>

                Account Information

            </h2>


            <div class="staff-info-grid">


                <div class="staff-info-item">

                    <span>
                        Staff ID
                    </span>

                    <strong>
                        #<?php
                        echo htmlspecialchars(
                            $staff['user_id']
                        );
                        ?>
                    </strong>

                </div>


                <div class="staff-info-item">

                    <span>
                        Full Name
                    </span>

                    <strong>
                        <?php
                        echo htmlspecialchars(
                            $staff['full_name']
                        );
                        ?>
                    </strong>

                </div>

                <div class="staff-info-item">

                    <span>
                        Role
                    </span>

                    <strong class="staff-role">

                        <i class="fa-solid fa-user-tie"></i>

                        <?php
                        echo htmlspecialchars(
                            $staff['role']
                        );
                        ?>

                    </strong>

                </div>


                <div class="staff-info-item">

                    <span>
                        Account Status
                    </span>

                    <strong>

                        <span
                            class="staff-status
                            <?php
                            echo strtolower(
                                $staff['status']
                            );
                            ?>"
                        >

                            <?php
                            echo htmlspecialchars(
                                $staff['status']
                            );
                            ?>

                        </span>

                    </strong>

                </div>


                <div class="staff-info-item">

                    <span>
                        Created On
                    </span>

                    <strong>

                        <?php

                        echo date(
                            "F d, Y h:i A",
                            strtotime(
                                $staff['created_at']
                            )
                        );

                        ?>

                    </strong>

                </div>


            </div>


        </div>


   <!-- ACCOUNT ACTIONS -->

<div class="staff-profile-section">

    <h2>

        <i class="fa-solid fa-shield-halved"></i>

        Account Actions

    </h2>


    <div class="staff-profile-actions">

        <!-- EDIT STAFF -->

        <a
            href="staff_edit_admin.php?id=<?php echo $staff['user_id']; ?>"
            class="staff-edit-large"
        >

            <i class="fa-solid fa-pen"></i>

            Edit Staff

        </a>


        <!-- ACTIVATE / DEACTIVATE -->

        <?php if($staff['status'] === 'Active'): ?>

            <button
                type="button"
                class="staff-deactivate-large"
                onclick="openStatusModal(
                    <?php echo $staff['user_id']; ?>,
                    'Inactive'
                )"
            >

                <i class="fa-solid fa-user-slash"></i>

                Deactivate Account

            </button>

        <?php else: ?>

            <button
                type="button"
                class="staff-activate-large"
                onclick="openStatusModal(
                    <?php echo $staff['user_id']; ?>,
                    'Active'
                )"
            >

                <i class="fa-solid fa-user-check"></i>

                Activate Account

            </button>

        <?php endif; ?>

    </div>

</div>


<!-- END OF STAFF PROFILE CARD -->

</div>


<!-- =====================================================
     DELETE STAFF MODAL
===================================================== -->

<div
    id="deleteStaffModal"
    class="delete-staff-modal"
>

    <div class="delete-staff-modal-box">

        <div class="delete-staff-icon">

            <i class="fa-solid fa-trash"></i>

        </div>


        <h2>
            Delete Staff?
        </h2>


        <p>

            Are you sure you want to delete

            <strong>
                <?php echo htmlspecialchars($staff['full_name']); ?>
            </strong>

            ?

        </p>


        <span>
            This action cannot be undone.
        </span>


        <div class="delete-staff-modal-buttons">


            <!-- NO BUTTON -->

            <button
                type="button"
                class="delete-cancel-btn"
                onclick="closeDeleteStaffModal()"
            >

                No

            </button>


            <!-- YES DELETE BUTTON -->

            <form
                method="POST"
                action="staff_delete_admin.php"
            >

                <input
                    type="hidden"
                    name="user_id"
                    value="<?php echo $staff['user_id']; ?>"
                >


                <button
                    type="submit"
                    class="delete-confirm-btn"
                >

                    Yes, Delete

                </button>

            </form>


        </div>

    </div>

</div>


<!-- =====================================================
     STATUS MODAL
===================================================== -->

<div
    id="statusModal"
    class="staff-modal"
>

    <div class="staff-modal-box">


        <div class="staff-modal-icon">

            <i class="fa-solid fa-circle-question"></i>

        </div>


        <h2 id="modalTitle">
            Change Account Status
        </h2>


        <p id="modalMessage">
            Are you sure you want to change this account's status?
        </p>


        <div class="staff-modal-buttons">


            <!-- NO -->

            <button
                type="button"
                class="modal-cancel"
                onclick="closeStatusModal()"
            >

                No

            </button>


            <!-- YES -->

            <form
                method="POST"
                action="staff_status_admin.php"
                id="statusForm"
            >

                <input
                    type="hidden"
                    name="user_id"
                    id="modalUserId"
                >


                <input
                    type="hidden"
                    name="status"
                    id="modalStatus"
                >


                <button
                    type="submit"
                    class="modal-confirm"
                >

                    Yes

                </button>

            </form>


        </div>

    </div>

</div>
</div>

<script>

function openStatusModal(userId, status){

    document.getElementById(
        "modalUserId"
    ).value = userId;


    document.getElementById(
        "modalStatus"
    ).value = status;


    if(status === "Inactive"){

        document.getElementById(
            "modalTitle"
        ).textContent =
        "Deactivate Account";


        document.getElementById(
            "modalMessage"
        ).textContent =
        "Are you sure you want to deactivate this staff account?";

    }
    else{

        document.getElementById(
            "modalTitle"
        ).textContent =
        "Activate Account";


        document.getElementById(
            "modalMessage"
        ).textContent =
        "Are you sure you want to activate this staff account?";

    }


    document.getElementById(
        "statusModal"
    ).classList.add("show");

}


function closeStatusModal(){

    document.getElementById(
        "statusModal"
    ).classList.remove("show");

}


document.getElementById(
    "statusModal"
).addEventListener(
    "click",
    function(event){

        if(event.target === this){

            closeStatusModal();

        }

    }
);

function openDeleteStaffModal(){

    document
        .getElementById("deleteStaffModal")
        .classList.add("show");

}


function closeDeleteStaffModal(){

    document
        .getElementById("deleteStaffModal")
        .classList.remove("show");

}


document
    .getElementById("deleteStaffModal")
    .addEventListener("click", function(event){

        if(event.target === this){

            closeDeleteStaffModal();

        }

    });

</script>
</body>
</html>