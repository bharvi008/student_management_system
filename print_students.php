<?php

include "../db.php";

$result = mysqli_query($conn,"SELECT * FROM students ORDER BY id DESC");

?>

<!DOCTYPE html>
<html>

<head>

<title>Print Students</title>

<style>

body{
font-family:Arial;
}

table{

width:100%;
border-collapse:collapse;

}

th{

background:#1e3c72;
color:white;
padding:10px;

}

td{

padding:10px;
border:1px solid #ddd;
text-align:center;

}

</style>

</head>

<body onload="window.print()">

<h2 align="center">

Student List

</h2>

<table>

<tr>

<th>ID</th>
<th>Name</th>
<th>Email</th>
<th>Phone</th>
<th>Course</th>
<th>Semester</th>

</tr>

<?php

while($row=mysqli_fetch_assoc($result))
{

?>

<tr>

<td><?php echo $row['id']; ?></td>

<td><?php echo $row['name']; ?></td>

<td><?php echo $row['email']; ?></td>

<td><?php echo $row['phone']; ?></td>

<td><?php echo $row['course']; ?></td>

<td><?php echo $row['semester']; ?></td>

</tr>

<?php

}

?>

</table>

</body>

</html>