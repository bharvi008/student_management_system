<?php
session_start();
include "../db.php";

if(!isset($_SESSION['teacher_id']))
{
    header("Location:../login.php");
    exit();
}

$teacher_id = $_SESSION['teacher_id'];

/* =========================
   SELECTED COURSE
========================= */

if(!isset($_SESSION['teacher_course'], $_SESSION['teacher_semester']) || trim($_SESSION['teacher_course']) == "" || trim((string)$_SESSION['teacher_semester']) == "") {
    header("Location:dashboard.php");
    exit();
}

$selected_course = $_SESSION['teacher_course'];
$selected_semester = (string)$_SESSION['teacher_semester'];
$safe_course = mysqli_real_escape_string($conn, $selected_course);
$safe_semester = mysqli_real_escape_string($conn, $selected_semester);

/* =========================
   SELECT DATE
========================= */

$selected_date = isset($_GET['date']) ? $_GET['date'] : date("Y-m-d");


/* =========================
   SAVE ATTENDANCE
========================= */

if(isset($_POST['save']))
{
    $date = $_POST['attendance_date'];

    if(isset($_POST['status']))
    {
        foreach($_POST['status'] as $student_id => $status)
        {
            $student_id = intval($student_id);
            $status = mysqli_real_escape_string($conn, $status);

            /* Verify student belongs to selected course */
            $student_check = mysqli_query(
                $conn,
                "SELECT id FROM students
                 WHERE id='$student_id'
                 AND course='$safe_course'
                 AND semester='$safe_semester'
                 LIMIT 1"
            );

            if(mysqli_num_rows($student_check) == 0)
            {
                continue;
            }

            /* Check already exists */
            $check = mysqli_query($conn,"
                SELECT id FROM attendance
                WHERE student_id='$student_id'
                AND attendance_date='$date'
            ");

            if(mysqli_num_rows($check) > 0)
            {
                /* Update */
                mysqli_query($conn,"
                    UPDATE attendance
                    SET status='$status',
                        teacher_id='$teacher_id'
                    WHERE student_id='$student_id'
                    AND attendance_date='$date'
                ");
            }
            else
            {
                /* Insert */
                mysqli_query($conn,"
                    INSERT INTO attendance
                    (student_id, teacher_id, attendance_date, status)
                    VALUES
                    ('$student_id','$teacher_id','$date','$status')
                ");
            }
        }
    }

    echo "<script>
            alert('Attendance Saved Successfully');
            window.location='attendance.php?date=$date';
          </script>";
    exit();
}


/* =========================
   STUDENTS
========================= */

$students = mysqli_query($conn,"
    SELECT *
    FROM students
    WHERE course='$safe_course' AND semester='$safe_semester'
    ORDER BY name ASC
");


/* =========================
   SELECTED DATE ATTENDANCE
========================= */

$attendance_data = array();

$att = mysqli_query($conn,"
    SELECT *
    FROM attendance
    WHERE attendance_date='$selected_date'
");

while($row = mysqli_fetch_assoc($att))
{
    $attendance_data[$row['student_id']] = $row['status'];
}


/* =========================
   DATE-WISE HISTORY
========================= */

$history = mysqli_query($conn,"
    SELECT
        attendance.attendance_date,
        COUNT(*) AS total,
        SUM(attendance.status='Present') AS present,
        SUM(attendance.status='Absent') AS absent
    FROM attendance
    JOIN students
        ON attendance.student_id=students.id
    WHERE students.course='$safe_course' AND students.semester='$safe_semester'
    GROUP BY attendance.attendance_date
    ORDER BY attendance.attendance_date DESC
");

?>

<!DOCTYPE html>
<html>
<head>

<title>EduTrack - Attendance</title>

<meta name="viewport" content="width=device-width, initial-scale=1">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Arial, sans-serif;
}

body{
    background:#FFF7ED;
    color:#292524;
}

/* HEADER */

.top-header{
    background:#1C1917;
    color:white;
    padding:18px 7%;
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.logo a{
    color:white;
    text-decoration:none;
    font-size:28px;
    font-weight:bold;
}

.logo span{
    color:#FF8A00;
}

.home-nav{
    display:flex;
    gap:25px;
}

.home-nav a{
    color:white;
    text-decoration:none;
    font-size:14px;
}

.home-nav a:hover{
    color:#FFB000;
}


/* MENU */

.teacher-menu{
    width:94%;
    max-width:1200px;
    margin:20px auto;
    padding:12px;
    background:white;
    border-radius:18px;
    box-shadow:0 8px 25px rgba(0,0,0,.08);

    display:flex;
    flex-wrap:wrap;
    justify-content:center;
    gap:8px;
}

.teacher-menu a{
    text-decoration:none;
    color:#44403C;
    padding:11px 16px;
    border-radius:12px;
    font-size:14px;
    font-weight:bold;
    transition:.3s;
}

.teacher-menu a:hover,
.teacher-menu .active{
    background:linear-gradient(90deg,#FF6B00,#FFB000);
    color:white;
    transform:translateY(-2px);
}

.teacher-menu .logout{
    color:#DC2626;
}

.teacher-menu .logout:hover{
    background:#DC2626;
    color:white;
}


/* MAIN */

.container{
    width:94%;
    max-width:1200px;
    margin:30px auto 60px;
}

.page-title{
    margin-bottom:25px;
}

.page-title h1{
    font-size:32px;
    color:#1C1917;
}

.page-title p{
    margin-top:7px;
    color:#78716C;
}


/* DATE SELECT */

.date-box{
    background:white;
    padding:20px;
    border-radius:18px;
    box-shadow:0 8px 25px rgba(0,0,0,.07);
    margin-bottom:25px;

    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:15px;
    flex-wrap:wrap;
}

.date-box label{
    font-weight:bold;
}

.date-box input{
    padding:11px 14px;
    border:1px solid #ddd;
    border-radius:10px;
    font-size:15px;
}

.view-btn{
    border:none;
    background:linear-gradient(90deg,#FF6B00,#FFB000);
    color:white;
    padding:11px 20px;
    border-radius:10px;
    font-weight:bold;
    cursor:pointer;
}


/* ATTENDANCE FORM */

.attendance-card{
    background:white;
    border-radius:20px;
    padding:20px;
    box-shadow:0 8px 25px rgba(0,0,0,.07);
    overflow:hidden;
}

.attendance-card h2{
    margin-bottom:18px;
}

table{
    width:100%;
    border-collapse:collapse;
}

th{
    background:#1C1917;
    color:white;
    padding:14px;
    text-align:left;
}

td{
    padding:13px;
    border-bottom:1px solid #eee;
}

tr:hover{
    background:#FFF7ED;
}

.student-photo{
    width:45px;
    height:45px;
    border-radius:50%;
    object-fit:cover;
}

.status-buttons{
    display:flex;
    gap:8px;
}

.status-buttons input{
    display:none;
}

.status-buttons label{
    padding:8px 14px;
    border-radius:8px;
    cursor:pointer;
    font-size:13px;
    font-weight:bold;
    background:#f3f4f6;
}

.status-buttons input:checked + .present{
    background:#16A34A;
    color:white;
}

.status-buttons input:checked + .absent{
    background:#DC2626;
    color:white;
}

.save-btn{
    margin-top:20px;
    border:none;
    background:linear-gradient(90deg,#FF6B00,#FFB000);
    color:white;
    padding:13px 25px;
    border-radius:10px;
    font-weight:bold;
    cursor:pointer;
    font-size:15px;
}


/* HISTORY */

.history{
    margin-top:35px;
}

.history h2{
    margin-bottom:15px;
}

.history-card{
    background:white;
    border-radius:18px;
    overflow:hidden;
    box-shadow:0 8px 25px rgba(0,0,0,.07);
}

.date-link{
    text-decoration:none;
    font-weight:bold;
    color:#FF6B00;
}

.badge{
    display:inline-block;
    padding:6px 10px;
    border-radius:20px;
    font-size:12px;
    font-weight:bold;
}

.present-badge{
    background:#DCFCE7;
    color:#15803D;
}

.absent-badge{
    background:#FEE2E2;
    color:#DC2626;
}


/* FOOTER */

.footer{
    background:#1C1917;
    color:#ddd;
    text-align:center;
    padding:35px 15px;
}

.footer h3{
    color:white;
    margin-bottom:10px;
}

.footer p{
    margin:6px;
    font-size:14px;
}


/* MOBILE */

@media(max-width:700px){

    .top-header{
        flex-direction:column;
        gap:15px;
    }

    .home-nav{
        flex-wrap:wrap;
        justify-content:center;
    }

    .container{
        width:96%;
    }

    .attendance-card,
    .history-card{
        overflow-x:auto;
    }

    table{
        min-width:750px;
    }

}

</style>

</head>

<body>


<!-- HEADER -->

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


<!-- TEACHER MENU -->

<nav class="teacher-menu">

    <a href="dashboard.php">🏠 Dashboard</a>

    <a href="students.php">👨‍🎓 Students</a>

    <a href="attendance.php" class="active">
        📅 Attendance
    </a>

    <a href="result.php">📊 Result</a>

    <a href="course.php">📚 Course</a>

    <a href="profile.php">👤 My Profile</a>

    <a href="change_password.php">🔑 Password</a>

    <a href="logout.php"
       class="logout"
       onclick="return confirm('Are you sure you want to logout?')">
       🚪 Logout
    </a>

</nav>


<div class="container">


    <div class="page-title">

        <h1>📅 Attendance</h1>

        <p>
            Manage attendance for
            <strong><?php echo htmlspecialchars($selected_course); ?> — Semester <?php echo htmlspecialchars($selected_semester); ?></strong> students only.
        </p>

    </div>


    <!-- DATE SELECT -->

    <form method="GET" class="date-box">

        <div>

            <label>
                📅 Select Attendance Date
            </label>

            <input
                type="date"
                name="date"
                value="<?php echo $selected_date; ?>"
                required
            >

        </div>

        <button class="view-btn">
            View Attendance
        </button>

    </form>


    <!-- ATTENDANCE FORM -->

    <form method="POST">

        <input
            type="hidden"
            name="attendance_date"
            value="<?php echo $selected_date; ?>"
        >

        <div class="attendance-card">

            <h2>
                Attendance for
                <?php echo date("d M Y",strtotime($selected_date)); ?>
            </h2>


            <table>

                <tr>

                    <th>#</th>
                    <th>Student</th>
                    <th>Course</th>
                    <th>Semester</th>
                    <th>Status</th>

                </tr>


                <?php

                $i = 1;

                while($student = mysqli_fetch_assoc($students))
                {

                    $sid = $student['id'];

                    $current_status =
                        isset($attendance_data[$sid])
                        ? $attendance_data[$sid]
                        : "";

                ?>

                <tr>

                    <td>
                        <?php echo $i++; ?>
                    </td>

                    <td>

                        <div style="display:flex;align-items:center;gap:10px;">

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
                                echo '<div class="student-photo"
                                    style="background:#FFEDD5;
                                    display:flex;
                                    align-items:center;
                                    justify-content:center;">
                                    👤
                                </div>';
                            }

                            ?>

                            <strong>
                                <?php echo ucwords(strtolower($student['name'])); ?>
                            </strong>

                        </div>

                    </td>

                    <td>
                        <?php echo $student['course']; ?>
                    </td>

                    <td>
                        <?php echo $student['semester']; ?>
                    </td>

                    <td>

                        <div class="status-buttons">

                            <input
                                type="radio"
                                id="present_<?php echo $sid; ?>"
                                name="status[<?php echo $sid; ?>]"
                                value="Present"
                                <?php
                                if($current_status=="Present")
                                echo "checked";
                                ?>
                                required
                            >

                            <label
                                for="present_<?php echo $sid; ?>"
                                class="present"
                            >
                                Present
                            </label>


                            <input
                                type="radio"
                                id="absent_<?php echo $sid; ?>"
                                name="status[<?php echo $sid; ?>]"
                                value="Absent"
                                <?php
                                if($current_status=="Absent")
                                echo "checked";
                                ?>
                            >

                            <label
                                for="absent_<?php echo $sid; ?>"
                                class="absent"
                            >
                                Absent
                            </label>

                        </div>

                    </td>

                </tr>

                <?php } ?>

            </table>


            <button
                type="submit"
                name="save"
                class="save-btn"
            >
                💾 Save Attendance
            </button>

        </div>

    </form>


    <!-- DATE WISE HISTORY -->

    <div class="history">

        <h2>📋 Attendance History</h2>

        <div class="history-card">

            <table>

                <tr>

                    <th>Date</th>
                    <th>Total Students</th>
                    <th>Present</th>
                    <th>Absent</th>
                    <th>View</th>

                </tr>

                <?php

                if(mysqli_num_rows($history) > 0)
                {

                    while($h = mysqli_fetch_assoc($history))
                    {

                ?>

                <tr>

                    <td>
                        <strong>
                            <?php
                            echo date(
                                "d M Y",
                                strtotime($h['attendance_date'])
                            );
                            ?>
                        </strong>
                    </td>

                    <td>
                        <?php echo $h['total']; ?>
                    </td>

                    <td>

                        <span class="badge present-badge">
                            <?php echo $h['present']; ?> Present
                        </span>

                    </td>

                    <td>

                        <span class="badge absent-badge">
                            <?php echo $h['absent']; ?> Absent
                        </span>

                    </td>

                    <td>

                        <a
                            class="date-link"
                            href="attendance.php?date=<?php
                            echo $h['attendance_date'];
                            ?>"
                        >
                            View →
                        </a>

                    </td>

                </tr>

                <?php

                    }

                }
                else
                {

                ?>

                <tr>

                    <td colspan="5" style="text-align:center;padding:30px;">
                        No attendance history available.
                    </td>

                </tr>

                <?php } ?>

            </table>

        </div>

    </div>


</div>


<footer class="footer">

    <h3>EduTrack</h3>

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