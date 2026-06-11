<?php 
include("config.php");

if (isset($_POST['send'])) {
	$coursecode=$_POST['coursecode'];
	$coursename=$_POST['coursename'];
	$yearofstudy=$_POST['yearofstudy'];
	$semester=$_POST['semester'];
	$department=$_POST['department'];
	$course=$_POST['course'];
	$sql="INSERT INTO courseunit (coursecode,coursename,yearofstudy,semester,department,course)values('$coursecode','$coursename','$yearofstudy','$semester','$department','$course')";
	$execute=mysqli_query($connect,$sql);

	if ($execute) {
		?>
		<script>
			alert("Registered Successfully");
		</script>
		<?php
	}
	else{
		?>
		<script>
			alert("Error Occured");
		</script>
		<?php
	}
}

 ?>


<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<link rel="stylesheet" type="text/css" href="bootstrap/dist/css/bootstrap.min.css">
	<title></title>
	<style type="text/css">
.form-data{
			padding: 20px;
		}
		h4,h2{
			text-align: center;
			font-family:montserrati;
			padding-top:10px;

		}
		ul{
       	text-align: center;
       }
       li{
       	 text-decoration: none;
       	 list-style: none;
       	 display:inline-block;
       }
		a{
       	text-decoration: none;
       	color: white;
       	font-family: montserrati;
       	padding: 10px;
       	margin-top: 5px;
       }
		h1{
			color: white;
		}
       ul{
       	text-align: center;
       	</style>
</head>
<body>
<div class="container-fluid">
	<div class="row" style="background: blanchedalmond;">
		<div class="col-lg-12">
			<h2>NOVEL UNIVERSITY</h2>
			<h2>COURSE UNITY REGISTRATION</h2>
			<h4>"SUCCESS SUCCESS"</h4>
			
		</div>
		</div>
		<div class="row" style="background:black;">
		
			<ul>
				<a class="nav-link" href="home.php"><i class="fas fa-user-graduate"></i> Dashboard</a>
			<li><a href="student.php">student</a></li>
			<li><a href="course.php">course</a></li>
			<li><a href="faculty.php">faculty</a></li>
			<li><a href="department.php">department</a></li>
			<li><a href="courseunit.php">courseunit</a></li>
		</ul>
		</div>
	
	<div class="row">
		<div class="col-md-4" style="background:ivory;">
			<form method="post">
				<center><label>coursecode</label></center>
 	<center><input type="text" name="coursecode"></center>
 	<center><br><br>
 	<center><label>coursename</label></center>
 	<center><input type="text" name="coursename"></center>
 	<center><br><br>
 	<center><label>yearofstudy</label></center>
 	<center><input type="number" name="yearofstudy"></center>
 	<br><br>
 	<center><label>semester</label></center>
 	<center><input type="number" name="semester"></center>
 	<center><br><br>
 	<center><label>department</label></center>
 	<center><input type="text" name="department"></center>
 	<br><br>
 	<center><label>course</label></center>
 	<center><input type="text" name="course"></center>
 	<center><br><br>
 	<button type="Register" name="send">send</button>
 	<button type="reset">cancel</button>
 	<br>
			</form>

			
		</div>
		<div class="col-md-8">
			<table class="table table-bordered table-striped table-primary">
				<tr>
					<th>id</th>
					<th>coursecode</th>
				    <th>coursename</th>
					<th>yearofstudy</th>
					<th>semester</th>
					<th>department</th>
					<th>course</th>
					<th>delete</th>
					<th>update</th>
					

				</tr>
				<?php 	
					$query=mysqli_query($connect,"Select * from courseunit");
					while($row=mysqli_fetch_assoc($query)){
						?>
						<tr>
							<td><?php 	echo $row['id']; ?></td>
							<td><?php 	echo $row['coursecode']; ?></td>
							<td><?php 	echo $row['coursename']; ?></td>
							<td><?php 	echo $row['yearofstudy']; ?></td>
							<td><?php 	echo $row['semester']; ?></td>
							<td><?php 	echo $row['department']; ?></td>
							<td><?php 	echo $row['course']; ?></td>
							<td><button class="btn btn-danger"><a href="delete_course.php? deleteid='.$id.'"></a>delete</td>
							
						</tr>
						<?php
					}

				 ?>
				 

</body>
</html>