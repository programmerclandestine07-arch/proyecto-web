<?php
require_once '../includes/init.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /proyecto-web/auth/login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: manage_courses.php");
    exit;
}

$course_id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM courses WHERE id = ?");
$stmt->execute([$course_id]);
$course = $stmt->fetch();

if (!$course) {
    header("Location: manage_courses.php");
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $category = $_POST['category'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $thumbnail = $course['thumbnail'];

    // Image Upload Handling
    if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] == 0) {
        $upload_dir = '../assets/img/uploads/';
        $file_ext = pathinfo($_FILES['thumbnail']['name'], PATHINFO_EXTENSION);
        $file_name = time() . '_' . uniqid() . '.' . $file_ext;
        
        if (move_uploaded_file($_FILES['thumbnail']['tmp_name'], $upload_dir . $file_name)) {
            $thumbnail = $file_name;
        }
    }

    if (!empty($title) && !empty($description) && !empty($price)) {
        $stmt = $pdo->prepare("UPDATE courses SET title = ?, category = ?, description = ?, price = ?, thumbnail = ? WHERE id = ?");
        $stmt->execute([$title, $category, $description, $price, $thumbnail, $course_id]);
        $success = "Course updated successfully!";
        header("Refresh: 2; url=manage_courses.php");
    } else {
        $error = "All fields are required!";
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
            <div style="max-width: 600px; background: var(--card-bg); padding: 3rem; border-radius: 16px;">
                <h2 style="margin-bottom: 2rem;">Edit Course</h2>
                
                <?php if ($error): ?>
                    <div class="alert alert-error"><?php echo $error; ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert" style="background: rgba(34, 197, 94, 0.1); color: #22c55e; border: 1px solid #22c55e;"><?php echo $success; ?></div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Course Title</label>
                        <input type="text" name="title" value="<?php echo htmlspecialchars($course['title']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Category</label>
                        <select name="category" style="width: 100%; padding: 0.8rem; border-radius: 8px; background: #0f172a; color: white; border: 1px solid rgba(255,255,255,0.1)">
                            <?php 
                            $categories = ['Web Development', 'Data Science', 'Mobile Apps', 'AI & Machine Learning', 'Programming Basics', 'Other'];
                            foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat; ?>" <?php echo $course['category'] == $cat ? 'selected' : ''; ?>><?php echo $cat; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Thumbnail Image (Leave blank to keep current)</label>
                        <input type="file" name="thumbnail" accept="image/*" style="padding: 0.5rem; background: var(--bg-color); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; width: 100%;">
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" rows="5" style="width: 100%; padding: 0.8rem; border-radius: 8px; background: #0f172a; color: white; border: 1px solid rgba(255,255,255,0.1)"><?php echo htmlspecialchars($course['description']); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Price ($)</label>
                        <input type="number" step="0.01" name="price" value="<?php echo htmlspecialchars($course['price']); ?>" required>
                    </div>
                    <button type="submit" class="btn-primary" style="width: 100%">Update Course</button>
                    <a href="manage_courses.php" style="display: block; text-align: center; margin-top: 1rem; color: var(--text-dim); text-decoration: none;">Cancel</a>
                </form>
            </div>
        </main>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
