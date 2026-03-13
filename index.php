<?php 
require_once 'includes/db.php';
include 'includes/header.php'; 

// Fetch featured courses
$stmt = $pdo->query("SELECT * FROM courses LIMIT 3");
$courses = $stmt->fetchAll();
?>

<main>
    <section class="hero">
        <div class="container">
            <h1>Unlock Your Potential with <span>CodeAcademy</span></h1>
            <p>Master the most in-demand programming languages from industry experts.</p>
            <div class="hero-btns">
                <a href="/auth/register.php" class="btn-primary">Get Started for Free</a>
                <a href="/courses.php" class="btn-secondary">View Courses</a>
            </div>
        </div>
    </section>

    <section id="featured-courses" class="courses">
        <div class="container">
            <h2 class="section-title">Featured Courses</h2>
            <div class="course-grid">
                <?php foreach ($courses as $course): ?>
                <div class="course-card">
                    <i data-lucide="code" class="card-icon"></i>
                    <h3><?php echo htmlspecialchars($course['title']); ?></h3>
                    <p><?php echo htmlspecialchars($course['description']); ?></p>
                    <div class="card-footer">
                        <span class="price">$<?php echo number_format($course['price'], 2); ?></span>
                        <a href="/courses.php" class="btn-secondary">Details</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
