<?php
require_once 'db.php';
session_start();

if (!isset($_SESSION['student_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT m.module_id, m.module_title, sp.completed_at
        FROM student_progress sp
        JOIN modules m ON sp.module_id = m.module_id
        WHERE sp.student_id = ?
        ORDER BY sp.completed_at DESC
    ");
    $stmt->execute([$_SESSION['student_id']]);
    $completed_modules = $stmt->fetchAll();

    $stmt = $pdo->prepare("
        SELECT score, total_questions, percentage, taken_at
        FROM quiz_scores
        WHERE student_id = ?
        ORDER BY taken_at DESC
    ");
    $stmt->execute([$_SESSION['student_id']]);
    $quiz_scores = $stmt->fetchAll();

    $total_modules = $pdo->query("SELECT COUNT(*) FROM modules")->fetchColumn();
    $completed_count = count($completed_modules);
    $progress_percentage = $total_modules > 0 ? round(($completed_count / $total_modules) * 100) : 0;

    echo json_encode([
        'success' => true,
        'progress' => [
            'completed_modules' => $completed_modules,
            'quiz_scores' => $quiz_scores,
            'overall_percentage' => $progress_percentage,
            'completed_count' => $completed_count,
            'total_modules' => $total_modules
        ]
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch progress: ' . $e->getMessage()]);
}
?>