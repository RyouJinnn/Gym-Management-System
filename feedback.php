<?php
session_start();
include("connect.php");

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];

$stmt = $con->prepare("SELECT * FROM signup WHERE email=? LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

$success = "";
$error = "";

/* Submit Feedback */
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $comment = trim($_POST['comment']);
    $member_id = $user['id'];

    if (!empty($comment)) {

        $insert = $con->prepare("
            INSERT INTO feedback(member_id, comment)
            VALUES(?, ?)
        ");

        $insert->bind_param("is", $member_id, $comment);

        if ($insert->execute()) {

    $success = "Feedback submitted successfully!";

} else {

    die("Insert Error: " . $insert->error);

}

        $insert->close();

    }else{

        $error = "Please enter your feedback.";

    }
}

/* Recent Feedback */

$feedback_stmt = $con->prepare("
    SELECT feedback_id,
           comment,
           created_at
    FROM feedback
    WHERE member_id=?
    ORDER BY created_at DESC
");

$feedback_stmt->bind_param("i",$user['id']);
$feedback_stmt->execute();

$feedback_result = $feedback_stmt->get_result();

?>
<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Fit Function Gym | Feedback</title>
<link rel="stylesheet" href="sidebar.css">
<link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@600;700&family=Poppins:wght@300;400;500;600;700&display=swap"
rel="stylesheet">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins',sans-serif;
}

body{
    background:#070707;
    color:#fff;
    overflow-x:hidden;
}

a{
    text-decoration:none;
    color:white;
}

.wrapper{
    display:flex;
    min-height:100vh;
}

.main{
    width:100%;
    margin-left:0;
    padding:25px;
    display:flex;
    flex-direction:column;
    align-items:center;
}

.dashboard-content{
    width:100%;
    max-width:1250px;
    margin:0 auto;
}

/* ---------- Hero ---------- */

.hero{
    width:calc(100% - 30px);
    margin-left:30px;
    min-height:420px;
    border-radius:20px;
    overflow:hidden;
    position:relative;
    margin-bottom:30px;
    background:url("facility.jpg") center center/cover no-repeat;
    display:flex;
    align-items:center;
}

.hero::before{
    content:"";
    position:absolute;
    inset:0;
    background:linear-gradient(
        90deg,
        rgba(0,0,0,.72),
        rgba(0,0,0,.42),
        rgba(0,0,0,.12)
    );
}

.hero-content{
    position:relative;
    z-index:2;
    max-width:520px;
    padding:40px;
}

.hero-content h5{
    font-size:15px;
    letter-spacing:2px;
    margin-bottom:8px;
}

.hero-content h1{
    font-family:'Orbitron',sans-serif;
    font-size:46px;
    color:#39ff14;
    margin-bottom:15px;
}

.hero-content p{
    color:#ddd;
    font-size:14px;
    line-height:1.8;
}

/* ---------- Feedback Form ---------- */

.feedback-card{
    width:100%;
    background:#111;
    border:1px solid #262626;
    border-radius:20px;
    padding:35px;
}

.feedback-card h2{
    font-size:28px;
    margin-bottom:10px;
}

.feedback-card p{
    color:#cfcfcf;
    margin-bottom:25px;
    line-height:1.8;
}

.feedback-card textarea{
    width:100%;
    min-height:230px;
    padding:18px;
    background:#181818;
    color:#fff;
    border:1px solid #333;
    border-radius:12px;
    resize:vertical;
    outline:none;
    font-size:15px;
    transition:.3s;
}

.feedback-card textarea:focus{
    border-color:#39ff14;
}

.button-area{
    display:flex;
    justify-content:flex-end;
    margin-top:25px;
}

.button-area button{
    width:190px;
    height:50px;
    border:2px solid #39ff14;
    border-radius:12px;
    background:transparent;
    color:#39ff14;
    font-size:15px;
    font-weight:600;
    cursor:pointer;
    transition:.3s;
}

.button-area button:hover{
    background:#39ff14;
    color:#000;
}

/* ---------- Messages ---------- */

.success{
    background:#12391a;
    color:#39ff14;
    padding:15px;
    border-radius:12px;
    margin-bottom:20px;
}

.error{
    background:#401515;
    color:#ff7b7b;
    padding:15px;
    border-radius:12px;
    margin-bottom:20px;
}

/* ---------- Responsive ---------- */

@media(max-width:900px){

    .hero{
        width:100%;
        margin-left:0;
        min-height:320px;
    }

    .hero-content{
        padding:30px;
    }

    .hero-content h1{
        font-size:36px;
    }

    .feedback-card,
    .feedback-history{
        padding:25px;
    }

    .button-area{
        justify-content:center;
    }

}

</style>

</head>

<body>

<div class="wrapper">

<?php include 'sidebar.php'; ?>

<main class="main">

<div class="dashboard-content">
    <!-- Hero Section -->
<section class="hero">

    <div class="hero-content">

        <h5>MEMBER FEEDBACK</h5>

        <h1>FEEDBACK</h1>

        <p>
            Your opinion matters to us.
            Tell us about your experience with
            Fit Function Gym so we can continue
            improving our facilities and services.
        </p>

    </div>

</section>

<!-- Feedback Form -->

<div class="feedback-card">

    <h2>
        <i class="fa-solid fa-comment-dots"></i>
        Share Your Feedback
    </h2>

    <p>
        We'd love to hear your suggestions,
        compliments, or concerns.
    </p>

    <?php if(!empty($success)): ?>

        <div class="success">
            <?php echo $success; ?>
        </div>

    <?php endif; ?>

    <?php if(!empty($error)): ?>

        <div class="error">
            <?php echo $error; ?>
        </div>

    <?php endif; ?>

    <form method="POST">

        <textarea
            name="comment"
            placeholder="Write your feedback here..."
            required
        ></textarea>

        <div class="button-area">

            <button type="submit">

                <i class="fa-solid fa-paper-plane"></i>

                Submit Feedback

            </button>

        </div>

    </form>

</div>

</div>
</div>
</main>
</div>

<script>

const menuBtn = document.getElementById("menuBtn");
const sidebar = document.getElementById("sidebar");

if(menuBtn && sidebar){

    menuBtn.addEventListener("click", function(e){

        e.stopPropagation();
        sidebar.classList.toggle("open");

    });

    window.addEventListener("click", function(e){

        if(
            sidebar.classList.contains("open") &&
            !sidebar.contains(e.target) &&
            !menuBtn.contains(e.target)
        ){
            sidebar.classList.remove("open");
        }

    });

}

/* Auto resize textarea */

const textarea = document.querySelector("textarea");

if(textarea){

    textarea.addEventListener("input", function(){

        this.style.height = "230px";
        this.style.height = this.scrollHeight + "px";

    });

}

</script>

</body>
</html>