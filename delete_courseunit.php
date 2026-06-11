<?php 	
include 'courseunit.php';
if(isset($_GET['id'])) {
	$id = $_GET['id'];
	$sql = "DELETE FROM courseunit WHERE id=?";
	$stmt = $connect->prepare($sql);
	$stmt->bind_param("i", $id);
	if ($stmt->execute()) {
		echo "courseunit deleted succussfully";
		header("location: view_courseunit.php");
	} else {
		echo "error: " . $connect->error;
    }

}

 ?>