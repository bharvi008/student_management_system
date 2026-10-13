<?php

session_start();

if(!isset($_SESSION['student_id']))
{
    header("Location:../login.php");
    exit();
}

include "../db.php";

$id = $_SESSION['student_id'];

$result = mysqli_query($conn, "SELECT * FROM students WHERE id='$id'");
$row = mysqli_fetch_assoc($result);

?>

<!DOCTYPE html>
<html>

<head>

<title>Student ID Card - EduTrack</title>

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    font-family:Arial, sans-serif;
    background:#FFF7ED;
    color:#292524;
}

/* ================= HEADER ================= */

.top-header{
    height:70px;
    background:#1C1917;
    display:flex;
    align-items:center;
    justify-content:space-between;
    padding:0 50px;
    box-shadow:0 3px 15px rgba(0,0,0,.15);
}

.logo a{
    text-decoration:none;
    color:white;
    font-size:27px;
    font-weight:bold;
}

.logo span{
    color:#FF8A00;
}

.home-nav{
    display:flex;
    gap:28px;
}

.home-nav a{
    text-decoration:none;
    color:#ddd;
    font-size:15px;
    transition:.3s;
}

.home-nav a:hover{
    color:#FFB000;
}

/* ================= STUDENT MENU ================= */

.student-menu{
    width:fit-content;
    max-width:95%;
    margin:22px auto;
    padding:8px;
    background:white;
    border-radius:50px;
    box-shadow:0 5px 20px rgba(0,0,0,.08);

    display:flex;
    flex-wrap:wrap;
    justify-content:center;
    gap:5px;
}

.student-menu a{
    text-decoration:none;
    color:#57534E;
    padding:11px 16px;
    border-radius:30px;
    font-size:14px;
    font-weight:600;
    transition:.3s;
}

.student-menu a:hover{
    background:#FFF1E6;
    color:#FF6B00;
    transform:translateY(-2px);
}

.student-menu a.active{
    background:linear-gradient(90deg,#FF6B00,#FFB000);
    color:white;
}

.student-menu .logout{
    color:#DC2626;
}

.student-menu .logout:hover{
    background:#FEE2E2;
    color:#B91C1C;
}

/* ================= PAGE TITLE ================= */

.page-title{
    text-align:center;
    margin:25px 0;
}

.page-title h1{
    font-size:30px;
    color:#1C1917;
}

.page-title p{
    margin-top:8px;
    color:#78716C;
}

/* ================= ID CARD AREA ================= */

.id-wrapper{
    display:flex;
    justify-content:center;
    padding:10px 20px 50px;
}

/* ================= ID CARD ================= */

.id-card{

    width:390px;

    background:white;

    border-radius:20px;

    overflow:hidden;

    box-shadow:
        0 15px 40px rgba(28,25,23,.18);

    border:1px solid #FED7AA;

    transition:.4s;
}

.id-card:hover{
    transform:translateY(-7px);
    box-shadow:
        0 20px 45px rgba(28,25,23,.25);
}

/* CARD HEADER */

.card-top{

    background:linear-gradient(
        135deg,
        #1C1917,
        #292524
    );

    color:white;

    text-align:center;

    padding:24px 15px;

    position:relative;
}

.card-top:after{
    content:"";
    position:absolute;
    bottom:-1px;
    left:0;
    width:100%;
    height:5px;
    background:linear-gradient(
        90deg,
        #FF6B00,
        #FFB000
    );
}

.card-top h2{
    font-size:25px;
    margin-bottom:7px;
}

.card-top p{
    color:#FED7AA;
    font-size:14px;
}

/* PHOTO */

.photo{
    text-align:center;
    margin-top:25px;
}

.photo img{

    width:125px;
    height:125px;

    border-radius:50%;

    object-fit:cover;

    border:5px solid #FF8A00;

    box-shadow:0 6px 20px rgba(255,107,0,.25);
}

/* STUDENT NAME */

.student-name{
    text-align:center;
    margin-top:14px;
}

.student-name h3{
    font-size:21px;
    color:#1C1917;
}

.student-name p{
    margin-top:4px;
    color:#78716C;
    font-size:13px;
}

/* INFO */

.info{
    padding:22px 25px 10px;
}

.info-row{

    display:flex;

    justify-content:space-between;

    align-items:center;

    padding:11px 0;

    border-bottom:1px solid #F3F4F6;

    gap:15px;
}

.info-row:last-child{
    border-bottom:none;
}

.label{
    color:#78716C;
    font-size:13px;
    font-weight:bold;
}

.value{
    color:#292524;
    font-size:14px;
    font-weight:600;
    text-align:right;
    word-break:break-word;
}

/* CARD BOTTOM */

.card-bottom{

    margin:8px 25px 20px;

    padding:12px;

    text-align:center;

    border-radius:10px;

    background:#FFF7ED;

    color:#C2410C;

    font-size:12px;

    font-weight:bold;
}

/* ================= BUTTON ================= */

.print-section{
    text-align:center;
    margin-bottom:45px;
}

.print-btn{

    border:none;

    padding:13px 28px;

    border-radius:10px;

    background:linear-gradient(
        90deg,
        #FF6B00,
        #FFB000
    );

    color:white;

    font-size:15px;

    font-weight:bold;

    cursor:pointer;

    box-shadow:0 7px 20px rgba(255,107,0,.25);

    transition:.3s;
}

.print-btn:hover{
    transform:translateY(-3px);
    box-shadow:0 10px 25px rgba(255,107,0,.35);
}

/* ================= FOOTER ================= */

.footer{

    background:#1C1917;

    color:#D6D3D1;

    text-align:center;

    padding:30px 15px;

    margin-top:20px;
}

.footer h3{
    color:white;
    margin-bottom:10px;
}

.footer p{
    font-size:13px;
    margin:6px 0;
    color:#A8A29E;
}

/* ================= RESPONSIVE ================= */

@media(max-width:900px){

    .top-header{
        padding:0 25px;
    }

    .home-nav{
        gap:15px;
    }

}

@media(max-width:650px){

    .top-header{
        height:auto;
        padding:18px 15px;
        flex-direction:column;
        gap:12px;
    }

    .home-nav{
        flex-wrap:wrap;
        justify-content:center;
    }

    .student-menu{
        border-radius:18px;
        padding:8px;
    }

    .student-menu a{
        font-size:12px;
        padding:9px 11px;
    }

    .page-title h1{
        font-size:25px;
    }

    .id-card{
        width:100%;
        max-width:390px;
    }

}

/* ================= PRINT ================= */

@media print{

    body{
        background:white;
    }

    .top-header,
    .student-menu,
    .page-title,
    .print-section,
    .footer{
        display:none !important;
    }

    .id-wrapper{
        padding:0;
        margin:0;
    }

    .id-card{
        width:390px;
        margin:0 auto;
        box-shadow:none;
        border:1px solid #ddd;
        transform:none !important;
    }

}

</style>

</head>

<body>


<!-- ================= HEADER ================= -->

<header class="top-header">

    <div class="logo">

        <a href="../index.php">
            Edu<span>Track</span>
        </a>

    </div>

    <nav class="home-nav">

        <a href="../index.php">Home</a>

        <a href="../index.php#about">About</a>

        <a href="../index.php#features">Features</a>

        <a href="../index.php#contact">Contact</a>

    </nav>

</header>


<!-- ================= STUDENT MENU ================= -->

<nav class="student-menu">

    <a href="studentdashboard.php">
        🏠 Dashboard
    </a>

    <a href="profile.php">
        👤 Profile
    </a>

    <a href="attendance.php">
        📅 Attendance
    </a>

    <a href="result.php">
        📊 Result
    </a>

    <a href="course.php">
        📚 Course
    </a>

    <a href="id_card.php" class="active">
        🆔 ID Card
    </a>

    <a href="change_password.php">
        🔑 Password
    </a>

    <a href="logout.php"
       class="logout"
       onclick="return confirm('Are you sure you want to logout?')">

        🚪 Logout

    </a>

</nav>


<!-- ================= TITLE ================= -->

<div class="page-title">

    <h1>Student Identity Card</h1>

    <p>Your official student identification card</p>

</div>


<!-- ================= ID CARD ================= -->

<div class="id-wrapper">

    <div class="id-card">


        <!-- CARD TOP -->

        <div class="card-top">

            <h2>EduTrack</h2>

            <p>Student Identity Card</p>

        </div>


        <!-- PHOTO -->

        <div class="photo">

            <?php

            $photo = !empty($row['photo'])
                     ? $row['photo']
                     : 'default.png';

            ?>

            <img
                src="../uploads/<?php echo htmlspecialchars($photo); ?>"
                alt="Student Photo"
            >

        </div>


        <!-- NAME -->

        <div class="student-name">

            <h3>
                <?php echo htmlspecialchars($row['name']); ?>
            </h3>

            <p>
                Student ID: <?php echo htmlspecialchars($row['id']); ?>
            </p>

        </div>


        <!-- INFO -->

        <div class="info">


            <div class="info-row">

                <span class="label">
                    Student ID
                </span>

                <span class="value">
                    <?php echo htmlspecialchars($row['id']); ?>
                </span>

            </div>


            <div class="info-row">

                <span class="label">
                    Course
                </span>

                <span class="value">
                    <?php echo htmlspecialchars($row['course']); ?>
                </span>

            </div>


            <div class="info-row">

                <span class="label">
                    Semester
                </span>

                <span class="value">
                    <?php echo htmlspecialchars($row['semester']); ?>
                </span>

            </div>


            <div class="info-row">

                <span class="label">
                    Email
                </span>

                <span class="value">
                    <?php echo htmlspecialchars($row['email']); ?>
                </span>

            </div>


            <div class="info-row">

                <span class="label">
                    Phone
                </span>

                <span class="value">
                    <?php echo htmlspecialchars($row['phone']); ?>
                </span>

            </div>


        </div>


        <!-- CARD BOTTOM -->

        <div class="card-bottom">

            This card is issued by EduTrack Student Management System

        </div>


    </div>

</div>


<!-- ================= PRINT BUTTON ================= -->

<div class="print-section">

    <button
        class="print-btn"
        onclick="window.print()">

        🖨 Print ID Card

    </button>

</div>


<!-- ================= FOOTER ================= -->

<footer class="footer">

    <h3>Student Management System</h3>

    <p>
        Manage Students • Attendance • Results • Courses
    </p>

    <p>
        Designed Using HTML • CSS • JavaScript • PHP • MySQL
    </p>

    <p>
        © 2026 Student Management System. All Rights Reserved.
    </p>

</footer>


</body>

</html>