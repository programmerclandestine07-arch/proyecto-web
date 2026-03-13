<?php
require_once '../includes/init.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['course_id'])) {
    header("Location: /auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$course_id = $_GET['course_id'];

// Verify 100% completion
$stmt = $pdo->prepare("
    SELECT 
        (SELECT COUNT(*) FROM lessons WHERE course_id = ?) as total,
        (SELECT COUNT(*) FROM lesson_completions lc JOIN lessons l ON lc.lesson_id = l.id WHERE lc.user_id = ? AND l.course_id = ?) as completed
");
$stmt->execute([$course_id, $user_id, $course_id]);
$progress = $stmt->fetch();

if ($progress['total'] == 0 || $progress['completed'] < $progress['total']) {
    die("Course not completed yet.");
}

// Get student and course details
$stmt = $pdo->prepare("SELECT name FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$student_name = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT title FROM courses WHERE id = ?");
$stmt->execute([$course_id]);
$course_title = $stmt->fetchColumn();

// Check or generate certificate hash
$stmt = $pdo->prepare("SELECT certificate_hash FROM certificates WHERE user_id = ? AND course_id = ?");
$stmt->execute([$user_id, $course_id]);
$certificate_hash = $stmt->fetchColumn();

if (!$certificate_hash) {
    $certificate_hash = bin2hex(random_bytes(16));
    $stmt = $pdo->prepare("INSERT INTO certificates (user_id, course_id, certificate_hash) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $course_id, $certificate_hash]);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Certificate - <?php echo htmlspecialchars($course_title); ?></title>
    <style>
        body { background: #f1f5f9; font-family: 'Inter', sans-serif; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; }
        .certificate { width: 850px; height: 600px; background: white; border: 20px solid #0f172a; padding: 50px; position: relative; box-shadow: 0 40px 60px rgba(0,0,0,0.1); border-radius: 4px; box-sizing: border-box; }
        .certificate::before { content: ''; position: absolute; top: 0; left: 0; right: 0; bottom: 0; border: 5px solid #38bdf8; margin: 5px; pointer-events: none; }
        .logo { font-size: 24px; font-weight: 800; color: #0f172a; text-align: center; margin-bottom: 50px; }
        .logo span { color: #38bdf8; }
        h1 { font-size: 48px; text-transform: uppercase; margin-bottom: 20px; color: #0f172a; text-align: center; }
        .sub { font-size: 18px; color: #64748b; margin-bottom: 30px; text-align: center; }
        .name { font-size: 42px; color: #0f172a; font-weight: 800; text-align: center; border-bottom: 2px solid #e2e8f0; width: 80%; margin: 20px auto; padding-bottom: 10px; font-family: 'Georgia', serif; font-style: italic; }
        .course { font-size: 24px; color: #0f172a; font-weight: 600; text-align: center; margin-top: 30px; }
        .footer { position: absolute; bottom: 50px; left: 50px; right: 50px; display: flex; justify-content: space-between; align-items: flex-end; }
        .signature { text-align: center; border-top: 1px solid #e2e8f0; width: 200px; padding-top: 10px; font-weight: 600; color: #64748b; }
        .id { font-size: 12px; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; }
        @media print { .btn-print { display: none; } body { background: white; } }
        .btn-print { position: fixed; top: 20px; right: 20px; background: #0f172a; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: 600; }
    </style>
</head>
<body>
    <button class="btn-print" onclick="window.print()">Print Certificate</button>
    <div class="certificate">
        <div class="logo">Code<span>Academy</span></div>
        <p class="sub">This is to certify that</p>
        <div class="name"><?php echo htmlspecialchars($student_name); ?></div>
        <p class="sub">has successfully completed the course</p>
        <div class="course"><?php echo htmlspecialchars($course_title); ?></div>
        
        <div class="footer">
            <div>
                <div class="id">Certificate ID: <?php echo $certificate_hash; ?></div>
                <div class="id">Issued on: <?php echo date('F d, Y'); ?></div>
            </div>
            <div class="signature">Academy Director</div>
        </div>
    </div>
</body>
</html>
