<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Fit Function Gym | Sign Up</title>
<link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@600;700;800&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/intl-tel-input@24.8.0/build/css/intlTelInput.css" rel="stylesheet">
<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    font-family:'Poppins',sans-serif;
    background-color: black;
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    position:relative;
}

body::before{
    content:"";
    position:absolute;
    inset:0;
    background:rgba(0,0,0,.80);
}

.container{
    width:90%;
    max-width:1500px;
    min-height:90vh;
    display:flex;
    /* Background */
    background-image:
        linear-gradient(rgba(0,0,0,.65), rgba(0,0,0,.65)),
        url("logofit.png");
    background-repeat: no-repeat;    

    background-size: 70%;             
    background-position: center;      
    background-color:#000;
    border-radius:20px;
    overflow:hidden;
    position:relative;
    z-index:2;
}

.right{
    width:100%;
    display:flex;
    justify-content:center;
    align-items:center;
    padding:20px;
}

.logo{

    text-align:center;
    margin-bottom:35px;

}

.logo img{

    width:70px;
    margin-bottom:10px;

}

.logo h2{
    color:white;
    font-size:34px;
}

.logo p{
    font-family:'Orbitron', sans-serif;
    color:#39ff14;
    font-size:13px;
    font-weight:700;
    letter-spacing:2px;
    text-transform:uppercase;
}

.row{
    display:flex;
    gap:12px;
}

.row .input-box:first-child{
    flex:1;
}

.row .input-box:last-child{
    flex:1.35;
}

.form-container{
    width:100%;
    max-width:560px;
    padding:30px;
    background:rgba(15,15,15,.55);
    backdrop-filter:blur(18px);
    -webkit-backdrop-filter:blur(18px);
    border:1px solid rgba(57,255,20,.15);
    border-radius:18px;
    box-shadow:0 15px 40px rgba(0,0,0,.45);
}


.input-box label{
    display:block;
    color:#d9d9d9;
    font-size:14px;
    margin-bottom:8px;
    font-weight:500;
}

.input-box{

    margin-bottom:18px;

}

.input-box input{
    width:100%;
    padding:14px 18px;
    background:rgba(255,255,255,.06);
    border:1px solid rgba(255,255,255,.12);
    border-radius:8px;
    color:#fff;
    font-size:14px;
}

.input-box input[type="date"]{
    color:#fff;
    background:#1b1b1b;
    color-scheme: dark;
}

.input-box input[type="date"]::-webkit-calendar-picker-indicator{
    filter: invert(1);
    cursor:pointer;
}

.input-box select{
    width:100%;
    padding:14px 42px 14px 18px;
    background:#1b1b1b;
    color:#fff;
    border:1px solid rgba(255,255,255,.12);
    border-radius:8px;
    font-size:39ff14px;
    appearance:none;
    -webkit-appearance:none;
    -moz-appearance:none;

    background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='18' height='18' fill='white' viewBox='0 0 16 16'%3E%3Cpath d='M1.5 5.5L8 12l6.5-6.5' stroke='white' stroke-width='2' fill='none'/%3E%3C/svg%3E");
    background-repeat:no-repeat;
    background-position:right 18px center;
    background-size:16px;
}

.input-box select option{
    background:#1b1b1b;
    color:#ffffff;
}

.input-box input::placeholder{
    color:#bfbfbf;
}

.input-box input:focus,
.input-box select:focus{
    border-color:#39ff14;
    box-shadow:0 0 10px rgba(57,255,20,.4);
}

.password-box{
    position:relative;
    width:100%;
}

.password-box input{
    width:100%;
    padding-right:50px;
}

.toggle-password{
    position:absolute;
    right:18px;
    top:50%;
    transform:translateY(-50%);
    font-size:18px;
    color:#bfbfbf;
    cursor:pointer;
    transition:.25s;
    line-height:1;
}

.toggle-password:hover{
    color:#39ff14;
}

button{
    width:100%;
    padding:14px;
    margin-top:10px;
    background:#000;
    color:#fff;
    border:2px solid #39ff14;
    border-radius:8px;
    font-size:16px;
    font-weight:bold;
    cursor:pointer;
    transition:.3s;
}

button:hover{
    background:#39ff14;
    color:#000;
}

.login{
    text-align:center;
    margin-top:18px;
    color:white;
    font-size: 14px;
}

.login a{
    color:#39ff14;
    text-decoration:none;
    font-weight:bold;
}

.login a:hover{
    text-decoration:underline;
}

.back-home{
    text-align:center;
    margin-top:12px;
}

.back-home a{
    color:#bfbfbf;
    text-decoration:none;
    font-size:15px;
    transition:0.3s;
}

.back-home a:hover{
    color:#39ff14;
    text-decoration:underline;
}

select{
    color-scheme: dark;
}

.date-box{
    position:relative;
    width:100%;
}

.date-box input{
    width:100%;
    padding-right:50px;
    color-scheme:dark;
}

.calendar-icon{
    position:absolute;
    right:18px;
    top:50%;
    transform:translateY(-50%);
    font-size:18px;
    color:#bfbfbf;
    pointer-events:none;
}

.date-box input[type="date"]::-webkit-calendar-picker-indicator{
    position:absolute;
    right:0;
    top:0;
    width:100%;
    height:100%;
    opacity:0;
    cursor:pointer;
}

.error.valid{
    color:#39ff14;
}

.error{
    color:#ff4d4d;
}

input.valid,
select.valid{
    border:2px solid #39ff14 !important;
}

input.invalid,
select.invalid{
    border:2px solid #ff4d4d !important;
}

.iti{
    width:100%;
}

.iti input{
    width:100% !important;
    height:48px;
    padding-left:115px !important;
    background:#1b1b1b !important;
    color:#fff !important;
    border:1px solid rgba(255,255,255,.12);
    border-radius:8px;
    font-size:14px;
}

.iti__selected-country{
    background:transparent !important;
    color:#fff !important;
    border:none !important;
}

.iti__selected-country:hover{
    background:transparent !important;
}

.iti__selected-country:focus{
    background:transparent !important;
}

.iti__selected-country-primary{
    background:transparent !important;
}

.iti__country-container{
    background:transparent !important;
}

.iti__country-list{
    background:#1b1b1b !important;
    color:#fff !important;
    border:1px solid rgba(255,255,255,.12);
}

.iti__search-input{
    background:#1b1b1b !important;
    color:#fff !important;
    border:none !important;
    outline:none !important;
}

.iti__search-input::placeholder{
    color:#9d9d9d;
}

.iti__country{
    color:#fff !important;
    background:#1b1b1b !important;
}

.iti__country-name{
    color:#fff !important;
}

.iti__dial-code{
    color:#39ff14 !important;
    font-weight:600;
}

.iti__country:hover,
.iti__country.iti__highlight{
    background:#2b2b2b !important;
    color:#fff !important;
}

.iti__active{
    background:#333 !important;
    color:#fff !important;
}

.iti__country.iti__highlight .iti__dial-code{
    color:#39ff14 !important;
}

.iti__search-input::selection{
    background:#39ff14;
    color:#000;
}

.iti__search-input:-webkit-autofill,
.iti__search-input:-webkit-autofill:hover,
.iti__search-input:-webkit-autofill:focus{
    -webkit-text-fill-color:#fff;
    -webkit-box-shadow:0 0 0px 1000px #1b1b1b inset;
}

.iti__arrow{
    border-top-color:#fff !important;
}

.iti__flag-container{
    background:transparent !important;
}

#contact{
    color:#fff;
}

#contact::placeholder{
    color:#aaa;
}

.iti--allow-dropdown input:focus{
    border-color:#39ff14 !important;
    box-shadow:0 0 10px rgba(57,255,20,.35);
}

</style>

</head>

<body>

<div class="container">

   <div class="right">

    <div class="form-container">

        <div class="logo">
            <img src="logofit.png" alt="Logo">
            <h2>Create Account</h2>
            <p>Join Fit Function Gym today</p>
        </div>

        <form action="signup_process.php" method="POST" id="signupForm">

        <div class="row">

    <div class="input-box">
        <input type="text" id="firstname" name="firstname" placeholder="First Name" required>
        <small id="firstnameError" class="error"></small>
    </div>

    <div class="input-box">
        <input type="text" id="middlename" name="middlename" placeholder="Middle Name">
    </div>

</div>

<div class="row">

    <div class="input-box">
        <input type="text" id="lastname" name="lastname" placeholder="Last Name" required>
        <small id="lastnameError" class="error"></small>
    </div>

    <div class="input-box">
        <input type="text" id="suffix" name="suffix" placeholder="Suffix">
    </div>

</div>

<div class="input-box">
    <input
        type="email"
        id="email"
        name="email"
        placeholder="Email Address"
        required>

    <small id="emailError" class="error"></small>
</div>

           <div class="input-box">

    <label>Contact Number</label>

    <input
    type="tel"
    id="contact"
    name="contact_number"
    placeholder="Contact Number">

    <small id="contactError" class="error"></small>

</div>

            <div class="row">

                <div class="input-box">
                    <label>Gender</label>
                <select id="gender" name="gender" required>
                    <option value="">Please Select</option>
                    <option>Male</option>
                    <option>Female</option>
                    <option>Rather not say</option>
                </select>
                    <small id="genderError" class="error"></small>
                </div>

                <div class="input-box">

    <label>Birthdate</label>

    <div class="date-box">

        <input
            type="date"
            id="birthdate"
            name="birthdate"
            required>

        <i class="fa-solid fa-calendar-days calendar-icon"></i>

    </div>

    <small id="birthdateError" class="error"></small>

</div>
            </div>

<div class="input-box">

    <div class="password-box">
        <input
            type="password"
            id="password"
            name="password"
            placeholder="Password"
            required>

        <i class="fa-solid fa-eye toggle-password"
           onclick="togglePassword('password', this)"></i>
    </div>

    <small id="passwordError" class="error"></small>

</div>

            <div class="input-box">

    <div class="password-box">
        <input
            type="password"
            id="confirm_password"
            name="confirm_password"
            placeholder="Confirm Password"
            required>

        <i class="fa-solid fa-eye toggle-password"
           onclick="togglePassword('confirm_password', this)"></i>
    </div>
    <small id="confirmError" class="error"></small>

</div>
           <button type="submit" name="btn_signup">
    Create Account
</button>

            <div class="login">
                Already have an account?
                <a href="login.php">Log In</a>
            </div>

            <div class="back-home">
                <a href="home.php">← Back to Home</a>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@24.8.0/build/js/intlTelInput.min.js"></script>
<script>

const form = document.getElementById("signupForm");

const firstname = document.getElementById("firstname");
const lastname = document.getElementById("lastname");
const email = document.getElementById("email");
const contact = document.getElementById("contact");
const gender = document.getElementById("gender");
const birthdate = document.getElementById("birthdate");
const password = document.getElementById("password");
const confirmPassword = document.getElementById("confirm_password");

const firstnameError = document.getElementById("firstnameError");
const lastnameError = document.getElementById("lastnameError");
const emailError = document.getElementById("emailError");
const contactError = document.getElementById("contactError");
const genderError = document.getElementById("genderError");
const birthdateError = document.getElementById("birthdateError");
const passwordError = document.getElementById("passwordError");
const confirmError = document.getElementById("confirmError");

const touched = {
    firstname:false,
    lastname:false,
    email:false,
    contact:false,
    gender:false,
    birthdate:false,
    password:false,
    confirm:false
};

function setError(input,errorBox,message){

    input.classList.remove("valid");
    input.classList.add("invalid");

    errorBox.classList.remove("valid");
    errorBox.classList.add("error");
    errorBox.innerHTML=message;

    return false;

}

function setSuccess(input,errorBox,message){

    input.classList.remove("invalid");
    input.classList.add("valid");

    errorBox.classList.add("valid");
    errorBox.classList.add("error");
    errorBox.innerHTML="✓ " + message;

    return true;

}

function toTitleCase(str) {
    return str
        .toLowerCase()
        .replace(/\b\w/g, function(letter) {
            return letter.toUpperCase();
        });
}

function validateFirstname(force=false){

    const value = firstname.value.trim();

    firstname.value = toTitleCase(
    firstname.value.replace(/[^A-Za-z '-]/g,"")
);

    if(!force && !touched.firstname) return true;

    const pattern = /^[A-Za-z]+(?:[ '-][A-Za-z]+)*$/;

    if(value===""){
        return setError(firstname, firstnameError, "First Name is required.");
    }

    if(!pattern.test(value)){
    return setError(firstname, firstnameError, "Only letters, spaces, hyphens (-) and apostrophes (') are allowed.");
}

    return setSuccess(firstname, firstnameError, "Valid First Name");

}

firstname.addEventListener("focus",()=>touched.firstname=true);
firstname.addEventListener("input",()=>validateFirstname());
firstname.addEventListener("blur",()=>validateFirstname());

function validateLastname(force=false){

    const value = lastname.value.trim();

    lastname.value = toTitleCase(
    lastname.value.replace(/[^A-Za-z '-]/g,"")
);

    if(!force && !touched.lastname) return true;

    const pattern = /^[A-Za-z]+(?:[ '-][A-Za-z]+)*$/;

    if(value===""){
    return setError(lastname, lastnameError, "Last Name is required.");
    }

    if(!pattern.test(value)){
    return setError(lastname, lastnameError, "Only letters, spaces, hyphens (-) and apostrophes (') are allowed.");
    }

    return setSuccess(lastname, lastnameError, "Valid Last Name");

}

lastname.addEventListener("focus",()=>touched.lastname=true);
lastname.addEventListener("input",()=>validateLastname());
lastname.addEventListener("blur",()=>validateLastname());

const middlename = document.getElementById("middlename");
const suffix = document.getElementById("suffix");

middlename.addEventListener("input", function(){
    this.value = toTitleCase(
        this.value.replace(/[^A-Za-z '-]/g,"")
    );
});

suffix.addEventListener("input", function(){
    this.value = this.value.toUpperCase().replace(/[^A-Za-z '.-]/g,"");
});

function validateEmail(force = false){

    if(!force && !touched.email) return true;

    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if(email.value.trim() === ""){
        return setError(email, emailError, "Email Address is required.");
    }

    if(!regex.test(email.value)){
        return setError(email, emailError, "Please enter a valid email address.");
    }

    fetch("check_email.php",{

        method:"POST",

        headers:{
            "Content-Type":"application/x-www-form-urlencoded"
        },

        body:"email="+encodeURIComponent(email.value)

    })

    .then(response=>response.text())

    .then(data=>{

        data = data.trim();

        if(data === "exists"){

            setError(
                email,
                emailError,
                "This email is already registered."
            );

        }else{

            setSuccess(
                email,
                emailError,
                "This email is available for registration."
            );

        }

    });

    return true;
}

email.addEventListener("focus", () => {
    touched.email = true;
});

email.addEventListener("input", () => {
    validateEmail();
});

email.addEventListener("blur", () => {
    validateEmail();
});

form.addEventListener("submit", function(e){

    e.preventDefault();

    const valid =
        validateFirstname(true) &&
        validateLastname(true) &&
        validateContact(true) &&
        validateGender(true) &&
        validateBirthdate(true) &&
        validatePassword(true) &&
        validateConfirmPassword(true);

    if(!valid){
        return;
    }

    fetch("check_email.php",{

        method:"POST",

        headers:{
            "Content-Type":"application/x-www-form-urlencoded"
        },

        body:"email="+encodeURIComponent(email.value)

    })

    .then(response=>response.text())

    .then(data=>{

        data = data.trim();

        if(data === "exists"){

            setError(
                email,
                emailError,
                "This email is already registered."
            );

            email.focus();

            return;
        }

        contact.value = iti.getNumber();

        form.submit();

    });

});

const iti = window.intlTelInput(contact, {
    initialCountry: "ph",
    preferredCountries: ["ph","us","gb","ca","au","jp","kr"],
    separateDialCode: true,
    nationalMode: false,
    autoPlaceholder: "aggressive",
    utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@24.8.0/build/js/utils.js"
});

function validateContact(force=false){

    if(!force && !touched.contact) return true;

    if(contact.value.trim()===""){

        return setError(contact, contactError, "Contact Number is required.");

    }

    if(!iti.isValidNumber()){

       return setError(contact, contactError, "Please enter a valid phone number.");

    }

   return setSuccess(contact, contactError, "Valid Contact Number");

}

contact.addEventListener("focus", () => {
    touched.contact = true;
});

contact.addEventListener("blur", () => {
    validateContact();
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

    if (allowedKeys.includes(e.key) || e.ctrlKey || e.metaKey) {
        return;
    }

    if (!/^[0-9]$/.test(e.key)) {
        e.preventDefault();
    }

});

contact.addEventListener("input",()=>{

    if(touched.contact){

        validateContact();

    }

});

contact.addEventListener("blur",()=>{

    validateContact();

});

contact.addEventListener("countrychange",()=>{

    validateContact();

});

function validateGender(force=false){

    if(!force && !touched.gender) return true;

    if(gender.value===""){

    return setError(gender, genderError, "Please select your gender.");

    }

    return setSuccess(gender, genderError, "Selected");

}

gender.addEventListener("focus", () => {
    touched.gender = true;
});

gender.addEventListener("blur", () => {
    validateGender();
});

gender.addEventListener("change", () => {
    validateGender();
});

function validateBirthdate(force=false){

    if(!force && !touched.birthdate) return true;

    if(birthdate.value===""){

    return setError(birthdate, birthdateError, "Birthdate is required.");

    }

    const today=new Date();
    const dob=new Date(birthdate.value);

    if(dob>today){

    return setError(birthdate, birthdateError, "Birthdate cannot be in the future.");

    }

    let age=today.getFullYear()-dob.getFullYear();

    const month=today.getMonth()-dob.getMonth();

    if(month<0 || (month===0 && today.getDate()<dob.getDate())){

        age--;

    }

    if(age<15){

    return setError(birthdate, birthdateError, "You must be at least 15 years old.");

    }

    return setSuccess(birthdate, birthdateError, "Valid Birthdate");

}

birthdate.addEventListener("focus", () => {
    touched.birthdate = true;
});

birthdate.addEventListener("blur", () => {
    validateBirthdate();
});

birthdate.addEventListener("change", () => {
    validateBirthdate();
});

function validatePassword(force=false){

    if(!force && !touched.password) return true;

    const regex=/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;

    if(password.value===""){
        return setError(password,passwordError,"Password is required.");
    }

    if(!regex.test(password.value)){
        return setError(password,passwordError,"Password must contain at least 8 characters, uppercase, lowercase, number and special character.");
    }

    validateConfirmPassword();

    return setSuccess(password,passwordError,"Strong Password");

}

password.addEventListener("focus",()=>{

    touched.password=true;

});

password.addEventListener("input",()=>{

    validatePassword();
    validateConfirmPassword();

});

password.addEventListener("blur",()=>{

    validatePassword();
    validateConfirmPassword();

});

function validateConfirmPassword(force=false){

    if(!force && !touched.confirm) return true;

    if(password.value===""){

        return setError(confirmPassword, confirmError, "Please enter your password first.");

    }

    if(confirmPassword.value===""){

        return setError(confirmPassword, confirmError, "Please confirm your password.");

    }

    if(password.value!==confirmPassword.value){

        return setError(confirmPassword, confirmError, "Passwords do not match.");

    }

    return setSuccess(confirmPassword, confirmError, "Passwords match");

}

confirmPassword.addEventListener("focus",()=>{

    touched.confirm=true;

});

confirmPassword.addEventListener("input",()=>{

    validateConfirmPassword();

});

confirmPassword.addEventListener("blur",()=>{

    validateConfirmPassword();

});

function togglePassword(id,icon){

    const input=document.getElementById(id);

    if(input.type==="password"){

        input.type="text";

        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");

    }else{

        input.type="password";

        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");

    }

}

form.addEventListener("submit", function(e){

    const valid =
        validateFirstname(true) &&
        validateLastname(true) &&
        validateEmail(true) &&
        validateContact(true) &&
        validateGender(true) &&
        validateBirthdate(true) &&
        validatePassword(true) &&
        validateConfirmPassword(true);

    if(!valid){
        e.preventDefault();
        return false;
    }

    contact.value = iti.getNumber();

    return true;

});

</script>

</body>
</html>