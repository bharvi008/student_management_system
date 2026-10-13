<?php
session_start();

if(!isset($_SESSION['student_id']))
{
    header("Location:../login.php");
    exit();
}

include "../db.php";

$student_id = $_SESSION['student_id'];

/* FETCH NOTICES */
$notices = mysqli_query(
    $conn,
    "SELECT * FROM notices ORDER BY created_at DESC"
);
?>

<!DOCTYPE html>
<html>
<head>

<title>Notice Board | EduTrack</title>

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Arial, sans-serif;
}

body{
    background:#F7F7F5;
    color:#292524;
}

/* HEADER */

.header{
    background:#171412;
    padding:18px 40px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    box-shadow:0 4px 15px rgba(0,0,0,.12);
}

.logo{
    color:white;
    font-size:27px;
    font-weight:bold;
}

.logo span{
    color:#FF8A00;
}

.back-btn{
    text-decoration:none;
    color:white;
    background:linear-gradient(90deg,#FF6B00,#FFB000);
    padding:10px 18px;
    border-radius:8px;
    font-size:14px;
}

/* MAIN */

.container{
    max-width:1000px;
    margin:40px auto;
    padding:0 20px;
}

/* TITLE */

.page-title{
    margin-bottom:30px;
}

.page-title h1{
    font-size:32px;
    color:#1C1917;
}

.page-title p{
    color:#78716C;
    margin-top:7px;
}

/* NOTICE */

.notice-card{
    background:white;
    border-radius:15px;
    padding:24px;
    margin-bottom:18px;
    box-shadow:0 5px 20px rgba(0,0,0,.07);
    border-left:5px solid #FF8A00;
    transition:.3s;
}

.notice-card:hover{
    transform:translateY(-3px);
    box-shadow:0 8px 25px rgba(0,0,0,.10);
}

.notice-top{
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:15px;
}

.notice-title{
    font-size:20px;
    font-weight:bold;
    color:#292524;
}

.notice-description{
    margin-top:14px;
    color:#57534E;
    line-height:1.6;
    font-size:15px;
}

.notice-date{
    margin-top:18px;
    font-size:12px;
    color:#A8A29E;
}

/* PRIORITY */

.priority{
    padding:6px 12px;
    border-radius:20px;
    font-size:12px;
    font-weight:bold;
    white-space:nowrap;
}

.normal{
    background:#E7F7EC;
    color:#15803D;
}

.important{
    background:#FFF4D6;
    color:#B45309;
}

.urgent{
    background:#FEE2E2;
    color:#DC2626;
}

/* EMPTY */

.no-notice{
    background:white;
    padding:50px 20px;
    text-align:center;
    border-radius:15px;
    box-shadow:0 5px 20px rgba(0,0,0,.06);
}

.no-notice .icon{
    font-size:45px;
    margin-bottom:15px;
}

.no-notice h3{
    color:#292524;
    margin-bottom:8px;
}

.no-notice p{
    color:#78716C;
}

/* MOBILE */

@media(max-width:600px){

    .header{
        padding:15px 20px;
    }

    .logo{
        font-size:23px;
    }

    .back-btn{
        padding:8px 12px;
    }

    .page-title h1{
        font-size:26px;
    }

    .notice-top{
        align-items:flex-start;
        flex-direction:column;
    }

}

</style>

</head>

<body>

<!-- HEADER -->

<div class="header">

    <div class="logo">
        Edu<span>Track</span>
    </div>

    <a href="studentdashboard.php" class="back-btn">
        ← Dashboard
    </a>

</div>


<!-- MAIN -->

<div class="container">

    <div class="page-title">

        <h1>📢 Notice Board</h1>

        <p>Latest college announcements and important updates</p>

    </div>


    <?php

    if(mysqli_num_rows($notices) > 0)
    {

        while($row = mysqli_fetch_assoc($notices))
        {

            $priority = strtolower($row['priority']);

    ?>

        <div class="notice-card">

            <div class="notice-top">

                <div class="notice-title">
                    <?php echo htmlspecialchars($row['title']); ?>
                </div>

                <span class="priority <?php echo $priority; ?>">
                    <?php echo htmlspecialchars($row['priority']); ?>
                </span>

            </div>


            <div class="notice-description">

                <?php
                echo nl2br(htmlspecialchars($row['description']));
                ?>

            </div>


            <div class="notice-date">

                📅 Posted:
                <?php
                echo date("d M Y, h:i A", strtotime($row['created_at']));
                ?>

            </div>

        </div>

    <?php

        }

    }
    else
    {

    ?>

        <div class="no-notice">

            <div class="icon">📢</div>

            <h3>No Notices Available</h3>

            <p>There are currently no announcements.</p>

        </div>

    <?php

    }

    ?>

</div>

</body>
</html>