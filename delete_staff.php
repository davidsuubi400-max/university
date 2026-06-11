<?php 	
include 'staff.php';
if(isset($_GET['id'])) {
	$id = $_GET['id'];
	$sql = "DELETE FROM staff WHERE id=?";
	$stmt = $connect->prepare($sql);
	$stmt->bind_param("i", $id);
	if ($stmt->execute()) {
		echo "staff deleted succussfully";
		header("location: view_staff.php");
	} else {
		echo "error: " . $connect->error;
    }

}

 ?>