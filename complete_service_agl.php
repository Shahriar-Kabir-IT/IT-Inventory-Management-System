<?php
require_once 'db_connect.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

try {
    // Start transaction
    $pdo->beginTransaction();
    
    // Update asset status to ACTIVE
    $stmt = $pdo->prepare("UPDATE assets_agl SET 
        status = 'ACTIVE',
        last_maintenance = ?
        WHERE asset_id = ?");
    
    $stmt->execute([
        date('Y-m-d'),
        $data['asset_id']
    ]);
    
    // Update service history record
    $stmt = $pdo->prepare("UPDATE service_history_agl SET 
        completion_date = ?,
        status = 'COMPLETED'
        WHERE asset_id = ? AND status = 'PENDING'");
    
    $stmt->execute([
        date('Y-m-d'),
        $data['asset_id']
    ]);
    
    $pdo->commit();
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>