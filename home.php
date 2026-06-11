<?php
include 'config.php';

// Fetch counts for each module
$student_count = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) as total FROM student"))['total'];
$course_count = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) as total FROM course"))['total'];
$faculty_count = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) as total FROM faculty"))['total'];
$department_count = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) as total FROM department"))['total'];
$courseunit_count = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) as total FROM courseunity"))['total'];
$staff_count = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) as total FROM staff"))['total'];

// Recent students
$recent_students = mysqli_query($connect, "SELECT firstname, lastname, regno FROM student ORDER BY id DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Dashboard | David Elementary University</title>
    <link rel="stylesheet" href="bootstrap/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
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
        .uni-header h1 { font-weight: 600; letter-spacing: -0.3px; margin: 0; font-size: 1.9rem; }
        .uni-header .motto { margin: 0; font-size: 0.9rem; opacity: 0.9; }

        .nav-bar {
            background: #ffffff;
            padding: 0.5rem 1rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            border-bottom: 1px solid #e9ecef;
        }
        .navbar-toggler { border: none; padding: 0.25rem 0.5rem; }
        .navbar-toggler:focus { outline: none; box-shadow: 0 0 0 2px rgba(31,110,140,0.2); }
        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba(44,62,78,0.8)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }
        .navbar-nav .nav-link {
            color: #2c3e4e;
            font-weight: 500;
            padding: 0.5rem 1rem;
            transition: all 0.2s ease;
        }
        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link.active {
            color: #1f6e8c;
            background-color: #f8f9fa;
            border-radius: 8px;
        }

        /* Dropdown styling */
        .dropdown-menu {
            border-radius: 12px;
            border: none;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            margin-top: 10px;
            min-width: 220px;
        }
        .dropdown-item {
            padding: 0.6rem 1.2rem;
            font-weight: 500;
            color: #2c3e4e;
        }
        .dropdown-item i {
            width: 1.8rem;
            color: #ff8c42;
        }
        .dropdown-item:hover {
            background-color: #fef5e9;
            color: #1f6e8c;
        }
        .dropdown-divider { margin: 0.3rem 0; }

        /* Section label inside dropdown */
        .dropdown-header {
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #9baab5;
            padding: 0.5rem 1.2rem 0.2rem;
        }

        /* Main horizontal nav */
        .main-nav { background: #1f6e8c; padding: 0; }
        .main-nav .navbar-nav .nav-link {
            color: rgba(255,255,255,0.85);
            font-weight: 500;
            font-size: 0.92rem;
            padding: 0.75rem 1.1rem;
            border-radius: 0;
            transition: background 0.2s, color 0.2s;
        }
        .main-nav .navbar-nav .nav-link:hover,
        .main-nav .navbar-nav .nav-link.active {
            background: rgba(255,255,255,0.15);
            color: #ffffff;
            border-radius: 0;
        }
        .main-nav .navbar-nav .nav-link i { margin-right: 6px; color: #ff8c42; }
        .main-nav .navbar-toggler { border-color: rgba(255,255,255,0.4); }
        .main-nav .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba(255,255,255,0.8)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        /* Dashboard cards */
        .stat-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 12px 28px rgba(0,0,0,0.08);
            border: none;
            transition: transform 0.2s, box-shadow 0.2s;
            overflow: hidden;
        }
        .stat-card:hover { transform: translateY(-5px); box-shadow: 0 20px 35px rgba(0,0,0,0.12); }
        .stat-card .card-body { padding: 1.5rem; }
        .stat-icon { font-size: 2.5rem; color: #ff8c42; margin-bottom: 0.5rem; }
        .stat-number { font-size: 2.2rem; font-weight: 700; color: #1f5068; margin-bottom: 0; }
        .stat-label { font-size: 1rem; font-weight: 500; color: #6c7a89; margin-top: 0.5rem; }
        .btn-stat {
            background: transparent;
            border: 1px solid #e0e7ed;
            border-radius: 40px;
            padding: 5px 18px;
            font-size: 0.8rem;
            color: #1f6e8c;
            transition: all 0.2s;
        }
        .btn-stat:hover { background: #1f6e8c; color: white; border-color: #1f6e8c; }

        .recent-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 12px 28px rgba(0,0,0,0.08);
            border: none;
            overflow: hidden;
        }
        .recent-card .card-header {
            background: #fef9ef;
            border-bottom: none;
            padding: 1rem 1.5rem;
            font-weight: 600;
            font-size: 1.1rem;
            color: #1f5068;
        }
        .list-group-item {
            border-left: none; border-right: none;
            border-color: #eef2f5;
            padding: 0.8rem 1.2rem;
        }
        .list-group-item:first-child { border-top: none; }

        footer {
            background: #eef2f5;
            margin-top: 3rem;
            padding: 1.5rem;
            text-align: center;
            font-size: 0.8rem;
            color: #5b7c8e;
            border-top: 1px solid #dce5ec;
        }
        @media (max-width: 768px) { .uni-header h1 { font-size: 1.4rem; } }
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

    <!-- TOP NAV BAR (with expanded dropdown) -->
    <div class="nav-bar">
        <div class="container">
            <nav class="navbar navbar-expand-lg p-0">
                <button class="navbar-toggler ms-auto" type="button" data-toggle="collapse" data-target="#navbarMenu" aria-controls="navbarMenu" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarMenu">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-bars"></i> Menu
                            </a>
                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">

                                <!-- Main Pages -->
                                <h6 class="dropdown-header">Main</h6>
                                <a class="dropdown-item active" href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>

                                <!-- Academic -->
                                <div class="dropdown-divider"></div>
                                <h6 class="dropdown-header">Academic</h6>
                                <a class="dropdown-item" href="student.php"><i class="fas fa-user-graduate"></i> Student</a>
                                <a class="dropdown-item" href="course.php"><i class="fas fa-book-open"></i> Course</a>
                                <a class="dropdown-item" href="faculty.php"><i class="fas fa-chalkboard-user"></i> Faculty</a>
                                <a class="dropdown-item" href="department.php"><i class="fas fa-building"></i> Department</a>
                                <a class="dropdown-item" href="courseunit.php"><i class="fas fa-layer-group"></i> Course Unit</a>
                                <a class="dropdown-item" href="staff.php"><i class="fas fa-users"></i> Staff</a>

                                <!-- Administration -->
                                <div class="dropdown-divider"></div>
                                <h6 class="dropdown-header">Administration</h6>
                                <a class="dropdown-item" href="enrollment.php"><i class="fas fa-file-signature"></i> Enrollment</a>
                                <a class="dropdown-item" href="attendance.php"><i class="fas fa-calendar-check"></i> Attendance</a>
                                <a class="dropdown-item" href="grades.php"><i class="fas fa-star-half-alt"></i> Grades</a>
                                <a class="dropdown-item" href="timetable.php"><i class="fas fa-calendar-alt"></i> Timetable</a>
                                <a class="dropdown-item" href="exams.php"><i class="fas fa-pen-to-square"></i> Exams</a>
                                <a class="dropdown-item" href="library.php"><i class="fas fa-book-bookmark"></i> Library</a>
                                <a class="dropdown-item" href="finance.php"><i class="fas fa-coins"></i> Finance</a>

                                <!-- Account -->
                                <div class="dropdown-divider"></div>
                                <h6 class="dropdown-header">Account</h6>
                                <a class="dropdown-item" href="settings.php"><i class="fas fa-cog"></i> Settings</a>
                                <a class="dropdown-item" href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a>
                                <a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt" style="color:#e74c3c;"></i> Logout</a>

                            </div>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>
    </div>

    <!-- FULL HORIZONTAL NAV BAR -->
    <div class="main-nav">
        <div class="container">
            <nav class="navbar navbar-expand-lg p-0">
                <button class="navbar-toggler my-1" type="button" data-toggle="collapse" data-target="#mainNavLinks" aria-controls="mainNavLinks" aria-expanded="false" aria-label="Toggle main navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="mainNavLinks">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link active" href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="student.php"><i class="fas fa-user-graduate"></i> Student</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="course.php"><i class="fas fa-book-open"></i> Course</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="faculty.php"><i class="fas fa-chalkboard-user"></i> Faculty</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="department.php"><i class="fas fa-building"></i> Department</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="courseunit.php"><i class="fas fa-layer-group"></i> Course Unit</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="staff.php"><i class="fas fa-users"></i> Staff</a>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>
    </div>

    <!-- DASHBOARD CONTENT -->
    <div class="container mt-5 mb-4">
        <div class="row mb-4">
            <div class="col-12">
                <div class="bg-white p-4 shadow-sm" style="border-radius: 20px;">
                    <h2 class="fw-semibold mb-2" style="color: #1f5068;">Welcome to the Management Dashboard</h2>
                    <p class="text-muted mb-0">Manage all aspects of your institution: students, courses, faculty, departments, course units, and staff.</p>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-5">
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="stat-card h-100">
                    <div class="card-body text-center">
                        <div class="stat-icon"><i class="fas fa-user-graduate"></i></div>
                        <div class="stat-number"><?php echo $student_count; ?></div>
                        <div class="stat-label">Total Students</div>
                        <a href="student.php" class="btn btn-stat mt-3">Manage <i class="fas fa-arrow-right ms-1"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="stat-card h-100">
                    <div class="card-body text-center">
                        <div class="stat-icon"><i class="fas fa-book-open"></i></div>
                        <div class="stat-number"><?php echo $course_count; ?></div>
                        <div class="stat-label">Total Courses</div>
                        <a href="course.php" class="btn btn-stat mt-3">Manage <i class="fas fa-arrow-right ms-1"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="stat-card h-100">
                    <div class="card-body text-center">
                        <div class="stat-icon"><i class="fas fa-chalkboard-user"></i></div>
                        <div class="stat-number"><?php echo $faculty_count; ?></div>
                        <div class="stat-label">Total Faculties</div>
                        <a href="faculty.php" class="btn btn-stat mt-3">Manage <i class="fas fa-arrow-right ms-1"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="stat-card h-100">
                    <div class="card-body text-center">
                        <div class="stat-icon"><i class="fas fa-building"></i></div>
                        <div class="stat-number"><?php echo $department_count; ?></div>
                        <div class="stat-label">Total Departments</div>
                        <a href="department.php" class="btn btn-stat mt-3">Manage <i class="fas fa-arrow-right ms-1"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="stat-card h-100">
                    <div class="card-body text-center">
                        <div class="stat-icon"><i class="fas fa-layer-group"></i></div>
                        <div class="stat-number"><?php echo $courseunit_count; ?></div>
                        <div class="stat-label">Total Course Units</div>
                        <a href="courseunit.php" class="btn btn-stat mt-3">Manage <i class="fas fa-arrow-right ms-1"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="stat-card h-100">
                    <div class="card-body text-center">
                        <div class="stat-icon"><i class="fas fa-users"></i></div>
                        <div class="stat-number"><?php echo $staff_count; ?></div>
                        <div class="stat-label">Total Staff</div>
                        <a href="staff.php" class="btn btn-stat mt-3">Manage <i class="fas fa-arrow-right ms-1"></i></a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity Row -->
        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="recent-card h-100">
                    <div class="card-header">
                        <i class="fas fa-history me-2"></i> Recent Student Registrations
                    </div>
                    <div class="list-group list-group-flush">
                        <?php if (mysqli_num_rows($recent_students) > 0): ?>
                            <?php while ($student = mysqli_fetch_assoc($recent_students)): ?>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><strong><?php echo htmlspecialchars($student['firstname'] . ' ' . $student['lastname']); ?></strong> (<?php echo htmlspecialchars($student['regno']); ?>)</span>
                                    <i class="fas fa-user-check text-success"></i>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="list-group-item text-muted text-center">No recent registrations.</div>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer bg-white border-0 pb-3">
                        <a href="student.php" class="btn btn-sm btn-outline-primary rounded-pill">View All Students</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-4">
                <div class="recent-card h-100">
                    <div class="card-header">
                        <i class="fas fa-chart-line me-2"></i> System Overview
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Students</span><span><?php echo $student_count; ?></span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-info" style="width: <?php echo min(100, $student_count / 5); ?>%"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Courses</span><span><?php echo $course_count; ?></span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-success" style="width: <?php echo min(100, $course_count / 5); ?>%"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Staff</span><span><?php echo $staff_count; ?></span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-warning" style="width: <?php echo min(100, $staff_count / 5); ?>%"></div>
                            </div>
                        </div>
                        <hr>
                        <div class="mt-3">
                            <p class="text-muted small mb-0"><i class="fas fa-database me-1"></i> Database last updated: <?php echo date('Y-m-d H:i:s'); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FOOTER -->
    <footer>
        <div class="container">
            <i class="fas fa-graduation-cap me-1"></i> David Elementary University — Empowering future leaders
            <br><small>© <?php echo date("Y"); ?> | Management Dashboard</small>
        </div>
    </footer>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="bootstrap/dist/js/bootstrap.min.js"></script>
</body>
</html>