<?php
header("Content-Type: application/json");
require_once 'db_connect.php';

$data = json_decode(file_get_contents('php://input'), true);

try {
    // Validate input
    if (empty($data['name']) || empty($data['username']) || empty($data['password']) || 
        empty($data['employee_id']) || empty($data['user_type']) || empty($data['factory'])) {
        throw new Exception("All fields are required");
    }

    // Hash password
    $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);

    $stmt = $pdo->prepare("INSERT INTO users (name, username, password, employee_id, user_type, factory) 
                          VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $data['name'],
        $data['username'],
        $hashedPassword,
        $data['employee_id'],
        $data['user_type'],
        $data['factory']
    ]);

    echo json_encode(['success' => true, 'message' => 'User created successfully']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>