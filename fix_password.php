<?php
// fix_password.php - Run this ONCE then delete it!

require_once 'config/db.php';

// Generate correct hash for "Admin@123"
$password = "Admin@123";
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "<h2>Password Hash Generated!</h2>";
echo "<p><strong>Password:</strong> $password</p>";
echo "<p><strong>Hash:</strong></p>";
echo "<textarea style='width: 100%; height: 80px;'>$hash</textarea>";
echo "<p><strong>SQL to run in phpMyAdmin:</strong></p>";
echo "<textarea style='width: 100%; height: 120px;'>UPDATE admins SET password = '$hash' WHERE email = 'admin@faithuniversity.edu';</textarea>";
echo "<p style='color: red; font-weight: bold;'>⚠️ DELETE THIS FILE AFTER USE!</p>";
?>