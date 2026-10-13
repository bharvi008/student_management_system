<?php

session_start();

include "../db.php";

if(!isset($_SESSION['teacher_id']))
{
    header("Location:../login.php");
    exit();
}

if(!isset($_GET['id']))
{
    header("Location:students.php");
    exit();
}

$id = intval($_GET['id']);

$result = mysqli_query(
    $conn,
    "SELECT * FROM students WHERE id='$id'"
);

if(mysqli_num_rows($result) == 0)
{
    header("Location:students.php");
    exit();
}

$row = mysqli_fetch_assoc($result);

$name = ucwords(
    strtolower(
        trim($row['name'])
    )
);

?>

<!DOCTYPE html>

<html>

<head>

<title>EduTrack | Student Profile</title>

<meta name="viewport" content="width=device-width, initial-scale=1">

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


/* =========================================
   BODY
========================================= */

body{

    min-height:100vh;

    background:#F7F7F5;

    color:#292524;
}


/* =========================================
   MAIN
========================================= */

.main{

    min-height:100vh;

    display:flex;

    justify-content:center;

    align-items:center;

    padding:40px 20px;
}


/* =========================================
   PROFILE WRAPPER
========================================= */

.profile-wrapper{

    width:850px;

    max-width:100%;
}


/* =========================================
   PAGE HEADER
========================================= */

.page-header{

    display:flex;

    justify-content:space-between;

    align-items:flex-end;

    margin-bottom:18px;
}

.page-header h1{

    font-size:25px;

    color:#1C1917;

    margin-bottom:5px;
}

.page-header p{

    font-size:13px;

    color:#78716C;
}


/* =========================================
   PROFILE CARD
========================================= */

.profile-card{

    width:100%;

    background:white;

    border:1px solid #E7E5E4;

    border-radius:20px;

    overflow:hidden;

    box-shadow:
        0 12px 35px
        rgba(0,0,0,.08);
}


/* =========================================
   PROFILE TOP
========================================= */

.profile-top{

    min-height:190px;

    padding:30px;

    display:flex;

    align-items:center;

    gap:25px;

    position:relative;

    overflow:hidden;

    background:
        linear-gradient(
            135deg,
            #1C1917,
            #292524
        );
}


/* ORANGE CIRCLE */

.profile-top:before{

    content:"";

    position:absolute;

    width:230px;

    height:230px;

    border-radius:50%;

    right:-90px;

    top:-120px;

    background:
        rgba(255,138,0,.12);
}


/* SECOND CIRCLE */

.profile-top:after{

    content:"";

    position:absolute;

    width:130px;

    height:130px;

    border-radius:50%;

    right:90px;

    bottom:-90px;

    background:
        rgba(255,176,0,.08);
}


/* =========================================
   PHOTO
========================================= */

.photo-area{

    position:relative;

    z-index:2;

    flex-shrink:0;
}

.student-photo{

    width:125px;

    height:125px;

    object-fit:cover;

    border-radius:18px;

    border:4px solid white;

    display:block;

    box-shadow:
        0 8px 25px
        rgba(0,0,0,.30);
}


.no-photo{

    width:125px;

    height:125px;

    border-radius:18px;

    border:4px solid white;

    background:#FFF7ED;

    display:flex;

    justify-content:center;

    align-items:center;

    font-size:45px;

    box-shadow:
        0 8px 25px
        rgba(0,0,0,.30);
}


/* =========================================
   STUDENT NAME
========================================= */

.profile-info{

    position:relative;

    z-index:2;
}

.profile-info h2{

    color:white;

    font-size:28px;

    margin-bottom:7px;
}

.profile-info p{

    color:#D6D3D1;

    font-size:13px;

    margin-bottom:13px;
}


/* COURSE BADGE */

.course-badge{

    display:inline-flex;

    align-items:center;

    gap:6px;

    padding:7px 13px;

    border-radius:20px;

    background:
        rgba(255,255,255,.10);

    border:1px solid
        rgba(255,255,255,.15);

    color:#FFB000;

    font-size:11px;

    font-weight:bold;
}


/* =========================================
   PROFILE BODY
========================================= */

.profile-body{

    padding:28px;
}


/* =========================================
   SECTION HEADER
========================================= */

.section-header{

    display:flex;

    justify-content:space-between;

    align-items:center;

    margin-bottom:18px;

    padding-bottom:15px;

    border-bottom:1px solid #E7E5E4;
}

.section-header h3{

    color:#1C1917;

    font-size:18px;
}

.section-header span{

    color:#78716C;

    font-size:11px;
}


/* =========================================
   INFORMATION GRID
========================================= */

.info-grid{

    display:grid;

    grid-template-columns:
        repeat(2,1fr);

    gap:15px;
}


/* =========================================
   INFO BOX
========================================= */

.info-box{

    position:relative;

    background:#FAFAF9;

    border:1px solid #E7E5E4;

    border-radius:13px;

    padding:17px;

    overflow:hidden;

    transition:.3s;
}


/* ORANGE LEFT BORDER */

.info-box:before{

    content:"";

    position:absolute;

    left:0;

    top:0;

    width:3px;

    height:100%;

    background:
        linear-gradient(
            #FF6B00,
            #FFB000
        );
}


.info-box:hover{

    background:#FFF7ED;

    border-color:#FFB000;

    transform:translateY(-2px);

    box-shadow:
        0 7px 18px
        rgba(0,0,0,.06);
}


/* =========================================
   LABEL
========================================= */

.info-label{

    display:block;

    color:#78716C;

    font-size:10px;

    text-transform:uppercase;

    letter-spacing:1px;

    margin-bottom:8px;
}


/* =========================================
   VALUE
========================================= */

.info-value{

    display:block;

    color:#292524;

    font-size:14px;

    font-weight:bold;

    word-break:break-word;
}


/* =========================================
   COURSE BADGE BODY
========================================= */

.body-course{

    display:inline-flex;

    align-items:center;

    gap:5px;

    padding:6px 12px;

    border-radius:20px;

    background:#FFF1E6;

    color:#EA580C;

    font-size:12px;

    font-weight:bold;
}


/* =========================================
   SEMESTER BADGE
========================================= */

.semester-badge{

    display:inline-flex;

    align-items:center;

    gap:5px;

    padding:6px 12px;

    border-radius:20px;

    background:#292524;

    color:white;

    font-size:12px;

    font-weight:bold;
}


/* =========================================
   BACK AREA
========================================= */

.back-area{

    display:flex;

    justify-content:center;

    align-items:center;

    margin-top:25px;

    padding-top:20px;

    border-top:1px solid #E7E5E4;
}


/* =========================================
   BACK BUTTON
========================================= */

.back-btn{

    display:inline-flex;

    align-items:center;

    justify-content:center;

    gap:8px;

    padding:12px 24px;

    border-radius:10px;

    text-decoration:none;

    color:white;

    font-size:13px;

    font-weight:bold;

    background:
        linear-gradient(
            90deg,
            #FF6B00,
            #FFB000
        );

    box-shadow:
        0 7px 18px
        rgba(255,107,0,.20);

    transition:.3s;
}

.back-btn:hover{

    transform:translateY(-2px);

    box-shadow:
        0 10px 24px
        rgba(255,107,0,.30);
}


/* =========================================
   FOOTER
========================================= */

.footer{

    text-align:center;

    margin-top:20px;

    color:#78716C;

    font-size:11px;
}

.footer strong{

    color:#FF6B00;
}


/* =========================================
   TABLET
========================================= */

@media(max-width:700px){

    .main{

        padding:25px 15px;
    }

    .profile-wrapper{

        width:100%;
    }

    .page-header{

        display:block;

        margin-bottom:15px;
    }

    .profile-top{

        flex-direction:column;

        text-align:center;

        padding:25px 20px;
    }

    .profile-info h2{

        font-size:23px;
    }

    .profile-body{

        padding:20px;
    }

    .info-grid{

        grid-template-columns:1fr;
    }

    .section-header{

        display:block;
    }

    .section-header span{

        display:block;

        margin-top:5px;
    }

}


/* =========================================
   SMALL MOBILE
========================================= */

@media(max-width:420px){

    .main{

        padding:15px 10px;
    }

    .profile-top{

        padding:22px 15px;
    }

    .student-photo,
    .no-photo{

        width:105px;

        height:105px;
    }

    .profile-info h2{

        font-size:21px;
    }

    .profile-body{

        padding:15px;
    }

}

</style>

</head>


<body>


<!-- =========================================
     MAIN
========================================= -->

<div class="main">


    <div class="profile-wrapper">


        <!-- PAGE HEADER -->

        <div class="page-header">

            <div>

                <h1>
                    Student Profile
                </h1>

                <p>
                    View student personal and academic information
                </p>

            </div>

        </div>



        <!-- PROFILE CARD -->

        <div class="profile-card">


            <!-- =================================
                 PROFILE TOP
            ================================== -->

            <div class="profile-top">


                <!-- PHOTO -->

                <div class="photo-area">

                    <?php

                    if(!empty($row['photo']))
                    {

                    ?>

                        <img
                            src="../uploads/<?php
                            echo htmlspecialchars(
                                $row['photo']
                            );
                            ?>"
                            alt="Student Photo"
                            class="student-photo"
                        >

                    <?php

                    }
                    else
                    {

                    ?>

                        <div class="no-photo">
                            👤
                        </div>

                    <?php

                    }

                    ?>

                </div>



                <!-- STUDENT NAME -->

                <div class="profile-info">

                    <h2>

                        <?php

                        echo htmlspecialchars(
                            $name
                        );

                        ?>

                    </h2>

                    <p>
                        Student Profile
                    </p>


                    <div class="course-badge">

                        🎓

                        <?php

                        echo htmlspecialchars(
                            $row['course']
                        );

                        ?>

                    </div>

                </div>


            </div>



            <!-- =================================
                 PROFILE BODY
            ================================== -->

            <div class="profile-body">


                <!-- SECTION -->

                <div class="section-header">

                    <h3>
                        Personal & Academic Details
                    </h3>

                    <span>
                        Registered Student Information
                    </span>

                </div>



                <!-- INFORMATION -->

                <div class="info-grid">


                    <!-- NAME -->

                    <div class="info-box">

                        <span class="info-label">
                            Full Name
                        </span>

                        <span class="info-value">

                            <?php

                            echo htmlspecialchars(
                                $name
                            );

                            ?>

                        </span>

                    </div>



                    <!-- EMAIL -->

                    <div class="info-box">

                        <span class="info-label">
                            Email Address
                        </span>

                        <span class="info-value">

                            <?php

                            echo htmlspecialchars(
                                $row['email']
                            );

                            ?>

                        </span>

                    </div>



                    <!-- PHONE -->

                    <div class="info-box">

                        <span class="info-label">
                            Phone Number
                        </span>

                        <span class="info-value">

                            <?php

                            echo htmlspecialchars(
                                $row['phone']
                            );

                            ?>

                        </span>

                    </div>



                    <!-- GENDER -->

                    <div class="info-box">

                        <span class="info-label">
                            Gender
                        </span>

                        <span class="info-value">

                            <?php

                            echo htmlspecialchars(
                                $row['gender']
                            );

                            ?>

                        </span>

                    </div>



                    <!-- COURSE -->

                    <div class="info-box">

                        <span class="info-label">
                            Course
                        </span>

                        <span class="info-value">

                            <span class="body-course">

                                📚

                                <?php

                                echo htmlspecialchars(
                                    $row['course']
                                );

                                ?>

                            </span>

                        </span>

                    </div>



                    <!-- SEMESTER -->

                    <div class="info-box">

                        <span class="info-label">
                            Semester
                        </span>

                        <span class="info-value">

                            <span class="semester-badge">

                                🎓

                                Semester

                                <?php

                                echo htmlspecialchars(
                                    $row['semester']
                                );

                                ?>

                            </span>

                        </span>

                    </div>


                </div>



                <!-- BACK -->

                <div class="back-area">

                    <a
                        href="students.php"
                        class="back-btn"
                    >

                        ⬅

                        Back to Students

                    </a>

                </div>


            </div>


        </div>



        <!-- FOOTER -->

        <div class="footer">

            <strong>
                EduTrack
            </strong>

            — Student Management System

        </div>


    </div>


</div>


</body>

</html>