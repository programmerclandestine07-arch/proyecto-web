<?php
require_once '../includes/db.php';
include '../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: /auth/login.php");
    exit;
}

// Fetch user's enrollments
$stmt = $pdo->prepare("SELECT courses.* FROM courses JOIN enrollments ON courses.id = enrollments.course_id WHERE enrollments.user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$enrolled_courses = $stmt->fetchAll();
?>

<div class="container">
    <div class="dashboard-grid">
        <aside class="sidebar">
            <div class="sidebar-user">
                <h3>Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?></h3>
                <p>Student Account</p>
            </div>
            <nav class="sidebar-nav">
                <a href="dashboard.php" class="active">My Learning</a>
                <a href="/courses.php">Explore More</a>
                <a href="#">Settings</a>
            </nav>
        </aside>

        <main class="dashboard-main">
            <h2>My Enrolled Courses</h2>
            <div class="course-grid">
                <?php if (empty($enrolled_courses)): ?>
                    <p>You are not enrolled in any courses yet. <a href="/courses.php">Browse courses here.</a></p>
                <?php else: ?>
                    <?php foreach ($enrolled_courses as $course): ?>
                        <div class="course-card">
                            <i data-lucide="play-circle" class="card-icon"></i>
                            <h3><?php echo htmlspecialchars($course['title']); ?></h3>
                            <a href="#" class="btn-primary" style="display: block; text-align: center; margin-top: 1rem;">Continue Learning</a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </main>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
