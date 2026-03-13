<?php
require_once '../includes/db.php';
include '../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: /auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$success = '';
$error = '';

// Fetch current user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];

    if (!empty($name) && !empty($email)) {
        try {
            $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
            $stmt->execute([$name, $email, $user_id]);
            
            $_SESSION['name'] = $name; // Update session name
            $success = "Profile updated successfully!";
            
            // Refresh user data
            $user['name'] = $name;
            $user['email'] = $email;
        } catch (PDOException $e) {
            $error = "Email already in use by another account.";
        }
    } else {
        $error = "Name and Email are required!";
    }
}
?>

<div class="container">
    <div class="sidebar-mobile-header" id="sidebar-toggle">
        <span>Account Menu</span>
        <i data-lucide="chevron-down"></i>
    </div>
    <div class="dashboard-grid">
        <aside class="sidebar">
            <div class="sidebar-user">
                <h3>Account Settings</h3>
            </div>
            <nav class="sidebar-nav">
                <a href="dashboard.php">My Learning</a>
                <a href="/courses.php">Explore More</a>
                <a href="profile.php" class="active">Profile Settings</a>
            </nav>
        </aside>

        <main class="dashboard-main">
            <div style="max-width: 500px; background: var(--card-bg); padding: 3rem; border-radius: 16px; border: 1px solid rgba(255,255,255,0.05)">
                <h2 style="margin-bottom: 2rem;">Update Profile</h2>

                <?php if ($success): ?>
                    <div class="alert" style="background: rgba(34, 197, 94, 0.1); color: #22c55e; border: 1px solid #22c55e; margin-bottom: 1.5rem; padding: 1rem; border-radius: 8px;">
                        <?php echo $success; ?>
                    </div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert alert-error" style="margin-bottom: 1.5rem;"><?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>
                    <button type="submit" class="btn-primary" style="width: 100%; margin-top: 1rem;">Save Changes</button>
                </form>

                <hr style="margin: 2.5rem 0; border: 0; border-top: 1px solid rgba(255,255,255,0.1)">
                
                <h3 style="margin-bottom: 1rem; color: var(--text-dim);">Security</h3>
                <p style="font-size: 0.9rem; color: var(--text-dim); margin-bottom: 1.5rem;">To change your password, please contact support or use the reset link in your email.</p>
            </div>
        </main>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
