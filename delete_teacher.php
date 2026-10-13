<?php

include "../db.php";

if(isset($_GET['id']))
{

$id = $_GET['id'];

/* Teacher Photo Delete */

$result = mysqli_query($conn,"SELECT * FROM teacher WHERE id='$id'");

$row = mysqli_fetch_assoc($result);

if($row)
{

if(!empty($row['photo']) && file_exists("../uploads/".$row['photo']))
{
unlink("../uploads/".$row['photo']);
}

/* Delete Teacher */

mysqli_query($conn,"DELETE FROM teacher WHERE id='$id'");

echo "<script>

alert('Teacher Deleted Successfully');

window.location='manage_teacher.php';

</script>";

}

else
{

echo "<script>

alert('Teacher Not Found');

window.location='manage_teacher.php';

</script>";

}

}

?>