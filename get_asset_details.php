<?php
header("Content-Type: application/json");
require_once 'db_functions.php';

try {
    $assetId = $_GET['asset_id'] ?? null;
    $factory = $_GET['factory'] ?? 'head_office';
    
    if (!$assetId) {
        throw new Exception("Asset ID is required");
    }
    
    // Map factory codes to their respective tables
    $factoryTables = [
        'head_office' => 'it_assets',
        'agl' => 'it_assets_agl',
        'ajl' => 'it_assets_ajl',
        'abm' => 'it_assets_abm',
        'pwpl' => 'it_assets_pwpl'
    ];
    
    $table = $factoryTables[$factory] ?? 'it_assets';
    
    $query = "SELECT asset_name FROM $table WHERE asset_id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$assetId]);
    $asset = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$asset) {
        echo json_encode(['error' => 'Asset not found']);
        exit;
    }
    
    echo json_encode($asset);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: '. $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}