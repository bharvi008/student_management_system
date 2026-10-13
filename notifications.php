<?php

session_start();

if(!isset($_SESSION['student_id']))
{
    header("Location:../login.php");
    exit();
}

include "../db.php";

$student_id = $_SESSION['student_id'];


/* =========================
   MARK ALL AS READ
========================= */

if(isset($_POST['mark_all_read']))
{
    mysqli_query(
        $conn,
        "UPDATE notifications
         SET is_read=1
         WHERE student_id='$student_id'"
    );

    header("Location:notifications.php");
    exit();
}


/* =========================
   MARK SINGLE NOTIFICATION
   AS READ
========================= */

if(isset($_GET['read']))
{
    $notification_id = intval($_GET['read']);

    mysqli_query(
        $conn,
        "UPDATE notifications
         SET is_read=1
         WHERE id='$notification_id'
         AND student_id='$student_id'"
    );

    header("Location:notifications.php");
    exit();
}


/* =========================
   FETCH NOTIFICATIONS
========================= */

$notifications = mysqli_query(
    $conn,
    "SELECT *
     FROM notifications
     WHERE student_id='$student_id'
     ORDER BY created_at DESC"
);


/* =========================
   UNREAD COUNT
========================= */

$unread_count = 0;

$count_query = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM notifications
     WHERE student_id='$student_id'
     AND is_read=0"
);

if($count_query)
{
    $count_data = mysqli_fetch_assoc($count_query);

    $unread_count = $count_data['total'];
}

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Notifications | EduTrack</title>


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

    min-height:100vh;

}


/* ================= HEADER ================= */

.header{

    width:100%;

    min-height:75px;

    background:
        linear-gradient(
            135deg,
            #1C1917,
            #292524
        );

    display:flex;

    align-items:center;

    justify-content:space-between;

    padding:15px 45px;

    box-shadow:
        0 5px 25px rgba(0,0,0,.20);

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


/* ================= HEADER BUTTON ================= */

.back-btn{

    text-decoration:none;

    color:#FFFFFF;

    background:
        linear-gradient(
            90deg,
            #FF6B00,
            #FFB000
        );

    padding:11px 18px;

    border-radius:10px;

    font-size:14px;

    font-weight:600;

    transition:.3s;

}

.back-btn:hover{

    transform:translateY(-2px);

    box-shadow:
        0 7px 18px rgba(255,107,0,.30);

}


/* ================= MAIN ================= */

.container{

    width:94%;

    max-width:1000px;

    margin:40px auto 60px;

}


/* ================= PAGE TOP ================= */

.page-top{

    background:#FFFFFF;

    border-radius:22px;

    padding:28px 30px;

    margin-bottom:25px;

    display:flex;

    align-items:center;

    justify-content:space-between;

    gap:20px;

    box-shadow:
        0 8px 30px rgba(41,37,36,.10);

}


.page-title h1{

    font-size:30px;

    color:#292524;

}

.page-title p{

    margin-top:7px;

    color:#78716C;

    font-size:14px;

}


/* ================= UNREAD COUNT ================= */

.unread-box{

    text-align:center;

    background:#FFF7ED;

    border:1px solid #FED7AA;

    border-radius:15px;

    padding:12px 20px;

    min-width:110px;

}

.unread-box strong{

    display:block;

    font-size:24px;

    color:#FF6B00;

}

.unread-box span{

    font-size:11px;

    color:#78716C;

}


/* ================= MARK ALL ================= */

.mark-form{

    margin-bottom:20px;

    text-align:right;

}

.mark-btn{

    border:none;

    cursor:pointer;

    background:#292524;

    color:#FFFFFF;

    padding:10px 17px;

    border-radius:9px;

    font-size:13px;

    transition:.3s;

}

.mark-btn:hover{

    background:#FF6B00;

}


/* ================= NOTIFICATION CARD ================= */

.notification-card{

    position:relative;

    background:#FFFFFF;

    border-radius:18px;

    padding:22px 24px;

    margin-bottom:16px;

    box-shadow:
        0 7px 25px rgba(41,37,36,.08);

    border-left:5px solid #E7E5E4;

    transition:.3s;

}


.notification-card:hover{

    transform:translateY(-3px);

    box-shadow:
        0 12px 30px rgba(41,37,36,.13);

}


/* ================= UNREAD ================= */

.notification-card.unread{

    border-left:5px solid #FF6B00;

    background:#FFFCF8;

}


/* ================= TOP ================= */

.notification-top{

    display:flex;

    align-items:flex-start;

    justify-content:space-between;

    gap:15px;

}


.notification-left{

    display:flex;

    gap:15px;

    align-items:flex-start;

}


.notification-icon{

    width:45px;

    height:45px;

    flex-shrink:0;

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

    font-size:21px;

}


/* ================= TITLE ================= */

.notification-title{

    font-size:17px;

    font-weight:700;

    color:#292524;

    margin-bottom:7px;

}


/* ================= MESSAGE ================= */

.notification-message{

    color:#57534E;

    font-size:14px;

    line-height:1.6;

}


/* ================= DATE ================= */

.notification-date{

    margin-top:12px;

    font-size:12px;

    color:#A8A29E;

}


/* ================= STATUS ================= */

.status{

    font-size:11px;

    padding:5px 9px;

    border-radius:20px;

    white-space:nowrap;

    font-weight:bold;

}

.status-unread{

    background:#FEE2E2;

    color:#DC2626;

}

.status-read{

    background:#E7F7EC;

    color:#15803D;

}


/* ================= READ BUTTON ================= */

.read-btn{

    display:inline-block;

    margin-top:13px;

    padding:8px 13px;

    background:#FFF7ED;

    color:#EA580C;

    border-radius:8px;

    text-decoration:none;

    font-size:12px;

    font-weight:600;

    transition:.3s;

}

.read-btn:hover{

    background:#FF6B00;

    color:#FFFFFF;

}


/* ================= EMPTY ================= */

.empty{

    background:#FFFFFF;

    border-radius:20px;

    padding:65px 20px;

    text-align:center;

    box-shadow:
        0 8px 25px rgba(41,37,36,.08);

}


.empty-icon{

    font-size:55px;

    margin-bottom:15px;

}


.empty h2{

    font-size:22px;

    margin-bottom:8px;

    color:#292524;

}


.empty p{

    color:#78716C;

    font-size:14px;

}


/* ================= FOOTER ================= */

.footer{

    background:
        linear-gradient(
            135deg,
            #1C1917,
            #292524
        );

    color:#FFFFFF;

    text-align:center;

    padding:28px 20px;

}


.footer h3{

    color:#FFB000;

    font-size:20px;

    margin-bottom:8px;

}


.footer p{

    color:#FFFFFF;

    opacity:.85;

    font-size:12px;

    margin:5px;

}


/* ================= RESPONSIVE ================= */

@media(max-width:700px){

    .header{

        padding:16px 20px;

    }


    .logo a{

        font-size:23px;

    }


    .page-top{

        flex-direction:column;

        align-items:flex-start;

    }


    .unread-box{

        width:100%;

    }


    .notification-top{

        flex-direction:column;

    }


    .notification-left{

        width:100%;

    }


    .container{

        width:95%;

    }

}

</style>

</head>


<body>


<!-- ================= HEADER ================= -->

<header class="header">

    <div class="logo">

        <a href="../index.php">

            Edu<span>Track</span>

        </a>

    </div>


    <a href="studentdashboard.php" class="back-btn">

        ← Dashboard

    </a>

</header>



<!-- ================= MAIN ================= -->

<div class="container">


    <!-- ================= PAGE TOP ================= -->

    <div class="page-top">

        <div class="page-title">

            <h1>🔔 Notifications</h1>

            <p>

                Stay updated with your latest notifications.

            </p>

        </div>


        <div class="unread-box">

            <strong>

                <?php echo $unread_count; ?>

            </strong>

            <span>

                Unread

            </span>

        </div>

    </div>



    <!-- ================= MARK ALL ================= -->

    <?php

    if($unread_count > 0)

    {

    ?>

        <div class="mark-form">

            <form method="POST">

                <button
                    type="submit"
                    name="mark_all_read"
                    class="mark-btn"
                >

                    ✓ Mark All as Read

                </button>

            </form>

        </div>

    <?php

    }

    ?>



    <!-- ================= NOTIFICATIONS ================= -->

    <?php

    if(mysqli_num_rows($notifications) > 0)

    {

        while($row = mysqli_fetch_assoc($notifications))

        {

            $is_unread = ($row['is_read'] == 0);

    ?>


        <div class="notification-card
            <?php
            if($is_unread)
            {
                echo 'unread';
            }
            ?>">


            <div class="notification-top">


                <div class="notification-left">


                    <div class="notification-icon">

                        🔔

                    </div>


                    <div>


                        <div class="notification-title">

                            <?php

                            echo htmlspecialchars(
                                $row['title']
                            );

                            ?>

                        </div>


                        <div class="notification-message">

                            <?php

                            echo nl2br(
                                htmlspecialchars(
                                    $row['message']
                                )
                            );

                            ?>

                        </div>


                        <div class="notification-date">

                            📅

                            <?php

                            echo date(
                                "d M Y, h:i A",
                                strtotime(
                                    $row['created_at']
                                )
                            );

                            ?>

                        </div>


                        <?php

                        if($is_unread)

                        {

                        ?>

                            <a
                                href="notifications.php?read=<?php echo $row['id']; ?>"
                                class="read-btn"
                            >

                                ✓ Mark as Read

                            </a>

                        <?php

                        }

                        ?>


                    </div>

                </div>



                <?php

                if($is_unread)

                {

                ?>

                    <span class="status status-unread">

                        Unread

                    </span>

                <?php

                }
                else

                {

                ?>

                    <span class="status status-read">

                        Read

                    </span>

                <?php

                }

                ?>


            </div>

        </div>


    <?php

        }

    }

    else

    {

    ?>


        <div class="empty">

            <div class="empty-icon">

                🔔

            </div>

            <h2>

                No Notifications

            </h2>

            <p>

                You don't have any notifications right now.

            </p>

        </div>


    <?php

    }

    ?>


</div>



<!-- ================= FOOTER ================= -->

<footer class="footer">

    <h3>

        EduTrack

    </h3>

    <p>

        Student Management System

    </p>

    <p>

        © 2026 EduTrack. All Rights Reserved.

    </p>

</footer>


</body>

</html>