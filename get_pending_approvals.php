<?php
require_once 'db_connect.php';
require_once 'approval_functions.php';

header('Content-Type: application/json');

try {
    $approvals = getPendingApprovals();
    echo json_encode($approvals);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>