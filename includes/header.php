<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CodeAcademy | Learn to Code</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>
    <header class="navbar">
        <div class="container">
            <a href="/" class="logo">Code<span>Academy</span></a>
            <nav class="nav-links" id="nav-menu">
                <a href="/">Home</a>
                <a href="/courses.php">Courses</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="/<?php echo $_SESSION['role']; ?>/dashboard.php">Dashboard</a>
                    <a href="/auth/logout.php" class="btn-secondary">Logout</a>
                <?php else: ?>
                    <a href="/auth/login.php">Login</a>
                    <a href="/auth/register.php" class="btn-primary">Get Started</a>
                <?php endif; ?>
            </nav>
            <button class="mobile-toggle" id="mobile-toggle" aria-label="Toggle Menu">
                <i data-lucide="menu"></i>
            </button>
        </div>
    </header>
