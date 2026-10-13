<?php

include "../db.php";

$id=$_GET['id'];

mysqli_query($conn,"DELETE FROM course WHERE id='$id'");

header("Location:course.php");

?>