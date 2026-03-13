<?php
require_once '../includes/init.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /proyecto-web/auth/login.php");
    exit;
}

// Stats
$userCount = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'student'")->fetchColumn();
$courseCount = $pdo->query("SELECT COUNT(*) FROM courses")->fetchColumn();
$enrollCount = $pdo->query("SELECT COUNT(*) FROM enrollments")->fetchColumn();

include '../includes/header.php';
?>

<div class="container">
    <div class="sidebar-mobile-header" id="sidebar-toggle">
        <span>Admin Menu</span>
        <i data-lucide="chevron-down"></i>
    </div>
    <div class="dashboard-grid">
        <aside class="sidebar">
            <div class="sidebar-user">
                <h3>Admin Panel</h3>
            </div>
            <nav class="sidebar-nav">
                <a href="dashboard.php" class="active">Overview</a>
                <a href="manage_courses.php">Manage Courses</a>
                <a href="manage_students.php">Manage Students</a>
            </nav>
        </aside>

        <main class="dashboard-main">
            <h2>System Overview</h2>
            <div class="course-grid">
                <div class="course-card">
                    <h3><?php echo $userCount; ?></h3>
                    <p>Total Students</p>
                </div>
                <div class="course-card">
                    <h3><?php echo $courseCount; ?></h3>
                    <p>Active Courses</p>
                </div>
                <div class="course-card">
                    <h3><?php echo $enrollCount; ?></h3>
                    <p>Total Enrollments</p>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
