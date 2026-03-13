<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CodeAcademy | Learn to Code</title>
    <!-- Use relative path or base path -->
    <link rel="stylesheet" href="/proyecto-web/assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>
    <header class="navbar">
        <div class="container">
            <a href="/proyecto-web/" class="logo">Code<span>Academy</span></a>
            <nav class="nav-links" id="nav-menu">
                <a href="/proyecto-web/">Home</a>
                <a href="/proyecto-web/courses.php">Courses</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="/proyecto-web/<?php echo $_SESSION['role']; ?>/dashboard.php">Dashboard</a>
                    <a href="/proyecto-web/auth/logout.php" class="btn-secondary">Logout</a>
                <?php else: ?>
                    <a href="/proyecto-web/auth/login.php">Login</a>
                    <a href="/proyecto-web/auth/register.php" class="btn-primary">Get Started</a>
                <?php endif; ?>
            </nav>
            <button class="mobile-toggle" id="mobile-toggle" aria-label="Toggle Menu">
                <i data-lucide="menu"></i>
            </button>
        </div>
    </header>
