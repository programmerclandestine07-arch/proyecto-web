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
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $video_url = $_POST['video_url'];
    $order_index = $_POST['order_index'];

    if (!empty($title)) {
        $stmt = $pdo->prepare("INSERT INTO lessons (course_id, title, content, video_url, order_index) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$course_id, $title, $content, $video_url, $order_index]);
        $success = "Lesson added successfully!";
        header("Refresh: 2; url=manage_lessons.php?course_id=$course_id");
    } else {
        $error = "Lesson title is required!";
    }
}

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
            <div style="max-width: 800px; background: var(--card-bg); padding: 3rem; border-radius: 16px;">
                <h2 style="margin-bottom: 2rem;">Add New Lesson</h2>
                
                <?php if ($error): ?>
                    <div class="alert alert-error"><?php echo $error; ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert" style="background: rgba(34, 197, 94, 0.1); color: #22c55e; border: 1px solid #22c55e;"><?php echo $success; ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label>Lesson Title</label>
                        <input type="text" name="title" required>
                    </div>
                    <div class="form-group">
                        <label>Order Index (0, 1, 2...)</label>
                        <input type="number" name="order_index" value="0">
                    </div>
                    <div class="form-group">
                        <label>Video URL (YouTube/Vimeo/etc.)</label>
                        <input type="text" name="video_url" placeholder="https://youtube.com/...">
                    </div>
                    <div class="form-group">
                        <label>Lesson Content (Text/HTML)</label>
                        <textarea name="content" rows="10" style="width: 100%; padding: 0.8rem; border-radius: 8px; background: #0f172a; color: white; border: 1px solid rgba(255,255,255,0.1)"></textarea>
                    </div>
                    <button type="submit" class="btn-primary" style="width: 100%">Create Lesson</button>
                    <a href="manage_lessons.php?course_id=<?php echo $course_id; ?>" style="display: block; text-align: center; margin-top: 1rem; color: var(--text-dim); text-decoration: none;">Cancel</a>
                </form>
            </div>
        </main>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
