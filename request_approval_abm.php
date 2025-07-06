<?php
require_once 'db_connect.php';
require_once 'approval_functions.php';

header('Content-Type: application/json');

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['action_type'], $data['requested_by'], $data['factory'])) {
        throw new Exception('Missing required fields');
    }
    
    $assetId = $data['asset_id'] ?? null;
    $currentStatus = $data['status'] ?? null;
    
    // Remove asset_id from action details if it exists
    if (isset($data['asset_id'])) {
        unset($data['asset_id']);
    }
    
    $approvalId = createApprovalRequest(
        $assetId,
        $data['action_type'],
        $data['requested_by'],
        $data['factory'],
        $currentStatus,
        json_encode($data)
    );
    
    if ($approvalId) {
        echo json_encode(['success' => true, 'approval_id' => $approvalId]);
    } else {
        throw new Exception('Failed to create approval request');
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>