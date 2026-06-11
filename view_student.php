<?php 	
include 'student.php';

$sql = "SELECT * FROM student";
$result = $connect->query($sql);
if ($result->num_rows> 0) {
	while ($row = $result->fetch_assoc()) {
		echo "regno: " . $row["regno"]. "- name: " . $row["firstname"]. " ". $row["lastname"]. " - contact: " . $row["contact"]. " - course: " . $row["course"]. " - department: " . $row["department"]. "<br>";
		echo  "<a href='update_student.php?id=" . $row["id"] . "'>update</a> <a href='delete_student.php?id=" . $row["id"] . "'>delete</a><br>"; 
	}
} else {
	echo "0 result";
}
?>