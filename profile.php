<?php
session_start();
include('connect.php');

if(!isset($_SESSION['email'])){
    header('Location: login.php');
    exit();
}

$email=$_SESSION['email'];
$stmt=$con->prepare("SELECT * FROM signup WHERE email=? LIMIT 1");
$stmt->bind_param("s",$email);
$stmt->execute();
$user=$stmt->get_result()->fetch_assoc();
$fullname=trim($user['first_name'].' '.$user['last_name']);
?>
<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>My Profile | Fit Function Gym</title>
<link rel="stylesheet" href="sidebar.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

<link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

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

/*================ MAIN ================*/

.main{

width:100%;

padding:40px;

display:flex;

justify-content:center;

}

.profile-container{

width:100%;

max-width:1280px;

margin-left:40px;

}

.page-title{

font-size:30px;

font-weight:700;

margin-bottom:22px;

}

/*================ PROFILE CARD ================*/

.profile-card{

display:grid;

grid-template-columns:2fr 1fr;

gap:45px;

background:#111;

border:1px solid #2b2b2b;

border-radius:24px;

padding:40px;

box-shadow:

0 20px 45px rgba(0,0,0,.45);

}

.profile-left{

padding-right:10px;

}

.profile-left h2{

font-size:20px;

color:#39ff14;

margin-bottom:15px;

padding-bottom:12px;

border-bottom:1px solid #2a2a2a;

}

.profile-left input[type="date"]{

    color-scheme: dark;

}

.profile-left input[type="date"]::-webkit-calendar-picker-indicator{

    filter: invert(1);

    cursor: pointer;

}

.form-grid{

display:grid;

grid-template-columns:1fr 1fr;

column-gap:18px;

row-gap:14px;

}

.full-width{

grid-column:1/3;

}

.profile-left label{

display:block;

font-size:13px;

font-weight:500;

margin-bottom:6px;

color:#f2f2f2;

}

.profile-left input,
.profile-left select,
.profile-left textarea{

width:100%;

height:46px;

padding:10px 14px;

background:#1b1b1b;

border:1px solid #343434;

border-radius:8px;

color:#fff;

font-size:14px;

outline:none;

transition:.3s;

}

.profile-left input:focus,

.profile-left select:focus,

.profile-left textarea:focus{

border-color:#39ff14;

}

.profile-left select{

    appearance:none;
    -webkit-appearance:none;
    -moz-appearance:none;

    padding-right:42px;

    background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='18' height='18' viewBox='0 0 16 16'%3E%3Cpath d='M3 6l5 5 5-5' stroke='white' stroke-width='2' fill='none' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");

    background-repeat:no-repeat;

    background-position:right 14px center;

    background-size:16px;

}

.profile-left textarea{

height:46px;

padding:10px 14px;

resize:none;

overflow:hidden;

line-height:24px;

}

/*================ RIGHT PANEL ================*/

.profile-right{

display:flex;

flex-direction:column;

align-items:center;

background:#161616;

border:1px solid #292929;

border-radius:15px;

padding:24px;

width:100%;

max-width:300px;

margin:auto;

}

.profile-avatar{

width:120px;

height:120px;

border-radius:50%;

border:4px solid #39ff14;

padding:4px;

object-fit:cover;

background:#101010;

box-shadow:0 0 18px rgba(57,255,20,.25);

transition:.3s;

}

.profile-avatar:hover{

transform:scale(1.03);

}

.profile-name{

font-size:24px;

font-weight:600;

margin:18px 0 20px;

text-align:center;

}

.upload-btn{

display:flex;
align-items:center;
justify-content:center;
gap:12px;

width:100%;
height:40px;

margin-top:15px;
margin-bottom:12px;

background:#181818;

border:2px solid #39ff14;

border-radius:10px;

color:#39ff14;

font-size:15px;
font-weight:600;

cursor:pointer;

transition:.3s;

}

.upload-btn:hover{

background:#39ff14;
color:#000;

}

.date-box{
    position:relative;
}

.date-box input{
    padding-right:48px;
    color-scheme:dark;
}

.calendar-icon{

    position:absolute;

    right:15px;

    top:50%;

    transform:translateY(-50%);

    color:#fff;

    font-size:16px;

    pointer-events:none;

}

.date-box input[type="date"]::-webkit-calendar-picker-indicator{

    position:absolute;

    inset:0;

    width:100%;

    height:100%;

    opacity:0;

    cursor:pointer;

}

#fileName{

min-height:20px;

font-size:12px;
color:#8e8e8e;

margin-bottom:10px 0 18px;

text-align:center;

word-break:break-word;

}

.profile-btn{

width:100%;

height:46px;

font-size:14px;

border-radius:10px;

display:flex;

align-items:center;

justify-content:center;

gap:8px;

margin-top:8px;

}

.save-btn{

background:#39ff14;
margin-bottom: 6px;
color:#000;

}

.save-btn:hover{

background:#57ff3d;

transform:translateY(-2px);

}

.save-btn:disabled{

background:#3b3b3b;

border-color:#555;

color:#999;

cursor:not-allowed;

opacity:.75;

}

.save-btn:disabled:hover{

background:#3b3b3b;

color:#999;

transform:none;

}

.password-btn{

background:transparent;

color:#39ff14;

}

.password-btn:hover{

background:#39ff14;

color:#000;

transform:translateY(-2px);

}

input[readonly]{
    background:#151515;
    color:#bdbdbd;
    cursor:not-allowed;
    border:1px solid #2c2c2c;
}

@media(max-width:1000px){

.profile-card{

grid-template-columns:1fr;

}

.form-grid{

grid-template-columns:1fr;

}

.full-width{

grid-column:auto;

}

.profile-container{

margin-left:0;

}

}

</style>

</head>
<body>

<div class="wrapper">

    <?php include 'sidebar.php'; ?>

    <main class="main">

        <div class="profile-container">

            <h1 class="page-title">
                My Profile
            </h1>

            <form action="update_profile.php"
                  method="POST"
                  enctype="multipart/form-data">

                <div class="profile-card">

    <div class="profile-left">

        <h2>Personal Information</h2>

        <div class="form-grid">

            <!-- First Name -->

            <div>

                <label>First Name</label>

                <input
type="text"
id="firstname"
name="first_name"
value="<?= htmlspecialchars($user['first_name']) ?>"
required>

            </div>

            <!-- Middle Name -->

            <div>

                <label>Middle Name</label>

                <input
type="text"
id="middlename"
name="middlename"
value="<?= htmlspecialchars($user['middlename'] ?? '') ?>">

            </div>

            <!-- Last Name -->

            <div>

                <label>Last Name</label>

               <input
type="text"
id="lastname"
name="last_name"
value="<?= htmlspecialchars($user['last_name']) ?>"
required>

            </div>

            <!-- Suffix -->

            <div>

                <label>Suffix</label>

                <input
type="text"
id="suffix"
name="suffix"
value="<?= htmlspecialchars($user['suffix'] ?? '') ?>">

            </div>

            <!-- Email -->

            <div>

                <label>Email Address</label>

                <input
                type="email"
                name="email"
                readonly
                value="<?= htmlspecialchars($user['email']) ?>">

            </div>

             <!-- Gender -->

            <div>

                <label>Gender</label>

                <select name="gender">

                    <option value="Male"
                    <?= $user['gender']=="Male" ? "selected" : "" ?>>
                    Male
                    </option>

                    <option value="Female"
                    <?= $user['gender']=="Female" ? "selected" : "" ?>>
                    Female
                    </option>

                    <option value="Rather not say"
                    <?= $user['gender']=="Rather not say" ? "selected" : "" ?>>
                    Rather not say
                    </option>

                </select>

            </div>


            <!-- Contact -->

            <div>

                <label>Contact Number</label>

               <input
type="tel"
id="contact"
name="contact_number"
value="<?= htmlspecialchars($user['contact_number']) ?>"
maxlength="11"
placeholder="09XXXXXXXXX"
autocomplete="off"
required>
            </div>

            <!-- Birthdate -->

            <div>

                <label>Birthdate</label>

                <div class="date-box">

    <?php
$minDate = date('Y-m-d', strtotime('-100 years'));
$maxBirthdate = date('Y-m-d', strtotime('-15 years'));
?>

<input
type="date"
id="birthdate"
name="birthdate"
min="<?= $minDate ?>"
max="<?= $maxBirthdate ?>"
value="<?= htmlspecialchars($user['birthdate']) ?>"
required>

    <i class="fa-solid fa-calendar-days calendar-icon"></i>

</div>

            </div>

            <!-- Address -->

            <div class="full-width">

                <label>Address</label>

                <textarea
                name="address"><?= htmlspecialchars($user['address'] ?? '') ?></textarea>

            </div>

        </div>

    </div>
<div class="profile-right">

    <img
    id="previewImage"
    class="profile-avatar"
    src="<?= !empty($user['profile_picture']) ? htmlspecialchars($user['profile_picture']) : 'defaultimg.png'; ?>"
    alt="Profile Picture">

    <h2 class="profile-name">
        <?= htmlspecialchars($fullname) ?>
    </h2>

    <input
    type="file"
    id="profile_picture"
    name="profile_picture"
    accept="image/*"
    hidden>

    <label
    for="profile_picture"
    class="upload-btn">

        <i class="fa-solid fa-image"></i>

        Choose Image

    </label>

    <p id="fileName">
        No image selected
    </p>

    <button
type="submit"
id="saveBtn"
class="profile-btn save-btn"
disabled>

        <i class="fa-solid fa-floppy-disk"></i>
        Save Changes

    </button>

    <button
    type="button"
    class="profile-btn password-btn"
    onclick="window.location='change_password.php'">

        <i class="fa-solid fa-lock"></i>
        Change Password

    </button>

</div>

</div>

</form>

</div>

</main>

</div>

<script>

const menuBtn=document.getElementById("menuBtn");
const sidebar=document.getElementById("sidebar");

menuBtn.addEventListener("click", function (e) {
    e.stopPropagation();
    sidebar.classList.toggle("open");
});

const imageInput=document.getElementById("profile_picture");
const preview=document.getElementById("previewImage");
const fileName=document.getElementById("fileName");

const saveBtn=document.getElementById("saveBtn");

const form=document.querySelector("form");

const fields=form.querySelectorAll("input,select,textarea");

const originalValues={};

fields.forEach(function(field){

    if(field.type!="file"){

        originalValues[field.name]=field.value;

    }

});

function checkChanges(){

    let changed=false;

    fields.forEach(function(field){

        if(field.type==="file"){

            if(field.files.length){

                changed=true;

            }

        }

        else{

            if(field.value!==originalValues[field.name]){

                changed=true;

            }

        }

    });

    saveBtn.disabled=!changed;

}

fields.forEach(function(field){

    field.addEventListener("input",checkChanges);

    field.addEventListener("change",checkChanges);

});

imageInput.addEventListener("change",function(){

    if(this.files.length){

        fileName.innerHTML=this.files[0].name;

        const reader=new FileReader();

        reader.onload=function(e){

            preview.src=e.target.result;

        }

        reader.readAsDataURL(this.files[0]);

    }

    else{

        fileName.innerHTML="No image selected";

    }

    checkChanges();

});

window.onclick=function(e){

    if(

        sidebar.classList.contains("open")

        &&

        !sidebar.contains(e.target)

        &&

        !menuBtn.contains(e.target)

    ){

        sidebar.classList.remove("open");

    }

}

const firstname = document.getElementById("firstname");

firstname.addEventListener("input", function () {

    this.value = this.value
        .replace(/[^A-Za-z '-]/g, "")
        .toLowerCase()
        .replace(/\b\w/g, c => c.toUpperCase());

    checkChanges();

});

const middlename = document.getElementById("middlename");

middlename.addEventListener("input", function () {

    this.value = this.value
        .replace(/[^A-Za-z '-]/g, "")
        .toLowerCase()
        .replace(/\b\w/g, c => c.toUpperCase());

    checkChanges();

});

const lastname = document.getElementById("lastname");

lastname.addEventListener("input", function () {

    this.value = this.value
        .replace(/[^A-Za-z '-]/g, "")
        .toLowerCase()
        .replace(/\b\w/g, c => c.toUpperCase());

    checkChanges();

});

const suffix = document.getElementById("suffix");

suffix.addEventListener("input", function () {

    this.value = this.value
        .toUpperCase()
        .replace(/[^A-Za-z '.-]/g, "");

    checkChanges();

});

function allowLettersOnly(e) {

    const allowed = [
        "Backspace",
        "Delete",
        "ArrowLeft",
        "ArrowRight",
        "Tab",
        "Home",
        "End"
    ];

    if (allowed.includes(e.key) || e.ctrlKey || e.metaKey) {
        return;
    }

    if (!/^[A-Za-z '-]$/.test(e.key)) {
        e.preventDefault();
    }
}

firstname.addEventListener("keydown", allowLettersOnly);
middlename.addEventListener("keydown", allowLettersOnly);
lastname.addEventListener("keydown", allowLettersOnly);


/* ===========================
   CONTACT NUMBER
=========================== */

const contact = document.getElementById("contact");

contact.addEventListener("input", function () {

    // Keep numbers only
    this.value = this.value.replace(/\D/g, "");

    // Maximum 11 digits
    if (this.value.length > 11) {
        this.value = this.value.substring(0, 11);
    }

    checkChanges();

});

contact.addEventListener("keydown", function(e){

    const allowedKeys = [
        "Backspace",
        "Delete",
        "ArrowLeft",
        "ArrowRight",
        "Tab",
        "Home",
        "End"
    ];

    if (
        allowedKeys.includes(e.key) ||
        e.ctrlKey ||
        e.metaKey
    ){
        return;
    }

    // Only allow numbers
    if(!/^[0-9]$/.test(e.key)){
        e.preventDefault();
    }

});
</script>
</body>
</html>
