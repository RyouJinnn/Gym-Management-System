<?php
$currentPage = basename($_SERVER['PHP_SELF']);

/* Default values so every page works */
if (!isset($hasMembership)) {
    $hasMembership = false;
}

if (!isset($plan_name)) {
    $plan_name = "";
}
?>

<button type="button" class="floating-menu" id="menuBtn">
    <i class="fa-solid fa-bars"></i>
</button>

<aside class="sidebar" id="sidebar">

    <div class="sidebar-content">

        <div class="logo">
            <img src="logofit.png" alt="Fit Function Gym Logo">
            <span>FIT</span>
            <h1>FUNCTION GYM</h1>
        </div>

        <div class="sidebar-user">

            <img
                class="avatar"
                src="<?=
                    !empty($user['profile_picture'])
                    ? htmlspecialchars($user['profile_picture'])
                    : 'defaultimg.png';
                ?>"
                alt="Profile Picture">

            <div class="user-info">

                <h4>
                    <?=
                    htmlspecialchars(
                        trim($user['first_name'] . " " . $user['last_name'])
                    );
                    ?>
                </h4>

                <small>

                    <?php
                    if($hasMembership){
                        echo htmlspecialchars($plan_name);
                    }else{
                        echo "No Active Plan";
                    }
                    ?>

                </small>

            </div>

        </div>

        <nav class="menu">

            <a href="dashboard.php"
               class="<?= $currentPage=="dashboard.php" ? "active" : "" ?>">
                <i class="fa-solid fa-house"></i>
                <span>Home</span>
            </a>

            <a href="profile.php"
               class="<?= $currentPage=="profile.php" ? "active" : "" ?>">
                <i class="fa-solid fa-user"></i>
                <span>My Profile</span>
            </a>

            <a href="membership_db.php"
               class="<?= $currentPage=="membership_db.php" ? "active" : "" ?>">
                <i class="fa-solid fa-id-card"></i>
                <span>Membership</span>
            </a>

            <a href="attendance.php"
               class="<?= $currentPage=="attendance.php" ? "active" : "" ?>">
                <i class="fa-solid fa-clipboard-check"></i>
                <span>Attendance</span>
            </a>

            <a href="feedback.php"
               class="<?= $currentPage=="feedback.php" ? "active" : "" ?>">
                <i class="fa-solid fa-comment-dots"></i>
                <span>Feedback</span>
            </a>

        </nav>

    </div>

    <hr class="sidebar-divider">

    <div class="logout">

        <a href="#" id="logoutBtn">
    <i class="fa-solid fa-right-from-bracket"></i>
    <span>Logout</span>
</a>

    </div>

<!-- Logout Confirmation -->

<div id="logoutModal" class="logout-modal">

    <div class="logout-box">

        <h2>Logout</h2>

        <p>Are you sure you want to logout?</p>

        <div class="logout-buttons">

            <button id="cancelLogout" class="cancel-btn">
                No
            </button>

            <a href="logout.php" class="logout-btn">
                Yes
            </a>
        </div>
    </div>
</div>

</aside>
<script>
const logoutBtn = document.getElementById("logoutBtn");
const logoutModal = document.getElementById("logoutModal");
const cancelLogout = document.getElementById("cancelLogout");

logoutBtn.addEventListener("click", function(e){
    e.preventDefault();
    logoutModal.classList.add("show");
});

cancelLogout.addEventListener("click", function(){
    logoutModal.classList.remove("show");
});

logoutModal.addEventListener("click", function(e){
    if(e.target === logoutModal){

        logoutModal.classList.remove("show");
    }
});
</script>