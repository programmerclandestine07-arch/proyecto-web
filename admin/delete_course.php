<?php
require_once '../includes/init.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /proyecto-web/auth/login.php");
    exit;
}

if (isset($_GET['id'])) {
    $course_id = $_GET['id'];

    // Get thumbnail filename before deleting
    $stmt = $pdo->prepare("SELECT thumbnail FROM courses WHERE id = ?");
    $stmt->execute([$course_id]);
    $course = $stmt->fetch();

    if ($course && !empty($course['thumbnail']) && $course['thumbnail'] !== 'default-course.jpg') {
        $file_path = '../assets/img/uploads/' . $course['thumbnail'];
        if (file_exists($file_path)) {
            unlink($file_path); // Delete the file from server
        }
    }

    $stmt = $pdo->prepare("DELETE FROM courses WHERE id = ?");
    $stmt->execute([$course_id]);
}

header("Location: manage_courses.php");
exit;
?>
