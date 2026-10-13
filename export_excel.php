<?php

include "../db.php";

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Student_List.xls");

echo "<table border='1'>";

echo "<tr>
<th>ID</th>
<th>Name</th>
<th>Email</th>
<th>Phone</th>
<th>Gender</th>
<th>Course</th>
<th>Semester</th>
</tr>";

$result = mysqli_query($conn,"SELECT * FROM students ORDER BY id DESC");

while($row = mysqli_fetch_assoc($result))
{
    echo "<tr>";

    echo "<td>".$row['id']."</td>";
    echo "<td>".$row['name']."</td>";
    echo "<td>".$row['email']."</td>";
    echo "<td>".$row['phone']."</td>";
    echo "<td>".$row['gender']."</td>";
    echo "<td>".$row['course']."</td>";
    echo "<td>".$row['semester']."</td>";

    echo "</tr>";
}

echo "</table>";

?>