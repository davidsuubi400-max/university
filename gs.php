<?php 
include("config.php");
if (isset($_POST['send'])) {
    $coursename = $_POST['coursename'];
    $duration   = $_POST['duration'];
    $department = $_POST['department'];
    $sql = "INSERT INTO course(coursename, duration, department) values('$coursename','$duration','$department')";
    $execute = mysqli_query($connect, $sql);

    if ($execute) {
        ?>
        <script>
            alert("Registered Successfully");
        </script>
        <?php
    } else {
        ?>
        <script>
            alert("Error Occured");
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
    <title>Course Management | David Elementary University</title>
    <!-- Bootstrap 5 CSS (modern replacement for the old bootstrap) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts for professional typography -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
    <!-- Font Awesome 6 (free icons) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f4f7fc;
            color: #1e2a3e;
        }

        /* Header styling */
        .uni-header {
            background: linear-gradient(135deg, #0b2b3b 0%, #1a4a5f 100%);
            color: white;
            padding: 2rem 0;
            text-align: center;
            border-bottom: 4px solid #f4c542;
        }
        .uni-header h1 {
            font-weight: 700;
            letter-spacing: -0.5px;
            font-size: 2.4rem;
            margin-bottom: 0.25rem;
        }
        .uni-header .motto {
            font-style: italic;
            font-weight: 300;
            font-size: 1.1rem;
            opacity: 0.9;
        }

        /* Navigation styling */
        .nav-menu {
            background: #ffffff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            border-bottom: 1px solid #e2e8f0;
        }
        .nav-menu .nav-links {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 0.25rem;
            padding: 0.75rem 0;
            margin: 0;
        }
        .nav-menu .nav-links li {
            list-style: none;
        }
        .nav-menu .nav-links a {
            text-decoration: none;
            font-weight: 500;
            padding: 0.6rem 1.4rem;
            border-radius: 40px;
            background: transparent;
            color: #2c3e50;
            transition: all 0.2s ease;
            font-size: 0.95rem;
            display: inline-block;
        }
        .nav-menu .nav-links a:hover {
            background: #eef2f8;
            color: #1a4a5f;
        }
        .nav-menu .nav-links a:active, .nav-menu .nav-links a.active {
            background: #1e4a6e;
            color: white;
        }

        /* Main content container */
        .main-container {
            max-width: 1400px;
            margin: 2rem auto;
            padding: 0 1.5rem;
        }

        /* Form card */
        .form-card {
            background: white;
            border-radius: 24px;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05), 0 8px 10px -6px rgba(0,0,0,0.02);
            padding: 1.8rem;
            height: 100%;
            border: 1px solid #eef2f6;
            transition: transform 0.1s ease;
        }
        .form-card h3 {
            font-weight: 700;
            font-size: 1.5rem;
            color: #0f2b38;
            border-left: 5px solid #f4c542;
            padding-left: 1rem;
            margin-bottom: 1.5rem;
        }
        .form-label {
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: #2c3e50;
        }
        .form-control {
            border-radius: 14px;
            border: 1px solid #cfdfed;
            padding: 0.7rem 1rem;
            font-size: 0.95rem;
            transition: 0.2s;
        }
        .form-control:focus {
            border-color: #1e4a6e;
            box-shadow: 0 0 0 3px rgba(30,74,110,0.2);
        }
        .btn-send {
            background: #1e4a6e;
            border: none;
            padding: 0.7rem 1.8rem;
            border-radius: 40px;
            font-weight: 600;
            color: white;
            transition: all 0.2s;
        }
        .btn-send:hover {
            background: #0f3a55;
            transform: translateY(-1px);
            box-shadow: 0 5px 12px rgba(0,0,0,0.1);
        }
        .btn-cancel {
            background: #eef2f8;
            border: none;
            padding: 0.7rem 1.8rem;
            border-radius: 40px;
            font-weight: 500;
            color: #4a627a;
            margin-left: 0.8rem;
        }
        .btn-cancel:hover {
            background: #e2e8f0;
        }

        /* Table card */
        .table-card {
            background: white;
            border-radius: 24px;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05);
            padding: 1.5rem;
            border: 1px solid #eef2f6;
            height: 100%;
        }
        .table-card h3 {
            font-weight: 700;
            font-size: 1.5rem;
            color: #0f2b38;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .table-responsive {
            border-radius: 18px;
            overflow-x: auto;
        }
        .table {
            margin-bottom: 0;
            font-size: 0.9rem;
        }
        .table thead {
            background: #eef2fa;
        }
        .table th {
            font-weight: 600;
            color: #1e4663;
            border-bottom: 2px solid #dce5ed;
            padding: 1rem 0.8rem;
        }
        .table td {
            padding: 0.9rem 0.8rem;
            vertical-align: middle;
            border-color: #eef2f8;
        }
        .table tbody tr:hover {
            background-color: #fafcff;
        }
        .btn-sm-action {
            padding: 0.3rem 0.9rem;
            border-radius: 30px;
            font-size: 0.8rem;
            font-weight: 500;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-block;
        }
        .btn-delete {
            background: #fee2e2;
            color: #b91c1c;
        }
        .btn-delete:hover {
            background: #fecaca;
            color: #991b1b;
        }
        .btn-update {
            background: #e2f0ff;
            color: #1e4a6e;
        }
        .btn-update:hover {
            background: #c9e2ff;
            color: #0e3a55;
        }

        /* footer */
        .footer {
            text-align: center;
            margin-top: 3rem;
            padding: 1.5rem;
            font-size: 0.85rem;
            color: #5f7f9e;
            border-top: 1px solid #e0ecf5;
            background: #ffffff;
        }

        /* Responsive tweaks */
        @media (max-width: 768px) {
            .main-container {
                padding: 0 1rem;
            }
            .form-card, .table-card {
                padding: 1.2rem;
                margin-bottom: 1.5rem;
            }
            .nav-menu .nav-links a {
                padding: 0.4rem 1rem;
                font-size: 0.85rem;
            }
        }
    </style>
</head>
<body>

<!-- University Header -->
<div class="uni-header">
    <div class="container">
        <h1>DAVID ELEMENTARY UNIVERSITY</h1>
        <div class="motto">"SUCCESS · INTEGRITY · EXCELLENCE"</div>
    </div>
</div>

<!-- Navigation Menu -->
<div class="nav-menu">
    <div class="container">
        <ul class="nav-links">
            <li><a href="student.php"><i class="fas fa-user-graduate me-1"></i> Student</a></li>
            <li><a href="course.php" class="active"><i class="fas fa-book-open me-1"></i> Course</a></li>
            <li><a href="faculty.php"><i class="fas fa-chalkboard-user me-1"></i> Faculty</a></li>
            <li><a href="department.php"><i class="fas fa-building me-1"></i> Department</a></li>
            <li><a href="courseunit.php"><i class="fas fa-layer-group me-1"></i> Course Unit</a></li>
            <li><a href="staff.php"><i class="fas fa-users me-1"></i> Staff</a></li>
        </ul>
    </div>
</div>

<!-- Main Content -->
<div class="main-container">
    <div class="row g-4">
        <!-- Form Column (Add Course) -->
        <div class="col-lg-4 col-md-12">
            <div class="form-card">
                <h3><i class="fas fa-plus-circle me-2" style="color:#f4c542;"></i> Register New Course</h3>
                <form method="post">
                    <div class="mb-3">
                        <label class="form-label"><i class="fas fa-tag me-1"></i> Course Name</label>
                        <input type="text" name="coursename" class="form-control" placeholder="e.g., Introduction to Programming" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><i class="fas fa-hourglass-half me-1"></i> Duration</label>
                        <input type="text" name="duration" class="form-control" placeholder="e.g., 4 Years / 1 Semester">
                    </div>
                    <div class="mb-4">
                        <label class="form-label"><i class="fas fa-university me-1"></i> Department</label>
                        <input type="text" name="department" class="form-control" placeholder="e.g., Computer Science">
                    </div>
                    <div>
                        <button type="submit" name="send" class="btn-send"><i class="fas fa-save me-1"></i> Save Course</button>
                        <button type="reset" class="btn-cancel"><i class="fas fa-undo-alt me-1"></i> Reset</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Table Column (Course List) -->
        <div class="col-lg-8 col-md-12">
            <div class="table-card">
                <h3><i class="fas fa-table-list me-2" style="color:#1e4a6e;"></i> Course Directory</h3>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>#ID</th>
                                <th>Course Name</th>
                                <th>Duration</th>
                                <th>Department</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $query = mysqli_query($connect, "Select * from course");
                            while($row = mysqli_fetch_assoc($query)){
                            ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td class="fw-semibold"><?php echo htmlspecialchars($row['coursename']); ?></td>
                                <td><?php echo htmlspecialchars($row['duration']); ?></td>
                                <td><?php echo htmlspecialchars($row['department']); ?></td>
                                <td>
                                    <a href="delete_course.php?id=<?php echo $row['id']; ?>" class="btn-sm-action btn-delete me-2" onclick="return confirm('Are you sure you want to delete this course?')">
                                        <i class="fas fa-trash-alt me-1"></i> Delete
                                    </a>
                                    <a href="update_course.php?id=<?php echo $row['id']; ?>" class="btn-sm-action btn-update">
                                        <i class="fas fa-edit me-1"></i> Update
                                    </a>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <!-- optional note if no courses -->
                <?php if(mysqli_num_rows($query) == 0): ?>
                    <div class="text-center text-muted py-4">No courses registered yet. Use the form to add one.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Footer -->
<div class="footer">
    <i class="fas fa-graduation-cap me-1"></i> David Elementary University — Academic Management System
</div>

<!-- optional: Bootstrap JS bundle (for any interactive components) not strictly needed but keeps modern -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>