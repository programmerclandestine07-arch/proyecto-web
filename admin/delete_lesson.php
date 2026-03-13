<?php
require_once '../includes/init.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /proyecto-web/auth/login.php");
    exit;
}

if (isset($_GET['id']) && isset($_GET['course_id'])) {
    $lesson_id = $_GET['id'];
    $course_id = $_GET['course_id'];
    
    $stmt = $pdo->prepare("DELETE FROM lessons WHERE id = ?");
    $stmt->execute([$lesson_id]);
    
    header("Location: manage_lessons.php?course_id=$course_id");
    exit;
}

header("Location: manage_courses.php");
exit;
?>
