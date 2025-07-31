<?php
header("Content-Type: application/json");
require_once 'db_functions.php';

try {
    if (isset($_GET['asset_id'])) {
        $assetId = $_GET['asset_id'];
        $stmt = $pdo->prepare("SELECT * FROM service_history_abm WHERE asset_id = ? ORDER BY service_date DESC");
        $stmt->execute([$assetId]);
        $history = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $history = getAllServiceHistory();
    }
    
    echo json_encode($history);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: '. $e->getMessage()]);
}
?>