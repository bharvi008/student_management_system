<?php

session_start();

if(!isset($_SESSION['admin']))
{
    header("Location:../login.php");
    exit();
}

include "../db.php";

$message = "";
$message_type = "";


/* =========================================
   ADD TEACHER
========================================= */

if(isset($_POST['save']))
{

    $name = mysqli_real_escape_string(
        $conn,
        trim($_POST['name'])
    );

    $email = mysqli_real_escape_string(
        $conn,
        trim($_POST['email'])
    );

    $phone = mysqli_real_escape_string(
        $conn,
        trim($_POST['phone'])
    );

    $subject = mysqli_real_escape_string(
        $conn,
        trim($_POST['subject'])
    );

    $qualification = mysqli_real_escape_string(
        $conn,
        $_POST['qualification']
    );

    $password = md5($_POST['password']);

    $photo = "";


    /* =========================================
       CHECK PHONE
    ========================================= */

    if(!preg_match('/^[0-9]{10}$/', $phone))
    {
        $message = "Phone number must contain exactly 10 digits.";
        $message_type = "error";
    }
    else
    {

        /* =========================================
           CHECK DUPLICATE EMAIL
        ========================================= */

        $check = mysqli_query($conn,"
            SELECT id
            FROM teacher
            WHERE email='$email'
        ");


        if(mysqli_num_rows($check) > 0)
        {
            $message = "Teacher with this email already exists.";
            $message_type = "error";
        }
        else
        {

            /* =========================================
               PHOTO UPLOAD
            ========================================= */

            if(
                isset($_FILES['photo']) &&
                $_FILES['photo']['error'] == 0
            )
            {

                $original_name = $_FILES['photo']['name'];

                $extension = strtolower(
                    pathinfo(
                        $original_name,
                        PATHINFO_EXTENSION
                    )
                );

                $allowed = array(
                    'jpg',
                    'jpeg',
                    'png',
                    'webp'
                );


                if(in_array($extension,$allowed))
                {

                    $upload_dir = "../uploads/";

                    if(!is_dir($upload_dir))
                    {
                        mkdir(
                            $upload_dir,
                            0777,
                            true
                        );
                    }


                    $photo =
                        "teacher_".
                        time().
                        "_".
                        rand(1000,9999).
                        ".".
                        $extension;


                    move_uploaded_file(
                        $_FILES['photo']['tmp_name'],
                        $upload_dir.$photo
                    );

                }
                else
                {

                    $message =
                        "Only JPG, JPEG, PNG or WEBP photo is allowed.";

                    $message_type = "error";

                }

            }


            /* =========================================
               INSERT TEACHER
            ========================================= */

            if($message_type != "error")
            {

                $sql = "
                    INSERT INTO teacher
                    (
                        name,
                        email,
                        phone,
                        subject,
                        qualification,
                        password,
                        photo
                    )

                    VALUES
                    (
                        '$name',
                        '$email',
                        '$phone',
                        '$subject',
                        '$qualification',
                        '$password',
                        '$photo'
                    )
                ";


                if(mysqli_query($conn,$sql))
                {

                    echo "
                    <script>

                        alert('Teacher Added Successfully');

                        window.location='manage_teacher.php';

                    </script>
                    ";

                    exit();

                }
                else
                {

                    $message =
                        "Unable to add teacher. Please try again.";

                    $message_type = "error";

                }

            }

        }

    }

}

?>


<!DOCTYPE html>
<html>

<head>

<title>EduTrack | Add Teacher</title>

<meta
    name="viewport"
    content="width=device-width, initial-scale=1"
>

<style>

/* =========================================
   RESET
========================================= */

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Arial, Helvetica, sans-serif;
}

body{
    background:#F7F7F5;
    color:#292524;
}


/* =========================================
   SIDEBAR
========================================= */

.sidebar{

    position:fixed;

    left:0;
    top:0;

    width:250px;
    height:100vh;

    background:#171412;

    color:white;

    padding:22px 15px;

    box-shadow:
        5px 0 25px rgba(0,0,0,.12);

    overflow-y:auto;

    z-index:1000;
}


/* LOGO */

.logo{

    padding:5px 12px 25px;

    border-bottom:1px solid #3A3531;

    margin-bottom:20px;
}

.logo a{

    color:white;

    text-decoration:none;

    font-size:27px;

    font-weight:bold;
}

.logo span{
    color:#FF8A00;
}

.admin-label{

    font-size:11px;

    color:#A8A29E;

    text-transform:uppercase;

    letter-spacing:1.5px;

    margin-top:6px;
}


/* =========================================
   SIDEBAR MENU
========================================= */

.sidebar ul{
    list-style:none;
}

.sidebar ul li{
    margin:5px 0;
}

.sidebar ul li a{

    display:flex;

    align-items:center;

    gap:12px;

    color:#D6D3D1;

    text-decoration:none;

    padding:13px 14px;

    border-radius:11px;

    font-size:14px;

    transition:.3s;
}

.sidebar ul li a:hover{

    background:#292524;

    color:#FFB000;

    transform:translateX(4px);
}

.sidebar ul li a.active{

    background:
        linear-gradient(
            90deg,
            #FF6B00,
            #FFB000
        );

    color:white;

    box-shadow:
        0 7px 20px
        rgba(255,107,0,.25);
}


/* LOGOUT */

.sidebar .logout{

    margin-top:20px;

    padding-top:18px;

    border-top:1px solid #3A3531;
}

.sidebar .logout a{
    color:#FCA5A5;
}

.sidebar .logout a:hover{

    background:#7F1D1D;

    color:white;
}


/* =========================================
   MAIN
========================================= */

.main{

    margin-left:250px;

    min-height:100vh;

    padding:0 30px 40px;
}


/* =========================================
   TOPBAR
========================================= */

.topbar{

    height:82px;

    background:white;

    margin:0 -30px 30px;

    padding:0 30px;

    display:flex;

    justify-content:space-between;

    align-items:center;

    border-bottom:1px solid #E7E5E4;
}

.top-title h1{

    font-size:25px;

    color:#1C1917;
}

.top-title p{

    margin-top:5px;

    color:#78716C;

    font-size:13px;
}


/* ADMIN PROFILE */

.admin-area{

    display:flex;

    align-items:center;

    gap:12px;
}

.admin-info{
    text-align:right;
}

.admin-info strong{

    display:block;

    font-size:14px;
}

.admin-info span{

    display:block;

    color:#78716C;

    font-size:12px;

    margin-top:3px;
}

.admin-avatar{

    width:43px;
    height:43px;

    border-radius:12px;

    background:
        linear-gradient(
            135deg,
            #FF6B00,
            #FFB000
        );

    display:flex;

    align-items:center;

    justify-content:center;

    font-size:20px;
}


/* =========================================
   PAGE HEADER
========================================= */

.page-header{

    display:flex;

    justify-content:space-between;

    align-items:center;

    gap:15px;

    flex-wrap:wrap;

    margin-bottom:25px;
}

.page-header h2{

    font-size:25px;

    color:#1C1917;
}

.page-header p{

    color:#78716C;

    font-size:13px;

    margin-top:6px;
}

.back-btn{

    text-decoration:none;

    background:white;

    border:1px solid #E7E5E4;

    color:#44403C;

    padding:11px 17px;

    border-radius:10px;

    font-size:13px;

    font-weight:bold;

    transition:.3s;
}

.back-btn:hover{

    border-color:#FF8A00;

    color:#FF6B00;

    transform:translateY(-2px);
}


/* =========================================
   MESSAGE
========================================= */

.message{

    max-width:950px;

    margin:0 auto 20px;

    padding:14px 17px;

    border-radius:12px;

    font-size:13px;

    font-weight:bold;
}

.message.error{

    background:#FEE2E2;

    color:#B91C1C;

    border:1px solid #FECACA;
}


/* =========================================
   FORM CARD
========================================= */

.form-card{

    max-width:950px;

    margin:auto;

    background:white;

    border:1px solid #E7E5E4;

    border-radius:20px;

    padding:30px;

    box-shadow:
        0 8px 30px
        rgba(0,0,0,.06);
}


/* FORM TOP */

.form-top{

    display:flex;

    align-items:center;

    gap:15px;

    padding-bottom:20px;

    margin-bottom:28px;

    border-bottom:1px solid #EEEAE7;
}

.form-icon{

    width:52px;
    height:52px;

    border-radius:14px;

    background:#FFF1E6;

    display:flex;

    align-items:center;

    justify-content:center;

    font-size:25px;
}

.form-top h3{

    font-size:19px;

    color:#1C1917;
}

.form-top p{

    color:#78716C;

    font-size:12px;

    margin-top:4px;
}


/* =========================================
   FORM GRID
========================================= */

.form-grid{

    display:grid;

    grid-template-columns:
        repeat(2,1fr);

    gap:20px;
}

.form-group{

    display:flex;

    flex-direction:column;
}

.form-group.full{

    grid-column:1 / -1;
}

.form-group label{

    font-size:13px;

    font-weight:bold;

    color:#44403C;

    margin-bottom:8px;
}

.required{
    color:#DC2626;
}


/* INPUT */

.form-group input,
.form-group select{

    width:100%;

    padding:13px 14px;

    border:1px solid #D6D3D1;

    border-radius:10px;

    background:#FAFAF9;

    color:#292524;

    font-size:14px;

    outline:none;

    transition:.3s;
}

.form-group input:focus,
.form-group select:focus{

    border-color:#FF8A00;

    background:white;

    box-shadow:
        0 0 0 3px
        rgba(255,138,0,.10);
}

.form-group small{

    color:#A8A29E;

    font-size:11px;

    margin-top:6px;
}


/* =========================================
   PASSWORD
========================================= */

.password-box{
    position:relative;
}

.password-box input{
    padding-right:48px;
}

.toggle-password{

    position:absolute;

    right:14px;

    top:50%;

    transform:translateY(-50%);

    cursor:pointer;

    user-select:none;
}


/* =========================================
   PHOTO UPLOAD
========================================= */

.photo-section{

    display:flex;

    align-items:center;

    gap:20px;

    padding:18px;

    background:#FAFAF9;

    border:1px dashed #D6D3D1;

    border-radius:14px;
}

.photo-preview{

    width:90px;
    height:90px;

    border-radius:15px;

    background:#FFF1E6;

    border:2px solid #FED7AA;

    display:flex;

    align-items:center;

    justify-content:center;

    overflow:hidden;

    flex-shrink:0;

    font-size:35px;
}

.photo-preview img{

    width:100%;
    height:100%;

    object-fit:cover;
}

.photo-content{
    flex:1;
}

.photo-content h4{

    font-size:14px;

    margin-bottom:6px;
}

.photo-content p{

    color:#78716C;

    font-size:11px;

    margin-bottom:12px;
}

.photo-content input{

    background:white;
}


/* =========================================
   INFORMATION BOX
========================================= */

.info-box{

    grid-column:1 / -1;

    background:#FFF7ED;

    border:1px solid #FED7AA;

    border-radius:13px;

    padding:14px 16px;

    display:flex;

    gap:10px;

    align-items:flex-start;
}

.info-box .icon{
    font-size:18px;
}

.info-box strong{

    display:block;

    font-size:12px;

    color:#9A3412;

    margin-bottom:3px;
}

.info-box p{

    color:#9A3412;

    font-size:11px;

    line-height:1.5;
}


/* =========================================
   ACTIONS
========================================= */

.form-actions{

    margin-top:30px;

    padding-top:20px;

    border-top:1px solid #EEEAE7;

    display:flex;

    justify-content:flex-end;

    gap:12px;
}

.cancel-btn{

    text-decoration:none;

    padding:13px 22px;

    border:1px solid #D6D3D1;

    color:#57534E;

    border-radius:10px;

    font-size:14px;

    font-weight:bold;

    transition:.3s;
}

.cancel-btn:hover{
    background:#F5F5F4;
}

.save-btn{

    border:none;

    padding:13px 25px;

    border-radius:10px;

    background:
        linear-gradient(
            90deg,
            #FF6B00,
            #FFB000
        );

    color:white;

    font-size:14px;

    font-weight:bold;

    cursor:pointer;

    box-shadow:
        0 6px 18px
        rgba(255,107,0,.22);

    transition:.3s;
}

.save-btn:hover{

    transform:translateY(-2px);

    box-shadow:
        0 10px 25px
        rgba(255,107,0,.30);
}


/* =========================================
   FOOTER
========================================= */

.footer{

    text-align:center;

    padding:30px 10px 5px;

    color:#78716C;

    font-size:12px;
}

.footer strong{
    color:#FF6B00;
}


/* =========================================
   RESPONSIVE
========================================= */

@media(max-width:900px){

    .sidebar{
        width:210px;
    }

    .main{

        margin-left:210px;

        padding-left:20px;
        padding-right:20px;
    }

    .topbar{

        margin-left:-20px;
        margin-right:-20px;

        padding-left:20px;
        padding-right:20px;
    }

}


@media(max-width:700px){

    .sidebar{

        position:relative;

        width:100%;

        height:auto;

        padding:15px;
    }

    .logo{
        text-align:center;
    }

    .sidebar ul{

        display:grid;

        grid-template-columns:
            repeat(2,1fr);

        gap:5px;
    }

    .main{

        margin-left:0;

        padding:0 15px 30px;
    }

    .topbar{

        margin:0 -15px 25px;

        padding:15px;

        min-height:70px;
    }

    .admin-info{
        display:none;
    }

    .form-grid{
        grid-template-columns:1fr;
    }

    .form-group.full,
    .info-box{
        grid-column:auto;
    }

    .form-card{
        padding:20px;
    }

    .photo-section{

        flex-direction:column;

        align-items:flex-start;
    }

}


@media(max-width:480px){

    .sidebar ul{
        grid-template-columns:1fr;
    }

    .form-actions{
        flex-direction:column;
    }

    .cancel-btn,
    .save-btn{

        width:100%;

        text-align:center;
    }

}

</style>

</head>


<body>


<!-- =========================================
     SIDEBAR
========================================= -->

<aside class="sidebar">


    <div class="logo">

        <a href="admindashboard.php">

            Edu<span>Track</span>

        </a>

        <div class="admin-label">
            Administration
        </div>

    </div>


    <ul>


        <li>

            <a href="admindashboard.php">

                🏠

                <span>Dashboard</span>

            </a>

        </li>


        <li>

            <a href="add_student.php">

                ➕

                <span>Add Student</span>

            </a>

        </li>


        <li>

            <a href="manage_student.php">

                👨‍🎓

                <span>Manage Students</span>

            </a>

        </li>


        <li>

            <a href="attendance.php">

                📅

                <span>Attendance</span>

            </a>

        </li>


        <li>

            <a href="result.php">

                📊

                <span>Results</span>

            </a>

        </li>


        <li>

            <a href="course.php">

                📚

                <span>Courses</span>

            </a>

        </li>


        <li>

            <a href="attendance_report.php">

                📄

                <span>Attendance Report</span>

            </a>

        </li>


        <li>

            <a href="result_report.php">

                📄

                <span>Result Report</span>

            </a>

        </li>


        <li>

            <a
                href="add_teacher.php"
                class="active"
            >

                👨‍🏫

                <span>Add Teacher</span>

            </a>

        </li>


        <li>

            <a href="manage_teacher.php">

                📋

                <span>Manage Teachers</span>

            </a>

        </li>


        <li class="logout">

            <a
                href="logout.php"
                onclick="
                return confirm(
                    'Are you sure you want to logout?'
                )
                "
            >

                🚪

                <span>Logout</span>

            </a>

        </li>


    </ul>


</aside>



<!-- =========================================
     MAIN
========================================= -->

<main class="main">


    <!-- TOP BAR -->

    <header class="topbar">


        <div class="top-title">

            <h1>
                Add Teacher
            </h1>

            <p>
                EduTrack Administration
            </p>

        </div>


        <div class="admin-area">


            <div class="admin-info">

                <strong>
                    Administrator
                </strong>

                <span>
                    System Admin
                </span>

            </div>


            <div class="admin-avatar">
                👨‍💼
            </div>


        </div>


    </header>



    <!-- PAGE HEADER -->

    <div class="page-header">


        <div>

            <h2>
                👨‍🏫 Add New Teacher
            </h2>

            <p>
                Create a teacher account and add professional information.
            </p>

        </div>


        <a
            href="manage_teacher.php"
            class="back-btn"
        >
            ← Back to Teachers
        </a>


    </div>



    <!-- MESSAGE -->

    <?php if($message != "") { ?>

        <div class="message <?php echo $message_type; ?>">

            ⚠️ <?php echo $message; ?>

        </div>

    <?php } ?>



    <!-- =========================================
         FORM CARD
    ========================================= -->

    <div class="form-card">


        <div class="form-top">


            <div class="form-icon">
                👨‍🏫
            </div>


            <div>

                <h3>
                    Teacher Information
                </h3>

                <p>
                    Enter personal, academic and login details for the teacher.
                </p>

            </div>


        </div>



        <form
            method="POST"
            enctype="multipart/form-data"
            onsubmit="return validateTeacherForm()"
        >


            <div class="form-grid">


                <!-- NAME -->

                <div class="form-group">

                    <label>

                        Teacher Name

                        <span class="required">
                            *
                        </span>

                    </label>

                    <input
                        type="text"
                        name="name"
                        placeholder="Enter teacher name"
                        required
                    >

                </div>



                <!-- EMAIL -->

                <div class="form-group">

                    <label>

                        Email Address

                        <span class="required">
                            *
                        </span>

                    </label>

                    <input
                        type="email"
                        name="email"
                        placeholder="teacher@example.com"
                        required
                    >

                </div>



                <!-- PHONE -->

                <div class="form-group">

                    <label>

                        Phone Number

                        <span class="required">
                            *
                        </span>

                    </label>

                    <input
                        type="text"
                        name="phone"
                        id="phone"
                        placeholder="10 digit phone number"
                        maxlength="10"
                        pattern="[0-9]{10}"
                        inputmode="numeric"
                        required
                    >

                    <small>
                        Enter exactly 10 digits.
                    </small>

                </div>



                <!-- SUBJECT -->

                <div class="form-group">

                    <label>

                        Subject

                        <span class="required">
                            *
                        </span>

                    </label>

                    <input
                        type="text"
                        name="subject"
                        placeholder="Example: Java Programming"
                        required
                    >

                </div>



                <!-- QUALIFICATION -->

                <div class="form-group">

                    <label>

                        Qualification

                        <span class="required">
                            *
                        </span>

                    </label>

                    <select
                        name="qualification"
                        required
                    >

                        <option value="">
                            Select Qualification
                        </option>

                        <option value="MCA">
                            MCA
                        </option>

                        <option value="M.Sc IT">
                            M.Sc IT
                        </option>

                        <option value="M.Tech">
                            M.Tech
                        </option>

                        <option value="B.Tech">
                            B.Tech
                        </option>

                        <option value="BCA">
                            BCA
                        </option>

                        <option value="B.Sc IT">
                            B.Sc IT
                        </option>

                        <option value="Ph.D">
                            Ph.D
                        </option>

                        <option value="B.Ed">
                            B.Ed
                        </option>

                        <option value="M.Ed">
                            M.Ed
                        </option>

                        <option value="NET Qualified">
                            NET Qualified
                        </option>

                    </select>

                </div>



                <!-- PASSWORD -->

                <div class="form-group">

                    <label>

                        Login Password

                        <span class="required">
                            *
                        </span>

                    </label>


                    <div class="password-box">

                        <input
                            type="password"
                            name="password"
                            id="password"
                            placeholder="Create teacher password"
                            required
                        >

                        <span
                            class="toggle-password"
                            onclick="togglePassword()"
                            title="Show / Hide Password"
                        >
                            👁️
                        </span>

                    </div>


                    <small>
                        Teacher will use this password to login.
                    </small>

                </div>



                <!-- PHOTO -->

                <div class="form-group full">

                    <label>
                        Teacher Photo
                    </label>


                    <div class="photo-section">


                        <div
                            class="photo-preview"
                            id="photoPreview"
                        >
                            👤
                        </div>


                        <div class="photo-content">

                            <h4>
                                Upload Profile Photo
                            </h4>

                            <p>
                                JPG, JPEG, PNG or WEBP image recommended.
                            </p>


                            <input
                                type="file"
                                name="photo"
                                id="photo"
                                accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                                onchange="previewPhoto(event)"
                            >

                        </div>


                    </div>


                </div>



                <!-- INFO -->

                <div class="info-box">


                    <div class="icon">
                        💡
                    </div>


                    <div>

                        <strong>
                            Teacher Login Information
                        </strong>

                        <p>
                            After creating the account, the teacher can login using the email address and password entered above.
                        </p>

                    </div>


                </div>


            </div>



            <!-- =========================================
                 ACTIONS
            ========================================= -->

            <div class="form-actions">


                <a
                    href="manage_teacher.php"
                    class="cancel-btn"
                >
                    Cancel
                </a>


                <button
                    type="submit"
                    name="save"
                    class="save-btn"
                >
                    👨‍🏫 Add Teacher
                </button>


            </div>


        </form>


    </div>



    <!-- FOOTER -->

    <footer class="footer">

        <p>

            <strong>EduTrack</strong>
            — Student Management System

        </p>

        <p>
            © 2026 EduTrack. All Rights Reserved.
        </p>

    </footer>


</main>



<script>

/* =========================================
   PHONE - NUMBERS ONLY
========================================= */

document
.getElementById("phone")
.addEventListener("input", function()
{

    this.value =
        this.value.replace(/[^0-9]/g,'');

});


/* =========================================
   SHOW / HIDE PASSWORD
========================================= */

function togglePassword()
{

    const password =
        document.getElementById("password");

    const icon =
        document.querySelector(".toggle-password");


    if(password.type === "password")
    {

        password.type = "text";

        icon.innerHTML = "🙈";

    }
    else
    {

        password.type = "password";

        icon.innerHTML = "👁️";

    }

}


/* =========================================
   PHOTO PREVIEW
========================================= */

function previewPhoto(event)
{

    const file =
        event.target.files[0];

    const preview =
        document.getElementById("photoPreview");


    if(file)
    {

        const reader =
            new FileReader();


        reader.onload =
            function(e)
            {

                preview.innerHTML =
                    '<img src="' +
                    e.target.result +
                    '" alt="Teacher Photo">';

            };


        reader.readAsDataURL(file);

    }
    else
    {

        preview.innerHTML = "👤";

    }

}


/* =========================================
   VALIDATE
========================================= */

function validateTeacherForm()
{

    const phone =
        document.getElementById("phone").value;


    if(phone.length !== 10)
    {

        alert(
            "Phone number must contain exactly 10 digits."
        );

        return false;

    }


    return true;

}

</script>


</body>

</html>