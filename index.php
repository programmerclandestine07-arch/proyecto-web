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
                <?php foreach ($courses as $course): 
                    $thumbnail = !empty($course['thumbnail']) ? '/assets/img/uploads/' . $course['thumbnail'] : 'https://images.unsplash.com/photo-1498050108023-c5249f4df085?auto=format&fit=crop&w=600&q=80';
                ?>
                <div class="course-card" style="padding: 0; overflow: hidden;">
                    <img src="<?php echo $thumbnail; ?>" alt="<?php echo htmlspecialchars($course['title']); ?>" style="width: 100%; height: 200px; object-fit: cover;">
                    <div style="padding: 2rem;">
                        <h3><?php echo htmlspecialchars($course['title']); ?></h3>
                        <p><?php echo htmlspecialchars($course['description']); ?></p>
                        <div class="card-footer" style="margin-top: 2rem; display: flex; justify-content: space-between; align-items: center;">
                            <span class="price" style="font-weight: 800; font-size: 1.25rem;">$<?php echo number_format($course['price'], 2); ?></span>
                            <a href="/courses.php" class="btn-secondary">Details</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
