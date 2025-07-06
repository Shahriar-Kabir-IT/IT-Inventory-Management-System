<?php
require_once 'db_connect.php';

header('Content-Type: application/json');

try {
    $stmt = $pdo->query("SELECT * FROM assets_agl");
    $assets = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($assets);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>