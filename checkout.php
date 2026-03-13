<?php
require_once 'includes/init.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: /proyecto-web/auth/login.php");
    exit;
}

if (!isset($_GET['course_id'])) {
    header("Location: courses.php");
    exit;
}

$course_id = $_GET['course_id'];
$user_id = $_SESSION['user_id'];

// Get course details
$stmt = $pdo->prepare("SELECT * FROM courses WHERE id = ?");
$stmt->execute([$course_id]);
$course = $stmt->fetch();

if (!$course) {
    header("Location: courses.php");
    exit;
}

// Check if already enrolled
$check = $pdo->prepare("SELECT * FROM enrollments WHERE user_id = ? AND course_id = ?");
$check->execute([$user_id, $course_id]);
if ($check->fetch()) {
    header("Location: /proyecto-web/student/dashboard.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Simulated Payment Logic
    $card_number = $_POST['card_number'];
    
    if (strlen($card_number) < 16) {
        $error = "Invalid card details. Please try again.";
    } else {
        // Payment "Successful" - Enroll student
        $stmt = $pdo->prepare("INSERT INTO enrollments (user_id, course_id) VALUES (?, ?)");
        $stmt->execute([$user_id, $course_id]);
        header("Location: /proyecto-web/student/dashboard.php?payment_success=1");
        exit;
    }
}

include 'includes/header.php';
?>

<div class="container" style="padding: 80px 0;">
    <div style="max-width: 900px; margin: 0 auto; display: grid; grid-template-columns: 1fr 350px; gap: 4rem;">
        <div>
            <h2 style="margin-bottom: 2rem;">Checkout</h2>
            
            <div style="background: var(--card-bg); padding: 2rem; border-radius: 16px; border: 1px solid rgba(255,255,255,0.05); margin-bottom: 2rem;">
                <h3 style="margin-bottom: 1.5rem;">Payment Method</h3>
                
                <?php if ($error): ?>
                    <div class="alert alert-error" style="margin-bottom: 1.5rem;"><?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label>Cardholder Name</label>
                        <input type="text" placeholder="John Doe" required>
                    </div>
                    <div class="form-group">
                        <label>Card Number</label>
                        <input type="text" name="card_number" placeholder="0000 0000 0000 0000" maxlength="16" required>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label>Expiry Date</label>
                            <input type="text" placeholder="MM/YY" required>
                        </div>
                        <div class="form-group">
                            <label>CVC</label>
                            <input type="text" placeholder="123" maxlength="3" required>
                        </div>
                    </div>
                    <button type="submit" class="btn-primary" style="width: 100%; margin-top: 1rem; font-size: 1.1rem; padding: 1rem;">
                        Pay $<?php echo number_format($course['price'], 2); ?>
                    </button>
                </form>
                <p style="text-align: center; color: var(--text-dim); font-size: 0.85rem; margin-top: 1.5rem;">
                    <i data-lucide="shield-check" style="width: 14px; height: 14px; vertical-align: middle;"></i> Secure Encrypted Payment
                </p>
            </div>
        </div>

        <aside>
            <h3 style="margin-bottom: 2rem;">Order Summary</h3>
            <div style="background: var(--card-bg); padding: 2rem; border-radius: 16px; border: 1px solid rgba(255,255,255,0.05);">
                <div style="display: flex; gap: 1rem; margin-bottom: 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 1.5rem;">
                    <?php 
                    $thumbnail = !empty($course['thumbnail']) ? '/assets/img/uploads/' . $course['thumbnail'] : 'https://images.unsplash.com/photo-1498050108023-c5249f4df085?auto=format&fit=crop&w=600&q=80';
                    ?>
                    <img src="<?php echo $thumbnail; ?>" style="width: 80px; height: 60px; object-fit: cover; border-radius: 8px;">
                    <div>
                        <h4 style="font-size: 0.95rem;"><?php echo htmlspecialchars($course['title']); ?></h4>
                        <p style="color: var(--primary); font-weight: 700;">$<?php echo number_format($course['price'], 2); ?></p>
                    </div>
                </div>
                
                <div style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
                    <span style="color: var(--text-dim);">Course Price</span>
                    <span>$<?php echo number_format($course['price'], 2); ?></span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 1.5rem;">
                    <span style="color: var(--text-dim);">Platform Fee</span>
                    <span style="color: #22c55e;">FREE</span>
                </div>
                <div style="display: flex; justify-content: space-between; font-weight: 800; font-size: 1.25rem; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 1.5rem;">
                    <span>Total</span>
                    <span>$<?php echo number_format($course['price'], 2); ?></span>
                </div>
            </div>
        </aside>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
