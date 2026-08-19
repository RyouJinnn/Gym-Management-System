<?php

include("includes/admin_auth.php");

/* =========================
   DELETE MESSAGE
========================= */

if(isset($_POST['delete_contact'])){

    $contactId = (int)$_POST['contact_id'];

    $deleteStmt = $con->prepare("
        DELETE FROM contact_messages
        WHERE contact_id = ?
    ");

    $deleteStmt->bind_param("i", $contactId);
    $deleteStmt->execute();
    $deleteStmt->close();

    header("Location: contact_messages_admin.php?deleted=1");
    exit;
}


/* =========================
   SEARCH
========================= */

$search = "";

if(isset($_GET['search'])){
    $search = trim($_GET['search']);
}


/* =========================
   GET MESSAGES
========================= */

$sql = "
    SELECT
        contact_id,
        full_name,
        email,
        subject,
        message,
        created_at
    FROM contact_messages
    WHERE 1=1
";

$params = [];
$types = "";

if($search !== ""){

    $sql .= "
        AND (
            full_name LIKE ?
            OR email LIKE ?
            OR subject LIKE ?
            OR message LIKE ?
        )
    ";

    $searchValue = "%" . $search . "%";

    $params[] = $searchValue;
    $params[] = $searchValue;
    $params[] = $searchValue;
    $params[] = $searchValue;

    $types = "ssss";
}

$sql .= " ORDER BY created_at DESC";

$stmt = $con->prepare($sql);

if(!empty($params)){

    $stmt->bind_param(
        $types,
        ...$params
    );

}

$stmt->execute();

$result = $stmt->get_result();


/* =========================
   TOTAL MESSAGES
========================= */

$countQuery = mysqli_query(
    $con,
    "SELECT COUNT(*) AS total FROM contact_messages"
);

$totalMessages = mysqli_fetch_assoc($countQuery)['total'];

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta
name="viewport"
content="width=device-width, initial-scale=1.0"
>

<title>Contact Messages | Admin Panel</title>

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

<style>

/* =========================
   CONTACT CONTENT
========================= */

.contact-content{
    width:100%;
    padding:35px 34px;
    box-sizing:border-box;
}

.contact-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:25px;
}

.contact-header h2{
    margin:0;
    color:#ffd400;
    font-family:'Orbitron',sans-serif;
    font-size:36px;
}

.contact-header p{
    margin:7px 0 0;
    color:#bdbdbd;
    font-size:15px;
}

.contact-total{
    display:flex;
    align-items:center;
    gap:10px;
    padding:14px 20px;
    background:#181818;
    border:1px solid #292929;
    border-radius:14px;
    color:#fff;
}

.contact-total i{
    color:#ffd400;
    font-size:19px;
}

.contact-total span{
    color:#ffd400;
    font-weight:700;
    font-size:19px;
}


/* =========================
   SUCCESS
========================= */

.contact-success{
    display:flex;
    align-items:center;
    gap:10px;
    padding:13px 17px;
    margin-bottom:20px;
    border:1px solid #39ff14;
    border-radius:10px;
    background:rgba(57,255,20,.07);
    color:#39ff14;
    font-size:14px;
}


/* =========================
   SEARCH
========================= */

.contact-tools{
    background:#171717;
    border:1px solid #292929;
    border-radius:16px;
    padding:18px;
    margin-bottom:22px;
}

.contact-search{
    display:flex;
    gap:12px;
}

.contact-search-box{
    position:relative;
    flex:1;
}

.contact-search-box i{
    position:absolute;
    left:17px;
    top:50%;
    transform:translateY(-50%);
    color:#ffd400;
}

.contact-search-box input{
    width:100%;
    height:50px;
    padding:0 16px 0 45px;
    box-sizing:border-box;
    background:#101010;
    border:1px solid #333;
    border-radius:11px;
    color:#fff;
    font-family:'Poppins',sans-serif;
    outline:none;
}

.contact-search-box input:focus{
    border-color:#ffd400;
}

.contact-search-btn{
    height:50px;
    padding:0 22px;
    border:0;
    border-radius:11px;
    background:#ffd400;
    color:#111;
    font-family:'Poppins',sans-serif;
    font-weight:700;
    cursor:pointer;
}

.contact-clear{
    display:flex;
    align-items:center;
    padding:0 18px;
    border:1px solid #444;
    border-radius:11px;
    color:#fff;
    text-decoration:none;
}


/* =========================
   TABLE
========================= */

.contact-table-card{
    width:100%;
    background:#171717;
    border:1px solid #292929;
    border-radius:17px;
    overflow:hidden;
}

.contact-table-wrapper{
    width:100%;
    overflow-x:auto;
}

.contact-table{
    width:100%;
    min-width:1100px;
    border-collapse:collapse;
    table-layout:fixed;
}

.contact-table th{
    padding:17px 20px;
    background:#1e1e1e;
    color:#ffd400;
    text-align:left;
    font-size:14px;
    font-weight:700;
}

.contact-table td{
    padding:17px 20px;
    border-top:1px solid #292929;
    color:#d0d0d0;
    vertical-align:middle;
    font-size:14px;
}


/* COLUMN WIDTHS */

.contact-table th:nth-child(1),
.contact-table td:nth-child(1){
    width:17%;
}

.contact-table th:nth-child(2),
.contact-table td:nth-child(2){
    width:21%;
}

.contact-table th:nth-child(3),
.contact-table td:nth-child(3){
    width:17%;
}

.contact-table th:nth-child(4),
.contact-table td:nth-child(4){
    width:23%;
}

.contact-table th:nth-child(5),
.contact-table td:nth-child(5){
    width:13%;
}

.contact-table th:nth-child(6),
.contact-table td:nth-child(6){
    width:9%;
}


/* =========================
   NAME
========================= */

.contact-name strong{
    display:block;
    color:#fff;
    font-size:15px;
    margin-bottom:4px;
}

.contact-name span{
    color:#888;
    font-size:12px;
}


/* =========================
   EMAIL
========================= */

.contact-email{
    color:#cfcfcf;
    word-break:break-word;
}


/* =========================
   SUBJECT
========================= */

.contact-subject{
    color:#fff;
    font-weight:600;
}


/* =========================
   MESSAGE PREVIEW
========================= */

.contact-message-preview{
    color:#aaa;
    line-height:1.5;
    display:-webkit-box;
    -webkit-line-clamp:2;
    -webkit-box-orient:vertical;
    overflow:hidden;
}


/* =========================
   DATE
========================= */

.contact-date{
    color:#aaa;
    line-height:1.5;
    white-space:nowrap;
}

/* CONTACT MESSAGE ACTIONS */

.contact-actions {
    display: flex;
    align-items: center;
    gap: 10px;
}

.contact-view-btn,
.contact-delete-btn {
    width: 32px;
    height: 32px;
    min-width: 32px;
    min-height: 32px;
    border: none;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    cursor: pointer;
    transition: 0.2s ease;
}

.contact-view-btn {
    background: #252525;
    color: white;
}

.contact-delete-btn {
    background: #4a1c20;
    color: #ff5555;
}

.contact-view-btn:hover {
    background: #333333;
    transform: translateY(-2px);
}

.contact-delete-btn:hover {
    background: #642126;
    transform: translateY(-2px);
}

/* =========================
   EMPTY
========================= */

.contact-empty{
    text-align:center;
    padding:55px 20px !important;
}

.contact-empty i{
    display:block;
    color:#555;
    font-size:40px;
    margin-bottom:13px;
}

.contact-empty strong{
    display:block;
    color:#fff;
    font-size:17px;
    margin-bottom:5px;
}

.contact-empty span{
    color:#777;
}


/* =========================
   VIEW MODAL
========================= */

.contact-modal{
    position:fixed;
    inset:0;
    display:none;
    align-items:center;
    justify-content:center;
    background:rgba(0,0,0,.78);
    z-index:9999;
    padding:20px;
}

.contact-modal.show{
    display:flex;
}

.contact-modal-box{
    width:100%;
    max-width:650px;
    max-height:80vh;
    overflow-y:auto;
    background:#181818;
    border:1px solid #333;
    border-radius:18px;
    padding:28px;
    box-sizing:border-box;
}

.contact-modal-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:22px;
}

.contact-modal-header h3{
    margin:0;
    color:#ffd400;
    font-family:'Orbitron',sans-serif;
    font-size:21px;
}

.contact-modal-close{
    width:38px;
    height:38px;
    border:0;
    border-radius:9px;
    background:#292929;
    color:#fff;
    cursor:pointer;
    font-size:17px;
}

.contact-modal-info{
    margin-bottom:18px;
}

.contact-modal-info strong{
    display:block;
    color:#fff;
    font-size:17px;
    margin-bottom:4px;
}

.contact-modal-info span{
    color:#999;
    font-size:13px;
}

.contact-modal-subject{
    margin-bottom:13px;
    color:#ffd400;
    font-weight:700;
}

.contact-modal-message{
    padding:18px;
    background:#101010;
    border:1px solid #292929;
    border-radius:11px;
    color:#d5d5d5;
    line-height:1.7;
    white-space:pre-wrap;
    word-break:break-word;
}


/* =========================
   DELETE MODAL
========================= */

.contact-delete-modal{
    position:fixed;
    inset:0;
    display:none;
    align-items:center;
    justify-content:center;
    background:rgba(0,0,0,.78);
    z-index:10000;
    padding:20px;
}

.contact-delete-modal.show{
    display:flex;
}

.contact-delete-box{
    width:100%;
    max-width:400px;
    background:#181818;
    border:1px solid #333;
    border-radius:18px;
    padding:28px;
    text-align:center;
}

.contact-delete-icon{
    width:58px;
    height:58px;
    margin:0 auto 16px;
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;
    background:#401b1b;
    color:#ff5c5c;
    font-size:23px;
}

.contact-delete-box h3{
    margin:0 0 9px;
    color:#fff;
}

.contact-delete-box p{
    margin:0 0 23px;
    color:#999;
}

.contact-delete-actions{
    display:flex;
    justify-content:center;
    gap:10px;
}

.contact-cancel-btn,
.contact-confirm-btn{
    padding:11px 21px;
    border:0;
    border-radius:9px;
    cursor:pointer;
    font-family:'Poppins',sans-serif;
    font-weight:600;
}

.contact-cancel-btn{
    background:#292929;
    color:#fff;
}

.contact-confirm-btn{
    background:#d93636;
    color:#fff;
}

</style>

</head>

<body>

<div class="wrapper">

<?php include("includes/admin_sidebar.php"); ?>

<div class="main">

<div class="contact-content">


<!-- HEADER -->

<div class="contact-header">

    <div>

        <h2>Contact Messages</h2>

        <p>
            View messages submitted through the contact form.
        </p>

    </div>

    <div class="contact-total">

        <i class="fa-solid fa-envelope"></i>

        <span>
            <?php echo $totalMessages; ?>
        </span>

        Messages

    </div>

</div>


<!-- SUCCESS MESSAGE -->

<?php if(isset($_GET['deleted'])): ?>

<div class="contact-success">

    <i class="fa-solid fa-circle-check"></i>

    Contact message was successfully deleted.

</div>

<?php endif; ?>


<!-- SEARCH -->

<div class="contact-tools">

    <form
        method="GET"
        class="contact-search"
    >

        <div class="contact-search-box">

            <i class="fa-solid fa-magnifying-glass"></i>

            <input
                type="text"
                name="search"
                placeholder="Search messages..."
                value="<?php echo htmlspecialchars($search); ?>"
            >

        </div>

        <button
            type="submit"
            class="contact-search-btn"
        >

            <i class="fa-solid fa-magnifying-glass"></i>

            Search

        </button>

        <?php if($search !== ""): ?>

            <a
                href="contact_messages_admin.php"
                class="contact-clear"
            >
                Clear
            </a>

        <?php endif; ?>

    </form>

</div>


<!-- TABLE -->

<div class="contact-table-card">

    <div class="contact-table-wrapper">

        <table class="contact-table">

            <thead>

                <tr>

                    <th>Name</th>

                    <th>Email</th>

                    <th>Subject</th>

                    <th>Message</th>

                    <th>Submitted</th>

                    <th>Action</th>

                </tr>

            </thead>

            <tbody>

            <?php if($result->num_rows > 0): ?>

                <?php while($contact = $result->fetch_assoc()): ?>

                    <tr>

                        <!-- NAME -->

                        <td>

                            <div class="contact-name">

                                <strong>
                                    <?php
                                    echo htmlspecialchars(
                                        $contact['full_name']
                                    );
                                    ?>
                                </strong>

                                <span>
                                    Contact Message
                                </span>

                            </div>

                        </td>


                        <!-- EMAIL -->

                        <td>

                            <div class="contact-email">

                                <?php
                                echo htmlspecialchars(
                                    $contact['email']
                                );
                                ?>

                            </div>

                        </td>


                        <!-- SUBJECT -->

                        <td>

                            <div class="contact-subject">

                                <?php
                                echo htmlspecialchars(
                                    $contact['subject']
                                );
                                ?>

                            </div>

                        </td>


                        <!-- MESSAGE -->

                        <td>

                            <div class="contact-message-preview">

                                <?php
                                echo htmlspecialchars(
                                    $contact['message']
                                );
                                ?>

                            </div>

                        </td>


                        <!-- SUBMITTED -->

                        <td>

                            <div class="contact-date">

                                <?php
                                echo date(
                                    "M d, Y",
                                    strtotime(
                                        $contact['created_at']
                                    )
                                );
                                ?>

                                <br>

                                <?php
                                echo date(
                                    "h:i A",
                                    strtotime(
                                        $contact['created_at']
                                    )
                                );
                                ?>

                            </div>

                        </td>


                        <!-- ACTION -->

                        <td>

                            <div class="contact-actions">

                                <!-- VIEW -->

                                <button
                                    type="button"
                                    class="contact-view-btn"
                                    title="View Message"
                                    onclick='openContactModal(
                                        <?php echo json_encode($contact["full_name"]); ?>,
                                        <?php echo json_encode($contact["email"]); ?>,
                                        <?php echo json_encode($contact["subject"]); ?>,
                                        <?php echo json_encode($contact["message"]); ?>
                                    )'
                                >

                                    <i class="fa-solid fa-eye"></i>

                                </button>


                                <!-- DELETE -->

                                <button
                                    type="button"
                                    class="contact-delete-btn"
                                    title="Delete Message"
                                    onclick='openDeleteModal(
                                        <?php echo (int)$contact["contact_id"]; ?>,
                                        <?php echo json_encode($contact["full_name"]); ?>
                                    )'
                                >

                                    <i class="fa-solid fa-trash"></i>

                                </button>

                            </div>

                        </td>

                    </tr>

                <?php endwhile; ?>

            <?php else: ?>

                <tr>

                    <td
                        colspan="6"
                        class="contact-empty"
                    >

                        <i class="fa-solid fa-envelope-open"></i>

                        <strong>
                            No contact messages found
                        </strong>

                        <span>
                            There are no messages to display.
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


<!-- =========================
     VIEW MESSAGE MODAL
========================= -->

<div
    id="contactModal"
    class="contact-modal"
>

    <div class="contact-modal-box">

        <div class="contact-modal-header">

            <h3>
                Contact Message
            </h3>

            <button
                type="button"
                class="contact-modal-close"
                onclick="closeContactModal()"
            >

                <i class="fa-solid fa-xmark"></i>

            </button>

        </div>


        <div class="contact-modal-info">

            <strong id="modalName"></strong>

            <span id="modalEmail"></span>

        </div>


        <div
            id="modalSubject"
            class="contact-modal-subject"
        ></div>


        <div
            id="modalMessage"
            class="contact-modal-message"
        ></div>

    </div>

</div>


<!-- =========================
     DELETE MODAL
========================= -->

<div
    id="deleteContactModal"
    class="contact-delete-modal"
>

    <div class="contact-delete-box">

        <div class="contact-delete-icon">

            <i class="fa-solid fa-trash"></i>

        </div>

        <h3>
            Delete Message?
        </h3>

        <p>

            Are you sure you want to delete
            <strong id="deleteContactName"></strong>'s message?

        </p>


        <form method="POST">

            <input
                type="hidden"
                name="contact_id"
                id="deleteContactId"
            >

            <div class="contact-delete-actions">

                <button
                    type="button"
                    class="contact-cancel-btn"
                    onclick="closeDeleteModal()"
                >
                    Cancel
                </button>

                <button
                    type="submit"
                    name="delete_contact"
                    class="contact-confirm-btn"
                >

                    <i class="fa-solid fa-trash"></i>

                    Delete

                </button>

            </div>

        </form>

    </div>

</div>


<script>

/* =========================
   VIEW MESSAGE
========================= */

function openContactModal(
    name,
    email,
    subject,
    message
){

    document.getElementById("modalName").textContent = name;

    document.getElementById("modalEmail").textContent = email;

    document.getElementById("modalSubject").textContent =
        subject;

    document.getElementById("modalMessage").textContent =
        message;

    document
        .getElementById("contactModal")
        .classList.add("show");

}


function closeContactModal(){

    document
        .getElementById("contactModal")
        .classList.remove("show");

}


/* =========================
   DELETE
========================= */

function openDeleteModal(
    contactId,
    contactName
){

    document.getElementById("deleteContactId").value =
        contactId;

    document.getElementById("deleteContactName").textContent =
        contactName;

    document
        .getElementById("deleteContactModal")
        .classList.add("show");

}


function closeDeleteModal(){

    document
        .getElementById("deleteContactModal")
        .classList.remove("show");

}


/* =========================
   CLOSE WHEN CLICKING OUTSIDE
========================= */

document
    .getElementById("contactModal")
    .addEventListener(
        "click",
        function(event){

            if(event.target === this){

                closeContactModal();

            }

        }
    );


document
    .getElementById("deleteContactModal")
    .addEventListener(
        "click",
        function(event){

            if(event.target === this){

                closeDeleteModal();

            }

        }
    );

</script>

</body>

</html>