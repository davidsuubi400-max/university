<?php 	
include 'department.php';
if(isset($_GET['id'])) {
	$id = $_GET['id'];
	$sql = "DELETE FROM department WHERE id=?";
	$stmt = $connect->prepare($sql);
	$stmt->bind_param("i", $id);
	if ($stmt->execute()) {
		echo "department deleted succussfully";
		header("location: view_department.php");
	} else {
		echo "error: " . $connect->error;
    }

}

 ?>