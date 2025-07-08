<?php
require_once 'db_connect.php';

header('Content-Type: application/json');

try {
    $factory = isset($_GET['factory']) ? $_GET['factory'] : 'head_office';
    
    // Validate factory input
    $validFactories = ['head_office', 'agl', 'ajl', 'abm', 'pwpl', 'all'];
    if (!in_array($factory, $validFactories)) {
        throw new Exception('Invalid factory specified');
    }
    
    if ($factory === 'all') {
        // Query all factories
        $query = "(
            SELECT sh.*, 'head_office' as source_table 
            FROM service_history sh
            LEFT JOIN assets a ON sh.asset_id = a.asset_id
          )";
        
        // Add unions for each factory table
        $factories = ['agl', 'ajl', 'abm', 'pwpl'];
        foreach ($factories as $f) {
            $query .= " UNION ALL (
                SELECT sh.*, '$f' as source_table 
                FROM service_history_$f sh
                LEFT JOIN assets_$f a ON sh.asset_id = a.asset_id
              )";
        }
        
        $query .= " ORDER BY service_date DESC";
        $stmt = $pdo->query($query);
    } else {
        // Query specific factory
        $table = $factory === 'head_office' ? 'service_history' : "service_history_$factory";
        $stmt = $pdo->query("SELECT * FROM $table ORDER BY service_date DESC");
    }
    
    $history = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($history);
    
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}