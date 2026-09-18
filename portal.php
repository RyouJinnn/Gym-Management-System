<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Fit Function Gym | Portal</title>

<!-- Google Fonts -->

<link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap"
      rel="stylesheet">

<!-- Font Awesome -->

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

<style>

/* ==========================================
   RESET
========================================== */

*{

    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins',sans-serif;

}

/* ==========================================
   BODY
========================================== */

body{

    background:#000;
    color:#fff;
    min-height:100vh;

    display:flex;
    justify-content:center;
    align-items:center;

    overflow-x:hidden;
    position:relative;

}

/* ==========================================
   BACKGROUND
========================================== */

body::before{

    content:"";

    position:fixed;

    inset:0;

    background:url("gymbg.jpg") center center/cover no-repeat;

    filter:brightness(.18);

    z-index:-2;

}

/* ==========================================
   OVERLAY
========================================== */

body::after{

    content:"";

    position:fixed;

    inset:0;

    background:rgba(0,0,0,.45);

    z-index:-1;

}

/* ==========================================
   CONTAINER
========================================== */

.portal-container{

    width:95%;
    max-width:1400px;

    padding:50px;

}

/* ==========================================
   HEADER
========================================== */

.portal-header{

    text-align:center;

    margin-bottom:70px;

}

/* ==========================================
   LOGO
========================================== */

.portal-logo{

    width:100px;

    margin:0 auto 20px;

}

.portal-logo img{

    width:100%;

}

/* ==========================================
   TITLE
========================================== */

.portal-header h1{

    font-family:'Orbitron',sans-serif;

    font-size:40px;

    font-weight:700;

    margin-bottom:10px;

}

.portal-header h1 span{

    color:#39ff14;

}

/* ==========================================
   SUBTITLE
========================================== */

.portal-header h3{

    font-size:15px;

    font-weight:400;

    color:#d7d7d7;

    letter-spacing:2px;

    margin-bottom:40px;

}
/* ==========================================
   PORTAL CARDS
========================================== */

.portal-cards{

    display:flex;

    justify-content:center;

    gap:40px;

    flex-wrap:wrap;

    margin-bottom:70px;

}

/* ==========================================
   CARD
========================================== */

.portal-card{

    width:420px;

    background:#121212;

    border:1px solid #2a2a2a;

    border-radius:25px;

    padding:45px 35px;

    text-align:center;

    transition:.35s;

}

.portal-card:hover{

    transform:translateY(-8px);

    border-color:#39ff14;

    box-shadow:0 0 35px rgba(57,255,20,.18);

}

/* ==========================================
   CARD ICON
========================================== */

.portal-icon{

    width:80px;

    height:80px;

    border:3px solid #39ff14;

    border-radius:50%;

    display:flex;

    justify-content:center;

    align-items:center;

    margin:0 auto 30px;

}

.portal-icon i{

    font-size:30px;

    color:#39ff14;

}

/* ==========================================
   CARD TITLE
========================================== */

.portal-card h2{

    font-family:'Orbitron',sans-serif;

    font-size:25px;

    margin-bottom:20px;

}

/* ==========================================
   CARD TEXT
========================================== */

.portal-card p{

    color:#c7c7c7;

    line-height:1.8;

    font-size:14px;

    margin-bottom:30px;

}

/* ==========================================
   BUTTON
========================================== */

.portal-btn{

    display:inline-flex;

    justify-content:center;

    align-items:center;

    gap:12px;

    width:100%;

    height:50px;

    border:2px solid #39ff14;

    border-radius:14px;

    background:#39ff14;

    color:#000;

    font-size:14px;

    font-weight:700;

    text-decoration:none;

    transition:.3s;

}

.portal-btn:hover{

    background:transparent;

    color:#39ff14;

}

.portal-btn i{

    transition:.3s;

}

.portal-btn:hover i{

    transform:translateX(8px);

}
/* ==========================================
   RESPONSIVE
========================================== */

@media(max-width:1200px){

    .portal-header h1{

        font-size:50px;

    }

    .portal-header h2{

        font-size:42px;

    }

}

@media(max-width:900px){

    .portal-container{

        padding:35px 20px;

    }

    .portal-header{

        margin-bottom:50px;

    }

    .portal-header h1{

        font-size:40px;

    }

    .portal-header h2{

        font-size:34px;

    }

    .portal-description{

        font-size:16px;

    }

    .portal-cards{

        flex-direction:column;

        align-items:center;

        gap:30px;

    }

    .portal-card{

        width:100%;
        max-width:500px;

    }

}

@media(max-width:600px){

    .portal-logo{

        width:90px;

    }

    .portal-header h1{

        font-size:30px;

    }

    .portal-header h3{

        font-size:14px;

        letter-spacing:1px;

    }

    .portal-header h2{

        font-size:28px;

    }

    .portal-description{

        font-size:15px;

    }

    .portal-card{

        padding:30px 25px;

    }

    .portal-icon{

        width:90px;
        height:90px;

    }

    .portal-icon i{

        font-size:40px;

    }

    .portal-card h2{

        font-size:24px;

    }

    .portal-card p{

        font-size:15px;

    }

    .portal-btn{

        height:52px;

        font-size:16px;

    }

    .portal-footer{

        font-size:14px;

    }

}
</style>
</head>
<body>
    <div class="portal-container">

    <div class="portal-header">

        <div class="portal-logo">

            <img src="logofit.png"
                 alt="Fit Function Gym">

        </div>

        <h1>

            <span>FIT</span>

            FUNCTION GYM

        </h1>

        <h3>

            HOME OF HEALTH AND FITNESS

        </h3>

    <div class="portal-cards">

        <!-- ADMIN -->

        <div class="portal-card">

            <div class="portal-icon">

                <i class="fa-solid fa-shield-halved"></i>

            </div>

            <h2>

                ADMIN PORTAL

            </h2>

            <p>

                Access the administrator dashboard to manage
                members, staff, attendance, payments,
                reports and the entire gym system.

            </p>

            <a href="login_admin.php"
               class="portal-btn">

                Go to Admin Login

                <i class="fa-solid fa-arrow-right"></i>

            </a>

        </div>

        <!-- STAFF -->

        <div class="portal-card">

            <div class="portal-icon">

                <i class="fa-solid fa-users"></i>

            </div>

            <h2>

                STAFF PORTAL

            </h2>

            <p>

                Access the staff dashboard to assist members,
                verify attendance, manage daily operations
                and provide customer support.

            </p>

            <a href="login_staff.php"
               class="portal-btn">

                Go to Staff Login

                <i class="fa-solid fa-arrow-right"></i>

            </a>

        </div>
    </div>
</div>
<script>

/* ==========================================
   FADE-IN ANIMATION
========================================== */

document.addEventListener("DOMContentLoaded",function(){

    const cards=document.querySelectorAll(".portal-card");

    cards.forEach(function(card,index){

        card.style.opacity="0";
        card.style.transform="translateY(40px)";

        setTimeout(function(){

            card.style.transition=".7s ease";

            card.style.opacity="1";
            card.style.transform="translateY(0)";

        },index*180);

    });

});

</script>

</body>
</html>