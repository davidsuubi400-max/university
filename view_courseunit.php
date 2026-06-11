<?php 	
include 'courseunit.php';

$sql = "SELECT * FROM courseunit";
$result = $connect->query($sql);
if ($result->num_rows> 0) {
	while ($row = $result->fetch_assoc()) {
		echo "coursecode: " . $row["coursecode"]. "coursename: " . $row["coursename"]. "yearofstudy: " . $row["yearofstudy"]. "semester: " . $row["semester"]. "department: " . $row["department"].
		"course: " . $row["course"]. " <a href='update_courseunit.php?id=" . $row["id"] . "'>update</a> <a href='delete_courseunit.php?id=" . $row["id"] . "'>delete</a><br>"; 
	}
} else {
	echo "0 result";
}
?>