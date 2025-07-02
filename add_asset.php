<?php
require_once 'db_connect.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

try {
    // Generate a unique asset ID
    $asset_id = 'IT-' . date('YmdHis');
    
    $stmt = $pdo->prepare("INSERT INTO assets (
        asset_id, asset_name, category, brand, model, serial_number, 
        status, location, assigned_to, department, purchase_date, 
        purchase_price, warranty_expiry, last_maintenance, priority, notes
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->execute([
        $asset_id,
        $data['asset_name'],
        $data['category'],
        $data['brand'],
        $data['model'],
        $data['serial_number'],
        $data['status'] ?? 'ACTIVE',
        $data['location'],
        $data['assigned_to'] ?? 'Unassigned',
        $data['department'],
        $data['purchase_date'],
        $data['purchase_price'],
        $data['warranty_expiry'],
        $data['last_maintenance'] ?? date('Y-m-d'),
        $data['priority'] ?? 'Medium',
        $data['notes'] ?? ''
    ]);
    
    echo json_encode(['success' => true, 'asset_id' => $asset_id]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>