<?php
require_once '../config/db.php';

try {
    $stmt = $pdo->query("SELECT module_id, module_title, module_description FROM modules ORDER BY module_id");
    $modules = $stmt->fetchAll();

    echo json_encode(['success' => true, 'modules' => $modules]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch modules: ' . $e->getMessage()]);
}
?>