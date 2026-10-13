<?php

session_start();

if(!isset($_SESSION['admin']))
{
    header("Location:../login.php");
    exit();
}

include "../db.php";


/* =====================================================
   APPROVE REQUEST
===================================================== */

if(isset($_GET['approve']))
{
    $request_id = mysqli_real_escape_string(
        $conn,
        $_GET['approve']
    );


    $request_query = mysqli_query($conn,"
        SELECT *
        FROM student_requests
        WHERE id='$request_id'
        AND status='Pending'
    ");

    $request = mysqli_fetch_assoc($request_query);


    if($request)
    {

        $student_id = $request['student_id'];

        $type = $request['request_type'];

        $new_value = mysqli_real_escape_string(
            $conn,
            $request['new_value']
        );


        /* ================= UPDATE STUDENT DATA ================= */

        if($type == "Phone Number")
        {
            mysqli_query($conn,"
                UPDATE students
                SET phone='$new_value'
                WHERE id='$student_id'
            ");
        }

        elseif($type == "Email")
        {
            mysqli_query($conn,"
                UPDATE students
                SET email='$new_value'
                WHERE id='$student_id'
            ");
        }

        elseif($type == "Name")
        {
            mysqli_query($conn,"
                UPDATE students
                SET name='$new_value'
                WHERE id='$student_id'
            ");
        }

        elseif($type == "Course")
        {
            mysqli_query($conn,"
                UPDATE students
                SET course='$new_value'
                WHERE id='$student_id'
            ");
        }

        elseif($type == "Semester")
        {
            mysqli_query($conn,"
                UPDATE students
                SET semester='$new_value'
                WHERE id='$student_id'
            ");
        }


        /* ================= REQUEST STATUS ================= */

        mysqli_query($conn,"
            UPDATE student_requests
            SET
                status='Approved'
            WHERE id='$request_id'
        ");


        echo "<script>

            alert('Request Approved Successfully');

            window.location='student_requests.php';

        </script>";

        exit();

    }

}


/* =====================================================
   REJECT REQUEST
===================================================== */

if(isset($_GET['reject']))
{
    $request_id = mysqli_real_escape_string(
        $conn,
        $_GET['reject']
    );


    mysqli_query($conn,"
        UPDATE student_requests

        SET
            status='Rejected'

        WHERE
            id='$request_id'
            AND status='Pending'
    ");


    echo "<script>

        alert('Request Rejected');

        window.location='student_requests.php';

    </script>";

    exit();
}


/* =====================================================
   GET ALL REQUESTS
===================================================== */

$requests = mysqli_query($conn,"
    SELECT
        student_requests.*,
        students.name AS student_name,
        students.email AS student_email

    FROM student_requests

    INNER JOIN students
    ON student_requests.student_id = students.id

    ORDER BY student_requests.id DESC
");


/* =====================================================
   COUNTS
===================================================== */

$total_query = mysqli_query($conn,"
    SELECT COUNT(*) AS total
    FROM student_requests
");

$total_data = mysqli_fetch_assoc($total_query);

$total_requests = $total_data['total'];


$pending_query = mysqli_query($conn,"
    SELECT COUNT(*) AS total
    FROM student_requests
    WHERE status='Pending'
");

$pending_data = mysqli_fetch_assoc($pending_query);

$pending_requests = $pending_data['total'];


$approved_query = mysqli_query($conn,"
    SELECT COUNT(*) AS total
    FROM student_requests
    WHERE status='Approved'
");

$approved_data = mysqli_fetch_assoc($approved_query);

$approved_requests = $approved_data['total'];

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Student Requests - EduTrack</title>


<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    font-family:Arial,sans-serif;
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

    box-shadow:
        5px 0 25px rgba(0,0,0,.12);

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

    background:
        linear-gradient(
            90deg,
            #FF6B00,
            #FFB000
        );

    color:white;

    box-shadow:
        0 7px 20px rgba(255,107,0,.25);

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
   HEADER
===================================================== */

.top-header{

    height:85px;

    display:flex;

    align-items:center;

    justify-content:space-between;

    border-bottom:1px solid #E7E5E4;

}


.header-title h1{

    font-size:25px;

    margin-bottom:5px;

}


.header-title p{

    font-size:13px;

    color:#78716C;

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

    font-size:22px;

    box-shadow:
        0 5px 15px rgba(255,107,0,.20);

}


/* =====================================================
   PAGE TITLE
===================================================== */

.page-title{

    margin-top:30px;

    margin-bottom:25px;

}


.page-title h2{

    font-size:27px;

    margin-bottom:6px;

}


.page-title p{

    color:#78716C;

    font-size:13px;

}


/* =====================================================
   STATS
===================================================== */

.stats{

    display:grid;

    grid-template-columns:
        repeat(3,1fr);

    gap:20px;

    margin-bottom:25px;

}


.stat-card{

    background:white;

    border:1px solid #E7E5E4;

    border-radius:16px;

    padding:20px;

    display:flex;

    align-items:center;

    gap:15px;

    box-shadow:
        0 7px 25px rgba(0,0,0,.05);

}


.stat-icon{

    width:50px;

    height:50px;

    border-radius:13px;

    background:#FFF7ED;

    display:flex;

    align-items:center;

    justify-content:center;

    font-size:23px;

}


.stat-card h3{

    font-size:23px;

    margin-bottom:4px;

}


.stat-card p{

    font-size:12px;

    color:#78716C;

}


/* =====================================================
   CARD
===================================================== */

.card{

    background:white;

    border:1px solid #E7E5E4;

    border-radius:18px;

    overflow:hidden;

    box-shadow:
        0 8px 30px rgba(0,0,0,.06);

}


.card-header{

    padding:20px 22px;

    border-bottom:1px solid #E7E5E4;

    display:flex;

    align-items:center;

    justify-content:space-between;

}


.card-header h3{

    font-size:17px;

}


.card-header span{

    color:#78716C;

    font-size:12px;

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

    min-width:1050px;

}


th{

    background:#292524;

    color:#FFF7ED;

    padding:15px 12px;

    font-size:11px;

    text-transform:uppercase;

    letter-spacing:.3px;

}


td{

    padding:14px 12px;

    border-bottom:1px solid #E7E5E4;

    text-align:center;

    font-size:13px;

}


tr:hover{

    background:#FFF7ED;

}


/* =====================================================
   STUDENT
===================================================== */

.student-name{

    font-weight:bold;

    color:#292524;

}


.student-email{

    color:#78716C;

    font-size:11px;

    margin-top:4px;

}


/* =====================================================
   REQUEST TYPE
===================================================== */

.request-type{

    display:inline-block;

    padding:6px 10px;

    background:#FFF7ED;

    color:#EA580C;

    border-radius:20px;

    font-size:11px;

    font-weight:bold;

}


/* =====================================================
   VALUES
===================================================== */

.old-value{

    color:#78716C;

}


.new-value{

    color:#292524;

    font-weight:bold;

}


/* =====================================================
   REASON
===================================================== */

.reason{

    max-width:180px;

    margin:auto;

    color:#57534E;

    font-size:12px;

    line-height:1.4;

}


/* =====================================================
   STATUS
===================================================== */

.status{

    display:inline-block;

    padding:6px 11px;

    border-radius:20px;

    font-size:11px;

    font-weight:bold;

}


.pending{

    background:#FFF7ED;

    color:#EA580C;

}


.approved{

    background:#F0FDF4;

    color:#16A34A;

}


.rejected{

    background:#FEF2F2;

    color:#DC2626;

}


/* =====================================================
   ACTIONS
===================================================== */

.actions{

    display:flex;

    align-items:center;

    justify-content:center;

    gap:7px;

}


.approve-btn{

    display:inline-block;

    padding:8px 12px;

    border-radius:8px;

    background:#16A34A;

    color:white;

    text-decoration:none;

    font-size:11px;

    font-weight:bold;

    transition:.3s;

}


.approve-btn:hover{

    background:#15803D;

    transform:translateY(-2px);

}


.reject-btn{

    display:inline-block;

    padding:8px 12px;

    border-radius:8px;

    background:#DC2626;

    color:white;

    text-decoration:none;

    font-size:11px;

    font-weight:bold;

    transition:.3s;

}


.reject-btn:hover{

    background:#B91C1C;

    transform:translateY(-2px);

}


/* =====================================================
   EMPTY
===================================================== */

.empty{

    text-align:center;

    padding:60px 20px;

    color:#78716C;

}


.empty-icon{

    font-size:45px;

    margin-bottom:12px;

}


.empty h3{

    color:#292524;

    margin-bottom:6px;

}


/* =====================================================
   RESPONSIVE
===================================================== */

@media(max-width:900px){

    .stats{

        grid-template-columns:
            repeat(2,1fr);

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


    .top-header{

        height:auto;

        padding:15px 0;

    }


    .admin-info{

        display:none;

    }


    .stats{

        grid-template-columns:1fr;

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

            <a href="result_report.php">

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


        <!-- ================= STUDENT REQUESTS ================= -->

        <li>

            <a
                href="student_requests.php"
                class="active"
            >

                📝

                <span>
                    Student Requests
                </span>

            </a>

        </li>


        <!-- ================= LOGOUT ================= -->

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


    <!-- ================= HEADER ================= -->

    <div class="top-header">


        <div class="header-title">

            <h1>
                Student Requests
            </h1>

            <p>
                Review and manage student information change requests
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



    <!-- ================= PAGE TITLE ================= -->

    <div class="page-title">

        <h2>
            Student Change Requests
        </h2>

        <p>
            Approve or reject requests submitted by students
        </p>

    </div>



    <!-- ================= STATS ================= -->

    <div class="stats">


        <div class="stat-card">


            <div class="stat-icon">
                📝
            </div>


            <div>

                <h3>

                    <?php

                    echo $total_requests;

                    ?>

                </h3>

                <p>
                    Total Requests
                </p>

            </div>


        </div>



        <div class="stat-card">


            <div class="stat-icon">
                🟡
            </div>


            <div>

                <h3>

                    <?php

                    echo $pending_requests;

                    ?>

                </h3>

                <p>
                    Pending Requests
                </p>

            </div>


        </div>



        <div class="stat-card">


            <div class="stat-icon">
                ✓
            </div>


            <div>

                <h3>

                    <?php

                    echo $approved_requests;

                    ?>

                </h3>

                <p>
                    Approved Requests
                </p>

            </div>


        </div>


    </div>



    <!-- ================= REQUEST TABLE ================= -->

    <div class="card">


        <div class="card-header">

            <h3>
                All Student Requests
            </h3>

            <span>
                Latest requests first
            </span>

        </div>



        <div class="table-wrapper">


        <?php

        if(mysqli_num_rows($requests) > 0)
        {

        ?>


        <table>


            <tr>

                <th>
                    Student
                </th>

                <th>
                    Request Type
                </th>

                <th>
                    Current Value
                </th>

                <th>
                    New Value
                </th>

                <th>
                    Reason
                </th>

                <th>
                    Status
                </th>

                <th>
                    Action
                </th>

            </tr>


            <?php

            while($row=mysqli_fetch_assoc($requests))
            {

            ?>


            <tr>


                <!-- ================= STUDENT ================= -->

                <td>

                    <div class="student-name">

                        <?php

                        echo htmlspecialchars(
                            $row['student_name']
                        );

                        ?>

                    </div>


                    <div class="student-email">

                        <?php

                        echo htmlspecialchars(
                            $row['student_email']
                        );

                        ?>

                    </div>

                </td>



                <!-- ================= REQUEST TYPE ================= -->

                <td>

                    <span class="request-type">

                        <?php

                        echo htmlspecialchars(
                            $row['request_type']
                        );

                        ?>

                    </span>

                </td>



                <!-- ================= CURRENT ================= -->

                <td>

                    <span class="old-value">

                        <?php

                        echo htmlspecialchars(
                            $row['old_value']
                        );

                        ?>

                    </span>

                </td>



                <!-- ================= NEW ================= -->

                <td>

                    <span class="new-value">

                        <?php

                        echo htmlspecialchars(
                            $row['new_value']
                        );

                        ?>

                    </span>

                </td>



                <!-- ================= REASON ================= -->

                <td>

                    <div class="reason">

                        <?php

                        if(!empty($row['reason']))
                        {

                            echo htmlspecialchars(
                                $row['reason']
                            );

                        }
                        else
                        {

                            echo "No reason provided";

                        }

                        ?>

                    </div>

                </td>



                <!-- ================= STATUS ================= -->

                <td>


                    <?php

                    if($row['status']=="Pending")
                    {

                    ?>

                        <span class="status pending">

                            🟡 Pending

                        </span>

                    <?php

                    }
                    elseif($row['status']=="Approved")
                    {

                    ?>

                        <span class="status approved">

                            ✓ Approved

                        </span>

                    <?php

                    }
                    else
                    {

                    ?>

                        <span class="status rejected">

                            ✕ Rejected

                        </span>

                    <?php

                    }

                    ?>


                </td>



                <!-- ================= ACTION ================= -->

                <td>


                    <?php

                    if($row['status']=="Pending")
                    {

                    ?>


                    <div class="actions">


                        <a
                            href="student_requests.php?approve=<?php echo $row['id']; ?>"
                            class="approve-btn"
                            onclick="return confirm('Are you sure you want to approve this request?')"
                        >

                            ✓ Approve

                        </a>


                        <a
                            href="student_requests.php?reject=<?php echo $row['id']; ?>"
                            class="reject-btn"
                            onclick="return confirm('Are you sure you want to reject this request?')"
                        >

                            ✕ Reject

                        </a>


                    </div>


                    <?php

                    }
                    else
                    {

                        echo "-";

                    }

                    ?>


                </td>


            </tr>


            <?php

            }

            ?>


        </table>


        <?php

        }
        else
        {

        ?>


        <div class="empty">

            <div class="empty-icon">
                📋
            </div>

            <h3>
                No Student Requests
            </h3>

            <p>
                There are no change requests available right now.
            </p>

        </div>


        <?php

        }

        ?>


        </div>


    </div>


</div>


</body>

</html>