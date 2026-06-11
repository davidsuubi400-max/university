<?php 	
include 'staff.php';

$sql = "SELECT * FROM staff";
$result = $connect->query($sql);
if ($result->num_rows> 0) {
	while ($row = $result->fetch_assoc()) {
		echo "staffno: " . $row["staffno"]. "- name: " . $row["firstname"]. " ". $row["lastname"]. " - contact: " . $row["contact"]. " - gender: " . $row["gender"]. " - designation: " . $row["designation"]. " - department: " . $row["department"]. "<br>";
		echo  "<a href='update_student.php?id=" . $row["id"] . "'>update</a> <a href='delete_student.php?id=" . $row["id"] . "'>delete</a><br>"; 
	}
} else {
	echo "0 result";
}
?>