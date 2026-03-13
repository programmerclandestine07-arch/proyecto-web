<?php
require_once '../includes/db.php';
include '../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /auth/login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: manage_students.php");
    exit;
}

$student_id = $_GET['id'];

// Fetch student data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND role = 'student'");
$stmt->execute([$student_id]);
$student = $stmt->fetch();

if (!$student) {
    header("Location: manage_students.php");
    exit;
}

// Fetch enrolled courses and their progress
$stmt = $pdo->prepare("
    SELECT c.id, c.title, e.enrolled_at,
    (SELECT COUNT(*) FROM lessons l WHERE l.course_id = c.id) as total_lessons,
    (SELECT COUNT(*) FROM lesson_completions lc JOIN lessons l ON lc.lesson_id = l.id WHERE lc.user_id = ? AND l.course_id = c.id) as completed_lessons
    FROM courses c
    JOIN enrollments e ON c.id = e.course_id
    WHERE e.user_id = ?
");
$stmt->execute([$student_id, $student_id]);
$enrollments = $stmt->fetchAll();
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
                <a href="manage_courses.php">Manage Courses</a>
                <a href="manage_students.php" class="active">Manage Students</a>
            </nav>
        </aside>

        <main class="dashboard-main">
            <div style="margin-bottom: 2rem;">
                <a href="manage_students.php" style="color: var(--text-dim); text-decoration: none;">&larr; Back to Students</a>
                <h2 style="margin-top: 1rem;">Progress for <?php echo htmlspecialchars($student['name']); ?></h2>
                <p style="color: var(--text-dim);"><?php echo htmlspecialchars($student['email']); ?></p>
            </div>

            <div class="course-grid">
                <?php if (empty($enrollments)): ?>
                    <p style="color: var(--text-dim);">This student is not enrolled in any courses yet.</p>
                <?php else: ?>
                    <?php foreach ($enrollments as $enroll): 
                        $percent = $enroll['total_lessons'] > 0 ? round(($enroll['completed_lessons'] / $enroll['total_lessons']) * 100) : 0;
                    ?>
                        <div class="course-card">
                            <h3 style="margin-bottom: 0.5rem;"><?php echo htmlspecialchars($enroll['title']); ?></h3>
                            <p style="font-size: 0.9rem; color: var(--text-dim); margin-bottom: 1.5rem;">Enrolled: <?php echo date('M d, Y', strtotime($enroll['enrolled_at'])); ?></p>
                            
                            <div style="margin-bottom: 0.5rem; display: flex; justify-content: space-between; font-size: 0.9rem;">
                                <span>Progress</span>
                                <span><?php echo $percent; ?>%</span>
                            </div>
                            <div style="background: rgba(255,255,255,0.05); border-radius: 8px; overflow: hidden; height: 8px;">
                                <div style="width: <?php echo $percent; ?>%; background: var(--primary); height: 100%;"></div>
                            </div>
                            <p style="margin-top: 1rem; font-size: 0.85rem; color: var(--text-dim);">
                                <?php echo $enroll['completed_lessons']; ?> of <?php echo $enroll['total_lessons']; ?> lessons completed
                            </p>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </main>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
