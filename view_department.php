<?php 	
include 'department.php';

$sql = "SELECT * FROM department";
$result = $connect->query($sql);
if ($result->num_rows> 0) {
	while ($row = $result->fetch_assoc()) {
		echo "name: " . $row["name"]. "faculty: " . $row["faculty"]. " <a href='update_department.php?id=" . $row["id"] . "'>update</a> <a href='delete_department.php?id=" . $row["id"] . "'>delete</a><br>"; 
	}
} else {
	echo "0 result";
}
?>