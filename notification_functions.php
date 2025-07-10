<?php
require_once 'db_connect.php';

function createNotification($userId, $requestId, $message) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("INSERT INTO notifications (user_id, request_id, message) VALUES (?, ?, ?)");
        $stmt->execute([$userId, $requestId, $message]);
        return $pdo->lastInsertId();
    } catch (PDOException $e) {
        error_log("Notification creation failed: " . $e->getMessage());
        return false;
    }
}

function getUnreadNotificationCount($userId) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND is_read = FALSE");
        $stmt->execute([$userId]);
        return $stmt->fetch()['count'];
    } catch (PDOException $e) {
        error_log("Notification count failed: " . $e->getMessage());
        return 0;
    }
}

function getNotifications($userId, $limit = 10) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("
            SELECT n.*, r.action_type, r.asset_id, r.status as request_status 
            FROM notifications n
            JOIN approval_requests r ON n.request_id = r.id
            WHERE n.user_id = ?
            ORDER BY n.created_at DESC
            LIMIT ?
        ");
        $stmt->execute([$userId, $limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Fetch notifications failed: " . $e->getMessage());
        return [];
    }
}

function markNotificationAsRead($notificationId, $userId) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("UPDATE notifications SET is_read = TRUE WHERE id = ? AND user_id = ?");
        return $stmt->execute([$notificationId, $userId]);
    } catch (PDOException $e) {
        error_log("Mark notification read failed: " . $e->getMessage());
        return false;
    }
}

function markAllNotificationsAsRead($userId) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("UPDATE notifications SET is_read = TRUE WHERE user_id = ?");
        return $stmt->execute([$userId]);
    } catch (PDOException $e) {
        error_log("Mark all notifications read failed: " . $e->getMessage());
        return false;
    }
}
?>