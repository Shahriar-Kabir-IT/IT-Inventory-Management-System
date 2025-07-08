<?php
require_once 'db_connect.php';

header('Content-Type: application/json');

try {
    $factory = isset($_GET['factory']) ? $_GET['factory'] : 'head_office';
    
    // Map factory names to their respective tables
    $tableMap = [
        'head_office' => 'deleted_assets',
        'agl' => 'deleted_assets_agl',
        'ajl' => 'deleted_assets_ajl',
        'abm' => 'deleted_assets_abm',
        'pwpl' => 'deleted_assets_pwpl'
    ];
    
    if (!array_key_exists($factory, $tableMap)) {
        throw new Exception("Invalid factory specified");
    }
    
    $tableName = $tableMap[$factory];
    
    $stmt = $pdo->prepare("SELECT 
        original_asset_id as asset_id,
        asset_name,
        category,
        brand,
        model,
        location,
        assigned_to,
        purchase_date,
        removal_reason,
        removal_notes,
        removed_by,
        removal_date
    FROM $tableName ORDER BY removal_date DESC");
    
    $stmt->execute();
    $deletedAssets = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($deletedAssets);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}