<?php

session_start();

if(!isset($_SESSION['admin']))
{
    header("Location:../login.php");
    exit();
}

include "../db.php";


/* =====================================================
   SELECTED STUDENT
===================================================== */

$selected_student = "";

if(isset($_GET['student_id']))
{
    $selected_student = mysqli_real_escape_string(
        $conn,
        $_GET['student_id']
    );
}


/* =====================================================
   STUDENT LIST
===================================================== */

$students = mysqli_query($conn,"
    SELECT id,name
    FROM students
    ORDER BY name ASC
");


/* =====================================================
   OVERALL STUDENT RESULT
   All subjects total for each student
===================================================== */

$overall_result = mysqli_query($conn,"
    SELECT
        students.id AS student_id,
        students.name,

        SUM(result.marks) AS total_marks,

        SUM(result.total) AS total_possible,

        COUNT(result.id) AS total_subjects

    FROM result

    INNER JOIN students
    ON result.student_id = students.id

    GROUP BY students.id, students.name

    ORDER BY students.name ASC
");


/* =====================================================
   SELECTED STUDENT SUBJECT-WISE RESULT
===================================================== */

$subject_result = false;

$selected_student_name = "";

if($selected_student != "")
{

    $student_info = mysqli_query($conn,"
        SELECT name
        FROM students
        WHERE id='$selected_student'
    ");

    $student_data = mysqli_fetch_assoc($student_info);

    if($student_data)
    {
        $selected_student_name = $student_data['name'];
    }


    $subject_result = mysqli_query($conn,"
        SELECT *
        FROM result
        WHERE student_id='$selected_student'

        ORDER BY id ASC
    ");

}

?>

<!DOCTYPE html>

<html>

<head>

<title>Result Report - EduTrack</title>

<meta name="viewport" content="width=device-width, initial-scale=1">


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


/* =====================================================
   SIDEBAR
===================================================== */

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
    background:linear-gradient(90deg,#FF6B00,#FFB000);
    color:white;
    box-shadow:0 7px 20px rgba(255,107,0,.25);
}

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


/* =====================================================
   MAIN
===================================================== */

.main{
    margin-left:250px;
    min-height:100vh;
    padding:0 30px 40px;
}


/* =====================================================
   TOPBAR
===================================================== */

.topbar{
    height:80px;
    display:flex;
    align-items:center;
    justify-content:space-between;
    background:white;
    margin:0 -30px 30px;
    padding:0 30px;
    border-bottom:1px solid #E7E5E4;
}

.topbar-left h1{
    font-size:24px;
    color:#292524;
    margin-bottom:4px;
}

.topbar-left p{
    color:#78716C;
    font-size:13px;
}

.admin-profile{
    display:flex;
    align-items:center;
    gap:12px;
}

.admin-info{
    text-align:right;
}

.admin-info strong{
    display:block;
    font-size:14px;
    color:#292524;
}

.admin-info span{
    display:block;
    font-size:12px;
    color:#78716C;
    margin-top:3px;
}

.avatar{
    width:44px;
    height:44px;
    border-radius:12px;
    background:linear-gradient(135deg,#FF6B00,#FFB000);
    display:flex;
    align-items:center;
    justify-content:center;
    color:white;
    font-size:22px;
    box-shadow:0 5px 15px rgba(255,107,0,.20);
}


/* =====================================================
   PAGE HEADER
===================================================== */

.page-header{
    display:flex;
    align-items:center;
    justify-content:space-between;
    margin-bottom:25px;
}

.page-title h2{
    font-size:25px;
    color:#292524;
    margin-bottom:5px;
}

.page-title p{
    color:#78716C;
    font-size:13px;
}


/* =====================================================
   PRINT BUTTON
===================================================== */

.print-btn{
    display:inline-flex;
    align-items:center;
    gap:8px;
    padding:12px 18px;
    background:linear-gradient(90deg,#FF6B00,#FFB000);
    color:white;
    border:none;
    border-radius:10px;
    font-size:14px;
    font-weight:bold;
    cursor:pointer;
    box-shadow:0 6px 18px rgba(255,107,0,.20);
    transition:.3s;
}

.print-btn:hover{
    transform:translateY(-2px);
    box-shadow:0 9px 22px rgba(255,107,0,.30);
}


/* =====================================================
   SECTION
===================================================== */

.section{
    background:white;
    border:1px solid #E7E5E4;
    border-radius:18px;
    box-shadow:0 8px 30px rgba(0,0,0,.06);
    overflow:hidden;
    margin-bottom:25px;
}

.section-header{
    padding:20px 22px;
    border-bottom:1px solid #E7E5E4;
    display:flex;
    align-items:center;
    justify-content:space-between;
}

.section-title{
    display:flex;
    align-items:center;
    gap:12px;
}

.section-icon{
    width:44px;
    height:44px;
    border-radius:12px;
    background:#FFF7ED;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:21px;
}

.section-title h3{
    font-size:17px;
    color:#292524;
}

.section-title p{
    font-size:11px;
    color:#78716C;
    margin-top:3px;
}

.section-number{
    background:#292524;
    color:#FFF7ED;
    padding:7px 11px;
    border-radius:20px;
    font-size:11px;
    font-weight:bold;
}


/* =====================================================
   TABLE
===================================================== */

.table-wrapper{
    width:100%;
    overflow-x:auto;
}

table{
    width:100%;
    border-collapse:collapse;
    min-width:800px;
}

table th{
    background:#292524;
    color:#FFF7ED;
    padding:15px 12px;
    font-size:11px;
    text-transform:uppercase;
    letter-spacing:.4px;
    white-space:nowrap;
}

table td{
    padding:14px 12px;
    text-align:center;
    border-bottom:1px solid #E7E5E4;
    font-size:13px;
    color:#44403C;
}

table tbody tr{
    transition:.2s;
}

table tbody tr:hover{
    background:#FFF7ED;
}

table tbody tr:last-child td{
    border-bottom:none;
}


/* =====================================================
   STUDENT NAME
===================================================== */

.student-name{
    font-weight:bold;
    color:#292524;
}

.student-id{
    color:#FF6B00;
    font-weight:bold;
}


/* =====================================================
   SUBJECT
===================================================== */

.subject{
    font-weight:600;
    color:#57534E;
}


/* =====================================================
   MARKS
===================================================== */

.marks{
    font-weight:bold;
    color:#292524;
}

.total{
    color:#78716C;
}


/* =====================================================
   PERCENTAGE
===================================================== */

.percentage{
    display:inline-block;
    padding:6px 11px;
    background:#FFF7ED;
    color:#EA580C;
    border-radius:20px;
    font-size:11px;
    font-weight:bold;
}


/* =====================================================
   GRADE
===================================================== */

.grade{
    display:inline-block;
    min-width:38px;
    padding:6px 9px;
    background:#F5F5F4;
    color:#57534E;
    border-radius:8px;
    font-size:11px;
    font-weight:bold;
}


/* =====================================================
   STATUS
===================================================== */

.status-pass{
    display:inline-block;
    padding:6px 11px;
    background:#F0FDF4;
    color:#16A34A;
    border-radius:20px;
    font-size:11px;
    font-weight:bold;
}

.status-fail{
    display:inline-block;
    padding:6px 11px;
    background:#FEF2F2;
    color:#DC2626;
    border-radius:20px;
    font-size:11px;
    font-weight:bold;
}


/* =====================================================
   VIEW BUTTON
===================================================== */

.view-btn{
    display:inline-block;
    padding:8px 13px;
    background:#FFF7ED;
    color:#EA580C;
    border:1px solid #FED7AA;
    border-radius:8px;
    text-decoration:none;
    font-size:12px;
    font-weight:bold;
    transition:.3s;
}

.view-btn:hover{
    background:#FF6B00;
    color:white;
}


/* =====================================================
   SELECT STUDENT
===================================================== */

.select-box{
    padding:20px 22px;
    background:#FFF7ED;
    border-bottom:1px solid #FED7AA;
}

.select-box form{
    display:flex;
    align-items:center;
    gap:12px;
}

.select-box label{
    font-size:13px;
    font-weight:bold;
    color:#44403C;
}

.select-box select{
    width:300px;
    padding:11px 13px;
    border:1px solid #D6D3D1;
    border-radius:9px;
    background:white;
    color:#292524;
    outline:none;
    font-size:13px;
}

.select-box select:focus{
    border-color:#FF8A00;
    box-shadow:0 0 0 3px rgba(255,138,0,.10);
}

.show-btn{
    padding:11px 18px;
    background:linear-gradient(90deg,#FF6B00,#FFB000);
    color:white;
    border:none;
    border-radius:9px;
    font-size:13px;
    font-weight:bold;
    cursor:pointer;
}


/* =====================================================
   SELECTED STUDENT HEADER
===================================================== */

.selected-student{
    padding:18px 22px;
    background:#FAFAF9;
    border-bottom:1px solid #E7E5E4;
    display:flex;
    align-items:center;
    gap:12px;
}

.selected-icon{
    width:45px;
    height:45px;
    border-radius:12px;
    background:linear-gradient(135deg,#FF6B00,#FFB000);
    display:flex;
    align-items:center;
    justify-content:center;
    color:white;
    font-size:21px;
}

.selected-student strong{
    display:block;
    color:#292524;
    font-size:16px;
}

.selected-student span{
    display:block;
    color:#78716C;
    font-size:11px;
    margin-top:3px;
}


/* =====================================================
   EMPTY
===================================================== */

.empty{
    padding:45px 20px;
    text-align:center;
    color:#78716C;
}

.empty-icon{
    font-size:40px;
    margin-bottom:10px;
}

.empty h3{
    color:#44403C;
    margin-bottom:5px;
}

.empty p{
    font-size:13px;
}


/* =====================================================
   RESPONSIVE
===================================================== */

@media(max-width:900px){

    .sidebar{
        width:220px;
    }

    .main{
        margin-left:220px;
        padding:0 20px 30px;
    }

    .topbar{
        margin:0 -20px 25px;
        padding:0 20px;
    }

}

@media(max-width:700px){

    .sidebar{
        position:relative;
        width:100%;
        height:auto;
    }

    .main{
        margin-left:0;
        padding:20px;
    }

    .topbar{
        margin:0 -20px 25px;
        padding:0 20px;
    }

    .admin-info{
        display:none;
    }

    .page-header{
        display:block;
    }

    .print-btn{
        margin-top:15px;
    }

    .select-box form{
        flex-direction:column;
        align-items:stretch;
    }

    .select-box select{
        width:100%;
    }

}


/* =====================================================
   PRINT
===================================================== */

@media print{

    body{
        background:white;
    }

    .sidebar,
    .topbar,
    .page-header,
    .select-box,
    .view-btn{
        display:none !important;
    }

    .main{
        margin:0;
        padding:0;
    }

    .section{
        box-shadow:none;
        border:1px solid #ddd;
    }

}

</style>

</head>


<body>


<!-- =====================================================
     SIDEBAR
===================================================== -->

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

            <a href="course.php">

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

            <a href="result_report.php" class="active">

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



<!-- =====================================================
     MAIN
===================================================== -->

<div class="main">


    <!-- TOPBAR -->

    <div class="topbar">


        <div class="topbar-left">

            <h1>
                Result Report
            </h1>

            <p>
                View overall and subject-wise student performance
            </p>

        </div>


        <div class="admin-profile">


            <div class="admin-info">

                <strong>
                    Administrator
                </strong>

                <span>
                    System Admin
                </span>

            </div>


            <div class="avatar">
                👨‍💼
            </div>


        </div>


    </div>



    <!-- PAGE HEADER -->

    <div class="page-header">


        <div class="page-title">

            <h2>
                Academic Result Report
            </h2>

            <p>
                Overall student performance and detailed subject results
            </p>

        </div>


        <button
            class="print-btn"
            onclick="window.print()"
        >

            🖨 Print Report

        </button>


    </div>



    <!-- =================================================
         SECTION 1 : OVERALL RESULT
    ================================================== -->

    <div class="section">


        <div class="section-header">


            <div class="section-title">


                <div class="section-icon">
                    📊
                </div>


                <div>

                    <h3>
                        Overall Student Result
                    </h3>

                    <p>
                        Total performance of all subjects
                    </p>

                </div>


            </div>


            <div class="section-number">
                SECTION 1
            </div>


        </div>



        <?php

        if(mysqli_num_rows($overall_result) > 0)
        {

        ?>


        <div class="table-wrapper">

        <table>


            <thead>

                <tr>

                    <th>
                        ID
                    </th>

                    <th>
                        Student Name
                    </th>

                    <th>
                        Subjects
                    </th>

                    <th>
                        Total Marks
                    </th>

                    <th>
                        Total
                    </th>

                    <th>
                        Overall %
                    </th>

                    <th>
                        Grade
                    </th>

                    <th>
                        Status
                    </th>

                    <th>
                        View
                    </th>

                </tr>

            </thead>


            <tbody>


            <?php

            $i = 1;


            while($row=mysqli_fetch_assoc($overall_result))
            {

                $total_marks = $row['total_marks'];
                $total_possible = $row['total_possible'];

                if($total_possible > 0)
                {
                    $overall_percentage =
                        ($total_marks / $total_possible) * 100;
                }
                else
                {
                    $overall_percentage = 0;
                }


                /* ================= GRADE ================= */

                if($overall_percentage >= 90)
                {
                    $grade = "A+";
                }
                elseif($overall_percentage >= 80)
                {
                    $grade = "A";
                }
                elseif($overall_percentage >= 70)
                {
                    $grade = "B+";
                }
                elseif($overall_percentage >= 60)
                {
                    $grade = "B";
                }
                elseif($overall_percentage >= 50)
                {
                    $grade = "C";
                }
                elseif($overall_percentage >= 40)
                {
                    $grade = "D";
                }
                else
                {
                    $grade = "F";
                }


                /* ================= STATUS ================= */

                if($overall_percentage >= 40)
                {
                    $status = "Pass";
                }
                else
                {
                    $status = "Fail";
                }

            ?>


            <tr>


                <td>

                    <span class="student-id">

                        #<?php echo $i++; ?>

                    </span>

                </td>


                <td>

                    <span class="student-name">

                        <?php
                        echo htmlspecialchars($row['name']);
                        ?>

                    </span>

                </td>


                <td>

                    <?php
                    echo $row['total_subjects'];
                    ?>

                </td>


                <td>

                    <span class="marks">

                        <?php
                        echo $total_marks;
                        ?>

                    </span>

                </td>


                <td>

                    <span class="total">

                        <?php
                        echo $total_possible;
                        ?>

                    </span>

                </td>


                <td>

                    <span class="percentage">

                        <?php
                        echo number_format(
                            $overall_percentage,
                            2
                        );
                        ?>%

                    </span>

                </td>


                <td>

                    <span class="grade">

                        <?php
                        echo $grade;
                        ?>

                    </span>

                </td>


                <td>

                    <?php

                    if($status=="Pass")
                    {

                    ?>

                        <span class="status-pass">
                            ✓ Pass
                        </span>

                    <?php

                    }
                    else
                    {

                    ?>

                        <span class="status-fail">
                            ✕ Fail
                        </span>

                    <?php

                    }

                    ?>

                </td>


                <td>

                    <a
                        class="view-btn"
                        href="result_report.php?student_id=<?php echo $row['student_id']; ?>"
                    >

                        View Subjects

                    </a>

                </td>


            </tr>


            <?php

            }

            ?>


            </tbody>


        </table>

        </div>


        <?php

        }
        else
        {

        ?>


        <div class="empty">

            <div class="empty-icon">
                📊
            </div>

            <h3>
                No Results Found
            </h3>

            <p>
                No student result has been added yet.
            </p>

        </div>


        <?php

        }

        ?>


    </div>



    <!-- =================================================
         SECTION 2 : SUBJECT WISE RESULT
    ================================================== -->

    <div class="section">


        <div class="section-header">


            <div class="section-title">


                <div class="section-icon">
                    📚
                </div>


                <div>

                    <h3>
                        Subject-wise Result
                    </h3>

                    <p>
                        Select a student to view all subjects
                    </p>

                </div>


            </div>


            <div class="section-number">
                SECTION 2
            </div>


        </div>



        <!-- STUDENT SELECT -->

        <div class="select-box">


            <form
                method="GET"
                action="result_report.php"
            >


                <label>
                    Select Student:
                </label>


                <select
                    name="student_id"
                    required
                >

                    <option value="">
                        -- Select Student --
                    </option>


                    <?php

                    mysqli_data_seek($students,0);


                    while($student=mysqli_fetch_assoc($students))
                    {

                    ?>

                    <option
                        value="<?php echo $student['id']; ?>"
                        <?php

                        if(
                            $selected_student ==
                            $student['id']
                        )
                        {
                            echo "selected";
                        }

                        ?>
                    >

                        <?php
                        echo htmlspecialchars(
                            $student['name']
                        );
                        ?>

                    </option>

                    <?php

                    }

                    ?>

                </select>


                <button
                    type="submit"
                    class="show-btn"
                >

                    View Result

                </button>


            </form>


        </div>



        <?php

        if(
            $selected_student != "" &&
            $selected_student_name != ""
        )
        {

        ?>


        <!-- SELECTED STUDENT -->

        <div class="selected-student">


            <div class="selected-icon">
                👨‍🎓
            </div>


            <div>

                <strong>

                    <?php
                    echo htmlspecialchars(
                        $selected_student_name
                    );
                    ?>

                </strong>

                <span>
                    Subject-wise Academic Result
                </span>

            </div>


        </div>



        <?php

        if(
            $subject_result &&
            mysqli_num_rows($subject_result) > 0
        )
        {

        ?>


        <div class="table-wrapper">

        <table>


            <thead>

                <tr>

                    <th>
                        #
                    </th>

                    <th>
                        Subject
                    </th>

                    <th>
                        Marks
                    </th>

                    <th>
                        Total
                    </th>

                    <th>
                        Percentage
                    </th>

                    <th>
                        Grade
                    </th>

                    <th>
                        Status
                    </th>

                </tr>

            </thead>


            <tbody>


            <?php

            $subject_no = 1;


            while($subject=mysqli_fetch_assoc($subject_result))
            {

                $marks = $subject['marks'];
                $total = $subject['total'];


                if($total > 0)
                {
                    $percentage =
                        ($marks / $total) * 100;
                }
                else
                {
                    $percentage = 0;
                }


                /* ================= GRADE ================= */

                if($percentage >= 90)
                {
                    $grade = "A+";
                }
                elseif($percentage >= 80)
                {
                    $grade = "A";
                }
                elseif($percentage >= 70)
                {
                    $grade = "B+";
                }
                elseif($percentage >= 60)
                {
                    $grade = "B";
                }
                elseif($percentage >= 50)
                {
                    $grade = "C";
                }
                elseif($percentage >= 40)
                {
                    $grade = "D";
                }
                else
                {
                    $grade = "F";
                }


                /* ================= STATUS ================= */

                if($percentage >= 40)
                {
                    $status = "Pass";
                }
                else
                {
                    $status = "Fail";
                }

            ?>


            <tr>


                <td>

                    <span class="student-id">

                        <?php
                        echo $subject_no++;
                        ?>

                    </span>

                </td>


                <td>

                    <span class="subject">

                        <?php
                        echo htmlspecialchars(
                            $subject['subject']
                        );
                        ?>

                    </span>

                </td>


                <td>

                    <span class="marks">

                        <?php
                        echo $marks;
                        ?>

                    </span>

                </td>


                <td>

                    <span class="total">

                        <?php
                        echo $total;
                        ?>

                    </span>

                </td>


                <td>

                    <span class="percentage">

                        <?php
                        echo number_format(
                            $percentage,
                            2
                        );
                        ?>%

                    </span>

                </td>


                <td>

                    <span class="grade">

                        <?php
                        echo $grade;
                        ?>

                    </span>

                </td>


                <td>


                    <?php

                    if($status=="Pass")
                    {

                    ?>

                        <span class="status-pass">
                            ✓ Pass
                        </span>

                    <?php

                    }
                    else
                    {

                    ?>

                        <span class="status-fail">
                            ✕ Fail
                        </span>

                    <?php

                    }

                    ?>


                </td>


            </tr>


            <?php

            }

            ?>


            </tbody>


        </table>

        </div>


        <?php

        }
        else
        {

        ?>


        <div class="empty">

            <div class="empty-icon">
                📚
            </div>

            <h3>
                No Subject Results
            </h3>

            <p>
                No result has been added for this student.
            </p>

        </div>


        <?php

        }

        ?>


        <?php

        }
        else
        {

        ?>


        <div class="empty">

            <div class="empty-icon">
                👨‍🎓
            </div>

            <h3>
                Select a Student
            </h3>

            <p>
                Select a student above to view subject-wise results.
            </p>

        </div>


        <?php

        }

        ?>


    </div>



</div>


</body>

</html>