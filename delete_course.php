<?php 	
include 'course.php';
if(isset($_GET['id'])) {
	$id = $_GET['id'];
	$sql = "DELETE FROM course WHERE id=?";
	$stmt = $connect->prepare($sql);
	$stmt->bind_param("i", $id);
	if ($stmt->execute()) {
		echo "course deleted succussfully " );
		header("location: view_course.php");
	} else {
		echo "error: " . $connect->error;
    }

}

 ?>