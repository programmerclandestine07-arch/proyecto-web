<?php
require_once '../includes/db.php';
include '../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /auth/login.php");
    exit;
}

// Fetch all courses
$stmt = $pdo->query("SELECT * FROM courses ORDER BY created_at DESC");
$courses = $stmt->fetchAll();
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
                <a href="dashboard.php">Overview</a>
                <a href="manage_courses.php" class="active">Manage Courses</a>
                <a href="manage_students.php">Manage Students</a>
            </nav>
        </aside>

        <main class="dashboard-main">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                <h2>Manage Courses</h2>
                <a href="add_course.php" class="btn-primary">+ Add New Course</a>
            </div>

            <div style="background: var(--card-bg); border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05)">
                <table style="width: 100%; border-collapse: collapse; text-align: left;">
                    <thead>
                        <tr style="background: rgba(255,255,255,0.02); border-bottom: 1px solid rgba(255,255,255,0.1)">
                            <th style="padding: 1rem;">Course Title</th>
                            <th style="padding: 1rem;">Price</th>
                            <th style="padding: 1rem;">Created At</th>
                            <th style="padding: 1rem;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($courses as $course): ?>
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.05)">
                            <td style="padding: 1rem;"><?php echo htmlspecialchars($course['title']); ?></td>
                            <td style="padding: 1rem;">$<?php echo number_format($course['price'], 2); ?></td>
                            <td style="padding: 1rem; color: var(--text-dim)"><?php echo date('M d, Y', strtotime($course['created_at'])); ?></td>
                            <td style="padding: 1rem;">
                                <a href="manage_lessons.php?course_id=<?php echo $course['id']; ?>" style="color: var(--secondary); text-decoration: none; margin-right: 1rem;">Lessons</a>
                                <a href="edit_course.php?id=<?php echo $course['id']; ?>" style="color: var(--primary); text-decoration: none; margin-right: 1rem;">Edit</a>
                                <a href="delete_course.php?id=<?php echo $course['id']; ?>" style="color: #ef4444; text-decoration: none;" onclick="return confirm('Are you sure?')">Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
