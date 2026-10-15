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

$score = intval($data['score'] ?? 0);
$total_questions = intval($data['total_questions'] ?? 0);
$percentage = floatval($data['percentage'] ?? 0);

if ($total_questions <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid quiz data']);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO quiz_scores (student_id, score, total_questions, percentage) VALUES (?, ?, ?, ?)");
    $stmt->execute([$_SESSION['student_id'], $score, $total_questions, $percentage]);

    echo json_encode(['success' => true, 'message' => 'Quiz score saved']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to save quiz score: ' . $e->getMessage()]);
}
?>