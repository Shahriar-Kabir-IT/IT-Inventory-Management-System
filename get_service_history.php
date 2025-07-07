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
            $history = array_merge($history, $stmt->fetchAll(PDO::FETCH_ASSOC));
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
    }
    
    echo json_encode($history);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: '. $e->getMessage()]);
}