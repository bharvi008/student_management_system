<?php

session_start();

if(!isset($_SESSION['admin']))
{
    header("Location:../login.php");
    exit();
}

include "../db.php";


/* =========================
   GET STUDENT ID
========================= */

if(!isset($_GET['id']) || empty($_GET['id']))
{
    header("Location:admindashboard.php");
    exit();
}

$student_id = intval($_GET['id']);


/* =========================
   STUDENT DETAILS
========================= */

$student_query = mysqli_query(
    $conn,
    "SELECT * FROM students WHERE id='$student_id'"
);

if(!$student_query || mysqli_num_rows($student_query) == 0)
{
    die("Student not found.");
}

$student = mysqli_fetch_assoc($student_query);


/* =========================
   ATTENDANCE SUMMARY
========================= */

$attendance_query = mysqli_query(
    $conn,
    "
    SELECT
        COUNT(*) AS total_classes,
        SUM(CASE WHEN status='Present' THEN 1 ELSE 0 END) AS present,
        SUM(CASE WHEN status='Absent' THEN 1 ELSE 0 END) AS absent
    FROM attendance
    WHERE student_id='$student_id'
    "
);

$attendance = mysqli_fetch_assoc($attendance_query);

$total_classes = $attendance['total_classes'] ?? 0;
$present = $attendance['present'] ?? 0;
$absent = $attendance['absent'] ?? 0;

$percentage = 0;

if($total_classes > 0)
{
    $percentage = round(($present / $total_classes) * 100);
}


/* =========================
   DATE WISE ATTENDANCE
========================= */

$history_query = mysqli_query(
    $conn,
    "
    SELECT
        attendance_date,
        status
    FROM attendance
    WHERE student_id='$student_id'
    ORDER BY attendance_date DESC
    "
);

?>

<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Low Attendance - EduTrack</title>

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    font-family:Arial, Helvetica, sans-serif;
    background:#F7F7F5;
    color:#292524;
}


/* =========================
   MAIN
========================= */

.main{
    min-height:100vh;
    padding:35px;
}


/* =========================
   TOP BAR
========================= */

.topbar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:25px;
}

.title-area h1{
    font-size:28px;
    color:#1C1917;
}

.title-area p{
    color:#78716C;
    margin-top:6px;
    font-size:14px;
}

.back-btn{
    text-decoration:none;
    background:#1C1917;
    color:white;
    padding:11px 18px;
    border-radius:9px;
    font-size:14px;
    transition:.3s;
}

.back-btn:hover{
    background:#FF6B00;
}


/* =========================
   STUDENT CARD
========================= */

.student-card{
    background:white;
    border-radius:16px;
    padding:25px;
    box-shadow:0 5px 20px rgba(0,0,0,.07);
    margin-bottom:25px;
    border:1px solid #E7E5E4;
}

.student-header{
    display:flex;
    align-items:center;
    gap:20px;
}

.student-photo{
    width:75px;
    height:75px;
    border-radius:50%;
    object-fit:cover;
    background:#FFF7ED;
    border:3px solid #FF8A00;
}

.student-placeholder{
    width:75px;
    height:75px;
    border-radius:50%;
    background:linear-gradient(135deg,#FF6B00,#FFB000);
    display:flex;
    align-items:center;
    justify-content:center;
    color:white;
    font-size:28px;
    font-weight:bold;
}

.student-info h2{
    font-size:22px;
    margin-bottom:7px;
}

.student-info p{
    color:#78716C;
    font-size:14px;
    margin:3px 0;
}


/* =========================
   STAT CARDS
========================= */

.stats{
    display:grid;
    grid-template-columns:repeat(4,1fr);
    gap:18px;
    margin-bottom:25px;
}

.stat-card{
    background:white;
    border-radius:14px;
    padding:22px;
    border:1px solid #E7E5E4;
    box-shadow:0 4px 15px rgba(0,0,0,.05);
}

.stat-card h4{
    font-size:13px;
    color:#78716C;
    text-transform:uppercase;
    margin-bottom:10px;
}

.stat-card .number{
    font-size:28px;
    font-weight:bold;
    color:#1C1917;
}

.present-number{
    color:#16A34A !important;
}

.absent-number{
    color:#DC2626 !important;
}

.low-number{
    color:#EA580C !important;
}


/* =========================
   ATTENDANCE BOX
========================= */

.attendance-box{
    background:white;
    border-radius:16px;
    padding:25px;
    margin-bottom:25px;
    border:1px solid #E7E5E4;
    box-shadow:0 5px 20px rgba(0,0,0,.06);
}

.attendance-title{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:18px;
}

.attendance-title h3{
    font-size:18px;
}

.percentage{
    font-size:20px;
    font-weight:bold;
    color:#DC2626;
}

.progress-bg{
    width:100%;
    height:13px;
    background:#E7E5E4;
    border-radius:20px;
    overflow:hidden;
}

.progress-bar{
    height:100%;
    width:<?php echo $percentage; ?>%;
    background:linear-gradient(90deg,#FF6B00,#FFB000);
    border-radius:20px;
}


/* =========================
   HISTORY TABLE
========================= */

.history-card{
    background:white;
    border-radius:16px;
    padding:25px;
    border:1px solid #E7E5E4;
    box-shadow:0 5px 20px rgba(0,0,0,.06);
}

.history-card h3{
    margin-bottom:20px;
}

.table-wrapper{
    overflow-x:auto;
}

table{
    width:100%;
    border-collapse:collapse;
}

th{
    background:#FFF7ED;
    color:#57534E;
    text-align:left;
    padding:14px;
    font-size:13px;
}

td{
    padding:14px;
    border-bottom:1px solid #F0EEEC;
    font-size:14px;
}

.status{
    display:inline-block;
    padding:6px 12px;
    border-radius:20px;
    font-size:12px;
    font-weight:bold;
}

.present{
    background:#DCFCE7;
    color:#166534;
}

.absent{
    background:#FEE2E2;
    color:#991B1B;
}

.no-data{
    text-align:center;
    padding:30px;
    color:#78716C;
}


/* =========================
   RESPONSIVE
========================= */

@media(max-width:900px){

    .stats{
        grid-template-columns:repeat(2,1fr);
    }

}

@media(max-width:600px){

    .main{
        padding:20px;
    }

    .topbar{
        flex-direction:column;
        align-items:flex-start;
        gap:15px;
    }

    .stats{
        grid-template-columns:1fr;
    }

    .student-header{
        align-items:flex-start;
    }

}

</style>

</head>


<body>


<div class="main">


    <!-- TOP BAR -->

    <div class="topbar">

        <div class="title-area">

            <h1>⚠️ Low Attendance Details</h1>

            <p>Student attendance information</p>

        </div>

        <a href="admindashboard.php" class="back-btn">
            ← Back to Dashboard
        </a>

    </div>


    <!-- STUDENT INFORMATION -->

    <div class="student-card">

        <div class="student-header">

            <?php

            if(!empty($student['photo']))
            {

            ?>

                <img
                    src="../uploads/<?php echo htmlspecialchars($student['photo']); ?>"
                    class="student-photo"
                >

            <?php

            }
            else
            {

            ?>

                <div class="student-placeholder">
                    <?php echo strtoupper(substr($student['name'],0,1)); ?>
                </div>

            <?php

            }

            ?>


            <div class="student-info">

                <h2>
                    <?php echo htmlspecialchars($student['name']); ?>
                </h2>

                <p>
                    Course:
                    <?php echo htmlspecialchars($student['course']); ?>
                </p>

                <p>
                    Semester:
                    <?php echo htmlspecialchars($student['semester']); ?>
                </p>

            </div>

        </div>

    </div>


    <!-- STATISTICS -->

    <div class="stats">


        <div class="stat-card">

            <h4>Total Classes</h4>

            <div class="number">
                <?php echo $total_classes; ?>
            </div>

        </div>


        <div class="stat-card">

            <h4>Present</h4>

            <div class="number present-number">
                <?php echo $present; ?>
            </div>

        </div>


        <div class="stat-card">

            <h4>Absent</h4>

            <div class="number absent-number">
                <?php echo $absent; ?>
            </div>

        </div>


        <div class="stat-card">

            <h4>Attendance</h4>

            <div class="number low-number">
                <?php echo $percentage; ?>%
            </div>

        </div>


    </div>


    <!-- ATTENDANCE PROGRESS -->

    <div class="attendance-box">

        <div class="attendance-title">

            <h3>Attendance Percentage</h3>

            <div class="percentage">
                <?php echo $percentage; ?>%
            </div>

        </div>

        <div class="progress-bg">

            <div class="progress-bar"></div>

        </div>

    </div>


    <!-- ATTENDANCE HISTORY -->

    <div class="history-card">

        <h3>📅 Attendance History</h3>

        <div class="table-wrapper">

            <table>

                <thead>

                    <tr>

                        <th>#</th>

                        <th>Date</th>

                        <th>Status</th>

                    </tr>

                </thead>

                <tbody>

                <?php

                $count = 1;

                if($history_query && mysqli_num_rows($history_query) > 0)
                {

                    while($row = mysqli_fetch_assoc($history_query))
                    {

                ?>

                        <tr>

                            <td>
                                <?php echo $count++; ?>
                            </td>

                            <td>
                                <?php
                                echo date(
                                    "d M Y",
                                    strtotime($row['attendance_date'])
                                );
                                ?>
                            </td>

                            <td>

                                <?php

                                if(strtolower($row['status']) == 'present')
                                {

                                ?>

                                    <span class="status present">
                                        Present
                                    </span>

                                <?php

                                }
                                else
                                {

                                ?>

                                    <span class="status absent">
                                        Absent
                                    </span>

                                <?php

                                }

                                ?>

                            </td>

                        </tr>

                <?php

                    }

                }
                else
                {

                ?>

                    <tr>

                        <td colspan="3" class="no-data">
                            No attendance records found.
                        </td>

                    </tr>

                <?php

                }

                ?>

                </tbody>

            </table>

        </div>

    </div>


</div>


</body>

</html>