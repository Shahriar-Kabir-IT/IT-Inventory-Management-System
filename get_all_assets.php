<?php
header("Content-Type: application/json");
require_once 'db_functions.php';

try {
    // Get parameters
    $assetId = $_GET['asset_id'] ?? null;
    $factory = $_GET['factory'] ?? 'all'; // Default to all factories
    
    // Map factory codes to their respective service history tables
    $factoryTables = [
        'head_office' => 'service_history',
        'agl' => 'service_history_agl',
        'ajl' => 'service_history_ajl',
        'abm' => 'service_history_abm',
        'pwpl' => 'service_history_pwpl'
    ];
    
    $history = [];
    
    if ($factory === 'all') {
        // Get history from all factories
        foreach ($factoryTables as $factoryCode => $table) {
            $query = "SELECT *, '$factoryCode' as factory FROM $table";
            $params = [];
            
            if ($assetId) {
                $query .= " WHERE asset_id = ?";
                $params[] = $assetId;
            }
            
            $query .= " ORDER BY service_date DESC";
            $stmt = $pdo->prepare($query);
            $stmt->execute($params);
            $factoryHistory = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Add asset names by joining with asset tables
            foreach ($factoryHistory as &$record) {
                $assetTable = ($factoryCode === 'head_office') ? 'assets' : "assets_$factoryCode";
                $assetStmt = $pdo->prepare("SELECT asset_name FROM $assetTable WHERE asset_id = ?");
                $assetStmt->execute([$record['asset_id']]);
                $asset = $assetStmt->fetch(PDO::FETCH_ASSOC);
                $record['asset_name'] = $asset['asset_name'] ?? 'N/A';
            }
            
            $history = array_merge($history, $factoryHistory);
        }
    } else {
        // Get history from specific factory
        $table = $factoryTables[$factory] ?? 'service_history';
        $query = "SELECT * FROM $table";
        $params = [];
        
        if ($assetId) {
            $query .= " WHERE asset_id = ?";
            $params[] = $assetId;
        }
        
        $query .= " ORDER BY service_date DESC";
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $history = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Add asset names
        $assetTable = ($factory === 'head_office') ? 'assets' : "assets_$factory";
        foreach ($history as &$record) {
            $assetStmt = $pdo->prepare("SELECT asset_name FROM $assetTable WHERE asset_id = ?");
            $assetStmt->execute([$record['asset_id']]);
            $asset = $assetStmt->fetch(PDO::FETCH_ASSOC);
            $record['asset_name'] = $asset['asset_name'] ?? 'N/A';
        }
    }
    
    echo json_encode($history);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: '. $e->getMessage()]);
}