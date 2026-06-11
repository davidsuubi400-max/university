<?php 	
include 'faculty.php';
if(isset($_GET['id'])) {
	$id = $_GET['id'];
	$sql = "DELETE FROM faculty WHERE id=?";
	$stmt = $connect->prepare($sql);
	$stmt->bind_param("i", $id);
	if ($stmt->execute()) {
		echo "faculty deleted succussfully";
		header("location: view_faculty.php");
	} else {
		echo "error: " . $connect->error;
    }

}

 ?>