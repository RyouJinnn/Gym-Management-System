<?php
session_start();
include("connect.php");

if (isset($_SESSION['admin_id'])) {

    $backPage = "dashboard_admin.php";

} elseif (isset($_SESSION['staff_id'])) {

    $backPage = "staff_dashboard.php";

} else {

    header("Location: login.php");
    exit;

}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Attendance QR Scanner</title>

<link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
<script src="https://unpkg.com/html5-qrcode"></script>
<style>

#reader video{
    width:100% !important;
    height:100% !important;
    object-fit:cover !important;
    display:block !important;
}

*{
margin:0;
padding:0;
box-sizing:border-box;
font-family:Poppins,sans-serif;
}

body{

background:#070707;
color:white;
min-height:100vh;
display:flex;
justify-content:center;
align-items:center;
padding:40px;

}

.container{

width:1100px;
max-width:100%;
display:grid;
grid-template-columns:2fr 1fr;
gap:30px;

}

.left-panel{

background:#111;
border:2px solid #39ff14;
border-radius:20px;
padding:30px;

}

.right-panel{

background:#111;
border:2px solid #39ff14;
border-radius:20px;
padding:30px;

}

.title{

font-family:Orbitron;
font-size:34px;
color:#39ff14;
margin-bottom:10px;

}

.subtitle{

color:#bbb;
margin-bottom:25px;

}

#reader{

width:100%;
min-height:520px;
border-radius:15px;
overflow:hidden;
background:#000;

}

.waiting{

margin-top:20px;
text-align:center;
color:#39ff14;
font-size:18px;
font-weight:600;

}

.right-panel h2{

color:#39ff14;
margin-bottom:20px;

}

.member-photo{

width:150px;
height:150px;
border-radius:50%;
border:4px solid #39ff14;
object-fit:cover;
display:block;
margin:auto;

}

.info{

margin-top:25px;

}

.info p{

margin:12px 0;
font-size:17px;
color:#ddd;

}

.info strong{

color:#39ff14;

}

.status{

margin-top:20px;
padding:12px;
border-radius:10px;
text-align:center;
background:#1b1b1b;
color:#39ff14;
font-weight:700;

}

.back-btn{

position:fixed;
top:20px;
left:20px;
width:40px;
height:40px;
border-radius:50%;
border:2px solid #39ff14;
display:flex;
justify-content:center;
align-items:center;
color:#39ff14;
text-decoration:none;
font-size:18px;
transition:.3s;

}

.back-btn:hover{

background:#39ff14;
color:black;
}
.popup{

position:fixed;

top:30px;

right:30px;

padding:18px 25px;

border-radius:12px;

font-weight:600;

font-size:16px;

opacity:0;

transform:translateY(-20px);

transition:.3s;

z-index:9999;

pointer-events:none;

}

.popup.show{

opacity:1;

transform:translateY(0);

}

.popup.success{

background:#39ff14;

color:#000;

}

.popup.error{

background:#ff3b30;

color:white;

}

.status-text{

font-weight:700;

}

.status-green{

color:#39ff14;

}

.status-red{

color:#ff4d4d;

}

.status-yellow{

color:#ffd93d;

}

.status-gray{

color:#bdbdbd;

}

.member-modal{

    display:none;

    position:fixed;

    inset:0;

    background:rgba(0,0,0,.80);

    z-index:10000;

    align-items:center;

    justify-content:center;

    padding:20px;

}

.member-modal.show{

    display:flex;

}

.member-modal-content{

    position:relative;

    width:650px;

    max-width:100%;

    max-height:90vh;

    overflow-y:auto;

    background:#111;

    border:2px solid #39ff14;

    border-radius:20px;

    padding:30px;

    box-shadow:0 0 30px rgba(57,255,20,.25);

}

.member-modal-content h2{

    text-align:center;

    color:#39ff14;

    margin-bottom:20px;

}

.modal-close{

    position:absolute;

    top:12px;

    right:15px;

    width:38px;

    height:38px;

    border:none;

    border-radius:50%;

    background:transparent;

    color:#39ff14;

    font-size:30px;

    cursor:pointer;

}

.modal-close:hover{

    background:#39ff14;

    color:#000;

}

.modal-member-photo{

    width:130px;

    height:130px;

    display:block;

    margin:0 auto 25px;

    border-radius:50%;

    object-fit:cover;

    border:4px solid #39ff14;

}

.member-details{

    display:grid;

    grid-template-columns:1fr 1fr;

    gap:10px;

}

.detail-row{

    background:#1a1a1a;

    border-radius:10px;

    padding:12px 14px;

    display:flex;

    flex-direction:column;

    gap:4px;

}

.detail-row strong{

    color:#39ff14;

    font-size:13px;

}

.detail-row span{

    color:#ddd;

    font-size:14px;

    word-break:break-word;

}

.attendance-buttons{

    display:flex;

    gap:12px;

    margin-top:25px;

}

.attendance-buttons button{

    flex:1;

    height:50px;

    border-radius:10px;

    font-size:15px;

    font-weight:700;

    cursor:pointer;

    transition:.3s;

}

.check-in-btn{

    border:2px solid #39ff14;

    background:#39ff14;

    color:#000;

}

.check-in-btn:hover{

    background:#32e612;

    transform:translateY(-2px);

}

.check-out-btn{

    border:2px solid #39ff14;

    background:transparent;

    color:#39ff14;

}

.check-out-btn:hover{

    background:#39ff14;

    color:#000;

    transform:translateY(-2px);

}

.attendance-buttons button:disabled{

    opacity:.4;

    cursor:not-allowed;

    transform:none;

}

@media(max-width:600px){

    .member-modal-content{

        padding:22px;

    }

    .member-details{

        grid-template-columns:1fr;

    }

    .attendance-buttons{

        flex-direction:column;

    }

}

@media(max-width:900px){

.container{

grid-template-columns:1fr;

}

}

</style>

</head>

<body>

<a href="<?php echo $backPage; ?>" class="back-btn">
    <i class="fa-solid fa-arrow-left"></i>
</a>

<div class="container">

<div class="left-panel">

<h1 class="title">

Attendance QR Scanner

</h1>

<p class="subtitle">

Point the camera at the member's QR Code.

</p>

<div id="reader"></div>

<div class="waiting">

📷 Waiting for QR Code...

</div>

</div>

<div class="right-panel">

<h2>Member Information</h2>

<img src="defaultimg.png" class="member-photo" id="memberPhoto">

<div class="info">

<p>

<strong>Name:</strong>

<span id="memberName">-</span>

</p>

<p>

<strong>Membership:</strong>

<span id="memberPlan">-</span>

</p>

<p>
<strong>Status:</strong>
<span id="memberStatus" class="status-text">Waiting...</span>
</p>

<p>
<strong>Time:</strong>
<span id="scanTime">--:--</span>
</p>
</div>

<div class="status" id="scannerStatus">

    🟢 Ready to Scan

</div>
</div>
</div>

<!-- MEMBER INFORMATION MODAL -->
<div id="memberModal" class="member-modal">

    <div class="member-modal-content">

        <button type="button" class="modal-close" id="closeMemberModal">
            &times;
        </button>

        <h2>Member Information</h2>

        <img
            src="defaultimg.png"
            class="modal-member-photo"
            id="modalMemberPhoto"
            alt="Member Photo"
        >

        <div class="member-details">

            <div class="detail-row">
                <strong>Member ID</strong>
                <span id="modalMemberID">-</span>
            </div>

            <div class="detail-row">
                <strong>Full Name</strong>
                <span id="modalMemberName">-</span>
            </div>

            <div class="detail-row">
                <strong>Email</strong>
                <span id="modalMemberEmail">-</span>
            </div>

            <div class="detail-row">
                <strong>Contact Number</strong>
                <span id="modalMemberContact">-</span>
            </div>

            <div class="detail-row">
                <strong>Gender</strong>
                <span id="modalMemberGender">-</span>
            </div>

            <div class="detail-row">
                <strong>Birthdate</strong>
                <span id="modalMemberBirthdate">-</span>
            </div>

            <div class="detail-row">
                <strong>Address</strong>
                <span id="modalMemberAddress">-</span>
            </div>

            <div class="detail-row">
                <strong>Membership Plan</strong>
                <span id="modalMemberPlan">-</span>
            </div>

            <div class="detail-row">
                <strong>Membership Status</strong>
                <span id="modalMemberStatus">-</span>
            </div>

            <div class="detail-row">
                <strong>Start Date</strong>
                <span id="modalStartDate">-</span>
            </div>

            <div class="detail-row">
                <strong>End Date</strong>
                <span id="modalEndDate">-</span>
            </div>

            <div class="detail-row">
                <strong>Today's Attendance</strong>
                <span id="modalAttendanceStatus">-</span>
            </div>

        </div>

        <div class="attendance-buttons" id="attendanceButtons">

            <button type="button" id="checkInBtn" class="check-in-btn">
                <i class="fa-solid fa-right-to-bracket"></i>
                Check In
            </button>

            <button type="button" id="checkOutBtn" class="check-out-btn">
                <i class="fa-solid fa-right-from-bracket"></i>
                Check Out
            </button>

        </div>

    </div>

</div>

<script>
document.addEventListener("DOMContentLoaded", function(){

    const waitingText =
        document.querySelector(".waiting");

    const scannerStatus =
        document.getElementById("scannerStatus");

    const memberModal =
        document.getElementById("memberModal");

    const closeMemberModal =
        document.getElementById("closeMemberModal");

    const checkInBtn =
        document.getElementById("checkInBtn");

    const checkOutBtn =
        document.getElementById("checkOutBtn");

    let scannedMemberCode = "";

    let processingScan = false;


    /* ===========================
       QR SCANNER
    =========================== */

    const html5QrCode =
        new Html5Qrcode("reader");


    function onScanFailure(error){

        // Ignore normal QR scan failures

    }


    function onScanSuccess(decodedText, decodedResult){

        if(processingScan){
            return;
        }

        processingScan = true;

        scannedMemberCode = decodedText;


        html5QrCode.stop()
        .then(() => {

            waitingText.innerHTML =
                "✅ QR Code Detected";

            scannerStatus.innerHTML =
                "🟡 Loading Member...";


            return fetch("process_scan.php", {

                method:"POST",

                headers:{
                    "Content-Type":
                        "application/x-www-form-urlencoded"
                },

                body:
                    "member_code=" +
                    encodeURIComponent(decodedText)

            });

        })

        .then(response => response.json())

        .then(data => {

            if(data.status === "error"){

                alert(data.message);

                processingScan = false;

                startScanner();

                return;

            }


            if(data.status === "not_found"){

                alert("Member not found.");

                processingScan = false;

                startScanner();

                return;

            }


            if(!data.member){

                alert(data.message || "Member information unavailable.");

                processingScan = false;

                startScanner();

                return;

            }


            showMemberInformation(data);

        })

        .catch(error => {

            console.error(
                "SCAN ERROR:",
                error
            );

            alert(
                "Unable to load member information."
            );

            processingScan = false;

            startScanner();

        });

    }


    /* ===========================
       SHOW MEMBER INFORMATION
    =========================== */

    function showMemberInformation(data){

        const member = data.member;


        document.getElementById("modalMemberID").innerHTML =
            "M" +
            String(member.id).padStart(5,"0");


        let fullName =
            (member.first_name || "") + " " +
            (member.middlename || "") + " " +
            (member.last_name || "") + " " +
            (member.suffix || "");

        fullName =
            fullName.replace(/\s+/g," ").trim();


        document.getElementById("modalMemberName").innerHTML =
            fullName || "-";


        document.getElementById("modalMemberEmail").innerHTML =
            member.email || "-";


        document.getElementById("modalMemberContact").innerHTML =
            member.contact_number || "-";


        document.getElementById("modalMemberGender").innerHTML =
            member.gender || "-";


        document.getElementById("modalMemberBirthdate").innerHTML =
            member.birthdate || "-";


        document.getElementById("modalMemberAddress").innerHTML =
            member.address || "-";


        document.getElementById("modalMemberPlan").innerHTML =
            member.plan_name || "-";


        document.getElementById("modalMemberStatus").innerHTML =
            member.status || "-";


        document.getElementById("modalStartDate").innerHTML =
            member.start_date || "-";


        document.getElementById("modalEndDate").innerHTML =
            member.end_date || "-";


        /* Profile picture */

        if(
            member.profile_picture &&
            member.profile_picture !== "" &&
            member.profile_picture !== "defaultimg.png"
        ){

            document.getElementById("modalMemberPhoto").src =
                member.profile_picture;

        }else{

            document.getElementById("modalMemberPhoto").src =
                "defaultimg.png";

        }


        /* ===========================
           ATTENDANCE STATUS
        =========================== */

        const attendanceStatus =
            document.getElementById(
                "modalAttendanceStatus"
            );


        checkInBtn.disabled = true;
        checkOutBtn.disabled = true;


        if(data.status === "ready_check_in"){

            attendanceStatus.innerHTML =
                "🟢 Ready for Check In";

            attendanceStatus.style.color =
                "#39ff14";

            checkInBtn.disabled = false;

        }


        else if(data.status === "ready_check_out"){

            attendanceStatus.innerHTML =
                "🟡 Checked In at " +
                (data.check_in || "-");

            attendanceStatus.style.color =
                "#ffd93d";

            checkOutBtn.disabled = false;

        }


        else if(data.status === "completed"){

            attendanceStatus.innerHTML =
                "✔ Attendance Completed Today";

            attendanceStatus.style.color =
                "#39ff14";

        }


        else if(data.status === "pending"){

            attendanceStatus.innerHTML =
                "🟡 Membership Pending Approval";

            attendanceStatus.style.color =
                "#ffd93d";

        }


        else if(data.status === "inactive"){

            attendanceStatus.innerHTML =
                "⚫ Membership Inactive";

            attendanceStatus.style.color =
                "#bdbdbd";

        }


        else if(data.status === "expired"){

            attendanceStatus.innerHTML =
                "🔴 Membership Expired";

            attendanceStatus.style.color =
                "#ff4d4d";

        }


        else if(data.status === "no_membership"){

            attendanceStatus.innerHTML =
                "❌ No Membership";

            attendanceStatus.style.color =
                "#ff4d4d";

        }


        /* Open modal */

        memberModal.classList.add("show");

        scannerStatus.innerHTML =
            "🟡 Member Selected";

    }


    /* ===========================
       CHECK IN
    =========================== */

    checkInBtn.addEventListener(
        "click",
        function(){

            recordAttendance("check_in");

        }
    );


    /* ===========================
       CHECK OUT
    =========================== */

    checkOutBtn.addEventListener(
        "click",
        function(){

            recordAttendance("check_out");

        }
    );


    /* ===========================
       RECORD ATTENDANCE
    =========================== */

    function recordAttendance(action){

        if(!scannedMemberCode){
            return;
        }


        checkInBtn.disabled = true;
        checkOutBtn.disabled = true;


        scannerStatus.innerHTML =
            "🟡 Recording Attendance...";


        fetch("attendance_action.php", {

            method:"POST",

            headers:{
                "Content-Type":
                    "application/x-www-form-urlencoded"
            },

            body:
                "member_code=" +
                encodeURIComponent(scannedMemberCode) +
                "&action=" +
                encodeURIComponent(action)

        })

        .then(response => response.json())

        .then(data => {

            if(data.status === "success"){

                document.getElementById(
                    "modalAttendanceStatus"
                ).innerHTML =
                    action === "check_in"
                    ? "🟢 Checked In at " + data.time
                    : "🟢 Checked Out at " + data.time;

                document.getElementById(
                    "modalAttendanceStatus"
                ).style.color =
                    "#39ff14";


                checkInBtn.disabled = true;
                checkOutBtn.disabled = true;


                scannerStatus.innerHTML =
                    "🟢 Attendance Recorded";

            }

            else{

                alert(
                    data.message ||
                    "Unable to record attendance."
                );


                checkInBtn.disabled =
                    action !== "check_in";

                checkOutBtn.disabled =
                    action !== "check_out";


                scannerStatus.innerHTML =
                    "🔴 Attendance Failed";

            }

        })

        .catch(error => {

            console.error(
                "ATTENDANCE ERROR:",
                error
            );

            alert(
                "Unable to record attendance."
            );


            scannerStatus.innerHTML =
                "🔴 Attendance Failed";

        });

    }


    /* ===========================
       CLOSE MODAL
    =========================== */

    closeMemberModal.addEventListener(
        "click",
        function(){

            memberModal.classList.remove("show");

            scannedMemberCode = "";

            processingScan = false;

            waitingText.innerHTML =
                "📷 Waiting for QR Code...";

            scannerStatus.innerHTML =
                "🟢 Ready to Scan";

            startScanner();

        }
    );


    /* Close when clicking outside */

    memberModal.addEventListener(
        "click",
        function(event){

            if(event.target === memberModal){

                closeMemberModal.click();

            }

        }
    );


    /* ===========================
       START SCANNER
    =========================== */

    function startScanner(){

        waitingText.innerHTML =
            "📷 Starting Camera...";

        scannerStatus.innerHTML =
            "🟡 Starting...";


        html5QrCode.start(

            {
                facingMode:"environment"
            },

            {
                fps:10,

                qrbox:{
                    width:250,
                    height:250
                }

            },

            onScanSuccess,

            onScanFailure

        )

        .then(() => {

            waitingText.innerHTML =
                "📷 Waiting for QR Code...";

            scannerStatus.innerHTML =
                "🟢 Ready to Scan";

        })

        .catch(error => {

            console.error(
                "QR CAMERA ERROR:",
                error
            );

            waitingText.innerHTML =
                "❌ Camera Could Not Start";

            scannerStatus.innerHTML =
                "🔴 Scanner Error";

        });
    }

    startScanner();
});

</script>

<div id="popup" class="popup"></div>

</body>

</html>
