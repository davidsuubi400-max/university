<?php
// Include only the database connection
include("config.php");

// Process the update when form is submitted
if (isset($_POST['submit'])) {
    $id = $_POST['id'];
    $coursename = $_POST['coursename'];
    $duration = $_POST['duration'];
    $department = $_POST['department'];

    // Basic update query (consider using prepared statements for security)
    $sql = "UPDATE course SET coursename='$coursename', duration='$duration', department='$department' WHERE id=$id";

    if ($connect->query($sql) === TRUE) {
        // Redirect to the course listing page after successful update
        header("Location: course.php");
        exit; // Always call exit after header redirection
    } else {
        echo "Error updating record: " . $connect->error;
        exit;
    }
}

// Check if an ID is provided via GET to load the course data
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM course WHERE id=$id";
    $result = $connect->query($sql);

    if ($result->num_rows == 0) {
        echo "Course not found.";
        exit;
    }
    $row = $result->fetch_assoc();
} else {
    // If no ID is provided, redirect back to the course list
    header("Location: course.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Course | David Elementary University</title>
    <link rel="stylesheet" type="text/css" href="bootstrap/dist/css/bootstrap.min.css">
    <style>
        body {
            background: #f4f7fc;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .update-container {
            max-width: 600px;
            margin: 50px auto;
            background: white;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        }
        .update-container h2 {
            color: #1f5068;
            margin-bottom: 25px;
            font-weight: 600;
            border-left: 5px solid #ffb347;
            padding-left: 15px;
        }
        .btn-update {
            background: #1f6e8c;
            border: none;
            border-radius: 40px;
            padding: 8px 28px;
            font-weight: 500;
        }
        .btn-update:hover {
            background: #0e4e66;
        }
        .btn-cancel {
            background: #e9ecef;
            border: none;
            border-radius: 40px;
            padding: 8px 28px;
            color: #4a6272;
            margin-left: 10px;
        }
        .form-label {
            font-weight: 500;
            color: #2c5a6e;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="update-container">
        <h2><i class="fas fa-book-open"></i> Edit Course Information</h2>
        <form method="post" action="">
            <div class="mb-3">
                <label for="coursename" class="form-label">Course Name</label>
                <input type="text" class="form-control" id="coursename" name="coursename" value="<?php echo htmlspecialchars($row['coursename']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="duration" class="form-label">Duration (in years)</label>
                <input type="number" class="form-control" id="duration" name="duration" value="<?php echo htmlspecialchars($row['duration']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="department" class="form-label">Department</label>
                <input type="text" class="form-control" id="department" name="department" value="<?php echo htmlspecialchars($row['department']); ?>" required>
            </div>
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <div class="d-flex justify-content-start mt-4">
                <button type="submit" name="submit" class="btn btn-update text-white">Update Course</button>
                <a href="course.php" class="btn btn-cancel">Cancel</a>
            </div>
        </form>
    </div>
</div>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</body>
</html>