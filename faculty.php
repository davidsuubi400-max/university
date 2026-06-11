<?php
// ─────────────────────────────────────────────
//  DATABASE CONNECTION  (edit these 4 values)
// ─────────────────────────────────────────────
$host   = "localhost";
$user   = "root";
$pass   = "";           // your MySQL password
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
//  CREATE TABLES IF THEY DON'T EXIST
// ─────────────────────────────────────────────
$create_faculty_table = "
CREATE TABLE IF NOT EXISTS faculty (
    faculty_id INT AUTO_INCREMENT PRIMARY KEY,
    faculty_name VARCHAR(255) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (!mysqli_query($connect, $create_faculty_table)) {
    die("Error creating faculty table: " . mysqli_error($connect));
}

$create_department_table = "
CREATE TABLE IF NOT EXISTS department (
    dept_id INT AUTO_INCREMENT PRIMARY KEY,
    dept_name VARCHAR(255) NOT NULL,
    faculty_id INT,
    FOREIGN KEY (faculty_id) REFERENCES faculty(faculty_id) ON DELETE RESTRICT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (!mysqli_query($connect, $create_department_table)) {
    die("Error creating department table: " . mysqli_error($connect));
}

// ─────────────────────────────────────────────
//  HANDLE: INSERT NEW FACULTY
// ─────────────────────────────────────────────
$success_msg = "";
$error_msg   = "";
$faculty_name = "";

if (isset($_POST['send'])) {
    $faculty_name = trim($_POST['name']);

    if (empty($faculty_name)) {
        $error_msg = "Faculty name cannot be empty.";
    } else {
        $stmt = mysqli_prepare($connect,
            "INSERT INTO faculty (faculty_name) VALUES (?)"
        );

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $faculty_name);

            if (mysqli_stmt_execute($stmt)) {
                $success_msg = "Faculty <strong>" . htmlspecialchars($faculty_name) . "</strong> registered successfully.";
                $faculty_name = ""; // clear field after success
            } else {
                $error_msg = "Execute failed: " . mysqli_stmt_error($stmt);
            }
            mysqli_stmt_close($stmt);
        } else {
            $error_msg = "Prepare failed: " . mysqli_error($connect);
        }
    }
}

// ─────────────────────────────────────────────
//  HANDLE: DELETE FACULTY
// ─────────────────────────────────────────────
if (isset($_GET['delete_id'])) {
    $del_id = (int) $_GET['delete_id'];

    // Check if any departments are linked — cannot delete a parent with children
    $check = mysqli_prepare($connect, "SELECT COUNT(*) FROM department WHERE faculty_id = ?");
    if ($check) {
        mysqli_stmt_bind_param($check, "i", $del_id);
        mysqli_stmt_execute($check);
        mysqli_stmt_bind_result($check, $linked_count);
        mysqli_stmt_fetch($check);
        mysqli_stmt_close($check);
    } else {
        $linked_count = 0;
    }

    if ($linked_count > 0) {
        $error_msg = "Cannot delete: this faculty has <strong>{$linked_count}</strong> department(s) linked to it. Remove those departments first.";
    } else {
        $stmt = mysqli_prepare($connect, "DELETE FROM faculty WHERE faculty_id = ?");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $del_id);
            if (mysqli_stmt_execute($stmt)) {
                $success_msg = "Faculty deleted successfully.";
            } else {
                $error_msg = "Delete failed: " . mysqli_stmt_error($stmt);
            }
            mysqli_stmt_close($stmt);
        } else {
            $error_msg = "Delete prepare failed: " . mysqli_error($connect);
        }
    }
}

// ─────────────────────────────────────────────
//  HANDLE: UPDATE FACULTY
// ─────────────────────────────────────────────
if (isset($_POST['update_faculty'])) {
    $update_id = (int) $_POST['update_id'];
    $faculty_name = trim($_POST['faculty_name']);
    
    if (empty($faculty_name)) {
        $error_msg = "Faculty name cannot be empty.";
    } else {
        // Check if faculty exists
        $check = mysqli_prepare($connect, "SELECT faculty_id FROM faculty WHERE faculty_id = ?");
        if ($check) {
            mysqli_stmt_bind_param($check, "i", $update_id);
            mysqli_stmt_execute($check);
            mysqli_stmt_store_result($check);
            
            if (mysqli_stmt_num_rows($check) > 0) {
                $stmt = mysqli_prepare($connect, "UPDATE faculty SET faculty_name = ? WHERE faculty_id = ?");
                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, "si", $faculty_name, $update_id);
                    if (mysqli_stmt_execute($stmt)) {
                        $success_msg = "Faculty updated successfully.";
                    } else {
                        $error_msg = "Update failed: " . mysqli_stmt_error($stmt);
                    }
                    mysqli_stmt_close($stmt);
                } else {
                    $error_msg = "Update prepare failed: " . mysqli_error($connect);
                }
            } else {
                $error_msg = "Faculty not found.";
            }
            mysqli_stmt_close($check);
        } else {
            $error_msg = "Check prepare failed: " . mysqli_error($connect);
        }
    }
}

// ─────────────────────────────────────────────
//  FETCH: ALL FACULTIES with department count
// ─────────────────────────────────────────────
$faculty_query = "SELECT f.faculty_id, f.faculty_name, COUNT(d.dept_id) AS dept_count
     FROM faculty f
     LEFT JOIN department d ON f.faculty_id = d.faculty_id
     GROUP BY f.faculty_id
     ORDER BY f.faculty_id DESC";

$faculty_result = mysqli_query($connect, $faculty_query);

// Check if query was successful
if (!$faculty_result) {
    // If query fails, show error and create empty result
    $error_msg = "Database query error: " . mysqli_error($connect);
    $faculty_count = 0;
} else {
    $faculty_count = mysqli_num_rows($faculty_result);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Management | David Elementary University</title>
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
            --amber:   #b45309;
            --amber-lt:#fef3c7;
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

        /* ── HEADER ─────────────────────────── */
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

        /* ── NAV ────────────────────────────── */
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

        /* ── ALERTS ─────────────────────────── */
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

        /* ── CARDS ──────────────────────────── */
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

        /* ── FORM ───────────────────────────── */
        .panel-body { padding: 1.4rem; }
        .form-label {
            font-size: .8rem;
            font-weight: 600;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: .5px;
            margin-bottom: .3rem;
        }
        .form-control {
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
        .form-control:focus {
            border-color: var(--teal);
            box-shadow: 0 0 0 3px rgba(26,127,142,.14);
            outline: none;
            background: var(--white);
        }
        .form-control::placeholder { color: #b0bec7; }

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

        /* ── TABLE ──────────────────────────── */
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
        .table tbody tr:last-child td { border-bottom: none; }

        .faculty-id {
            font-family: 'DM Mono', monospace;
            font-size: .78rem;
            background: var(--gold-lt);
            color: #7a4f00;
            padding: 3px 8px;
            border-radius: 6px;
        }

        .dept-count-badge {
            display: inline-block;
            background: var(--teal-lt);
            color: var(--teal);
            border: 1px solid #b2e0e6;
            padding: 3px 10px;
            border-radius: 50px;
            font-size: .72rem;
            font-weight: 700;
        }
        .dept-count-badge.zero {
            background: var(--gray-bg);
            color: var(--muted);
            border-color: var(--border);
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
            white-space: nowrap;
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
            white-space: nowrap;
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

        /* info tip box */
        .tip-box {
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            color: #0369a1;
            border-radius: 10px;
            padding: .75rem 1rem;
            font-size: .82rem;
            margin-top: 1rem;
            line-height: 1.6;
        }
        .tip-box i { margin-right: 5px; }

        /* Modal styles */
        .modal-content {
            border-radius: var(--radius);
            border: none;
        }
        .modal-header {
            border-bottom: 1px solid var(--border);
            background: linear-gradient(90deg, #f7f9fc, #eef2f8);
        }

        /* ── FOOTER ─────────────────────────── */
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

<!-- ── HEADER ─────────────────────────────── -->
<div class="site-header text-center">
    <div class="container">
        <h1><i class="fas fa-university me-2"></i>DAVID ELEMENTARY UNIVERSITY</h1>
        <p class="motto">Success &bull; Integrity &bull; Excellence</p>
    </div>
</div>

<!-- ── NAVIGATION ────────────────────────── -->
<div class="site-nav">
    <div class="container">
        <nav class="nav justify-content-center flex-wrap">
            <a class="nav-link" href="home.php"><i class="fas fa-house"></i>Dashboard</a>
            <a class="nav-link" href="student.php"><i class="fas fa-user-graduate"></i>Student</a>
            <a class="nav-link" href="course.php"><i class="fas fa-book-open"></i>Course</a>
            <a class="nav-link active" href="faculty.php"><i class="fas fa-chalkboard-user"></i>Faculty</a>
            <a class="nav-link" href="department.php"><i class="fas fa-building"></i>Department</a>
            <a class="nav-link" href="courseunit.php"><i class="fas fa-layer-group"></i>Course Unit</a>
            <a class="nav-link" href="staff.php"><i class="fas fa-users"></i>Staff</a>
        </nav>
    </div>
</div>

<!-- ── MAIN CONTENT ───────────────────────── -->
<div class="container mt-4 mb-5">

    <!-- Alerts -->
    <?php if ($success_msg): ?>
        <div class="alert-bar alert-success">
            <i class="fas fa-circle-check fa-lg"></i>
            <?php echo $success_msg; ?>
        </div>
    <?php endif; ?>
    <?php if ($error_msg): ?>
        <div class="alert-bar alert-error">
            <i class="fas fa-circle-exclamation fa-lg"></i>
            <?php echo $error_msg; ?>
        </div>
    <?php endif; ?>

    <div class="row g-4">

        <!-- ── LEFT: REGISTRATION FORM ──── -->
        <div class="col-xl-4 col-lg-5">
            <div class="card-panel">
                <div class="panel-head">
                    <i class="fas fa-chalkboard-user"></i> Add New Faculty
                </div>
                <div class="panel-body">
                    <form method="POST" action="faculty.php" novalidate>

                        <div class="mb-3">
                            <label class="form-label">Faculty Name <span style="color:red">*</span></label>
                            <input type="text" class="form-control" name="name"
                                   placeholder="e.g. Faculty of Science & Technology"
                                   required
                                   value="<?php echo isset($faculty_name) ? htmlspecialchars($faculty_name) : ''; ?>">
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

                    <div class="tip-box">
                        <i class="fas fa-circle-info"></i>
                        <strong>Start here.</strong> Faculty must be added before departments, and departments before staff or courses.
                    </div>
                </div>
            </div>
        </div>

        <!-- ── RIGHT: FACULTY TABLE ──────── -->
        <div class="col-xl-8 col-lg-7">
            <div class="card-panel">
                <div class="panel-head">
                    <i class="fas fa-table-list"></i>
                    Registered Faculties
                    <span class="count-pill"><?php echo $faculty_count; ?></span>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Faculty ID</th>
                                <th>Faculty Name</th>
                                <th class="text-center">Departments</th>
                                <th colspan="2" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if ($faculty_result && $faculty_count > 0):
                            $i = 1;
                            while ($row = mysqli_fetch_assoc($faculty_result)): ?>
                            <tr>
                                <td class="text-muted"><?php echo $i++; ?></td>
                                <td><span class="faculty-id"><?php echo $row['faculty_id']; ?></span></td>
                                <td><?php echo htmlspecialchars($row['faculty_name']); ?></td>
                                <td class="text-center">
                                    <span class="dept-count-badge <?php echo $row['dept_count'] == 0 ? 'zero' : ''; ?>">
                                        <?php echo $row['dept_count']; ?> dept<?php echo $row['dept_count'] != 1 ? 's' : ''; ?>
                                    </span>
                                </td>
                                <td>
                                    <button onclick="openUpdateModal(<?php echo $row['faculty_id']; ?>, '<?php echo htmlspecialchars(addslashes($row['faculty_name'])); ?>')"
                                       class="btn-upd">
                                       <i class="fas fa-pen me-1"></i>Edit
                                    </button>
                                </td>
                                <td>
                                    <a href="faculty.php?delete_id=<?php echo (int)$row['faculty_id']; ?>"
                                       class="btn-del"
                                       onclick="return confirm('Delete faculty: <?php echo addslashes($row['faculty_name']); ?>?\n\nThis will fail if departments are still linked.')">
                                       <i class="fas fa-trash me-1"></i>Delete
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr class="empty-row">
                                <td colspan="6">
                                    <i class="fas fa-chalkboard"></i>
                                    No faculties registered yet. Use the form to add the first one.
                                </td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div><!-- /row -->
</div><!-- /container -->

<!-- Update Faculty Modal -->
<div class="modal fade" id="updateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-edit text-primary me-2"></i>Update Faculty</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="faculty.php" onsubmit="return confirm('Save changes?')">
                <div class="modal-body">
                    <input type="hidden" name="update_id" id="update_id">
                    <div class="mb-3">
                        <label class="form-label">Faculty Name <span style="color:red">*</span></label>
                        <input type="text" name="faculty_name" id="update_faculty_name" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="update_faculty" class="btn btn-primary">Update Faculty</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ── FOOTER ─────────────────────────────── -->
<footer>
    <i class="fas fa-graduation-cap me-1"></i>
    <span>David Elementary University</span> &mdash; Faculty Management System
    <br><small>© <?php echo date("Y"); ?> All rights reserved</small>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Update modal handlers
    function openUpdateModal(id, facultyName) {
        document.getElementById('update_id').value = id;
        document.getElementById('update_faculty_name').value = facultyName;
        
        const updateModal = new bootstrap.Modal(document.getElementById('updateModal'));
        updateModal.show();
    }
</script>

</body>
</html>