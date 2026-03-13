<?php
require_once '../includes/db.php';
include '../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: /auth/login.php");
    exit;
}

// Fetch user's enrollments with progress
$stmt = $pdo->prepare("
    SELECT c.*,
    (SELECT COUNT(*) FROM lessons l WHERE l.course_id = c.id) as total_lessons,
    (SELECT COUNT(*) FROM lesson_completions lc JOIN lessons l ON lc.lesson_id = l.id WHERE lc.user_id = ? AND l.course_id = c.id) as completed_lessons
    FROM courses c
    JOIN enrollments e ON c.id = e.course_id
    WHERE e.user_id = ?
");
$stmt->execute([$_SESSION['user_id'], $_SESSION['user_id']]);
$enrolled_courses = $stmt->fetchAll();
?>

<div class="container">
    <div class="sidebar-mobile-header" id="sidebar-toggle">
        <span>Dashboard Menu</span>
        <i data-lucide="chevron-down"></i>
    </div>
    <div class="dashboard-grid">
        <aside class="sidebar">
            <div class="sidebar-user">
                <h3>Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?></h3>
                <p>Student Account</p>
            </div>
            <nav class="sidebar-nav">
                <a href="dashboard.php" class="active">My Learning</a>
                <a href="/courses.php">Explore More</a>
                <a href="profile.php">Profile Settings</a>
            </nav>
        </aside>

        <main class="dashboard-main">
            <?php if (isset($_GET['payment_success'])): ?>
                <div class="alert" style="background: rgba(34, 197, 94, 0.1); color: #22c55e; border: 1px solid #22c55e; margin-bottom: 2rem;">
                    <i data-lucide="check-circle" style="width: 18px; height: 18px; vertical-align: middle; margin-right: 10px;"></i>
                    Payment successful! Welcome to your new course.
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['enrolled'])): ?>
                <div class="alert" style="background: rgba(34, 197, 94, 0.1); color: #22c55e; border: 1px solid #22c55e; margin-bottom: 2rem;">
                    Successfully enrolled in the course!
                </div>
            <?php endif; ?>

            <h2>My Enrolled Courses</h2>
            <div class="course-grid">
                <?php if (empty($enrolled_courses)): ?>
                    <p>You are not enrolled in any courses yet. <a href="/courses.php">Browse courses here.</a></p>
                <?php else: ?>
                    <?php foreach ($enrolled_courses as $course): 
                        $percent = $course['total_lessons'] > 0 ? round(($course['completed_lessons'] / $course['total_lessons']) * 100) : 0;
                    ?>
                        <div class="course-card">
                            <i data-lucide="play-circle" class="card-icon"></i>
                            <h3 style="margin-bottom: 1rem;"><?php echo htmlspecialchars($course['title']); ?></h3>
                            
                            <div style="margin-bottom: 0.5rem; display: flex; justify-content: space-between; font-size: 0.85rem; color: var(--text-dim);">
                                <span>Progress</span>
                                <span><?php echo $percent; ?>%</span>
                            </div>
                            <div style="background: rgba(255,255,255,0.05); border-radius: 8px; overflow: hidden; height: 6px; margin-bottom: 1.5rem;">
                                <div style="width: <?php echo $percent; ?>%; background: var(--primary); height: 100%; transition: width 0.3s"></div>
                            </div>

                            <a href="view_course.php?id=<?php echo $course['id']; ?>" class="btn-primary" style="display: block; text-align: center; margin-bottom: 0.5rem;">Continue Learning</a>
                            <?php if ($percent == 100): ?>
                                <a href="certificate.php?course_id=<?php echo $course['id']; ?>" target="_blank" class="btn-secondary" style="display: block; text-align: center; border-color: #fbbf24; color: #fbbf24 !important;">
                                    <i data-lucide="award" style="width: 14px; height: 14px; vertical-align: middle; margin-right: 5px;"></i> Download Certificate
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </main>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
