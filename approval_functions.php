<?php
require_once 'db_connect.php';

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
            return ['success' => true, 'message' => 'Action rejected'];
        }
    } catch (PDOException $e) {
        error_log("Error processing approval: " . $e->getMessage());
        return ['success' => false, 'message' => 'Database error'];
    }
}

function executeApprovedAction($actionType, $actionDetails, $assetId, $factory) {
    global $pdo;
    
    try {
        // Handle both JSON string and array formats for action_details
        if (is_string($actionDetails)) {
            $actionDetails = json_decode($actionDetails, true);
        }

        // Determine table names based on factory
        $assetsTable = "assets_".strtolower($factory);
        $serviceHistoryTable = "service_history_".strtolower($factory);
        $deletedAssetsTable = "deleted_assets_".strtolower($factory);
        
        // Validate tables exist
        $tables = $pdo->query("SHOW TABLES LIKE '$assetsTable'")->fetchAll();
        if (empty($tables)) {
            throw new Exception("Invalid factory or tables don't exist for factory: $factory");
        }

        switch ($actionType) {
            case 'ADD':
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
                    $actionDetails['last_maintenance'] ?? date('Y-m-d'),
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
        return ['success' => false, 'message' => 'Database error'];
    }
}