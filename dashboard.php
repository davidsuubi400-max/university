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

// Monthly enrollment data (last 6 months)
$monthly_data = [];
for ($i = 5; $i >= 0; $i--) {
    $month = date('Y-m', strtotime("-$i months"));
    $label = date('M Y', strtotime("-$i months"));
    $result = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) as total FROM student WHERE DATE_FORMAT(created_at, '%Y-%m') = '$month'"));
    $monthly_data[] = ['label' => $label, 'count' => $result['total'] ?? 0];
}

// Upcoming exams (next 5)
$upcoming_exams = mysqli_query($connect, "SELECT * FROM exams WHERE exam_date >= CURDATE() ORDER BY exam_date ASC LIMIT 5");

// Pending enrollments count
$pending_enroll = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) as total FROM enrollment WHERE status = 'pending'"))['total'] ?? 0;

// Low attendance alert count (below 75%)
$low_attendance = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) as total FROM attendance WHERE percentage < 75"))['total'] ?? 0;

// Unpaid fees count
$unpaid_fees = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) as total FROM finance WHERE status = 'unpaid'"))['total'] ?? 0;

// Session greeting
$hour = (int)date('H');
$greeting = $hour < 12 ? 'Good Morning' : ($hour < 17 ? 'Good Afternoon' : 'Good Evening');
$greeting_icon = $hour < 12 ? '🌅' : ($hour < 17 ? '☀️' : '🌙');

// Logged-in admin name (update to match your session variable)
$admin_name = $_SESSION['admin_name'] ?? 'Administrator';
$admin_role = $_SESSION['admin_role'] ?? 'Super Admin';
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Dashboard | David Elementary University</title>
    <link rel="stylesheet" href="bootstrap/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --bg: #f0f4f9;
            --surface: #ffffff;
            --surface2: #f8fafc;
            --border: #e2e8f0;
            --text: #1a2e42;
            --text-muted: #64748b;
            --primary: #0f4c75;
            --primary-light: #1b6ca8;
            --accent: #f97316;
            --accent-light: #fff7ed;
            --success: #22c55e;
            --warning: #f59e0b;
            --danger: #ef4444;
            --info: #3b82f6;
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04);
            --shadow: 0 4px 16px rgba(0,0,0,0.08);
            --shadow-lg: 0 12px 32px rgba(0,0,0,0.1);
            --radius: 16px;
            --radius-sm: 10px;
        }
        [data-theme="dark"] {
            --bg: #0d1117;
            --surface: #161b22;
            --surface2: #1c2330;
            --border: #30363d;
            --text: #e6edf3;
            --text-muted: #7d8590;
            --primary: #1b6ca8;
            --primary-light: #388bce;
            --accent: #f97316;
            --accent-light: #271a0a;
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.3);
            --shadow: 0 4px 16px rgba(0,0,0,0.4);
            --shadow-lg: 0 12px 32px rgba(0,0,0,0.5);
        }

        * { font-family: 'Plus Jakarta Sans', sans-serif; margin: 0; padding: 0; box-sizing: border-box; }
        body { background: var(--bg); color: var(--text); transition: background 0.3s, color 0.3s; }

        /* ─── HEADER ─────────────────────────────── */
        .uni-header {
            background: linear-gradient(135deg, #0b2b40 0%, #154e6b 60%, #1a6188 100%);
            color: white;
            padding: 0;
            position: relative;
            overflow: hidden;
        }
        .uni-header::before {
            content: '';
            position: absolute; inset: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
        .header-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1.2rem 2rem;
            position: relative;
            z-index: 1;
        }
        .header-brand { display: flex; align-items: center; gap: 14px; }
        .header-logo {
            width: 52px; height: 52px;
            background: rgba(255,255,255,0.15);
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.6rem;
            border: 1px solid rgba(255,255,255,0.2);
            backdrop-filter: blur(4px);
        }
        .header-title h1 { font-size: 1.3rem; font-weight: 800; letter-spacing: -0.3px; margin: 0; line-height: 1.2; }
        .header-title p { font-size: 0.75rem; opacity: 0.7; margin: 2px 0 0; letter-spacing: 0.08em; text-transform: uppercase; }
        .header-right { display: flex; align-items: center; gap: 12px; }

        /* Search */
        .header-search {
            position: relative;
            display: flex; align-items: center;
        }
        .header-search input {
            background: rgba(255,255,255,0.12);
            border: 1px solid rgba(255,255,255,0.2);
            color: white;
            border-radius: 30px;
            padding: 8px 16px 8px 38px;
            font-size: 0.85rem;
            width: 220px;
            backdrop-filter: blur(4px);
            transition: all 0.3s;
        }
        .header-search input::placeholder { color: rgba(255,255,255,0.55); }
        .header-search input:focus { outline: none; width: 280px; background: rgba(255,255,255,0.18); }
        .header-search .search-icon {
            position: absolute; left: 12px;
            color: rgba(255,255,255,0.6); font-size: 0.8rem;
            pointer-events: none;
        }

        /* Dark mode toggle */
        .theme-toggle {
            width: 36px; height: 36px;
            background: rgba(255,255,255,0.12);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 10px;
            color: white;
            cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.85rem;
            transition: all 0.2s;
        }
        .theme-toggle:hover { background: rgba(255,255,255,0.22); }

        /* Notification bell */
        .notif-btn {
            position: relative;
            width: 36px; height: 36px;
            background: rgba(255,255,255,0.12);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 10px;
            color: white;
            cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.85rem;
            text-decoration: none;
        }
        .notif-btn:hover { background: rgba(255,255,255,0.22); color: white; }
        .notif-badge {
            position: absolute; top: -5px; right: -5px;
            background: var(--accent);
            color: white;
            font-size: 0.6rem; font-weight: 700;
            width: 16px; height: 16px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
        }

        /* Admin avatar */
        .admin-profile {
            display: flex; align-items: center; gap: 10px;
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 30px;
            padding: 5px 14px 5px 5px;
            cursor: pointer;
            text-decoration: none;
            color: white;
        }
        .admin-profile:hover { background: rgba(255,255,255,0.18); color: white; }
        .admin-avatar {
            width: 30px; height: 30px;
            background: linear-gradient(135deg, var(--accent), #fb923c);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.75rem; font-weight: 700;
        }
        .admin-info { line-height: 1.15; }
        .admin-info .name { font-size: 0.8rem; font-weight: 600; }
        .admin-info .role { font-size: 0.68rem; opacity: 0.65; }

        @media (max-width: 768px) {
            .header-search, .admin-info { display: none; }
            .header-title h1 { font-size: 1rem; }
            .header-inner { padding: 1rem; }
        }

        /* ─── NAV BAR ─────────────────────────────── */
        .nav-bar {
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
        }
        .navbar-nav .nav-link {
            color: var(--text);
            font-weight: 500;
            font-size: 0.9rem;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            transition: all 0.2s;
        }
        .navbar-nav .nav-link:hover, .navbar-nav .nav-link.active {
            color: var(--primary-light);
            background: #eff6ff;
        }
        [data-theme="dark"] .navbar-nav .nav-link:hover,
        [data-theme="dark"] .navbar-nav .nav-link.active { background: #1e3a5f; }
        .dropdown-menu {
            border-radius: 14px;
            border: 1px solid var(--border);
            box-shadow: var(--shadow-lg);
            background: var(--surface);
            min-width: 230px;
            margin-top: 10px;
        }
        .dropdown-item {
            padding: 0.6rem 1.2rem;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--text);
            border-radius: 8px;
            margin: 2px 6px;
            width: calc(100% - 12px);
        }
        .dropdown-item i { width: 1.8rem; color: var(--accent); }
        .dropdown-item:hover { background: var(--accent-light); color: var(--primary); }
        .dropdown-header { font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: var(--text-muted); padding: 0.6rem 1.2rem 0.2rem; }
        .dropdown-divider { border-color: var(--border); }

        /* ─── MAIN NAV ─────────────────────────────── */
        .main-nav { background: var(--primary); }
        .main-nav .navbar-nav .nav-link {
            color: rgba(255,255,255,0.8);
            font-size: 0.875rem;
            font-weight: 500;
            padding: 0.7rem 1rem;
            border-radius: 0;
            transition: all 0.2s;
        }
        .main-nav .navbar-nav .nav-link:hover,
        .main-nav .navbar-nav .nav-link.active { color: #fff; background: rgba(255,255,255,0.12); }
        .main-nav .navbar-nav .nav-link i { margin-right: 6px; color: #fbbf24; }
        .main-nav .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba(255,255,255,0.8)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        /* ─── BREADCRUMB ─────────────────────────────── */
        .breadcrumb-bar {
            background: var(--surface2);
            border-bottom: 1px solid var(--border);
            padding: 0.6rem 0;
        }
        .breadcrumb { margin: 0; font-size: 0.82rem; }
        .breadcrumb-item a { color: var(--primary-light); text-decoration: none; }
        .breadcrumb-item.active { color: var(--text-muted); }

        /* ─── CONTENT ─────────────────────────────── */
        .dashboard-container { padding: 2rem 0 4rem; }

        /* Greeting banner */
        .greeting-banner {
            background: var(--surface);
            border-radius: var(--radius);
            padding: 1.8rem 2rem;
            box-shadow: var(--shadow);
            border-left: 4px solid var(--accent);
            display: flex; align-items: center; justify-content: space-between;
            flex-wrap: wrap; gap: 1rem;
        }
        .greeting-text h2 { font-size: 1.4rem; font-weight: 700; color: var(--text); margin: 0; }
        .greeting-text p { color: var(--text-muted); margin: 4px 0 0; font-size: 0.9rem; }
        .quick-actions { display: flex; gap: 10px; flex-wrap: wrap; }
        .btn-quick {
            display: flex; align-items: center; gap: 7px;
            padding: 9px 18px;
            border-radius: 30px;
            font-size: 0.82rem; font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
            white-space: nowrap;
        }
        .btn-quick-primary { background: var(--primary); color: white; border: none; }
        .btn-quick-primary:hover { background: var(--primary-light); color: white; transform: translateY(-2px); box-shadow: 0 6px 16px rgba(15,76,117,0.3); }
        .btn-quick-orange { background: var(--accent); color: white; border: none; }
        .btn-quick-orange:hover { background: #ea6c00; color: white; transform: translateY(-2px); box-shadow: 0 6px 16px rgba(249,115,22,0.3); }
        .btn-quick-outline { background: transparent; color: var(--text); border: 1.5px solid var(--border); }
        .btn-quick-outline:hover { border-color: var(--primary-light); color: var(--primary-light); transform: translateY(-2px); }

        /* Alert cards */
        .alert-strip {
            background: var(--surface);
            border-radius: var(--radius);
            padding: 1.2rem 1.5rem;
            box-shadow: var(--shadow);
            display: flex; align-items: center; gap: 14px;
            border-left: 4px solid var(--warning);
            cursor: pointer;
            transition: transform 0.2s;
        }
        .alert-strip:hover { transform: translateY(-2px); box-shadow: var(--shadow-lg); }
        .alert-strip.danger { border-left-color: var(--danger); }
        .alert-strip.info { border-left-color: var(--info); }
        .alert-strip.success { border-left-color: var(--success); }
        .alert-icon-box {
            width: 42px; height: 42px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.1rem; flex-shrink: 0;
        }
        .alert-icon-box.warning { background: #fef3c7; color: var(--warning); }
        .alert-icon-box.danger { background: #fee2e2; color: var(--danger); }
        .alert-icon-box.info { background: #dbeafe; color: var(--info); }
        .alert-icon-box.success { background: #dcfce7; color: var(--success); }
        .alert-content .label { font-size: 0.8rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; }
        .alert-content .value { font-size: 1.4rem; font-weight: 800; color: var(--text); line-height: 1.2; }
        .alert-content .desc { font-size: 0.78rem; color: var(--text-muted); margin-top: 1px; }

        /* ─── STAT CARDS ─────────────────────────────── */
        .stat-card {
            background: var(--surface);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            transition: transform 0.25s, box-shadow 0.25s;
            overflow: hidden;
            position: relative;
        }
        .stat-card::after {
            content: '';
            position: absolute; bottom: 0; left: 0; right: 0;
            height: 3px;
            background: var(--card-accent, var(--accent));
        }
        .stat-card:hover { transform: translateY(-5px); box-shadow: var(--shadow-lg); }
        .stat-card .card-body { padding: 1.5rem; }
        .stat-top { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 1rem; }
        .stat-icon-box {
            width: 52px; height: 52px;
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.3rem;
        }
        .stat-trend { font-size: 0.75rem; font-weight: 600; padding: 3px 9px; border-radius: 20px; }
        .trend-up { background: #dcfce7; color: #16a34a; }
        .trend-neutral { background: #f1f5f9; color: #64748b; }
        [data-theme="dark"] .trend-up { background: #14532d; color: #4ade80; }
        [data-theme="dark"] .trend-neutral { background: #1e293b; color: #94a3b8; }
        .stat-number { font-size: 2.4rem; font-weight: 800; color: var(--text); line-height: 1; }
        .stat-label { font-size: 0.875rem; font-weight: 500; color: var(--text-muted); margin-top: 4px; }
        .btn-stat {
            display: inline-flex; align-items: center; gap: 6px;
            background: transparent;
            border: 1px solid var(--border);
            border-radius: 30px;
            padding: 6px 16px;
            font-size: 0.78rem; font-weight: 600;
            color: var(--text-muted);
            text-decoration: none;
            margin-top: 1rem;
            transition: all 0.2s;
        }
        .btn-stat:hover { color: var(--primary-light); border-color: var(--primary-light); background: #eff6ff; }

        /* ─── SECTION TITLE ─────────────────────────────── */
        .section-title {
            font-size: 1rem; font-weight: 700; color: var(--text);
            display: flex; align-items: center; gap: 8px;
            padding-bottom: 1rem;
        }
        .section-title .dot { width: 8px; height: 8px; border-radius: 50%; background: var(--accent); }

        /* ─── CARDS (recent, chart, etc.) ─────────────────────────────── */
        .panel {
            background: var(--surface);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            overflow: hidden;
            height: 100%;
        }
        .panel-header {
            padding: 1.1rem 1.5rem;
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center; justify-content: space-between;
        }
        .panel-header h6 { font-size: 0.9rem; font-weight: 700; color: var(--text); margin: 0; }
        .panel-header a { font-size: 0.78rem; color: var(--primary-light); text-decoration: none; font-weight: 600; }
        .panel-header a:hover { text-decoration: underline; }
        .panel-body { padding: 1.2rem 1.5rem; }

        /* List items */
        .stu-item {
            display: flex; align-items: center; gap: 12px;
            padding: 0.75rem 0;
            border-bottom: 1px solid var(--border);
        }
        .stu-item:last-child { border-bottom: none; }
        .stu-avatar {
            width: 36px; height: 36px; border-radius: 10px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: white; display: flex; align-items: center; justify-content: center;
            font-size: 0.75rem; font-weight: 700; flex-shrink: 0;
        }
        .stu-name { font-size: 0.875rem; font-weight: 600; color: var(--text); margin: 0; }
        .stu-reg { font-size: 0.75rem; color: var(--text-muted); }
        .stu-badge { font-size: 0.7rem; font-weight: 600; padding: 2px 10px; border-radius: 20px; background: #dcfce7; color: #16a34a; margin-left: auto; flex-shrink: 0; }
        [data-theme="dark"] .stu-badge { background: #14532d; color: #4ade80; }

        /* Chart canvas */
        #enrollChart { max-height: 220px; }

        /* Overview progress */
        .prog-row { margin-bottom: 1rem; }
        .prog-row:last-child { margin-bottom: 0; }
        .prog-top { display: flex; justify-content: space-between; margin-bottom: 5px; }
        .prog-label { font-size: 0.82rem; font-weight: 600; color: var(--text); }
        .prog-val { font-size: 0.82rem; font-weight: 700; color: var(--text-muted); }
        .progress { background: var(--border); border-radius: 4px; height: 7px; }
        .progress-bar { border-radius: 4px; }

        /* Upcoming exams */
        .exam-item {
            display: flex; align-items: center; gap: 12px;
            padding: 0.7rem 0;
            border-bottom: 1px solid var(--border);
        }
        .exam-item:last-child { border-bottom: none; }
        .exam-date-box {
            min-width: 44px; height: 44px; border-radius: 10px;
            background: var(--accent-light);
            display: flex; flex-direction: column; align-items: center; justify-content: center;
        }
        .exam-date-box .d { font-size: 1rem; font-weight: 800; color: var(--accent); line-height: 1; }
        .exam-date-box .m { font-size: 0.6rem; font-weight: 700; color: var(--accent); text-transform: uppercase; }
        .exam-course { font-size: 0.85rem; font-weight: 600; color: var(--text); }
        .exam-meta { font-size: 0.75rem; color: var(--text-muted); }
        .exam-tag { font-size: 0.68rem; font-weight: 700; padding: 2px 9px; border-radius: 20px; margin-left: auto; flex-shrink: 0; }
        .tag-written { background: #dbeafe; color: #1d4ed8; }
        .tag-practical { background: #fce7f3; color: #9d174d; }
        [data-theme="dark"] .tag-written { background: #1e3a5f; color: #93c5fd; }
        [data-theme="dark"] .tag-practical { background: #4c1d36; color: #f9a8d4; }

        /* Top departments */
        .dept-item { display: flex; align-items: center; gap: 10px; padding: 0.65rem 0; border-bottom: 1px solid var(--border); }
        .dept-item:last-child { border-bottom: none; }
        .dept-rank { font-size: 0.9rem; font-weight: 800; color: var(--text-muted); min-width: 24px; }
        .dept-name { font-size: 0.85rem; font-weight: 600; color: var(--text); }
        .dept-count { font-size: 0.75rem; color: var(--text-muted); }
        .dept-bar-wrap { flex: 1; }
        .dept-bar { height: 5px; border-radius: 3px; background: linear-gradient(90deg, var(--primary), var(--primary-light)); }

        /* Quick links */
        .quick-link-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        .quick-link {
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            padding: 1rem; gap: 8px;
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            text-decoration: none;
            transition: all 0.2s;
        }
        .quick-link:hover { background: #eff6ff; border-color: var(--primary-light); transform: translateY(-2px); }
        [data-theme="dark"] .quick-link:hover { background: #1e3a5f; }
        .quick-link i { font-size: 1.3rem; color: var(--primary-light); }
        .quick-link span { font-size: 0.78rem; font-weight: 600; color: var(--text); text-align: center; }

        /* DB info */
        .db-row { display: flex; align-items: center; gap: 10px; padding: 0.65rem 0; border-bottom: 1px solid var(--border); font-size: 0.82rem; }
        .db-row:last-child { border-bottom: none; }
        .db-row i { color: var(--primary-light); width: 20px; }
        .db-row .val { font-weight: 600; margin-left: auto; color: var(--text-muted); }

        /* ─── FOOTER ─────────────────────────────── */
        footer {
            background: var(--surface);
            border-top: 1px solid var(--border);
            padding: 1.5rem;
            text-align: center;
            font-size: 0.8rem;
            color: var(--text-muted);
        }
        footer a { color: var(--primary-light); text-decoration: none; }

        /* Notification dropdown */
        .notif-panel {
            position: absolute; right: 0; top: calc(100% + 12px);
            width: 320px; background: var(--surface);
            border-radius: 14px;
            border: 1px solid var(--border);
            box-shadow: var(--shadow-lg);
            z-index: 1000;
            display: none;
        }
        .notif-panel.open { display: block; }
        .notif-panel-header { padding: 1rem 1.2rem 0.7rem; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; }
        .notif-panel-header h6 { margin: 0; font-size: 0.9rem; font-weight: 700; color: var(--text); }
        .notif-item { display: flex; gap: 10px; align-items: flex-start; padding: 0.8rem 1.2rem; border-bottom: 1px solid var(--border); cursor: pointer; transition: background 0.15s; }
        .notif-item:hover { background: var(--surface2); }
        .notif-item:last-child { border-bottom: none; }
        .notif-dot { width: 8px; height: 8px; border-radius: 50%; margin-top: 5px; flex-shrink: 0; }
        .notif-text { font-size: 0.82rem; color: var(--text); font-weight: 500; }
        .notif-time { font-size: 0.72rem; color: var(--text-muted); margin-top: 2px; }

        .notif-wrapper { position: relative; }
    </style>
</head>
<body>

<!-- HEADER -->
<div class="uni-header">
    <div class="header-inner">
        <div class="header-brand">
            <div class="header-logo">🎓</div>
            <div class="header-title">
                <h1>DAVID ELEMENTARY UNIVERSITY</h1>
                <p>"Success · Integrity · Excellence"</p>
            </div>
        </div>
        <div class="header-right">
            <div class="header-search">
                <i class="fas fa-search search-icon"></i>
                <input type="text" placeholder="Search students, courses..." id="globalSearch">
            </div>
            <div class="notif-wrapper">
                <a href="#" class="notif-btn" id="notifBtn" title="Notifications">
                    <i class="fas fa-bell"></i>
                    <?php if (($pending_enroll + $low_attendance + $unpaid_fees) > 0): ?>
                        <span class="notif-badge"><?php echo min(9, $pending_enroll + $low_attendance + $unpaid_fees); ?></span>
                    <?php endif; ?>
                </a>
                <div class="notif-panel" id="notifPanel">
                    <div class="notif-panel-header">
                        <h6><i class="fas fa-bell me-2"></i> Notifications</h6>
                        <a href="#" style="font-size:0.75rem;color:var(--text-muted);">Mark all read</a>
                    </div>
                    <?php if ($pending_enroll > 0): ?>
                    <div class="notif-item">
                        <span class="notif-dot" style="background:var(--warning);"></span>
                        <div>
                            <div class="notif-text"><?php echo $pending_enroll; ?> enrollment(s) pending approval</div>
                            <div class="notif-time">Review in Enrollment module</div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if ($low_attendance > 0): ?>
                    <div class="notif-item">
                        <span class="notif-dot" style="background:var(--danger);"></span>
                        <div>
                            <div class="notif-text"><?php echo $low_attendance; ?> student(s) below 75% attendance</div>
                            <div class="notif-time">Check Attendance module</div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if ($unpaid_fees > 0): ?>
                    <div class="notif-item">
                        <span class="notif-dot" style="background:var(--info);"></span>
                        <div>
                            <div class="notif-text"><?php echo $unpaid_fees; ?> unpaid fee record(s)</div>
                            <div class="notif-time">Check Finance module</div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if (($pending_enroll + $low_attendance + $unpaid_fees) === 0): ?>
                    <div class="notif-item">
                        <span class="notif-dot" style="background:var(--success);"></span>
                        <div>
                            <div class="notif-text">All systems running smoothly</div>
                            <div class="notif-time">No pending actions</div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <button class="theme-toggle" id="themeToggle" title="Toggle dark mode">
                <i class="fas fa-moon" id="themeIcon"></i>
            </button>
            <a href="settings.php" class="admin-profile">
                <div class="admin-avatar"><?php echo strtoupper(substr($admin_name, 0, 2)); ?></div>
                <div class="admin-info">
                    <div class="name"><?php echo htmlspecialchars($admin_name); ?></div>
                    <div class="role"><?php echo htmlspecialchars($admin_role); ?></div>
                </div>
            </a>
        </div>
    </div>
</div>

<!-- TOP NAV (dropdown) -->
<div class="nav-bar">
    <div class="container">
        <nav class="navbar navbar-expand-lg p-0">
            <button class="navbar-toggler ms-auto" type="button" data-toggle="collapse" data-target="#navbarMenu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarMenu">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown">
                            <i class="fas fa-bars"></i> Menu
                        </a>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <h6 class="dropdown-header">Main</h6>
                            <a class="dropdown-item active" href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                            <div class="dropdown-divider"></div>
                            <h6 class="dropdown-header">Academic</h6>
                            <a class="dropdown-item" href="student.php"><i class="fas fa-user-graduate"></i> Student</a>
                            <a class="dropdown-item" href="course.php"><i class="fas fa-book-open"></i> Course</a>
                            <a class="dropdown-item" href="faculty.php"><i class="fas fa-chalkboard-user"></i> Faculty</a>
                            <a class="dropdown-item" href="department.php"><i class="fas fa-building"></i> Department</a>
                            <a class="dropdown-item" href="courseunit.php"><i class="fas fa-layer-group"></i> Course Unit</a>
                            <a class="dropdown-item" href="staff.php"><i class="fas fa-users"></i> Staff</a>
                            <div class="dropdown-divider"></div>
                            <h6 class="dropdown-header">Administration</h6>
                            <a class="dropdown-item" href="enrollment.php"><i class="fas fa-file-signature"></i> Enrollment</a>
                            <a class="dropdown-item" href="attendance.php"><i class="fas fa-calendar-check"></i> Attendance</a>
                            <a class="dropdown-item" href="grades.php"><i class="fas fa-star-half-alt"></i> Grades</a>
                            <a class="dropdown-item" href="timetable.php"><i class="fas fa-calendar-alt"></i> Timetable</a>
                            <a class="dropdown-item" href="exams.php"><i class="fas fa-pen-to-square"></i> Exams</a>
                            <a class="dropdown-item" href="library.php"><i class="fas fa-book-bookmark"></i> Library</a>
                            <a class="dropdown-item" href="finance.php"><i class="fas fa-coins"></i> Finance</a>
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

<!-- MAIN NAV -->
<div class="main-nav">
    <div class="container">
        <nav class="navbar navbar-expand-lg p-0">
            <button class="navbar-toggler my-1" type="button" data-toggle="collapse" data-target="#mainNavLinks">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNavLinks">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link active" href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="student.php"><i class="fas fa-user-graduate"></i> Student</a></li>
                    <li class="nav-item"><a class="nav-link" href="course.php"><i class="fas fa-book-open"></i> Course</a></li>
                    <li class="nav-item"><a class="nav-link" href="faculty.php"><i class="fas fa-chalkboard-user"></i> Faculty</a></li>
                    <li class="nav-item"><a class="nav-link" href="department.php"><i class="fas fa-building"></i> Department</a></li>
                    <li class="nav-item"><a class="nav-link" href="courseunit.php"><i class="fas fa-layer-group"></i> Course Unit</a></li>
                    <li class="nav-item"><a class="nav-link" href="staff.php"><i class="fas fa-users"></i> Staff</a></li>
                </ul>
            </div>
        </nav>
    </div>
</div>

<!-- BREADCRUMB -->
<div class="breadcrumb-bar">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php"><i class="fas fa-home me-1"></i>Home</a></li>
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
        </nav>
    </div>
</div>

<!-- DASHBOARD CONTENT -->
<div class="dashboard-container">
    <div class="container">

        <!-- Greeting + Quick Actions -->
        <div class="greeting-banner mb-4">
            <div class="greeting-text">
                <h2><?php echo $greeting_icon; ?> <?php echo $greeting; ?>, <?php echo htmlspecialchars($admin_name); ?>!</h2>
                <p>Today is <?php echo date('l, F j, Y'); ?> &mdash; Here's what's happening at the university.</p>
            </div>
            <div class="quick-actions">
                <a href="student.php?action=add" class="btn-quick btn-quick-primary">
                    <i class="fas fa-user-plus"></i> Add Student
                </a>
                <a href="course.php?action=add" class="btn-quick btn-quick-orange">
                    <i class="fas fa-plus-circle"></i> Add Course
                </a>
                <a href="enrollment.php" class="btn-quick btn-quick-outline">
                    <i class="fas fa-file-signature"></i> Enrollments
                </a>
                <a href="reports.php" class="btn-quick btn-quick-outline">
                    <i class="fas fa-chart-bar"></i> Reports
                </a>
            </div>
        </div>

        <!-- Alert Strip Row -->
        <?php if ($pending_enroll > 0 || $low_attendance > 0 || $unpaid_fees > 0): ?>
        <div class="row mb-4">
            <?php if ($pending_enroll > 0): ?>
            <div class="col-md-4 mb-3">
                <a href="enrollment.php" style="text-decoration:none;">
                    <div class="alert-strip">
                        <div class="alert-icon-box warning"><i class="fas fa-clock"></i></div>
                        <div class="alert-content">
                            <div class="label">Pending</div>
                            <div class="value"><?php echo $pending_enroll; ?></div>
                            <div class="desc">Enrollments awaiting approval</div>
                        </div>
                        <i class="fas fa-chevron-right ms-auto" style="color:var(--text-muted);font-size:0.8rem;"></i>
                    </div>
                </a>
            </div>
            <?php endif; ?>
            <?php if ($low_attendance > 0): ?>
            <div class="col-md-4 mb-3">
                <a href="attendance.php" style="text-decoration:none;">
                    <div class="alert-strip danger">
                        <div class="alert-icon-box danger"><i class="fas fa-user-minus"></i></div>
                        <div class="alert-content">
                            <div class="label">Low Attendance</div>
                            <div class="value"><?php echo $low_attendance; ?></div>
                            <div class="desc">Students below 75% attendance</div>
                        </div>
                        <i class="fas fa-chevron-right ms-auto" style="color:var(--text-muted);font-size:0.8rem;"></i>
                    </div>
                </a>
            </div>
            <?php endif; ?>
            <?php if ($unpaid_fees > 0): ?>
            <div class="col-md-4 mb-3">
                <a href="finance.php" style="text-decoration:none;">
                    <div class="alert-strip info">
                        <div class="alert-icon-box info"><i class="fas fa-coins"></i></div>
                        <div class="alert-content">
                            <div class="label">Unpaid Fees</div>
                            <div class="value"><?php echo $unpaid_fees; ?></div>
                            <div class="desc">Outstanding fee records</div>
                        </div>
                        <i class="fas fa-chevron-right ms-auto" style="color:var(--text-muted);font-size:0.8rem;"></i>
                    </div>
                </a>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- Stat Cards -->
        <div class="section-title mb-3"><span class="dot"></span> Overview Statistics</div>
        <div class="row mb-5">
            <?php
            $cards = [
                ['icon'=>'fas fa-user-graduate','count'=>$student_count,'label'=>'Total Students','link'=>'student.php','color'=>'#3b82f6','accent'=>'#3b82f6'],
                ['icon'=>'fas fa-book-open','count'=>$course_count,'label'=>'Total Courses','link'=>'course.php','color'=>'#22c55e','accent'=>'#22c55e'],
                ['icon'=>'fas fa-chalkboard-user','count'=>$faculty_count,'label'=>'Total Faculties','link'=>'faculty.php','color'=>'#f97316','accent'=>'#f97316'],
                ['icon'=>'fas fa-building','count'=>$department_count,'label'=>'Total Departments','link'=>'department.php','color'=>'#8b5cf6','accent'=>'#8b5cf6'],
                ['icon'=>'fas fa-layer-group','count'=>$courseunit_count,'label'=>'Course Units','link'=>'courseunit.php','color'=>'#06b6d4','accent'=>'#06b6d4'],
                ['icon'=>'fas fa-users','count'=>$staff_count,'label'=>'Total Staff','link'=>'staff.php','color'=>'#ec4899','accent'=>'#ec4899'],
            ];
            foreach ($cards as $card):
            ?>
            <div class="col-xl-2 col-lg-4 col-md-4 col-6 mb-4">
                <div class="stat-card" style="--card-accent:<?php echo $card['accent']; ?>">
                    <div class="card-body">
                        <div class="stat-top">
                            <div class="stat-icon-box" style="background:<?php echo $card['color']; ?>1a;">
                                <i class="<?php echo $card['icon']; ?>" style="color:<?php echo $card['color']; ?>"></i>
                            </div>
                            <span class="stat-trend trend-up"><i class="fas fa-arrow-up"></i> &mdash;</span>
                        </div>
                        <div class="stat-number"><?php echo $card['count']; ?></div>
                        <div class="stat-label"><?php echo $card['label']; ?></div>
                        <a href="<?php echo $card['link']; ?>" class="btn-stat">Manage <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Row: Chart + Upcoming Exams -->
        <div class="row mb-4">
            <div class="col-lg-7 mb-4">
                <div class="panel">
                    <div class="panel-header">
                        <h6><i class="fas fa-chart-bar me-2" style="color:var(--accent)"></i> Student Enrollment Trend (Last 6 Months)</h6>
                        <a href="reports.php">View Report</a>
                    </div>
                    <div class="panel-body">
                        <canvas id="enrollChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-5 mb-4">
                <div class="panel">
                    <div class="panel-header">
                        <h6><i class="fas fa-pen-to-square me-2" style="color:var(--accent)"></i> Upcoming Exams</h6>
                        <a href="exams.php">View All</a>
                    </div>
                    <div class="panel-body">
                        <?php
                        // Fallback demo data if no DB table exists yet
                        $demo_exams = [
                            ['course'=>'Computer Science 101','date'=>date('Y-m-d', strtotime('+3 days')),'room'=>'Hall A','type'=>'Written'],
                            ['course'=>'Mathematics II','date'=>date('Y-m-d', strtotime('+5 days')),'room'=>'Lab 2','type'=>'Practical'],
                            ['course'=>'Business Administration','date'=>date('Y-m-d', strtotime('+7 days')),'room'=>'Hall B','type'=>'Written'],
                            ['course'=>'Physics','date'=>date('Y-m-d', strtotime('+10 days')),'room'=>'Hall C','type'=>'Written'],
                        ];
                        if ($upcoming_exams && mysqli_num_rows($upcoming_exams) > 0):
                            while ($ex = mysqli_fetch_assoc($upcoming_exams)):
                        ?>
                        <div class="exam-item">
                            <div class="exam-date-box">
                                <div class="d"><?php echo date('d', strtotime($ex['exam_date'])); ?></div>
                                <div class="m"><?php echo date('M', strtotime($ex['exam_date'])); ?></div>
                            </div>
                            <div>
                                <div class="exam-course"><?php echo htmlspecialchars($ex['course_name'] ?? $ex['title'] ?? 'Exam'); ?></div>
                                <div class="exam-meta"><i class="fas fa-door-open me-1"></i><?php echo htmlspecialchars($ex['room'] ?? 'TBA'); ?></div>
                            </div>
                            <span class="exam-tag tag-written">Written</span>
                        </div>
                        <?php endwhile; else:
                            foreach ($demo_exams as $ex): ?>
                        <div class="exam-item">
                            <div class="exam-date-box">
                                <div class="d"><?php echo date('d', strtotime($ex['date'])); ?></div>
                                <div class="m"><?php echo date('M', strtotime($ex['date'])); ?></div>
                            </div>
                            <div>
                                <div class="exam-course"><?php echo $ex['course']; ?></div>
                                <div class="exam-meta"><i class="fas fa-door-open me-1"></i><?php echo $ex['room']; ?></div>
                            </div>
                            <span class="exam-tag <?php echo $ex['type']==='Practical'?'tag-practical':'tag-written'; ?>"><?php echo $ex['type']; ?></span>
                        </div>
                        <?php endforeach; endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Row: Recent Students + Overview + Quick Links -->
        <div class="row mb-4">
            <div class="col-lg-5 mb-4">
                <div class="panel">
                    <div class="panel-header">
                        <h6><i class="fas fa-history me-2" style="color:var(--accent)"></i> Recent Registrations</h6>
                        <a href="student.php">View All</a>
                    </div>
                    <div class="panel-body">
                        <?php
                        // Reset pointer
                        mysqli_data_seek($recent_students, 0);
                        if (mysqli_num_rows($recent_students) > 0):
                            while ($s = mysqli_fetch_assoc($recent_students)):
                                $initials = strtoupper(substr($s['firstname'],0,1).substr($s['lastname'],0,1));
                        ?>
                        <div class="stu-item">
                            <div class="stu-avatar"><?php echo $initials; ?></div>
                            <div>
                                <div class="stu-name"><?php echo htmlspecialchars($s['firstname'].' '.$s['lastname']); ?></div>
                                <div class="stu-reg"><?php echo htmlspecialchars($s['regno']); ?></div>
                            </div>
                            <span class="stu-badge">New</span>
                        </div>
                        <?php endwhile; else: ?>
                        <div class="text-center py-3" style="color:var(--text-muted);font-size:0.85rem;">No recent registrations.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 mb-4">
                <div class="panel">
                    <div class="panel-header">
                        <h6><i class="fas fa-chart-pie me-2" style="color:var(--accent)"></i> System Overview</h6>
                    </div>
                    <div class="panel-body">
                        <?php
                        $prog_items = [
                            ['label'=>'Students','val'=>$student_count,'max'=>500,'color'=>'#3b82f6'],
                            ['label'=>'Courses','val'=>$course_count,'max'=>100,'color'=>'#22c55e'],
                            ['label'=>'Staff','val'=>$staff_count,'max'=>200,'color'=>'#f59e0b'],
                            ['label'=>'Departments','val'=>$department_count,'max'=>20,'color'=>'#8b5cf6'],
                            ['label'=>'Course Units','val'=>$courseunit_count,'max'=>300,'color'=>'#06b6d4'],
                        ];
                        foreach ($prog_items as $pi):
                            $pct = $pi['max'] > 0 ? min(100, round($pi['val'] / $pi['max'] * 100)) : 0;
                        ?>
                        <div class="prog-row">
                            <div class="prog-top">
                                <span class="prog-label"><?php echo $pi['label']; ?></span>
                                <span class="prog-val"><?php echo $pi['val']; ?></span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar" style="width:<?php echo $pct; ?>%;background:<?php echo $pi['color']; ?>"></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <div class="db-row" style="margin-top:1rem;">
                            <i class="fas fa-database"></i>
                            <span style="font-size:0.8rem;color:var(--text-muted);">Last updated</span>
                            <span class="val"><?php echo date('d M H:i'); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 mb-4">
                <div class="panel" style="height:auto;">
                    <div class="panel-header">
                        <h6><i class="fas fa-bolt me-2" style="color:var(--accent)"></i> Quick Links</h6>
                    </div>
                    <div class="panel-body">
                        <div class="quick-link-grid">
                            <a href="enrollment.php" class="quick-link">
                                <i class="fas fa-file-signature"></i>
                                <span>Enrollment</span>
                            </a>
                            <a href="attendance.php" class="quick-link">
                                <i class="fas fa-calendar-check"></i>
                                <span>Attendance</span>
                            </a>
                            <a href="grades.php" class="quick-link">
                                <i class="fas fa-star-half-alt"></i>
                                <span>Grades</span>
                            </a>
                            <a href="timetable.php" class="quick-link">
                                <i class="fas fa-calendar-alt"></i>
                                <span>Timetable</span>
                            </a>
                            <a href="library.php" class="quick-link">
                                <i class="fas fa-book-bookmark"></i>
                                <span>Library</span>
                            </a>
                            <a href="finance.php" class="quick-link">
                                <i class="fas fa-coins"></i>
                                <span>Finance</span>
                            </a>
                        </div>
                    </div>
                    <!-- System info -->
                    <div class="panel-header" style="border-top:1px solid var(--border);margin-top:0.5rem;">
                        <h6><i class="fas fa-server me-2" style="color:var(--accent)"></i> System Info</h6>
                    </div>
                    <div class="panel-body" style="padding-top:0.5rem;">
                        <div class="db-row"><i class="fas fa-clock"></i><span>Server Time</span><span class="val"><?php echo date('H:i:s'); ?></span></div>
                        <div class="db-row"><i class="fas fa-calendar"></i><span>Date</span><span class="val"><?php echo date('d/m/Y'); ?></span></div>
                        <div class="db-row"><i class="fas fa-code-branch"></i><span>Version</span><span class="val">v2.0</span></div>
                        <div class="db-row"><i class="fas fa-circle" style="color:#22c55e;font-size:0.6rem;"></i><span>Status</span><span class="val" style="color:#22c55e;">Online</span></div>
                    </div>
                </div>
            </div>
        </div>

    </div><!-- /container -->
</div>

<!-- FOOTER -->
<footer>
    <div class="container">
        <i class="fas fa-graduation-cap me-1"></i> David Elementary University &mdash; Empowering Future Leaders
        <br><small>© <?php echo date("Y"); ?> Management Dashboard &nbsp;|&nbsp; <a href="settings.php">Settings</a> &nbsp;|&nbsp; <a href="logout.php">Logout</a></small>
    </div>
</footer>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="bootstrap/dist/js/bootstrap.min.js"></script>
<script>
// ─── Dark mode ────────────────────────────────────────────
const html = document.documentElement;
const btn = document.getElementById('themeToggle');
const icon = document.getElementById('themeIcon');
const saved = localStorage.getItem('theme') || 'light';
html.setAttribute('data-theme', saved);
icon.className = saved === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
btn.addEventListener('click', () => {
    const next = html.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
    html.setAttribute('data-theme', next);
    localStorage.setItem('theme', next);
    icon.className = next === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
    if (enrollChart) updateChartTheme();
});

// ─── Notification panel ───────────────────────────────────
const notifBtn = document.getElementById('notifBtn');
const notifPanel = document.getElementById('notifPanel');
notifBtn.addEventListener('click', (e) => {
    e.preventDefault();
    e.stopPropagation();
    notifPanel.classList.toggle('open');
});
document.addEventListener('click', (e) => {
    if (!notifPanel.contains(e.target) && e.target !== notifBtn) {
        notifPanel.classList.remove('open');
    }
});

// ─── Enrollment chart ─────────────────────────────────────
const labels = <?php echo json_encode(array_column($monthly_data, 'label')); ?>;
const counts = <?php echo json_encode(array_column($monthly_data, 'count')); ?>;

let enrollChart;
function getChartColors() {
    const dark = document.documentElement.getAttribute('data-theme') === 'dark';
    return { grid: dark ? '#30363d' : '#e2e8f0', text: dark ? '#7d8590' : '#64748b' };
}

function updateChartTheme() {
    const c = getChartColors();
    enrollChart.options.scales.x.grid.color = c.grid;
    enrollChart.options.scales.y.grid.color = c.grid;
    enrollChart.options.scales.x.ticks.color = c.text;
    enrollChart.options.scales.y.ticks.color = c.text;
    enrollChart.update();
}

function initChart() {
    const c = getChartColors();
    const ctx = document.getElementById('enrollChart').getContext('2d');
    const gradient = ctx.createLinearGradient(0, 0, 0, 220);
    gradient.addColorStop(0, 'rgba(59,130,246,0.3)');
    gradient.addColorStop(1, 'rgba(59,130,246,0.02)');
    enrollChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels,
            datasets: [{
                label: 'New Students',
                data: counts,
                borderColor: '#3b82f6',
                backgroundColor: gradient,
                borderWidth: 2.5,
                pointBackgroundColor: '#3b82f6',
                pointRadius: 5,
                pointHoverRadius: 7,
                fill: true,
                tension: 0.4,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false }, tooltip: { backgroundColor: '#0f4c75', titleColor: '#fff', bodyColor: '#cbd5e1', padding: 10, cornerRadius: 8 } },
            scales: {
                x: { grid: { color: c.grid }, ticks: { color: c.text, font: { size: 11, family: 'Plus Jakarta Sans' } } },
                y: { grid: { color: c.grid }, ticks: { color: c.text, font: { size: 11, family: 'Plus Jakarta Sans' }, stepSize: 1, precision: 0 }, beginAtZero: true }
            }
        }
    });
}
initChart();

// ─── Global search hint ───────────────────────────────────
document.getElementById('globalSearch').addEventListener('keydown', function(e) {
    if (e.key === 'Enter' && this.value.trim()) {
        window.location.href = 'student.php?search=' + encodeURIComponent(this.value.trim());
    }
});
</script>
</body>
</html>
