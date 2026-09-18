<?php

require_once("includes/admin_auth.php");

$member_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if($member_id <= 0){
    die("Invalid member ID.");
}

$stmt = $con->prepare("
    SELECT
        id,
        first_name,
        middlename,
        last_name,
        qr_token
    FROM signup
    WHERE id = ?
    LIMIT 1
");

$stmt->bind_param("i", $member_id);
$stmt->execute();

$result = $stmt->get_result();
$member = $result->fetch_assoc();

$stmt->close();

if(!$member){
    die("Member not found.");
}

$full_name =
    $member['first_name'] . ' ' .
    (!empty($member['middlename'])
        ? $member['middlename'] . ' '
        : '') .
    $member['last_name'];

$qrFile = "qrcodes/member_" . $member_id . ".png";

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Member QR Code | Admin Panel</title>

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

    <link
        rel="stylesheet"
        href="assets/css/admin.css"
    >

    <style>

        .qr-page{
            display:flex;
            justify-content:center;
            padding:40px 20px;
        }

        .qr-card{
            width:100%;
            max-width:450px;
            background:#181818;
            border:1px solid #303030;
            border-radius:16px;
            padding:30px;
            text-align:center;
            box-shadow:0 10px 30px rgba(0,0,0,0.4);
        }

        .qr-card h1{
            margin:0 0 8px;
            color:#39ff14;
            font-family:'Poppins',sans-serif;
            font-size:24px;
        }

        .qr-card .member-name{
            color:#fff;
            font-family:'Poppins',sans-serif;
            font-size:18px;
            font-weight:600;
            margin-bottom:5px;
        }

        .qr-card .member-id{
            color:#999;
            font-family:'Poppins',sans-serif;
            font-size:13px;
            margin-bottom:25px;
        }

        .qr-image-box{
            background:#fff;
            width:280px;
            height:280px;
            margin:0 auto 25px;
            padding:15px;
            border-radius:12px;
            display:flex;
            align-items:center;
            justify-content:center;
        }

        .qr-image-box img{
            width:100%;
            height:100%;
            object-fit:contain;
        }

        .qr-not-found{
            padding:40px 20px;
            color:#aaa;
            font-family:'Poppins',sans-serif;
        }

        .qr-not-found i{
            font-size:40px;
            color:#ff5252;
            margin-bottom:15px;
        }

        .qr-back{
            display:inline-flex;
            align-items:center;
            gap:8px;
            padding:11px 18px;
            background:#39ff14;
            color:#000;
            text-decoration:none;
            border-radius:8px;
            font-family:'Poppins',sans-serif;
            font-size:13px;
            font-weight:600;
        }

        .qr-back:hover{
            box-shadow:0 0 15px rgba(57,255,20,0.35);
        }

    </style>

</head>

<body>

<?php include("includes/admin_sidebar.php"); ?>

<div class="main">

    <div class="dashboard-content">

        <div class="qr-page">

            <div class="qr-card">

                <h1>
                    <i class="fa-solid fa-qrcode"></i>
                    Member QR Code
                </h1>

                <div class="member-name">
                    <?= htmlspecialchars($full_name) ?>
                </div>

                <div class="member-id">
                    Member #<?= $member_id ?>
                </div>

                <?php if(
                    !empty($member['qr_token']) &&
                    file_exists(__DIR__ . "/" . $qrFile)
                ): ?>

                    <div class="qr-image-box">

                        <img
                            src="<?= htmlspecialchars($qrFile) ?>"
                            alt="QR Code for <?= htmlspecialchars($full_name) ?>"
                        >

                    </div>

                    <p style="
                        color:#aaa;
                        font-family:'Poppins',sans-serif;
                        font-size:12px;
                        margin-bottom:20px;
                    ">
                        Scan this QR code to identify the member.
                    </p>

                <?php else: ?>

                    <div class="qr-not-found">

                        <i class="fa-solid fa-qrcode"></i>

                        <p>
                            QR code has not been generated yet.
                        </p>

                    </div>

                <?php endif; ?>

                <a
                    href="members_admin.php"
                    class="qr-back"
                >
                    <i class="fa-solid fa-arrow-left"></i>
                    Back to Members
                </a>

            </div>

        </div>

    </div>

</div>

</body>

</html>