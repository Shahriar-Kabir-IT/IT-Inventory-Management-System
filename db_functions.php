<?php
// db_functions.php
require_once 'db_connect.php';

function getDBConnection() {
    global $pdo;
    return $pdo;
}

function getAllServiceHistory() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM service_history ORDER BY service_date DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>