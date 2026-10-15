<?php
require_once('../config/db.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$student_id = trim($data['student_id'] ?? '');
$full_name = trim($data['full_name'] ?? '');
$password = $data['password'] ?? '';

if (empty($student_id) || empty($full_name) || empty($password)) {
    http_response_code(400);
    echo json_encode(['error' => 'Student ID, Full Name, and Password are required']);
    exit;
}

if (strlen($password) < 6) {
    http_response_code(400);
    echo json_encode(['error' => 'Password must be at least 6 characters']);
    exit;
}

$password_hash = password_hash($password, PASSWORD_DEFAULT);

try {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM students WHERE student_id = ?");
    $stmt->execute([$student_id]);
    if ($stmt->fetchColumn() > 0) {
        http_response_code(409);
        echo json_encode(['error' => 'Student ID already exists']);
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO students (student_id, full_name, password_hash) VALUES (?, ?, ?)");
    $stmt->execute([$student_id, $full_name, $password_hash]);

    echo json_encode(['success' => true, 'message' => 'Account created successfully']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Registration failed: ' . $e->getMessage()]);
}
?>