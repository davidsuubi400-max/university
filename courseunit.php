<?php 
include("config.php");

if (isset($_POST['send'])) {
    $coursecode = $_POST['coursecode'];
    $coursename = $_POST['coursename'];
    $yearofstudy = $_POST['yearofstudy'];
    $semester = $_POST['semester'];
    $department = $_POST['department'];
    $course = $_POST['course'];

    $sql = "INSERT INTO courseunity (coursecode, coursename, yearofstudy, semester, department, course) 
            VALUES ('$coursecode', '$coursename', '$yearofstudy', '$semester', '$department', '$course')";
    $execute = mysqli_query($connect, $sql);

    if ($execute) {
        ?>
        <script>
            alert("✅ Course Unit Registered Successfully");
        </script>
        <?php
    } else {
        ?>
        <script>
            alert("❌ Error Occurred. Please try again.");
        </script>
        <?php
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Course Unit Management | David Elementary University</title>
    <!-- Bootstrap 4 CSS (local path preserved) -->
    <link rel="stylesheet" type="text/css" href="bootstrap/dist/css/bootstrap.min.css">
    <!-- Google Fonts for professional typography -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }
        body {
            background: #f4f7fc;
            margin: 0;
            padding: 0;
        }
        /* university header styling */
        .uni-header {
            background: linear-gradient(135deg, #0b2b40 0%, #154e6b 100%);
            color: white;
            padding: 1.8rem 0;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        .uni-header h1 {
            font-weight: 600;
            letter-spacing: -0.3px;
            margin: 0;
            font-size: 1.9rem;
        }
        .uni-header .motto {
            margin: 0;
            font-size: 0.9rem;
            opacity: 0.9;
        }
        /* modern navigation - white background */
        .nav-links {
            background: #ffffff;
            padding: 0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            border-bottom: 1px solid #e9ecef;
        }
        .nav-links .nav-link {
            color: #2c3e4e;
            padding: 0.9rem 1.6rem;
            font-weight: 500;
            transition: all 0.2s ease;
            border-bottom: 3px solid transparent;
        }
        .nav-links .nav-link:hover {
            background: #f8f9fa;
            color: #1f6e8c;
            border-bottom-color: #ffb347;
            text-decoration: none;
        }
        /* cards & form */
        .form-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 12px 28px rgba(0,0,0,0.08);
            border: none;
            overflow: hidden;
            transition: transform 0.2s;
        }
        .form-card .card-header {
            background: #fef9ef;
            border-bottom: 1px solid #e9ecef;
            padding: 1rem 1.5rem;
            font-weight: 600;
            font-size: 1.2rem;
            color: #1f5068;
        }
        .form-card .card-header i {
            margin-right: 8px;
            color: #ff8c42;
        }
        .form-control, .form-control:focus {
            border-radius: 12px;
            padding: 0.6rem 1rem;
            border: 1px solid #cfdee9;
            transition: 0.2s;
        }
        .form-control:focus {
            border-color: #ffb347;
            box-shadow: 0 0 0 3px rgba(255,180,71,0.2);
        }
        .btn-send {
            background: #1f6e8c;
            border: none;
            border-radius: 40px;
            padding: 8px 28px;
            font-weight: 500;
            transition: all 0.2s;
        }
        .btn-send:hover {
            background: #0e4e66;
            transform: translateY(-1px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.1);
        }
        .btn-reset {
            background: #e9ecef;
            border: none;
            border-radius: 40px;
            padding: 8px 28px;
            color: #4a6272;
            font-weight: 500;
        }
        .btn-reset:hover {
            background: #dee2e6;
        }
        /* table card */
        .table-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 12px 28px rgba(0,0,0,0.08);
            border: none;
            overflow-x: auto;
        }
        .table-card .card-header {
            background: #fef9ef;
            border-bottom: none; /* Removed white line */
            font-weight: 600;
            font-size: 1.1rem;
            padding: 1rem 1.5rem;
        }
        .table {
            margin-bottom: 0;
        }
        .table thead th {
            background: #eef3fa;
            color: #1f5068;
            font-weight: 600;
            border-bottom: 2px solid #d4e0e9;
            padding: 1rem 0.8rem;
            font-size: 0.9rem;
        }
        .table tbody td {
            vertical-align: middle;
            padding: 0.8rem;
            color: #2c3e4e;
        }
        .table-hover tbody tr:hover {
            background-color: #fef5e9;
            transition: 0.1s;
        }
        .btn-action {
            border-radius: 30px;
            padding: 5px 14px;
            font-size: 0.75rem;
            font-weight: 500;
            margin: 0 2px;
        }
        .btn-delete {
            background: #ffe6e5;
            color: #c13b2b;
            border: 1px solid #ffcdc9;
        }
        .btn-delete:hover {
            background: #f8d7da;
            color: #a71d2a;
        }
        .btn-update {
            background: #e3f0fa;
            color: #1f6e8c;
            border: 1px solid #cde1ef;
        }
        .btn-update:hover {
            background: #cde1f0;
            color: #0a4b64;
        }
        footer {
            background: #eef2f5;
            margin-top: 3rem;
            padding: 1.5rem;
            text-align: center;
            font-size: 0.8rem;
            color: #5b7c8e;
            border-top: 1px solid #dce5ec;
        }
        @media (max-width: 768px) {
            .nav-links .nav-link {
                padding: 0.6rem 1rem;
                font-size: 0.85rem;
            }
            .uni-header h1 {
                font-size: 1.4rem;
            }
            .form-card, .table-card {
                margin-bottom: 1.8rem;
            }
        }
    </style>
</head>
<body>

<div class="container-fluid px-0">

    <!-- HEADER SECTION with modern gradient -->
    <div class="uni-header text-center">
        <div class="container">
            <h1>DAVID ELEMENTARY UNIVERSITY</h1>
            <div class="motto">"SUCCESS · INTEGRITY · EXCELLENCE"</div>
        </div>
    </div>

    <!-- PROFESSIONAL NAVIGATION MENU (white background) -->
    <div class="nav-links text-center">
        <div class="container">
            <div class="nav justify-content-center">
                <a class="nav-link" href="home.php"><i class="fas fa-user-graduate"></i> Dashboard</a>
                <a class="nav-link" href="student.php"><i class="fas fa-user-graduate"></i> Student</a>
                <a class="nav-link" href="course.php"><i class="fas fa-book-open"></i> Course</a>
                <a class="nav-link" href="faculty.php"><i class="fas fa-chalkboard-user"></i> Faculty</a>
                <a class="nav-link" href="department.php"><i class="fas fa-building"></i> Department</a>
                <a class="nav-link" href="courseunit.php"><i class="fas fa-layer-group"></i> Courseunit</a>
                <a class="nav-link" href="staff.php"><i class="fas fa-users"></i> Staff</a>
            </div>
        </div>
    </div>

    <!-- MAIN CONTENT: FORM + TABLE SIDE BY SIDE -->
    <div class="container mt-5 mb-4">
        <div class="row">
            <!-- LEFT COLUMN: REGISTRATION FORM -->
            <div class="col-lg-5 col-md-12 mb-4">
                <div class="form-card">
                    <div class="card-header">
                        <i class="fas fa-layer-group"></i> Register Course Unit
                    </div>
                    <div class="card-body p-4">
                        <form method="post">
                            <div class="form-group mb-3">
                                <label for="coursecode" class="form-label fw-semibold">Course Code</label>
                                <input type="text" class="form-control" id="coursecode" name="coursecode" placeholder="e.g., CS101" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="coursename" class="form-label fw-semibold">Course Name</label>
                                <input type="text" class="form-control" id="coursename" name="coursename" placeholder="e.g., Introduction to Programming" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="yearofstudy" class="form-label fw-semibold">Year of Study</label>
                                <input type="number" class="form-control" id="yearofstudy" name="yearofstudy" placeholder="e.g., 1" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="semester" class="form-label fw-semibold">Semester</label>
                                <input type="number" class="form-control" id="semester" name="semester" placeholder="e.g., 1" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="department" class="form-label fw-semibold">Department</label>
                                <input type="text" class="form-control" id="department" name="department" placeholder="e.g., Computing & Informatics" required>
                            </div>
                            <div class="form-group mb-4">
                                <label for="course" class="form-label fw-semibold">Course</label>
                                <input type="text" class="form-control" id="course" name="course" placeholder="e.g., Computer Science" required>
                            </div>
                            <div class="d-flex gap-3 justify-content-start">
                                <button type="submit" name="send" class="btn btn-send text-white"><i class="fas fa-save me-1"></i> Register Unit</button>
                                <button type="reset" class="btn btn-reset"><i class="fas fa-undo-alt me-1"></i> Clear</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN: COURSE UNITS LIST TABLE -->
            <div class="col-lg-7 col-md-12">
                <div class="table-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-table-list me-2"></i> Registered Course Units</span>
                        <span class="badge bg-secondary rounded-pill px-3 py-2">Updated in real-time</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Course Code</th>
                                    <th>Course Name</th>
                                    <th>Year</th>
                                    <th>Semester</th>
                                    <th>Department</th>
                                    <th>Course</th>
                                    <th colspan="2" class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                    $query = mysqli_query($connect, "SELECT * FROM courseunity");
                                    if (mysqli_num_rows($query) > 0) {
                                        while ($row = mysqli_fetch_assoc($query)) {
                                ?>
                                <tr>
                                    <td class="fw-semibold"><?php echo $row['id']; ?></td>
                                    <td><?php echo htmlspecialchars($row['coursecode']); ?></td>
                                    <td><?php echo htmlspecialchars($row['coursename']); ?></td>
                                    <td><?php echo htmlspecialchars($row['yearofstudy']); ?></td>
                                    <td><?php echo htmlspecialchars($row['semester']); ?></td>
                                    <td><?php echo htmlspecialchars($row['department']); ?></td>
                                    <td><?php echo htmlspecialchars($row['course']); ?></td>
                                    <td class="text-center">
                                        <a href="delete_courseunit.php?id=<?php echo $row['id']; ?>" class="btn btn-action btn-delete" onclick="return confirm('Are you sure you want to delete this course unit?');">
                                            <i class="fas fa-trash-alt me-1"></i> Delete
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <a href="update_courseunit.php?id=<?php echo $row['id']; ?>" class="btn btn-action btn-update">
                                            <i class="fas fa-edit me-1"></i> Update
                                        </a>
                                    </td>
                                </tr>
                                <?php 
                                        }
                                    } else {
                                        echo '<tr><td colspan="9" class="text-center py-4 text-muted"><i class="fas fa-database me-2"></i>No course units registered yet. Use the form to add.</td></tr>';
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FOOTER -->
    <footer>
        <div class="container">
            <i class="fas fa-graduation-cap me-1"></i> David Elementary University — Empowering future leaders
            <br><small>© <?php echo date("Y"); ?> | Course Unit Management System</small>
        </div>
    </footer>
</div>

<!-- Optional Bootstrap JS bundle -->
<script src="bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>