<?php

session_start();

if(!isset($_SESSION['admin']))
{
    header("Location:../login.php");
    exit();
}

include "../db.php";


/* =========================
   SEARCH STUDENTS
========================= */

if(isset($_GET['search']))
{
    $search = mysqli_real_escape_string(
        $conn,
        $_GET['search']
    );

    $result = mysqli_query(
        $conn,

        "SELECT * FROM students
         WHERE
         name LIKE '%$search%'
         OR course LIKE '%$search%'
         OR semester LIKE '%$search%'
         ORDER BY id DESC"
    );
}
else
{
    $result = mysqli_query(
        $conn,

        "SELECT * FROM students
         ORDER BY id DESC"
    );
}


$total_students = mysqli_num_rows($result);

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>
    Manage Students | EduTrack
</title>


<style>

/* =========================
   RESET
========================= */

*{
    margin:0;
    padding:0;
    box-sizing:border-box;

    font-family:
        Arial,
        Helvetica,
        sans-serif;
}


/* =========================
   BODY
========================= */

body{
    background:#F7F7F5;

    color:#292524;
}


/* =========================
   SIDEBAR
========================= */

.sidebar{
    position:fixed;

    left:0;
    top:0;

    width:250px;
    height:100vh;

    background:#171412;

    padding:25px 18px;

    color:white;

    overflow-y:auto;

    z-index:1000;
}


.logo{
    font-size:28px;

    font-weight:800;

    padding:0 15px;

    margin-bottom:5px;
}


.logo span{
    color:#FF8A00;
}


.admin-label{
    color:#A8A29E;

    font-size:12px;

    padding:0 15px;

    margin-bottom:30px;

    text-transform:uppercase;

    letter-spacing:1px;
}


.menu-title{
    font-size:11px;

    color:#78716C;

    padding:0 15px;

    margin:20px 0 8px;

    text-transform:uppercase;

    letter-spacing:1px;
}


.sidebar a{
    display:flex;

    align-items:center;

    gap:12px;

    color:#D6D3D1;

    text-decoration:none;

    padding:12px 15px;

    border-radius:10px;

    margin-bottom:5px;

    font-size:14px;

    transition:.3s;
}


.sidebar a:hover{
    background:#292524;

    color:white;

    transform:translateX(3px);
}


.sidebar a.active{
    background:
        linear-gradient(
            90deg,
            #FF6B00,
            #FFB000
        );

    color:white;

    font-weight:700;

    box-shadow:
        0 7px 20px
        rgba(255,107,0,.20);
}


.logout{
    margin-top:30px;

    background:#2A1B16 !important;

    color:#FF8A00 !important;
}


.logout:hover{
    background:#7F1D1D !important;

    color:white !important;
}


/* =========================
   MAIN
========================= */

.main{
    margin-left:250px;

    min-height:100vh;
}


/* =========================
   TOPBAR
========================= */

.topbar{
    height:82px;

    background:white;

    border-bottom:
        1px solid #E7E5E4;

    display:flex;

    align-items:center;

    justify-content:space-between;

    padding:0 35px;
}


.page-title h2{
    font-size:22px;

    color:#1C1917;
}


.page-title p{
    font-size:13px;

    color:#78716C;

    margin-top:4px;
}


/* =========================
   ADMIN PROFILE
========================= */

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

    color:#1C1917;
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
        0 5px 15px
        rgba(255,107,0,.20);
}


/* =========================
   CONTENT
========================= */

.content{
    padding:30px 35px;
}


/* =========================
   PAGE HEADER
========================= */

.page-header{
    background:
        linear-gradient(
            135deg,
            #1C1917,
            #292524
        );

    color:white;

    border-radius:18px;

    padding:28px 30px;

    display:flex;

    justify-content:space-between;

    align-items:center;

    margin-bottom:25px;

    box-shadow:
        0 8px 25px
        rgba(0,0,0,.08);
}


.page-header h1{
    font-size:26px;

    margin-bottom:7px;
}


.page-header p{
    color:#D6D3D1;

    font-size:14px;
}


.student-count{
    background:
        rgba(255,255,255,.10);

    border:
        1px solid
        rgba(255,255,255,.15);

    padding:15px 22px;

    border-radius:14px;

    text-align:center;
}


.student-count strong{
    display:block;

    color:#FFB000;

    font-size:26px;
}


.student-count span{
    color:#D6D3D1;

    font-size:12px;
}


/* =========================
   TOOLBAR
========================= */

.toolbar{
    background:white;

    border:1px solid #E7E5E4;

    border-radius:16px;

    padding:20px;

    margin-bottom:25px;

    box-shadow:
        0 5px 20px
        rgba(0,0,0,.04);
}


.search-form{
    display:flex;

    align-items:center;

    gap:10px;

    flex-wrap:wrap;
}


.search-box{
    position:relative;

    flex:1;

    min-width:280px;
}


.search-box input{
    width:100%;

    padding:13px 15px 13px 42px;

    border:
        1px solid #D6D3D1;

    border-radius:9px;

    outline:none;

    font-size:14px;

    transition:.3s;
}


.search-box input:focus{
    border-color:#FF8A00;

    box-shadow:
        0 0 0 3px
        rgba(255,107,0,.10);
}


.search-icon{
    position:absolute;

    left:15px;

    top:50%;

    transform:translateY(-50%);

    color:#78716C;

    font-size:16px;
}


/* =========================
   BUTTONS
========================= */

.btn{
    display:inline-flex;

    align-items:center;

    justify-content:center;

    gap:7px;

    border:none;

    padding:12px 17px;

    border-radius:9px;

    text-decoration:none;

    font-size:13px;

    font-weight:700;

    cursor:pointer;

    transition:.3s;
}


.btn:hover{
    transform:translateY(-2px);
}


/* SEARCH */

.search-btn{
    background:
        linear-gradient(
            90deg,
            #FF6B00,
            #FFB000
        );

    color:white;
}


.search-btn:hover{
    box-shadow:
        0 7px 15px
        rgba(255,107,0,.20);
}


/* RESET */

.reset-btn{
    background:#F5F5F4;

    color:#57534E;

    border:1px solid #E7E5E4;
}


.reset-btn:hover{
    background:#E7E5E4;
}


/* ADD */

.add-btn{
    background:#1C1917;

    color:white;
}


.add-btn:hover{
    background:#292524;
}


/* EXPORT */

.export-btn{
    background:#ECFDF5;

    color:#15803D;

    border:
        1px solid #BBF7D0;
}


.export-btn:hover{
    background:#DCFCE7;
}


/* PRINT */

.print-btn{
    background:#EFF6FF;

    color:#2563EB;

    border:
        1px solid #BFDBFE;
}


.print-btn:hover{
    background:#DBEAFE;
}


/* =========================
   ACTION ROW
========================= */

.action-row{
    display:flex;

    align-items:center;

    gap:10px;

    flex-wrap:wrap;

    margin-top:15px;
}


/* =========================
   TABLE CARD
========================= */

.card{
    background:white;

    border-radius:16px;

    padding:25px;

    box-shadow:
        0 5px 20px
        rgba(0,0,0,.05);

    border:
        1px solid #E7E5E4;
}


/* =========================
   TABLE HEADER
========================= */

.table-header{
    display:flex;

    align-items:center;

    justify-content:space-between;

    margin-bottom:20px;
}


.table-header h3{
    font-size:18px;

    color:#1C1917;
}


.table-header p{
    color:#78716C;

    font-size:12px;

    margin-top:4px;
}


.result-count{
    color:#78716C;

    font-size:13px;
}


/* =========================
   TABLE
========================= */

.table-wrapper{
    overflow-x:auto;
}


table{
    width:100%;

    border-collapse:collapse;

    min-width:900px;
}


th{
    background:#292524;

    color:white;

    padding:15px;

    text-align:left;

    font-size:12px;

    font-weight:600;

    white-space:nowrap;
}


th:first-child{
    border-radius:8px 0 0 8px;
}


th:last-child{
    border-radius:0 8px 8px 0;
}


td{
    padding:14px 15px;

    border-bottom:
        1px solid #E7E5E4;

    font-size:13px;

    color:#44403C;

    vertical-align:middle;
}


tr:hover td{
    background:#FFF7ED;
}


/* =========================
   ID BADGE
========================= */

.id-badge{
    width:34px;

    height:34px;

    border-radius:8px;

    background:#F5F5F4;

    display:flex;

    align-items:center;

    justify-content:center;

    font-weight:700;

    color:#57534E;
}


/* =========================
   STUDENT NAME
========================= */

.student-name{
    font-weight:700;

    color:#1C1917;

    white-space:nowrap;
}


/* =========================
   EMAIL
========================= */

.email{
    color:#57534E;

    font-size:13px;
}


/* =========================
   PHONE
========================= */

.phone{
    white-space:nowrap;

    color:#57534E;
}


/* =========================
   COURSE BADGE
========================= */

.course-badge{
    display:inline-block;

    background:#FFF7ED;

    color:#EA580C;

    padding:6px 10px;

    border-radius:7px;

    font-size:12px;

    font-weight:700;

    white-space:nowrap;
}


/* =========================
   SEMESTER BADGE
========================= */

.semester-badge{
    display:inline-block;

    background:#F5F5F4;

    color:#57534E;

    padding:6px 10px;

    border-radius:7px;

    font-size:12px;

    font-weight:600;

    white-space:nowrap;
}


/* =========================
   GENDER
========================= */

.gender{
    color:#57534E;

    font-size:13px;
}


/* =========================
   ACTION
========================= */

.action{
    display:flex;

    gap:7px;

    white-space:nowrap;
}


.edit,
.delete{
    display:inline-block;

    padding:8px 12px;

    border-radius:7px;

    text-decoration:none;

    font-size:12px;

    font-weight:700;

    transition:.3s;
}


.edit{
    background:#FFF7ED;

    color:#EA580C;
}


.edit:hover{
    background:#FFEDD5;
}


.delete{
    background:#FEF2F2;

    color:#DC2626;
}


.delete:hover{
    background:#FEE2E2;
}


/* =========================
   EMPTY
========================= */

.empty{
    text-align:center;

    padding:55px 20px;

    color:#78716C;
}


.empty-icon{
    font-size:45px;

    margin-bottom:12px;
}


.empty h3{
    color:#44403C;

    margin-bottom:6px;

    font-size:18px;
}


.empty p{
    font-size:13px;
}


/* =========================
   FOOTER
========================= */

.footer{
    padding:25px 35px;

    text-align:center;

    color:#A8A29E;

    font-size:12px;
}


/* =========================
   RESPONSIVE
========================= */

@media(max-width:1000px){

    .sidebar{
        width:210px;
    }

    .main{
        margin-left:210px;
    }

    .content{
        padding:25px 20px;
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
    }

    .topbar{
        padding:0 20px;
    }

    .admin-info{
        display:none;
    }

    .content{
        padding:20px;
    }

    .page-header{
        flex-direction:column;

        align-items:flex-start;

        gap:20px;
    }

    .student-count{
        width:100%;
    }

    .search-box{
        min-width:100%;
    }

    .search-btn,
    .reset-btn{
        flex:1;
    }

    .footer{
        padding:20px;
    }

}

</style>

</head>


<body>


<!-- =========================
     SIDEBAR
========================= -->

<div class="sidebar">


    <!-- LOGO -->

    <div class="logo">

        Edu<span>Track</span>

    </div>


    <!-- ADMIN LABEL -->

    <div class="admin-label">

        Administration

    </div>


    <!-- MAIN -->

    <div class="menu-title">

        Main

    </div>


    <a href="admindashboard.php">

        📊 Dashboard

    </a>


    <!-- MANAGEMENT -->

    <div class="menu-title">

        Management

    </div>


    <a
        href="add_student.php"
        class="active"
    >

        👨‍🎓 Students

    </a>


    <a href="add_teacher.php">

        👨‍🏫 Teachers

    </a>


    <a href="course.php">

        📚 Courses

    </a>


    <!-- ACADEMIC -->

    <div class="menu-title">

        Academic

    </div>


    <a href="attendance.php">

        📝 Attendance

    </a>


    <a href="attendance_report.php">

        📈 Attendance Report

    </a>


    <a href="result.php">

        📊 Results

    </a>


    <!-- ACCOUNT -->

    <div class="menu-title">

        Account

    </div>


    <a
        href="logout.php"
        class="logout"
        onclick="
            return confirm(
                'Are you sure you want to logout?'
            )
        "
    >

        🚪 Logout

    </a>


</div>



<!-- =========================
     MAIN
========================= -->

<div class="main">


    <!-- =========================
         TOPBAR
    ========================= -->

    <div class="topbar">


        <div class="page-title">

            <h2>
                Manage Students
            </h2>

            <p>
                View and manage all registered students
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



    <!-- =========================
         CONTENT
    ========================= -->

    <div class="content">


        <!-- =========================
             PAGE HEADER
        ========================= -->

        <div class="page-header">


            <div>

                <h1>
                    👨‍🎓 Student Management
                </h1>

                <p>
                    Search, view, edit and manage student records.
                </p>

            </div>


            <div class="student-count">

                <strong>

                    <?php
                    echo $total_students;
                    ?>

                </strong>

                <span>
                    Students Found
                </span>

            </div>


        </div>



        <!-- =========================
             TOOLBAR
        ========================= -->

        <div class="toolbar">


            <!-- SEARCH -->

            <form
                method="GET"
                class="search-form"
            >


                <div class="search-box">


                    <span class="search-icon">
                        🔍
                    </span>


                    <input
                        type="text"
                        name="search"
                        placeholder="Search by student name, course or semester..."
                        value="<?php

                        if(isset($_GET['search']))
                        {
                            echo htmlspecialchars(
                                $_GET['search']
                            );
                        }

                        ?>"
                    >


                </div>


                <button
                    type="submit"
                    class="btn search-btn"
                >

                    🔍 Search

                </button>


                <a
                    href="manage_student.php"
                    class="btn reset-btn"
                >

                    ↻ Reset

                </a>


            </form>



            <!-- OTHER ACTIONS -->

            <div class="action-row">


                <a
                    href="add_student.php"
                    class="btn add-btn"
                >

                    + Add Student

                </a>


                <a
                    href="export_excel.php"
                    class="btn export-btn"
                >

                    📊 Export Excel

                </a>


                <a
                    href="print_students.php"
                    target="_blank"
                    class="btn print-btn"
                >

                    🖨 Print

                </a>


            </div>


        </div>



        <!-- =========================
             STUDENT TABLE
        ========================= -->

        <div class="card">


            <div class="table-header">


                <div>

                    <h3>
                        All Students
                    </h3>

                    <p>
                        Registered student records
                    </p>

                </div>


                <div class="result-count">

                    <?php
                    echo $total_students;
                    ?>
                    Records

                </div>


            </div>



            <div class="table-wrapper">


                <?php

                if($total_students > 0)

                {

                ?>


                <table>


                    <thead>

                        <tr>

                            <th>
                                ID
                            </th>

                            <th>
                                Name
                            </th>

                            <th>
                                Email
                            </th>

                            <th>
                                Phone
                            </th>

                            <th>
                                Gender
                            </th>

                            <th>
                                Course
                            </th>

                            <th>
                                Semester
                            </th>

                            <th>
                                Action
                            </th>

                        </tr>

                    </thead>



                    <tbody>


                    <?php

                    while(
                        $row =
                        mysqli_fetch_assoc($result)
                    )

                    {

                    ?>


                        <tr>


                            <!-- ID -->

                            <td>

                                <div class="id-badge">

                                    <?php
                                    echo $row['id'];
                                    ?>

                                </div>

                            </td>



                            <!-- NAME -->

                            <td>

                                <div class="student-name">

                                    <?php

                                    echo htmlspecialchars(
                                        $row['name']
                                    );

                                    ?>

                                </div>

                            </td>



                            <!-- EMAIL -->

                            <td>

                                <span class="email">

                                    <?php

                                    echo htmlspecialchars(
                                        $row['email']
                                    );

                                    ?>

                                </span>

                            </td>



                            <!-- PHONE -->

                            <td>

                                <span class="phone">

                                    <?php

                                    echo htmlspecialchars(
                                        $row['phone']
                                    );

                                    ?>

                                </span>

                            </td>



                            <!-- GENDER -->

                            <td>

                                <span class="gender">

                                    <?php

                                    echo htmlspecialchars(
                                        $row['gender']
                                    );

                                    ?>

                                </span>

                            </td>



                            <!-- COURSE -->

                            <td>

                                <span class="course-badge">

                                    <?php

                                    echo htmlspecialchars(
                                        $row['course']
                                    );

                                    ?>

                                </span>

                            </td>



                            <!-- SEMESTER -->

                            <td>

                                <span class="semester-badge">

                                    Semester
                                    <?php

                                    echo htmlspecialchars(
                                        $row['semester']
                                    );

                                    ?>

                                </span>

                            </td>



                            <!-- ACTION -->

                            <td>


                                <div class="action">


                                    <a
                                        class="edit"
                                        href="
                                        edit_student.php?id=
                                        <?php
                                        echo $row['id'];
                                        ?>
                                        "
                                    >

                                        ✏ Edit

                                    </a>


                                    <a
                                        class="delete"
                                        href="
                                        delete_student.php?id=
                                        <?php
                                        echo $row['id'];
                                        ?>
                                        "

                                        onclick="
                                            return confirm(
                                                'Delete this student?'
                                            )
                                        "
                                    >

                                        🗑 Delete

                                    </a>


                                </div>


                            </td>


                        </tr>


                    <?php

                    }

                    ?>


                    </tbody>


                </table>


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
                            No Students Found
                        </h3>


                        <p>
                            No student records match your search.
                        </p>


                    </div>


                <?php

                }

                ?>


            </div>


        </div>



    </div>



    <!-- =========================
         FOOTER
========================= -->

    <div class="footer">

        ©
        <?php
        echo date("Y");
        ?>

        <strong>
            EduTrack
        </strong>

        · Student Management System

    </div>


</div>


</body>

</html>