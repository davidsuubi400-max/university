<?php
// Include only the database connection, not the whole student.php
include("config.php");

// Check if the form was submitted for update
if (isset($_POST['submit'])) {
    // Sanitize inputs (basic, but consider using prepared statements for security)
    $id = $_POST['id'];
    $regno = $_POST['regno'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $contact = $_POST['contact'];
    $course = $_POST['course'];
    $department = $_POST['department'];

    // Update query
    $sql = "UPDATE student SET regno='$regno', firstname='$firstname', lastname='$lastname', contact='$contact', course='$course', department='$department' WHERE id=$id";

    if ($connect->query($sql) === TRUE) {
        // Success: redirect back to student.php
        // Use absolute path or relative as needed
        header("Location: student.php");
        exit; // Always call exit after header redirection
    } else {
        echo "Error updating record: " . $connect->error;
    }
}

// Check if an ID is provided via GET to load the student data
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM student WHERE id=$id";
    $result = $connect->query($sql);
    if ($result->num_rows == 0) {
        echo "Student not found.";
        exit;
    }
    $row = $result->fetch_assoc();
} else {
    // If no ID is provided, redirect back to student list
    header("Location: student.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Student | David Elementary University</title>
    <!-- Bootstrap CSS (ensure path is correct) -->
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
        <h2><i class="fas fa-user-edit"></i> Edit Student Information</h2>
        <form method="post" action="">
            <div class="mb-3">
                <label for="regno" class="form-label">Registration Number</label>
                <input type="text" class="form-control" id="regno" name="regno" value="<?php echo htmlspecialchars($row['regno']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="firstname" class="form-label">First Name</label>
                <input type="text" class="form-control" id="firstname" name="firstname" value="<?php echo htmlspecialchars($row['firstname']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="lastname" class="form-label">Last Name</label>
                <input type="text" class="form-control" id="lastname" name="lastname" value="<?php echo htmlspecialchars($row['lastname']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="contact" class="form-label">Contact Number</label>
                <input type="number" class="form-control" id="contact" name="contact" value="<?php echo htmlspecialchars($row['contact']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="course" class="form-label">Course</label>
                <input type="text" class="form-control" id="course" name="course" value="<?php echo htmlspecialchars($row['course']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="department" class="form-label">Department</label>
                <input type="text" class="form-control" id="department" name="department" value="<?php echo htmlspecialchars($row['department']); ?>" required>
            </div>
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <div class="d-flex justify-content-start mt-4">
                <button type="submit" name="submit" class="btn btn-update text-white">Update Student</button>
                <a href="student.php" class="btn btn-cancel">Cancel</a>
            </div>
        </form>
    </div>
</div>
<!-- Optional Font Awesome (if you want icons) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</body>
</html>