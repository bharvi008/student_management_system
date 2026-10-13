<?php

session_start();

include "../db.php";

if(!isset($_SESSION['teacher_id']))
{
    header("Location:../login.php");
    exit();
}

$teacher_id = $_SESSION['teacher_id'];


/* =====================================================
   SELECTED COURSE
===================================================== */

if(!isset($_SESSION['teacher_course'], $_SESSION['teacher_semester']) || trim($_SESSION['teacher_course']) == "" || trim((string)$_SESSION['teacher_semester']) == "") {
    header("Location:dashboard.php");
    exit();
}

$selected_course = $_SESSION['teacher_course'];
$selected_semester = (string)$_SESSION['teacher_semester'];
$safe_course = mysqli_real_escape_string($conn, $selected_course);
$safe_semester = mysqli_real_escape_string($conn, $selected_semester);


/* =====================================================
   SUBJECT SELECTION
===================================================== */

$subject_option = trim($_GET['subject'] ?? '');
$new_subject = trim($_GET['new_subject'] ?? '');

$active_subject = '';

if ($subject_option === '__new__') {
    $active_subject = $new_subject;
} elseif ($subject_option !== '') {
    $active_subject = $subject_option;
}

$safe_active_subject = mysqli_real_escape_string(
    $conn,
    $active_subject
);


/* =====================================================
   SAVE MARKS FOR ALL STUDENTS IN SELECTED SUBJECT
===================================================== */

if (isset($_POST['save_all'])) {

    $subject = trim($_POST['subject'] ?? '');
    $safe_subject = mysqli_real_escape_string($conn, $subject);
    $marks_list = $_POST['marks'] ?? array();

    if ($subject === '') {
        echo "<script>alert('Please select a subject first.'); window.location='result.php';</script>";
        exit();
    }

    if (!is_array($marks_list)) {
        $marks_list = array();
    }

    $saved_count = 0;

    foreach ($marks_list as $student_id => $mark_value) {

        $student_id = intval($student_id);

        /* Skip blank marks */
        if ($mark_value === '' || $mark_value === null) {
            continue;
        }

        if (!is_numeric($mark_value)) {
            continue;
        }

        $marks = floatval($mark_value);

        if ($marks < 0 || $marks > 100) {
            continue;
        }

        /* Verify student belongs to selected course and semester */
        $student_check = mysqli_query(
            $conn,
            "SELECT id
             FROM students
             WHERE id='$student_id'
             AND course='$safe_course'
             AND semester='$safe_semester'
             LIMIT 1"
        );

        if (!$student_check || mysqli_num_rows($student_check) === 0) {
            continue;
        }

        $total = 100;
        $percentage = $marks;

        /* Check whether this student already has this subject result */
        $check = mysqli_query(
            $conn,
            "SELECT id
             FROM result
             WHERE student_id='$student_id'
             AND subject='$safe_subject'
             LIMIT 1"
        );

        if ($check && mysqli_num_rows($check) > 0) {

            mysqli_query(
                $conn,
                "UPDATE result
                 SET marks='$marks',
                     total='$total',
                     percentage='$percentage'
                 WHERE student_id='$student_id'
                 AND subject='$safe_subject'"
            );

        } else {

            mysqli_query(
                $conn,
                "INSERT INTO result
                    (student_id, subject, marks, total, percentage)
                 VALUES
                    ('$student_id', '$safe_subject', '$marks', '$total', '$percentage')"
            );

        }

        $saved_count++;
    }

    $redirect_subject = urlencode($subject);

    echo "<script>
        alert('Marks saved successfully for $saved_count student(s).');
        window.location='result.php?subject=$redirect_subject';
    </script>";
    exit();
}


/* =====================================================
   SUBJECT OPTIONS ALREADY USED IN THIS COURSE/SEMESTER
===================================================== */

$subject_query = mysqli_query(
    $conn,
    "SELECT DISTINCT result.subject
     FROM result
     INNER JOIN students
        ON result.student_id = students.id
     WHERE students.course='$safe_course'
     AND students.semester='$safe_semester'
     AND result.subject <> ''
     ORDER BY result.subject ASC"
);

$subject_options = array();

if ($subject_query) {
    while ($subject_row = mysqli_fetch_assoc($subject_query)) {
        $subject_options[] = $subject_row['subject'];
    }
}

/* Include a selected custom subject in the dropdown if needed */
if (
    $active_subject !== '' &&
    $subject_option !== '__new__' &&
    !in_array($active_subject, $subject_options, true)
) {
    $subject_options[] = $active_subject;
    sort($subject_options);
}


/* =====================================================
   STUDENTS AND EXISTING MARKS FOR SELECTED SUBJECT
===================================================== */

$students_for_subject = false;

if ($active_subject !== '') {

    $students_for_subject = mysqli_query(
        $conn,
        "SELECT
            s.id,
            s.name,
            s.email,
            s.phone,
            s.course,
            s.semester,
            s.photo,
            (
                SELECT r.marks
                FROM result r
                WHERE r.student_id = s.id
                AND r.subject = '$safe_active_subject'
                ORDER BY r.id DESC
                LIMIT 1
            ) AS existing_marks
         FROM students s
         WHERE s.course='$safe_course'
         AND s.semester='$safe_semester'
         ORDER BY s.name ASC"
    );
}


/* =====================================================
   GET STUDENTS
===================================================== */
/* =====================================================
   GET STUDENTS
===================================================== */

$student = mysqli_query($conn,"
    SELECT *
    FROM students
    WHERE course='$safe_course' AND semester='$safe_semester'
    ORDER BY name ASC
");


/* =====================================================
   GET RESULTS
===================================================== */

$result = mysqli_query($conn,"
    SELECT
        result.*,
        students.name,
        students.course,
        students.semester,
        students.photo

    FROM result

    JOIN students
    ON result.student_id = students.id

    WHERE students.course='$safe_course' AND students.semester='$safe_semester'

    ORDER BY result.id DESC
");


/* =====================================================
   STORE RESULT DATA
===================================================== */

$result_rows = array();

$pass_count = 0;
$fail_count = 0;

while($result_row = mysqli_fetch_assoc($result))
{

    $result_rows[] = $result_row;

    $marks_value =
        floatval($result_row['marks']);


    /* PASS if marks >= 40 */

    if($marks_value >= 40)
    {
        $pass_count++;
    }
    else
    {
        $fail_count++;
    }

}


$total_results = count($result_rows);

/* =====================================================
   STUDENT-WISE OVERALL RESULT
===================================================== */

$student_overall = array();

foreach($result_rows as $overall_row)
{
    $sid = $overall_row['student_id'];

    if(!isset($student_overall[$sid]))
    {
        $student_overall[$sid] = array(
            "name" => $overall_row['name'],
            "course" => $overall_row['course'],
            "semester" => $overall_row['semester'],
            "photo" => $overall_row['photo'],
            "marks" => 0,
            "total" => 0,
            "subjects" => 0
        );
    }

    $marks = floatval($overall_row['marks']);
    $total = floatval($overall_row['total']);

    if($total <= 0)
    {
        $total = 100;
    }

    $student_overall[$sid]["marks"] += $marks;
    $student_overall[$sid]["total"] += $total;
    $student_overall[$sid]["subjects"]++;
}

/* Calculate percentage, grade and status */

foreach($student_overall as $sid => $overall)
{
    if($overall["total"] > 0)
    {
        $student_overall[$sid]["percentage"] =
            round(($overall["marks"] / $overall["total"]) * 100);
    }
    else
    {
        $student_overall[$sid]["percentage"] = 0;
    }

    $percentage = $student_overall[$sid]["percentage"];

    if($percentage >= 90)
    {
        $student_overall[$sid]["grade"] = "A+";
    }
    elseif($percentage >= 80)
    {
        $student_overall[$sid]["grade"] = "A";
    }
    elseif($percentage >= 70)
    {
        $student_overall[$sid]["grade"] = "B+";
    }
    elseif($percentage >= 60)
    {
        $student_overall[$sid]["grade"] = "B";
    }
    elseif($percentage >= 50)
    {
        $student_overall[$sid]["grade"] = "C";
    }
    elseif($percentage >= 40)
    {
        $student_overall[$sid]["grade"] = "D";
    }
    else
    {
        $student_overall[$sid]["grade"] = "F";
    }

    $student_overall[$sid]["status"] =
        ($percentage >= 40) ? "PASS" : "FAIL";
}

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Result Management | EduTrack</title>


<style>

/* =====================================================
   RESET
===================================================== */

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


/* =====================================================
   HEADER
===================================================== */

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

    transition:.3s;

}


.home-nav a:hover{

    color:#FFB000;

}


/* =====================================================
   TEACHER MENU
===================================================== */

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


.teacher-menu .logout{

    color:#DC2626;

}


.teacher-menu .logout:hover{

    background:#FEE2E2;

    color:#DC2626;

}


/* =====================================================
   MAIN
===================================================== */

.main{

    width:90%;

    max-width:1250px;

    margin:40px auto 60px;

    animation:fadeUp .6s ease;

}


/* =====================================================
   PAGE HEADER
===================================================== */

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

    margin-bottom:25px;

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


.result-badge{

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


/* =====================================================
   SUMMARY
===================================================== */

.summary-grid{

    display:grid;

    grid-template-columns:
    repeat(3,1fr);

    gap:20px;

    margin-bottom:30px;

}


.summary-card{

    background:white;

    padding:23px;

    border-radius:20px;

    display:flex;

    align-items:center;

    gap:16px;

    box-shadow:
    0 8px 25px
    rgba(0,0,0,.07);

    transition:.3s;

}


.summary-card:hover{

    transform:translateY(-6px);

    box-shadow:
    0 15px 30px
    rgba(0,0,0,.10);

}


.summary-icon{

    width:60px;

    height:60px;

    border-radius:17px;

    background:#FFF1E6;

    display:flex;

    align-items:center;

    justify-content:center;

    font-size:28px;

}


.summary-card h2{

    font-size:30px;

    margin-bottom:4px;

}


.summary-card p{

    color:#78716C;

    font-size:13px;

}


/* =====================================================
   ADD RESULT
===================================================== */

.add-result-card{

    background:white;

    padding:30px;

    border-radius:25px;

    box-shadow:
    0 12px 30px
    rgba(0,0,0,.08);

    margin-bottom:30px;

}


.card-heading{

    margin-bottom:22px;

}


.card-heading h2{

    font-size:22px;

    margin-bottom:6px;

}


.card-heading p{

    color:#78716C;

    font-size:13px;

}


/* =====================================================
   FORM
===================================================== */

.form-grid{

    display:grid;

    grid-template-columns:
    1.5fr 1fr .8fr;

    gap:18px;

    align-items:end;

}


.form-group{

    display:flex;

    flex-direction:column;

}


.form-group label{

    font-size:13px;

    font-weight:bold;

    color:#44403C;

    margin-bottom:8px;

}


.form-group select,
.form-group input{

    width:100%;

    padding:13px 14px;

    border:1px solid #E7E5E4;

    border-radius:12px;

    outline:none;

    background:#FFFBF5;

    color:#292524;

    font-size:14px;

    transition:.3s;

}


.form-group select:focus,
.form-group input:focus{

    border-color:#FF8A00;

    background:white;

    box-shadow:
    0 0 0 3px
    rgba(255,138,0,.10);

}


/* =====================================================
   MARKS INPUT
===================================================== */

.marks-input{

    position:relative;

}


.marks-input input{

    padding-right:55px;

}


.marks-label{

    position:absolute;

    right:15px;

    bottom:14px;

    color:#78716C;

    font-size:13px;

    font-weight:bold;

}


/* =====================================================
   SAVE BUTTON
===================================================== */

.save-btn{

    width:100%;

    border:none;

    background:
    linear-gradient(
        90deg,
        #FF6B00,
        #FFB000
    );

    color:white;

    padding:13px 20px;

    border-radius:12px;

    font-size:15px;

    font-weight:bold;

    cursor:pointer;

    box-shadow:
    0 7px 17px
    rgba(255,107,0,.20);

    transition:.3s;

}


.save-btn:hover{

    transform:translateY(-3px);

    box-shadow:
    0 12px 25px
    rgba(255,107,0,.30);

}


/* =====================================================
   SEARCH
===================================================== */

.search-box{

    background:white;

    padding:17px 20px;

    border-radius:18px;

    box-shadow:
    0 8px 25px
    rgba(0,0,0,.06);

    margin-bottom:20px;

    display:flex;

    align-items:center;

    gap:12px;

}


.search-box span{

    font-size:22px;

}


.search-box input{

    width:100%;

    border:none;

    outline:none;

    font-size:15px;

    background:transparent;

}


.search-box input::placeholder{

    color:#A8A29E;

}


/* =====================================================
   OVERALL RESULT
===================================================== */

.overall-section{
    margin-bottom:30px;
}

.overall-title{
    margin-bottom:18px;
}

.overall-title h2{
    font-size:22px;
    margin-bottom:6px;
}

.overall-title p{
    color:#78716C;
    font-size:13px;
}

.overall-grid{
    display:grid;
    grid-template-columns:repeat(2,1fr);
    gap:20px;
}

.overall-card{
    background:white;
    border-radius:24px;
    padding:24px;
    box-shadow:0 12px 30px rgba(0,0,0,.08);
    border:1px solid #F1F0EE;
    transition:.3s;
    animation:fadeUp .6s ease;
}

.overall-card:hover{
    transform:translateY(-5px);
    box-shadow:0 18px 35px rgba(0,0,0,.10);
}

.overall-student{
    display:flex;
    align-items:center;
    gap:14px;
    margin-bottom:20px;
}

.overall-photo{
    width:55px;
    height:55px;
    border-radius:50%;
    object-fit:cover;
    border:3px solid #FFE0C2;
    background:#FFF1E6;
}

.overall-student h3{
    font-size:17px;
    margin-bottom:4px;
}

.overall-student p{
    color:#78716C;
    font-size:12px;
}

.overall-stats{
    display:grid;
    grid-template-columns:repeat(4,1fr);
    gap:10px;
    margin-bottom:18px;
}

.overall-stat{
    background:#FFFBF5;
    border:1px solid #F1F0EE;
    border-radius:14px;
    padding:12px 8px;
    text-align:center;
}

.overall-stat strong{
    display:block;
    font-size:20px;
    margin-bottom:4px;
}

.overall-stat span{
    color:#78716C;
    font-size:10px;
}

.overall-progress-head{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:8px;
    font-size:13px;
    font-weight:bold;
}

.overall-progress{
    width:100%;
    height:10px;
    background:#F1F0EE;
    border-radius:20px;
    overflow:hidden;
    margin-bottom:16px;
}

.overall-progress-bar{
    height:100%;
    background:linear-gradient(90deg,#FF6B00,#FFB000);
    border-radius:20px;
}

.overall-footer{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:10px;
}

.overall-grade{
    background:#FFF1E6;
    color:#C2410C;
    padding:9px 15px;
    border-radius:20px;
    font-weight:bold;
    font-size:13px;
}

.overall-pass{
    background:#DCFCE7;
    color:#15803D;
    padding:9px 15px;
    border-radius:20px;
    font-weight:bold;
    font-size:13px;
}

.overall-fail{
    background:#FEE2E2;
    color:#DC2626;
    padding:9px 15px;
    border-radius:20px;
    font-weight:bold;
    font-size:13px;
}

/* =====================================================
   RESULTS CARD
===================================================== */

.results-card{

    background:white;

    border-radius:25px;

    overflow:hidden;

    box-shadow:
    0 12px 30px
    rgba(0,0,0,.08);

}


.results-header{

    padding:23px 25px;

    border-bottom:1px solid #F1F0EE;

    display:flex;

    align-items:center;

    justify-content:space-between;

}


.results-header h2{

    font-size:21px;

}


.results-count{

    background:#FFF1E6;

    color:#C2410C;

    padding:8px 14px;

    border-radius:20px;

    font-size:12px;

    font-weight:bold;

}


/* =====================================================
   RESULT LIST
===================================================== */

.result-list{

    padding:20px;

}


/* =====================================================
   RESULT ITEM
===================================================== */

.result-item{

    display:flex;

    align-items:center;

    justify-content:space-between;

    gap:20px;

    background:#FFFBF5;

    border:1px solid #F1F0EE;

    border-radius:20px;

    padding:18px 20px;

    margin-bottom:14px;

    transition:.3s;

}


.result-item:hover{

    transform:translateY(-3px);

    border-color:#FED7AA;

    box-shadow:
    0 8px 20px
    rgba(0,0,0,.07);

}


/* =====================================================
   STUDENT
===================================================== */

.result-student{

    display:flex;

    align-items:center;

    gap:14px;

    min-width:290px;

}


.student-photo{

    width:58px;

    height:58px;

    border-radius:50%;

    object-fit:cover;

    border:3px solid #FFE0C2;

    background:#FFF1E6;

}


.student-info h3{

    font-size:16px;

    margin-bottom:5px;

}


.student-info p{

    color:#78716C;

    font-size:12px;

    margin:3px 0;

}


/* =====================================================
   SUBJECT
===================================================== */

.subject-box{

    min-width:150px;

}


.subject-box span{

    display:inline-block;

    background:#FFF1E6;

    color:#C2410C;

    padding:9px 14px;

    border-radius:20px;

    font-size:12px;

    font-weight:bold;

}


/* =====================================================
   MARKS
===================================================== */

.marks-box{

    text-align:center;

    min-width:100px;

}


.marks-box strong{

    font-size:26px;

    color:#292524;

}


.marks-box small{

    display:block;

    color:#78716C;

    font-size:11px;

    margin-top:3px;

}


/* =====================================================
   STATUS
===================================================== */

.result-status{

    min-width:100px;

    text-align:center;

}


.pass{

    display:inline-block;

    background:#DCFCE7;

    color:#15803D;

    padding:9px 15px;

    border-radius:20px;

    font-size:12px;

    font-weight:bold;

}


.fail{

    display:inline-block;

    background:#FEE2E2;

    color:#DC2626;

    padding:9px 15px;

    border-radius:20px;

    font-size:12px;

    font-weight:bold;

}


/* =====================================================
   EMPTY
===================================================== */

.empty-state{

    text-align:center;

    padding:65px 20px;

}


.empty-icon{

    font-size:60px;

    margin-bottom:15px;

}


.empty-state h3{

    margin-bottom:8px;

}


.empty-state p{

    color:#78716C;

}


/* =====================================================
   FOOTER
===================================================== */

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


/* =====================================================
   ANIMATION
===================================================== */

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


/* =====================================================
   TABLET
===================================================== */

@media(max-width:950px){

    .form-grid{

        grid-template-columns:
        1fr 1fr;

    }


    .form-group:last-child{

        grid-column:
        1 / -1;

    }


    .result-item{

        flex-wrap:wrap;

    }

}


/* =====================================================
   MOBILE
===================================================== */

@media(max-width:950px){

    .overall-grid{
        grid-template-columns:1fr;
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


    .summary-grid{

        grid-template-columns:1fr;

    }


    .add-result-card{

        padding:22px;

    }


    .form-grid{

        grid-template-columns:1fr;

    }


    .form-group:last-child{

        grid-column:auto;

    }


    .result-item{

        flex-direction:column;

        align-items:flex-start;

    }


    .result-student{

        width:100%;

        min-width:0;

    }


    .subject-box,
    .marks-box,
    .result-status{

        width:100%;

        min-width:0;

        text-align:left;

    }


    .results-header{

        flex-direction:column;

        align-items:flex-start;

        gap:10px;

    }

}



/* SUBJECT SELECTOR AND BULK MARKS ENTRY */
.subject-form {
    margin-bottom: 20px;
}

.subject-help {
    color: #78716C;
    font-size: 13px;
    margin-top: 14px;
    line-height: 1.5;
}

.student-marks-section {
    margin-top: 25px;
}

.marks-section-heading {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 15px;
    flex-wrap: wrap;
    margin-bottom: 18px;
}

.marks-section-heading h3 {
    font-size: 20px;
    margin-bottom: 7px;
}

.marks-section-heading p {
    color: #78716C;
    font-size: 13px;
    line-height: 1.6;
}

.student-search {
    display: flex;
    align-items: center;
    gap: 12px;
    background: #FFFBF5;
    border: 1px solid #F1F0EE;
    padding: 13px 16px;
    border-radius: 13px;
    margin-bottom: 16px;
}

.student-search span {
    font-size: 20px;
}

.student-search input {
    border: 0;
    outline: none;
    background: transparent;
    width: 100%;
    font-size: 14px;
}

.marks-table-wrap {
    width: 100%;
    overflow-x: auto;
    border: 1px solid #F1F0EE;
    border-radius: 16px;
}

.marks-table {
    width: 100%;
    min-width: 850px;
    border-collapse: collapse;
}

.marks-table th {
    background: #292524;
    color: white;
    text-align: left;
    padding: 14px;
    font-size: 13px;
    white-space: nowrap;
}

.marks-table td {
    padding: 13px 14px;
    border-bottom: 1px solid #F1F0EE;
    font-size: 13px;
    vertical-align: middle;
}

.marks-table tbody tr:hover td {
    background: #FFFBF5;
}

.marks-student {
    display: flex;
    align-items: center;
    gap: 10px;
    min-width: 190px;
}

.marks-student-photo {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #FFE0C2;
    background: #FFF1E6;
    flex-shrink: 0;
}

.no-photo {
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
}

.marks-table td small {
    display: block;
    color: #78716C;
    margin-top: 5px;
}

.student-mark-input {
    width: 115px;
    padding: 11px;
    border: 1px solid #E7E5E4;
    border-radius: 10px;
    outline: none;
    font-size: 14px;
    background: white;
}

.student-mark-input:focus {
    border-color: #FF8A00;
    box-shadow: 0 0 0 3px rgba(255,138,0,.10);
}

.save-all-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 15px;
    flex-wrap: wrap;
    margin-top: 18px;
}

.save-all-row p {
    color: #78716C;
    font-size: 12px;
    line-height: 1.5;
}

.save-all-btn {
    width: auto;
    min-width: 190px;
}

.subject-empty-state {
    text-align: center;
    background: #FFFBF5;
    border: 1px dashed #FED7AA;
    border-radius: 18px;
    padding: 35px 20px;
    margin-top: 20px;
}

.subject-empty-state div {
    font-size: 42px;
    margin-bottom: 10px;
}

.subject-empty-state h3 {
    margin-bottom: 8px;
}

.subject-empty-state p {
    color: #78716C;
    font-size: 13px;
    line-height: 1.6;
}

@media(max-width:700px) {
    .marks-section-heading {
        align-items: flex-start;
    }

    .save-all-row {
        align-items: stretch;
    }

    .save-all-btn {
        width: 100%;
    }
}

</style>

</head>


<body>


<!-- =====================================================
     HEADER
===================================================== -->

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



<!-- =====================================================
     TEACHER MENU
===================================================== -->

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


    <a href="result.php" class="active">

        📊 Result

    </a>


    <a href="course.php">

        📚 Course

    </a>


    <a href="profile.php">

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



<!-- =====================================================
     MAIN
===================================================== -->

<div class="main">


    <!-- PAGE HEADER -->

    <div class="page-header">

        <div>

            <h1>
                📊 Result Management
            </h1>

            <p>
                Add and manage results for
                <strong><?php echo htmlspecialchars($selected_course); ?> — Semester <?php echo htmlspecialchars($selected_semester); ?></strong> students only.
            </p>

        </div>


        <div>

            <div class="result-badge">
                📝 Result Panel
            </div>

            <div style="margin-top:10px;text-align:right;color:#FFB000;font-size:13px;font-weight:bold;">
                🎓 <?php echo htmlspecialchars($selected_course); ?> — Semester <?php echo htmlspecialchars($selected_semester); ?>
            </div>

        </div>

    </div>



    <!-- =================================================
         SUMMARY
    ================================================= -->

    <div class="summary-grid">


        <!-- TOTAL -->

        <div class="summary-card">

            <div class="summary-icon">

                📋

            </div>

            <div>

                <h2>

                    <?php
                    echo $total_results;
                    ?>

                </h2>

                <p>
                    Total Results
                </p>

            </div>

        </div>



        <!-- PASS -->

        <div class="summary-card">

            <div class="summary-icon">

                🟢

            </div>

            <div>

                <h2>

                    <?php
                    echo $pass_count;
                    ?>

                </h2>

                <p>
                    Passed Results
                </p>

            </div>

        </div>



        <!-- FAIL -->

        <div class="summary-card">

            <div class="summary-icon">

                🔴

            </div>

            <div>

                <h2>

                    <?php
                    echo $fail_count;
                    ?>

                </h2>

                <p>
                    Failed Results
                </p>

            </div>

        </div>


    </div>



    <!-- =================================================
         ADD RESULT
    ================================================= -->

    <div class="add-result-card">


        <div class="card-heading">

            <h2>
                ➕ Add / Update Result
            </h2>

            <p>
                Select a student, enter subject and marks out of 100.
            </p>

        </div>



        <!-- SUBJECT SELECTOR -->
        <form method="GET" id="subjectForm" class="subject-form">

            <div class="form-grid">

                <div class="form-group">
                    <label for="subjectSelect">📚 Select Subject</label>

                    <select
                        name="subject"
                        id="subjectSelect"
                        onchange="handleSubjectChange()"
                        required
                    >
                        <option value="">-- Select Subject --</option>

                        <?php foreach ($subject_options as $subject_option_name) { ?>
                            <option
                                value="<?php echo htmlspecialchars($subject_option_name); ?>"
                                <?php echo ($subject_option === $subject_option_name) ? 'selected' : ''; ?>
                            >
                                <?php echo htmlspecialchars($subject_option_name); ?>
                            </option>
                        <?php } ?>

                        <option
                            value="__new__"
                            <?php echo ($subject_option === '__new__') ? 'selected' : ''; ?>
                        >
                            + Add New Subject
                        </option>
                    </select>
                </div>

                <div
                    class="form-group"
                    id="newSubjectGroup"
                    style="<?php echo ($subject_option === '__new__') ? '' : 'display:none;'; ?>"
                >
                    <label for="newSubject">Enter New Subject Name</label>

                    <input
                        type="text"
                        name="new_subject"
                        id="newSubject"
                        placeholder="e.g. PHP Programming"
                        value="<?php echo ($subject_option === '__new__') ? htmlspecialchars($new_subject) : ''; ?>"
                    >
                </div>

                <div class="form-group">
                    <button type="submit" class="save-btn" id="loadStudentsButton">
                        🔎 Load Students
                    </button>
                </div>

            </div>

            <p class="subject-help">
                Select an existing subject to load its student list, or choose
                “Add New Subject” to enter a new subject name.
            </p>

        </form>

        <?php if ($active_subject !== '' && $students_for_subject) { ?>

            <div class="student-marks-section">

                <div class="marks-section-heading">
                    <div>
                        <h3>
                            📝 Enter Marks — <?php echo htmlspecialchars($active_subject); ?>
                        </h3>
                        <p>
                            <?php echo htmlspecialchars($selected_course); ?>
                            — Semester <?php echo htmlspecialchars($selected_semester); ?>.
                            Enter marks out of 100 for each student.
                        </p>
                    </div>

                    <span class="results-count">
                        <?php echo mysqli_num_rows($students_for_subject); ?> Students
                    </span>
                </div>

                <div class="student-search">
                    <span>🔍</span>
                    <input
                        type="search"
                        id="studentMarksSearch"
                        placeholder="Search student by name, email or phone..."
                        oninput="searchSubjectStudents()"
                    >
                </div>

                <form method="POST">

                    <input
                        type="hidden"
                        name="subject"
                        value="<?php echo htmlspecialchars($active_subject); ?>"
                    >

                    <div class="marks-table-wrap">

                        <table class="marks-table">

                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Student</th>
                                    <th>Email / Phone</th>
                                    <th>Course</th>
                                    <th>Semester</th>
                                    <th>Marks / 100</th>
                                </tr>
                            </thead>

                            <tbody id="subjectStudentsBody">

                                <?php
                                $student_number = 1;

                                if (mysqli_num_rows($students_for_subject) > 0) {
                                    while ($student_row = mysqli_fetch_assoc($students_for_subject)) {
                                        $existing_marks = $student_row['existing_marks'];
                                ?>

                                    <tr
                                        class="subject-student-row"
                                        data-search="<?php
                                            echo htmlspecialchars(
                                                strtolower(
                                                    ($student_row['name'] ?? '') . ' ' .
                                                    ($student_row['email'] ?? '') . ' ' .
                                                    ($student_row['phone'] ?? '')
                                                )
                                            );
                                        ?>"
                                    >

                                        <td><?php echo $student_number++; ?></td>

                                        <td>
                                            <div class="marks-student">

                                                <?php if (!empty($student_row['photo'])) { ?>

                                                    <img
                                                        class="marks-student-photo"
                                                        src="../uploads/<?php echo rawurlencode(basename($student_row['photo'])); ?>"
                                                        alt="Student photo"
                                                        onerror="this.style.display='none'"
                                                    >

                                                <?php } else { ?>

                                                    <div class="marks-student-photo no-photo">👤</div>

                                                <?php } ?>

                                                <strong>
                                                    <?php
                                                    echo htmlspecialchars(
                                                        ucwords(
                                                            strtolower(
                                                                trim($student_row['name'] ?? '')
                                                            )
                                                        )
                                                    );
                                                    ?>
                                                </strong>

                                            </div>
                                        </td>

                                        <td>
                                            <div><?php echo htmlspecialchars($student_row['email'] ?? ''); ?></div>
                                            <small><?php echo htmlspecialchars($student_row['phone'] ?? ''); ?></small>
                                        </td>

                                        <td><?php echo htmlspecialchars($student_row['course'] ?? ''); ?></td>

                                        <td><?php echo htmlspecialchars($student_row['semester'] ?? ''); ?></td>

                                        <td>
                                            <input
                                                class="student-mark-input"
                                                type="number"
                                                name="marks[<?php echo (int)$student_row['id']; ?>]"
                                                min="0"
                                                max="100"
                                                step="0.01"
                                                placeholder="0 - 100"
                                                value="<?php
                                                    echo ($existing_marks !== null && $existing_marks !== '')
                                                        ? htmlspecialchars((string)$existing_marks)
                                                        : '';
                                                ?>"
                                            >
                                        </td>

                                    </tr>

                                <?php
                                    }
                                } else {
                                ?>

                                    <tr>
                                        <td colspan="6" class="empty-state">
                                            No students found for this course and semester.
                                        </td>
                                    </tr>

                                <?php } ?>

                            </tbody>

                        </table>

                    </div>

                    <div class="save-all-row">
                        <p>Leave marks blank if you do not want to save a result for that student yet.</p>

                        <button
                            type="submit"
                            name="save_all"
                            class="save-btn save-all-btn"
                            onclick="return confirm('Save marks for this subject?');"
                        >
                            💾 Save All Marks
                        </button>
                    </div>

                </form>

            </div>

        <?php } elseif ($subject_option === '' || ($subject_option === '__new__' && $new_subject === '')) { ?>

            <div class="subject-empty-state">
                <div>📚</div>
                <h3>Select a Subject First</h3>
                <p>Choose a subject above. All students in the selected course and semester will appear here.</p>
            </div>

        <?php } ?>


    </div>



    <!-- =================================================
         SEARCH
    ================================================= -->

    <div class="search-box">

        <span>
            🔍
        </span>


        <input
            type="text"
            id="resultSearch"
            placeholder="Search student, subject or course..."
            onkeyup="searchResults()"
        >

    </div>



    <!-- =================================================
         STUDENT-WISE OVERALL RESULT
    ================================================= -->

    <?php if(count($student_overall) > 0){ ?>

    <div class="overall-section">

        <div class="overall-title">
            <h2>🏆 Overall Student Result</h2>
            <p>Each student's marks from all subjects are combined to calculate the overall result.</p>
        </div>

        <div class="overall-grid">

            <?php foreach($student_overall as $sid => $overall){ ?>

            <div class="overall-card">

                <div class="overall-student">

                    <?php if(!empty($overall['photo'])){ ?>

                        <img
                            class="overall-photo"
                            src="../uploads/<?php echo htmlspecialchars($overall['photo']); ?>"
                            alt="Student Photo"
                        >

                    <?php }else{ ?>

                        <div
                            class="overall-photo"
                            style="display:flex;align-items:center;justify-content:center;font-size:25px;"
                        >
                            👤
                        </div>

                    <?php } ?>

                    <div>
                        <h3>
                            <?php
                            echo htmlspecialchars(
                                ucwords(
                                    strtolower(
                                        trim($overall['name'])
                                    )
                                )
                            );
                            ?>
                        </h3>

                        <p>
                            <?php echo htmlspecialchars($overall['course']); ?>
                            • Semester <?php echo htmlspecialchars($overall['semester']); ?>
                        </p>
                    </div>

                </div>

                <div class="overall-stats">

                    <div class="overall-stat">
                        <strong>
                            <?php echo $overall['subjects']; ?>
                        </strong>
                        <span>Subjects</span>
                    </div>

                    <div class="overall-stat">
                        <strong>
                            <?php echo rtrim(rtrim(number_format($overall['marks'],2,'.',''),'0'),'.'); ?>
                        </strong>
                        <span>Marks</span>
                    </div>

                    <div class="overall-stat">
                        <strong>
                            <?php echo $overall['percentage']; ?>%
                        </strong>
                        <span>Percentage</span>
                    </div>

                    <div class="overall-stat">
                        <strong>
                            <?php echo $overall['grade']; ?>
                        </strong>
                        <span>Grade</span>
                    </div>

                </div>

                <div class="overall-progress-head">
                    <span>Overall Performance</span>
                    <span><?php echo $overall['percentage']; ?>%</span>
                </div>

                <div class="overall-progress">
                    <div
                        class="overall-progress-bar"
                        style="width:<?php echo min(100,$overall['percentage']); ?>%;"
                    ></div>
                </div>

                <div class="overall-footer">

                    <div class="overall-grade">
                        Total:
                        <?php
                        echo rtrim(rtrim(number_format($overall['marks'],2,'.',''),'0'),'.');
                        ?>
                        /
                        <?php
                        echo rtrim(rtrim(number_format($overall['total'],2,'.',''),'0'),'.');
                        ?>
                    </div>

                    <?php if($overall['status'] == "PASS"){ ?>

                        <div class="overall-pass">
                            🟢 PASS
                        </div>

                    <?php }else{ ?>

                        <div class="overall-fail">
                            🔴 FAIL
                        </div>

                    <?php } ?>

                </div>

            </div>

            <?php } ?>

        </div>

    </div>

    <?php } ?>


    <!-- =================================================
         RESULTS
    ================================================= -->

    <div class="results-card">


        <div class="results-header">

            <h2>
                📋 Student Results
            </h2>


            <span class="results-count">

                <?php
                echo $total_results;
                ?>

                Results

            </span>

        </div>



        <div
            class="result-list"
            id="resultList"
        >


        <?php

        if(count($result_rows) > 0)
        {

            foreach($result_rows as $row)
            {

                $marks =
                    floatval(
                        $row['marks']
                    );


                /* PASS / FAIL */

                if($marks >= 40)
                {
                    $status = "PASS";
                }
                else
                {
                    $status = "FAIL";
                }

        ?>


            <div
                class="result-item"
                data-search="<?php

                    echo htmlspecialchars(

                        strtolower(

                            $row['name']." ".
                            $row['subject']." ".
                            $row['course']." ".
                            $row['semester']

                        )

                    );

                ?>"
            >


                <!-- STUDENT -->

                <div class="result-student">


                    <?php

                    if(!empty($row['photo']))
                    {

                    ?>

                        <img
                            class="student-photo"
                            src="../uploads/<?php

                            echo htmlspecialchars(
                                $row['photo']
                            );

                            ?>"
                            alt="Student Photo"
                        >

                    <?php

                    }
                    else
                    {

                    ?>

                        <div
                            class="student-photo"
                            style="
                                display:flex;
                                align-items:center;
                                justify-content:center;
                                font-size:27px;
                            "
                        >

                            👤

                        </div>

                    <?php

                    }

                    ?>


                    <div class="student-info">


                        <h3>

                            <?php

                            echo htmlspecialchars(

                                ucwords(

                                    strtolower(

                                        trim(
                                            $row['name']
                                        )

                                    )

                                )

                            );

                            ?>

                        </h3>


                        <p>

                            🎓 Semester
                            <?php

                            echo htmlspecialchars(
                                $row['semester']
                            );

                            ?>

                        </p>


                        <p>

                            📚
                            <?php

                            echo htmlspecialchars(
                                $row['course']
                            );

                            ?>

                        </p>


                    </div>

                </div>



                <!-- SUBJECT -->

                <div class="subject-box">

                    <span>

                        📖

                        <?php

                        echo htmlspecialchars(
                            $row['subject']
                        );

                        ?>

                    </span>

                </div>



                <!-- MARKS -->

                <div class="marks-box">

                    <strong>

                        <?php
                        echo $marks;
                        ?>

                    </strong>


                    <small>
                        Marks / 100
                    </small>

                </div>



                <!-- STATUS -->

                <div class="result-status">


                    <?php

                    if($status == "PASS")
                    {

                    ?>

                        <span class="pass">

                            🟢 PASS

                        </span>

                    <?php

                    }
                    else
                    {

                    ?>

                        <span class="fail">

                            🔴 FAIL

                        </span>

                    <?php

                    }

                    ?>


                </div>


            </div>


        <?php

            }

        }
        else
        {

        ?>


            <div class="empty-state">

                <div class="empty-icon">
                    📊
                </div>


                <h3>
                    No Results Found
                </h3>


                <p>
                    Add a student's result using the form above.
                </p>

            </div>


        <?php

        }

        ?>


        </div>

    </div>


</div>



<!-- =====================================================
     FOOTER
===================================================== -->

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



<!-- =====================================================
     SEARCH JAVASCRIPT
===================================================== -->

<script>

function searchResults()
{

    let input =
        document
        .getElementById("resultSearch")
        .value
        .toLowerCase()
        .trim();


    let results =
        document.querySelectorAll(
            ".result-item"
        );


    results.forEach(function(item)
    {

        let text =
            item
            .getAttribute("data-search")
            .toLowerCase();


        if(text.includes(input))
        {

            item.style.display = "flex";

        }
        else
        {

            item.style.display = "none";

        }

    });

}


function handleSubjectChange() {
    const subjectSelect = document.getElementById("subjectSelect");
    const newSubjectGroup = document.getElementById("newSubjectGroup");
    const newSubjectInput = document.getElementById("newSubject");
    const subjectForm = document.getElementById("subjectForm");

    if (!subjectSelect || !newSubjectGroup || !newSubjectInput || !subjectForm) {
        return;
    }

    if (subjectSelect.value === "__new__") {
        newSubjectGroup.style.display = "flex";
        newSubjectInput.required = true;
        newSubjectInput.focus();
    } else if (subjectSelect.value !== "") {
        newSubjectGroup.style.display = "none";
        newSubjectInput.required = false;
        subjectForm.submit();
    } else {
        newSubjectGroup.style.display = "none";
        newSubjectInput.required = false;
    }
}

function searchSubjectStudents() {
    const input = document.getElementById("studentMarksSearch");
    const rows = document.querySelectorAll(".subject-student-row");

    if (!input) {
        return;
    }

    const searchText = input.value.toLowerCase().trim();

    rows.forEach(function(row) {
        const searchableText = (row.getAttribute("data-search") || "").toLowerCase();
        row.style.display = searchableText.includes(searchText) ? "" : "none";
    });
}

document.addEventListener("DOMContentLoaded", function() {
    const subjectSelect = document.getElementById("subjectSelect");
    const newSubjectGroup = document.getElementById("newSubjectGroup");
    const newSubjectInput = document.getElementById("newSubject");

    if (subjectSelect && newSubjectGroup && newSubjectInput) {
        if (subjectSelect.value === "__new__") {
            newSubjectGroup.style.display = "flex";
            newSubjectInput.required = true;
        } else {
            newSubjectGroup.style.display = "none";
            newSubjectInput.required = false;
        }
    }
});

</script>


</body>

</html>