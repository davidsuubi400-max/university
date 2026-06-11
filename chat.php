<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>David Elementary University Dashboard</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

<style>

:root{
    --primary:#0b2b40;
    --secondary:#1f6e8c;
    --accent:#ff8c42;
    --light:#f4f7fc;
    --dark:#1a1a1a;
}

body{
    background:var(--light);
    font-family:'Segoe UI',sans-serif;
}

.sidebar{
    position:fixed;
    top:0;
    left:0;
    width:260px;
    height:100vh;
    background:linear-gradient(180deg,var(--primary),var(--secondary));
    color:white;
    overflow-y:auto;
    z-index:999;
}

.sidebar .logo{
    text-align:center;
    padding:25px 15px;
    border-bottom:1px solid rgba(255,255,255,.15);
}

.sidebar .logo h4{
    margin-top:10px;
    font-weight:700;
}

.sidebar a{
    display:block;
    color:white;
    text-decoration:none;
    padding:14px 25px;
    transition:.3s;
}

.sidebar a:hover{
    background:rgba(255,255,255,.1);
    padding-left:35px;
}

.sidebar i{
    width:30px;
}

.main-content{
    margin-left:260px;
    padding:25px;
}

.topbar{
    background:white;
    padding:15px 25px;
    border-radius:15px;
    box-shadow:0 5px 15px rgba(0,0,0,.05);
    margin-bottom:25px;
}

.card-dashboard{
    border:none;
    border-radius:20px;
    box-shadow:0 10px 25px rgba(0,0,0,.08);
    transition:.3s;
}

.card-dashboard:hover{
    transform:translateY(-5px);
}

.stat-icon{
    font-size:40px;
    color:var(--accent);
}

.stat-number{
    font-size:34px;
    font-weight:700;
    color:var(--secondary);
}

.section-card{
    background:white;
    border:none;
    border-radius:20px;
    box-shadow:0 10px 25px rgba(0,0,0,.08);
}

.quick-btn{
    min-width:170px;
    margin:5px;
}

.notification{
    border-left:4px solid orange;
    padding:10px;
    background:#fff7eb;
    margin-bottom:10px;
    border-radius:8px;
}

.activity-item{
    padding:12px;
    border-bottom:1px solid #eee;
}

.announcement{
    background:#eef7ff;
    padding:15px;
    border-radius:10px;
    margin-bottom:10px;
}

footer{
    margin-top:30px;
    text-align:center;
    color:#777;
}

.dark-mode{
    background:#121212 !important;
    color:white;
}

.dark-mode .section-card,
.dark-mode .topbar,
.dark-mode .card-dashboard{
    background:#1f1f1f;
    color:white;
}

.dark-mode .table{
    color:white;
}

</style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">

    <div class="logo">
        <i class="fas fa-graduation-cap fa-3x"></i>
        <h4>DEU</h4>
        <small>University ERP</small>
    </div>

    <a href="#"><i class="fas fa-chart-line"></i> Dashboard</a>
    <a href="#"><i class="fas fa-user-graduate"></i> Students</a>
    <a href="#"><i class="fas fa-book-open"></i> Courses</a>
    <a href="#"><i class="fas fa-building"></i> Departments</a>
    <a href="#"><i class="fas fa-chalkboard-user"></i> Faculty</a>
    <a href="#"><i class="fas fa-users"></i> Staff</a>
    <a href="#"><i class="fas fa-file-signature"></i> Enrollment</a>
    <a href="#"><i class="fas fa-calendar-check"></i> Attendance</a>
    <a href="#"><i class="fas fa-star"></i> Grades</a>
    <a href="#"><i class="fas fa-money-bill-wave"></i> Finance</a>
    <a href="#"><i class="fas fa-book"></i> Library</a>
    <a href="#"><i class="fas fa-chart-pie"></i> Reports</a>
    <a href="#"><i class="fas fa-cog"></i> Settings</a>
    <a href="#"><i class="fas fa-sign-out-alt"></i> Logout</a>

</div>

<!-- MAIN CONTENT -->
<div class="main-content">

    <!-- TOPBAR -->
    <div class="topbar d-flex justify-content-between align-items-center">

        <div>
            <h3 class="mb-0">Dashboard</h3>
            <small class="text-muted">Welcome back Administrator</small>
        </div>

        <div class="d-flex align-items-center gap-3">

            <input type="text" class="form-control" placeholder="Search..." style="width:250px">

            <button class="btn btn-dark" onclick="toggleDarkMode()">
                <i class="fas fa-moon"></i>
            </button>

            <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown">
                    Admin
                </button>

                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#">Profile</a></li>
                    <li><a class="dropdown-item" href="#">Settings</a></li>
                    <li><a class="dropdown-item" href="#">Logout</a></li>
                </ul>
            </div>

        </div>

    </div>

    <!-- STATISTICS -->
    <div class="row">

        <div class="col-lg-3 mb-4">
            <div class="card card-dashboard">
                <div class="card-body text-center">
                    <i class="fas fa-user-graduate stat-icon"></i>
                    <div class="stat-number">1250</div>
                    <div>Total Students</div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 mb-4">
            <div class="card card-dashboard">
                <div class="card-body text-center">
                    <i class="fas fa-book-open stat-icon"></i>
                    <div class="stat-number">54</div>
                    <div>Courses</div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 mb-4">
            <div class="card card-dashboard">
                <div class="card-body text-center">
                    <i class="fas fa-users stat-icon"></i>
                    <div class="stat-number">120</div>
                    <div>Staff</div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 mb-4">
            <div class="card card-dashboard">
                <div class="card-body text-center">
                    <i class="fas fa-money-bill-wave stat-icon"></i>
                    <div class="stat-number">$85K</div>
                    <div>Revenue</div>
                </div>
            </div>
        </div>

    </div>

    <!-- QUICK ACTIONS -->
    <div class="section-card p-4 mb-4">

        <h5>Quick Actions</h5>

        <button class="btn btn-primary quick-btn">
            <i class="fas fa-user-plus"></i> Add Student
        </button>

        <button class="btn btn-success quick-btn">
            <i class="fas fa-book-medical"></i> Add Course
        </button>

        <button class="btn btn-warning quick-btn">
            <i class="fas fa-user-tie"></i> Add Staff
        </button>

        <button class="btn btn-danger quick-btn">
            <i class="fas fa-file-pdf"></i> Generate Report
        </button>

    </div>

    <div class="row">

        <!-- CHART -->
        <div class="col-lg-8 mb-4">
            <div class="section-card p-4">
                <h5>Student Growth</h5>
                <canvas id="studentChart"></canvas>
            </div>
        </div>

        <!-- NOTIFICATIONS -->
        <div class="col-lg-4 mb-4">

            <div class="section-card p-4">

                <h5>Notifications</h5>

                <div class="notification">
                    15 students have pending tuition fees.
                </div>

                <div class="notification">
                    Examination week begins next Monday.
                </div>

                <div class="notification">
                    New course registration opened.
                </div>

            </div>

        </div>

    </div>

    <!-- FINANCE + ATTENDANCE -->
    <div class="row">

        <div class="col-lg-6 mb-4">
            <div class="section-card p-4">
                <h5>Finance Summary</h5>

                <div class="mb-3">
                    <strong>Revenue Collected</strong>
                    <div class="progress mt-2">
                        <div class="progress-bar bg-success" style="width:80%">
                            $80,000
                        </div>
                    </div>
                </div>

                <div>
                    <strong>Outstanding Fees</strong>
                    <div class="progress mt-2">
                        <div class="progress-bar bg-danger" style="width:35%">
                            $20,000
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="section-card p-4">
                <h5>Attendance Rate</h5>

                <div class="progress" style="height:30px;">
                    <div class="progress-bar bg-info" style="width:89%">
                        89%
                    </div>
                </div>

            </div>
        </div>

    </div>

    <!-- RECENT ACTIVITIES + ANNOUNCEMENTS -->
    <div class="row">

        <div class="col-lg-6 mb-4">

            <div class="section-card">

                <div class="p-4 border-bottom">
                    <h5>Recent Activities</h5>
                </div>

                <div class="activity-item">
                    Student John Doe Registered
                </div>

                <div class="activity-item">
                    New Course Added
                </div>

                <div class="activity-item">
                    Faculty Record Updated
                </div>

                <div class="activity-item">
                    Staff Account Created
                </div>

            </div>

        </div>

        <div class="col-lg-6 mb-4">

            <div class="section-card p-4">

                <h5>Announcements</h5>

                <div class="announcement">
                    Semester begins on September 1st.
                </div>

                <div class="announcement">
                    Graduation ceremony scheduled for November.
                </div>

                <div class="announcement">
                    New library resources are available.
                </div>

            </div>

        </div>

    </div>

    <!-- TABLE -->
    <div class="section-card p-4">

        <h5>Recent Students</h5>

        <table class="table">

            <thead>
                <tr>
                    <th>Reg No</th>
                    <th>Name</th>
                    <th>Course</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>

                <tr>
                    <td>DEU001</td>
                    <td>John Doe</td>
                    <td>Computer Science</td>
                    <td><span class="badge bg-success">Active</span></td>
                </tr>

                <tr>
                    <td>DEU002</td>
                    <td>Jane Smith</td>
                    <td>Information Technology</td>
                    <td><span class="badge bg-success">Active</span></td>
                </tr>

                <tr>
                    <td>DEU003</td>
                    <td>David Brown</td>
                    <td>Business Administration</td>
                    <td><span class="badge bg-warning">Pending</span></td>
                </tr>

            </tbody>

        </table>

    </div>

    <footer>
        © 2026 David Elementary University | University Management Dashboard
    </footer>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

const ctx = document.getElementById('studentChart');

new Chart(ctx,{
    type:'line',
    data:{
        labels:['Jan','Feb','Mar','Apr','May','Jun'],
        datasets:[{
            label:'Students',
            data:[500,700,850,950,1100,1250],
            borderColor:'#1f6e8c',
            backgroundColor:'rgba(31,110,140,0.2)',
            fill:true,
            tension:.4
        }]
    }
});

function toggleDarkMode(){
    document.body.classList.toggle('dark-mode');
}

</script>

</body>
</html>