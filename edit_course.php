<?php

session_start();

if(!isset($_SESSION['admin']))
{
    header("Location:../login.php");
    exit();
}

include "../db.php";

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if($id <= 0)
{
    header("Location:course.php");
    exit();
}


/* =========================
   GET COURSE
========================= */

$data = mysqli_query($conn,"
    SELECT * FROM course
    WHERE id='$id'
");

if(mysqli_num_rows($data) == 0)
{
    header("Location:course.php");
    exit();
}

$row = mysqli_fetch_assoc($data);

$message = "";
$message_type = "";


/* =========================
   UPDATE COURSE
========================= */

if(isset($_POST['update']))
{
    $course = mysqli_real_escape_string(
        $conn,
        trim($_POST['course_name'])
    );

    $duration = mysqli_real_escape_string(
        $conn,
        trim($_POST['duration'])
    );

    $fees = mysqli_real_escape_string(
        $conn,
        trim($_POST['fees'])
    );


    if($course == "" || $duration == "" || $fees == "")
    {
        $message = "Please fill all fields.";
        $message_type = "error";
    }
    else
    {

        /* Check duplicate course */

        $check = mysqli_query($conn,"
            SELECT id
            FROM course
            WHERE course_name='$course'
            AND id!='$id'
        ");


        if(mysqli_num_rows($check) > 0)
        {
            $message = "This course already exists.";
            $message_type = "error";
        }
        else
        {

            $update = mysqli_query($conn,"
                UPDATE course SET

                course_name='$course',
                duration='$duration',
                fees='$fees'

                WHERE id='$id'
            ");


            if($update)
            {
                header("Location:course.php");
                exit();
            }
            else
            {
                $message = "Course update failed.";
                $message_type = "error";
            }

        }

    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1">

<title>EduTrack | Edit Course</title>


<style>

/* =========================
   RESET
========================= */

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


/* =========================
   SIDEBAR
========================= */

.sidebar{

    position:fixed;

    left:0;
    top:0;

    width:250px;
    height:100vh;

    background:#171412;

    color:white;

    padding:22px 15px;

    z-index:1000;

    box-shadow:5px 0 25px rgba(0,0,0,.12);

    overflow-y:auto;
}


/* LOGO */

.logo{

    padding:5px 12px 25px;

    border-bottom:1px solid #3a3531;

    margin-bottom:20px;
}

.logo a{

    color:white;

    text-decoration:none;

    font-size:27px;

    font-weight:bold;

    letter-spacing:.5px;
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


/* MENU */

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

    background:linear-gradient(
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

    border-top:1px solid #3a3531;

    padding-top:18px;
}

.sidebar .logout a{

    color:#FCA5A5;
}

.sidebar .logout a:hover{

    background:#7F1D1D;

    color:white;
}


/* =========================
   MAIN
========================= */

.main{

    margin-left:250px;

    min-height:100vh;

    padding:0 30px 40px;
}


/* =========================
   TOPBAR
========================= */

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


/* PAGE TITLE */

.top-title h1{

    font-size:25px;

    color:#1C1917;
}

.top-title p{

    margin-top:5px;

    color:#78716C;

    font-size:13px;
}


/* ADMIN */

.admin-area{

    display:flex;

    align-items:center;

    gap:15px;
}

.admin-info{

    text-align:right;
}

.admin-info strong{

    display:block;

    font-size:14px;
}

.admin-info span{

    color:#78716C;

    font-size:12px;
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

    color:white;

    font-size:20px;

    box-shadow:
        0 5px 15px
        rgba(255,107,0,.25);
}


/* =========================
   CONTENT
========================= */

.content{

    max-width:900px;

    margin:auto;
}


/* =========================
   PAGE BANNER
========================= */

.page-banner{

    background:
        linear-gradient(
            120deg,
            #1C1917,
            #292524
        );

    border-radius:20px;

    padding:30px;

    color:white;

    position:relative;

    overflow:hidden;

    margin-bottom:25px;
}

.page-banner:after{

    content:"";

    position:absolute;

    width:220px;
    height:220px;

    border-radius:50%;

    right:-80px;
    top:-100px;

    background:rgba(255,138,0,.13);
}

.page-banner h2{

    position:relative;

    z-index:2;

    font-size:27px;

    margin-bottom:8px;
}

.page-banner p{

    position:relative;

    z-index:2;

    color:#D6D3D1;

    font-size:14px;
}


/* =========================
   CARD
========================= */

.card{

    background:white;

    border:1px solid #E7E5E4;

    border-radius:18px;

    padding:30px;

    box-shadow:
        0 5px 20px
        rgba(0,0,0,.04);
}


/* CARD HEADER */

.card-header{

    display:flex;

    align-items:center;

    gap:13px;

    margin-bottom:25px;
}

.card-icon{

    width:48px;
    height:48px;

    border-radius:13px;

    background:#FFF1E6;

    display:flex;

    align-items:center;

    justify-content:center;

    font-size:22px;
}

.card-header h3{

    font-size:19px;

    color:#1C1917;
}

.card-header p{

    margin-top:4px;

    font-size:12px;

    color:#78716C;
}


/* =========================
   FORM
========================= */

.form-group{

    margin-bottom:20px;
}

.form-group label{

    display:block;

    font-size:13px;

    font-weight:bold;

    color:#44403C;

    margin-bottom:8px;
}

.form-group input{

    width:100%;

    padding:14px 15px;

    border:1px solid #D6D3D1;

    border-radius:10px;

    outline:none;

    font-size:14px;

    transition:.3s;
}

.form-group input:focus{

    border-color:#FF8A00;

    box-shadow:
        0 0 0 3px
        rgba(255,107,0,.10);
}


/* =========================
   BUTTONS
========================= */

.button-row{

    display:flex;

    gap:12px;

    margin-top:25px;
}

.update-btn{

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

    transition:.3s;
}

.update-btn:hover{

    transform:translateY(-2px);

    box-shadow:
        0 8px 18px
        rgba(255,107,0,.25);
}

.cancel-btn{

    display:inline-flex;

    align-items:center;

    justify-content:center;

    padding:13px 25px;

    border-radius:10px;

    background:#F5F5F4;

    color:#57534E;

    text-decoration:none;

    font-size:14px;

    font-weight:bold;

    transition:.3s;
}

.cancel-btn:hover{

    background:#E7E5E4;
}


/* =========================
   MESSAGE
========================= */

.message{

    padding:14px 18px;

    border-radius:11px;

    margin-bottom:20px;

    font-size:14px;

    font-weight:bold;
}

.message.error{

    background:#FEF2F2;

    color:#DC2626;

    border:1px solid #FECACA;
}


/* =========================
   FOOTER
========================= */

.footer{

    text-align:center;

    padding:25px 10px 5px;

    color:#78716C;

    font-size:12px;
}

.footer strong{

    color:#FF6B00;
}


/* =========================
   RESPONSIVE
========================= */

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

        padding:0 20px;
    }
}


@media(max-width:700px){

    .sidebar{

        position:relative;

        width:100%;

        height:auto;

        padding:15px;
    }

    .sidebar ul{

        display:grid;

        grid-template-columns:
            repeat(2,1fr);

        gap:5px;
    }

    .sidebar ul li a{

        font-size:12px;
    }

    .logo{

        text-align:center;
    }

    .main{

        margin-left:0;

        padding:0 15px 30px;
    }

    .topbar{

        margin:0 -15px 20px;

        padding:0 15px;

        min-height:75px;

        height:auto;
    }

    .admin-info{

        display:none;
    }

    .content{

        width:100%;
    }

    .card{

        padding:22px;
    }

    .button-row{

        flex-direction:column;
    }

    .update-btn,
    .cancel-btn{

        width:100%;
    }
}


@media(max-width:480px){

    .sidebar ul{

        grid-template-columns:1fr;
    }

    .page-banner{

        padding:23px;
    }

    .page-banner h2{

        font-size:23px;
    }
}

</style>

</head>


<body>


<!-- =========================
     SIDEBAR
========================= -->

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

                <span>
                    Dashboard
                </span>

            </a>

        </li>


        <li>

            <a href="add_student.php">

                ➕

                <span>
                    Add Student
                </span>

            </a>

        </li>


        <li>

            <a href="manage_student.php">

                👨‍🎓

                <span>
                    Manage Students
                </span>

            </a>

        </li>


        <li>

            <a href="attendance.php">

                📅

                <span>
                    Attendance
                </span>

            </a>

        </li>


        <li>

            <a href="result.php">

                📊

                <span>
                    Results
                </span>

            </a>

        </li>


        <li>

            <a href="course.php" class="active">

                📚

                <span>
                    Courses
                </span>

            </a>

        </li>


        <li>

            <a href="attendance_report.php">

                📄

                <span>
                    Attendance Report
                </span>

            </a>

        </li>


        <li>

            <a href="result_report.php">

                📄

                <span>
                    Result Report
                </span>

            </a>

        </li>


        <li>

            <a href="add_teacher.php">

                👨‍🏫

                <span>
                    Add Teacher
                </span>

            </a>

        </li>


        <li>

            <a href="manage_teacher.php">

                📋

                <span>
                    Manage Teachers
                </span>

            </a>

        </li>


        <li class="logout">

            <a
                href="logout.php"
                onclick="return confirm('Are you sure you want to logout?')"
            >

                🚪

                <span>
                    Logout
                </span>

            </a>

        </li>


    </ul>

</aside>



<!-- =========================
     MAIN
========================= -->

<main class="main">


    <!-- TOPBAR -->

    <header class="topbar">


        <div class="top-title">

            <h1>
                Edit Course
            </h1>

            <p>
                EduTrack Administration • Update Course Details
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



    <!-- CONTENT -->

    <div class="content">


        <!-- BANNER -->

        <section class="page-banner">

            <h2>
                ✏️ Edit Course
            </h2>

            <p>
                Update the information of your selected course.
            </p>

        </section>



        <?php

        if($message != "")
        {

        ?>

            <div class="message error">

                <?php
                echo $message;
                ?>

            </div>

        <?php

        }

        ?>



        <!-- EDIT CARD -->

        <section class="card">


            <div class="card-header">


                <div class="card-icon">
                    📚
                </div>


                <div>

                    <h3>
                        Course Information
                    </h3>

                    <p>
                        Modify the course details below
                    </p>

                </div>


            </div>



            <form method="POST">


                <!-- COURSE NAME -->

                <div class="form-group">

                    <label>
                        Course Name
                    </label>

                    <input
                        type="text"
                        name="course_name"
                        value="<?php
                        echo htmlspecialchars(
                            $row['course_name']
                        );
                        ?>"
                        placeholder="Enter course name"
                        required
                    >

                </div>



                <!-- DURATION -->

                <div class="form-group">

                    <label>
                        Duration
                    </label>

                    <input
                        type="text"
                        name="duration"
                        value="<?php
                        echo htmlspecialchars(
                            $row['duration']
                        );
                        ?>"
                        placeholder="Example: 3 Years"
                        required
                    >

                </div>



                <!-- FEES -->

                <div class="form-group">

                    <label>
                        Course Fees
                    </label>

                    <input
                        type="number"
                        name="fees"
                        value="<?php
                        echo htmlspecialchars(
                            $row['fees']
                        );
                        ?>"
                        placeholder="Enter course fees"
                        min="0"
                        required
                    >

                </div>



                <!-- BUTTONS -->

                <div class="button-row">


                    <button
                        type="submit"
                        name="update"
                        class="update-btn"
                    >

                        ✓ Update Course

                    </button>


                    <a
                        href="course.php"
                        class="cancel-btn"
                    >

                        ← Cancel

                    </a>


                </div>


            </form>


        </section>


    </div>



    <!-- FOOTER -->

    <footer class="footer">

        <p>

            <strong>
                EduTrack
            </strong>

            — Student Management System

        </p>

        <p style="margin-top:5px;">

            © <?php echo date("Y"); ?>

            EduTrack.

            All Rights Reserved.

        </p>

    </footer>


</main>


</body>

</html>