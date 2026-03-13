<?php
require_once '../includes/init.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    die("Unauthorized");
}

if (isset($_GET['lesson_id']) && isset($_GET['course_id'])) {
    $lesson_id = $_GET['lesson_id'];
    $course_id = $_GET['course_id'];
    $user_id = $_SESSION['user_id'];

    // Check if already completed
    $check = $pdo->prepare("SELECT * FROM lesson_completions WHERE user_id = ? AND lesson_id = ?");
    $check->execute([$user_id, $lesson_id]);
    $is_completed = $check->fetch();

    if ($is_completed) {
        // Mark as incomplete
        $stmt = $pdo->prepare("DELETE FROM lesson_completions WHERE user_id = ? AND lesson_id = ?");
        $stmt->execute([$user_id, $lesson_id]);
    } else {
        // Mark as complete
        $stmt = $pdo->prepare("INSERT INTO lesson_completions (user_id, lesson_id) VALUES (?, ?)");
        $stmt->execute([$user_id, $lesson_id]);
    }

    header("Location: view_course.php?id=$course_id&lesson_id=$lesson_id");
    exit;
}
?>
