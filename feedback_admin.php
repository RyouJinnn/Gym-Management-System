<?php

require_once("includes/admin_auth.php");

/* =========================================================
   DELETE FEEDBACK - SAME PAGE AJAX
========================================================= */

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_feedback'])) {

    header('Content-Type: application/json');

    $feedback_id = isset($_POST['feedback_id'])
        ? (int)$_POST['feedback_id']
        : 0;

    if ($feedback_id <= 0) {

        echo json_encode([
            "success" => false,
            "message" => "Invalid feedback."
        ]);

        exit;
    }


    $stmt = $con->prepare("
        DELETE FROM feedback
        WHERE feedback_id = ?
    ");

    if (!$stmt) {

        echo json_encode([
            "success" => false,
            "message" => "Unable to delete feedback."
        ]);

        exit;
    }


    $stmt->bind_param("i", $feedback_id);

    if ($stmt->execute()) {

        if ($stmt->affected_rows > 0) {

            echo json_encode([
                "success" => true,
                "message" => "Feedback deleted successfully!"
            ]);

        } else {

            echo json_encode([
                "success" => false,
                "message" => "Feedback was not found."
            ]);
        }

    } else {

        echo json_encode([
            "success" => false,
            "message" => "Failed to delete feedback."
        ]);
    }

    $stmt->close();

    exit;
}


/* =========================================================
   SEARCH
========================================================= */

$search = "";

if (isset($_GET['search'])) {

    $search = trim($_GET['search']);

}


/* =========================================================
   GET FEEDBACK
========================================================= */

$sql = "
    SELECT
        f.feedback_id,
        f.member_id,
        f.comment,
        f.created_at,
        s.first_name,
        s.last_name,
        s.profile_picture
    FROM feedback f
    INNER JOIN signup s
        ON f.member_id = s.id
";


$params = [];
$types = "";


/* SEARCH */

if ($search !== "") {

    $sql .= "
        WHERE
            s.first_name LIKE ?
            OR s.last_name LIKE ?
            OR f.comment LIKE ?
    ";

    $searchValue = "%" . $search . "%";

    $params[] = $searchValue;
    $params[] = $searchValue;
    $params[] = $searchValue;

    $types = "sss";
}


$sql .= "
    ORDER BY f.created_at DESC
";


/* =========================================================
   PREPARE
========================================================= */

$stmt = $con->prepare($sql);

if (!$stmt) {

    die("SQL Error: " . $con->error);

}


if (!empty($params)) {

    $stmt->bind_param(
        $types,
        ...$params
    );

}


$stmt->execute();

$result = $stmt->get_result();


/* =========================================================
   TOTAL FEEDBACK
========================================================= */

$countQuery = mysqli_query(
    $con,
    "SELECT COUNT(*) AS total FROM feedback"
);

$totalFeedback = mysqli_fetch_assoc(
    $countQuery
)['total'];

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>Feedback | Admin Panel</title>


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


        <div class="feedback-content">


            <!-- PAGE HEADER -->

            <div class="feedback-header">

                <div>

                    <h2>
                        Feedback
                    </h2>

                    <p>
                        Review feedback submitted by gym members.
                    </p>

                </div>


                <div class="feedback-total">

                    <i class="fa-solid fa-comments"></i>

                    <span>
                        <?php echo $totalFeedback; ?>
                    </span>

                    Feedback

                </div>

            </div>



            <!-- SUCCESS MESSAGE -->

            <div
                id="feedbackSuccess"
                class="feedback-success"
                style="display:none;"
            >

                <i class="fa-solid fa-circle-check"></i>

                <span id="feedbackSuccessText">
                    Feedback deleted successfully!
                </span>

            </div>



            <!-- SEARCH -->

            <div class="feedback-tools">

                <form
                    method="GET"
                    class="feedback-search"
                >

                    <div class="feedback-search-box">

                        <i class="fa-solid fa-magnifying-glass"></i>

                        <input
                            type="text"
                            name="search"
                            placeholder="Search feedback..."
                            value="<?php echo htmlspecialchars($search); ?>"
                        >

                    </div>


                    <button
                        type="submit"
                        class="feedback-filter-btn"
                    >

                        <i class="fa-solid fa-magnifying-glass"></i>

                        Search

                    </button>


                    <?php if ($search !== ""): ?>

                        <a
                            href="feedback_admin.php"
                            class="feedback-clear-btn"
                        >
                            Clear
                        </a>

                    <?php endif; ?>

                </form>

            </div>



            <!-- FEEDBACK TABLE -->

            <div class="feedback-table-card">

                <div class="table-wrapper">

                    <table class="feedback-table">

                        <thead>

                            <tr>

                                <th>ID</th>

                                <th>Member</th>

                                <th>Feedback</th>

                                <th>Date</th>

                                <th>Action</th>

                            </tr>

                        </thead>


                        <tbody id="feedbackTableBody">


                        <?php if ($result->num_rows > 0): ?>


                            <?php while ($feedback = $result->fetch_assoc()): ?>


                                <tr
                                    id="feedback-row-<?php echo (int)$feedback['feedback_id']; ?>"
                                >


                                    <!-- ID -->

                                    <td>

                                        #<?php
                                        echo (int)$feedback['feedback_id'];
                                        ?>

                                    </td>



                                    <!-- MEMBER -->

                                    <td>

                                        <div class="feedback-member">


                                            <?php if (!empty($feedback['profile_picture'])): ?>

                                                <img
                                                    src="<?php echo htmlspecialchars($feedback['profile_picture']); ?>"
                                                    alt="Profile"
                                                    class="feedback-avatar"
                                                >

                                            <?php else: ?>

                                                <div class="feedback-avatar">

                                                    <?php

                                                    echo strtoupper(
                                                        substr(
                                                            $feedback['first_name'],
                                                            0,
                                                            1
                                                        )
                                                    );

                                                    ?>

                                                </div>

                                            <?php endif; ?>


                                            <div>

                                                <strong>

                                                    <?php

                                                    echo htmlspecialchars(
                                                        $feedback['first_name']
                                                        . " "
                                                        . $feedback['last_name']
                                                    );

                                                    ?>

                                                </strong>


                                                <small>
                                                    Member #<?php
                                                    echo (int)$feedback['member_id'];
                                                    ?>
                                                </small>

                                            </div>


                                        </div>

                                    </td>



                                    <!-- COMMENT -->

                                    <td>

                                        <div class="feedback-comment">

                                            <?php

                                            echo nl2br(
                                                htmlspecialchars(
                                                    $feedback['comment']
                                                )
                                            );

                                            ?>

                                        </div>

                                    </td>



                                    <!-- DATE -->

                                    <td>

                                        <?php

                                        echo date(
                                            "M d, Y",
                                            strtotime(
                                                $feedback['created_at']
                                            )
                                        );

                                        ?>

                                        <br>

                                        <small>

                                            <?php

                                            echo date(
                                                "h:i A",
                                                strtotime(
                                                    $feedback['created_at']
                                                )
                                            );

                                            ?>

                                        </small>

                                    </td>



                                    <!-- ACTION -->

                                    <td>

                                        <button
                                            type="button"
                                            class="feedback-delete-btn"
                                            title="Delete Feedback"
                                            onclick="openFeedbackDeleteModal(
                                                <?php
                                                echo (int)$feedback['feedback_id'];
                                                ?>
                                            )"
                                        >

                                            <i class="fa-solid fa-trash"></i>

                                        </button>

                                    </td>


                                </tr>


                            <?php endwhile; ?>


                        <?php else: ?>


                            <tr>

                                <td
                                    colspan="5"
                                    class="no-feedback"
                                >

                                    <i class="fa-solid fa-comments"></i>

                                    <strong>
                                        No feedback found
                                    </strong>

                                    <span>
                                        There are no feedback messages to display.
                                    </span>

                                </td>

                            </tr>


                        <?php endif; ?>


                        </tbody>

                    </table>

                </div>

            </div>


        </div>

    </div>

</div>



<!-- =====================================================
     DELETE FEEDBACK MODAL
===================================================== -->

<div
    id="deleteFeedbackModal"
    class="delete-feedback-modal"
>


    <div class="delete-feedback-modal-content">


        <div class="delete-feedback-modal-icon">

            <i class="fa-solid fa-trash"></i>

        </div>


        <h2>
            Delete Feedback?
        </h2>


        <p>
            Are you sure you want to delete this feedback?
        </p>


        <p class="delete-feedback-warning">
            This action cannot be undone.
        </p>



        <div class="delete-feedback-modal-actions">


            <button
                type="button"
                class="delete-feedback-cancel-btn"
                onclick="closeFeedbackDeleteModal()"
            >

                No

            </button>



            <button
                type="button"
                class="delete-feedback-confirm-btn"
                onclick="deleteFeedback()"
            >

                <i class="fa-solid fa-trash"></i>

                Yes, Delete

            </button>


        </div>


    </div>

</div>



<script>

/* =====================================================
   DELETE MODAL
===================================================== */

let selectedFeedbackId = 0;


function openFeedbackDeleteModal(feedbackId) {

    selectedFeedbackId = feedbackId;

    document
        .getElementById("deleteFeedbackModal")
        .classList.add("show");

}


function closeFeedbackDeleteModal() {

    document
        .getElementById("deleteFeedbackModal")
        .classList.remove("show");

    selectedFeedbackId = 0;

}



/* CLOSE WHEN CLICKING OUTSIDE */

document
    .getElementById("deleteFeedbackModal")
    .addEventListener(
        "click",
        function(event) {

            if (event.target === this) {

                closeFeedbackDeleteModal();

            }

        }
    );



/* =====================================================
   DELETE FEEDBACK WITHOUT GOING TO ANOTHER PAGE
===================================================== */

function deleteFeedback() {

    if (selectedFeedbackId <= 0) {
        return;
    }


    const feedbackId = selectedFeedbackId;


    const button =
        document.querySelector(
            ".delete-feedback-confirm-btn"
        );


    button.disabled = true;

    button.innerHTML =
        '<i class="fa-solid fa-spinner fa-spin"></i> Deleting...';



    const formData = new FormData();

    formData.append(
        "delete_feedback",
        "1"
    );

    formData.append(
        "feedback_id",
        feedbackId
    );



    fetch(
        "feedback_admin.php",
        {
            method: "POST",
            body: formData
        }
    )

    .then(response => response.json())

    .then(data => {


        if (data.success) {


            /* CLOSE MODAL */

            closeFeedbackDeleteModal();


            /* SHOW SUCCESS MESSAGE */

            const success =
                document.getElementById(
                    "feedbackSuccess"
                );


            const successText =
                document.getElementById(
                    "feedbackSuccessText"
                );


            successText.textContent =
                data.message;


            success.style.display =
                "flex";


            /*
                REFRESH CURRENT PAGE
                AFTER SHOWING SUCCESS
            */

            setTimeout(
                function() {

                    location.reload();

                },
                1200
            );


        } else {


            alert(data.message);


            button.disabled = false;

            button.innerHTML =
                '<i class="fa-solid fa-trash"></i> Yes, Delete';

        }

    })


    .catch(error => {

        console.error(error);

        alert(
            "Something went wrong while deleting the feedback."
        );


        button.disabled = false;

        button.innerHTML =
            '<i class="fa-solid fa-trash"></i> Yes, Delete';

    });

}

</script>


</body>

</html>