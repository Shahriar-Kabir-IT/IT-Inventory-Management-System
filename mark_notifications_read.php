<?php
require_once 'db_connect.php';
require_once 'approval_functions.php';

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

$success = markNotificationsAsRead($_SESSION['user_id']);
echo json_encode(['success' => $success]);
?>