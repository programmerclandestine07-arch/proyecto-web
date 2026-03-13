<?php
require_once '../includes/init.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: /proyecto-web/auth/login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit;
}

$course_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Verify enrollment
$stmt = $pdo->prepare("SELECT * FROM enrollments WHERE user_id = ? AND course_id = ?");
$stmt->execute([$user_id, $course_id]);
if (!$stmt->fetch()) {
    header("Location: dashboard.php");
    exit;
}

// Get course and lessons
$stmt = $pdo->prepare("SELECT title FROM courses WHERE id = ?");
$stmt->execute([$course_id]);
$course = $stmt->fetch();

$stmt = $pdo->prepare("SELECT * FROM lessons WHERE course_id = ? ORDER BY order_index ASC");
$stmt->execute([$course_id]);
$lessons = $stmt->fetchAll();

// Fetch completed lessons for this student
$stmt = $pdo->prepare("SELECT lesson_id FROM lesson_completions WHERE user_id = ?");
$stmt->execute([$user_id]);
$completed_lessons = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Get current lesson
$current_lesson_id = isset($_GET['lesson_id']) ? $_GET['lesson_id'] : (isset($lessons[0]) ? $lessons[0]['id'] : null);
$current_lesson = null;
if ($current_lesson_id) {
    foreach ($lessons as $l) {
        if ($l['id'] == $current_lesson_id) {
            $current_lesson = $l;
            break;
        }
    }
}

// Progress calculation
$total_lessons = count($lessons);
$completed_count = count(array_intersect($completed_lessons, array_column($lessons, 'id')));
$progress_percent = $total_lessons > 0 ? round(($completed_count / $total_lessons) * 100) : 0;

include '../includes/header.php';
?>

<div class="container">
    <div class="dashboard-grid" style="grid-template-columns: 320px 1fr;">
        <aside class="sidebar">
            <h3><?php echo htmlspecialchars($course['title']); ?></h3>
            <div style="margin: 1rem 0; background: rgba(255,255,255,0.05); border-radius: 8px; overflow: hidden; height: 8px;">
                <div style="width: <?php echo $progress_percent; ?>%; background: var(--primary); height: 100%; transition: width 0.3s"></div>
            </div>
            <p style="color: var(--text-dim); margin-bottom: 2rem; font-size: 0.85rem;"><?php echo $progress_percent; ?>% Complete</p>

            <nav class="sidebar-nav">
                <?php foreach ($lessons as $index => $lesson): ?>
                    <a href="view_course.php?id=<?php echo $course_id; ?>&lesson_id=<?php echo $lesson['id']; ?>" 
                       class="<?php echo $current_lesson_id == $lesson['id'] ? 'active' : ''; ?>"
                       style="display: flex; justify-content: space-between; align-items: center;">
                       <span><?php echo ($index + 1) . ". " . htmlspecialchars($lesson['title']); ?></span>
                       <?php if (in_array($lesson['id'], $completed_lessons)): ?>
                           <i data-lucide="check-circle-2" style="width: 16px; height: 16px; color: #22c55e;"></i>
                       <?php endif; ?>
                    </a>
                <?php endforeach; ?>
            </nav>
        </aside>

        <main class="dashboard-main">
            <?php if ($current_lesson): ?>
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 2rem;">
                    <h1 style="margin: 0;"><?php echo htmlspecialchars($current_lesson['title']); ?></h1>
                    <a href="toggle_complete.php?lesson_id=<?php echo $current_lesson['id']; ?>&course_id=<?php echo $course_id; ?>" 
                       class="btn-secondary" style="border-color: <?php echo in_array($current_lesson['id'], $completed_lessons) ? '#22c55e' : 'var(--primary)'; ?>; 
                                                 color: <?php echo in_array($current_lesson['id'], $completed_lessons) ? '#22c55e !important' : 'var(--primary) !important'; ?>">
                        <?php echo in_array($current_lesson['id'], $completed_lessons) ? 'Completed' : 'Mark as Complete'; ?>
                    </a>
                </div>
                
                <?php if (!empty($current_lesson['video_url'])): ?>
                    <div style="aspect-ratio: 16/9; background: #000; border-radius: 12px; margin-bottom: 2rem; overflow: hidden; border: 1px solid rgba(255,255,255,0.1)">
                        <iframe width="100%" height="100%" src="<?php echo str_replace('watch?v=', 'embed/', $current_lesson['video_url']); ?>" 
                                frameborder="0" allowfullscreen></iframe>
                    </div>
                <?php endif; ?>

                <div class="lesson-content" style="background: var(--card-bg); padding: 2rem; border-radius: 12px; border: 1px solid rgba(255,255,255,0.05)">
                    <?php echo nl2br($current_lesson['content']); ?>
                </div>

                <!-- Review Section -->
                <?php
                // Fetch student's current review
                $stmt = $pdo->prepare("SELECT * FROM reviews WHERE user_id = ? AND course_id = ?");
                $stmt->execute([$user_id, $course_id]);
                $my_review = $stmt->fetch();
                ?>
                <div style="margin-top: 4rem; padding: 2rem; background: rgba(255,255,255,0.02); border-radius: 12px; border: 1px dashed rgba(255,255,255,0.1)">
                    <h3 style="margin-bottom: 1.5rem;">Rate this Course</h3>
                    <form action="submit_review.php" method="POST">
                        <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
                        <div class="form-group" style="margin-bottom: 1rem;">
                            <label>Rating (1-5 Stars)</label>
                            <select name="rating" style="width: 100px; padding: 0.5rem; background: #0f172a; color: white; border: 1px solid rgba(255,255,255,0.1); border-radius: 8px;">
                                <?php for($i=5; $i>=1; $i--): ?>
                                    <option value="<?php echo $i; ?>" <?php echo ($my_review && $my_review['rating'] == $i) ? 'selected' : ''; ?>>
                                        <?php echo $i; ?> Stars
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Your Feedback</label>
                            <textarea name="comment" rows="3" placeholder="Tell us what you think..." style="width: 100%; padding: 0.8rem; border-radius: 8px; background: #0f172a; color: white; border: 1px solid rgba(255,255,255,0.1)"><?php echo $my_review ? htmlspecialchars($my_review['comment']) : ''; ?></textarea>
                        </div>
                        <button type="submit" class="btn-secondary" style="margin-top: 1rem;">Submit Review</button>
                    </form>
                </div>
            <?php else: ?>
                <div style="text-align: center; padding: 4rem;">
                    <i data-lucide="book-open" style="width: 64px; height: 64px; color: var(--text-dim); margin-bottom: 1rem;"></i>
                    <h2>No lessons available for this course yet.</h2>
                </div>
            <?php endif; ?>
        </main>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
