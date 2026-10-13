<?php

session_start();

if(!isset($_SESSION['admin']))
{
    header("Location:../login.php");
    exit();
}

include "../db.php";

$id = $_GET['id'];

$result = mysqli_query($conn,"SELECT * FROM teacher WHERE id='$id'");

$row = mysqli_fetch_assoc($result);

if(!$row)
{
    echo "<script>
    alert('Teacher not found');
    window.location='manage_teacher.php';
    </script>";
    exit();
}


if(isset($_POST['update']))
{

$name = mysqli_real_escape_string($conn,$_POST['name']);
$email = mysqli_real_escape_string($conn,$_POST['email']);
$phone = mysqli_real_escape_string($conn,$_POST['phone']);
$subject = mysqli_real_escape_string($conn,$_POST['subject']);
$qualification = mysqli_real_escape_string($conn,$_POST['qualification']);


if($_FILES['photo']['name']!="")
{

$photo = time()."_".basename($_FILES['photo']['name']);

move_uploaded_file(
    $_FILES['photo']['tmp_name'],
    "../uploads/".$photo
);

mysqli_query($conn,"UPDATE teacher SET

name='$name',
email='$email',
phone='$phone',
subject='$subject',
qualification='$qualification',
photo='$photo'

WHERE id='$id'");

}
else
{

mysqli_query($conn,"UPDATE teacher SET

name='$name',
email='$email',
phone='$phone',
subject='$subject',
qualification='$qualification'

WHERE id='$id'");

}


echo "<script>

alert('Teacher Updated Successfully');

window.location='manage_teacher.php';

</script>";

}

?>

<!DOCTYPE html>

<html>

<head>

<title>Edit Teacher - EduTrack</title>

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


/* ================= FORM CARD ================= */

.form-card{
    background:white;
    border-radius:18px;
    padding:30px;
    box-shadow:0 8px 30px rgba(0,0,0,.07);
    border:1px solid #E7E5E4;
    max-width:850px;
    margin:auto;
}


/* ================= TEACHER INFO ================= */

.teacher-info{
    display:flex;
    align-items:center;
    gap:16px;
    padding:18px;
    background:#FFF7ED;
    border:1px solid #FED7AA;
    border-radius:14px;
    margin-bottom:25px;
}

.teacher-photo{
    width:60px;
    height:60px;
    border-radius:14px;
    object-fit:cover;
    border:3px solid white;
    box-shadow:0 4px 12px rgba(0,0,0,.12);
}

.teacher-placeholder{
    width:60px;
    height:60px;
    border-radius:14px;
    background:linear-gradient(135deg,#FF6B00,#FFB000);
    display:flex;
    align-items:center;
    justify-content:center;
    color:white;
    font-size:28px;
}

.teacher-info h3{
    font-size:17px;
    color:#292524;
    margin-bottom:4px;
}

.teacher-info p{
    font-size:12px;
    color:#78716C;
}


/* ================= FORM ================= */

.form-grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:20px;
}

.form-group{
    display:flex;
    flex-direction:column;
}

.form-group.full{
    grid-column:1 / -1;
}

.form-group label{
    font-size:13px;
    font-weight:bold;
    color:#44403C;
    margin-bottom:8px;
}

.form-group input,
.form-group select{
    width:100%;
    padding:13px 14px;
    border:1px solid #D6D3D1;
    border-radius:10px;
    background:#FAFAF9;
    color:#292524;
    font-size:14px;
    outline:none;
    transition:.3s;
}

.form-group input:focus,
.form-group select:focus{
    border-color:#FF8A00;
    background:white;
    box-shadow:0 0 0 3px rgba(255,138,0,.10);
}

.form-group input:hover,
.form-group select:hover{
    border-color:#FFB000;
}


/* ================= PHOTO UPLOAD ================= */

.photo-upload{
    margin-top:20px;
    padding:18px;
    border:1px dashed #FDBA74;
    background:#FFF7ED;
    border-radius:12px;
}

.photo-upload label{
    display:block;
    font-size:13px;
    font-weight:bold;
    color:#44403C;
    margin-bottom:9px;
}

.photo-upload input{
    width:100%;
    padding:10px;
    background:white;
    border:1px solid #E7E5E4;
    border-radius:8px;
    font-size:13px;
}


/* ================= BUTTONS ================= */

.button-area{
    display:flex;
    justify-content:flex-end;
    gap:12px;
    margin-top:28px;
    padding-top:22px;
    border-top:1px solid #E7E5E4;
}

.back-btn{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    padding:12px 22px;
    background:#F5F5F4;
    color:#57534E;
    text-decoration:none;
    border-radius:10px;
    font-size:14px;
    font-weight:bold;
    transition:.3s;
}

.back-btn:hover{
    background:#E7E5E4;
}

.update-btn{
    border:none;
    padding:12px 25px;
    background:linear-gradient(90deg,#FF6B00,#FFB000);
    color:white;
    border-radius:10px;
    font-size:14px;
    font-weight:bold;
    cursor:pointer;
    box-shadow:0 6px 18px rgba(255,107,0,.20);
    transition:.3s;
}

.update-btn:hover{
    transform:translateY(-2px);
    box-shadow:0 9px 22px rgba(255,107,0,.30);
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

    .form-grid{
        grid-template-columns:1fr;
    }

    .form-group.full{
        grid-column:auto;
    }

    .form-card{
        padding:20px;
    }

    .page-header{
        display:block;
    }

    .button-area{
        flex-direction:column;
    }

    .back-btn,
    .update-btn{
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
                Edit Teacher
            </h1>

            <p>
                Update teacher information and details
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
                Teacher Information
            </h2>

            <p>
                Edit and update registered teacher details
            </p>

        </div>

    </div>



    <!-- FORM CARD -->

    <div class="form-card">


        <!-- TEACHER INFO -->

        <div class="teacher-info">


            <?php

            if(!empty($row['photo']) &&
               file_exists("../uploads/".$row['photo']))
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

                <div class="teacher-placeholder">
                    👨‍🏫
                </div>

            <?php

            }

            ?>


            <div>

                <h3>
                    <?php
                    echo htmlspecialchars($row['name']);
                    ?>
                </h3>

                <p>
                    Teacher ID: #<?php echo $row['id']; ?>
                </p>

            </div>

        </div>



        <!-- FORM -->

        <form
            method="POST"
            enctype="multipart/form-data"
        >


            <div class="form-grid">


                <!-- NAME -->

                <div class="form-group">

                    <label>
                        Teacher Name
                    </label>

                    <input
                        type="text"
                        name="name"
                        value="<?php echo htmlspecialchars($row['name']); ?>"
                        required
                    >

                </div>



                <!-- EMAIL -->

                <div class="form-group">

                    <label>
                        Email Address
                    </label>

                    <input
                        type="email"
                        name="email"
                        value="<?php echo htmlspecialchars($row['email']); ?>"
                        required
                    >

                </div>



                <!-- PHONE -->

                <div class="form-group">

                    <label>
                        Phone Number
                    </label>

                    <input
                        type="text"
                        name="phone"
                        value="<?php echo htmlspecialchars($row['phone']); ?>"
                        maxlength="10"
                        pattern="[0-9]{10}"
                        inputmode="numeric"
                        required
                    >

                </div>



                <!-- SUBJECT -->

                <div class="form-group">

                    <label>
                        Subject
                    </label>

                    <input
                        type="text"
                        name="subject"
                        value="<?php echo htmlspecialchars($row['subject']); ?>"
                        required
                    >

                </div>



                <!-- QUALIFICATION -->

                <div class="form-group full">

                    <label>
                        Qualification
                    </label>

                    <select
                        name="qualification"
                        required
                    >

                        <option value="MCA"
                        <?php
                        if($row['qualification']=="MCA")
                        {
                            echo "selected";
                        }
                        ?>>
                            MCA
                        </option>


                        <option value="M.Sc IT"
                        <?php
                        if($row['qualification']=="M.Sc IT")
                        {
                            echo "selected";
                        }
                        ?>>
                            M.Sc IT
                        </option>


                        <option value="M.Tech"
                        <?php
                        if($row['qualification']=="M.Tech")
                        {
                            echo "selected";
                        }
                        ?>>
                            M.Tech
                        </option>


                        <option value="B.Tech"
                        <?php
                        if($row['qualification']=="B.Tech")
                        {
                            echo "selected";
                        }
                        ?>>
                            B.Tech
                        </option>


                        <option value="BCA"
                        <?php
                        if($row['qualification']=="BCA")
                        {
                            echo "selected";
                        }
                        ?>>
                            BCA
                        </option>


                        <option value="B.Sc IT"
                        <?php
                        if($row['qualification']=="B.Sc IT")
                        {
                            echo "selected";
                        }
                        ?>>
                            B.Sc IT
                        </option>


                        <option value="Ph.D"
                        <?php
                        if($row['qualification']=="Ph.D")
                        {
                            echo "selected";
                        }
                        ?>>
                            Ph.D
                        </option>


                        <option value="B.Ed"
                        <?php
                        if($row['qualification']=="B.Ed")
                        {
                            echo "selected";
                        }
                        ?>>
                            B.Ed
                        </option>


                        <option value="M.Ed"
                        <?php
                        if($row['qualification']=="M.Ed")
                        {
                            echo "selected";
                        }
                        ?>>
                            M.Ed
                        </option>


                        <option value="NET Qualified"
                        <?php
                        if($row['qualification']=="NET Qualified")
                        {
                            echo "selected";
                        }
                        ?>>
                            NET Qualified
                        </option>

                    </select>

                </div>


            </div>



            <!-- PHOTO -->

            <div class="photo-upload">

                <label>
                    📷 Change Teacher Photo
                </label>

                <input
                    type="file"
                    name="photo"
                    accept=".jpg,.jpeg,.png"
                >

            </div>



            <!-- BUTTONS -->

            <div class="button-area">

                <a
                    href="manage_teacher.php"
                    class="back-btn"
                >
                    ← Back to Teachers
                </a>


                <button
                    type="submit"
                    name="update"
                    class="update-btn"
                >
                    ✓ Update Teacher
                </button>

            </div>


        </form>

    </div>

</div>


</body>

</html>