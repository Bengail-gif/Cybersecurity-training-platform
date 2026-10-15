<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Testing database connection...<br>";

require_once '../config/db.php';

echo "Database connected successfully!<br>";
echo "PDO variable exists: " . (isset($pdo) ? 'YES' : 'NO') . "<br>";

// Test query
try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM admins");
    $count = $stmt->fetchColumn();
    echo "Number of admins in database: $count<br>";
} catch (PDOException $e) {
    echo "Query failed: " . $e->getMessage();
}
?>