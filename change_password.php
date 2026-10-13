<?php

session_start();

include "../db.php";

if(!isset($_SESSION['teacher_id']))
{
    header("Location:../login.php");
    exit();
}

$id = $_SESSION['teacher_id'];

$message = "";
$message_type = "";


/* ==========================================
   CHANGE PASSWORD
========================================== */

if(isset($_POST['change']))
{
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    /* Old password MD5 */
    $old = md5($old_password);

    /* Check old password */

    $result = mysqli_query($conn,"
        SELECT id
        FROM teacher
        WHERE id='$id'
        AND password='$old'
        LIMIT 1
    ");

    if(mysqli_num_rows($result) > 0)
    {

        /* Check new password */

        if($new_password == $confirm_password)
        {

            /* New password MD5 */

            $new = md5($new_password);

            $update = mysqli_query($conn,"
                UPDATE teacher
                SET password='$new'
                WHERE id='$id'
            ");

            if($update)
            {
                echo "<script>

                    alert('Password Changed Successfully');

                    window.location='dashboard.php';

                </script>";

                exit();
            }
            else
            {
                $message = "Something went wrong. Please try again.";
                $message_type = "error";
            }

        }
        else
        {
            $message = "New Password and Confirm Password do not match.";
            $message_type = "error";
        }

    }
    else
    {
        $message = "Old Password is Incorrect.";
        $message_type = "error";
    }
}

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Change Password | EduTrack</title>


<style>

/* ==========================================
   RESET
========================================== */

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}


/* ==========================================
   BODY
========================================== */

body{

    font-family:
    Arial,
    Helvetica,
    sans-serif;

    background:#FFF7ED;

    color:#292524;

    min-height:100vh;

}


/* ==========================================
   HEADER
========================================== */

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
    0 4px 20px
    rgba(0,0,0,.15);

}


/* LOGO */

.logo a{

    text-decoration:none;

    font-size:28px;

    font-weight:800;

    color:white;

}

.logo span{

    color:#FF8A00;

}


/* HOME NAV */

.home-nav{

    display:flex;

    gap:28px;

}

.home-nav a{

    text-decoration:none;

    color:white;

    font-size:15px;

    transition:.3s;

}

.home-nav a:hover{

    color:#FFB000;

}


/* ==========================================
   TEACHER MENU
========================================== */

.teacher-menu{

    width:fit-content;

    max-width:95%;

    margin:25px auto 10px;

    padding:10px 14px;

    display:flex;

    align-items:center;

    justify-content:center;

    gap:8px;

    flex-wrap:wrap;

    background:white;

    border-radius:50px;

    box-shadow:
    0 10px 30px
    rgba(0,0,0,.08);

}


.teacher-menu a{

    text-decoration:none;

    color:#44403C;

    padding:11px 16px;

    border-radius:30px;

    font-size:14px;

    font-weight:600;

    transition:.3s;

}


.teacher-menu a:hover{

    background:#FFF1E6;

    color:#FF6B00;

    transform:translateY(-2px);

}


/* ACTIVE */

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
    rgba(255,107,0,.25);

}


/* LOGOUT */

.teacher-menu .logout{

    color:#DC2626;

}

.teacher-menu .logout:hover{

    background:#FEE2E2;

    color:#DC2626;

}


/* ==========================================
   MAIN
========================================== */

.main{

    width:90%;

    max-width:1000px;

    margin:40px auto 70px;

    animation:fadeUp .6s ease;

}


/* ==========================================
   PAGE HEADER
========================================== */

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
    rgba(0,0,0,.12);

}


.page-header h1{

    font-size:30px;

    margin-bottom:8px;

}


.page-header p{

    color:#D6D3D1;

    line-height:1.5;

}


.password-badge{

    background:
    linear-gradient(
        90deg,
        #FF6B00,
        #FFB000
    );

    padding:13px 20px;

    border-radius:25px;

    font-weight:bold;

    white-space:nowrap;

}


/* ==========================================
   PASSWORD CARD
========================================== */

.password-card{

    background:white;

    width:100%;

    max-width:600px;

    margin:0 auto;

    padding:35px;

    border-radius:25px;

    box-shadow:
    0 12px 35px
    rgba(0,0,0,.08);

    border:1px solid #F1F0EE;

}


/* CARD TOP */

.password-icon{

    width:75px;

    height:75px;

    border-radius:22px;

    background:#FFF1E6;

    display:flex;

    align-items:center;

    justify-content:center;

    font-size:36px;

    margin:0 auto 20px;

    animation:floatIcon 3s ease-in-out infinite;

}


.password-card h2{

    text-align:center;

    font-size:24px;

    margin-bottom:8px;

}


.password-card .description{

    text-align:center;

    color:#78716C;

    font-size:13px;

    margin-bottom:28px;

    line-height:1.5;

}


/* ==========================================
   MESSAGE
========================================== */

.message{

    padding:13px 15px;

    border-radius:12px;

    margin-bottom:20px;

    text-align:center;

    font-size:13px;

    font-weight:600;

}


.message.error{

    background:#FEE2E2;

    color:#DC2626;

}


/* ==========================================
   FORM
========================================== */

.form-group{

    margin-bottom:20px;

}


.form-group label{

    display:block;

    font-size:13px;

    font-weight:bold;

    color:#44403C;

    margin-bottom:8px;

}


.password-input{

    position:relative;

}


.password-input input{

    width:100%;

    padding:14px 50px 14px 15px;

    border:1px solid #E7E5E4;

    border-radius:13px;

    outline:none;

    background:#FFFBF5;

    color:#292524;

    font-size:14px;

    transition:.3s;

}


.password-input input:focus{

    border-color:#FF8A00;

    background:white;

    box-shadow:
    0 0 0 3px
    rgba(255,138,0,.10);

}


.password-input input::placeholder{

    color:#A8A29E;

}


/* EYE BUTTON */

.toggle-password{

    position:absolute;

    right:14px;

    top:50%;

    transform:translateY(-50%);

    border:none;

    background:none;

    cursor:pointer;

    font-size:19px;

    color:#78716C;

}


/* ==========================================
   PASSWORD REQUIREMENTS
========================================== */

.password-info{

    background:#FFFBF5;

    border:1px solid #F1F0EE;

    padding:16px;

    border-radius:14px;

    margin-bottom:22px;

}


.password-info h4{

    font-size:13px;

    margin-bottom:9px;

    color:#44403C;

}


.password-info p{

    font-size:12px;

    color:#78716C;

    margin:5px 0;

}


/* ==========================================
   BUTTON
========================================== */

.change-btn{

    width:100%;

    border:none;

    background:
    linear-gradient(
        90deg,
        #FF6B00,
        #FFB000
    );

    color:white;

    padding:14px 20px;

    border-radius:13px;

    font-size:15px;

    font-weight:bold;

    cursor:pointer;

    box-shadow:
    0 8px 20px
    rgba(255,107,0,.20);

    transition:.3s;

}


.change-btn:hover{

    transform:translateY(-3px);

    box-shadow:
    0 13px 28px
    rgba(255,107,0,.30);

}


/* ==========================================
   BACK BUTTON
========================================== */

.back-btn{

    display:block;

    width:max-content;

    margin:18px auto 0;

    text-decoration:none;

    color:#78716C;

    font-size:13px;

    transition:.3s;

}


.back-btn:hover{

    color:#FF6B00;

}


/* ==========================================
   FOOTER
========================================== */

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


/* ==========================================
   ANIMATION
========================================== */

@keyframes fadeUp{

    from{

        opacity:0;

        transform:translateY(20px);

    }

    to{

        opacity:1;

        transform:translateY(0);

    }

}


@keyframes floatIcon{

    0%,100%{

        transform:translateY(0);

    }

    50%{

        transform:translateY(-6px);

    }

}


/* ==========================================
   MOBILE
========================================== */

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

        flex-direction:column;

        align-items:flex-start;

        gap:18px;

        padding:25px;

    }


    .page-header h1{

        font-size:25px;

    }


    .password-card{

        padding:25px 20px;

    }

}

</style>

</head>


<body>


<!-- ==========================================
     HEADER
========================================== -->

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



<!-- ==========================================
     TEACHER MENU
========================================== -->

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

    <a href="profile.php">
        👤 My Profile
    </a>

    <a href="change_password.php" class="active">
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



<!-- ==========================================
     MAIN
========================================== -->

<div class="main">


    <!-- PAGE HEADER -->

    <div class="page-header">

        <div>

            <h1>
                🔐 Change Password
            </h1>

            <p>
                Update your teacher account password securely.
            </p>

        </div>


        <div class="password-badge">

            🛡️ Secure Account

        </div>

    </div>



    <!-- ======================================
         PASSWORD CARD
    ======================================= -->

    <div class="password-card">


        <div class="password-icon">

            🔑

        </div>


        <h2>
            Change Your Password
        </h2>


        <p class="description">

            Enter your current password and create a new
            password for your EduTrack teacher account.

        </p>



        <?php

        if($message != "")
        {

        ?>

        <div class="message <?php echo $message_type; ?>">

            <?php echo htmlspecialchars($message); ?>

        </div>

        <?php

        }

        ?>



        <form method="POST">


            <!-- OLD PASSWORD -->

            <div class="form-group">

                <label>
                    🔒 Current Password
                </label>

                <div class="password-input">

                    <input
                        type="password"
                        name="old_password"
                        id="old_password"
                        placeholder="Enter current password"
                        required
                    >

                    <button
                        type="button"
                        class="toggle-password"
                        onclick="togglePassword('old_password',this)"
                    >
                        👁️
                    </button>

                </div>

            </div>



            <!-- NEW PASSWORD -->

            <div class="form-group">

                <label>
                    🔑 New Password
                </label>

                <div class="password-input">

                    <input
                        type="password"
                        name="new_password"
                        id="new_password"
                        placeholder="Enter new password"
                        minlength="4"
                        required
                    >

                    <button
                        type="button"
                        class="toggle-password"
                        onclick="togglePassword('new_password',this)"
                    >
                        👁️
                    </button>

                </div>

            </div>



            <!-- CONFIRM PASSWORD -->

            <div class="form-group">

                <label>
                    🔐 Confirm New Password
                </label>

                <div class="password-input">

                    <input
                        type="password"
                        name="confirm_password"
                        id="confirm_password"
                        placeholder="Confirm new password"
                        minlength="4"
                        required
                    >

                    <button
                        type="button"
                        class="toggle-password"
                        onclick="togglePassword('confirm_password',this)"
                    >
                        👁️
                    </button>

                </div>

            </div>



            <!-- PASSWORD INFO -->

            <div class="password-info">

                <h4>
                    💡 Password Tips
                </h4>

                <p>
                    • Use at least 4 characters.
                </p>

                <p>
                    • Don't use an easy-to-guess password.
                </p>

                <p>
                    • Keep your password private.
                </p>

            </div>



            <!-- BUTTON -->

            <button
                type="submit"
                name="change"
                class="change-btn"
            >

                🔐 Change Password

            </button>


        </form>


        <a
            href="dashboard.php"
            class="back-btn"
        >
            ← Back to Dashboard
        </a>


    </div>


</div>



<!-- ==========================================
     FOOTER
========================================== -->

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



<script>

/* ==========================================
   SHOW / HIDE PASSWORD
========================================== */

function togglePassword(id,button)
{

    let input =
        document.getElementById(id);


    if(input.type === "password")
    {

        input.type = "text";

        button.innerHTML = "🙈";

    }
    else
    {

        input.type = "password";

        button.innerHTML = "👁️";

    }

}


/* ==========================================
   CONFIRM PASSWORD
========================================== */

document.querySelector("form").addEventListener(
    "submit",
    function(event)
    {

        let newPassword =
            document.getElementById("new_password").value;

        let confirmPassword =
            document.getElementById("confirm_password").value;


        if(newPassword !== confirmPassword)
        {

            event.preventDefault();

            alert(
                "New Password and Confirm Password do not match."
            );

        }

    }
);

</script>


</body>

</html>