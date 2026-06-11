<?php 	
include 'faculty.php';

$sql = "SELECT * FROM faculty";
$result = $connect->query($sql);
if ($result->num_rows> 0) {
	while ($row = $result->fetch_assoc()) {
		echo "name: " . $row["name"]. " <a href='update_faculty.php?id=" . $row["id"] . "'>update</a> <a href='delete_faculty.php?id=" . $row["id"] . "'>delete</a><br>"; 
	}
} else {
	echo "0 result";
}
?>