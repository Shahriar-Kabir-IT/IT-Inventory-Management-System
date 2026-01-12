<?php
require_once 'db_connect.php';

// Notification functions
function createNotification($userId, $message, $approvalId) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("INSERT INTO notifications 
                              (user_id, message, approval_id, is_read, created_at) 
                              VALUES (?, ?, ?, 0, NOW())");
        $stmt->execute([$userId, $message, $approvalId]);
        return $pdo->lastInsertId();
    } catch (PDOException $e) {
        error_log("Error creating notification: " . $e->getMessage());
        return false;
    }
}

function getUnreadNotifications($userId) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND is_read = 0");
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    } catch (PDOException $e) {
        error_log("Error fetching unread notifications: " . $e->getMessage());
        return 0;
    }
}

function getNotifications($userId) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching notifications: " . $e->getMessage());
        return [];
    }
}

function markNotificationsAsRead($userId) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0");
        $stmt->execute([$userId]);
        return true;
    } catch (PDOException $e) {
        error_log("Error marking notifications as read: " . $e->getMessage());
        return false;
    }
}

// Original functions with notification integration
function createApprovalRequest($assetId, $actionType, $requestedBy, $factory, $currentStatus = null, $actionDetails = null) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("INSERT INTO pending_approvals 
                              (asset_id, action_type, requested_by, factory, current_status, action_details, status) 
                              VALUES (?, ?, ?, ?, ?, ?, 'PENDING')");
        $stmt->execute([$assetId, $actionType, $requestedBy, $factory, $currentStatus, $actionDetails]);
        return $pdo->lastInsertId();
    } catch (PDOException $e) {
        error_log("Error creating approval request: " . $e->getMessage());
        return false;
    }
}

function getPendingApprovals($factory = null) {
    global $pdo;
    
    try {
        if ($factory) {
            $stmt = $pdo->prepare("SELECT * FROM pending_approvals WHERE status = 'PENDING' AND factory = ? ORDER BY request_date DESC");
            $stmt->execute([$factory]);
        } else {
            $stmt = $pdo->query("SELECT * FROM pending_approvals WHERE status = 'PENDING' ORDER BY request_date DESC");
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching pending approvals: " . $e->getMessage());
        return [];
    }
}

function processApproval($approvalId, $approver, $action = 'APPROVE') {
    global $pdo;
    
    try {
        $approval = $pdo->query("SELECT * FROM pending_approvals WHERE id = $approvalId")->fetch(PDO::FETCH_ASSOC);
        
        if (!$approval) {
            return ['success' => false, 'message' => 'Approval request not found'];
        }
        
        // Get user who made the request
        $userStmt = $pdo->prepare("SELECT id FROM users WHERE name = ? LIMIT 1");
        $userStmt->execute([$approval['requested_by']]);
        $user = $userStmt->fetch(PDO::FETCH_ASSOC);
        
        if ($action === 'APPROVE') {
            // Process the approved action
            $actionDetails = json_decode($approval['action_details'], true);
            $result = executeApprovedAction($approval['action_type'], $actionDetails, $approval['asset_id'], $approval['factory']);
            
            if ($result['success']) {
                $stmt = $pdo->prepare("UPDATE pending_approvals 
                                      SET status = 'APPROVED', 
                                          approved_by = ?,
                                          approval_date = NOW()
                                      WHERE id = ?");
                $stmt->execute([$approver, $approvalId]);
                
                // Create notification
                if ($user) {
                    $message = "Your request for " . strtolower($approval['action_type']) . " (Asset ID: {$approval['asset_id']}) has been approved";
                    createNotification($user['id'], $message, $approvalId);
                }
                
                return ['success' => true, 'message' => 'Action approved and processed successfully'];
            } else {
                return $result;
            }
        } else {
            // Reject the request
            $stmt = $pdo->prepare("UPDATE pending_approvals 
                                  SET status = 'REJECTED', 
                                      approved_by = ?,
                                      approval_date = NOW()
                                  WHERE id = ?");
            $stmt->execute([$approver, $approvalId]);
            
            // Create notification
            if ($user) {
                $message = "Your request for " . strtolower($approval['action_type']) . " (Asset ID: {$approval['asset_id']}) has been rejected";
                createNotification($user['id'], $message, $approvalId);
            }
            
            return ['success' => true, 'message' => 'Action rejected'];
        }
    } catch (PDOException $e) {
        error_log("Error processing approval: " . $e->getMessage());
        return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
    }
}

function executeApprovedAction($actionType, $actionDetails, $assetId, $factory) {
    global $pdo;
    
    try {
        // Handle both JSON string and array formats for action_details
        if (is_string($actionDetails)) {
            $actionDetails = json_decode($actionDetails, true);
        }

        // Validate factory
        if (empty($factory)) {
            throw new Exception("Factory is not specified");
        }

        // Determine table names based on factory
        $factory = strtolower($factory);
        if ($factory === 'head office' || $factory === 'head_office') {
            $assetsTable = 'assets';
            $serviceHistoryTable = 'service_history';
            $deletedAssetsTable = 'deleted_assets';
        } else {
            $assetsTable = "assets_" . $factory;
            $serviceHistoryTable = "service_history_" . $factory;
            $deletedAssetsTable = "deleted_assets_" . $factory;
        }
        
        // Validate tables exist
        $tables = $pdo->query("SHOW TABLES LIKE '$assetsTable'")->fetchAll();
        if (empty($tables)) {
            throw new Exception("Invalid factory or tables don't exist for factory: $factory");
        }

        switch ($actionType) {
            case 'ADD':
                // Check for duplicate asset_id and regenerate if necessary
                $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM $assetsTable WHERE asset_id = ?");
                $checkStmt->execute([$assetId]);
                if ($checkStmt->fetchColumn() > 0) {
                    // Try to parse prefix and number
                    if (preg_match('/^([A-Z]+)-(\d+)$/', $assetId, $matches)) {
                        $prefix = $matches[1];
                        $number = $matches[2];
                        $padLen = strlen($number);
                        
                        // Find max ID with this prefix
                        $stmt = $pdo->prepare("SELECT asset_id FROM $assetsTable WHERE asset_id LIKE ?");
                        $stmt->execute([$prefix . '-%']);
                        $existingIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
                        
                        $maxVal = 0;
                        foreach ($existingIds as $eid) {
                            if (preg_match('/^' . preg_quote($prefix, '/') . '-(\d+)$/', $eid, $m)) {
                                $val = (int)$m[1];
                                if ($val > $maxVal) $maxVal = $val;
                            }
                        }
                        
                        $newVal = $maxVal + 1;
                        $assetId = $prefix . '-' . str_pad($newVal, $padLen, '0', STR_PAD_LEFT);
                    } else {
                        // Fallback: append timestamp
                        $assetId = $assetId . '-' . time();
                    }
                }

                // Insert new asset
                $stmt = $pdo->prepare("INSERT INTO $assetsTable 
                                      (asset_id, asset_name, category, brand, model, serial_number, status, 
                                      location, assigned_to, department, purchase_date, purchase_price, 
                                      warranty_expiry, last_maintenance, priority, notes) 
                                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $assetId,
                    $actionDetails['asset_name'],
                    $actionDetails['category'],
                    $actionDetails['brand'],
                    $actionDetails['model'],
                    $actionDetails['serial_number'],
                    'ACTIVE', // Always set to ACTIVE when approved
                    $actionDetails['location'],
                    $actionDetails['assigned_to'] ?? 'Unassigned',
                    $actionDetails['department'],
                    $actionDetails['purchase_date'],
                    $actionDetails['purchase_price'],
                    $actionDetails['warranty_expiry'],
                    !empty($actionDetails['last_maintenance']) ? $actionDetails['last_maintenance'] : date('Y-m-d'),
                    $actionDetails['priority'] ?? 'Medium',
                    $actionDetails['notes'] ?? ''
                ]);
                break;
                
            case 'SERVICE':
                // Update asset status to MAINTENANCE and add service record
                $stmt = $pdo->prepare("UPDATE $assetsTable 
                                      SET status = 'MAINTENANCE', 
                                          last_maintenance = ?
                                      WHERE asset_id = ?");
                $stmt->execute([date('Y-m-d'), $assetId]);
                
                $stmt = $pdo->prepare("INSERT INTO $serviceHistoryTable 
                                      (asset_id, service_date, service_type, service_notes, service_by, status) 
                                      VALUES (?, ?, ?, ?, ?, 'PENDING')");
                $stmt->execute([
                    $assetId,
                    date('Y-m-d'),
                    $actionDetails['service_type'],
                    $actionDetails['service_notes'],
                    $actionDetails['service_by']
                ]);
                break;
                
            case 'COMPLETE_SERVICE':
                // Update asset status to ACTIVE and complete service record
                $stmt = $pdo->prepare("UPDATE $assetsTable 
                                      SET status = 'ACTIVE'
                                      WHERE asset_id = ?");
                $stmt->execute([$assetId]);
                
                $stmt = $pdo->prepare("UPDATE $serviceHistoryTable 
                                      SET completion_date = ?,
                                          status = 'COMPLETED'
                                      WHERE asset_id = ? AND status = 'PENDING'");
                $stmt->execute([date('Y-m-d'), $assetId]);
                break;
                
            case 'DELETE':
                // Move asset to deleted_assets table and remove from active assets
                $asset = $pdo->query("SELECT * FROM $assetsTable WHERE asset_id = '$assetId'")->fetch(PDO::FETCH_ASSOC);
                
                if ($asset) {
                    $stmt = $pdo->prepare("INSERT INTO $deletedAssetsTable 
                                          (original_asset_id, asset_name, category, brand, model, serial_number, 
                                          status, location, assigned_to, department, purchase_date, purchase_price, 
                                          warranty_expiry, last_maintenance, priority, notes, removal_reason, removal_notes, removed_by) 
                                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([
                        $asset['asset_id'],
                        $asset['asset_name'],
                        $asset['category'],
                        $asset['brand'],
                        $asset['model'],
                        $asset['serial_number'],
                        $asset['status'],
                        $asset['location'],
                        $asset['assigned_to'],
                        $asset['department'],
                        $asset['purchase_date'],
                        $asset['purchase_price'],
                        $asset['warranty_expiry'],
                        $asset['last_maintenance'],
                        $asset['priority'],
                        $asset['notes'],
                        $actionDetails['remove_reason'],
                        $actionDetails['remove_notes'],
                        $actionDetails['requested_by']
                    ]);
                    
                    $stmt = $pdo->prepare("DELETE FROM $assetsTable WHERE asset_id = ?");
                    $stmt->execute([$assetId]);
                }
                break;
                
            default:
                return ['success' => false, 'message' => 'Invalid action type'];
        }
        
        return ['success' => true];
    } catch (PDOException $e) {
        error_log("Error executing approved action: " . $e->getMessage());
        return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
    }
}