<?php
require_once '../includes/init.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /proyecto-web/auth/login.php");
    exit;
}

if (!isset($_GET['course_id'])) {
    header("Location: manage_courses.php");
    exit;
}

$course_id = $_GET['course_id'];

// Get course details
$stmt = $pdo->prepare("SELECT title FROM courses WHERE id = ?");
$stmt->execute([$course_id]);
$course = $stmt->fetch();

// Fetch all lessons for this course
$stmt = $pdo->prepare("SELECT * FROM lessons WHERE course_id = ? ORDER BY order_index ASC");
$stmt->execute([$course_id]);
$lessons = $stmt->fetchAll();

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
                <a href="dashboard.php">Overview</a>
                <a href="manage_courses.php" class="active">Manage Courses</a>
                <a href="manage_students.php">Manage Students</a>
            </nav>
        </aside>

        <main class="dashboard-main">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                <div>
                    <h2 style="margin-bottom: 0.5rem;">Lessons for: <?php echo htmlspecialchars($course['title']); ?></h2>
                    <a href="manage_courses.php" style="color: var(--text-dim); text-decoration: none;">&larr; Back to Courses</a>
                </div>
                <a href="add_lesson.php?course_id=<?php echo $course_id; ?>" class="btn-primary">+ Add New Lesson</a>
            </div>

            <div style="background: var(--card-bg); border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05)">
                <table style="width: 100%; border-collapse: collapse; text-align: left;">
                    <thead>
                        <tr style="background: rgba(255,255,255,0.02); border-bottom: 1px solid rgba(255,255,255,0.1)">
                            <th style="padding: 1rem;">Order</th>
                            <th style="padding: 1rem;">Lesson Title</th>
                            <th style="padding: 1rem;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($lessons)): ?>
                            <tr><td colspan="3" style="padding: 2rem; text-align: center; color: var(--text-dim);">No lessons found for this course.</td></tr>
                        <?php else: ?>
                            <?php foreach ($lessons as $lesson): ?>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.05)">
                                <td style="padding: 1rem;"><?php echo $lesson['order_index']; ?></td>
                                <td style="padding: 1rem;"><?php echo htmlspecialchars($lesson['title']); ?></td>
                                <td style="padding: 1rem;">
                                    <a href="edit_lesson.php?id=<?php echo $lesson['id']; ?>" style="color: var(--primary); text-decoration: none; margin-right: 1rem;">Edit</a>
                                    <a href="delete_lesson.php?id=<?php echo $lesson['id']; ?>&course_id=<?php echo $course_id; ?>" style="color: #ef4444; text-decoration: none;" onclick="return confirm('Are you sure?')">Delete</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
