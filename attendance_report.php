<?php

session_start();

if(!isset($_SESSION['admin']))
{
    header("Location:../login.php");
    exit();
}

include "../db.php";


/* =========================================
   DATE FILTER
========================================= */

$date = "";

if(isset($_GET['date']) && $_GET['date'] != "")
{
    $date = mysqli_real_escape_string(
        $conn,
        $_GET['date']
    );
}


/* =========================================
   ATTENDANCE QUERY
========================================= */

if($date != "")
{

    $result = mysqli_query($conn,"
        SELECT
            attendance.*,
            students.name,
            students.course,
            students.semester,
            students.photo

        FROM attendance

        INNER JOIN students
        ON attendance.student_id = students.id

        WHERE attendance.attendance_date='$date'

        ORDER BY students.name ASC
    ");

}
else
{

    $result = mysqli_query($conn,"
        SELECT
            attendance.*,
            students.name,
            students.course,
            students.semester,
            students.photo

        FROM attendance

        INNER JOIN students
        ON attendance.student_id = students.id

        ORDER BY
            attendance.attendance_date DESC,
            students.name ASC
    ");

}


/* =========================================
   STATISTICS
========================================= */

$total = 0;
$present = 0;
$absent = 0;

$report_data = array();


while($row = mysqli_fetch_assoc($result))
{

    $report_data[] = $row;

    $total++;

    if($row['status'] == "Present")
    {
        $present++;
    }
    else
    {
        $absent++;
    }

}


$percentage = 0;

if($total > 0)
{
    $percentage =
        round(
            ($present / $total) * 100
        );
}

?>

<!DOCTYPE html>

<html>

<head>

<title>EduTrack | Attendance Report</title>

<meta
    name="viewport"
    content="width=device-width, initial-scale=1"
>


<style>

/* =========================================
   RESET
========================================= */

*{
    margin:0;
    padding:0;
    box-sizing:border-box;

    font-family:
        Arial,
        Helvetica,
        sans-serif;
}

body{

    background:#F7F7F5;

    color:#292524;
}


/* =========================================
   SIDEBAR
========================================= */

.sidebar{

    position:fixed;

    left:0;
    top:0;

    width:250px;
    height:100vh;

    background:#171412;

    color:white;

    padding:22px 15px;

    box-shadow:
        5px 0 25px
        rgba(0,0,0,.12);

    overflow-y:auto;

    z-index:1000;
}


/* LOGO */

.logo{

    padding:
        5px
        12px
        25px;

    border-bottom:
        1px solid #3A3531;

    margin-bottom:20px;
}

.logo a{

    color:white;

    text-decoration:none;

    font-size:27px;

    font-weight:bold;
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

    transform:
        translateX(4px);
}

.sidebar ul li a.active{

    background:
        linear-gradient(
            90deg,
            #FF6B00,
            #FFB000
        );

    color:white;

    box-shadow:
        0 7px 20px
        rgba(255,107,0,.25);
}


/* LOGOUT */

.sidebar .logout{

    margin-top:20px;

    padding-top:18px;

    border-top:
        1px solid #3A3531;
}

.sidebar .logout a{

    color:#FCA5A5;
}

.sidebar .logout a:hover{

    background:#7F1D1D;

    color:white;
}


/* =========================================
   MAIN
========================================= */

.main{

    margin-left:250px;

    min-height:100vh;

    padding:
        0
        30px
        40px;
}


/* =========================================
   TOPBAR
========================================= */

.topbar{

    height:82px;

    background:white;

    margin:
        0
        -30px
        30px;

    padding:
        0
        30px;

    display:flex;

    justify-content:space-between;

    align-items:center;

    border-bottom:
        1px solid #E7E5E4;
}

.top-title h1{

    font-size:25px;

    color:#1C1917;
}

.top-title p{

    margin-top:5px;

    color:#78716C;

    font-size:13px;
}


/* ADMIN */

.admin-area{

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
}

.admin-info span{

    display:block;

    color:#78716C;

    font-size:12px;

    margin-top:3px;
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

    font-size:20px;
}


/* =========================================
   PAGE HEADER
========================================= */

.page-header{

    display:flex;

    justify-content:space-between;

    align-items:center;

    gap:15px;

    flex-wrap:wrap;

    margin-bottom:25px;
}

.page-header h2{

    font-size:25px;

    color:#1C1917;
}

.page-header p{

    color:#78716C;

    font-size:13px;

    margin-top:6px;
}


/* =========================================
   FILTER
========================================= */

.filter-card{

    background:white;

    border:
        1px solid #E7E5E4;

    border-radius:18px;

    padding:20px;

    display:flex;

    align-items:end;

    justify-content:space-between;

    gap:15px;

    flex-wrap:wrap;

    box-shadow:
        0 5px 20px
        rgba(0,0,0,.04);

    margin-bottom:20px;
}

.filter-field{

    flex:1;

    min-width:230px;
}

.filter-field label{

    display:block;

    font-size:13px;

    font-weight:bold;

    color:#44403C;

    margin-bottom:8px;
}

.filter-field input{

    width:100%;

    max-width:320px;

    padding:12px 14px;

    border:
        1px solid #D6D3D1;

    border-radius:10px;

    background:#FAFAF9;

    outline:none;

    font-size:14px;
}

.filter-field input:focus{

    border-color:#FF8A00;

    box-shadow:
        0 0 0 3px
        rgba(255,138,0,.10);
}


/* BUTTONS */

.filter-actions{

    display:flex;

    gap:10px;
}

.search-btn{

    border:none;

    padding:
        12px
        20px;

    border-radius:10px;

    background:
        linear-gradient(
            90deg,
            #FF6B00,
            #FFB000
        );

    color:white;

    font-weight:bold;

    cursor:pointer;

    transition:.3s;
}

.search-btn:hover{

    transform:
        translateY(-2px);

    box-shadow:
        0 8px 20px
        rgba(255,107,0,.25);
}

.all-btn{

    text-decoration:none;

    padding:
        12px
        20px;

    border:
        1px solid #D6D3D1;

    border-radius:10px;

    color:#57534E;

    background:white;

    font-size:13px;

    font-weight:bold;
}

.all-btn:hover{

    border-color:#FF8A00;

    color:#FF6B00;
}


/* =========================================
   REPORT INFO
========================================= */

.report-info{

    background:
        linear-gradient(
            135deg,
            #1C1917,
            #292524
        );

    color:white;

    border-radius:18px;

    padding:20px 22px;

    display:flex;

    justify-content:space-between;

    align-items:center;

    gap:15px;

    flex-wrap:wrap;

    margin-bottom:20px;
}

.report-info h3{

    font-size:17px;

    margin-bottom:5px;
}

.report-info p{

    color:#D6D3D1;

    font-size:12px;
}

.report-date{

    background:
        rgba(255,138,0,.15);

    border:
        1px solid
        rgba(255,138,0,.35);

    color:#FFB000;

    padding:
        9px
        14px;

    border-radius:20px;

    font-size:12px;

    font-weight:bold;
}


/* =========================================
   STATISTICS
========================================= */

.stats{

    display:grid;

    grid-template-columns:
        repeat(4,1fr);

    gap:15px;

    margin-bottom:22px;
}

.stat-card{

    background:white;

    border:
        1px solid #E7E5E4;

    border-radius:16px;

    padding:19px;

    position:relative;

    overflow:hidden;

    box-shadow:
        0 5px 20px
        rgba(0,0,0,.04);

    transition:.3s;
}

.stat-card:hover{

    transform:
        translateY(-4px);

    box-shadow:
        0 12px 25px
        rgba(0,0,0,.08);
}

.stat-icon{

    width:40px;
    height:40px;

    border-radius:11px;

    background:#FFF1E6;

    display:flex;

    align-items:center;

    justify-content:center;

    font-size:18px;

    margin-bottom:12px;
}

.stat-card h3{

    font-size:24px;

    margin-bottom:4px;

    color:#1C1917;
}

.stat-card p{

    color:#78716C;

    font-size:12px;
}

.green{

    color:#16A34A !important;
}

.red{

    color:#DC2626 !important;
}

.orange{

    color:#FF6B00 !important;
}


/* =========================================
   TABLE CARD
========================================= */

.report-card{

    background:white;

    border:
        1px solid #E7E5E4;

    border-radius:20px;

    padding:22px;

    box-shadow:
        0 5px 20px
        rgba(0,0,0,.04);

    overflow:hidden;
}

.report-heading{

    display:flex;

    justify-content:space-between;

    align-items:center;

    margin-bottom:18px;

    gap:10px;

    flex-wrap:wrap;
}

.report-heading h3{

    font-size:18px;

    color:#1C1917;
}

.print-btn{

    border:none;

    background:#1C1917;

    color:white;

    padding:
        10px
        17px;

    border-radius:9px;

    cursor:pointer;

    font-size:12px;

    font-weight:bold;

    transition:.3s;
}

.print-btn:hover{

    background:#FF6B00;

    transform:
        translateY(-2px);
}


/* =========================================
   TABLE
========================================= */

.table-wrapper{

    overflow-x:auto;
}

table{

    width:100%;

    border-collapse:collapse;

    min-width:800px;
}

th{

    background:#1C1917;

    color:white;

    padding:14px;

    text-align:left;

    font-size:12px;
}

td{

    padding:13px;

    border-bottom:
        1px solid #EEEAE7;

    font-size:13px;
}

tbody tr{

    transition:.2s;
}

tbody tr:hover{

    background:#FFF7ED;
}


/* STUDENT */

.student-info{

    display:flex;

    align-items:center;

    gap:10px;
}

.student-photo{

    width:42px;
    height:42px;

    border-radius:10px;

    object-fit:cover;

    background:#FFF1E6;

    display:flex;

    align-items:center;

    justify-content:center;

    font-size:18px;
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


/* DATE */

.date-text{

    font-weight:bold;

    color:#44403C;
}


/* BADGES */

.status{

    display:inline-block;

    padding:
        7px
        12px;

    border-radius:20px;

    font-size:11px;

    font-weight:bold;
}

.present{

    background:#DCFCE7;

    color:#15803D;
}

.absent{

    background:#FEE2E2;

    color:#DC2626;
}


/* =========================================
   EMPTY
========================================= */

.empty{

    text-align:center;

    padding:50px 20px;

    color:#78716C;
}

.empty-icon{

    font-size:38px;

    margin-bottom:10px;
}

.empty h3{

    color:#44403C;

    margin-bottom:5px;
}


/* =========================================
   FOOTER
========================================= */

.footer{

    text-align:center;

    padding:
        30px
        10px
        5px;

    color:#78716C;

    font-size:12px;
}

.footer strong{

    color:#FF6B00;
}


/* =========================================
   PRINT
========================================= */

@media print{

    body{
        background:white;
    }

    .sidebar,
    .topbar,
    .filter-card,
    .print-btn,
    .footer{
        display:none !important;
    }

    .main{

        margin:0;

        padding:0;
    }

    .report-info{

        background:white;

        color:black;

        border:
            1px solid #ddd;
    }

    .report-info p{
        color:#555;
    }

    .report-date{

        color:black;

        border:
            1px solid #999;
    }

    .stats{

        grid-template-columns:
            repeat(4,1fr);
    }

    .stat-card{

        box-shadow:none;

        border:
            1px solid #ddd;
    }

    .report-card{

        box-shadow:none;

        border:
            1px solid #ddd;
    }

    th{

        background:#eee !important;

        color:black !important;
    }

}


/* =========================================
   RESPONSIVE
========================================= */

@media(max-width:1000px){

    .stats{

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

        padding-left:20px;

        padding-right:20px;
    }

}


@media(max-width:700px){

    .sidebar{

        position:relative;

        width:100%;

        height:auto;

        padding:15px;
    }

    .logo{

        text-align:center;
    }

    .sidebar ul{

        display:grid;

        grid-template-columns:
            repeat(2,1fr);

        gap:5px;
    }

    .main{

        margin-left:0;

        padding:
            0
            15px
            30px;
    }

    .topbar{

        margin:
            0
            -15px
            25px;

        padding:
            15px;

        min-height:70px;
    }

    .admin-info{
        display:none;
    }

    .filter-card{

        flex-direction:column;

        align-items:stretch;
    }

    .filter-field input{

        max-width:none;
    }

    .filter-actions{

        width:100%;
    }

    .search-btn,
    .all-btn{

        flex:1;

        text-align:center;
    }

}


@media(max-width:480px){

    .sidebar ul{

        grid-template-columns:1fr;
    }

    .stats{

        grid-template-columns:1fr;
    }

    .report-info{

        align-items:flex-start;
    }

}

</style>

</head>


<body>


<!-- =========================================
     SIDEBAR
========================================= -->

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

                <span>Dashboard</span>

            </a>

        </li>


        <li>

            <a href="add_student.php">

                ➕

                <span>Add Student</span>

            </a>

        </li>


        <li>

            <a href="manage_student.php">

                👨‍🎓

                <span>Manage Students</span>

            </a>

        </li>


        <li>

            <a href="attendance.php">

                📅

                <span>Attendance</span>

            </a>

        </li>


        <li>

            <a href="result.php">

                📊

                <span>Results</span>

            </a>

        </li>


        <li>

            <a href="course.php">

                📚

                <span>Courses</span>

            </a>

        </li>


        <li>

            <a
                href="attendance_report.php"
                class="active"
            >

                📄

                <span>Attendance Report</span>

            </a>

        </li>


        <li>

            <a href="result_report.php">

                📄

                <span>Result Report</span>

            </a>

        </li>


        <li>

            <a href="add_teacher.php">

                👨‍🏫

                <span>Add Teacher</span>

            </a>

        </li>


        <li>

            <a href="manage_teacher.php">

                📋

                <span>Manage Teachers</span>

            </a>

        </li>


        <li class="logout">

            <a
                href="logout.php"
                onclick="
                return confirm(
                    'Are you sure you want to logout?'
                )
                "
            >

                🚪

                <span>Logout</span>

            </a>

        </li>


    </ul>


</aside>



<!-- =========================================
     MAIN
========================================= -->

<main class="main">


    <!-- TOP BAR -->

    <header class="topbar">


        <div class="top-title">

            <h1>
                Attendance Report
            </h1>

            <p>
                View and print student attendance records.
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



    <!-- PAGE HEADER -->

    <div class="page-header">


        <div>

            <h2>
                📄 Attendance Report
            </h2>

            <p>
                Generate attendance reports by selecting a specific date.
            </p>

        </div>


    </div>



    <!-- =========================================
         FILTER
    ========================================= -->

    <form
        method="GET"
        class="filter-card"
    >


        <div class="filter-field">

            <label>
                📅 Select Attendance Date
            </label>

            <input
                type="date"
                name="date"
                value="<?php
                echo htmlspecialchars($date);
                ?>"
            >

        </div>


        <div class="filter-actions">


            <button
                type="submit"
                class="search-btn"
            >
                🔍 Generate Report
            </button>


            <a
                href="attendance_report.php"
                class="all-btn"
            >
                View All
            </a>


        </div>


    </form>



    <!-- =========================================
         REPORT INFO
    ========================================= -->

    <div class="report-info">


        <div>

            <h3>
                📊 Attendance Summary
            </h3>

            <p>

                <?php

                if($date != "")
                {

                    echo "Showing attendance for selected date.";

                }
                else
                {

                    echo "Showing all available attendance records.";

                }

                ?>

            </p>

        </div>


        <div class="report-date">

            <?php

            if($date != "")
            {

                echo "📅 ";

                echo date(
                    "d M Y",
                    strtotime($date)
                );

            }
            else
            {

                echo "📅 All Dates";

            }

            ?>

        </div>


    </div>



    <!-- =========================================
         STATISTICS
    ========================================= -->

    <section class="stats">


        <div class="stat-card">

            <div class="stat-icon">
                👨‍🎓
            </div>

            <h3>
                <?php echo $total; ?>
            </h3>

            <p>
                Total Records
            </p>

        </div>


        <div class="stat-card">

            <div class="stat-icon">
                🟢
            </div>

            <h3 class="green">
                <?php echo $present; ?>
            </h3>

            <p>
                Present
            </p>

        </div>


        <div class="stat-card">

            <div class="stat-icon">
                🔴
            </div>

            <h3 class="red">
                <?php echo $absent; ?>
            </h3>

            <p>
                Absent
            </p>

        </div>


        <div class="stat-card">

            <div class="stat-icon">
                📈
            </div>

            <h3 class="orange">
                <?php echo $percentage; ?>%
            </h3>

            <p>
                Attendance Rate
            </p>

        </div>


    </section>



    <!-- =========================================
         REPORT TABLE
    ========================================= -->

    <div class="report-card">


        <div class="report-heading">


            <h3>
                Student Attendance Details
            </h3>


            <button
                onclick="window.print()"
                class="print-btn"
            >
                🖨 Print Report
            </button>


        </div>



        <?php

        if(count($report_data) > 0)
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
                        Student
                    </th>

                    <th>
                        Course
                    </th>

                    <th>
                        Semester
                    </th>

                    <th>
                        Date
                    </th>

                    <th>
                        Status
                    </th>

                </tr>

                </thead>


                <tbody>


                <?php

                $i = 1;


                foreach($report_data as $row)
                {

                ?>


                <tr>


                    <td>

                        <?php
                        echo $i++;
                        ?>

                    </td>


                    <!-- STUDENT -->

                    <td>

                        <div class="student-info">


                            <?php

                            if(
                                !empty(
                                    $row['photo']
                                )
                            )
                            {

                            ?>

                                <img
                                    src="../uploads/<?php
                                    echo htmlspecialchars(
                                        $row['photo']
                                    );
                                    ?>"
                                    class="student-photo"
                                >

                            <?php

                            }
                            else
                            {

                            ?>

                                <div class="student-photo">
                                    👤
                                </div>

                            <?php

                            }


                            ?>


                            <div class="student-name">

                                <strong>

                                    <?php

                                    echo htmlspecialchars(
                                        ucwords(
                                            strtolower(
                                                $row['name']
                                            )
                                        )
                                    );

                                    ?>

                                </strong>

                                <span>
                                    Student ID:
                                    <?php
                                    echo $row['student_id'];
                                    ?>
                                </span>

                            </div>


                        </div>

                    </td>


                    <!-- COURSE -->

                    <td>

                        <?php

                        echo htmlspecialchars(
                            $row['course']
                        );

                        ?>

                    </td>


                    <!-- SEMESTER -->

                    <td>

                        <?php

                        echo htmlspecialchars(
                            $row['semester']
                        );

                        ?>

                    </td>


                    <!-- DATE -->

                    <td>

                        <span class="date-text">

                            <?php

                            echo date(
                                "d M Y",
                                strtotime(
                                    $row[
                                        'attendance_date'
                                    ]
                                )
                            );

                            ?>

                        </span>

                    </td>


                    <!-- STATUS -->

                    <td>


                        <?php

                        if(
                            $row['status']
                            ==
                            "Present"
                        )
                        {

                        ?>

                            <span
                                class="
                                    status
                                    present
                                "
                            >
                                ✓ Present
                            </span>

                        <?php

                        }
                        else
                        {

                        ?>

                            <span
                                class="
                                    status
                                    absent
                                "
                            >
                                ✕ Absent
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
                📅
            </div>


            <h3>
                No Attendance Records Found
            </h3>


            <p>

                <?php

                if($date != "")
                {

                    echo
                    "No attendance has been recorded for ".
                    date(
                        "d M Y",
                        strtotime($date)
                    ).
                    ".";

                }
                else
                {

                    echo
                    "Attendance records will appear here.";

                }

                ?>

            </p>


        </div>


        <?php

        }

        ?>


    </div>



    <!-- =========================================
         FOOTER
    ========================================= -->

    <footer class="footer">

        <p>

            <strong>EduTrack</strong>
            — Student Management System

        </p>

        <p>

            © 2026 EduTrack.
            All Rights Reserved.

        </p>

    </footer>


</main>

</body>

</html>