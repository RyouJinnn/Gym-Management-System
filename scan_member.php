<?php
session_start();
include("connect.php");
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
<script src="https://unpkg.com/html5-qrcode" defer></script>
<style>

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

@media(max-width:900px){

.container{

grid-template-columns:1fr;

}

}

</style>

</head>

<body>

<a href="dashboard.php" class="back-btn">

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

<script>

document.addEventListener("DOMContentLoaded", function(){

   const waitingText = document.querySelector(".waiting");

   function onScanSuccess(decodedText, decodedResult){

    html5QrCode.stop()

    .then(()=>{

        waitingText.innerHTML = "✅ QR Code Detected";
        document.getElementById("scannerStatus").innerHTML =
"🟡 Processing...";

        return fetch("process_scan.php",{

            method:"POST",

            headers:{
                "Content-Type":"application/x-www-form-urlencoded"
            },

            body:"member_code="+encodeURIComponent(decodedText)

        });

    })

    .then(response=>response.json())

    .then(data=>{

        const popup=document.getElementById("popup");
        const statusText=document.getElementById("memberStatus");

        statusText.className="status-text";

        if(data.status==="check_in"){

            document.getElementById("memberName").innerHTML =
                data.member.first_name+" "+data.member.last_name;

            document.getElementById("memberPlan").innerHTML =
                data.member.plan_name;

            document.getElementById("scanTime").innerHTML =
                data.time;

            if(data.member.profile_picture && data.member.profile_picture !== ""){

    document.getElementById("memberPhoto").src =
        data.member.profile_picture;

}
else{

    document.getElementById("memberPhoto").src =
        "defaultimg.png";

}

            statusText.innerHTML="🟢 Checked In";
            statusText.classList.add("status-green");

            popup.className="popup success show";
            popup.innerHTML="✅ Check-In Successful";

        }

        else if(data.status==="check_out"){

            document.getElementById("memberName").innerHTML =
                data.member.first_name+" "+data.member.last_name;

            document.getElementById("memberPlan").innerHTML =
                data.member.plan_name;

            document.getElementById("scanTime").innerHTML =
                data.time;

            if(data.member.profile_picture && data.member.profile_picture !== ""){

    document.getElementById("memberPhoto").src =
        data.member.profile_picture;

}
else{

    document.getElementById("memberPhoto").src =
        "defaultimg.png";

}

            statusText.innerHTML="👋 Checked Out";
            statusText.classList.add("status-green");

            popup.className="popup success show";
            popup.innerHTML="👋 Check-Out Successful";

        }

        else if(data.status==="completed"){

            document.getElementById("memberName").innerHTML =
                data.member.first_name+" "+data.member.last_name;

            document.getElementById("memberPlan").innerHTML =
                data.member.plan_name;

            statusText.innerHTML="✔ Attendance Completed";
            statusText.classList.add("status-green");

            popup.className="popup error show";
            popup.innerHTML="⚠ Attendance Already Completed Today";

        }

        else if(data.status==="expired"){

            document.getElementById("memberName").innerHTML =
                data.member.first_name+" "+data.member.last_name;

            document.getElementById("memberPlan").innerHTML =
                data.member.plan_name;

            statusText.innerHTML="🔴 Membership Expired";
            statusText.classList.add("status-red");

            popup.className="popup error show";
            popup.innerHTML="🔴 Membership Expired";

        }

        else if(data.status==="pending"){

            document.getElementById("memberName").innerHTML =
                data.member.first_name+" "+data.member.last_name;

            document.getElementById("memberPlan").innerHTML =
                data.member.plan_name;

            statusText.innerHTML="🟡 Pending Approval";
            statusText.classList.add("status-yellow");

            popup.className="popup error show";
            popup.innerHTML="🟡 Membership Pending Approval";

        }

        else if(data.status==="inactive"){

            document.getElementById("memberName").innerHTML =
                data.member.first_name+" "+data.member.last_name;

            document.getElementById("memberPlan").innerHTML =
                data.member.plan_name;

            statusText.innerHTML="⚫ Membership Inactive";
            statusText.classList.add("status-gray");

            popup.className="popup error show";
            popup.innerHTML="🔴 Membership Inactive";

        }

        else if(data.status==="no_membership"){

            document.getElementById("memberName").innerHTML="-";
            document.getElementById("memberPlan").innerHTML="-";

            statusText.innerHTML="❌ No Membership";
            statusText.classList.add("status-red");

            popup.className="popup error show";
            popup.innerHTML="❌ No Membership Found";

        }

        else if(data.status==="not_found"){

            document.getElementById("memberName").innerHTML="-";
            document.getElementById("memberPlan").innerHTML="-";

            statusText.innerHTML="❌ Member Not Found";
            statusText.classList.add("status-red");

            popup.className="popup error show";
            popup.innerHTML="❌ Member Not Found";

        }

        else{

            popup.className="popup error show";
            popup.innerHTML=data.message;

        }

        setTimeout(()=>{

            popup.classList.remove("show");

            waitingText.innerHTML="📷 Waiting for QR Code...";
            document.getElementById("scannerStatus").innerHTML =
"🟢 Ready to Scan";
            statusText.className = "status-text";
statusText.innerHTML = "Waiting...";

document.getElementById("scanTime").innerHTML = "--:--";

            html5QrCode.start(

                { facingMode:"environment" },

                {

                    fps:10,

                    qrbox:{
                        width:250,
                        height:250
                    }

                },

                onScanSuccess,

                onScanFailure

            );

        },1500);

    })

    .catch(err=>{

        console.error(err);

        waitingText.innerHTML="❌ Error processing QR.";
        document.getElementById("scannerStatus").innerHTML =
"🔴 Scan Failed";

    });

}

    function onScanFailure(error){
        // Ignore scan failures
    }

    const html5QrCode = new Html5Qrcode("reader");

    Html5Qrcode.getCameras().then(devices => {

        if(devices && devices.length){

            html5QrCode.start(

                {
                    facingMode: "environment"
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

            );

        }
        else{

            waitingText.innerHTML="❌ No camera found.";
            document.getElementById("scannerStatus").innerHTML =
"🔴 Camera Error";

        }

    }).catch(err=>{

        waitingText.innerHTML="❌ Camera access denied.";
        document.getElementById("scannerStatus").innerHTML =
"🔴 Camera Error";

        console.error(err);

    });

});

</script>

<div id="popup" class="popup"></div>

</body>

</html>