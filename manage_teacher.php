<?php

session_start();

if(!isset($_SESSION['admin']))
{
    header("Location:../login.php");
    exit();
}

include "../db.php";

$result = mysqli_query($conn,"SELECT * FROM teacher ORDER BY id DESC");

$total_teachers = mysqli_num_rows($result);

?>

<!DOCTYPE html>

<html>

<head>

<title>Manage Teachers - EduTrack</title>

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


/* ================= SIDEBAR ================= */

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


/* ================= MAIN ================= */

.main{
    margin-left:250px;
    min-height:100vh;
    padding:0 30px 40px;
}


/* ================= TOPBAR ================= */

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


/* ================= PAGE HEADER ================= */

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


/* ================= ADD BUTTON ================= */

.add-btn{
    display:inline-flex;
    align-items:center;
    gap:8px;
    padding:12px 18px;
    background:linear-gradient(90deg,#FF6B00,#FFB000);
    color:white;
    text-decoration:none;
    border-radius:10px;
    font-size:14px;
    font-weight:bold;
    box-shadow:0 6px 18px rgba(255,107,0,.20);
    transition:.3s;
}

.add-btn:hover{
    transform:translateY(-2px);
    box-shadow:0 9px 22px rgba(255,107,0,.30);
}


/* ================= STAT CARD ================= */

.stat-card{
    background:white;
    border:1px solid #E7E5E4;
    border-radius:16px;
    padding:20px;
    display:flex;
    align-items:center;
    gap:15px;
    width:240px;
    margin-bottom:25px;
    box-shadow:0 6px 22px rgba(0,0,0,.05);
}

.stat-icon{
    width:50px;
    height:50px;
    border-radius:13px;
    background:#FFF7ED;
    color:#FF6B00;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:24px;
}

.stat-card h3{
    font-size:24px;
    color:#292524;
    margin-bottom:3px;
}

.stat-card p{
    font-size:12px;
    color:#78716C;
}


/* ================= TABLE CARD ================= */

.table-card{
    background:white;
    border:1px solid #E7E5E4;
    border-radius:18px;
    box-shadow:0 8px 30px rgba(0,0,0,.06);
    overflow:hidden;
}

.table-header{
    padding:20px 22px;
    border-bottom:1px solid #E7E5E4;
    display:flex;
    align-items:center;
    justify-content:space-between;
}

.table-header h3{
    font-size:17px;
    color:#292524;
}

.table-header span{
    font-size:12px;
    color:#78716C;
}


/* ================= TABLE ================= */

.table-wrapper{
    width:100%;
    overflow-x:auto;
}

table{
    width:100%;
    border-collapse:collapse;
    min-width:1000px;
}

table th{
    background:#292524;
    color:#FFF7ED;
    padding:15px 12px;
    font-size:12px;
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


/* ================= ID ================= */

.student-id{
    font-weight:bold;
    color:#FF6B00;
}


/* ================= PHOTO ================= */

.teacher-photo{
    width:52px;
    height:52px;
    border-radius:12px;
    object-fit:cover;
    border:2px solid #FED7AA;
}

.no-photo{
    width:52px;
    height:52px;
    border-radius:12px;
    background:#F5F5F4;
    color:#A8A29E;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    font-size:20px;
}


/* ================= NAME ================= */

.teacher-name{
    font-weight:bold;
    color:#292524;
}


/* ================= EMAIL ================= */

.email{
    color:#57534E;
}


/* ================= SUBJECT ================= */

.subject-badge{
    display:inline-block;
    padding:6px 11px;
    background:#FFF7ED;
    color:#C2410C;
    border-radius:20px;
    font-size:11px;
    font-weight:bold;
}


/* ================= QUALIFICATION ================= */

.qualification{
    display:inline-block;
    padding:6px 10px;
    background:#F5F5F4;
    color:#57534E;
    border-radius:8px;
    font-size:11px;
    font-weight:bold;
}


/* ================= ACTION ================= */

.actions{
    display:flex;
    align-items:center;
    justify-content:center;
    gap:8px;
}

.edit{
    display:inline-flex;
    align-items:center;
    gap:5px;
    background:#FFF7ED;
    color:#EA580C;
    padding:8px 13px;
    text-decoration:none;
    border-radius:8px;
    font-size:12px;
    font-weight:bold;
    border:1px solid #FED7AA;
    transition:.3s;
}

.edit:hover{
    background:#FF6B00;
    color:white;
}

.delete{
    display:inline-flex;
    align-items:center;
    gap:5px;
    background:#FEF2F2;
    color:#DC2626;
    padding:8px 13px;
    text-decoration:none;
    border-radius:8px;
    font-size:12px;
    font-weight:bold;
    border:1px solid #FECACA;
    transition:.3s;
}

.delete:hover{
    background:#DC2626;
    color:white;
}


/* ================= EMPTY ================= */

.empty{
    padding:50px 20px;
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


/* ================= RESPONSIVE ================= */

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

    .page-header{
        gap:15px;
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

    .add-btn{
        margin-top:15px;
    }

    .stat-card{
        width:100%;
    }

}

</style>

</head>


<body>


<!-- ================= SIDEBAR ================= -->

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

            <a href="manage_teacher.php" class="active">

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



<!-- ================= MAIN ================= -->

<div class="main">


    <!-- TOPBAR -->

    <div class="topbar">


        <div class="topbar-left">

            <h1>
                Manage Teachers
            </h1>

            <p>
                View and manage all registered teachers
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
                Teacher Management
            </h2>

            <p>
                Manage teacher profiles, subjects and qualifications
            </p>

        </div>


        <a
            href="add_teacher.php"
            class="add-btn"
        >

            + Add Teacher

        </a>


    </div>



    <!-- STAT -->

    <div class="stat-card">


        <div class="stat-icon">
            👨‍🏫
        </div>


        <div>

            <h3>
                <?php echo $total_teachers; ?>
            </h3>

            <p>
                Total Teachers
            </p>

        </div>


    </div>



    <!-- TABLE -->

    <div class="table-card">


        <div class="table-header">

            <h3>
                Registered Teachers
            </h3>

            <span>
                Teacher Directory
            </span>

        </div>



        <?php

        if($total_teachers > 0)
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
                        Photo
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
                        Subject
                    </th>

                    <th>
                        Qualification
                    </th>

                    <th>
                        Action
                    </th>

                </tr>

            </thead>


            <tbody>


            <?php

            while($row=mysqli_fetch_assoc($result))
            {

            ?>


            <tr>


                <!-- ID -->

                <td>

                    <span class="student-id">

                        #<?php echo $row['id']; ?>

                    </span>

                </td>



                <!-- PHOTO -->

                <td>


                    <?php

                    if(
                        !empty($row['photo']) &&
                        file_exists("../uploads/".$row['photo'])
                    )
                    {

                    ?>

                        <img
                            class="teacher-photo"
                            src="../uploads/<?php echo htmlspecialchars($row['photo']); ?>"
                        >

                    <?php

                    }
                    else
                    {

                    ?>

                        <span class="no-photo">
                            👨‍🏫
                        </span>

                    <?php

                    }

                    ?>


                </td>



                <!-- NAME -->

                <td>

                    <span class="teacher-name">

                        <?php
                        echo htmlspecialchars($row['name']);
                        ?>

                    </span>

                </td>



                <!-- EMAIL -->

                <td>

                    <span class="email">

                        <?php
                        echo htmlspecialchars($row['email']);
                        ?>

                    </span>

                </td>



                <!-- PHONE -->

                <td>

                    <?php
                    echo htmlspecialchars($row['phone']);
                    ?>

                </td>



                <!-- SUBJECT -->

                <td>

                    <span class="subject-badge">

                        <?php
                        echo htmlspecialchars($row['subject']);
                        ?>

                    </span>

                </td>



                <!-- QUALIFICATION -->

                <td>

                    <span class="qualification">

                        <?php
                        echo htmlspecialchars($row['qualification']);
                        ?>

                    </span>

                </td>



                <!-- ACTION -->

                <td>


                    <div class="actions">


                        <a
                            class="edit"
                            href="edit_teacher.php?id=<?php echo $row['id']; ?>"
                        >

                            ✏️ Edit

                        </a>


                        <a
                            class="delete"
                            onclick="return confirm('Delete Teacher?')"
                            href="delete_teacher.php?id=<?php echo $row['id']; ?>"
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

        </div>


        <?php

        }
        else
        {

        ?>


        <div class="empty">

            <div class="empty-icon">
                👨‍🏫
            </div>

            <h3>
                No Teachers Found
            </h3>

            <p>
                No teacher has been registered yet.
            </p>

        </div>


        <?php

        }

        ?>


    </div>


</div>


</body>

</html>