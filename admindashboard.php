<?php

session_start();

if(!isset($_SESSION['admin']))
{
    header("Location:../login.php");
    exit();
}

include "../db.php";


/* =========================
   DASHBOARD STATISTICS
========================= */

$totalStudent = mysqli_num_rows(
    mysqli_query($conn,"SELECT * FROM students")
);

$totalCourse = mysqli_num_rows(
    mysqli_query($conn,"SELECT * FROM course")
);

$totalAttendance = mysqli_num_rows(
    mysqli_query($conn,"SELECT * FROM attendance")
);

$totalResult = mysqli_num_rows(
    mysqli_query($conn,"SELECT * FROM result")
);

$totalTeacher = mysqli_num_rows(
    mysqli_query($conn,"SELECT * FROM teacher")
);


/* =========================
   STUDENT REQUESTS COUNT
========================= */

$request_count = 0;
$pending_requests = 0;

$request_table_check = mysqli_query(
    $conn,
    "SHOW TABLES LIKE 'student_requests'"
);

if(mysqli_num_rows($request_table_check) > 0)
{
    $request_count = mysqli_num_rows(
        mysqli_query(
            $conn,
            "SELECT * FROM student_requests"
        )
    );

    $pending_requests = mysqli_num_rows(
        mysqli_query(
            $conn,
            "SELECT * FROM student_requests
             WHERE status='Pending'"
        )
    );
}


/* =========================
   NOTICE CODE
========================= */

if(isset($_POST['add_notice']))
{
    $title = mysqli_real_escape_string(
        $conn,
        $_POST['title']
    );

    $description = mysqli_real_escape_string(
        $conn,
        $_POST['description']
    );

    $priority = mysqli_real_escape_string(
        $conn,
        $_POST['priority']
    );

    mysqli_query(
        $conn,
        "INSERT INTO notices
        (title, description, priority)
        VALUES
        ('$title','$description','$priority')"
    );

    header("Location:notice.php");
    exit();
}


/* =========================
   TODAY
========================= */

$today = date("Y-m-d");

$present = mysqli_num_rows(
    mysqli_query($conn,"
        SELECT * FROM attendance
        WHERE attendance_date='$today'
        AND status='Present'
    ")
);

$absent = mysqli_num_rows(
    mysqli_query($conn,"
        SELECT * FROM attendance
        WHERE attendance_date='$today'
        AND status='Absent'
    ")
);


/* =========================
   LOW ATTENDANCE ALERT
========================= */

$low_attendance_students = [];

$low_attendance_query = mysqli_query($conn,"
    SELECT
        s.id,
        s.name,
        s.course,
        s.semester,
        ROUND(
            (
                SUM(CASE WHEN a.status='Present' THEN 1 ELSE 0 END)
                / COUNT(*)
            ) * 100
        ) AS attendance_percentage
    FROM students s
    INNER JOIN attendance a
        ON s.id = a.student_id
    GROUP BY s.id, s.name, s.course, s.semester
    HAVING attendance_percentage < 75
    ORDER BY attendance_percentage ASC
    LIMIT 8
");

if($low_attendance_query)
{
    while($row = mysqli_fetch_assoc($low_attendance_query))
    {
        $low_attendance_students[] = $row;
    }
}


/* =========================
   ATTENDANCE %
========================= */

$today_total = $present + $absent;

$attendance_percentage = 0;

if($today_total > 0)
{
    $attendance_percentage =
        round(($present / $today_total) * 100);
}


/* =========================
   RECENT STUDENTS
========================= */

$recent_students = mysqli_query($conn,"
    SELECT *
    FROM students
    ORDER BY id DESC
    LIMIT 5
");

?>

<!DOCTYPE html>
<html>

<head>

<title>EduTrack | Admin Dashboard</title>

<meta name="viewport" content="width=device-width, initial-scale=1">

<style>
/* =========================
   LOW ATTENDANCE ALERT
========================= */

.low-attendance-panel{
    background:#FFFFFF;
    border:1px solid #E7E5E4;
    border-radius:18px;
    padding:22px;
    box-shadow:0 5px 20px rgba(0,0,0,.04);
    margin-bottom:25px;
}

.low-attendance-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:18px;
}

.low-attendance-header h2{
    font-size:19px;
    color:#1C1917;
}

.low-attendance-header span{
    font-size:12px;
    color:#DC2626;
    font-weight:bold;
}

.alert-table{
    width:100%;
    border-collapse:collapse;
}

.alert-table th{
    text-align:left;
    padding:12px 10px;
    background:#FFF7ED;
    color:#78716C;
    font-size:12px;
    text-transform:uppercase;
}

.alert-table td{
    padding:13px 10px;
    border-bottom:1px solid #F0EEEC;
    font-size:13px;
}

.alert-table tr:last-child td{
    border-bottom:none;
}

.alert-student{
    font-weight:bold;
    color:#292524;
}

.alert-course{
    color:#78716C;
    font-size:12px;
}

.attendance-danger{
    color:#DC2626;
    font-weight:bold;
}

.attendance-bar{
    width:120px;
    height:7px;
    background:#EEEAE7;
    border-radius:20px;
    overflow:hidden;
    display:inline-block;
    vertical-align:middle;
    margin-right:8px;
}

.attendance-bar span{
    display:block;
    height:100%;
    background:linear-gradient(90deg,#DC2626,#FF6B00);
    border-radius:20px;
}

.alert-view{
    display:inline-block;
    padding:7px 12px;
    background:#FFF1E6;
    color:#FF6B00;
    text-decoration:none;
    border-radius:8px;
    font-size:11px;
    font-weight:bold;
    transition:.3s;
}

.alert-view:hover{
    background:#FF6B00;
    color:white;
}

.no-alert{
    text-align:center;
    padding:25px;
    color:#16A34A;
    font-size:13px;
    background:#F0FDF4;
    border-radius:12px;
}



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


/* =========================
   STUDENT REQUEST BADGE
========================= */

.request-menu{
    display:flex;
    align-items:center;
    justify-content:space-between;
    width:100%;
}

.request-left{
    display:flex;
    align-items:center;
    gap:12px;
}

.request-badge{

    min-width:21px;
    height:21px;

    padding:0 6px;

    border-radius:20px;

    background:#FF6B00;

    color:white;

    font-size:11px;

    font-weight:bold;

    display:flex;

    align-items:center;

    justify-content:center;
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
   TOP HEADER
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


/* ADMIN AREA */

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
   WELCOME BANNER
========================= */

.welcome{

    background:
        linear-gradient(
            120deg,
            #1C1917,
            #292524
        );

    border-radius:20px;

    padding:30px;

    color:white;

    display:flex;

    justify-content:space-between;

    align-items:center;

    overflow:hidden;

    position:relative;

    margin-bottom:28px;
}

.welcome:after{

    content:"";

    position:absolute;

    width:220px;
    height:220px;

    border-radius:50%;

    right:-80px;
    top:-100px;

    background:rgba(255,138,0,.13);
}

.welcome-text{

    position:relative;

    z-index:2;
}

.welcome h2{

    font-size:28px;

    margin-bottom:8px;
}

.welcome p{

    color:#D6D3D1;

    font-size:14px;
}

.welcome-date{

    position:relative;

    z-index:2;

    text-align:right;
}

.welcome-date .date{

    font-size:15px;

    color:#FFB000;

    font-weight:bold;
}

.welcome-date .clock{

    margin-top:6px;

    font-size:13px;

    color:#D6D3D1;
}


/* =========================
   SECTION TITLE
========================= */

.section-title{

    display:flex;

    justify-content:space-between;

    align-items:center;

    margin-bottom:15px;
}

.section-title h2{

    font-size:19px;

    color:#1C1917;
}

.section-title span{

    font-size:12px;

    color:#78716C;
}


/* =========================
   STAT CARDS
========================= */

.stats{

    display:grid;

    grid-template-columns:
        repeat(5,1fr);

    gap:16px;

    margin-bottom:30px;
}

.stat-card{

    background:white;

    border:1px solid #E7E5E4;

    border-radius:17px;

    padding:20px;

    position:relative;

    overflow:hidden;

    transition:.3s;

    box-shadow:
        0 5px 20px
        rgba(0,0,0,.04);
}

.stat-card:hover{

    transform:translateY(-6px);

    box-shadow:
        0 15px 30px
        rgba(0,0,0,.09);
}

.stat-card:before{

    content:"";

    position:absolute;

    left:0;
    top:0;

    width:4px;
    height:100%;

    background:
        linear-gradient(
            #FF6B00,
            #FFB000
        );
}

.stat-icon{

    width:42px;
    height:42px;

    border-radius:11px;

    background:#FFF1E6;

    display:flex;

    align-items:center;

    justify-content:center;

    font-size:19px;

    margin-bottom:15px;
}

.stat-card h3{

    font-size:25px;

    margin-bottom:5px;

    color:#1C1917;
}

.stat-card p{

    color:#78716C;

    font-size:12px;
}


/* =========================
   CONTENT GRID
========================= */

.dashboard-grid{

    display:grid;

    grid-template-columns:
        1.5fr 1fr;

    gap:20px;

    margin-bottom:25px;
}

.panel{

    background:white;

    border:1px solid #E7E5E4;

    border-radius:18px;

    padding:22px;

    box-shadow:
        0 5px 20px
        rgba(0,0,0,.04);
}


/* =========================
   ATTENDANCE OVERVIEW
========================= */

.attendance-box{

    display:flex;

    align-items:center;

    gap:30px;

    padding-top:10px;
}

.circle{

    width:130px;
    height:130px;

    border-radius:50%;

    background:
        conic-gradient(
            #FF7A00
            <?php echo $attendance_percentage; ?>%,
            #EEEAE7
            <?php echo $attendance_percentage; ?>%
        );

    display:flex;

    align-items:center;

    justify-content:center;

    flex-shrink:0;
}

.circle-inner{

    width:96px;
    height:96px;

    border-radius:50%;

    background:white;

    display:flex;

    flex-direction:column;

    align-items:center;

    justify-content:center;
}

.circle-inner strong{

    font-size:24px;

    color:#1C1917;
}

.circle-inner span{

    color:#78716C;

    font-size:11px;
}

.attendance-details{

    flex:1;
}

.att-row{

    display:flex;

    justify-content:space-between;

    padding:12px 0;

    border-bottom:1px solid #F0EEEC;

    font-size:13px;
}

.att-row:last-child{

    border-bottom:none;
}

.present-text{

    color:#16A34A;

    font-weight:bold;
}

.absent-text{

    color:#DC2626;

    font-weight:bold;
}


/* =========================
   QUICK ACTIONS
========================= */

.quick-actions{

    display:grid;

    grid-template-columns:
        repeat(2,1fr);

    gap:12px;
}

.quick-action{

    text-decoration:none;

    color:#292524;

    background:#FAFAF9;

    border:1px solid #E7E5E4;

    padding:16px;

    border-radius:13px;

    transition:.3s;
}

.quick-action:hover{

    background:#FFF7ED;

    border-color:#FFB000;

    transform:translateY(-3px);
}

.quick-action .icon{

    font-size:22px;

    margin-bottom:8px;
}

.quick-action strong{

    display:block;

    font-size:13px;
}

.quick-action span{

    display:block;

    margin-top:4px;

    font-size:11px;

    color:#78716C;
}


/* =========================
   RECENT STUDENTS
========================= */

.student-list{

    display:flex;

    flex-direction:column;

    gap:5px;
}

.student-row{

    display:flex;

    align-items:center;

    gap:12px;

    padding:11px 5px;

    border-bottom:1px solid #F0EEEC;
}

.student-row:last-child{

    border-bottom:none;
}

.student-photo{

    width:40px;
    height:40px;

    border-radius:10px;

    object-fit:cover;

    background:#FFF1E6;

    display:flex;

    align-items:center;

    justify-content:center;
}

.student-name{

    flex:1;
}

.student-name strong{

    display:block;

    font-size:13px;
}

.student-name span{

    display:block;

    color:#78716C;

    font-size:11px;

    margin-top:3px;
}

.view-btn{

    text-decoration:none;

    color:#FF6B00;

    font-size:12px;

    font-weight:bold;
}


/* =========================
   SYSTEM STATUS
========================= */

.status-panel{

    background:#1C1917;

    color:white;

    border-radius:18px;

    padding:22px;

    margin-bottom:25px;
}

.status-panel h2{

    font-size:18px;

    margin-bottom:18px;
}

.status-grid{

    display:grid;

    grid-template-columns:
        repeat(4,1fr);

    gap:12px;
}

.status-item{

    background:#292524;

    border-radius:12px;

    padding:14px;
}

.status-item span{

    display:block;

    color:#A8A29E;

    font-size:11px;

    margin-bottom:7px;
}

.status-item strong{

    color:#86EFAC;

    font-size:13px;
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

@media(max-width:1200px){

    .stats{

        grid-template-columns:
            repeat(3,1fr);
    }

    .status-grid{

        grid-template-columns:
            repeat(2,1fr);
    }
}


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

    .dashboard-grid{

        grid-template-columns:1fr;
    }
}


.alert-table{
    min-width:700px;
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

        height:auto;

        min-height:75px;
    }

    .admin-info{

        display:none;
    }

    .welcome{

        flex-direction:column;

        align-items:flex-start;

        gap:18px;
    }

    .welcome-date{

        text-align:left;
    }

    .stats{

        grid-template-columns:
            repeat(2,1fr);
    }

    .attendance-box{

        flex-direction:column;

        align-items:flex-start;
    }
}


@media(max-width:480px){

    .stats{

        grid-template-columns:1fr;
    }

    .quick-actions{

        grid-template-columns:1fr;
    }

    .status-grid{

        grid-template-columns:1fr;
    }

    .sidebar ul{

        grid-template-columns:1fr;
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


        <!-- DASHBOARD -->

        <li>

            <a href="admindashboard.php" class="active">

                🏠

                <span>
                    Dashboard
                </span>

            </a>

        </li>


        <!-- ADD STUDENT -->

        <li>

            <a href="add_student.php">

                ➕

                <span>
                    Add Student
                </span>

            </a>

        </li>


        <!-- MANAGE STUDENTS -->

        <li>

            <a href="manage_student.php">

                👨‍🎓

                <span>
                    Manage Students
                </span>

            </a>

        </li>


        <!-- ATTENDANCE -->

        <li>

            <a href="attendance.php">

                📅

                <span>
                    Attendance
                </span>

            </a>

        </li>


        <!-- RESULTS -->

        <li>

            <a href="result.php">

                📊

                <span>
                    Results
                </span>

            </a>

        </li>


        <!-- COURSES -->

        <li>

            <a href="course.php">

                📚

                <span>
                    Courses
                </span>

            </a>

        </li>


        <!-- ATTENDANCE REPORT -->

        <li>

            <a href="attendance_report.php">

                📄

                <span>
                    Attendance Report
                </span>

            </a>

        </li>


        <!-- RESULT REPORT -->

        <li>

            <a href="result_report.php">

                📄

                <span>
                    Result Report
                </span>

            </a>

        </li>


        <!-- ADD TEACHER -->

        <li>

            <a href="add_teacher.php">

                👨‍🏫

                <span>
                    Add Teacher
                </span>

            </a>

        </li>


        <!-- MANAGE TEACHERS -->

        <li>

            <a href="manage_teacher.php">

                📋

                <span>
                    Manage Teachers
                </span>

            </a>

        </li>


        <!-- STUDENT REQUESTS -->

        <li>

            <a href="student_requests.php">

                <div class="request-menu">

                    <div class="request-left">

                        📝

                        <span>
                            Student Requests
                        </span>

                    </div>


                    <?php

                    if($pending_requests > 0)
                    {

                        echo '<span class="request-badge">'
                            .$pending_requests.
                            '</span>';

                    }

                    ?>

                </div>

            </a>

        </li>
		
		<li>
    <a href="notice.php">
        📢 <span>Notice Board</span>
    </a>
</li>


        <!-- LOGOUT -->

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


    <!-- TOP BAR -->

    <header class="topbar">


        <div class="top-title">

            <h1>
                Dashboard
            </h1>

            <p>
                EduTrack Administration Overview
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



    <!-- WELCOME -->

    <section class="welcome">


        <div class="welcome-text">

            <h2>

                Welcome back, Admin 👋

            </h2>

            <p>

                Here's what's happening in your student management system today.

            </p>

        </div>


        <div class="welcome-date">

            <div class="date">

                <?php

                echo date("d F Y");

                ?>

            </div>


            <div
                class="clock"
                id="clock"
            >

                Loading time...

            </div>

        </div>


    </section>



    <!-- STATISTICS -->

    <div class="section-title">

        <h2>
            System Overview
        </h2>

        <span>
            Live database statistics
        </span>

    </div>


    <section class="stats">


        <!-- STUDENTS -->

        <div class="stat-card">

            <div class="stat-icon">

                👨‍🎓

            </div>

            <h3>

                <?php

                echo $totalStudent;

                ?>

            </h3>

            <p>
                Total Students
            </p>

        </div>


        <!-- TEACHERS -->

        <div class="stat-card">

            <div class="stat-icon">

                👨‍🏫

            </div>

            <h3>

                <?php

                echo $totalTeacher;

                ?>

            </h3>

            <p>
                Total Teachers
            </p>

        </div>


        <!-- COURSES -->

        <div class="stat-card">

            <div class="stat-icon">

                📚

            </div>

            <h3>

                <?php

                echo $totalCourse;

                ?>

            </h3>

            <p>
                Total Courses
            </p>

        </div>


        <!-- ATTENDANCE -->

        <div class="stat-card">

            <div class="stat-icon">

                📅

            </div>

            <h3>

                <?php

                echo $totalAttendance;

                ?>

            </h3>

            <p>
                Attendance Records
            </p>

        </div>


        <!-- RESULTS -->

        <div class="stat-card">

            <div class="stat-icon">

                📊

            </div>

            <h3>

                <?php

                echo $totalResult;

                ?>

            </h3>

            <p>
                Total Results
            </p>

        </div>


    </section>



    <!-- CONTENT GRID -->

    <section class="dashboard-grid">


        <!-- ATTENDANCE -->

        <div class="panel">


            <div class="section-title">

                <h2>
                    Today's Attendance
                </h2>

                <span>

                    <?php

                    echo date("d M Y");

                    ?>

                </span>

            </div>


            <div class="attendance-box">


                <div class="circle">

                    <div class="circle-inner">

                        <strong>

                            <?php

                            echo $attendance_percentage;

                            ?>%

                        </strong>

                        <span>
                            Present
                        </span>

                    </div>

                </div>


                <div class="attendance-details">


                    <div class="att-row">

                        <span>
                            Total Marked
                        </span>

                        <strong>

                            <?php

                            echo $today_total;

                            ?>

                        </strong>

                    </div>


                    <div class="att-row">

                        <span>
                            Present Today
                        </span>

                        <strong class="present-text">

                            <?php

                            echo $present;

                            ?>

                        </strong>

                    </div>


                    <div class="att-row">

                        <span>
                            Absent Today
                        </span>

                        <strong class="absent-text">

                            <?php

                            echo $absent;

                            ?>

                        </strong>

                    </div>


                    <div class="att-row">

                        <span>
                            Attendance Rate
                        </span>

                        <strong class="present-text">

                            <?php

                            echo $attendance_percentage;

                            ?>%

                        </strong>

                    </div>


                </div>


            </div>


        </div>



        <!-- QUICK ACTIONS -->

        <div class="panel">


            <div class="section-title">

                <h2>
                    Quick Actions
                </h2>

            </div>


            <div class="quick-actions">


                <a
                    href="add_student.php"
                    class="quick-action"
                >

                    <div class="icon">

                        ➕

                    </div>

                    <strong>
                        Add Student
                    </strong>

                    <span>
                        Register new student
                    </span>

                </a>


                <a
                    href="manage_student.php"
                    class="quick-action"
                >

                    <div class="icon">

                        👨‍🎓

                    </div>

                    <strong>
                        Students
                    </strong>

                    <span>
                        Manage student records
                    </span>

                </a>


                <a
                    href="attendance_report.php"
                    class="quick-action"
                >

                    <div class="icon">

                        📄

                    </div>

                    <strong>
                        Attendance Report
                    </strong>

                    <span>
                        View attendance report
                    </span>

                </a>


                <a
                    href="result_report.php"
                    class="quick-action"
                >

                    <div class="icon">

                        📊

                    </div>

                    <strong>
                        Result Report
                    </strong>

                    <span>
                        View result report
                    </span>

                </a>


                <!-- STUDENT REQUESTS -->

                <a
                    href="student_requests.php"
                    class="quick-action"
                >

                    <div class="icon">

                        📝

                    </div>

                    <strong>
                        Student Requests
                    </strong>

                    <span>

                        <?php

                        if($pending_requests > 0)
                        {
                            echo $pending_requests .
                                 " pending request";

                            if($pending_requests > 1)
                            {
                                echo "s";
                            }
                        }
                        else
                        {
                            echo "No pending requests";
                        }

                        ?>

                    </span>

                </a>


            </div>


        </div>


    </section>



    
    <!-- =========================
         LOW ATTENDANCE ALERT
    ========================= -->

    <section class="low-attendance-panel">

        <div class="low-attendance-header">
            <h2>⚠️ Low Attendance Alert</h2>
            <span>Below 75%</span>
        </div>

        <?php if(count($low_attendance_students) > 0){ ?>

            <div style="overflow-x:auto;">

                <table class="alert-table">

                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Course</th>
                            <th>Semester</th>
                            <th>Attendance</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php foreach($low_attendance_students as $low_student){ ?>

                        <tr>

                            <td>
                                <div class="alert-student">
                                    <?php
                                    echo htmlspecialchars(
                                        ucwords(strtolower($low_student['name']))
                                    );
                                    ?>
                                </div>
                            </td>

                            <td>
                                <span class="alert-course">
                                    <?php echo htmlspecialchars($low_student['course']); ?>
                                </span>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($low_student['semester']); ?>
                            </td>

                            <td>
                                <span class="attendance-bar">
                                    <span style="width:<?php echo max(0,min(100,(float)$low_student['attendance_percentage'])); ?>%;"></span>
                                </span>

                                <strong class="attendance-danger">
                                    <?php echo $low_student['attendance_percentage']; ?>%
                                </strong>
                            </td>

                            <td>
                                <a
                                    href="low_attendance_view.php?id=<?php echo $low_student['id']; ?>"
                                    class="alert-view"
                                >
                                    View
                                </a>
                            </td>

                        </tr>

                    <?php } ?>

                    </tbody>

                </table>

            </div>

        <?php }else{ ?>

            <div class="no-alert">
                ✓ All students have attendance of 75% or above.
            </div>

        <?php } ?>

    </section>


<!-- RECENT STUDENTS -->

    <section class="panel">


        <div class="section-title">

            <h2>
                Recently Registered Students
            </h2>

            <a
                href="manage_student.php"
                class="view-btn"
            >

                View All →

            </a>

        </div>


        <div class="student-list">


            <?php

            if(mysqli_num_rows($recent_students) > 0)
            {

                while($student = mysqli_fetch_assoc($recent_students))
                {

            ?>


            <div class="student-row">


                <?php

                if(!empty($student['photo']))
                {

                    echo '<img
                        src="../uploads/'.$student['photo'].'"
                        class="student-photo"
                    >';

                }
                else
                {

                    echo '<div class="student-photo">

                        👤

                    </div>';

                }

                ?>


                <div class="student-name">

                    <strong>

                        <?php

                        echo ucwords(
                            strtolower(
                                $student['name']
                            )
                        );

                        ?>

                    </strong>

                    <span>

                        <?php

                        echo $student['course'];

                        ?>

                        • Semester

                        <?php

                        echo $student['semester'];

                        ?>

                    </span>

                </div>


                <a
                    href="student_profile.php?id=<?php
                    echo $student['id'];
                    ?>"
                    class="view-btn"
                >

                    View

                </a>


            </div>


            <?php

                }

            }
            else
            {

            ?>

                <p
                    style="
                    text-align:center;
                    padding:25px;
                    color:#78716C;
                    "
                >

                    No students registered yet.

                </p>

            <?php

            }

            ?>


        </div>


    </section>



    <!-- SYSTEM STATUS -->

    <section class="status-panel">


        <h2>

            ⚡ System Status

        </h2>


        <div class="status-grid">


            <div class="status-item">

                <span>
                    Student Module
                </span>

                <strong>
                    ● Active
                </strong>

            </div>


            <div class="status-item">

                <span>
                    Attendance Module
                </span>

                <strong>
                    ● Active
                </strong>

            </div>


            <div class="status-item">

                <span>
                    Result Module
                </span>

                <strong>
                    ● Active
                </strong>

            </div>


            <div class="status-item">

                <span>
                    Database
                </span>

                <strong>
                    ● Connected
                </strong>

            </div>


        </div>


    </section>



    <!-- FOOTER -->

    <footer class="footer">

        <p>

            <strong>
                EduTrack
            </strong>

            — Student Management System

        </p>

        <p>

            © 2026 EduTrack.
            All Rights Reserved.

        </p>

    </footer>


</main>



<script>

/* =========================
   LIVE CLOCK
========================= */

function updateClock()
{

    const now = new Date();

    document.getElementById("clock").innerHTML =
        now.toLocaleTimeString();

}

setInterval(updateClock,1000);

updateClock();

</script>


</body>

</html>