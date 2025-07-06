<?php
require_once 'db_connect.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

try {
    // Start transaction
    $pdo->beginTransaction();
    
    // Update asset status
    $stmt = $pdo->prepare("UPDATE assets_agl SET 
        status = ?, 
        last_maintenance = ?
        WHERE asset_id = ?");
    
    $stmt->execute([
        $data['status'],
        $data['last_maintenance'],
        $data['asset_id']
    ]);
    
    // If sending for service, add to service history
    if ($data['status'] === 'MAINTENANCE') {
        $stmt = $pdo->prepare("INSERT INTO service_history_agl (
            asset_id, service_date, service_type, service_notes, service_by, status
        ) VALUES (?, ?, ?, ?, ?, ?)");
        
        $stmt->execute([
            $data['asset_id'],
            date('Y-m-d'),
            $data['service_type'],
            $data['service_notes'],
            $data['service_by'] ?? 'Technician',
            'PENDING'
        ]);
    }
    
    $pdo->commit();
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>