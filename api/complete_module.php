<?php
require_once 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

if (!isset($_SESSION['student_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$module_id = trim($data['module_id'] ?? '');
if (empty($module_id)) {
    http_response_code(400);
    echo json_encode(['error' => 'Module ID is required']);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT IGNORE INTO student_progress (student_id, module_id) VALUES (?, ?)");
    $stmt->execute([$_SESSION['student_id'], $module_id]);

    echo json_encode(['success' => true, 'message' => 'Module marked as complete']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to save progress: ' . $e->getMessage()]);
}
?>