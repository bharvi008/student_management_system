<?php

session_start();

include "../db.php";

if(!isset($_SESSION['teacher_id']))
{
    header("Location:../login.php");
    exit();
}

$id = $_SESSION['teacher_id'];


/* ================= GET TEACHER DATA ================= */

$result = mysqli_query(
    $conn,
    "SELECT * FROM teacher WHERE id='$id'"
);

$row = mysqli_fetch_assoc($result);

if(!$row)
{
    echo "Teacher profile not found.";
    exit();
}


/* ================= UPDATE PROFILE ================= */

if(isset($_POST['update']))
{

    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $subject = trim($_POST['subject']);
    $qualification = trim($_POST['qualification']);


    /* Proper Name */

    $name = ucwords(
        strtolower($name)
    );


    /* ================= PHOTO UPLOAD ================= */

    if(
        isset($_FILES['photo']) &&
        $_FILES['photo']['name'] != ""
    )
    {

        $original_name = $_FILES['photo']['name'];
        $tmp_name = $_FILES['photo']['tmp_name'];

        $extension = strtolower(
            pathinfo(
                $original_name,
                PATHINFO_EXTENSION
            )
        );


        $allowed = array(
            "jpg",
            "jpeg",
            "png",
            "webp"
        );


        if(in_array($extension,$allowed))
        {

            /* Create uploads folder if not exists */

            $upload_dir = "../uploads/";

            if(!is_dir($upload_dir))
            {
                mkdir(
                    $upload_dir,
                    0777,
                    true
                );
            }


            /* New photo name */

            $photo = "teacher_".$id."_".time().".".$extension;


            $upload_path =
                $upload_dir.$photo;


            if(
                move_uploaded_file(
                    $tmp_name,
                    $upload_path
                )
            )
            {

                $sql = "UPDATE teacher SET

                        name='$name',
                        phone='$phone',
                        subject='$subject',
                        qualification='$qualification',
                        photo='$photo'

                        WHERE id='$id'";

            }
            else
            {

                echo "<script>
                    alert('Photo upload failed');
                </script>";

                $sql = "UPDATE teacher SET

                        name='$name',
                        phone='$phone',
                        subject='$subject',
                        qualification='$qualification'

                        WHERE id='$id'";

            }

        }
        else
        {

            echo "<script>
                alert('Only JPG, JPEG, PNG and WEBP images are allowed');
            </script>";

            $sql = "UPDATE teacher SET

                    name='$name',
                    phone='$phone',
                    subject='$subject',
                    qualification='$qualification'

                    WHERE id='$id'";

        }

    }
    else
    {

        $sql = "UPDATE teacher SET

                name='$name',
                phone='$phone',
                subject='$subject',
                qualification='$qualification'

                WHERE id='$id'";

    }


    /* ================= UPDATE DATABASE ================= */

    if(mysqli_query($conn,$sql))
    {

        $_SESSION['teacher_name'] = $name;

        echo "<script>

            alert('Profile Updated Successfully');

            window.location='profile.php';

        </script>";

        exit();

    }

}

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>My Profile | EduTrack</title>


<style>

/* ================= RESET ================= */

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}


body{

    font-family:
    Arial,
    Helvetica,
    sans-serif;

    background:#FFF7ED;

    color:#292524;

}


/* ================= HEADER ================= */

.top-header{

    width:100%;

    background:#1C1917;

    padding:18px 7%;

    display:flex;

    align-items:center;

    justify-content:space-between;

    position:sticky;

    top:0;

    z-index:1000;

    box-shadow:
    0 4px 20px rgba(0,0,0,0.15);

}


.logo a{

    text-decoration:none;

    font-size:28px;

    font-weight:800;

    color:white;

}


.logo span{

    color:#FF8A00;

}


.home-nav{

    display:flex;

    gap:28px;

}


.home-nav a{

    text-decoration:none;

    color:white;

    font-size:15px;

    transition:0.3s;

}


.home-nav a:hover{

    color:#FFB000;

}


/* ================= TEACHER MENU ================= */

.teacher-menu{

    width:fit-content;

    max-width:95%;

    margin:25px auto 10px;

    display:flex;

    align-items:center;

    justify-content:center;

    gap:8px;

    padding:10px 14px;

    background:white;

    border-radius:50px;

    box-shadow:
    0 10px 30px rgba(0,0,0,0.08);

    flex-wrap:wrap;

}


.teacher-menu a{

    text-decoration:none;

    color:#44403C;

    padding:11px 16px;

    border-radius:30px;

    font-size:14px;

    font-weight:600;

    transition:0.3s;

}


.teacher-menu a:hover{

    background:#FFF1E6;

    color:#FF6B00;

    transform:translateY(-2px);

}


.teacher-menu a.active{

    background:
    linear-gradient(
        90deg,
        #FF6B00,
        #FFB000
    );

    color:white;

    box-shadow:
    0 5px 15px
    rgba(255,107,0,0.25);

}


.teacher-menu .logout{

    color:#DC2626;

}


.teacher-menu .logout:hover{

    background:#FEE2E2;

    color:#DC2626;

}


/* ================= MAIN ================= */

.main{

    width:90%;

    max-width:1100px;

    margin:40px auto 60px;

    animation:
    fadeUp 0.6s ease;

}


/* ================= PAGE HEADER ================= */

.page-header{

    background:
    linear-gradient(
        135deg,
        #1C1917,
        #292524
    );

    color:white;

    padding:30px 35px;

    border-radius:25px;

    display:flex;

    align-items:center;

    justify-content:space-between;

    margin-bottom:30px;

    box-shadow:
    0 15px 35px
    rgba(0,0,0,0.12);

}


.page-header h1{

    font-size:30px;

    margin-bottom:8px;

}


.page-header p{

    color:#D6D3D1;

    line-height:1.5;

}


.profile-badge{

    background:#FF6B00;

    padding:11px 18px;

    border-radius:25px;

    font-weight:bold;

    white-space:nowrap;

}


/* ================= PROFILE CARD ================= */

.profile-card{

    background:white;

    border-radius:28px;

    padding:40px;

    box-shadow:
    0 15px 35px
    rgba(0,0,0,0.08);

}


/* ================= PROFILE TOP ================= */

.profile-top{

    text-align:center;

    padding-bottom:30px;

    margin-bottom:30px;

    border-bottom:1px solid #F1F0EE;

}


.profile-image-wrapper{

    position:relative;

    display:inline-block;

    margin-bottom:15px;

}


.profile-image{

    width:145px;

    height:145px;

    border-radius:50%;

    object-fit:cover;

    border:6px solid #FFF1E6;

    box-shadow:
    0 10px 25px
    rgba(255,107,0,0.18);

    background:#FFF7ED;

}


.profile-placeholder{

    width:145px;

    height:145px;

    border-radius:50%;

    background:#FFF1E6;

    border:6px solid #FFE0C2;

    display:flex;

    align-items:center;

    justify-content:center;

    font-size:55px;

    box-shadow:
    0 10px 25px
    rgba(255,107,0,0.15);

}


.profile-top h2{

    font-size:25px;

    margin-bottom:6px;

}


.profile-top p{

    color:#78716C;

    font-size:14px;

}


/* ================= FORM ================= */

.form-title{

    font-size:21px;

    margin-bottom:22px;

    color:#292524;

}


.form-grid{

    display:grid;

    grid-template-columns:
    repeat(2,1fr);

    gap:20px;

}


.form-group{

    display:flex;

    flex-direction:column;

}


.form-group.full{

    grid-column:
    1 / -1;

}


.form-group label{

    font-size:14px;

    font-weight:700;

    margin-bottom:8px;

    color:#44403C;

}


.form-group input,
.form-group select{

    width:100%;

    padding:13px 15px;

    border:1px solid #E7E5E4;

    border-radius:12px;

    outline:none;

    background:#FFFBF5;

    color:#292524;

    font-size:15px;

    transition:0.3s;

}


.form-group input:focus,
.form-group select:focus{

    border-color:#FF8A00;

    background:white;

    box-shadow:
    0 0 0 3px
    rgba(255,138,0,0.10);

}


.form-group input[readonly]{

    background:#F5F5F4;

    color:#78716C;

    cursor:not-allowed;

}


/* ================= FILE UPLOAD ================= */

.file-box{

    border:2px dashed #FED7AA;

    background:#FFF7ED;

    border-radius:15px;

    padding:18px;

    transition:0.3s;

}


.file-box:hover{

    border-color:#FF8A00;

    background:#FFF1E6;

}


.file-box input{

    border:none;

    background:transparent;

    padding:0;

}


.file-box input:focus{

    box-shadow:none;

}


/* ================= BUTTON ================= */

.button-area{

    margin-top:28px;

    display:flex;

    justify-content:flex-end;

}


.update-btn{

    border:none;

    background:
    linear-gradient(
        90deg,
        #FF6B00,
        #FFB000
    );

    color:white;

    padding:14px 28px;

    border-radius:12px;

    font-size:16px;

    font-weight:bold;

    cursor:pointer;

    box-shadow:
    0 8px 18px
    rgba(255,107,0,0.20);

    transition:0.3s;

}


.update-btn:hover{

    transform:
    translateY(-3px);

    box-shadow:
    0 12px 25px
    rgba(255,107,0,0.30);

}


/* ================= SECURITY INFO ================= */

.info-box{

    margin-top:25px;

    padding:20px;

    border-left:5px solid #FF6B00;

    background:
    linear-gradient(
        135deg,
        #FFF1E6,
        #FFF7ED
    );

    border-radius:15px;

}


.info-box h3{

    font-size:16px;

    margin-bottom:6px;

}


.info-box p{

    color:#78716C;

    font-size:14px;

    line-height:1.6;

}


/* ================= FOOTER ================= */

.footer{

    width:100%;

    background:#1C1917;

    color:white;

    text-align:center;

    padding:35px 20px;

    margin-top:60px;

}


.footer h3{

    color:#FFB000;

    margin-bottom:10px;

}


.footer p{

    color:#A8A29E;

    font-size:14px;

    margin:7px 0;

}


/* ================= ANIMATION ================= */

@keyframes fadeUp{

    from{

        opacity:0;

        transform:
        translateY(20px);

    }

    to{

        opacity:1;

        transform:
        translateY(0);

    }

}


/* ================= TABLET ================= */

@media(max-width:800px){

    .form-grid{

        grid-template-columns:1fr;

    }

    .form-group.full{

        grid-column:auto;

    }

}


/* ================= MOBILE ================= */

@media(max-width:700px){

    .top-header{

        flex-direction:column;

        gap:15px;

    }


    .home-nav{

        gap:15px;

        flex-wrap:wrap;

        justify-content:center;

    }


    .teacher-menu{

        border-radius:20px;

    }


    .teacher-menu a{

        font-size:13px;

        padding:9px 12px;

    }


    .main{

        width:94%;

        margin-top:25px;

    }


    .page-header{

        padding:25px;

        flex-direction:column;

        align-items:flex-start;

        gap:18px;

        border-radius:20px;

    }


    .page-header h1{

        font-size:25px;

    }


    .profile-card{

        padding:25px 20px;

        border-radius:22px;

    }


    .button-area{

        justify-content:stretch;

    }


    .update-btn{

        width:100%;

    }

}

</style>

</head>


<body>


<!-- ================= HEADER ================= -->

<header class="top-header">

    <div class="logo">

        <a href="../index.php">

            Edu<span>Track</span>

        </a>

    </div>


    <nav class="home-nav">

        <a href="../index.php">
            Home
        </a>

        <a href="../index.php#about">
            About
        </a>

        <a href="../index.php#features">
            Features
        </a>

        <a href="../index.php#contact">
            Contact
        </a>

    </nav>

</header>



<!-- ================= TEACHER MENU ================= -->

<nav class="teacher-menu">

    <a href="dashboard.php">

        🏠 Overview

    </a>


    <a href="students.php">

        👨‍🎓 Students

    </a>


    <a href="attendance.php">

        📅 Attendance

    </a>


    <a href="result.php">

        📊 Result

    </a>


    <a href="course.php">

        📚 Course

    </a>


    <a href="profile.php" class="active">

        👤 My Profile

    </a>


    <a href="change_password.php">

        🔑 Password

    </a>


    <a
        href="logout.php"
        class="logout"
        onclick="return confirm('Are you sure you want to logout?')"
    >

        🚪 Logout

    </a>

</nav>



<!-- ================= MAIN ================= -->

<div class="main">


    <!-- ================= PAGE HEADER ================= -->

    <div class="page-header">

        <div>

            <h1>
                👤 My Profile
            </h1>

            <p>
                View and update your teacher profile information.
            </p>

        </div>


        <div class="profile-badge">

            ✏️ Profile Settings

        </div>

    </div>



    <!-- ================= PROFILE CARD ================= -->

    <div class="profile-card">


        <!-- ================= PROFILE TOP ================= -->

        <div class="profile-top">


            <div class="profile-image-wrapper">

                <?php

                if(!empty($row['photo']))
                {

                ?>

                    <img
                        class="profile-image"
                        src="../uploads/<?php echo htmlspecialchars($row['photo']); ?>"
                        alt="Teacher Photo"
                    >

                <?php

                }
                else
                {

                ?>

                    <div class="profile-placeholder">

                        👤

                    </div>

                <?php

                }

                ?>

            </div>


            <h2>

                <?php

                echo htmlspecialchars(
                    ucwords(
                        strtolower(
                            trim($row['name'])
                        )
                    )
                );

                ?>

            </h2>


            <p>
                👨‍🏫 Teacher Profile
            </p>


        </div>



        <!-- ================= FORM ================= -->

        <h3 class="form-title">

            📝 Personal & Professional Information

        </h3>


        <form
            method="POST"
            enctype="multipart/form-data"
        >


            <div class="form-grid">


                <!-- NAME -->

                <div class="form-group">

                    <label>
                        👤 Full Name
                    </label>

                    <input
                        type="text"
                        name="name"
                        value="<?php
                        echo htmlspecialchars($row['name']);
                        ?>"
                        required
                    >

                </div>



                <!-- EMAIL -->

                <div class="form-group">

                    <label>
                        📧 Email Address
                    </label>

                    <input
                        type="email"
                        value="<?php
                        echo htmlspecialchars($row['email']);
                        ?>"
                        readonly
                    >

                </div>



                <!-- PHONE -->

                <div class="form-group">

                    <label>
                        📱 Phone Number
                    </label>

                    <input
                        type="text"
                        name="phone"
                        value="<?php
                        echo htmlspecialchars($row['phone']);
                        ?>"
                        maxlength="10"
                        pattern="[0-9]{10}"
                        title="Phone number must contain exactly 10 digits"
                        required
                    >

                </div>



                <!-- SUBJECT -->

                <div class="form-group">

                    <label>
                        📚 Subject
                    </label>

                    <input
                        type="text"
                        name="subject"
                        value="<?php
                        echo htmlspecialchars($row['subject']);
                        ?>"
                        required
                    >

                </div>



                <!-- QUALIFICATION -->

                <div class="form-group full">

                    <label>
                        🎓 Qualification
                    </label>

                    <select name="qualification" required>


                        <option value="MCA"
                        <?php
                        if($row['qualification']=="MCA")
                        echo "selected";
                        ?>>
                            MCA
                        </option>


                        <option value="M.Sc IT"
                        <?php
                        if($row['qualification']=="M.Sc IT")
                        echo "selected";
                        ?>>
                            M.Sc IT
                        </option>


                        <option value="M.Tech"
                        <?php
                        if($row['qualification']=="M.Tech")
                        echo "selected";
                        ?>>
                            M.Tech
                        </option>


                        <option value="B.Tech"
                        <?php
                        if($row['qualification']=="B.Tech")
                        echo "selected";
                        ?>>
                            B.Tech
                        </option>


                        <option value="BCA"
                        <?php
                        if($row['qualification']=="BCA")
                        echo "selected";
                        ?>>
                            BCA
                        </option>


                        <option value="B.Sc IT"
                        <?php
                        if($row['qualification']=="B.Sc IT")
                        echo "selected";
                        ?>>
                            B.Sc IT
                        </option>


                        <option value="Ph.D"
                        <?php
                        if($row['qualification']=="Ph.D")
                        echo "selected";
                        ?>>
                            Ph.D
                        </option>


                        <option value="B.Ed"
                        <?php
                        if($row['qualification']=="B.Ed")
                        echo "selected";
                        ?>>
                            B.Ed
                        </option>


                        <option value="M.Ed"
                        <?php
                        if($row['qualification']=="M.Ed")
                        echo "selected";
                        ?>>
                            M.Ed
                        </option>


                        <option value="NET Qualified"
                        <?php
                        if($row['qualification']=="NET Qualified")
                        echo "selected";
                        ?>>
                            NET Qualified
                        </option>


                    </select>

                </div>



                <!-- PHOTO -->

                <div class="form-group full">

                    <label>
                        📷 Change Profile Photo
                    </label>


                    <div class="file-box">

                        <input
                            type="file"
                            name="photo"
                            accept=".jpg,.jpeg,.png,.webp"
                        >

                    </div>

                </div>


            </div>



            <!-- ================= BUTTON ================= -->

            <div class="button-area">

                <button
                    type="submit"
                    name="update"
                    class="update-btn"
                >

                    💾 Update Profile

                </button>

            </div>


        </form>



        <!-- ================= INFO ================= -->

        <div class="info-box">

            <h3>
                🔒 Profile Information
            </h3>

            <p>
                Your email address is fixed and cannot be changed
                from this page. You can update your name, phone,
                subject, qualification and profile photo.
            </p>

        </div>


    </div>


</div>



<!-- ================= FOOTER ================= -->

<footer class="footer">

    <h3>
        EduTrack
    </h3>

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