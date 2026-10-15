<?php
require_once('../config/db.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$student_id = trim($data['student_id'] ?? '');
$password = $data['password'] ?? '';

if (empty($student_id) || empty($password)) {
    http_response_code(400);
    echo json_encode(['error' => 'Student ID and password are required']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT student_id, full_name, password_hash FROM students WHERE student_id = ?");
    $stmt->execute([$student_id]);
    $student = $stmt->fetch();

    if ($student && password_verify($password, $student['password_hash'])) {
        $_SESSION['student_id'] = $student['student_id'];
        $_SESSION['full_name'] = $student['full_name'];

        echo json_encode([
            'success' => true,
            'student' => [
                'id' => $student['student_id'],
                'name' => $student['full_name']
            ]
        ]);
    } else {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid Student ID or password']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Login failed: ' . $e->getMessage()]);
}
?>