<?php
require_once '../includes/init.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /proyecto-web/auth/login.php");
    exit;
}

// Fetch all students
$stmt = $pdo->query("SELECT * FROM users WHERE role = 'student' ORDER BY created_at DESC");
$students = $stmt->fetchAll();

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
                <a href="manage_courses.php">Manage Courses</a>
                <a href="manage_students.php" class="active">Manage Students</a>
            </nav>
        </aside>

        <main class="dashboard-main">
            <h2>Manage Students</h2>
            <p style="color: var(--text-dim); margin-bottom: 2rem;">Overview of all registered students and their progress.</p>

            <div style="background: var(--card-bg); border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05)">
                <table style="width: 100%; border-collapse: collapse; text-align: left;">
                    <thead>
                        <tr style="background: rgba(255,255,255,0.02); border-bottom: 1px solid rgba(255,255,255,0.1)">
                            <th style="padding: 1rem;">Student Name</th>
                            <th style="padding: 1rem;">Email</th>
                            <th style="padding: 1rem;">Joined At</th>
                            <th style="padding: 1rem;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($students)): ?>
                            <tr><td colspan="4" style="padding: 2rem; text-align: center; color: var(--text-dim);">No students found.</td></tr>
                        <?php else: ?>
                            <?php foreach ($students as $student): ?>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.05)">
                                <td style="padding: 1rem;"><?php echo htmlspecialchars($student['name']); ?></td>
                                <td style="padding: 1rem; color: var(--text-dim)"><?php echo htmlspecialchars($student['email']); ?></td>
                                <td style="padding: 1rem; color: var(--text-dim)"><?php echo date('M d, Y', strtotime($student['created_at'])); ?></td>
                                <td style="padding: 1rem;">
                                    <a href="view_student.php?id=<?php echo $student['id']; ?>" style="color: var(--primary); text-decoration: none;">View Progress</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
