<?php

session_start();

if(!isset($_SESSION['student_id']))
{
    header("Location:../login.php");
    exit();
}

include "../db.php";

$student_id = $_SESSION['student_id'];


/* ================= STUDENT DATA ================= */

$student_query = mysqli_query($conn,"
    SELECT *
    FROM students
    WHERE id='$student_id'
");

$student = mysqli_fetch_assoc($student_query);

if(!$student)
{
    echo "Student not found.";
    exit();
}


/* ================= SUBMIT REQUEST ================= */

if(isset($_POST['submit_request']))
{

    $request_type = mysqli_real_escape_string(
        $conn,
        $_POST['request_type']
    );

    $new_value = trim($_POST['new_value']);

    $reason = trim($_POST['reason']);


    /* ================= OLD VALUE ================= */

    $old_value = "";


    if($request_type == "Phone Number")
    {
        $old_value = $student['phone'];
    }
    elseif($request_type == "Email")
    {
        $old_value = $student['email'];
    }
    elseif($request_type == "Name")
    {
        $old_value = $student['name'];
    }
    elseif($request_type == "Semester")
    {
        $old_value = $student['semester'];
    }
    else
    {
        echo "<script>
        alert('Invalid request type');
        window.location='request.php';
        </script>";
        exit();
    }


    /* ================= VALIDATION ================= */

    if($new_value == "")
    {

        echo "<script>
        alert('Please enter new value');
        </script>";

    }
    else
    {


        /* ================= PHONE VALIDATION ================= */

        if($request_type == "Phone Number")
        {

            if(!preg_match('/^[0-9]{10}$/', $new_value))
            {

                echo "<script>
                alert('Phone number must be exactly 10 digits');
                </script>";

            }
            else
            {

                $new_value = mysqli_real_escape_string(
                    $conn,
                    $new_value
                );

                $reason = mysqli_real_escape_string(
                    $conn,
                    $reason
                );

                $old_value = mysqli_real_escape_string(
                    $conn,
                    $old_value
                );


                mysqli_query($conn,"
                    INSERT INTO student_requests
                    (
                        student_id,
                        request_type,
                        old_value,
                        new_value,
                        reason
                    )
                    VALUES
                    (
                        '$student_id',
                        '$request_type',
                        '$old_value',
                        '$new_value',
                        '$reason'
                    )
                ");


                echo "<script>
                alert('Request submitted successfully');
                window.location='request.php';
                </script>";

            }

        }
        else
        {

            $new_value = mysqli_real_escape_string(
                $conn,
                $new_value
            );

            $reason = mysqli_real_escape_string(
                $conn,
                $reason
            );

            $old_value = mysqli_real_escape_string(
                $conn,
                $old_value
            );


            /* ================= INSERT REQUEST ================= */

            mysqli_query($conn,"
                INSERT INTO student_requests
                (
                    student_id,
                    request_type,
                    old_value,
                    new_value,
                    reason
                )
                VALUES
                (
                    '$student_id',
                    '$request_type',
                    '$old_value',
                    '$new_value',
                    '$reason'
                )
            ");


            echo "<script>
            alert('Request submitted successfully');
            window.location='request.php';
            </script>";

        }

    }

}


/* ================= REQUEST HISTORY ================= */

$requests = mysqli_query($conn,"
    SELECT *
    FROM student_requests
    WHERE student_id='$student_id'
    ORDER BY id DESC
");

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Request Approval - EduTrack</title>


<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{

    font-family:Arial,sans-serif;

    background:
        linear-gradient(
            135deg,
            #FFF7ED,
            #FFFBF5
        );

    color:#292524;

    min-height:100vh;

}


/* ================= HEADER ================= */

.header{

    width:100%;

    height:75px;

    background:
        linear-gradient(
            135deg,
            #1C1917,
            #292524
        );

    display:flex;

    align-items:center;

    justify-content:space-between;

    padding:0 45px;

    box-shadow:
        0 5px 25px rgba(0,0,0,.20);

}


.logo a{

    color:white;

    text-decoration:none;

    font-size:27px;

    font-weight:700;

}

.logo span{

    color:#FF8A00;

}


.student-info{

    color:white;

    font-size:14px;

}


/* ================= CONTAINER ================= */

.container{

    width:94%;

    max-width:1200px;

    margin:35px auto 50px;

}


/* ================= TITLE ================= */

.page-title{

    margin-bottom:25px;

}

.page-title h1{

    font-size:30px;

    color:#292524;

    margin-bottom:7px;

}

.page-title p{

    color:#78716C;

    font-size:14px;

}


/* ================= GRID ================= */

.content-grid{

    display:grid;

    grid-template-columns:380px 1fr;

    gap:25px;

    align-items:start;

}


/* ================= CARD ================= */

.card{

    background:#FFFFFF;

    border-radius:20px;

    padding:25px;

    box-shadow:
        0 10px 35px rgba(41,37,36,.10);

    border:1px solid #E7E5E4;

}


/* ================= CARD HEADER ================= */

.card-header{

    display:flex;

    align-items:center;

    gap:12px;

    padding-bottom:18px;

    margin-bottom:20px;

    border-bottom:1px solid #E7E5E4;

}

.card-icon{

    width:45px;

    height:45px;

    border-radius:12px;

    background:#FFF7ED;

    display:flex;

    align-items:center;

    justify-content:center;

    font-size:22px;

}

.card-header h2{

    font-size:18px;

}

.card-header p{

    color:#78716C;

    font-size:11px;

    margin-top:4px;

}


/* ================= FORM ================= */

.form-group{

    margin-bottom:18px;

}

label{

    display:block;

    font-size:13px;

    font-weight:bold;

    margin-bottom:8px;

    color:#44403C;

}

select,
input,
textarea{

    width:100%;

    border:1px solid #D6D3D1;

    border-radius:10px;

    padding:12px 13px;

    background:#FAFAF9;

    font-size:14px;

    color:#292524;

    outline:none;

}

select:focus,
input:focus,
textarea:focus{

    border-color:#FF8A00;

    background:#FFFFFF;

    box-shadow:
        0 0 0 3px rgba(255,138,0,.10);

}

textarea{

    height:90px;

    resize:none;

}


.submit-btn{

    width:100%;

    border:none;

    border-radius:10px;

    padding:13px;

    background:
        linear-gradient(
            90deg,
            #FF6B00,
            #FFB000
        );

    color:white;

    font-size:14px;

    font-weight:bold;

    cursor:pointer;

    transition:.3s;

}

.submit-btn:hover{

    transform:translateY(-2px);

    box-shadow:
        0 8px 20px rgba(255,107,0,.25);

}


/* ================= CURRENT VALUE ================= */

.current-box{

    background:#F7F7F5;

    border:1px solid #E7E5E4;

    border-radius:10px;

    padding:12px;

    margin-bottom:18px;

}

.current-box span{

    display:block;

    font-size:11px;

    color:#78716C;

    margin-bottom:5px;

}

.current-box strong{

    font-size:14px;

}


/* ================= TABLE ================= */

.table-wrapper{

    overflow-x:auto;

}

table{

    width:100%;

    border-collapse:collapse;

    min-width:700px;

}

th{

    background:#292524;

    color:white;

    padding:14px 10px;

    font-size:11px;

    text-transform:uppercase;

}

td{

    padding:14px 10px;

    border-bottom:1px solid #E7E5E4;

    text-align:center;

    font-size:13px;

}

tr:hover{

    background:#FFF7ED;

}


/* ================= TYPE ================= */

.request-type{

    display:inline-block;

    padding:6px 10px;

    background:#FFF7ED;

    color:#EA580C;

    border-radius:20px;

    font-size:11px;

    font-weight:bold;

}


/* ================= STATUS ================= */

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


/* ================= EMPTY ================= */

.empty{

    padding:45px 20px;

    text-align:center;

    color:#78716C;

}

.empty-icon{

    font-size:42px;

    margin-bottom:10px;

}


/* ================= BACK ================= */

.back-btn{

    display:inline-block;

    margin-top:25px;

    padding:11px 18px;

    background:#292524;

    color:white;

    text-decoration:none;

    border-radius:10px;

    font-size:13px;

    transition:.3s;

}

.back-btn:hover{

    background:#FF6B00;

}


/* ================= RESPONSIVE ================= */

@media(max-width:900px){

    .content-grid{

        grid-template-columns:1fr;

    }

}


@media(max-width:600px){

    .header{

        padding:0 20px;

    }

    .container{

        width:94%;

    }

    .page-title h1{

        font-size:25px;

    }

}

</style>

</head>


<body>


<!-- ================= HEADER ================= -->

<header class="header">


    <div class="logo">

        <a href="studentdashboard.php">

            Edu<span>Track</span>

        </a>

    </div>


    <div class="student-info">

        👤

        <?php

        echo htmlspecialchars($student['name']);

        ?>

    </div>


</header>



<!-- ================= MAIN ================= -->

<div class="container">


    <div class="page-title">

        <h1>

            📝 Request Approval

        </h1>

        <p>

            Request changes to your student information

        </p>

    </div>



    <div class="content-grid">


        <!-- ================= NEW REQUEST ================= -->

        <div class="card">


            <div class="card-header">

                <div class="card-icon">

                    📝

                </div>

                <div>

                    <h2>

                        New Request

                    </h2>

                    <p>

                        Send request to teacher

                    </p>

                </div>

            </div>



            <form method="POST">


                <!-- CURRENT VALUE -->

                <div class="current-box">

                    <span>

                        Your Current Information

                    </span>

                    <strong>

                        Select a request type

                    </strong>

                </div>



                <!-- REQUEST TYPE -->

                <div class="form-group">

                    <label>

                        What do you want to change?

                    </label>


                    <select
                        name="request_type"
                        id="request_type"
                        required
                        onchange="showCurrentValue()"
                    >

                        <option value="">

                            Select Request Type

                        </option>

                        <option value="Phone Number">

                            📱 Phone Number

                        </option>

                        <option value="Email">

                            📧 Email

                        </option>

                        <option value="Name">

                            👤 Name

                        </option>

                        <option value="Semester">

                            🎓 Semester

                        </option>

                    </select>

                </div>



                <!-- NEW VALUE -->

                <div class="form-group">

                    <label>

                        New Value

                    </label>


                    <input
                        type="text"
                        name="new_value"
                        id="new_value"
                        placeholder="Enter new value"
                        required
                    >

                </div>



                <!-- REASON -->

                <div class="form-group">

                    <label>

                        Reason

                    </label>


                    <textarea
                        name="reason"
                        placeholder="Enter reason for this change..."
                    ></textarea>

                </div>



                <button
                    type="submit"
                    name="submit_request"
                    class="submit-btn"
                >

                    📤 Send Request

                </button>


            </form>


        </div>



        <!-- ================= REQUEST HISTORY ================= -->

        <div class="card">


            <div class="card-header">

                <div class="card-icon">

                    📋

                </div>

                <div>

                    <h2>

                        My Requests

                    </h2>

                    <p>

                        Check your request status

                    </p>

                </div>

            </div>



            <div class="table-wrapper">


            <?php

            if(mysqli_num_rows($requests) > 0)
            {

            ?>


            <table>


                <tr>

                    <th>
                        Request
                    </th>

                    <th>
                        Current
                    </th>

                    <th>
                        New
                    </th>

                    <th>
                        Status
                    </th>

                    <th>
                        Date
                    </th>

                </tr>


                <?php

                while($req=mysqli_fetch_assoc($requests))
                {

                ?>


                <tr>


                    <td>

                        <span class="request-type">

                            <?php

                            echo htmlspecialchars(
                                $req['request_type']
                            );

                            ?>

                        </span>

                    </td>


                    <td>

                        <?php

                        echo htmlspecialchars(
                            $req['old_value']
                        );

                        ?>

                    </td>


                    <td>

                        <strong>

                            <?php

                            echo htmlspecialchars(
                                $req['new_value']
                            );

                            ?>

                        </strong>

                    </td>


                    <td>


                        <?php

                        if($req['status']=="Pending")
                        {

                        ?>

                            <span class="status pending">

                                🟡 Pending

                            </span>

                        <?php

                        }
                        elseif($req['status']=="Approved")
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


                    <td>

                        <?php

                        echo date(
                            "d M Y",
                            strtotime($req['created_at'])
                        );

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

                        No Requests Yet

                    </h3>

                    <p>

                        Your submitted requests will appear here.

                    </p>

                </div>


            <?php

            }

            ?>


            </div>


        </div>


    </div>



    <a
        href="studentdashboard.php"
        class="back-btn"
    >

        ← Back to Dashboard

    </a>


</div>



<script>


/* ================= STUDENT CURRENT VALUES ================= */

const studentData = {

    phone:
        "<?php echo htmlspecialchars($student['phone'] ?? '', ENT_QUOTES); ?>",

    email:
        "<?php echo htmlspecialchars($student['email'] ?? '', ENT_QUOTES); ?>",

    name:
        "<?php echo htmlspecialchars($student['name'] ?? '', ENT_QUOTES); ?>",
semester:
        "<?php echo htmlspecialchars($student['semester'] ?? '', ENT_QUOTES); ?>"

};


/* ================= SHOW CURRENT VALUE ================= */

function showCurrentValue()
{

    const type =
        document.getElementById("request_type").value;

    const box =
        document.querySelector(".current-box strong");

    const input =
        document.getElementById("new_value");


    if(type === "Phone Number")
    {

        box.innerHTML =
            "📱 " + studentData.phone;

        input.placeholder =
            "Enter 10 digit phone number";

        input.maxLength = 10;

    }
    else if(type === "Email")
    {

        box.innerHTML =
            "📧 " + studentData.email;

        input.placeholder =
            "Enter new email";

        input.removeAttribute("maxlength");

    }
    else if(type === "Name")
    {

        box.innerHTML =
            "👤 " + studentData.name;

        input.placeholder =
            "Enter new name";

        input.removeAttribute("maxlength");

    }
    else if(type === "Semester")
    {

        box.innerHTML =
            "🎓 " + studentData.semester;

        input.placeholder =
            "Enter new semester";

        input.removeAttribute("maxlength");

    }
    else
    {

        box.innerHTML =
            "Select a request type";

        input.placeholder =
            "Enter new value";

    }

}

</script>


</body>

</html>