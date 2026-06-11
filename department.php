<?php 
// config.php - Database configuration
if (!file_exists('config.php')) {
    $host   = "localhost";
    $user   = "root";
    $pass   = "";
    $dbname = "university";
    
    $connect = mysqli_connect($host, $user, $pass, $dbname);
    
    if (!$connect) {
        die("<div style='font-family:sans-serif;padding:2rem;color:red;'>
            <h3>❌ Database Connection Failed</h3>
            <p>" . mysqli_connect_error() . "</p>
        </div>");
    }
} else {
    include("config.php");
    
    if (!$connect) {
        die("<div style='font-family:sans-serif;padding:2rem;color:red;'>
            <h3>❌ Database Connection Failed</h3>
            <p>Please check your config.php file.</p>
        </div>");
    }
}

// ─────────────────────────────────────────────
//  CHECK ACTUAL COLUMN NAMES IN FACULTY TABLE
// ─────────────────────────────────────────────

// First, let's see what columns exist in faculty table
$faculty_id_col = 'id'; // Assume 'id' since you said columns are 'name'
$faculty_name_col = 'name';

$check_faculty_cols = mysqli_query($connect, "SHOW COLUMNS FROM faculty");
if ($check_faculty_cols) {
    while ($col = mysqli_fetch_assoc($check_faculty_cols)) {
        if ($col['Field'] == 'faculty_id') $faculty_id_col = 'faculty_id';
        if ($col['Field'] == 'id') $faculty_id_col = 'id';
        if ($col['Field'] == 'faculty_name') $faculty_name_col = 'faculty_name';
        if ($col['Field'] == 'name') $faculty_name_col = 'name';
    }
}

// Check department table columns
$dept_id_col = 'id';
$dept_name_col = 'name';
$dept_faculty_col = 'faculty_id';

$check_dept_cols = mysqli_query($connect, "SHOW COLUMNS FROM department");
if ($check_dept_cols) {
    while ($col = mysqli_fetch_assoc($check_dept_cols)) {
        if ($col['Field'] == 'id') $dept_id_col = 'id';
        if ($col['Field'] == 'dept_id') $dept_id_col = 'dept_id';
        if ($col['Field'] == 'name') $dept_name_col = 'name';
        if ($col['Field'] == 'dept_name') $dept_name_col = 'dept_name';
        if ($col['Field'] == 'faculty_id') $dept_faculty_col = 'faculty_id';
        if ($col['Field'] == 'faculty') $dept_faculty_col = 'faculty';
    }
}

// ─────────────────────────────────────────────
//  ENSURE DEPARTMENT TABLE HAS CORRECT STRUCTURE
// ─────────────────────────────────────────────

// Check if department table exists
$check_dept_table = mysqli_query($connect, "SHOW TABLES LIKE 'department'");
$dept_table_exists = mysqli_num_rows($check_dept_table) > 0;

if (!$dept_table_exists) {
    // Create department table with correct structure matching faculty
    $create_department_table = "
    CREATE TABLE department (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        faculty_id INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if (!mysqli_query($connect, $create_department_table)) {
        die("Error creating department table: " . mysqli_error($connect));
    }
}

// ─────────────────────────────────────────────
//  HANDLE: INSERT NEW DEPARTMENT
// ─────────────────────────────────────────────
$success_msg = "";
$error_msg = "";

if (isset($_POST['send'])) {
    $name = trim($_POST['name']);
    $faculty_id = intval($_POST['faculty_id']);
    
    if (empty($name)) {
        $error_msg = "Department name cannot be empty.";
    } elseif ($faculty_id <= 0) {
        $error_msg = "Please select a valid faculty.";
    } else {
        // Check if faculty exists using the correct column name
        $check_faculty = mysqli_prepare($connect, "SELECT $faculty_id_col FROM faculty WHERE $faculty_id_col = ?");
        mysqli_stmt_bind_param($check_faculty, "i", $faculty_id);
        mysqli_stmt_execute($check_faculty);
        mysqli_stmt_store_result($check_faculty);
        
        if (mysqli_stmt_num_rows($check_faculty) > 0) {
            // Insert department
            $stmt = mysqli_prepare($connect, "INSERT INTO department ($dept_name_col, $dept_faculty_col) VALUES (?, ?)");
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "si", $name, $faculty_id);
                if (mysqli_stmt_execute($stmt)) {
                    $success_msg = "Department <strong>" . htmlspecialchars($name) . "</strong> registered successfully.";
                } else {
                    $error_msg = "Database error: " . mysqli_stmt_error($stmt);
                }
                mysqli_stmt_close($stmt);
            } else {
                $error_msg = "Prepare failed: " . mysqli_error($connect);
            }
        } else {
            $error_msg = "Selected faculty does not exist. Please add the faculty first.";
        }
        mysqli_stmt_close($check_faculty);
    }
}

// ─────────────────────────────────────────────
//  HANDLE: DELETE DEPARTMENT
// ─────────────────────────────────────────────
if (isset($_GET['delete_id'])) {
    $del_id = intval($_GET['delete_id']);
    
    $stmt = mysqli_prepare($connect, "DELETE FROM department WHERE $dept_id_col = ?");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $del_id);
        if (mysqli_stmt_execute($stmt)) {
            $success_msg = "Department deleted successfully.";
        } else {
            $error_msg = "Delete failed: " . mysqli_stmt_error($stmt);
        }
        mysqli_stmt_close($stmt);
    } else {
        $error_msg = "Delete prepare failed: " . mysqli_error($connect);
    }
}

// ─────────────────────────────────────────────
//  HANDLE: UPDATE DEPARTMENT
// ─────────────────────────────────────────────
if (isset($_POST['update_department'])) {
    $update_id = intval($_POST['update_id']);
    $name = trim($_POST['name']);
    $faculty_id = intval($_POST['faculty_id']);
    
    if (empty($name)) {
        $error_msg = "Department name cannot be empty.";
    } elseif ($faculty_id <= 0) {
        $error_msg = "Please select a valid faculty.";
    } else {
        // Check if faculty exists
        $check_faculty = mysqli_prepare($connect, "SELECT $faculty_id_col FROM faculty WHERE $faculty_id_col = ?");
        mysqli_stmt_bind_param($check_faculty, "i", $faculty_id);
        mysqli_stmt_execute($check_faculty);
        mysqli_stmt_store_result($check_faculty);
        
        if (mysqli_stmt_num_rows($check_faculty) > 0) {
            $stmt = mysqli_prepare($connect, "UPDATE department SET $dept_name_col = ?, $dept_faculty_col = ? WHERE $dept_id_col = ?");
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "sii", $name, $faculty_id, $update_id);
                if (mysqli_stmt_execute($stmt)) {
                    $success_msg = "Department updated successfully.";
                } else {
                    $error_msg = "Update failed: " . mysqli_stmt_error($stmt);
                }
                mysqli_stmt_close($stmt);
            } else {
                $error_msg = "Update prepare failed: " . mysqli_error($connect);
            }
        } else {
            $error_msg = "Selected faculty does not exist.";
        }
        mysqli_stmt_close($check_faculty);
    }
}

// ─────────────────────────────────────────────
//  FETCH: ALL DEPARTMENTS with faculty name
// ─────────────────────────────────────────────
$dept_query = mysqli_query($connect, 
    "SELECT d.$dept_id_col as id, d.$dept_name_col as name, 
            f.$faculty_id_col as faculty_id, f.$faculty_name_col as faculty_name 
     FROM department d
     LEFT JOIN faculty f ON d.$dept_faculty_col = f.$faculty_id_col
     ORDER BY d.$dept_id_col DESC"
);

if (!$dept_query) {
    $error_msg = "Database query error: " . mysqli_error($connect);
    $dept_count = 0;
} else {
    $dept_count = mysqli_num_rows($dept_query);
}

// Fetch faculties for dropdown - using 'id' and 'name' as column names
$faculties_query = mysqli_query($connect, "SELECT $faculty_id_col as faculty_id, $faculty_name_col as faculty_name FROM faculty ORDER BY $faculty_name_col");

if (!$faculties_query) {
    $faculties_count = 0;
} else {
    $faculties_count = mysqli_num_rows($faculties_query);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Department Management | David Elementary University</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * { font-family: 'Inter', sans-serif; }
        body { background: #f4f7fc; margin: 0; padding: 0; }
        .uni-header {
            background: linear-gradient(135deg, #0b2b40 0%, #154e6b 100%);
            color: white;
            padding: 1.8rem 0;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        .uni-header h1 { font-weight: 600; margin: 0; font-size: 1.9rem; }
        .uni-header .motto { font-size: 0.9rem; opacity: 0.9; }
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
        .nav-links .nav-link:hover,
        .nav-links .nav-link.active {
            background: #f8f9fa;
            color: #1f6e8c;
            border-bottom-color: #ffb347;
            text-decoration: none;
        }
        .alert-bar {
            padding: 0.85rem 1.2rem;
            border-radius: 12px;
            font-size: 0.9rem;
            font-weight: 500;
            margin-bottom: 1.5rem;
        }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .form-card, .table-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 12px 28px rgba(0,0,0,0.08);
            overflow: hidden;
            height: 100%;
        }
        .card-header {
            background: #fef9ef;
            padding: 1rem 1.5rem;
            font-weight: 600;
            font-size: 1.2rem;
            color: #1f5068;
            border-bottom: 1px solid #e9ecef;
        }
        .form-control, .form-select {
            border-radius: 12px;
            padding: 0.6rem 1rem;
            border: 1px solid #cfdee9;
        }
        .btn-send {
            background: #1f6e8c;
            color: white;
            border: none;
            border-radius: 40px;
            padding: 8px 28px;
            font-weight: 500;
        }
        .btn-send:hover { background: #0e4e66; }
        .btn-reset {
            background: #e9ecef;
            border: none;
            border-radius: 40px;
            padding: 8px 28px;
            color: #4a6272;
        }
        .table thead th {
            background: #eef3fa;
            color: #1f5068;
            padding: 1rem 0.8rem;
        }
        .btn-action {
            border-radius: 30px;
            padding: 5px 14px;
            font-size: 0.75rem;
            font-weight: 500;
            text-decoration: none;
            display: inline-block;
            cursor: pointer;
            border: none;
        }
        .btn-delete { background: #ffe6e5; color: #c13b2b; }
        .btn-update { background: #e3f0fa; color: #1f6e8c; }
        .count-pill {
            background: #ffb347;
            border-radius: 50px;
            padding: 3px 10px;
            font-size: 0.72rem;
            font-weight: 700;
        }
        footer {
            background: #eef2f5;
            margin-top: 3rem;
            padding: 1.5rem;
            text-align: center;
            font-size: 0.8rem;
            color: #5b7c8e;
        }
        @media (max-width: 768px) {
            .nav-links .nav-link { padding: 0.6rem 1rem; font-size: 0.85rem; }
            .uni-header h1 { font-size: 1.4rem; }
        }
    </style>
</head>
<body>

<div class="container-fluid px-0">
    <div class="uni-header text-center">
        <div class="container">
            <h1>DAVID ELEMENTARY UNIVERSITY</h1>
            <div class="motto">"SUCCESS · INTEGRITY · EXCELLENCE"</div>
        </div>
    </div>

    <div class="nav-links text-center">
        <div class="container">
            <div class="nav justify-content-center">
                <a class="nav-link" href="home.php"><i class="fas fa-user-graduate"></i> Dashboard</a>
                <a class="nav-link" href="student.php"><i class="fas fa-user-graduate"></i> Student</a>
                <a class="nav-link" href="course.php"><i class="fas fa-book-open"></i> Course</a>
                <a class="nav-link" href="faculty.php"><i class="fas fa-chalkboard-user"></i> Faculty</a>
                <a class="nav-link active" href="department.php"><i class="fas fa-building"></i> Department</a>
                <a class="nav-link" href="courseunit.php"><i class="fas fa-layer-group"></i> Courseunit</a>
                <a class="nav-link" href="staff.php"><i class="fas fa-users"></i> Staff</a>
            </div>
        </div>
    </div>

    <div class="container mt-5 mb-4">
        <?php if ($success_msg): ?>
            <div class="alert-bar alert-success"><?php echo $success_msg; ?></div>
        <?php endif; ?>
        <?php if ($error_msg): ?>
            <div class="alert-bar alert-error"><?php echo $error_msg; ?></div>
        <?php endif; ?>

        <div class="row">
            <div class="col-lg-4 col-md-12 mb-4">
                <div class="form-card">
                    <div class="card-header"><i class="fas fa-building"></i> Department Registration</div>
                    <div class="card-body p-4">
                        <form method="post">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Department Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" placeholder="e.g., Computer Science" required>
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Faculty <span class="text-danger">*</span></label>
                                <select name="faculty_id" class="form-select" required>
                                    <option value="">Select Faculty</option>
                                    <?php 
                                    if ($faculties_query && $faculties_count > 0) {
                                        mysqli_data_seek($faculties_query, 0);
                                        while ($faculty = mysqli_fetch_assoc($faculties_query)) {
                                            echo "<option value='{$faculty['faculty_id']}'>{$faculty['faculty_name']}</option>";
                                        }
                                    } else {
                                        echo "<option value=''>No faculties available. Please add a faculty first.</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="d-flex gap-3">
                                <button type="submit" name="send" class="btn-send">Register Department</button>
                                <button type="reset" class="btn-reset">Clear</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-8 col-md-12">
                <div class="table-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-table-list me-2"></i> Registered Departments</span>
                        <span class="count-pill"><?php echo $dept_count; ?> Total</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Department Name</th>
                                    <th>Faculty</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                if ($dept_query && $dept_count > 0) {
                                    while ($row = mysqli_fetch_assoc($dept_query)) {
                                ?>
                                <tr>
                                    <td><?php echo $row['id']; ?></td>
                                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['faculty_name'] ?? 'Not Assigned'); ?></td>
                                    <td>
                                        <button onclick="openUpdateModal(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars(addslashes($row['name'])); ?>', <?php echo $row['faculty_id'] ?? 0; ?>)" class="btn-action btn-update">
                                            <i class="fas fa-edit me-1"></i> Update
                                        </button>
                                        <a href="?delete_id=<?php echo $row['id']; ?>" class="btn-action btn-delete" onclick="return confirm('Delete this department?')">
                                            <i class="fas fa-trash-alt me-1"></i> Delete
                                        </a>
                                    </td>
                                </tr>
                                <?php 
                                    }
                                } else {
                                    echo '<tr><td colspan="4" class="text-center py-4 text-muted">No departments registered yet.</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <i class="fas fa-graduation-cap me-1"></i> David Elementary University — Department Management System
        <br><small>© <?php echo date("Y"); ?></small>
    </footer>
</div>

<!-- Update Modal -->
<div class="modal fade" id="updateModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Department</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="update_id" id="update_id">
                    <div class="mb-3">
                        <label class="form-label">Department Name</label>
                        <input type="text" name="name" id="update_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Faculty</label>
                        <select name="faculty_id" id="update_faculty_id" class="form-select" required>
                            <option value="">Select Faculty</option>
                            <?php 
                            if ($faculties_query && $faculties_count > 0) {
                                mysqli_data_seek($faculties_query, 0);
                                while ($faculty = mysqli_fetch_assoc($faculties_query)) {
                                    echo "<option value='{$faculty['faculty_id']}'>{$faculty['faculty_name']}</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="update_department" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
function openUpdateModal(id, name, facultyId) {
    document.getElementById('update_id').value = id;
    document.getElementById('update_name').value = name;
    document.getElementById('update_faculty_id').value = facultyId;
    new bootstrap.Modal(document.getElementById('updateModal')).show();
}
</script>
</body>
</html>