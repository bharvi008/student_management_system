
<?php
session_start();
include "../db.php";

/* ================= AUTHENTICATION ================= */

if (!isset($_SESSION['teacher_id'])) {
    header("Location:../login.php");
    exit();
}


/* ================= COURSE AND SEMESTER ================= */

if (
    !isset(
        $_SESSION['teacher_course'],
        $_SESSION['teacher_semester']
    ) ||
    trim($_SESSION['teacher_course']) === '' ||
    trim((string)$_SESSION['teacher_semester']) === ''
) {
    header("Location:dashboard.php");
    exit();
}

$selected_course = $_SESSION['teacher_course'];

$selected_semester = (string)$_SESSION['teacher_semester'];


/* ================= SECURE VALUES ================= */

$safe_course = mysqli_real_escape_string(
    $conn,
    $selected_course
);

$safe_semester = mysqli_real_escape_string(
    $conn,
    $selected_semester
);


/* ================= SEARCH ================= */

$search = trim($_GET['search'] ?? '');

$safe_search = mysqli_real_escape_string(
    $conn,
    $search
);


/* ================= FETCH STUDENTS ================= */

$sql = "
    SELECT
        id,
        name,
        email,
        phone,
        gender,
        course,
        semester,
        photo
    FROM students
    WHERE course = '$safe_course'
    AND semester = '$safe_semester'
";


/* ================= SEARCH FILTER ================= */

if ($search !== '') {

    $sql .= "
        AND (
            name LIKE '%$safe_search%'
            OR email LIKE '%$safe_search%'
            OR phone LIKE '%$safe_search%'
        )
    ";
}


/* ================= SORT STUDENTS ================= */

$sql .= " ORDER BY name ASC";


/* ================= EXECUTE QUERY ================= */

$students = mysqli_query($conn, $sql);


/* ================= TOTAL STUDENTS ================= */

$total = $students
    ? mysqli_num_rows($students)
    : 0;

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>Students | EduTrack</title>


    <!-- ================= CSS ================= -->

    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #FFF7ED;
            color: #292524;
        }


        /* HEADER */

        .top-header {
            background: #1C1917;
            color: white;
            padding: 18px 7%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 18px;
            flex-wrap: wrap;
        }

        .logo a {
            font-size: 28px;
            font-weight: 800;
            color: white;
            text-decoration: none;
        }

        .logo span {
            color: #FF8A00;
        }

        .home-nav {
            display: flex;
            gap: 22px;
            flex-wrap: wrap;
        }

        .home-nav a {
            color: white;
            text-decoration: none;
            font-size: 14px;
        }

        .home-nav a:hover {
            color: #FFB000;
        }


        /* TEACHER NAVIGATION */

        .teacher-menu {
            width: 94%;
            max-width: 1200px;
            margin: 20px auto;
            padding: 12px;
            background: white;
            border-radius: 18px;
            box-shadow: 0 8px 25px #00000012;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 8px;
        }

        .teacher-menu a {
            text-decoration: none;
            color: #44403C;
            padding: 11px 15px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: bold;
        }

        .teacher-menu a:hover,
        .teacher-menu .active {
            background: linear-gradient(
                90deg,
                #FF6B00,
                #FFB000
            );
            color: white;
        }

        .teacher-menu .logout {
            color: #DC2626;
        }


        /* MAIN CONTAINER */

        .container {
            width: 92%;
            max-width: 1200px;
            margin: 28px auto 50px;
        }


        /* PAGE HEADER */

        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            flex-wrap: wrap;
            margin-bottom: 22px;
        }

        .page-header h1 {
            font-size: 30px;
            margin-bottom: 8px;
        }

        .page-header p {
            color: #78716C;
            line-height: 1.5;
        }

        .badge {
            background: #FFEDD5;
            color: #9A3412;
            padding: 10px 14px;
            border-radius: 12px;
            font-weight: bold;
        }


        /* SEARCH TOOLBAR */

        .toolbar {
            background: white;
            padding: 18px;
            border-radius: 16px;
            box-shadow: 0 6px 22px #0000000b;
            margin-bottom: 20px;
            display: flex;
            gap: 12px;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        .toolbar form {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .toolbar input {
            padding: 12px 14px;
            border: 1px solid #E7E5E4;
            border-radius: 10px;
            min-width: 230px;
            font-size: 14px;
        }


        /* BUTTONS */

        .btn {
            border: 0;
            border-radius: 10px;
            padding: 12px 17px;
            background: #FF6B00;
            color: white;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }

        .btn:hover {
            background: #EA580C;
        }


        /* STUDENT COUNT */

        .count {
            font-weight: bold;
            color: #57534E;
        }


        /* STUDENT TABLE */

        .table-wrap {
            background: white;
            border-radius: 16px;
            overflow: auto;
            box-shadow: 0 8px 25px #0000000b;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 760px;
        }

        th,
        td {
            text-align: left;
            padding: 15px 16px;
            border-bottom: 1px solid #F0ECE8;
            font-size: 14px;
        }

        th {
            background: #292524;
            color: white;
        }

        tr:hover td {
            background: #FFFAF5;
        }


        /* STUDENT PHOTO */

        .student {
            display: flex;
            align-items: center;
            gap: 11px;
            font-weight: bold;
        }

        .photo {
            width: 42px;
            height: 42px;
            object-fit: cover;
            border-radius: 50%;
            background: #FFEDD5;
            display: flex;
            align-items: center;
            justify-content: center;
        }


        /* EMPTY MESSAGE */

        .empty {
            text-align: center;
            padding: 35px;
            color: #78716C;
        }


        /* RESPONSIVE DESIGN */

        @media (max-width: 600px) {

            .page-header h1 {
                font-size: 24px;
            }

            .container {
                width: 94%;
            }

            .toolbar input {
                min-width: 0;
                width: 100%;
            }

            .toolbar form {
                width: 100%;
            }

            .toolbar form input {
                flex: 1;
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

            <a href="../index.php">Home</a>

            <a href="../index.php#about">About</a>

            <a href="../index.php#features">Features</a>

            <a href="../index.php#contact">Contact</a>

        </nav>

    </header>


    <!-- ================= TEACHER MENU ================= -->

    <nav class="teacher-menu">

        <a href="dashboard.php">
            🏠 Dashboard
        </a>

        <a class="active" href="students.php">
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

        <a href="change_password.php">
            🔑 Password
        </a>

        <a
            class="logout"
            href="logout.php"
            onclick="return confirm('Are you sure you want to logout?')"
        >
            🚪 Logout
        </a>

    </nav>


    <!-- ================= MAIN CONTENT ================= -->

    <main class="container">


        <!-- PAGE TITLE -->

        <div class="page-header">

            <div>

                <h1>👨‍🎓 Students</h1>

                <p>
                    View students in the selected course and semester.
                </p>

            </div>


            <!-- SELECTED COURSE AND SEMESTER -->

            <div class="badge">

                🎓
                <?php
                echo htmlspecialchars($selected_course);
                ?>

                —

                Semester

                <?php
                echo htmlspecialchars($selected_semester);
                ?>

            </div>

        </div>


        <!-- ================= SEARCH BAR ================= -->

        <div class="toolbar">


            <!-- TOTAL STUDENTS -->

            <div class="count">

                Total Students:

                <?php
                echo $total;
                ?>

            </div>


            <!-- SEARCH FORM -->

            <form method="GET">

                <input
                    type="search"
                    name="search"
                    placeholder="Search name, email or phone"
                    value="<?php
                        echo htmlspecialchars($search);
                    ?>"
                >


                <button
                    class="btn"
                    type="submit"
                >
                    Search
                </button>


                <a
                    class="btn"
                    href="students.php"
                >
                    Reset
                </a>

            </form>

        </div>


        <!-- ================= STUDENT TABLE ================= -->

        <div class="table-wrap">

            <table>

                <thead>

                    <tr>

                        <th>#</th>

                        <th>Student</th>

                        <th>Email</th>

                        <th>Phone</th>

                        <th>Gender</th>

                        <th>Course</th>

                        <th>Semester</th>

                    </tr>

                </thead>


                <tbody>


                    <?php

                    if (
                        $students &&
                        mysqli_num_rows($students) > 0
                    ) {

                        $i = 1;

                        while (
                            $row = mysqli_fetch_assoc($students)
                        ) {

                    ?>

                        <tr>


                            <!-- NUMBER -->

                            <td>

                                <?php
                                echo $i++;
                                ?>

                            </td>


                            <!-- STUDENT NAME AND PHOTO -->

                            <td>

                                <div class="student">


                                    <?php

                                    if (!empty($row['photo'])) {

                                    ?>

                                        <img
                                            class="photo"
                                            src="../uploads/<?php
                                                echo rawurlencode(
                                                    $row['photo']
                                                );
                                            ?>"
                                            alt="Student photo"
                                        >

                                    <?php

                                    } else {

                                    ?>

                                        <div class="photo">
                                            👤
                                        </div>

                                    <?php

                                    }

                                    ?>


                                    <span>

                                        <?php

                                        echo htmlspecialchars(
                                            ucwords(
                                                strtolower(
                                                    $row['name'] ?? ''
                                                )
                                            )
                                        );

                                        ?>

                                    </span>

                                </div>

                            </td>


                            <!-- EMAIL -->

                            <td>

                                <?php

                                echo htmlspecialchars(
                                    $row['email'] ?? ''
                                );

                                ?>

                            </td>


                            <!-- PHONE -->

                            <td>

                                <?php

                                echo htmlspecialchars(
                                    $row['phone'] ?? ''
                                );

                                ?>

                            </td>


                            <!-- GENDER -->

                            <td>

                                <?php

                                echo htmlspecialchars(
                                    $row['gender'] ?? ''
                                );

                                ?>

                            </td>


                            <!-- COURSE -->

                            <td>

                                <?php

                                echo htmlspecialchars(
                                    $row['course'] ?? ''
                                );

                                ?>

                            </td>


                            <!-- SEMESTER -->

                            <td>

                                <?php

                                echo htmlspecialchars(
                                    $row['semester'] ?? ''
                                );

                                ?>

                            </td>


                        </tr>


                    <?php

                        }

                    } else {

                    ?>


                        <!-- NO STUDENTS FOUND -->

                        <tr>

                            <td
                                colspan="7"
                                class="empty"
                            >

                                No students found for this course
                                and semester.

                            </td>

                        </tr>


                    <?php

                    }

                    ?>


                </tbody>

            </table>

        </div>

    </main>

</body>

</html>
