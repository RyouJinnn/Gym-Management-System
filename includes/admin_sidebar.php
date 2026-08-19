<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="admin-sidebar">

    <div class="admin-logo">

        <img src="logofit.png" alt="Logo">

        <h2>
            <span>ADMIN</span> PANEL
        </h2>

    </div>

    <ul>

        <li class="<?= $current_page == 'dashboard_admin.php' ? 'active' : '' ?>">
            <a href="dashboard_admin.php">
                <i class="fa-solid fa-gauge-high"></i>
                Dashboard
            </a>
        </li>

        <li class="<?= $current_page == 'members_admin.php' ? 'active' : '' ?>">
            <a href="members_admin.php">
                <i class="fa-solid fa-users"></i>
                Members
            </a>
        </li>

        <li class="<?= $current_page == 'staff_admin.php' ? 'active' : '' ?>">
            <a href="staff_admin.php">
                <i class="fa-solid fa-user-tie"></i>
                Staff
            </a>
        </li>

        <li class="<?= $current_page == 'membership_plans_admin.php' ? 'active' : '' ?>">
            <a href="membership_plans_admin.php">
                <i class="fa-solid fa-id-card"></i>
                Membership Plans
            </a>
        </li>

        <li class="<?= $current_page == 'payments_admin.php' ? 'active' : '' ?>">
            <a href="payments_admin.php">
                <i class="fa-solid fa-money-check-dollar"></i>
                Payments
            </a>
        </li>

        <li class="<?= $current_page == 'attendance_admin.php' ? 'active' : '' ?>">
            <a href="attendance_admin.php">
                <i class="fa-solid fa-calendar-check"></i>
                Attendance
            </a>
        </li>

        <li class="<?= $current_page == 'reports_admin.php' ? 'active' : '' ?>">
            <a href="reports_admin.php">
                <i class="fa-solid fa-chart-column"></i>
                Reports
            </a>
        </li>

        <li class="<?= $current_page == 'feedback_admin.php' ? 'active' : '' ?>">
            <a href="feedback_admin.php">
                <i class="fa-solid fa-comments"></i>
                Feedback
            </a>
        </li>

        <li class="<?= $current_page == 'contact_messages_admin.php' ? 'active' : '' ?>">
            <a href="contact_messages_admin.php">
                <i class="fa-solid fa-envelope"></i>
                Contact Messages
            </a>
        </li>

        <!-- LOGOUT BUTTON -->

<button type="button" class="admin-sidebar-logout" onclick="openLogoutModal()">
    <i class="fas fa-sign-out-alt"></i>
    <span>Logout</span>
</button>


<!-- LOGOUT MODAL -->

<div id="adminLogoutModal" class="logout-modal">

    <div class="logout-modal-content">

        <h2>Logout</h2>

        <p>
            Are you sure you want to logout?
        </p>

        <div class="logout-modal-actions">

            <button
                type="button"
                class="logout-no-btn"
                onclick="closeLogoutModal()"
            >
                No
            </button>

            <form
                method="POST"
                action="logout_admin.php"
            >

                <button
                    type="submit"
                    class="logout-yes-btn"
                >
                    Yes
                </button>

            </form>

        </div>

    </div>

</div>

    </ul>

</div>

<script>

function openLogoutModal() {

    document
        .getElementById("adminLogoutModal")
        .classList.add("show");

}


function closeLogoutModal() {

    document
        .getElementById("adminLogoutModal")
        .classList.remove("show");

}


document
    .getElementById("adminLogoutModal")
    .addEventListener("click", function(event) {

        if(event.target === this) {

            closeLogoutModal();

        }

    });

</script>