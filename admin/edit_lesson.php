<?php
require_once '../includes/db.php';
include '../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /auth/login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: manage_courses.php");
    exit;
}

$lesson_id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM lessons WHERE id = ?");
$stmt->execute([$lesson_id]);
$lesson = $stmt->fetch();

if (!$lesson) {
    header("Location: manage_courses.php");
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $video_url = $_POST['video_url'];
    $order_index = $_POST['order_index'];

    if (!empty($title)) {
        $stmt = $pdo->prepare("UPDATE lessons SET title = ?, content = ?, video_url = ?, order_index = ? WHERE id = ?");
        $stmt->execute([$title, $content, $video_url, $order_index, $lesson_id]);
        $success = "Lesson updated successfully!";
        header("Refresh: 2; url=manage_lessons.php?course_id=" . $lesson['course_id']);
    } else {
        $error = "Lesson title is required!";
    }
}
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
                <a href="#">Manage Students</a>
            </nav>
        </aside>

        <main class="dashboard-main">
            <div style="max-width: 800px; background: var(--card-bg); padding: 3rem; border-radius: 16px;">
                <h2 style="margin-bottom: 2rem;">Edit Lesson</h2>
                
                <?php if ($error): ?>
                    <div class="alert alert-error"><?php echo $error; ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert" style="background: rgba(34, 197, 94, 0.1); color: #22c55e; border: 1px solid #22c55e;"><?php echo $success; ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label>Lesson Title</label>
                        <input type="text" name="title" value="<?php echo htmlspecialchars($lesson['title']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Order Index</label>
                        <input type="number" name="order_index" value="<?php echo htmlspecialchars($lesson['order_index']); ?>">
                    </div>
                    <div class="form-group">
                        <label>Video URL</label>
                        <input type="text" name="video_url" value="<?php echo htmlspecialchars($lesson['video_url']); ?>">
                    </div>
                    <div class="form-group">
                        <label>Lesson Content</label>
                        <textarea name="content" rows="10" style="width: 100%; padding: 0.8rem; border-radius: 8px; background: #0f172a; color: white; border: 1px solid rgba(255,255,255,0.1)"><?php echo htmlspecialchars($lesson['content']); ?></textarea>
                    </div>
                    <button type="submit" class="btn-primary" style="width: 100%">Update Lesson</button>
                    <a href="manage_lessons.php?course_id=<?php echo $lesson['course_id']; ?>" style="display: block; text-align: center; margin-top: 1rem; color: var(--text-dim); text-decoration: none;">Cancel</a>
                </form>
            </div>
        </main>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
