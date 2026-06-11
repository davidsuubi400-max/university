<?php
// ─────────────────────────────────────────────
//  DATABASE CONNECTION  (edit these 4 values)
// ─────────────────────────────────────────────
$host   = "localhost";
$user   = "root";
$pass   = "";          // your MySQL password
$dbname = "university"; // your database name

$connect = mysqli_connect($host, $user, $pass, $dbname);

if (!$connect) {
    die("<div style='font-family:sans-serif;padding:2rem;color:red;'>
        <h3>❌ Database Connection Failed</h3>
        <p>" . mysqli_connect_error() . "</p>
        <p>Check your host, username, password, and database name at the top of this file.</p>
    </div>");
}

// ─────────────────────────────────────────────
//  CREATE STAFF TABLE IF NOT EXISTS
// ─────────────────────────────────────────────
$create_table = "
CREATE TABLE IF NOT EXISTS staff (
    id INT AUTO_INCREMENT PRIMARY KEY,
    staffno VARCHAR(50) NOT NULL UNIQUE,
    firstname VARCHAR(100) NOT NULL,
    lastname VARCHAR(100) NOT NULL,
    contact VARCHAR(50),
    gender VARCHAR(20),
    designation VARCHAR(100),
    department VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (!mysqli_query($connect, $create_table)) {
    // Table might already exist, continue
}

// ─────────────────────────────────────────────
//  FETCH DEPARTMENTS FOR DROPDOWN
// ─────────────────────────────────────────────
$departments_query = mysqli_query($connect, "SELECT name FROM department ORDER BY name");
$has_departments = ($departments_query && mysqli_num_rows($departments_query) > 0);

// ─────────────────────────────────────────────
//  HANDLE: INSERT NEW STAFF
// ─────────────────────────────────────────────
$success_msg = "";
$error_msg   = "";

if (isset($_POST['send'])) {
    $staffno     = trim($_POST['staffno']);
    $firstname   = trim($_POST['firstname']);
    $lastname    = trim($_POST['lastname']);
    $contact     = trim($_POST['contact']);
    $gender      = trim($_POST['gender']);
    $designation = trim($_POST['designation']);
    $department  = trim($_POST['department']);

    // Validate required fields
    if (empty($staffno) || empty($firstname) || empty($lastname) || empty($department)) {
        $error_msg = "Please fill in all required fields.";
    } else {
        // Check if staff number already exists
        $check_stmt = mysqli_prepare($connect, "SELECT id FROM staff WHERE staffno = ?");
        if ($check_stmt) {
            mysqli_stmt_bind_param($check_stmt, "s", $staffno);
            mysqli_stmt_execute($check_stmt);
            mysqli_stmt_store_result($check_stmt);
            
            if (mysqli_stmt_num_rows($check_stmt) > 0) {
                $error_msg = "Staff number already exists. Please use a unique staff number.";
            } else {
                // Insert new staff
                $stmt = mysqli_prepare($connect,
                    "INSERT INTO staff (staffno, firstname, lastname, contact, gender, designation, department)
                     VALUES (?, ?, ?, ?, ?, ?, ?)"
                );

                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, "sssssss",
                        $staffno, $firstname, $lastname, $contact, $gender, $designation, $department
                    );

                    if (mysqli_stmt_execute($stmt)) {
                        $success_msg = "Staff member <strong>" . htmlspecialchars($firstname . ' ' . $lastname) . "</strong> registered successfully.";
                    } else {
                        $error_msg = "Execute failed: " . mysqli_stmt_error($stmt);
                    }
                    mysqli_stmt_close($stmt);
                } else {
                    $error_msg = "Query prepare failed: " . mysqli_error($connect);
                }
            }
            mysqli_stmt_close($check_stmt);
        } else {
            $error_msg = "Database error. Please ensure the staff table exists.";
        }
    }
}

// ─────────────────────────────────────────────
//  HANDLE: DELETE STAFF
// ─────────────────────────────────────────────
if (isset($_GET['delete_id'])) {
    $del_id = (int) $_GET['delete_id'];
    $stmt   = mysqli_prepare($connect, "DELETE FROM staff WHERE id = ?");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $del_id);
        if (mysqli_stmt_execute($stmt)) {
            $success_msg = "Staff member deleted successfully.";
        } else {
            $error_msg = "Delete failed: " . mysqli_stmt_error($stmt);
        }
        mysqli_stmt_close($stmt);
    } else {
        $error_msg = "Delete prepare failed: " . mysqli_error($connect);
    }
}

// ─────────────────────────────────────────────
//  HANDLE: UPDATE STAFF
// ─────────────────────────────────────────────
if (isset($_POST['update_staff'])) {
    $update_id   = intval($_POST['update_id']);
    $staffno     = trim($_POST['staffno']);
    $firstname   = trim($_POST['firstname']);
    $lastname    = trim($_POST['lastname']);
    $contact     = trim($_POST['contact']);
    $gender      = trim($_POST['gender']);
    $designation = trim($_POST['designation']);
    $department  = trim($_POST['department']);
    
    if (empty($staffno) || empty($firstname) || empty($lastname) || empty($department)) {
        $error_msg = "Please fill in all required fields.";
    } else {
        // Check if staff number exists for other records
        $check_stmt = mysqli_prepare($connect, "SELECT id FROM staff WHERE staffno = ? AND id != ?");
        if ($check_stmt) {
            mysqli_stmt_bind_param($check_stmt, "si", $staffno, $update_id);
            mysqli_stmt_execute($check_stmt);
            mysqli_stmt_store_result($check_stmt);
            
            if (mysqli_stmt_num_rows($check_stmt) > 0) {
                $error_msg = "Staff number already exists. Please use a unique staff number.";
            } else {
                $stmt = mysqli_prepare($connect,
                    "UPDATE staff SET staffno = ?, firstname = ?, lastname = ?, contact = ?, gender = ?, designation = ?, department = ? 
                     WHERE id = ?"
                );
                
                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, "sssssssi", $staffno, $firstname, $lastname, $contact, $gender, $designation, $department, $update_id);
                    
                    if (mysqli_stmt_execute($stmt)) {
                        $success_msg = "Staff member updated successfully.";
                    } else {
                        $error_msg = "Update failed: " . mysqli_stmt_error($stmt);
                    }
                    mysqli_stmt_close($stmt);
                } else {
                    $error_msg = "Update prepare failed: " . mysqli_error($connect);
                }
            }
            mysqli_stmt_close($check_stmt);
        } else {
            $error_msg = "Database error. Please ensure the staff table exists.";
        }
    }
}

// ─────────────────────────────────────────────
//  FETCH ALL STAFF for the table
// ─────────────────────────────────────────────
$staff_result = mysqli_query($connect, "SELECT * FROM staff ORDER BY id DESC");
$staff_count  = $staff_result ? mysqli_num_rows($staff_result) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Management | David Elementary University</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --navy:    #0b2032;
            --navy2:   #163248;
            --gold:    #e8a020;
            --gold-lt: #ffefc8;
            --teal:    #1a7f8e;
            --teal-lt: #e0f4f6;
            --red:     #c0392b;
            --red-lt:  #fdecea;
            --green:   #1a7a45;
            --green-lt:#e6f7ee;
            --gray-bg: #f2f4f8;
            --border:  #dde3ea;
            --text:    #1c2b38;
            --muted:   #6b7f8e;
            --white:   #ffffff;
            --radius:  14px;
            --shadow:  0 4px 24px rgba(11,32,50,.10);
        }
        *, *::before, *::after { box-sizing: border-box; }
        body {
            font-family: 'Sora', sans-serif;
            background: var(--gray-bg);
            color: var(--text);
            margin: 0;
        }

        .site-header {
            background: linear-gradient(135deg, var(--navy) 0%, var(--navy2) 100%);
            color: #fff;
            padding: 1.4rem 0;
            position: relative;
            overflow: hidden;
        }
        .site-header::after {
            content: "";
            position: absolute;
            bottom: -1px; left: 0; right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--gold), var(--teal), var(--gold));
        }
        .site-header h1 {
            font-size: 1.65rem;
            font-weight: 700;
            letter-spacing: .5px;
            margin: 0;
        }
        .site-header .motto {
            font-size: .78rem;
            opacity: .75;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin: 4px 0 0;
        }

        .site-nav {
            background: var(--white);
            border-bottom: 1px solid var(--border);
            box-shadow: 0 2px 8px rgba(0,0,0,.05);
        }
        .site-nav .nav-link {
            color: var(--text);
            font-weight: 500;
            font-size: .85rem;
            padding: .85rem 1.3rem;
            border-bottom: 3px solid transparent;
            transition: all .2s;
        }
        .site-nav .nav-link:hover,
        .site-nav .nav-link.active {
            color: var(--teal);
            border-bottom-color: var(--gold);
            background: #f8fbfc;
        }
        .site-nav .nav-link i { margin-right: 5px; opacity: .7; }

        .alert-bar {
            padding: .85rem 1.2rem;
            border-radius: var(--radius);
            font-size: .9rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: .7rem;
            margin-bottom: 1.2rem;
        }
        .alert-success { background: var(--green-lt); color: var(--green); border: 1px solid #a8dfc0; }
        .alert-error   { background: var(--red-lt);   color: var(--red);   border: 1px solid #f5c6c2; }

        .card-panel {
            background: var(--white);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            overflow: hidden;
        }
        .card-panel .panel-head {
            background: linear-gradient(90deg, #f7f9fc, #eef2f8);
            border-bottom: 1px solid var(--border);
            padding: 1rem 1.4rem;
            font-weight: 600;
            font-size: 1rem;
            color: var(--navy);
            display: flex;
            align-items: center;
            gap: .5rem;
        }
        .card-panel .panel-head i { color: var(--gold); }

        .panel-body { padding: 1.4rem; }
        .form-label {
            font-size: .8rem;
            font-weight: 600;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: .5px;
            margin-bottom: .3rem;
        }
        .form-control, select.form-control {
            border: 1.5px solid var(--border);
            border-radius: 10px;
            padding: .55rem .9rem;
            font-family: 'Sora', sans-serif;
            font-size: .88rem;
            color: var(--text);
            transition: border-color .2s, box-shadow .2s;
            background: #fafbfc;
            width: 100%;
        }
        .form-control:focus, select.form-control:focus {
            border-color: var(--teal);
            box-shadow: 0 0 0 3px rgba(26,127,142,.14);
            outline: none;
            background: var(--white);
        }

        .btn-register {
            background: var(--navy);
            color: #fff;
            border: none;
            border-radius: 50px;
            padding: .6rem 1.8rem;
            font-family: 'Sora', sans-serif;
            font-weight: 600;
            font-size: .88rem;
            cursor: pointer;
            transition: background .2s, transform .15s, box-shadow .2s;
        }
        .btn-register:hover {
            background: var(--teal);
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(26,127,142,.25);
        }
        .btn-clear {
            background: var(--gray-bg);
            color: var(--muted);
            border: 1.5px solid var(--border);
            border-radius: 50px;
            padding: .6rem 1.4rem;
            font-family: 'Sora', sans-serif;
            font-weight: 500;
            font-size: .88rem;
            cursor: pointer;
            transition: background .2s;
        }
        .btn-clear:hover { background: #e2e8ee; }

        .table {
            font-size: .83rem;
            margin: 0;
        }
        .table thead th {
            background: var(--navy);
            color: rgba(255,255,255,.85);
            font-weight: 600;
            font-size: .75rem;
            text-transform: uppercase;
            letter-spacing: .6px;
            padding: .9rem .75rem;
            border: none;
            white-space: nowrap;
        }
        .table tbody td {
            padding: .75rem;
            vertical-align: middle;
            border-bottom: 1px solid #f0f3f6;
            color: var(--text);
        }
        .table tbody tr:hover td { background: #f6fafc; }

        .badge-gender {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 50px;
            font-size: .72rem;
            font-weight: 600;
        }
        .badge-Male { background: #dbeafe; color: #1d4ed8; }
        .badge-Female { background: #fce7f3; color: #9d174d; }
        .badge-Other { background: #f3e8ff; color: #6d28d9; }

        .staff-no {
            font-family: 'DM Mono', monospace;
            font-size: .78rem;
            background: var(--gold-lt);
            color: #7a4f00;
            padding: 3px 8px;
            border-radius: 6px;
        }

        .btn-del {
            background: var(--red-lt);
            color: var(--red);
            border: 1px solid #f5c6c2;
            border-radius: 30px;
            padding: 4px 12px;
            font-size: .73rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: background .15s;
            display: inline-block;
        }
        .btn-del:hover { background: #fbd5d3; color: var(--red); text-decoration: none; }

        .btn-upd {
            background: var(--teal-lt);
            color: var(--teal);
            border: 1px solid #b2e0e6;
            border-radius: 30px;
            padding: 4px 12px;
            font-size: .73rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: background .15s;
            display: inline-block;
        }
        .btn-upd:hover { background: #c5ecf0; color: var(--teal); text-decoration: none; }

        .count-pill {
            background: var(--gold);
            color: var(--navy);
            border-radius: 50px;
            font-size: .72rem;
            font-weight: 700;
            padding: 3px 10px;
            margin-left: .5rem;
        }
        .empty-row td {
            text-align: center;
            padding: 2.5rem;
            color: var(--muted);
            font-size: .9rem;
        }
        .empty-row td i { font-size: 1.8rem; display: block; margin-bottom: .5rem; opacity: .35; }

        .modal-content {
            border-radius: var(--radius);
            border: none;
        }
        .modal-header {
            border-bottom: 1px solid var(--border);
            background: linear-gradient(90deg, #f7f9fc, #eef2f8);
        }

        footer {
            background: var(--navy);
            color: rgba(255,255,255,.5);
            text-align: center;
            padding: 1.2rem;
            font-size: .78rem;
            margin-top: 3rem;
        }
        footer span { color: var(--gold); }

        @media (max-width: 991px) {
            .site-header h1 { font-size: 1.25rem; }
            .panel-body { padding: 1rem; }
        }
    </style>
</head>
<body>

<div class="site-header text-center">
    <div class="container">
        <h1><i class="fas fa-university me-2"></i>DAVID ELEMENTARY UNIVERSITY</h1>
        <p class="motto">Success &bull; Integrity &bull; Excellence</p>
    </div>
</div>

<div class="site-nav">
    <div class="container">
        <nav class="nav justify-content-center flex-wrap">
            <a class="nav-link" href="home.php"><i class="fas fa-house"></i>Dashboard</a>
            <a class="nav-link" href="student.php"><i class="fas fa-user-graduate"></i>Student</a>
            <a class="nav-link" href="course.php"><i class="fas fa-book-open"></i>Course</a>
            <a class="nav-link" href="faculty.php"><i class="fas fa-chalkboard-user"></i>Faculty</a>
            <a class="nav-link" href="department.php"><i class="fas fa-building"></i>Department</a>
            <a class="nav-link" href="courseunit.php"><i class="fas fa-layer-group"></i>Course Unit</a>
            <a class="nav-link active" href="staff.php"><i class="fas fa-users"></i>Staff</a>
        </nav>
    </div>
</div>

<div class="container mt-4 mb-5">
    <?php if ($success_msg): ?>
        <div class="alert-bar alert-success">
            <i class="fas fa-circle-check fa-lg"></i>
            <?php echo $success_msg; ?>
        </div>
    <?php endif; ?>
    <?php if ($error_msg): ?>
        <div class="alert-bar alert-error">
            <i class="fas fa-circle-exclamation fa-lg"></i>
            <?php echo htmlspecialchars($error_msg); ?>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-xl-4 col-lg-5">
            <div class="card-panel">
                <div class="panel-head">
                    <i class="fas fa-user-plus"></i> Register Staff
                </div>
                <div class="panel-body">
                    <form method="POST" action="staff.php" novalidate>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Staff Number <span style="color:red">*</span></label>
                                <input type="text" class="form-control" name="staffno" placeholder="e.g. STF/001" required>
                            </div>

                            <div class="col-6">
                                <label class="form-label">First Name <span style="color:red">*</span></label>
                                <input type="text" class="form-control" name="firstname" placeholder="First name" required>
                            </div>

                            <div class="col-6">
                                <label class="form-label">Last Name <span style="color:red">*</span></label>
                                <input type="text" class="form-control" name="lastname" placeholder="Last name" required>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Contact Number</label>
                                <input type="text" class="form-control" name="contact" placeholder="e.g. +256700123456">
                            </div>

                            <div class="col-6">
                                <label class="form-label">Gender</label>
                                <select class="form-control" name="gender">
                                    <option value="">Select</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>

                            <div class="col-6">
                                <label class="form-label">Designation</label>
                                <input type="text" class="form-control" name="designation" placeholder="e.g. Lecturer">
                            </div>

                            <div class="col-12">
                                <label class="form-label">Department <span style="color:red">*</span></label>
                                <select class="form-control" name="department" required>
                                    <option value="">Select Department</option>
                                    <?php 
                                    if ($has_departments) {
                                        mysqli_data_seek($departments_query, 0);
                                        while ($dept = mysqli_fetch_assoc($departments_query)) {
                                            echo "<option value='" . htmlspecialchars($dept['name']) . "'>" . htmlspecialchars($dept['name']) . "</option>";
                                        }
                                    } else {
                                        echo "<option value=''>No departments available. Please add a department first.</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" name="send" class="btn-register">
                                <i class="fas fa-save me-1"></i> Register
                            </button>
                            <button type="reset" class="btn-clear">
                                <i class="fas fa-rotate-left me-1"></i> Clear
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-xl-8 col-lg-7">
            <div class="card-panel">
                <div class="panel-head">
                    <i class="fas fa-table-list"></i>
                    Registered Staff
                    <span class="count-pill"><?php echo $staff_count; ?></span>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Staff No</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Contact</th>
                                <th>Gender</th>
                                <th>Designation</th>
                                <th>Department</th>
                                <th colspan="2" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if ($staff_count > 0):
                            $i = 1;
                            while ($row = mysqli_fetch_assoc($staff_result)): ?>
                            <tr>
                                <td class="text-muted"><?php echo $i++; ?></td>
                                <td><span class="staff-no"><?php echo htmlspecialchars($row['staffno']); ?></span></td>
                                <td><?php echo htmlspecialchars($row['firstname']); ?></td>
                                <td><?php echo htmlspecialchars($row['lastname']); ?></td>
                                <td><?php echo htmlspecialchars($row['contact']); ?></td>
                                <td>
                                    <?php
                                        $g = $row['gender'];
                                        echo "<span class='badge-gender badge-{$g}'>{$g}</span>";
                                    ?>
                                </td>
                                <td><?php echo htmlspecialchars($row['designation']); ?></td>
                                <td><?php echo htmlspecialchars($row['department']); ?></td>
                                <td>
                                    <button onclick="openUpdateModal(
                                        <?php echo $row['id']; ?>,
                                        '<?php echo htmlspecialchars(addslashes($row['staffno'])); ?>',
                                        '<?php echo htmlspecialchars(addslashes($row['firstname'])); ?>',
                                        '<?php echo htmlspecialchars(addslashes($row['lastname'])); ?>',
                                        '<?php echo htmlspecialchars(addslashes($row['contact'])); ?>',
                                        '<?php echo htmlspecialchars(addslashes($row['gender'])); ?>',
                                        '<?php echo htmlspecialchars(addslashes($row['designation'])); ?>',
                                        '<?php echo htmlspecialchars(addslashes($row['department'])); ?>'
                                    )" class="btn-upd">
                                        <i class="fas fa-pen me-1"></i>Edit
                                    </button>
                                </td>
                                <td>
                                    <a href="staff.php?delete_id=<?php echo (int)$row['id']; ?>"
                                       class="btn-del"
                                       onclick="return confirm('Delete <?php echo addslashes($row['firstname'] . ' ' . $row['lastname']); ?>?')">
                                       <i class="fas fa-trash me-1"></i>Delete
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr class="empty-row">
                                <td colspan="10">
                                    <i class="fas fa-users-slash"></i>
                                    No staff registered yet. Use the form to add a member.
                                </td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Update Staff Modal -->
<div class="modal fade" id="updateModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-edit text-primary me-2"></i>Update Staff Member</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="staff.php">
                <div class="modal-body">
                    <input type="hidden" name="update_id" id="update_id">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Staff Number</label>
                            <input type="text" name="staffno" id="update_staffno" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">First Name</label>
                            <input type="text" name="firstname" id="update_firstname" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Last Name</label>
                            <input type="text" name="lastname" id="update_lastname" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Contact</label>
                            <input type="text" name="contact" id="update_contact" class="form-control">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Gender</label>
                            <select name="gender" id="update_gender" class="form-control">
                                <option value="">Select</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Designation</label>
                            <input type="text" name="designation" id="update_designation" class="form-control">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Department</label>
                        <select name="department" id="update_department" class="form-control" required>
                            <option value="">Select Department</option>
                            <?php 
                            if ($has_departments) {
                                mysqli_data_seek($departments_query, 0);
                                while ($dept = mysqli_fetch_assoc($departments_query)) {
                                    echo "<option value='" . htmlspecialchars($dept['name']) . "'>" . htmlspecialchars($dept['name']) . "</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="update_staff" class="btn btn-primary">Update Staff</button>
                </div>
            </form>
        </div>
    </div>
</div>

<footer>
    <i class="fas fa-graduation-cap me-1"></i>
    <span>David Elementary University</span> &mdash; Staff Management System
    <br><small>© <?php echo date("Y"); ?> All rights reserved</small>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<script>
function openUpdateModal(id, staffno, firstname, lastname, contact, gender, designation, department) {
    document.getElementById('update_id').value = id;
    document.getElementById('update_staffno').value = staffno;
    document.getElementById('update_firstname').value = firstname;
    document.getElementById('update_lastname').value = lastname;
    document.getElementById('update_contact').value = contact;
    document.getElementById('update_gender').value = gender;
    document.getElementById('update_designation').value = designation;
    document.getElementById('update_department').value = department;
    
    new bootstrap.Modal(document.getElementById('updateModal')).show();
}
</script>

</body>
</html>