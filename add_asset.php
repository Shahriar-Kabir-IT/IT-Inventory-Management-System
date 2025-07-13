<?php
require_once 'db_connect.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

try {
    // Only generate HOIT-XXX for Head Office assets
    if ($data['location'] === 'head office') {
        // Get the highest existing HOIT asset ID
        $stmt = $pdo->prepare("SELECT MAX(asset_id) as max_id FROM assets WHERE asset_id LIKE 'HOIT-%'");
        $stmt->execute();
        $result = $stmt->fetch();
        
        $maxNumber = 0;
        if ($result && $result['max_id']) {
            $parts = explode('-', $result['max_id']);
            $maxNumber = (int)end($parts);
        }
        
        // Generate new HOIT asset ID (e.g., HOIT-001, HOIT-002, etc.)
        $newNumber = $maxNumber + 1;
        $asset_id = 'HOIT-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    } else {
        // For non-Head Office assets, use a different format (optional)
        // Example: AGL-IT-001, AJL-IT-001, etc.
        $asset_id = strtoupper($data['location']) . '-IT-' . date('YmdHis');
    }
    
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