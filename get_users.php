<?php
header("Content-Type: application/json");
require_once 'db_connect.php';

try {
    $stmt = $pdo->query("SELECT id, name, username, employee_id, user_type, factory FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($users);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>