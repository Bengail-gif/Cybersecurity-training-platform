<?php
// admin/dashboard.php
require_once 'auth_check.php';
require_once '../config/db.php';

// Fetch live statistics from your database
try {
    $total_students = $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
    $total_courses = $pdo->query("SELECT COUNT(*) FROM courses")->fetchColumn();
    $total_campaigns = $pdo->query("SELECT COUNT(*) FROM phishing_campaigns")->fetchColumn();
    
    // Get recent students
    $recent_students_stmt = $pdo->query("SELECT student_id, full_name FROM students ORDER BY student_id DESC LIMIT 5");
    $recent_students = $recent_students_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $total_students = 0;
    $total_courses = 0;
    $total_campaigns = 0;
    $recent_students = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - FaithSec</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #1e3a8a; --primary-dark: #172554; --accent: #06b6d4;
            --bg: #f1f5f9; --card: #ffffff; --text: #0f172a; --muted: #64748b;
            --border: #e2e8f0; --success: #16a34a; --danger: #dc2626; --warning: #d97706;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body { background: var(--bg); color: var(--text); display: flex; min-height: 100vh; }
        
        /* Sidebar - MATCHES STUDENTS PAGE */
        .sidebar { width: 250px; background: var(--primary-dark); color: white; padding: 20px; position: fixed; height: 100%; }
        .sidebar h2 { font-size: 20px; margin-bottom: 30px; display: flex; align-items: center; gap: 10px; }
        .sidebar h2 i { color: var(--accent); }
        .menu-item { display: block; padding: 12px 15px; color: #cbd5e1; text-decoration: none; border-radius: 6px; margin-bottom: 5px; transition: 0.3s; }
        .menu-item:hover, .menu-item.active { background: rgba(255,255,255,0.1); color: white; }
        .menu-item i { width: 25px; }
        .logout-btn { margin-top: auto; position: absolute; bottom: 20px; width: calc(100% - 40px); background: var(--danger); color: white; text-align: center; padding: 12px 15px; border-radius: 6px; }
        .logout-btn:hover { background: #b91c1c; color: white; }

        /* Main Content */
        .main-content { margin-left: 250px; flex: 1; padding: 30px; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .header h1 { color: var(--primary); font-size: 32px; }
        .header p { color: var(--muted); margin-top: 5px; }
        .user-profile { display: flex; align-items: center; gap: 10px; background: white; padding: 10px 20px; border-radius: 25px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        
        /* Stats Grid */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: var(--card); padding: 25px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); display: flex; align-items: center; gap: 20px; }
        .stat-icon { width: 60px; height: 60px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px; }
        .icon-blue { background: #e0f2fe; color: #0284c7; }
        .icon-green { background: #dcfce7; color: #16a34a; }
        .icon-purple { background: #f3e8ff; color: #9333ea; }
        .stat-info h3 { font-size: 28px; color: var(--text); }
        .stat-info p { color: var(--muted); font-size: 14px; }

        /* Card & Table */
        .card { background: var(--card); border-radius: 10px; padding: 25px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .card h3 { margin-bottom: 20px; color: var(--text); }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid var(--border); }
        th { color: var(--muted); font-weight: 600; font-size: 13px; text-transform: uppercase; }
        .badge { padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: bold; }
        .badge-active { background: #dcfce7; color: #16a34a; }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <aside class="sidebar">
        <h2><i class="fas fa-shield-alt"></i> CyberSec Admin</h2>
        <nav>
    <a href="dashboard.php" class="menu-item active"><i class="fas fa-chart-line"></i> Dashboard</a>
    <a href="students.php" class="menu-item"><i class="fas fa-user-graduate"></i> Students</a>
    <a href="courses.php" class="menu-item"><i class="fas fa-book"></i> Courses</a>
    <a href="phishing.php" class="menu-item"><i class="fas fa-fish"></i> Phishing Sims</a>
    <a href="logout.php" class="menu-item logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
</nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <div class="header">
            <div>
                <h1>Welcome back, <?= htmlspecialchars(explode(' ', $admin_name)[0]) ?>!</h1>
                <p>Here's what's happening at Faith University today.</p>
            </div>
            <div class="user-profile">
                <i class="fas fa-user-circle" style="font-size: 24px; color: var(--primary);"></i>
                <span><?= htmlspecialchars($admin_name) ?></span>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon icon-blue"><i class="fas fa-users"></i></div>
                <div class="stat-info">
                    <h3><?= $total_students ?></h3>
                    <p>Total Students</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon icon-green"><i class="fas fa-book-open"></i></div>
                <div class="stat-info">
                    <h3><?= $total_courses ?></h3>
                    <p>Active Courses</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon icon-purple"><i class="fas fa-fish"></i></div>
                <div class="stat-info">
                    <h3><?= $total_campaigns ?></h3>
                    <p>Phishing Campaigns</p>
                </div>
            </div>
        </div>

        <!-- Recent Students Table -->
        <div class="card">
            <h3>Recently Registered Students</h3>
            <table>
                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>Full Name</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($recent_students) > 0): ?>
                        <?php foreach ($recent_students as $student): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($student['student_id']) ?></strong></td>
                            <td><?= htmlspecialchars($student['full_name']) ?></td>
                            <td><span class="badge badge-active">Active</span></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" style="text-align: center; color: var(--muted);">No students registered yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

</body>
</html>