<?php

include "db.php";

if(isset($_POST['register']))
{
    $name = mysqli_real_escape_string($conn,$_POST['name']);
    $email = mysqli_real_escape_string($conn,$_POST['email']);
    $phone = mysqli_real_escape_string($conn,$_POST['phone']);
    $gender = mysqli_real_escape_string($conn,$_POST['gender']);
    $course = mysqli_real_escape_string($conn,$_POST['course']);
    $semester = mysqli_real_escape_string($conn,$_POST['semester']);

    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];


    /* ================= PASSWORD CHECK ================= */

    if($password != $confirm)
    {
        echo "<script>
        alert('Password does not match');
        </script>";
    }
    else
    {
        $password = md5($password);


        /* ================= PHOTO UPLOAD ================= */

        $photo = "";

        if(isset($_FILES['photo']) && $_FILES['photo']['error'] == 0)
        {
            $extension = strtolower(
                pathinfo(
                    $_FILES['photo']['name'],
                    PATHINFO_EXTENSION
                )
            );

            $allow = array(
                "jpg",
                "jpeg",
                "png"
            );


            if(in_array($extension,$allow))
            {
                $photo = time().rand(1000,9999).".".$extension;


                if(!is_dir("uploads"))
                {
                    mkdir("uploads",0777,true);
                }


                move_uploaded_file(
                    $_FILES['photo']['tmp_name'],
                    "uploads/".$photo
                );
            }
            else
            {
                echo "<script>
                alert('Only JPG, JPEG and PNG files are allowed');
                </script>";

                exit();
            }
        }


        /* ================= EMAIL CHECK ================= */

        $check = mysqli_query(
            $conn,
            "SELECT * FROM students
             WHERE email='$email'"
        );


        if(mysqli_num_rows($check) > 0)
        {
            echo "<script>
            alert('Email Already Registered');
            </script>";
        }
        else
        {

            /* ================= INSERT ================= */

            $insert = mysqli_query(
                $conn,

                "INSERT INTO students
                (
                    name,
                    email,
                    password,
                    phone,
                    gender,
                    course,
                    semester,
                    photo
                )

                VALUES
                (
                    '$name',
                    '$email',
                    '$password',
                    '$phone',
                    '$gender',
                    '$course',
                    '$semester',
                    '$photo'
                )"
            );


            if($insert)
            {
                echo "<script>

                alert('Registration Successful');

                window.location='login.php';

                </script>";

                exit();
            }
            else
            {
                echo "<script>

                alert('Registration Failed');

                </script>";
            }

        }

    }

}

?>

<!DOCTYPE html>

<html>

<head>

<title>Student Registration - EduTrack</title>

<meta name="viewport" content="width=device-width, initial-scale=1.0">


<style>

/* ================= RESET ================= */

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}


/* ================= BODY ================= */

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


/* ================= REGISTER BOX ================= */

.box{

    width:500px;

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


/* ================= LOGO ================= */

.logo{

    text-align:center;

    font-size:31px;

    font-weight:bold;

    color:#1C1917;

    margin-bottom:5px;

}


.logo span{

    color:#FF6B00;

}


/* ================= TITLE ================= */

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


/* ================= FORM ================= */

.form-group{

    margin-bottom:15px;

}


.form-group label{

    display:block;

    margin-bottom:7px;

    color:#44403C;

    font-size:14px;

    font-weight:bold;

}


input,
select{

    width:100%;

    padding:13px 14px;

    border:1px solid #D6D3D1;

    border-radius:10px;

    outline:none;

    font-size:14px;

    background:white;

    color:#292524;

    transition:.3s;

}


input:focus,
select:focus{

    border-color:#FF8A00;

    box-shadow:
        0 0 0 3px rgba(255,138,0,.12);

}


/* ================= TWO COLUMN ================= */

.row{

    display:flex;

    gap:12px;

}


.row .form-group{

    flex:1;

}


/* ================= PHONE ================= */

.phone-info{

    font-size:11px;

    color:#A8A29E;

    margin-top:5px;

}


/* ================= PHOTO ================= */

.photo-box{

    background:#FFF7ED;

    border:2px dashed #FDBA74;

    border-radius:12px;

    padding:15px;

    text-align:center;

    transition:.3s;

}


.photo-box:hover{

    border-color:#FF6B00;

    background:#FFF1E6;

}


.photo-box input{

    border:none;

    background:transparent;

    padding:5px;

    box-shadow:none;

}


/* ================= BUTTON ================= */

button{

    width:100%;

    padding:14px;

    margin-top:8px;

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


button:hover{

    transform:translateY(-3px);

    box-shadow:
        0 12px 28px rgba(255,107,0,.35);

}


/* ================= LOGIN LINK ================= */

.bottom{

    text-align:center;

    margin-top:22px;

    padding-top:18px;

    border-top:1px solid #E7E5E4;

    color:#78716C;

    font-size:13px;

}


.bottom a{

    text-decoration:none;

    color:#FF6B00;

    font-weight:bold;

}


.bottom a:hover{

    color:#C2410C;

}


/* ================= RESPONSIVE ================= */

@media(max-width:550px){

    .box{

        width:100%;

        padding:25px 20px;

    }


    .row{

        flex-direction:column;

        gap:0;

    }


    .logo{

        font-size:28px;

    }

}

</style>

</head>


<body>


<!-- ================= REGISTER BOX ================= -->

<div class="box">


    <!-- LOGO -->

    <div class="logo">

        Edu<span>Track</span>

    </div>


    <!-- TITLE -->

    <div class="title">

        <h2>Student Registration 🎓</h2>

        <p>Create your student account</p>

    </div>


    <form
        method="POST"
        enctype="multipart/form-data"
    >


        <!-- NAME -->

        <div class="form-group">

            <label>
                Full Name
            </label>

            <input
                type="text"
                name="name"
                placeholder="Enter your full name"
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
                placeholder="Enter your email"
                required
            >

        </div>


        <!-- PHONE + GENDER -->

        <div class="row">


            <div class="form-group">

                <label>
                    Phone Number
                </label>

                <input
                    type="tel"
                    name="phone"
                    placeholder="10 digit number"
                    maxlength="10"
                    pattern="[0-9]{10}"
                    oninput="this.value=this.value.replace(/[^0-9]/g,'')"
                    title="Enter 10 digit mobile number"
                    required
                >

                <div class="phone-info">
                    Enter exactly 10 digits
                </div>

            </div>


            <div class="form-group">

                <label>
                    Gender
                </label>

                <select
                    name="gender"
                    required
                >

                    <option value="">
                        Select Gender
                    </option>

                    <option>
                        Male
                    </option>

                    <option>
                        Female
                    </option>

                    <option>
                        Other
                    </option>

                </select>

            </div>


        </div>


        <!-- COURSE -->

        <div class="form-group">

            <label>
                Course
            </label>

            <input
                type="text"
                name="course"
                placeholder="Enter your course"
                required
            >

        </div>


        <!-- SEMESTER -->

        <div class="form-group">

            <label>
                Semester
            </label>

            <select
                name="semester"
                required
            >

                <option value="">
                    Select Semester
                </option>

                <option>
                    Semester 1
                </option>

                <option>
                    Semester 2
                </option>

                <option>
                    Semester 3
                </option>

                <option>
                    Semester 4
                </option>

                <option>
                    Semester 5
                </option>

                <option>
                    Semester 6
                </option>

            </select>

        </div>


        <!-- PASSWORD -->

        <div class="row">


            <div class="form-group">

                <label>
                    Password
                </label>

                <input
                    type="password"
                    name="password"
                    placeholder="Create password"
                    required
                >

            </div>


            <div class="form-group">

                <label>
                    Confirm Password
                </label>

                <input
                    type="password"
                    name="confirm_password"
                    placeholder="Confirm password"
                    required
                >

            </div>


        </div>


        <!-- PHOTO -->

        <div class="form-group">

            <label>
                📷 Student Photo
            </label>

            <div class="photo-box">

                <input
                    type="file"
                    name="photo"
                    accept=".jpg,.jpeg,.png"
                    required
                >

                <p style="
                    font-size:11px;
                    color:#78716C;
                    margin-top:5px;
                ">

                    JPG, JPEG or PNG only

                </p>

            </div>

        </div>


        <!-- REGISTER BUTTON -->

        <button
            type="submit"
            name="register"
        >

            Create Student Account →

        </button>


    </form>


    <!-- LOGIN -->

    <div class="bottom">

        Already have an account?

        <a href="login.php">

            Login Here

        </a>

    </div>


</div>


</body>

</html>