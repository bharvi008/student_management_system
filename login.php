<?php

session_start();

include "db.php";


/* =========================
   LOGIN
========================= */

if(isset($_POST['login']))
{
    $email = mysqli_real_escape_string(
        $conn,
        $_POST['login_email']
    );

    $password = md5($_POST['password']);

    $type = $_POST['type'];


    /* =========================
       STUDENT LOGIN
    ========================= */

    if($type == "Student")
    {
        $sql = "SELECT * FROM students
                WHERE email='$email'
                AND password='$password'";

        $result = mysqli_query($conn,$sql);

        if($result && mysqli_num_rows($result) > 0)
        {
            $row = mysqli_fetch_assoc($result);

            $_SESSION['student_id'] = $row['id'];

            $_SESSION['student_name'] = $row['name'];

            header("Location: student/studentdashboard.php");

            exit();
        }
        else
        {
            echo "<script>
                    alert('Invalid Student Login');
                  </script>";
        }
    }


    /* =========================
       TEACHER LOGIN
    ========================= */

    if($type == "Teacher")
    {
        $sql = "SELECT * FROM teacher
                WHERE email='$email'
                AND password='$password'";

        $result = mysqli_query($conn,$sql);

        if($result && mysqli_num_rows($result) > 0)
        {
            $row = mysqli_fetch_assoc($result);

            $_SESSION['teacher_id'] = $row['id'];

            $_SESSION['teacher_name'] = $row['name'];

            header("Location: teacher/dashboard.php");

            exit();
        }
        else
        {
            echo "<script>
                    alert('Invalid Teacher Login');
                  </script>";
        }
    }
}

?>


<!DOCTYPE html>

<html>

<head>

<title>Login - EduTrack</title>

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>


<style>

/* =========================
   RESET
========================= */

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}


/* =========================
   BODY
========================= */

body{

    font-family:Arial,sans-serif;

    min-height:100vh;

    background:
    linear-gradient(
        rgba(28,25,23,.90),
        rgba(28,25,23,.94)
    );

    display:flex;

    justify-content:center;

    align-items:center;

    padding:30px 15px;
}


/* =========================
   LOGIN BOX
========================= */

.login-box{

    width:430px;

    background:#FFFBF5;

    padding:35px;

    border-radius:22px;

    border:1px solid #FED7AA;

    box-shadow:
        0 20px 50px rgba(0,0,0,.35);

    animation:fadeUp .7s ease;
}


@keyframes fadeUp{

    from{

        opacity:0;

        transform:translateY(30px);
    }

    to{

        opacity:1;

        transform:translateY(0);
    }
}


/* =========================
   LOGO
========================= */

.logo{

    text-align:center;

    font-size:32px;

    font-weight:bold;

    color:#1C1917;

    margin-bottom:5px;
}


.logo span{

    color:#FF6B00;
}


/* =========================
   TITLE
========================= */

.title{

    text-align:center;

    margin-bottom:25px;
}


.title h2{

    color:#292524;

    font-size:24px;

    margin-bottom:7px;
}


.title p{

    color:#78716C;

    font-size:13px;
}


/* =========================
   LOGIN TYPE
========================= */

.login-label{

    color:#44403C;

    font-size:14px;

    font-weight:bold;

    margin-bottom:10px;

    display:block;
}


.login-types{

    display:flex;

    gap:12px;

    margin-bottom:22px;
}


.type-card{

    flex:1;

    padding:17px 10px;

    background:white;

    border:2px solid #E7E5E4;

    border-radius:14px;

    text-align:center;

    cursor:pointer;

    transition:.3s;
}


.type-card:hover{

    border-color:#FFB000;

    transform:translateY(-3px);

    box-shadow:
        0 7px 18px rgba(255,107,0,.12);
}


.type-card.selected{

    border-color:#FF6B00;

    background:#FFF1E6;

    box-shadow:
        0 7px 18px rgba(255,107,0,.15);
}


.type-icon{

    font-size:30px;

    margin-bottom:7px;
}


.type-name{

    font-size:14px;

    font-weight:bold;

    color:#44403C;
}


.type-card.selected .type-name{

    color:#EA580C;
}


/* =========================
   INPUT
========================= */

.input-group{

    margin-bottom:16px;
}


.input-group label{

    display:block;

    color:#44403C;

    font-size:14px;

    font-weight:bold;

    margin-bottom:7px;
}


input{

    width:100%;

    padding:13px 15px;

    border:1px solid #D6D3D1;

    border-radius:10px;

    outline:none;

    font-size:14px;

    background:white;

    transition:.3s;
}


input:focus{

    border-color:#FF8A00;

    box-shadow:
        0 0 0 3px rgba(255,138,0,.12);
}


/* =========================
   LOGIN BUTTON
========================= */

.login-btn{

    width:100%;

    padding:14px;

    margin-top:5px;

    border:none;

    border-radius:11px;

    background:
        linear-gradient(
            90deg,
            #FF6B00,
            #FFB000
        );

    color:white;

    font-size:16px;

    font-weight:bold;

    cursor:pointer;

    box-shadow:
        0 8px 20px rgba(255,107,0,.25);

    transition:.3s;
}


.login-btn:hover{

    transform:translateY(-3px);

    box-shadow:
        0 12px 28px rgba(255,107,0,.35);
}


/* =========================
   MESSAGE
========================= */

.select-message{

    text-align:center;

    color:#A8A29E;

    font-size:12px;

    margin-top:-12px;

    margin-bottom:18px;
}


.select-message.active{

    color:#EA580C;

    font-weight:bold;
}


/* =========================
   REGISTER
========================= */

.register{

    text-align:center;

    margin-top:22px;

    font-size:13px;

    color:#78716C;
}


.register a{

    text-decoration:none;

    color:#FF6B00;

    font-weight:bold;
}


.register a:hover{

    color:#C2410C;
}


/* =========================
   MOBILE
========================= */

@media(max-width:500px){

    .login-box{

        width:100%;

        padding:25px 20px;
    }


    .logo{

        font-size:28px;
    }


    .login-types{

        gap:8px;
    }


    .type-card{

        padding:14px 8px;
    }

}

</style>

</head>


<body>


<div class="login-box">


    <!-- LOGO -->

    <div class="logo">

        Edu<span>Track</span>

    </div>


    <!-- TITLE -->

    <div class="title">

        <h2>Welcome Back 👋</h2>

        <p>Login to access your account</p>

    </div>


    <!-- LOGIN FORM -->

    <form
        method="POST"
        autocomplete="off"
        onsubmit="return validateLoginType()"
    >


        <!-- LOGIN TYPE -->

        <label class="login-label">

            Select Login Type

        </label>


        <div class="login-types">


            <!-- STUDENT -->

            <div
                class="type-card"
                id="studentCard"
                onclick="selectType('Student')"
            >

                <div class="type-icon">

                    🎓

                </div>

                <div class="type-name">

                    Student

                </div>

            </div>


            <!-- TEACHER -->

            <div
                class="type-card"
                id="teacherCard"
                onclick="selectType('Teacher')"
            >

                <div class="type-icon">

                    👨‍🏫

                </div>

                <div class="type-name">

                    Teacher

                </div>

            </div>


        </div>


        <!-- HIDDEN LOGIN TYPE -->

        <input
            type="hidden"
            name="type"
            id="loginType"
            value=""
        >


        <div
            class="select-message"
            id="selectMessage"
        >

            Please select Student or Teacher

        </div>


        <!-- EMAIL -->

        <div class="input-group">

            <label>

                Email

            </label>

            <input
                type="text"
                name="login_email"
                id="loginEmail"
                placeholder="Enter your email"
                autocomplete="off"
                required
            >

        </div>


        <!-- PASSWORD -->

        <div class="input-group">

            <label>

                Password

            </label>

            <input
                type="password"
                name="password"
                id="loginPassword"
                placeholder="Enter your password"
                autocomplete="new-password"
                required
            >

        </div>


        <!-- LOGIN BUTTON -->

        <button
            type="submit"
            name="login"
            class="login-btn"
        >

            Login →

        </button>


    </form>


    <!-- REGISTER -->

    <div class="register">

        Don't have an account?

        <a href="register.php">

            Register Here

        </a>

    </div>


</div>


<script>


/* =========================
   SELECT TYPE
========================= */

function selectType(type)
{

    document.getElementById("loginType").value = type;


    document.getElementById("studentCard")
        .classList.remove("selected");


    document.getElementById("teacherCard")
        .classList.remove("selected");


    if(type == "Student")
    {
        document.getElementById("studentCard")
            .classList.add("selected");
    }


    if(type == "Teacher")
    {
        document.getElementById("teacherCard")
            .classList.add("selected");
    }


    document.getElementById("selectMessage")
        .innerHTML = "Selected: " + type;


    document.getElementById("selectMessage")
        .classList.add("active");

}


/* =========================
   VALIDATION
========================= */

function validateLoginType()
{

    let type =
        document.getElementById("loginType").value;


    if(type == "")
    {

        alert("Please select Student or Teacher");

        return false;

    }


    return true;

}

</script>


</body>

</html>