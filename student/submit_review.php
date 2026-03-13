<?php
require_once '../includes/db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    die("Unauthorized");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['course_id'])) {
    $user_id = $_SESSION['user_id'];
    $course_id = $_POST['course_id'];
    $rating = (int)$_POST['rating'];
    $comment = $_POST['comment'];

    // Validate rating
    if ($rating < 1 || $rating > 5) {
        die("Invalid rating");
    }

    // Check if review already exists
    $check = $pdo->prepare("SELECT id FROM reviews WHERE user_id = ? AND course_id = ?");
    $check->execute([$user_id, $course_id]);
    $existing = $check->fetch();

    if ($existing) {
        // Update
        $stmt = $pdo->prepare("UPDATE reviews SET rating = ?, comment = ? WHERE user_id = ? AND course_id = ?");
        $stmt->execute([$rating, $comment, $user_id, $course_id]);
    } else {
        // Insert
        $stmt = $pdo->prepare("INSERT INTO reviews (user_id, course_id, rating, comment) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user_id, $course_id, $rating, $comment]);
    }

    header("Location: view_course.php?id=$course_id&reviewed=1");
    exit;
}
?>
