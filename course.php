<?php 
// config.php - Database configuration (create this file first)
// Make sure to update the database credentials to match your environment

/*
// Example content of config.php:
<?php
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'university_db';

$connect = mysqli_connect($host, $user, $password, $database);
if (!$connect) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
*/

// Include database configuration
include("config.php");

// Handle Add Course
if (isset($_POST['send'])) {
    $coursename = mysqli_real_escape_string($connect, $_POST['coursename']);
    $duration   = mysqli_real_escape_string($connect, $_POST['duration']);
    $department_id = intval($_POST['department_id']); // Now using department ID instead of name
    
    // Check if department exists
    $check_dept = mysqli_query($connect, "SELECT id FROM department WHERE id = $department_id");
    if (mysqli_num_rows($check_dept) > 0) {
        $sql = "INSERT INTO course(coursename, duration, department) VALUES('$coursename','$duration','$department_id')";
        $execute = mysqli_query($connect, $sql);
        
        if ($execute) {
            echo "<script>alert('Registered Successfully'); window.location.href='course.php';</script>";
        } else {
            echo "<script>alert('Error Occurred: " . mysqli_error($connect) . "');</script>";
        }
    } else {
        echo "<script>alert('Invalid Department Selected');</script>";
    }
}

// Handle Delete Course (if action is passed)
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $delete_sql = "DELETE FROM course WHERE id = $delete_id";
    if (mysqli_query($connect, $delete_sql)) {
        echo "<script>alert('Course Deleted Successfully'); window.location.href='course.php';</script>";
    } else {
        echo "<script>alert('Delete Failed: " . mysqli_error($connect) . "');</script>";
    }
}

// Handle Update Course
if (isset($_POST['update_course'])) {
    $update_id = intval($_POST['update_id']);
    $coursename = mysqli_real_escape_string($connect, $_POST['coursename']);
    $duration   = mysqli_real_escape_string($connect, $_POST['duration']);
    $department_id = intval($_POST['department_id']);
    
    // Check if department exists
    $check_dept = mysqli_query($connect, "SELECT id FROM department WHERE id = $department_id");
    if (mysqli_num_rows($check_dept) > 0) {
        $update_sql = "UPDATE course SET coursename='$coursename', duration='$duration', department='$department_id' WHERE id=$update_id";
        if (mysqli_query($connect, $update_sql)) {
            echo "<script>alert('Course Updated Successfully'); window.location.href='course.php';</script>";
        } else {
            echo "<script>alert('Update Failed: " . mysqli_error($connect) . "');</script>";
        }
    } else {
        echo "<script>alert('Invalid Department Selected');</script>";
    }
}

// Fetch all courses with department name using JOIN
$query = mysqli_query($connect, "SELECT course.*, department.name as dept_name FROM course LEFT JOIN department ON course.department = department.id ORDER BY course.id DESC");

// Fetch departments for dropdown
$departments_query = mysqli_query($connect, "SELECT id, name FROM department ORDER BY name");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Course Management | David Elementary University</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
    <!-- Font Awesome 6 -->
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
        .form-control, .form-select {
            border-radius: 14px;
            border: 1px solid #cfdfed;
            padding: 0.7rem 1rem;
            font-size: 0.95rem;
            transition: 0.2s;
        }
        .form-control:focus, .form-select:focus {
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
            cursor: pointer;
            border: none;
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

        /* Modal styles */
        .modal-content {
            border-radius: 24px;
            border: none;
        }
        .modal-header {
            border-bottom: 1px solid #eef2f8;
            background: #f8fafd;
            border-radius: 24px 24px 0 0;
        }
        .modal-footer {
            border-top: 1px solid #eef2f8;
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
            <li><a href="home.php"><i class="fas fa-user-graduate"></i> Dashboard</a></li>
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
                <form method="post" onsubmit="return confirm('Add this course?')">
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
                        <select name="department_id" class="form-select" required>
                            <option value="">Select Department</option>
                            <?php 
                            // Reset the departments query pointer
                            mysqli_data_seek($departments_query, 0);
                            while($dept = mysqli_fetch_assoc($departments_query)) {
                                echo "<option value='{$dept['id']}'>{$dept['name']}</option>";
                            }
                            ?>
                        </select>
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
                            if (mysqli_num_rows($query) > 0) {
                                while($row = mysqli_fetch_assoc($query)){
                            ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td class="fw-semibold"><?php echo htmlspecialchars($row['coursename']); ?></td>
                                <td><?php echo htmlspecialchars($row['duration']); ?></td>
                                <td><?php echo htmlspecialchars($row['dept_name'] ?? 'Not Assigned'); ?></td>
                                <td>
                                    <button onclick="openDeleteModal(<?php echo $row['id']; ?>)" class="btn-sm-action btn-delete me-2">
                                        <i class="fas fa-trash-alt me-1"></i> Delete
                                    </button>
                                    <button onclick="openUpdateModal(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars(addslashes($row['coursename'])); ?>', '<?php echo htmlspecialchars(addslashes($row['duration'])); ?>', <?php echo $row['department']; ?>)" class="btn-sm-action btn-update">
                                        <i class="fas fa-edit me-1"></i> Update
                                    </button>
                                 </td>
                             </tr>
                            <?php 
                                }
                            } else {
                            ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">No courses registered yet. Use the form to add one.</td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-trash-alt text-danger me-2"></i>Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this course? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <a href="#" id="confirmDeleteBtn" class="btn btn-danger">Delete</a>
            </div>
        </div>
    </div>
</div>

<!-- Update Course Modal -->
<div class="modal fade" id="updateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-edit text-primary me-2"></i>Update Course</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" onsubmit="return confirm('Save changes?')">
                <div class="modal-body">
                    <input type="hidden" name="update_id" id="update_id">
                    <div class="mb-3">
                        <label class="form-label">Course Name</label>
                        <input type="text" name="coursename" id="update_coursename" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Duration</label>
                        <input type="text" name="duration" id="update_duration" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Department</label>
                        <select name="department_id" id="update_department" class="form-select" required>
                            <option value="">Select Department</option>
                            <?php 
                            // Reset departments query for update modal
                            mysqli_data_seek($departments_query, 0);
                            while($dept = mysqli_fetch_assoc($departments_query)) {
                                echo "<option value='{$dept['id']}'>{$dept['name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="update_course" class="btn btn-primary">Update Course</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Footer -->
<div class="footer">
    <i class="fas fa-graduation-cap me-1"></i> David Elementary University — Academic Management System
</div>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Delete modal handlers
    let deleteId = null;
    
    function openDeleteModal(id) {
        deleteId = id;
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        const confirmBtn = document.getElementById('confirmDeleteBtn');
        confirmBtn.href = '?delete_id=' + id;
        deleteModal.show();
    }
    
    // Update modal handlers
    function openUpdateModal(id, coursename, duration, departmentId) {
        document.getElementById('update_id').value = id;
        document.getElementById('update_coursename').value = coursename;
        document.getElementById('update_duration').value = duration;
        document.getElementById('update_department').value = departmentId;
        
        const updateModal = new bootstrap.Modal(document.getElementById('updateModal'));
        updateModal.show();
    }
</script>

</body>
</html>