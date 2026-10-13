<?php

include "../db.php";

$id = $_GET['id'];

mysqli_query($conn,"DELETE FROM students WHERE id='$id'");

header("Location: manage_student.php");

exit();

?>