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

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Filter Logic
$search = isset($_GET['search']) ? $_GET['search'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';
$sql = "SELECT * FROM courses WHERE 1=1";
$params = [];

if (!empty($search)) {
    $sql .= " AND (title LIKE ? OR description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($category)) {
    $sql .= " AND category = ?";
    $params[] = $category;
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$courses = $stmt->fetchAll();

// Fetch Average Ratings for all courses
$ratings_stmt = $pdo->query("SELECT course_id, AVG(rating) as avg_rating, COUNT(*) as count FROM reviews GROUP BY course_id");
$ratings_data = $ratings_stmt->fetchAll(PDO::FETCH_UNIQUE);

// Get unique categories for the filter
$cat_stmt = $pdo->query("SELECT DISTINCT category FROM courses");
$all_categories = $cat_stmt->fetchAll(PDO::FETCH_COLUMN);
?>

<div class="container" style="padding: 60px 0;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 3rem; flex-wrap: wrap; gap: 2rem;">
        <h1 style="font-size: 2.5rem; font-weight: 800;">Explore Our Courses</h1>

        <div style="display: flex; gap: 1rem; flex-wrap: wrap; width: 100%; max-width: 800px; justify-content: flex-end;">
            <form action="courses.php" method="GET" style="display: flex; gap: 0.5rem; flex: 1; max-width: 350px;">
                <input type="text" name="search" placeholder="Search courses..." value="<?php echo htmlspecialchars($search); ?>" 
                       style="flex: 1; padding: 0.8rem 1.2rem; border-radius: 8px; background: var(--card-bg); border: 1px solid rgba(255,255,255,0.1); color: white;">
                <?php if (!empty($category)): ?>
                    <input type="hidden" name="category" value="<?php echo htmlspecialchars($category); ?>">
                <?php endif; ?>
                <button type="submit" class="btn-primary" style="padding: 0.8rem 1.5rem;">Search</button>
            </form>

            <div style="display: flex; gap: 0.5rem; align-items: center;">
                <span style="color: var(--text-dim); font-size: 0.9rem;">Filter by:</span>
                <select onchange="window.location.href='courses.php?search=<?php echo urlencode($search); ?>&category=' + this.value" 
                        style="padding: 0.8rem 1.2rem; border-radius: 8px; background: var(--card-bg); border: 1px solid rgba(255,255,255,0.1); color: white; cursor: pointer;">
                    <option value="">All Categories</option>
                    <?php foreach ($all_categories as $cat): ?>
                        <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo $category == $cat ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>

    <?php if (empty($courses)): ?>
        <div style="text-align: center; padding: 4rem; background: var(--card-bg); border-radius: 16px;">
            <i data-lucide="search-x" style="width: 48px; height: 48px; color: var(--text-dim); margin-bottom: 1rem;"></i>
            <h3>No courses found matching your criteria.</h3>
            <a href="courses.php" style="color: var(--primary); text-decoration: none; display: block; margin-top: 1rem;">View all courses</a>
        </div>
    <?php else: ?>
        <div class="course-grid">
            <?php foreach ($courses as $course): 
                $thumbnail = !empty($course['thumbnail']) ? '/assets/img/uploads/' . $course['thumbnail'] : 'https://images.unsplash.com/photo-1498050108023-c5249f4df085?auto=format&fit=crop&w=600&q=80';
            ?>
            <div class="course-card" style="padding: 0; overflow: hidden; position: relative;">
                <span style="position: absolute; top: 1rem; left: 1rem; background: rgba(15, 23, 42, 0.8); backdrop-filter: blur(8px); padding: 0.4rem 1rem; border-radius: 100px; font-size: 0.75rem; color: var(--primary); font-weight: 600; border: 1px solid rgba(255,255,255,0.1);">
                    <?php echo htmlspecialchars($course['category']); ?>
                </span>
                <img src="<?php echo $thumbnail; ?>" alt="<?php echo htmlspecialchars($course['title']); ?>" style="width: 100%; height: 200px; object-fit: cover;">
                <div style="padding: 2rem;">
                    <?php 
                    $avg = isset($ratings_data[$course['id']]) ? round($ratings_data[$course['id']]['avg_rating'], 1) : 0;
                    $count = isset($ratings_data[$course['id']]) ? $ratings_data[$course['id']]['count'] : 0;
                    ?>
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                        <div style="color: #fbbf24; display: flex; align-items: center;">
                            <i data-lucide="star" style="width: 14px; height: 14px; fill: currentColor;"></i>
                            <span style="margin-left: 4px; font-weight: 600; color: var(--text-main);"><?php echo $avg ?: 'New'; ?></span>
                        </div>
                        <span style="color: var(--text-dim); font-size: 0.8rem;">(<?php echo $count; ?> reviews)</span>
                    </div>
                    <h3><?php echo htmlspecialchars($course['title']); ?></h3>
                    <p><?php echo htmlspecialchars($course['description']); ?></p>
                    <div style="margin-top: 2rem; display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-weight: 800; font-size: 1.25rem;">$<?php echo number_format($course['price'], 2); ?></span>
                        <?php if ($user_id): ?>
                            <a href="checkout.php?course_id=<?php echo $course['id']; ?>" class="btn-primary">Enroll Now</a>
                        <?php else: ?>
                            <a href="/auth/login.php" class="btn-secondary">Login to Enroll</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
