<?php
include 'config.php';

// Fetch counts for each module
$student_count = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) as total FROM student"))['total'];
$course_count = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) as total FROM course"))['total'];
$faculty_count = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) as total FROM faculty"))['total'];
$department_count = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) as total FROM department"))['total'];
$courseunit_count = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) as total FROM courseunity"))['total'];
$staff_count = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) as total FROM staff"))['total'];

// Additional counts for new metrics
$enrollment_count = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) as total FROM enrollment"))['total'] ?? 0;
$attendance_count = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) as total FROM attendance"))['total'] ?? 0;
$grade_count = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) as total FROM grades"))['total'] ?? 0;
$exam_count = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) as total FROM exams"))['total'] ?? 0;
$library_count = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) as total FROM library"))['total'] ?? 0;
$finance_count = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) as total FROM finance"))['total'] ?? 0;

// Recent students
$recent_students = mysqli_query($connect, "SELECT firstname, lastname, regno FROM student ORDER BY id DESC LIMIT 5");

// Fetch recent activities (if activity log table exists)
$recent_activities = [];
if(mysqli_query($connect, "SHOW TABLES LIKE 'activity_log'")->num_rows > 0) {
    $activity_query = mysqli_query($connect, "SELECT action, username, created_at FROM activity_log ORDER BY id DESC LIMIT 5");
    while($row = mysqli_fetch_assoc($activity_query)) {
        $recent_activities[] = $row;
    }
}

// Get current date and time info
$current_hour = date('H');
if($current_hour < 12) $greeting = "Good Morning";
elseif($current_hour < 17) $greeting = "Good Afternoon";
else $greeting = "Good Evening";

// Get today's schedule if timetable table exists
$today_schedule = [];
if(mysqli_query($connect, "SHOW TABLES LIKE 'timetable'")->num_rows > 0) {
    $today = date('l');
    $schedule_query = mysqli_query($connect, "SELECT course_name, time, room FROM timetable WHERE day = '$today' LIMIT 3");
    while($row = mysqli_fetch_assoc($schedule_query)) {
        $today_schedule[] = $row;
    }
}

// Get pending tasks/alerts (customizable)
$pending_alerts = [];
$pending_alerts_count = 0;

// Check for low stock or alerts (example: if any table has thresholds)
// You can customize these based on your actual table structures

// System info
$total_records = $student_count + $course_count + $faculty_count + $department_count + $courseunit_count + $staff_count;
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
    <!-- Chart.js for enhanced charts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
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

        /* Alert badges */
        .alert-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #e74c3c;
            color: white;
            border-radius: 50%;
            width: 22px;
            height: 22px;
            font-size: 0.7rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        /* Quick action buttons */
        .quick-action-btn {
            transition: all 0.2s ease;
            border-radius: 12px;
            padding: 12px;
            background: #f8f9fa;
            text-align: center;
            cursor: pointer;
        }
        .quick-action-btn:hover {
            background: #1f6e8c;
            color: white;
            transform: translateY(-3px);
        }
        .quick-action-btn i { font-size: 1.5rem; margin-bottom: 8px; display: block; }

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
        
        /* Notification panel */
        .notification-toast {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 280px;
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
        <!-- Welcome Section with Greeting -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="bg-white p-4 shadow-sm" style="border-radius: 20px;">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div>
                            <h2 class="fw-semibold mb-2" style="color: #1f5068;"><?php echo $greeting; ?>!</h2>
                            <p class="text-muted mb-0">Welcome back to the Management Dashboard. Here's what's happening at David Elementary University today.</p>
                        </div>
                        <div class="mt-2 mt-sm-0">
                            <span class="badge bg-light text-dark p-2"><i class="fas fa-calendar-alt me-1 text-primary"></i> <?php echo date('l, F j, Y'); ?></span>
                            <span class="badge bg-light text-dark p-2 ms-2"><i class="fas fa-clock me-1 text-success"></i> <span id="liveClock"></span></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards Row 1 -->
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

        <!-- Secondary Statistics Cards (Additional Modules) -->
        <div class="row mb-5">
            <div class="col-12">
                <h4 class="mb-3" style="color: #1f5068;"><i class="fas fa-chart-pie me-2"></i>Module Overview</h4>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <div class="card text-center border-0 shadow-sm h-100">
                    <div class="card-body">
                        <i class="fas fa-file-signature fa-2x text-info mb-2"></i>
                        <h5 class="card-title mb-0"><?php echo number_format($enrollment_count); ?></h5>
                        <p class="text-muted small">Enrollments</p>
                        <a href="enrollment.php" class="stretched-link"></a>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <div class="card text-center border-0 shadow-sm h-100">
                    <div class="card-body">
                        <i class="fas fa-calendar-check fa-2x text-success mb-2"></i>
                        <h5 class="card-title mb-0"><?php echo number_format($attendance_count); ?></h5>
                        <p class="text-muted small">Attendance Records</p>
                        <a href="attendance.php" class="stretched-link"></a>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <div class="card text-center border-0 shadow-sm h-100">
                    <div class="card-body">
                        <i class="fas fa-star-half-alt fa-2x text-warning mb-2"></i>
                        <h5 class="card-title mb-0"><?php echo number_format($grade_count); ?></h5>
                        <p class="text-muted small">Grades Recorded</p>
                        <a href="grades.php" class="stretched-link"></a>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <div class="card text-center border-0 shadow-sm h-100">
                    <div class="card-body">
                        <i class="fas fa-pen-to-square fa-2x text-danger mb-2"></i>
                        <h5 class="card-title mb-0"><?php echo number_format($exam_count); ?></h5>
                        <p class="text-muted small">Exams Scheduled</p>
                        <a href="exams.php" class="stretched-link"></a>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <div class="card text-center border-0 shadow-sm h-100">
                    <div class="card-body">
                        <i class="fas fa-book-bookmark fa-2x text-primary mb-2"></i>
                        <h5 class="card-title mb-0"><?php echo number_format($library_count); ?></h5>
                        <p class="text-muted small">Library Items</p>
                        <a href="library.php" class="stretched-link"></a>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <div class="card text-center border-0 shadow-sm h-100">
                    <div class="card-body">
                        <i class="fas fa-coins fa-2x text-secondary mb-2"></i>
                        <h5 class="card-title mb-0"><?php echo number_format($finance_count); ?></h5>
                        <p class="text-muted small">Finance Transactions</p>
                        <a href="finance.php" class="stretched-link"></a>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <div class="card text-center border-0 shadow-sm h-100">
                    <div class="card-body">
                        <i class="fas fa-chart-line fa-2x text-success mb-2"></i>
                        <h5 class="card-title mb-0"><?php echo number_format($total_records); ?></h5>
                        <p class="text-muted small">Total Records</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <div class="card text-center border-0 shadow-sm h-100">
                    <div class="card-body">
                        <i class="fas fa-database fa-2x text-dark mb-2"></i>
                        <h5 class="card-title mb-0">Active</h5>
                        <p class="text-muted small">System Status</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart Section -->
        <div class="row mb-5">
            <div class="col-md-8 mb-4">
                <div class="recent-card h-100">
                    <div class="card-header">
                        <i class="fas fa-chart-bar me-2"></i> Data Distribution Overview
                    </div>
                    <div class="card-body">
                        <canvas id="distributionChart" height="250"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="recent-card h-100">
                    <div class="card-header">
                        <i class="fas fa-chart-pie me-2"></i> Percentage Breakdown
                    </div>
                    <div class="card-body">
                        <canvas id="pieChart" height="250"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions Section -->
        <div class="row mb-5">
            <div class="col-12">
                <h4 class="mb-3" style="color: #1f5068;"><i class="fas fa-bolt me-2"></i>Quick Actions</h4>
            </div>
            <div class="col-md-2 col-4 mb-3">
                <div class="quick-action-btn" onclick="window.location.href='student.php?action=add'">
                    <i class="fas fa-user-plus"></i>
                    <small>Add Student</small>
                </div>
            </div>
            <div class="col-md-2 col-4 mb-3">
                <div class="quick-action-btn" onclick="window.location.href='course.php?action=add'">
                    <i class="fas fa-plus-circle"></i>
                    <small>Add Course</small>
                </div>
            </div>
            <div class="col-md-2 col-4 mb-3">
                <div class="quick-action-btn" onclick="window.location.href='enrollment.php?action=add'">
                    <i class="fas fa-user-check"></i>
                    <small>New Enrollment</small>
                </div>
            </div>
            <div class="col-md-2 col-4 mb-3">
                <div class="quick-action-btn" onclick="window.location.href='attendance.php?action=take'">
                    <i class="fas fa-fingerprint"></i>
                    <small>Take Attendance</small>
                </div>
            </div>
            <div class="col-md-2 col-4 mb-3">
                <div class="quick-action-btn" onclick="window.location.href='grades.php?action=enter'">
                    <i class="fas fa-edit"></i>
                    <small>Enter Grades</small>
                </div>
            </div>
            <div class="col-md-2 col-4 mb-3">
                <div class="quick-action-btn" onclick="window.location.href='reports.php'">
                    <i class="fas fa-file-alt"></i>
                    <small>Generate Report</small>
                </div>
            </div>
        </div>

        <!-- Recent Activity Row (Enhanced) -->
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
                        <a href="student.php?action=add" class="btn btn-sm btn-primary rounded-pill ms-2"><i class="fas fa-plus"></i> Add New</a>
                    </div>
                </div>
            </div>
            
            <!-- Today's Schedule -->
            <div class="col-lg-6 mb-4">
                <div class="recent-card h-100">
                    <div class="card-header">
                        <i class="fas fa-calendar-day me-2"></i> Today's Schedule
                    </div>
                    <div class="list-group list-group-flush">
                        <?php if(count($today_schedule) > 0): ?>
                            <?php foreach($today_schedule as $schedule): ?>
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="fas fa-chalkboard me-2 text-primary"></i>
                                            <strong><?php echo htmlspecialchars($schedule['course_name']); ?></strong>
                                        </div>
                                        <span class="badge bg-info"><?php echo htmlspecialchars($schedule['time']); ?></span>
                                    </div>
                                    <small class="text-muted ms-4">Room: <?php echo htmlspecialchars($schedule['room']); ?></small>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="list-group-item text-muted text-center">No classes scheduled for today.</div>
                        <?php endif; ?>
                        <div class="list-group-item bg-light">
                            <a href="timetable.php" class="text-decoration-none">View Full Timetable <i class="fas fa-arrow-right ms-1"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Second Row of Info Cards -->
        <div class="row">
            <!-- System Overview Card (Enhanced) -->
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
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Faculties</span><span><?php echo $faculty_count; ?></span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-secondary" style="width: <?php echo min(100, $faculty_count / 5); ?>%"></div>
                            </div>
                        </div>
                        <hr>
                        <div class="row text-center mt-3">
                            <div class="col-4">
                                <i class="fas fa-server text-primary"></i>
                                <small class="d-block text-muted">PHP <?php echo phpversion(); ?></small>
                            </div>
                            <div class="col-4">
                                <i class="fas fa-database text-success"></i>
                                <small class="d-block text-muted">MySQL</small>
                            </div>
                            <div class="col-4">
                                <i class="fas fa-shield-alt text-danger"></i>
                                <small class="d-block text-muted">Secure</small>
                            </div>
                        </div>
                        <div class="mt-3">
                            <p class="text-muted small mb-0"><i class="fas fa-database me-1"></i> Database last updated: <?php echo date('Y-m-d H:i:s'); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activities / Notifications -->
            <div class="col-lg-6 mb-4">
                <div class="recent-card h-100">
                    <div class="card-header">
                        <i class="fas fa-bell me-2"></i> Recent Activities & Notifications
                    </div>
                    <div class="list-group list-group-flush">
                        <?php if(count($recent_activities) > 0): ?>
                            <?php foreach($recent_activities as $activity): ?>
                                <div class="list-group-item">
                                    <i class="fas fa-circle me-2" style="font-size: 8px; color: #1f6e8c; vertical-align: middle;"></i>
                                    <strong><?php echo htmlspecialchars($activity['action']); ?></strong>
                                    <br>
                                    <small class="text-muted">by <?php echo htmlspecialchars($activity['username']); ?> • <?php echo date('H:i, M d', strtotime($activity['created_at'])); ?></small>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="list-group-item text-muted text-center">No recent activities.</div>
                        <?php endif; ?>
                        <div class="list-group-item bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-tasks me-2"></i> Pending Tasks</span>
                                <span class="badge bg-warning">2</span>
                            </div>
                            <small class="text-muted">• Grade submissions pending for 3 courses</small><br>
                            <small class="text-muted">• Library returns overdue: 5 items</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Academic Year / Semester Info -->
        <div class="row mt-2">
            <div class="col-12">
                <div class="alert alert-info alert-dismissible fade show mb-4" role="alert" style="border-radius: 16px; background: linear-gradient(135deg, #e8f4f8 0%, #d4eaf1 100%); border: none;">
                    <i class="fas fa-graduation-cap me-2"></i>
                    <strong>Academic Year 2024-2025 | Semester 2</strong> — Mid-term examinations begin on <strong>March 15, 2025</strong>. Please ensure all grades are submitted by the deadline.
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- FOOTER -->
    <footer>
        <div class="container">
            <i class="fas fa-graduation-cap me-1"></i> David Elementary University — Empowering future leaders
            <br><small>© <?php echo date("Y"); ?> | Management Dashboard | Version 2.0 | Developed with <i class="fas fa-heart text-danger"></i> for Education</small>
        </div>
    </footer>
</div>

<!-- Notification Toast -->
<div class="notification-toast" id="notificationToast" style="display: none;">
    <div class="toast" role="alert" data-autohide="true" data-delay="5000">
        <div class="toast-header">
            <i class="fas fa-bell me-2 text-primary"></i>
            <strong class="me-auto">System Notification</strong>
            <small>Just now</small>
            <button type="button" class="ml-2 mb-1 close" data-dismiss="toast">&times;</button>
        </div>
        <div class="toast-body" id="toastMessage">
            Welcome back to the dashboard!
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="bootstrap/dist/js/bootstrap.min.js"></script>

<script>
    // Live Clock
    function updateClock() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        document.getElementById('liveClock').innerText = timeString;
    }
    updateClock();
    setInterval(updateClock, 1000);

    // Distribution Chart (Bar Chart)
    const ctx1 = document.getElementById('distributionChart').getContext('2d');
    new Chart(ctx1, {
        type: 'bar',
        data: {
            labels: ['Students', 'Courses', 'Faculties', 'Departments', 'Course Units', 'Staff'],
            datasets: [{
                label: 'Total Count',
                data: [<?php echo $student_count; ?>, <?php echo $course_count; ?>, <?php echo $faculty_count; ?>, <?php echo $department_count; ?>, <?php echo $courseunit_count; ?>, <?php echo $staff_count; ?>],
                backgroundColor: [
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(75, 192, 192, 0.7)',
                    'rgba(255, 206, 86, 0.7)',
                    'rgba(153, 102, 255, 0.7)',
                    'rgba(255, 159, 64, 0.7)',
                    'rgba(255, 99, 132, 0.7)'
                ],
                borderColor: [
                    'rgb(54, 162, 235)',
                    'rgb(75, 192, 192)',
                    'rgb(255, 206, 86)',
                    'rgb(153, 102, 255)',
                    'rgb(255, 159, 64)',
                    'rgb(255, 99, 132)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 }
                }
            },
            plugins: {
                legend: { position: 'top' },
                tooltip: { callbacks: { label: function(context) { return context.raw + ' records'; } } }
            }
        }
    });

    // Pie Chart
    const ctx2 = document.getElementById('pieChart').getContext('2d');
    new Chart(ctx2, {
        type: 'pie',
        data: {
            labels: ['Students', 'Courses', 'Staff', 'Others'],
            datasets: [{
                data: [<?php echo $student_count; ?>, <?php echo $course_count; ?>, <?php echo $staff_count; ?>, <?php echo $faculty_count + $department_count + $courseunit_count; ?>],
                backgroundColor: ['#36a2eb', '#4bc0c0', '#ffce56', '#ff6384'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { position: 'bottom' },
                tooltip: { callbacks: { label: function(context) { return context.label + ': ' + context.raw + ' (' + ((context.raw / <?php echo max(1, $total_records); ?>)*100).toFixed(1) + '%)'; } } }
            }
        }
    });

    // Show notification toast on page load
    $(document).ready(function() {
        $('#notificationToast').fadeIn().find('.toast').toast('show');
        setTimeout(function() {
            $('#notificationToast').fadeOut();
        }, 6000);
    });
</script>
</body>
</html>