<?php 	
include 'course.php';

$sql = "SELECT * FROM course";
$result = $connect->query($sql);
if ($result->num_rows> 0) {
	while ($row = $result->fetch_assoc()) {
		echo "coursename: " . $row["coursename"]. "duration: " . $row["duration"]. "department: " . $row["department"]. " <a href='update_course.php?id=" . $row["id"] . "'>update</a> <a href='delete_course.php?id=" . $row["id"] . "'>delete</a><br>"; 
	}
} else {
	echo "0 result";
}
?>