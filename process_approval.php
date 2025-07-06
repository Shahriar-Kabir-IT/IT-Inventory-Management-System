<?php
require_once 'db_connect.php';
require_once 'approval_functions.php';

header('Content-Type: application/json');

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['approval_id'], $data['action'], $data['approver'])) {
        throw new Exception('Missing required fields');
    }
    
    $result = processApproval($data['approval_id'], $data['approver'], $data['action']);
    echo json_encode($result);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>