<?php
require_once 'includes/db.php';
include 'includes/header.php';

// Enroll logic
if (isset($_GET['enroll']) && isset($_SESSION['user_id'])) {
    $course_id = $_GET['enroll'];
    $user_id = $_SESSION['user_id'];

    // Check if already enrolled
    $check = $pdo->prepare("SELECT * FROM enrollments WHERE user_id = ? AND course_id = ?");
    $check->execute([$user_id, $course_id]);
    
    if (!$check->fetch()) {
        $stmt = $pdo->prepare("INSERT INTO enrollments (user_id, course_id) VALUES (?, ?)");
        $stmt->execute([$user_id, $course_id]);
        header("Location: /student/dashboard.php?enrolled=1");
        exit;
    }
}

$stmt = $pdo->query("SELECT * FROM courses");
$courses = $stmt->fetchAll();
?>

<div class="container" style="padding: 60px 0;">
    <h1 class="section-title">Explore Our Courses</h1>
    <div class="course-grid">
        <?php foreach ($courses as $course): ?>
        <div class="course-card">
            <i data-lucide="book-open" class="card-icon" style="color: var(--secondary)"></i>
            <h3><?php echo htmlspecialchars($course['title']); ?></h3>
            <p><?php echo htmlspecialchars($course['description']); ?></p>
            <div style="margin-top: 2rem; display: flex; justify-content: space-between; align-items: center;">
                <span style="font-weight: 800; font-size: 1.25rem;">$<?php echo number_format($course['price'], 2); ?></span>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="courses.php?enroll=<?php echo $course['id']; ?>" class="btn-primary">Enroll Now</a>
                <?php else: ?>
                    <a href="/auth/login.php" class="btn-secondary">Login to Enroll</a>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
