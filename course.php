<?php

session_start();

include "../db.php";

if(!isset($_SESSION['teacher_id']))
{
    header("Location:../login.php");
    exit();
}

$result = mysqli_query($conn,"SELECT * FROM course ORDER BY id DESC");

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Courses | EduTrack</title>

<style>

/* ================================
   RESET
================================ */

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    font-family:Arial, Helvetica, sans-serif;
    background:#FFF7ED;
    color:#292524;
}


/* ================================
   HEADER
================================ */

.top-header{
    width:100%;
    background:#1C1917;
    padding:18px 7%;

    display:flex;
    align-items:center;
    justify-content:space-between;

    position:sticky;
    top:0;
    z-index:1000;

    box-shadow:0 4px 20px rgba(0,0,0,.15);
}

.logo a{
    text-decoration:none;
    font-size:28px;
    font-weight:800;
    color:white;
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
    color:white;
    font-size:15px;
    transition:.3s;
}

.home-nav a:hover{
    color:#FFB000;
}


/* ================================
   TEACHER MENU
================================ */

.teacher-menu{
    width:fit-content;
    max-width:95%;

    margin:25px auto 10px;
    padding:10px 14px;

    display:flex;
    align-items:center;
    justify-content:center;

    gap:8px;
    flex-wrap:wrap;

    background:white;
    border-radius:50px;

    box-shadow:0 10px 30px rgba(0,0,0,.08);
}

.teacher-menu a{
    text-decoration:none;
    color:#44403C;

    padding:11px 16px;

    border-radius:30px;

    font-size:14px;
    font-weight:600;

    transition:.3s;
}

.teacher-menu a:hover{
    background:#FFF1E6;
    color:#FF6B00;
    transform:translateY(-2px);
}

.teacher-menu a.active{
    background:linear-gradient(
        90deg,
        #FF6B00,
        #FFB000
    );

    color:white;

    box-shadow:
    0 5px 15px
    rgba(255,107,0,.25);
}

.teacher-menu .logout{
    color:#DC2626;
}

.teacher-menu .logout:hover{
    background:#FEE2E2;
    color:#DC2626;
}


/* ================================
   MAIN
================================ */

.main{
    width:90%;
    max-width:1250px;

    margin:40px auto 60px;

    animation:fadeUp .6s ease;
}


/* ================================
   PAGE HEADER
================================ */

.page-header{
    background:
    linear-gradient(
        135deg,
        #1C1917,
        #292524
    );

    color:white;

    padding:32px 35px;

    border-radius:25px;

    display:flex;
    align-items:center;
    justify-content:space-between;

    margin-bottom:30px;

    box-shadow:
    0 15px 35px
    rgba(0,0,0,.12);
}

.page-header h1{
    font-size:30px;
    margin-bottom:8px;
}

.page-header p{
    color:#D6D3D1;
    line-height:1.5;
}

.course-badge{
    background:
    linear-gradient(
        90deg,
        #FF6B00,
        #FFB000
    );

    padding:13px 20px;

    border-radius:25px;

    font-weight:bold;

    white-space:nowrap;
}


/* ================================
   COURSE GRID
================================ */

.course-grid{
    display:grid;

    grid-template-columns:
    repeat(3,1fr);

    gap:22px;
}


/* ================================
   COURSE CARD
================================ */

.course-card{
    background:white;

    padding:25px;

    border-radius:22px;

    border:1px solid #F1F0EE;

    box-shadow:
    0 10px 28px
    rgba(0,0,0,.07);

    position:relative;

    overflow:hidden;

    transition:.35s;

    animation:fadeUp .6s ease;
}

.course-card:hover{
    transform:translateY(-8px);

    box-shadow:
    0 18px 35px
    rgba(0,0,0,.12);

    border-color:#FED7AA;
}

.course-card::before{
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
}


/* ================================
   COURSE ICON
================================ */

.course-icon{
    width:60px;
    height:60px;

    border-radius:18px;

    background:#FFF1E6;

    display:flex;
    align-items:center;
    justify-content:center;

    font-size:29px;

    margin-bottom:18px;
}


/* ================================
   COURSE NAME
================================ */

.course-card h2{
    font-size:20px;

    margin-bottom:15px;

    color:#292524;
}


/* ================================
   COURSE DETAILS
================================ */

.course-detail{
    display:flex;

    align-items:center;

    gap:10px;

    padding:10px 0;

    border-bottom:1px solid #F5F5F4;

    font-size:14px;
}

.course-detail:last-child{
    border-bottom:none;
}

.detail-icon{
    width:35px;
    height:35px;

    border-radius:10px;

    background:#FFF7ED;

    display:flex;
    align-items:center;
    justify-content:center;
}

.detail-text{
    display:flex;
    flex-direction:column;
}

.detail-text small{
    color:#A8A29E;
    font-size:11px;
    margin-bottom:2px;
}

.detail-text strong{
    color:#44403C;
    font-size:14px;
}


/* ================================
   COURSE NUMBER
================================ */

.course-number{
    position:absolute;

    top:18px;
    right:18px;

    width:35px;
    height:35px;

    border-radius:50%;

    background:#1C1917;

    color:white;

    display:flex;
    align-items:center;
    justify-content:center;

    font-size:12px;
    font-weight:bold;
}


/* ================================
   EMPTY STATE
================================ */

.empty-state{
    background:white;

    padding:70px 20px;

    border-radius:25px;

    text-align:center;

    box-shadow:
    0 10px 28px
    rgba(0,0,0,.07);
}

.empty-icon{
    font-size:65px;

    margin-bottom:15px;
}

.empty-state h2{
    margin-bottom:8px;
}

.empty-state p{
    color:#78716C;
}


/* ================================
   FOOTER
================================ */

.footer{
    width:100%;

    background:#1C1917;

    color:white;

    text-align:center;

    padding:35px 20px;

    margin-top:60px;
}

.footer h3{
    color:#FFB000;
    margin-bottom:10px;
}

.footer p{
    color:#A8A29E;
    font-size:14px;
    margin:7px 0;
}


/* ================================
   ANIMATION
================================ */

@keyframes fadeUp{

    from{
        opacity:0;
        transform:translateY(20px);
    }

    to{
        opacity:1;
        transform:translateY(0);
    }

}


/* ================================
   TABLET
================================ */

@media(max-width:950px){

    .course-grid{
        grid-template-columns:
        repeat(2,1fr);
    }

}


/* ================================
   MOBILE
================================ */

@media(max-width:700px){

    .top-header{
        flex-direction:column;
        gap:15px;
    }

    .home-nav{
        gap:15px;
        flex-wrap:wrap;
        justify-content:center;
    }

    .teacher-menu{
        border-radius:20px;
    }

    .teacher-menu a{
        font-size:13px;
        padding:9px 12px;
    }

    .main{
        width:94%;
        margin-top:25px;
    }

    .page-header{
        flex-direction:column;
        align-items:flex-start;
        gap:18px;

        padding:25px;
    }

    .page-header h1{
        font-size:25px;
    }

    .course-grid{
        grid-template-columns:1fr;
    }

}

</style>

</head>


<body>


<!-- ================================
     HEADER
================================ -->

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

    </nav>

</header>



<!-- ================================
     TEACHER MENU
================================ -->

<nav class="teacher-menu">

    <a href="dashboard.php">
        🏠 Overview
    </a>

    <a href="students.php">
        👨‍🎓 Students
    </a>

    <a href="attendance.php">
        📅 Attendance
    </a>

    <a href="result.php">
        📊 Result
    </a>

    <a href="course.php" class="active">
        📚 Course
    </a>

    <a href="profile.php">
        👤 My Profile
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



<!-- ================================
     MAIN
================================ -->

<div class="main">


    <!-- PAGE HEADER -->

    <div class="page-header">

        <div>

            <h1>
                📚 Available Courses
            </h1>

            <p>
                Explore all courses available in EduTrack.
            </p>

        </div>


        <div class="course-badge">

            🎓 Course Panel

        </div>

    </div>



    <!-- ================================
         COURSES
    ================================= -->

    <?php

    if(mysqli_num_rows($result) > 0)

    {

    ?>

    <div class="course-grid">

        <?php

        $count = 1;

        while($row = mysqli_fetch_assoc($result))

        {

        ?>

        <div class="course-card">


            <!-- NUMBER -->

            <div class="course-number">

                <?php echo $count; ?>

            </div>


            <!-- ICON -->

            <div class="course-icon">

                📚

            </div>


            <!-- COURSE NAME -->

            <h2>

                <?php

                echo htmlspecialchars(
                    $row['course_name']
                );

                ?>

            </h2>


            <!-- DURATION -->

            <div class="course-detail">

                <div class="detail-icon">
                    ⏱️
                </div>

                <div class="detail-text">

                    <small>
                        Duration
                    </small>

                    <strong>

                        <?php

                        echo htmlspecialchars(
                            $row['duration']
                        );

                        ?>

                    </strong>

                </div>

            </div>


            <!-- FEES -->

            <div class="course-detail">

                <div class="detail-icon">
                    💰
                </div>

                <div class="detail-text">

                    <small>
                        Course Fees
                    </small>

                    <strong>

                        ₹
                        <?php

                        echo htmlspecialchars(
                            $row['fees']
                        );

                        ?>

                    </strong>

                </div>

            </div>


        </div>


        <?php

        $count++;

        }

        ?>

    </div>


    <?php

    }

    else

    {

    ?>


    <!-- EMPTY -->

    <div class="empty-state">

        <div class="empty-icon">
            📚
        </div>

        <h2>
            No Courses Available
        </h2>

        <p>
            Currently there are no courses available.
        </p>

    </div>


    <?php

    }

    ?>


</div>



<!-- ================================
     FOOTER
================================ -->

<footer class="footer">

    <h3>
        EduTrack
    </h3>

    <p>
        Manage Students • Attendance • Results • Courses
    </p>

    <p>
        Designed Using HTML • CSS • JavaScript • PHP • MySQL
    </p>

    <p>
        © 2026 EduTrack. All Rights Reserved.
    </p>

</footer>


</body>

</html>