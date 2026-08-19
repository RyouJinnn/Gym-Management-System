<?php

$current_page = basename($_SERVER['PHP_SELF']);

?>

<div class="admin-sidebar">

    <div class="admin-logo">

        <img src="logofit.png" alt="Logo">

        <h2>
            <span>STAFF</span> PANEL
        </h2>

    </div>

    <ul>

        <!-- DASHBOARD -->

        <li class="<?= $current_page == 'staff_dashboard.php' ? 'active' : '' ?>">

            <a href="staff_dashboard.php">

                <i class="fa-solid fa-gauge-high"></i>

                Dashboard

            </a>

        </li>


        <!-- MEMBERS -->

        <li class="<?= $current_page == 'members_staff.php' ? 'active' : '' ?>">

            <a href="members_staff.php">

                <i class="fa-solid fa-users"></i>

                Members

            </a>

        </li>


        <!-- PAYMENTS -->

        <li class="<?= $current_page == 'payments_staff.php' ? 'active' : '' ?>">

            <a href="payments_staff.php">

                <i class="fa-solid fa-money-check-dollar"></i>

                Payments

            </a>

        </li>


        <!-- ATTENDANCE -->

        <li class="<?= $current_page == 'attendance_staff.php' ? 'active' : '' ?>">

            <a href="attendance_staff.php">

                <i class="fa-solid fa-calendar-check"></i>

                Attendance

            </a>

        </li>


        <!-- REPORTS -->

        <li class="<?= $current_page == 'reports_staff.php' ? 'active' : '' ?>">

            <a href="reports_staff.php">

                <i class="fa-solid fa-chart-column"></i>

                Reports

            </a>

        </li>


        <!-- LOGOUT BUTTON -->

        <button
            type="button"
            class="admin-sidebar-logout"
            onclick="openLogoutModal()"
        >

            <i class="fas fa-sign-out-alt"></i>

            <span>Logout</span>

        </button>


        <!-- LOGOUT MODAL -->

        <div id="staffLogoutModal" class="logout-modal">

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
                        action="logout_staff.php"
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
        .getElementById("staffLogoutModal")
        .classList.add("show");

}


function closeLogoutModal() {

    document
        .getElementById("staffLogoutModal")
        .classList.remove("show");

}


document
    .getElementById("staffLogoutModal")
    .addEventListener("click", function(event) {

        if(event.target === this) {

            closeLogoutModal();

        }

    });

</script>