<?php
// admin/courses.php
require_once 'auth_check.php';
require_once '../config/db.php';

$message = '';
$message_type = ''; 

// --- HANDLE FORM SUBMISSIONS ---

// 1. Add New Course
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $category = trim($_POST['category']);
    $duration = trim($_POST['duration']);

    if (!empty($title) && !empty($category) && !empty($duration)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO courses (title, description, category, duration_weeks) VALUES (?, ?, ?, ?)");
            $stmt->execute([$title, $description, $category, $duration]);
            
            $message = "Course added successfully!";
            $message_type = 'success';
        } catch (PDOException $e) {
            $message = "Error: " . $e->getMessage();
            $message_type = 'error';
        }
    } else {
        $message = "Please fill in all required fields.";
        $message_type = 'error';
    }
}

// 2. Delete Course
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM courses WHERE id = ?");
        $stmt->execute([$delete_id]);
        $message = "Course deleted successfully!";
        $message_type = 'success';
    } catch (PDOException $e) {
        $message = "Error deleting course.";
        $message_type = 'error';
    }
}

// --- FETCH ALL COURSES ---
try {
    $stmt = $pdo->query("SELECT * FROM courses ORDER BY id DESC");
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $courses = [];
    $message = "Database Error: " . $e->getMessage();
    $message_type = 'error';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Courses - FaithSec Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #1e3a8a; --primary-dark: #172554; --accent: #06b6d4;
            --bg: #f1f5f9; --card: #ffffff; --text: #0f172a; --muted: #64748b;
            --border: #e2e8f0; --success: #16a34a; --danger: #dc2626;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body { background: var(--bg); color: var(--text); display: flex; min-height: 100vh; }
        
        .sidebar { width: 250px; background: var(--primary-dark); color: white; padding: 20px; position: fixed; height: 100%; }
        .sidebar h2 { font-size: 20px; margin-bottom: 30px; display: flex; align-items: center; gap: 10px; }
        .sidebar h2 i { color: var(--accent); }
        .menu-item { display: block; padding: 12px 15px; color: #cbd5e1; text-decoration: none; border-radius: 6px; margin-bottom: 5px; transition: 0.3s; }
        .menu-item.active { background: #1e3a8a; color: white; font-weight: bold; border-left: 4px solid #06b6d4; }
        .menu-item:hover { background: rgba(255,255,255,0.1); color: white; }
        .menu-item i { width: 25px; }
        .logout-btn { position: absolute; bottom: 20px; width: calc(100% - 40px); background: var(--danger); color: white; text-align: center; padding: 12px 15px; border-radius: 6px; }
        
        .main-content { margin-left: 250px; flex: 1; padding: 30px; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .header h1 { color: var(--primary); }
        
        .alert { padding: 15px; border-radius: 6px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
        .alert-success { background: #dcfce7; color: #16a34a; border: 1px solid #bbf7d0; }
        .alert-error { background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; }

        .btn { padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; }
        .btn-primary { background: var(--primary); color: white; }
        .btn-danger { background: var(--danger); color: white; padding: 6px 12px; font-size: 12px; }
        .card { background: var(--card); border-radius: 10px; padding: 25px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid var(--border); }
        th { color: var(--muted); font-weight: 600; font-size: 13px; text-transform: uppercase; }
        
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; }
        .modal.active { display: flex; }
        .modal-content { background: white; padding: 30px; border-radius: 10px; width: 100%; max-width: 500px; }
        .modal-header { display: flex; justify-content: space-between; margin-bottom: 20px; }
        .close-modal { background: none; border: none; font-size: 24px; cursor: pointer; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: 600; font-size: 14px; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 10px; border: 1px solid var(--border); border-radius: 6px; font-family: inherit; }
        .form-group textarea { resize: vertical; min-height: 80px; }
    </style>
</head>
<body>

    <aside class="sidebar">
        <h2><i class="fas fa-shield-alt"></i> CyberSec Admin</h2>
        <nav>
            <a href="dashboard.php" class="menu-item"><i class="fas fa-chart-line"></i> Dashboard</a>
            <a href="students.php" class="menu-item"><i class="fas fa-user-graduate"></i> Students</a>
            <a href="courses.php" class="menu-item active"><i class="fas fa-book"></i> Courses</a>
            <a href="phishing.php" class="menu-item"><i class="fas fa-fish"></i> Phishing Sims</a>
            <a href="logout.php" class="menu-item logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </aside>

    <main class="main-content">
        <div class="header">
            <h1>Course Management</h1>
            <button class="btn btn-primary" onclick="document.getElementById('addModal').classList.add('active')">
                <i class="fas fa-plus"></i> Add Course
            </button>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-<?= $message_type ?>">
                <i class="fas fa-<?= $message_type === 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <h3>Available Courses (<?= count($courses) ?>)</h3>
            <table>
                <thead>
                    <tr>
                        <th>Course Title</th>
                        <th>Category</th>
                        <th>Duration (Weeks)</th>
                        <th>Enrolled</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($courses) > 0): ?>
                        <?php foreach ($courses as $course): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($course['title']) ?></strong></td>
                            <td><?= htmlspecialchars(ucfirst($course['category'])) ?></td>
                            <td><?= htmlspecialchars($course['duration_weeks']) ?></td>
                            <td><?= htmlspecialchars($course['enrolled_count'] ?? 0) ?></td>
                            <td>
                                <a href="?delete=<?= $course['id'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure?');">
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center; color: var(--muted);">No courses found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <div id="addModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Add New Course</h2>
                <button class="close-modal" onclick="document.getElementById('addModal').classList.remove('active')">&times;</button>
            </div>
            <form method="POST" action="">
                <input type="hidden" name="action" value="add">
                <div class="form-group">
                    <label>Course Title *</label>
                    <input type="text" name="title" required placeholder="e.g., Phishing Awareness">
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" placeholder="Brief description of the course"></textarea>
                </div>
                <div class="form-group">
                    <label>Category *</label>
                    <select name="category" required>
                        <option value="beginner">Beginner</option>
                        <option value="intermediate">Intermediate</option>
                        <option value="advanced">Advanced</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Duration (Weeks) *</label>
                    <input type="number" name="duration" required min="1" placeholder="e.g., 2">
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">Save Course</button>
            </form>
        </div>
    </div>

</body>
</html>