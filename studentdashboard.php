<?php

session_start();

if(!isset($_SESSION['student_id']))
{
    header("Location: ../login.php");
    exit();
}

include "../db.php";


/* =========================
   STUDENT NAME
========================= */

$name = ucwords(
    strtolower(
        trim($_SESSION['student_name'])
    )
);


/* =========================
   STUDENT ID
========================= */

$student_id = $_SESSION['student_id'];


/* =========================
   NOTIFICATION COUNT
========================= */

$notification_count = 0;

$notification_query = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM notifications
     WHERE student_id='$student_id'
     AND is_read=0"
);

if($notification_query)
{
    $notification_data = mysqli_fetch_assoc(
        $notification_query
    );

    $notification_count =
        $notification_data['total'];
}


?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>Student Management System</title>


<style>

/* ================= GLOBAL ================= */

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{

    font-family:Arial,sans-serif;

    background:
        linear-gradient(
            135deg,
            #FFF7ED,
            #FFFBF5
        );

    color:#292524;

    overflow-x:hidden;
}


/* ================= TOP HEADER ================= */

.top-header{

    width:100%;

    height:75px;

    display:flex;

    align-items:center;

    justify-content:space-between;

    padding:0 45px;

    background:
        linear-gradient(
            135deg,
            #1C1917,
            #292524
        );

    box-shadow:
        0 5px 25px rgba(0,0,0,.20);

    animation:headerDown .7s ease;

}


/* ================= LOGO ================= */

.logo a{

    color:#FFFFFF;

    text-decoration:none;

    font-size:27px;

    font-weight:700;

    letter-spacing:.5px;

}

.logo span{

    color:#FF8A00;

}


/* ================= TOP NAVIGATION ================= */

.home-nav{

    display:flex;

    align-items:center;

    gap:32px;

}

.home-nav a{

    color:#FFFFFF;

    text-decoration:none;

    font-size:15px;

    font-weight:500;

    position:relative;

    transition:.3s;

}

.home-nav a::after{

    content:"";

    position:absolute;

    left:0;

    bottom:-7px;

    width:0;

    height:2px;

    background:#FFB000;

    transition:.3s;

}

.home-nav a:hover{

    color:#FFB000;

    transform:translateY(-2px);

}

.home-nav a:hover::after{

    width:100%;

}


/* ================= NOTIFICATION ================= */

.notification-btn{

    position:relative;

    display:inline-flex;

    align-items:center;

    justify-content:center;

    width:45px;

    height:45px;

    background:#292524;

    color:#FFFFFF;

    border-radius:12px;

    text-decoration:none;

    font-size:21px;

    transition:.3s;

    margin-left:5px;

}

.notification-btn:hover{

    background:#FF6B00;

    color:#FFFFFF;

    transform:translateY(-2px);

}

.notification-count{

    position:absolute;

    top:-6px;

    right:-6px;

    min-width:20px;

    height:20px;

    padding:0 5px;

    background:#DC2626;

    color:#FFFFFF;

    border-radius:50px;

    display:flex;

    align-items:center;

    justify-content:center;

    font-size:11px;

    font-weight:bold;

    border:2px solid #1C1917;

}


/* ================= STUDENT MENU ================= */

.student-menu{

    width:94%;

    max-width:1250px;

    margin:25px auto 0;

    background:#FFFFFF;

    border-radius:20px;

    padding:12px;

    display:flex;

    justify-content:center;

    align-items:center;

    flex-wrap:wrap;

    gap:8px;

    box-shadow:
        0 10px 35px rgba(41,37,36,.12);

    animation:menuAppear .8s ease;

}


/* ================= MENU LINKS ================= */

.student-menu a{

    text-decoration:none;

    color:#292524;

    padding:11px 18px;

    border-radius:12px;

    font-size:14px;

    font-weight:500;

    transition:.35s;

}


/* ================= MENU HOVER ================= */

.student-menu a:hover{

    background:#FF6B00;

    color:#FFFFFF;

    transform:translateY(-3px);

    box-shadow:
        0 7px 18px rgba(255,107,0,.25);

}


/* ================= ACTIVE ================= */

.student-menu .active{

    background:#FF6B00;

    color:#FFFFFF;

    box-shadow:
        0 5px 15px rgba(255,107,0,.20);

}


/* ================= LOGOUT ================= */

.student-menu .logout{

    color:#DC2626;

}

.student-menu .logout:hover{

    background:#DC2626;

    color:#FFFFFF;

}


/* ================= MAIN ================= */

.main{

    width:94%;

    max-width:1250px;

    margin:30px auto 50px;

}


/* ================= WELCOME ================= */

.welcome{

    position:relative;

    background:#FFFFFF;

    border-radius:24px;

    padding:30px 35px;

    margin-bottom:30px;

    overflow:hidden;

    box-shadow:
        0 10px 35px rgba(41,37,36,.10);

    animation:fadeUp .8s ease;

}


/* ================= DECORATIVE CIRCLE ================= */

.welcome::after{

    content:"";

    position:absolute;

    width:190px;

    height:190px;

    border-radius:50%;

    background:
        rgba(255,107,0,.08);

    right:-65px;

    top:-75px;

    animation:
        floatCircle 4s ease-in-out infinite;

}


/* ================= WELCOME TITLE ================= */

.welcome h1{

    font-size:34px;

    font-weight:700;

    color:#292524;

}

.welcome h1 span{

    color:#FF6B00;

}


.welcome p{

    margin-top:7px;

    color:#78716C;

    font-size:15px;

}


/* ================= CARDS ================= */

.cards{

    display:grid;

    grid-template-columns:
        repeat(4,1fr);

    gap:25px;

}


/* ================= CARD ================= */

.card{

    position:relative;

    display:block;

    text-decoration:none;

    color:#292524;

    background:#FFFFFF;

    border-radius:24px;

    padding:38px 20px;

    text-align:center;

    overflow:hidden;

    box-shadow:
        0 8px 28px rgba(41,37,36,.10);

    transition:.4s;

    animation:
        cardUp .8s ease backwards;

}


/* ================= CARD DELAY ================= */

.card:nth-child(1){

    animation-delay:.1s;

}

.card:nth-child(2){

    animation-delay:.2s;

}

.card:nth-child(3){

    animation-delay:.3s;

}

.card:nth-child(4){

    animation-delay:.4s;

}

.card:nth-child(5){

    animation-delay:.5s;

}

.card:nth-child(6){

    animation-delay:.6s;

}

.card:nth-child(7){

    animation-delay:.7s;

}


/* ================= TOP ORANGE LINE ================= */

.card::before{

    content:"";

    position:absolute;

    top:0;

    left:0;

    width:100%;

    height:5px;

    background:
        linear-gradient(
            90deg,
            #FF6B00,
            #FFB000
        );

    transform:scaleX(0);

    transform-origin:left;

    transition:.4s;

}

.card:hover::before{

    transform:scaleX(1);

}


/* ================= CARD HOVER ================= */

.card:hover{

    color:#292524;

    transform:translateY(-12px);

    box-shadow:
        0 20px 45px rgba(41,37,36,.18);

}


/* ================= ICON ================= */

.card .icon{

    font-size:45px;

    margin-bottom:15px;

    transition:.4s;

}

.card:hover .icon{

    transform:
        scale(1.2)
        rotate(5deg);

}


/* ================= CARD HEADING ================= */

.card h2{

    font-size:24px;

    margin-bottom:10px;

    transition:.3s;

}

.card:hover h2{

    color:#FF6B00;

}


/* ================= CARD DESCRIPTION ================= */

.card p{

    color:#78716C;

    font-size:14px;

}


/* ================= CARD CIRCLE ================= */

.card::after{

    content:"";

    position:absolute;

    width:90px;

    height:90px;

    border-radius:50%;

    background:
        rgba(255,107,0,.07);

    right:-35px;

    bottom:-35px;

    transition:.5s;

}

.card:hover::after{

    width:150px;

    height:150px;

}


/* ================= QUICK INFORMATION ================= */

.quick-info{

    margin-top:35px;

    background:#FFFFFF;

    border-radius:22px;

    padding:25px;

    box-shadow:
        0 8px 25px rgba(41,37,36,.08);

    animation:
        fadeUp 1s ease;

}

.quick-info h2{

    margin-bottom:8px;

    color:#292524;

}

.quick-info p{

    color:#78716C;

    font-size:14px;

}


/* ================= FOOTER ================= */

.footer{

    width:100%;

    background:
        linear-gradient(
            135deg,
            #1C1917,
            #292524
        );

    color:#FFFFFF;

    text-align:center;

    padding:30px 20px;

    margin-top:20px;

}

.footer h3{

    font-size:22px;

    margin-bottom:10px;

    color:#FFB000;

}

.footer p{

    margin:6px;

    font-size:13px;

    color:#FFFFFF;

    opacity:.9;

}


/* ================= ANIMATIONS ================= */

@keyframes headerDown{

    from{

        transform:translateY(-100%);

        opacity:0;

    }

    to{

        transform:translateY(0);

        opacity:1;

    }

}


@keyframes menuAppear{

    from{

        transform:translateY(-20px);

        opacity:0;

    }

    to{

        transform:translateY(0);

        opacity:1;

    }

}


@keyframes fadeUp{

    from{

        transform:translateY(30px);

        opacity:0;

    }

    to{

        transform:translateY(0);

        opacity:1;

    }

}


@keyframes cardUp{

    from{

        transform:translateY(50px);

        opacity:0;

    }

    to{

        transform:translateY(0);

        opacity:1;

    }

}


@keyframes floatCircle{

    0%,100%{

        transform:translateY(0);

    }

    50%{

        transform:translateY(15px);

    }

}


/* ================= RESPONSIVE ================= */

@media(max-width:1000px){

    .cards{

        grid-template-columns:
            repeat(2,1fr);

    }

}


@media(max-width:700px){

    .top-header{

        height:auto;

        padding:18px 20px;

        flex-direction:column;

        gap:12px;

    }


    .home-nav{

        gap:15px;

        flex-wrap:wrap;

        justify-content:center;

    }


    .student-menu{

        width:95%;

    }


    .student-menu a{

        padding:9px 12px;

        font-size:13px;

    }


    .main{

        width:95%;

    }


    .cards{

        grid-template-columns:1fr;

    }


    .welcome h1{

        font-size:26px;

    }

}

</style>

</head>


<body>


<!-- ================= TOP HEADER ================= -->

<header class="top-header">

    <div class="logo">

        <a href="../index.php">

            Edu<span>Track</span>

        </a>

    </div>


    <nav class="home-nav">

        <a href="../index.php">
            Home
        </a>

        <a href="../index.php#about">
            About
        </a>

        <a href="../index.php#features">
            Features
        </a>

        <a href="../index.php#contact">
            Contact
        </a>


        <!-- ================= NOTIFICATION ================= -->

        <a
            href="notifications.php"
            class="notification-btn"
            title="Notifications"
        >

            🔔

            <?php

            if($notification_count > 0)
            {

                echo '<span class="notification-count">'
                     . $notification_count .
                     '</span>';

            }

            ?>

        </a>

    </nav>

</header>



<!-- ================= STUDENT MENU ================= -->

<nav class="student-menu">


    <a
        href="studentdashboard.php"
        class="active"
    >

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


    <a href="notice.php">

        📢 Notice Board

    </a>





    <a href="request.php">

        📝 Request Approval

    </a>


    <a href="id_card.php">

        🆔 ID Card

    </a>


    <a href="change_password.php">

        🔑 Password

    </a>


    <a
        href="logout.php"
        class="logout"
        onclick="return confirm('Are you sure you want to logout?')"
    >

        🚪 Logout

    </a>


</nav>



<!-- ================= MAIN CONTENT ================= -->

<div class="main">


    <!-- ================= WELCOME ================= -->

    <div class="welcome">

        <h1>

            Welcome,

            <span>

                <?php

                echo htmlspecialchars($name);

                ?>

            </span>

            👋

        </h1>


        <p>

            Welcome to your Student Management System dashboard.

        </p>

    </div>



    <!-- ================= DASHBOARD CARDS ================= -->

    <div class="cards">


        <!-- PROFILE -->

        <a
            href="profile.php"
            class="card"
        >

            <div class="icon">

                👤

            </div>


            <h2>

                Profile

            </h2>


            <p>

                View Your Details

            </p>

        </a>



        <!-- ATTENDANCE -->

        <a
            href="attendance.php"
            class="card"
        >

            <div class="icon">

                📅

            </div>


            <h2>

                Attendance

            </h2>


            <p>

                Check Your Attendance

            </p>

        </a>



        <!-- RESULT -->

        <a
            href="result.php"
            class="card"
        >

            <div class="icon">

                📊

            </div>


            <h2>

                Result

            </h2>


            <p>

                View Your Marks

            </p>

        </a>



        <!-- COURSE -->

        <a
            href="course.php"
            class="card"
        >

            <div class="icon">

                📚

            </div>


            <h2>

                Course

            </h2>


            <p>

                View Course Information

            </p>

        </a>



        <!-- NOTICE BOARD -->

        <a
            href="notice.php"
            class="card"
        >

            <div class="icon">

                📢

            </div>


            <h2>

                Notice Board

            </h2>


            <p>

                View Latest Announcements

            </p>

        </a>



        <!-- REQUEST APPROVAL -->

        <a
            href="request.php"
            class="card"
        >

            <div class="icon">

                📝

            </div>


            <h2>

                Request Approval

            </h2>


            <p>

                Request Profile Changes

            </p>

        </a>


    </div>



    <!-- ================= QUICK INFORMATION ================= -->

    <div class="quick-info">

        <h2>

            🎓 Student Portal

        </h2>


        <p>

            Manage your profile, attendance, academic results,
            course information and request profile changes
            easily from one place.

        </p>

    </div>


</div>



<!-- ================= FOOTER ================= -->

<footer class="footer">

    <h3>

        Student Management System

    </h3>


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