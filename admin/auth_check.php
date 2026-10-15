<?php
// admin/auth_check.php
// Security middleware - must be included at the top of every admin page

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    // Not logged in - redirect to login page
    header("Location: login.php");
    exit;
}

// Optional: Session timeout (30 minutes of inactivity)
$timeout_duration = 1800; // 30 minutes in seconds

if (isset($_SESSION['LAST_ACTIVITY']) && 
    (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
    // Session expired
    session_unset();
    session_destroy();
    header("Location: login.php?expired=1");
    exit;
}

// Update last activity time
$_SESSION['LAST_ACTIVITY'] = time();

// Make admin info available to all pages
$admin_id = $_SESSION['admin_id'];
$admin_name = $_SESSION['admin_name'] ?? 'Admin';
$admin_email = $_SESSION['admin_email'] ?? '';
?>