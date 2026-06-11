<?php 	
include 'student.php';
if(isset($_GET['id'])) {
	$id = $_GET['id'];
	$sql = "DELETE FROM student WHERE id=?";
	$stmt = $connect->prepare($sql);
	$stmt->bind_param("i", $id);
	if ($stmt->execute()) {
		echo "student deleted succussfully ";
		header("location: view_student.php");
	} else {
		echo "error: " . $connect->error;
    }

}

 ?>