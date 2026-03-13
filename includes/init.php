<?php
// Initialize session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database connection
require_once __DIR__ . '/db.php';

// Base URL helper (optional but useful)
// define('BASE_URL', '/');
?>
