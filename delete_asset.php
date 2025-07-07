<?php
require_once 'db_connect.php';

$data = json_decode(file_get_contents('php://input'), true);
$assetId = $data['asset_id'] ?? null;
$removeReason = $data['remove_reason'] ?? 'Unknown';
$removeNotes = $data['remove_notes'] ?? '';

session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

try {
    $pdo->beginTransaction();

    // 1. First get the asset details to confirm it exists
    $assetStmt = $pdo->prepare("SELECT * FROM assets WHERE asset_id = ?");
    $assetStmt->execute([$assetId]);
    $asset = $assetStmt->fetch(PDO::FETCH_ASSOC);

    if (!$asset) {
        throw new Exception("Asset not found");
    }

    // 2. Archive asset to deleted_assets table
    $archiveStmt = $pdo->prepare("
        INSERT INTO deleted_assets (
            original_asset_id, asset_name, category, brand, model, serial_number, 
            status, location, assigned_to, department, purchase_date, purchase_price, 
            warranty_expiry, last_maintenance, priority, notes, removal_reason, 
            removal_notes, removed_by
        ) 
        VALUES (
            :asset_id, :asset_name, :category, :brand, :model, :serial_number, 
            :status, :location, :assigned_to, :department, :purchase_date, :purchase_price, 
            :warranty_expiry, :last_maintenance, :priority, :notes, :removal_reason, 
            :removal_notes, :removed_by
        )
    ");
    
    $archiveStmt->execute([
        ':asset_id' => $asset['asset_id'],
        ':asset_name' => $asset['asset_name'],
        ':category' => $asset['category'],
        ':brand' => $asset['brand'],
        ':model' => $asset['model'],
        ':serial_number' => $asset['serial_number'],
        ':status' => $asset['status'],
        ':location' => $asset['location'],
        ':assigned_to' => $asset['assigned_to'],
        ':department' => $asset['department'],
        ':purchase_date' => $asset['purchase_date'],
        ':purchase_price' => $asset['purchase_price'],
        ':warranty_expiry' => $asset['warranty_expiry'],
        ':last_maintenance' => $asset['last_maintenance'],
        ':priority' => $asset['priority'],
        ':notes' => $asset['notes'],
        ':removal_reason' => $removeReason,
        ':removal_notes' => $removeNotes,
        ':removed_by' => $_SESSION['username'] // or $_SESSION['user_id'] depending on what you store
    ]);

    // 3. Delete from original assets table
    $deleteStmt = $pdo->prepare("DELETE FROM assets WHERE asset_id = ?");
    $deleteStmt->execute([$assetId]);

    $pdo->commit();
    
    echo json_encode(['success' => true, 'message' => 'Asset deleted and archived successfully']);
} catch (PDOException $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}