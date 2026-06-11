<?php
include 'config.php'; // Database connection

// Handle form submission
if (isset($_POST['submit'])) {
    $id = intval($_POST['id']);
    $staffno = trim($_POST['staffno']);
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $contact = trim($_POST['contact']);
    $gender = trim($_POST['gender']);
    $designation = trim($_POST['designation']);
    $department = trim($_POST['department']);

    if (!empty($staffno) && !empty($firstname) && !empty($lastname) && !empty($contact) && !empty($gender) && !empty($designation) && !empty($department)) {
        $stmt = $connect->prepare("UPDATE staff SET staffno = ?, firstname = ?, lastname = ?, contact = ?, gender = ?, designation = ?, department = ? WHERE id = ?");
        $stmt->bind_param("sssssssi", $staffno, $firstname, $lastname, $contact, $gender, $designation, $department, $id);
        if ($stmt->execute()) {
            header("Location: view_staff.php?update=success");
            exit;
        } else {
            $error = "Update failed: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $error = "All fields are required.";
    }
}

// Fetch staff data if an ID is provided
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $connect->prepare("SELECT * FROM staff WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $staffno = htmlspecialchars($row['staffno']);
        $firstname = htmlspecialchars($row['firstname']);
        $lastname = htmlspecialchars($row['lastname']);
        $contact = htmlspecialchars($row['contact']);
        $gender = htmlspecialchars($row['gender']);
        $designation = htmlspecialchars($row['designation']);
        $department = htmlspecialchars($row['department']);
    } else {
        header("Location: view_staff.php");
        exit;
    }
    $stmt->close();
} else {
    header("Location: view_staff.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Staff | David Elementary University</title>
    <!-- Bootstrap 4 CSS (local path preserved) -->
    <link rel="stylesheet" href="bootstrap/dist/css/bootstrap.min.css">
    <!-- Google Fonts -->
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
        /* card styling */
        .form-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 12px 28px rgba(0,0,0,0.08);
            border: none;
            overflow: hidden;
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
        .btn-update {
            background: #1f6e8c;
            border: none;
            border-radius: 40px;
            padding: 8px 28px;
            font-weight: 500;
            transition: all 0.2s;
        }
        .btn-update:hover {
            background: #0e4e66;
            transform: translateY(-1px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.1);
        }
        .btn-back {
            background: #e9ecef;
            border: none;
            border-radius: 40px;
            padding: 8px 28px;
            color: #4a6272;
            font-weight: 500;
            transition: all 0.2s;
        }
        .btn-back:hover {
            background: #dee2e6;
            text-decoration: none;
            color: #2c3e4e;
        }
        .alert-custom {
            border-radius: 12px;
            margin-bottom: 1.5rem;
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
        }
    </style>
</head>
<body>

<div class="container-fluid px-0">
    <!-- HEADER -->
    <div class="uni-header text-center">
        <div class="container">
            <h1>DAVID ELEMENTARY UNIVERSITY</h1>
            <div class="motto">"SUCCESS · INTEGRITY · EXCELLENCE"</div>
        </div>
    </div>

    <!-- NAVIGATION -->
    <div class="nav-links text-center">
        <div class="container">
            <div class="nav justify-content-center">
                <a class="nav-link" href="student.php"><i class="fas fa-user-graduate"></i> Student</a>
                <a class="nav-link" href="course.php"><i class="fas fa-book-open"></i> Course</a>
                <a class="nav-link" href="faculty.php"><i class="fas fa-chalkboard-user"></i> Faculty</a>
                <a class="nav-link" href="department.php"><i class="fas fa-building"></i> Department</a>
                <a class="nav-link" href="courseunit.php"><i class="fas fa-layer-group"></i> Courseunit</a>
                <a class="nav-link" href="staff.php"><i class="fas fa-users"></i> Staff</a>
            </div>
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <div class="container mt-5 mb-4">
        <div class="row justify-content-center">
            <div class="col-lg-7 col-md-9">
                <div class="form-card">
                    <div class="card-header">
                        <i class="fas fa-edit"></i> Update Staff Information
                    </div>
                    <div class="card-body p-4">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger alert-custom" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i> <?php echo $error; ?>
                            </div>
                        <?php endif; ?>

                        <form method="post" action="">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="staffno" class="form-label fw-semibold">Staff Number</label>
                                    <input type="text" class="form-control" id="staffno" name="staffno" 
                                           value="<?php echo $staffno; ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="firstname" class="form-label fw-semibold">First Name</label>
                                    <input type="text" class="form-control" id="firstname" name="firstname" 
                                           value="<?php echo $firstname; ?>" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="lastname" class="form-label fw-semibold">Last Name</label>
                                    <input type="text" class="form-control" id="lastname" name="lastname" 
                                           value="<?php echo $lastname; ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="contact" class="form-label fw-semibold">Contact Number</label>
                                    <input type="text" class="form-control" id="contact" name="contact" 
                                           value="<?php echo $contact; ?>" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="gender" class="form-label fw-semibold">Gender</label>
                                    <select class="form-control" id="gender" name="gender" required>
                                        <option value="">Select Gender</option>
                                        <option value="Male" <?php echo ($gender == 'Male') ? 'selected' : ''; ?>>Male</option>
                                        <option value="Female" <?php echo ($gender == 'Female') ? 'selected' : ''; ?>>Female</option>
                                        <option value="Other" <?php echo ($gender == 'Other') ? 'selected' : ''; ?>>Other</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="designation" class="form-label fw-semibold">Designation</label>
                                    <input type="text" class="form-control" id="designation" name="designation" 
                                           value="<?php echo $designation; ?>" required>
                                </div>
                            </div>
                            <div class="form-group mb-4">
                                <label for="department" class="form-label fw-semibold">Department</label>
                                <input type="text" class="form-control" id="department" name="department" 
                                       value="<?php echo $department; ?>" required>
                            </div>
                            <input type="hidden" name="id" value="<?php echo $id; ?>">
                            <div class="d-flex gap-3 justify-content-start mt-2">
                                <button type="submit" name="submit" class="btn btn-update text-white">
                                    <i class="fas fa-save me-1"></i> Update Staff
                                </button>
                                <a href="view_staff.php" class="btn btn-back">
                                    <i class="fas fa-arrow-left me-1"></i> Back to List
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FOOTER -->
    <footer>
        <div class="container">
            <i class="fas fa-graduation-cap me-1"></i> David Elementary University — Empowering future leaders
            <br><small>© <?php echo date("Y"); ?> | Staff Management System</small>
        </div>
    </footer>
</div>

<!-- Bootstrap JS (optional) -->
<script src="bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>n