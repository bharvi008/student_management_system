<?php
session_start();
include "../db.php";

if(!isset($_SESSION['teacher_id'])) {
    header("Location:../login.php");
    exit();
}

$id = intval($_SESSION['teacher_id']);
$data = mysqli_query($conn, "SELECT * FROM teacher WHERE id='$id' LIMIT 1");
$teacherData = $data ? mysqli_fetch_assoc($data) : null;
$teacherName = $teacherData ? ucwords(strtolower(trim($teacherData['name']))) : 'Teacher';
$teacherPhoto = ($teacherData && !empty($teacherData['photo'])) ? $teacherData['photo'] : 'default.png';

/* Course + semester selection */
if(isset($_POST['select_course'])) {
    $course_input = trim($_POST['course'] ?? '');
    $semester_input = trim($_POST['semester'] ?? '');
    $safe_course_input = mysqli_real_escape_string($conn, $course_input);
    $safe_semester_input = mysqli_real_escape_string($conn, $semester_input);

    $valid_selection = false;
    if($course_input !== '' && $semester_input !== '') {
        $check_selection = mysqli_query($conn, "SELECT id FROM students WHERE course='$safe_course_input' AND semester='$safe_semester_input' LIMIT 1");
        $valid_selection = $check_selection && mysqli_num_rows($check_selection) > 0;
    }

    if($valid_selection) {
        $_SESSION['teacher_course'] = $course_input;
        $_SESSION['teacher_semester'] = $semester_input;
    } else {
        unset($_SESSION['teacher_course'], $_SESSION['teacher_semester']);
        $_SESSION['teacher_selection_error'] = 'Please select a valid course and semester that has students.';
    }
    header("Location:dashboard.php");
    exit();
}

if(isset($_POST['clear_course'])) {
    unset($_SESSION['teacher_course'], $_SESSION['teacher_semester']);
    header("Location:dashboard.php");
    exit();
}

$selected_course = $_SESSION['teacher_course'] ?? '';
$selected_semester = $_SESSION['teacher_semester'] ?? '';
$selection_ready = ($selected_course !== '' && $selected_semester !== '');
$selection_error = $_SESSION['teacher_selection_error'] ?? '';
unset($_SESSION['teacher_selection_error']);

$course_list = mysqli_query($conn, "SELECT course_name FROM course ORDER BY course_name ASC");
$semester_list = mysqli_query($conn, "SELECT DISTINCT semester FROM students WHERE semester IS NOT NULL AND semester<>'' ORDER BY CAST(semester AS UNSIGNED), semester ASC");

/* Dashboard figures are restricted to selected course + semester */
$student_where = '';
$selected_filter = '';
if($selection_ready) {
    $safe_course = mysqli_real_escape_string($conn, $selected_course);
    $safe_semester = mysqli_real_escape_string($conn, $selected_semester);
    $student_where = " WHERE course='$safe_course' AND semester='$safe_semester' ";
    $selected_filter = " students.course='$safe_course' AND students.semester='$safe_semester' ";
}

$totalStudents = 0;
$totalResults = 0;
$present = 0;
$absent = 0;
$totalAttendance = 0;
if($selection_ready) {
    $q = mysqli_query($conn, "SELECT id FROM students $student_where");
    $totalStudents = $q ? mysqli_num_rows($q) : 0;

    $q = mysqli_query($conn, "SELECT result.id FROM result JOIN students ON result.student_id=students.id WHERE $selected_filter");
    $totalResults = $q ? mysqli_num_rows($q) : 0;

    $today = date('Y-m-d');
    $q = mysqli_query($conn, "SELECT attendance.id FROM attendance JOIN students ON attendance.student_id=students.id WHERE attendance.attendance_date='$today' AND attendance.status='Present' AND $selected_filter");
    $present = $q ? mysqli_num_rows($q) : 0;
    $q = mysqli_query($conn, "SELECT attendance.id FROM attendance JOIN students ON attendance.student_id=students.id WHERE attendance.attendance_date='$today' AND attendance.status='Absent' AND $selected_filter");
    $absent = $q ? mysqli_num_rows($q) : 0;
    $q = mysqli_query($conn, "SELECT attendance.id FROM attendance JOIN students ON attendance.student_id=students.id WHERE $selected_filter");
    $totalAttendance = $q ? mysqli_num_rows($q) : 0;
}
$totalTeachersQuery = mysqli_query($conn, "SELECT id FROM teacher");
$totalTeachers = $totalTeachersQuery ? mysqli_num_rows($totalTeachersQuery) : 0;
$totalCoursesQuery = mysqli_query($conn, "SELECT id FROM course");
$totalCourses = $totalCoursesQuery ? mysqli_num_rows($totalCoursesQuery) : 0;
?>


<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Teacher Dashboard | EduTrack</title>

<style>

/* ================= RESET ================= */

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    font-family:Arial, Helvetica, sans-serif;
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
    box-shadow:0 4px 20px rgba(0,0,0,0.15);
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
    color:#fff;
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

    box-shadow:0 10px 30px rgba(0,0,0,0.08);

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
    background:linear-gradient(
        90deg,
        #FF6B00,
        #FFB000
    );

    color:white;

    box-shadow:0 5px 15px rgba(255,107,0,0.25);
}

.teacher-menu .logout{
    color:#DC2626;
}

.teacher-menu .logout:hover{
    background:#FEE2E2;
    color:#DC2626;
}


/* ================= MAIN ================= */

.container{
    width:90%;
    max-width:1450px;
    margin:35px auto 60px;
}


/* ================= WELCOME ================= */

.welcome-card{

    background:linear-gradient(
        135deg,
        #1C1917,
        #292524
    );

    color:white;

    border-radius:25px;

    padding:30px 35px;

    display:flex;

    justify-content:space-between;

    align-items:center;

    margin-bottom:35px;

    box-shadow:0 15px 35px rgba(0,0,0,0.15);

    animation:fadeUp 0.7s ease;
}

.welcome-left h1{
    font-size:30px;
    margin-bottom:10px;
}

.welcome-left p{
    color:#D6D3D1;
    line-height:1.6;
    margin-bottom:15px;
}

.subject-badge{
    display:inline-block;

    background:#FF6B00;

    padding:8px 16px;

    border-radius:20px;

    font-size:13px;

    font-weight:bold;
}

.teacher-profile{
    display:flex;
    align-items:center;
    gap:15px;
}

.teacher-profile img{

    width:75px;
    height:75px;

    border-radius:50%;

    object-fit:cover;

    border:4px solid #FF8A00;

    background:white;
}

.teacher-profile h3{
    margin-bottom:5px;
}

.teacher-profile p{
    color:#D6D3D1;
    font-size:13px;
}


/* ================= TITLE ================= */

.section-title{
    margin-bottom:20px;
}

.section-title h2{
    font-size:27px;
    color:#1C1917;
}

.section-title p{
    color:#78716C;
    margin-top:5px;
}


/* ================= STAT CARDS ================= */

.stats-grid{

    display:grid;

    grid-template-columns:
        repeat(3,1fr);

    gap:27px;
}


/* IMPORTANT:
   CARD IS CLICKABLE
*/

.stat-card{

    position:relative;

    display:block;

    text-decoration:none;

    background:white;

    min-height:220px;

    padding:30px;

    border-radius:25px;

    overflow:hidden;

    border-top:5px solid #FF6B00;

    box-shadow:
        0 12px 30px rgba(0,0,0,0.08);

    transition:
        transform 0.3s ease,
        box-shadow 0.3s ease;

    animation:fadeUp 0.6s ease;

    cursor:pointer;
}

.stat-card:nth-child(2){
    border-top-color:#FF8A00;
}

.stat-card:nth-child(3){
    border-top-color:#FFB000;
}

.stat-card:nth-child(4){
    border-top-color:#FF6B00;
}

.stat-card:nth-child(5){
    border-top-color:#FF8A00;
}

.stat-card:nth-child(6){
    border-top-color:#FFB000;
}


/* Hover */

.stat-card:hover{

    transform:translateY(-8px);

    box-shadow:
        0 20px 40px rgba(0,0,0,0.13);
}


/* Arrow */

.card-arrow{

    position:absolute;

    right:25px;

    bottom:25px;

    font-size:27px;

    color:#FF6B00;

    transition:0.3s;
}

.stat-card:hover .card-arrow{
    transform:translateX(8px);
}


/* Icon */

.stat-icon{

    width:65px;
    height:65px;

    border-radius:18px;

    background:#FFF1E6;

    display:flex;

    align-items:center;

    justify-content:center;

    font-size:34px;

    margin-bottom:22px;
}


/* Number */

.stat-card h3{

    font-size:40px;

    color:#292524;

    margin-bottom:8px;
}

.stat-card p{

    color:#78716C;

    font-size:17px;

    font-weight:500;
}


/* ================= QUICK ACTION ================= */

.quick-section{
    margin-top:45px;
}

.quick-grid{

    display:grid;

    grid-template-columns:
        repeat(4,1fr);

    gap:20px;
}

.quick-card{

    text-decoration:none;

    background:white;

    padding:25px;

    border-radius:20px;

    text-align:center;

    color:#292524;

    box-shadow:0 8px 25px rgba(0,0,0,0.07);

    transition:0.3s;
}

.quick-card:hover{

    transform:translateY(-6px);

    box-shadow:
        0 15px 30px rgba(0,0,0,0.12);

    color:#FF6B00;
}

.quick-card .icon{
    font-size:35px;
    margin-bottom:12px;
}

.quick-card h3{
    font-size:16px;
}


/* ================= INFO ================= */

.info-box{

    margin-top:35px;

    background:
        linear-gradient(
            135deg,
            #FFF1E6,
            #FFF7ED
        );

    border-left:5px solid #FF6B00;

    padding:25px;

    border-radius:18px;
}

.info-box h3{
    margin-bottom:8px;
}

.info-box p{
    color:#78716C;
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


/* ================= COURSE SELECTOR ================= */

.course-selector{
    background:white;
    border-radius:25px;
    padding:30px;
    margin-bottom:35px;
    box-shadow:0 12px 30px rgba(0,0,0,.08);
    border:1px solid #F1F0EE;
}

.course-selector-header{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:20px;
    margin-bottom:20px;
}

.course-selector-header h2{
    font-size:23px;
    color:#1C1917;
}

.course-selector-header p{
    color:#78716C;
    margin-top:6px;
    font-size:14px;
}

.course-form{
    display:flex;
    gap:12px;
    align-items:center;
    flex-wrap:wrap;
}

.course-form select{
    flex:1;
    min-width:260px;
    padding:14px 16px;
    border:1px solid #E7E5E4;
    border-radius:12px;
    background:#FFFBF5;
    font-size:15px;
    color:#292524;
    outline:none;
}

.course-form select:focus{
    border-color:#FF8A00;
    box-shadow:0 0 0 3px rgba(255,138,0,.10);
}

.course-btn{
    border:none;
    background:linear-gradient(90deg,#FF6B00,#FFB000);
    color:white;
    padding:14px 22px;
    border-radius:12px;
    font-weight:bold;
    cursor:pointer;
}

.clear-course{
    display:inline-block;
    text-decoration:none;
    color:#DC2626;
    background:#FEE2E2;
    padding:13px 18px;
    border-radius:12px;
    font-weight:bold;
}

.selected-course-badge{
    display:inline-flex;
    align-items:center;
    gap:8px;
    background:#FFF1E6;
    color:#C2410C;
    padding:9px 15px;
    border-radius:20px;
    font-size:13px;
    font-weight:bold;
}

.course-empty{
    background:linear-gradient(135deg,#FFF1E6,#FFF7ED);
    border-left:5px solid #FF6B00;
    padding:22px;
    border-radius:16px;
    color:#57534E;
    line-height:1.6;
}

@media(max-width:700px){
    .course-selector{
        padding:22px;
    }

    .course-form select{
        min-width:100%;
    }

    .course-btn,
    .clear-course{
        width:100%;
        text-align:center;
    }

    .course-selector-header{
        flex-direction:column;
        align-items:flex-start;
    }
}


/* ================= ANIMATION ================= */

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


/* ================= RESPONSIVE ================= */

@media(max-width:1000px){

    .stats-grid{
        grid-template-columns:repeat(2,1fr);
    }

    .quick-grid{
        grid-template-columns:repeat(2,1fr);
    }

}

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

    .welcome-card{
        flex-direction:column;
        align-items:flex-start;
        gap:25px;
    }

    .stats-grid{
        grid-template-columns:1fr;
    }

    .quick-grid{
        grid-template-columns:1fr;
    }

    .container{
        width:92%;
    }

}


/* ================= MOBILE ================= */

@media(max-width:450px){

    .welcome-left h1{
        font-size:24px;
    }

    .stat-card{
        min-height:200px;
    }

    .stat-card h3{
        font-size:34px;
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

    <a href="dashboard.php" class="active">
        🏠 Overview
    </a>

    <?php if($selection_ready){ ?>

        <a href="students.php">
            👨‍🎓 Students
        </a>

        <a href="attendance.php">
            📅 Attendance
        </a>

        <a href="result.php">
            📊 Result
        </a>

    <?php } ?>

    <a href="course.php">
        📚 Course
    </a>

    <a href="profile.php">
        👤 My Profile
    </a>

    <a href="change_password.php">
        🔑 Password
    </a>

    <a href="logout.php"
       class="logout"
       onclick="return confirm('Are you sure you want to logout?')">

        🚪 Logout

    </a>

</nav>




<!-- ================= MAIN ================= -->

<div class="container">


    <!-- ================= WELCOME ================= -->

    <div class="welcome-card">

        <div class="welcome-left">

            <h1>
                Welcome, <?php echo htmlspecialchars($teacherName); ?> 👋
            </h1>

            <p>
                Manage students, attendance, results and courses
                easily from your teacher dashboard.
            </p>

            <?php if(!empty($teacherData['subject'])) { ?>

                <span class="subject-badge">

                    📚
                    <?php
                    echo htmlspecialchars(
                        $teacherData['subject']
                    );
                    ?>

                </span>

            <?php } ?>

            <?php if($selection_ready){ ?>

                <span class="selected-course-badge">
                    🎓 Selected Course:
                    <?php echo htmlspecialchars($selected_course); ?> — Semester <?php echo htmlspecialchars($selected_semester); ?>
                </span>

            <?php } ?>

        </div>


        <div class="teacher-profile">

            <img
                src="../uploads/<?php echo htmlspecialchars($teacherPhoto); ?>"
                alt="Teacher Photo"
            >

            <div>

                <h3>
                    <?php
                    echo htmlspecialchars($teacherName);
                    ?>
                </h3>

                <p>
                    Teacher
                </p>

            </div>

        </div>

    </div>



    <!-- ================= COURSE SELECTION ================= -->

    <div class="course-selector">

        <div class="course-selector-header">

            <div>
                <h2>🎓 Select Course & Semester</h2>
                <p>
                    Select both course and semester. Students, Attendance and Result will be filtered to this selection.
                </p>
            </div>

            <?php if($selection_ready){ ?>

                <span class="selected-course-badge">
                    Active: <?php echo htmlspecialchars($selected_course); ?> — Semester <?php echo htmlspecialchars($selected_semester); ?>
                </span>

            <?php } ?>

        </div>

        <?php if($selection_error != '') { ?>
            <p style="color:#DC2626;font-weight:bold;margin:10px 0;"><?php echo htmlspecialchars($selection_error); ?></p>
        <?php } ?>

        <form method="POST" class="course-form">

            <select name="course" required>

                <option value="">
                    -- Select Course --
                </option>

                <?php
                if($course_list)
                {
                    while($course = mysqli_fetch_assoc($course_list))
                    {
                ?>

                    <option
                        value="<?php echo htmlspecialchars($course['course_name']); ?>"
                        <?php
                        if($selected_course == $course['course_name'])
                        {
                            echo "selected";
                        }
                        ?>
                    >
                        <?php echo htmlspecialchars($course['course_name']); ?>
                    </option>

                <?php
                    }
                }
                ?>

            </select>

            <select name="semester" required>
                <option value="">-- Select Semester --</option>
                <?php
                if($semester_list) {
                    while($semester_row = mysqli_fetch_assoc($semester_list)) {
                        $semester_value = (string)$semester_row['semester'];
                ?>
                    <option value="<?php echo htmlspecialchars($semester_value); ?>" <?php echo ($selected_semester === $semester_value) ? 'selected' : ''; ?>>
                        Semester <?php echo htmlspecialchars($semester_value); ?>
                    </option>
                <?php
                    }
                }
                ?>
            </select>

            <button
                type="submit"
                name="select_course"
                class="course-btn"
            >
                ✓ Apply Selection
            </button>

            <?php if($selection_ready){ ?>

                <button
                    type="submit"
                    name="clear_course"
                    class="clear-course"
                >
                    ✕ Clear Selection
                </button>

            <?php } ?>

        </form>

    </div>


    <?php if(!$selection_ready){ ?>

        <div class="course-empty">

            <strong>Course and semester not selected.</strong><br>

            Please select both course and semester above to access <strong>Students, Attendance and Result</strong>.

        </div>

    <?php } else { ?>


    <!-- ================= DASHBOARD OVERVIEW ================= -->

    <div class="section-title">

        <h2>
            📈 Dashboard Overview
        </h2>

        <p>
            Quick overview of your academic management system
        </p>

    </div>



    <!-- ================= CLICKABLE CARDS ================= -->

    <div class="stats-grid">


        <!-- PRESENT TODAY -->

        <a href="attendance.php" class="stat-card">

            <div class="stat-icon">
                ☑️
            </div>

            <h3>
                <?php echo $present; ?>
            </h3>

            <p>
                Present Today
            </p>

            <span class="card-arrow">
                →
            </span>

        </a>



        <!-- ABSENT TODAY -->

        <a href="attendance.php" class="stat-card">

            <div class="stat-icon">
                ❌
            </div>

            <h3>
                <?php echo $absent; ?>
            </h3>

            <p>
                Absent Today
            </p>

            <span class="card-arrow">
                →
            </span>

        </a>



        <!-- TOTAL STUDENTS -->

        <a href="students.php" class="stat-card">

            <div class="stat-icon">
                🎓
            </div>

            <h3>
                <?php echo $totalStudents; ?>
            </h3>

            <p>
                Total Students
            </p>

            <span class="card-arrow">
                →
            </span>

        </a>



        <!-- ATTENDANCE RECORDS -->

        <a href="attendance.php" class="stat-card">

            <div class="stat-icon">
                🗓️
            </div>

            <h3>
                <?php echo $totalAttendance; ?>
            </h3>

            <p>
                Attendance Records
            </p>

            <span class="card-arrow">
                →
            </span>

        </a>



        <!-- RESULT RECORDS -->

        <a href="result.php" class="stat-card">

            <div class="stat-icon">
                📊
            </div>

            <h3>
                <?php echo $totalResults; ?>
            </h3>

            <p>
                Result Records
            </p>

            <span class="card-arrow">
                →
            </span>

        </a>



        <!-- AVAILABLE COURSES -->

        <a href="course.php" class="stat-card">

            <div class="stat-icon">
                📚
            </div>

            <h3>
                <?php echo $totalCourses; ?>
            </h3>

            <p>
                Available Courses
            </p>

            <span class="card-arrow">
                →
            </span>

        </a>


    </div>



    <!-- ================= QUICK ACTION ================= -->

    <div class="quick-section">

        <div class="section-title">

            <h2>
                ⚡ Quick Actions
            </h2>

            <p>
                Quickly access important teacher functions
            </p>

        </div>


        <div class="quick-grid">


            <a href="students.php" class="quick-card">

                <div class="icon">
                    👨‍🎓
                </div>

                <h3>
                    View Students
                </h3>

            </a>


            <a href="attendance.php" class="quick-card">

                <div class="icon">
                    📅
                </div>

                <h3>
                    Manage Attendance
                </h3>

            </a>


            <a href="result.php" class="quick-card">

                <div class="icon">
                    📊
                </div>

                <h3>
                    Manage Results
                </h3>

            </a>


            <a href="course.php" class="quick-card">

                <div class="icon">
                    📚
                </div>

                <h3>
                    View Courses
                </h3>

            </a>


        </div>

    </div>



    <?php } ?>


    <!-- ================= INFO ================= -->

    <div class="info-box">

        <h3>
            💡 Teacher Dashboard
        </h3>

        <p>
            Select a course first, then manage only that course's
            students, attendance records and examination results.
            This keeps the teacher panel focused and organized.
        </p>

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