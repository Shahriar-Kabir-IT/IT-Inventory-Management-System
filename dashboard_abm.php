<?php
session_start();
require_once 'db_connect.php';

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit();
}

// Get current user info
$current_user = null;
try {
  $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
  $stmt->execute([$_SESSION['user_id']]);
  $current_user = $stmt->fetch();
} catch (PDOException $e) {
  die("Database error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>IT Asset Inventory Management</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
    }

    body {
      background: #f8fafc;
      min-height: 100vh;
      padding: 20px;
      font-size: 14px;
      color: #334155;
    }

    .container {
      max-width: 100%;
      margin: 0 auto;
      background: white;
      border-radius: 8px;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
      overflow: hidden;
      border: 1px solid #e2e8f0;
    }

    .header {
      background: #1e293b;
      color: white;
      padding: 18px 24px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .header h1 {
      font-size: 1.5rem;
      font-weight: 600;
      margin-bottom: 4px;
    }

    .header p {
      font-size: 0.875rem;
      opacity: 0.9;
      color: #cbd5e1;
    }

    .user-info {
      text-align: right;
    }

    .user-info p {
      margin-bottom: 2px;
      font-size: 0.875rem;
    }

    .user-info a {
      color: #93c5fd;
      text-decoration: none;
      font-size: 0.875rem;
      transition: color 0.2s;
    }

    .user-info a:hover {
      color: #60a5fa;
      text-decoration: underline;
    }

    .controls {
      padding: 12px 20px;
      background: #f8fafc;
      border-bottom: 1px solid #e2e8f0;
      display: flex;
      gap: 12px;
      flex-wrap: wrap;
      align-items: center;
    }

    .btn {
      padding: 8px 16px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-weight: 500;
      transition: all 0.2s ease;
      font-size: 0.875rem;
      display: inline-flex;
      align-items: center;
      gap: 6px;
    }

    .btn-primary {
      background: #3b82f6;
      color: white;
    }

    .btn-success {
      background: #10b981;
      color: white;
    }

    .btn-danger {
      background: #ef4444;
      color: white;
    }

    .btn-warning {
      background: #f59e0b;
      color: white;
    }

    .btn-info {
      background: #06b6d4;
      color: white;
    }

    .btn-secondary {
      background: #64748b;
      color: white;
    }

    .btn:hover {
      opacity: 0.9;
      box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    }

    .btn:active {
      transform: translateY(1px);
    }

    .filter-group {
      display: flex;
      gap: 8px;
      align-items: center;
    }

    .filter-group label {
      font-size: 0.875rem;
      color: #475569;
      font-weight: 500;
    }

    .filter-group select,
    .filter-group input {
      padding: 6px 12px;
      border: 1px solid #cbd5e1;
      border-radius: 6px;
      font-size: 0.875rem;
      min-width: 150px;
      background: white;
      color: #334155;
    }

    .filter-group select:focus,
    .filter-group input:focus {
      outline: none;
      border-color: #93c5fd;
      box-shadow: 0 0 0 3px rgba(147, 197, 253, 0.3);
    }

    .table-container {
      overflow-x: auto;
      max-height: 70vh;
      position: relative;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background: white;
      font-size: 0.875rem;
    }

    th {
      background: #f1f5f9;
      padding: 10px 12px;
      text-align: left;
      font-weight: 600;
      color: #475569;
      border-bottom: 1px solid #e2e8f0;
      position: sticky;
      top: 0;
      z-index: 10;
    }

    td {
      padding: 10px 12px;
      border-bottom: 1px solid #f1f5f9;
      color: #475569;
    }

    tr:hover {
      background-color: #f8fafc;
    }

    .status {
      padding: 4px 8px;
      border-radius: 12px;
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      display: inline-block;
    }

    .status-active {
      background: #dcfce7;
      color: #166534;
    }

    .status-inactive {
      background: #fef9c3;
      color: #854d0e;
    }

    .status-out-of-order {
      background: #fee2e2;
      color: #991b1b;
    }

    .status-maintenance {
      background: #e0f2fe;
      color: #075985;
    }

    .priority-high {
      color: #ef4444;
      font-weight: 600;
    }

    .priority-medium {
      color: #f59e0b;
      font-weight: 600;
    }

    .priority-low {
      color: #10b981;
      font-weight: 600;
    }

    .stats {
      padding: 16px 20px;
      background: #f8fafc;
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 12px;
      border-bottom: 1px solid #e2e8f0;
    }

    .stat-card {
      background: white;
      padding: 16px;
      border-radius: 8px;
      text-align: center;
      box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
      transition: transform 0.2s ease, box-shadow 0.2s ease;
      border: 1px solid #e2e8f0;
    }

    .stat-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    }

    .stat-number {
      font-size: 1.5rem;
      font-weight: 700;
      margin-bottom: 4px;
    }

    .stat-label {
      color: #64748b;
      font-size: 0.875rem;
    }

    /* Modal styles */
    #modal {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.5);
      display: flex;
      justify-content: center;
      align-items: center;
      z-index: 1000;
      backdrop-filter: blur(2px);
    }

    .modal-content {
      background: white;
      padding: 24px;
      border-radius: 8px;
      width: 90%;
      max-width: 800px;
      max-height: 90vh;
      overflow-y: auto;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
      border: 1px solid #e2e8f0;
    }

    .modal-content h2 {
      font-size: 1.25rem;
      margin-bottom: 16px;
      color: #1e293b;
      display: flex;
      align-items: center;
      gap: 8px;
      font-weight: 600;
    }

    .modal-content label {
      display: block;
      margin-bottom: 6px;
      font-weight: 500;
      color: #475569;
      font-size: 0.875rem;
    }

    .modal-content input,
    .modal-content select,
    .modal-content textarea {
      width: 100%;
      padding: 8px 12px;
      border: 1px solid #cbd5e1;
      border-radius: 6px;
      margin-bottom: 16px;
      font-size: 0.875rem;
      background: white;
      color: #334155;
    }

    .modal-content input:focus,
    .modal-content select:focus,
    .modal-content textarea:focus {
      outline: none;
      border-color: #93c5fd;
      box-shadow: 0 0 0 3px rgba(147, 197, 253, 0.3);
    }

    .modal-content textarea {
      height: 100px;
      resize: vertical;
    }

    .button-group {
      display: flex;
      justify-content: flex-end;
      gap: 8px;
      margin-top: 20px;
    }

    .modal-content button {
      padding: 8px 16px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-weight: 500;
      transition: all 0.2s;
      font-size: 0.875rem;
    }

    .warning-box {
      background: #fef2f2;
      padding: 12px;
      border-radius: 6px;
      border-left: 4px solid #ef4444;
      margin: 16px 0;
      color: #991b1b;
      font-size: 0.875rem;
    }

    .info-box {
      background: #ecfdf5;
      padding: 12px;
      border-radius: 6px;
      border-left: 4px solid #10b981;
      margin: 16px 0;
      color: #065f46;
      font-size: 0.875rem;
    }

    .history-table {
      width: 100%;
      border-collapse: collapse;
      margin: 16px 0;
      font-size: 0.875rem;
    }

    .history-table th,
    .history-table td {
      padding: 10px;
      border: 1px solid #e2e8f0;
      text-align: left;
    }

    .history-table th {
      background-color: #f1f5f9;
      font-weight: 600;
      color: #475569;
    }

    .history-table tr:nth-child(even) {
      background-color: #f8fafc;
    }

    .approval-notice {
      background-color: #ecfdf5;
      padding: 10px;
      border-radius: 6px;
      margin-bottom: 16px;
      text-align: center;
      font-size: 0.875rem;
      border-left: 4px solid #10b981;
    }

    .notification-badge {
      position: relative;
      display: inline-block;
    }

    .notification-count {
      position: absolute;
      top: -6px;
      right: -6px;
      background-color: #ef4444;
      color: white;
      border-radius: 50%;
      padding: 2px 5px;
      font-size: 0.75rem;
      min-width: 18px;
      text-align: center;
    }

    /* Search in modals */
    .modal-search {
      margin-bottom: 16px;
      position: relative;
    }

    .modal-search input {
      width: 100%;
      padding: 8px 12px 8px 32px;
      border: 1px solid #cbd5e1;
      border-radius: 6px;
      font-size: 0.875rem;
      background: white url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2364758b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='11' cy='11' r='8'%3E%3C/circle%3E%3Cline x1='21' y1='21' x2='16.65' y2='16.65'%3E%3C/line%3E%3C/svg%3E") no-repeat 10px center;
    }

    .modal-search input:focus {
      outline: none;
      border-color: #93c5fd;
      box-shadow: 0 0 0 3px rgba(147, 197, 253, 0.3);
    }

    /* Asset select in modals */
    .asset-select-container {
      margin-bottom: 16px;
    }

    .asset-select {
      width: 100%;
      border: 1px solid #cbd5e1;
      border-radius: 6px;
      max-height: 200px;
      overflow-y: auto;
      background: white;
    }

    .asset-select option {
      padding: 8px 12px;
      border-bottom: 1px solid #f1f5f9;
      cursor: pointer;
    }

    .asset-select option:hover {
      background-color: #f8fafc;
    }

    .asset-select option:checked {
      background-color: #3b82f6;
      color: white;
    }

    /* Loading indicator */
    #loading {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(255, 255, 255, 0.8);
      display: flex;
      justify-content: center;
      align-items: center;
      z-index: 9999;
    }

    .loading-spinner {
      border: 4px solid #f1f5f9;
      border-top: 4px solid #3b82f6;
      border-radius: 50%;
      width: 40px;
      height: 40px;
      animation: spin 1s linear infinite;
    }

    @keyframes spin {
      0% {
        transform: rotate(0deg);
      }

      100% {
        transform: rotate(360deg);
      }
    }

    /* Improved search results */
    .search-results {
      border: 1px solid #e2e8f0;
      border-radius: 6px;
      max-height: 200px;
      overflow-y: auto;
      background: white;
      margin-bottom: 16px;
    }

    .search-result-item {
      padding: 10px 12px;
      border-bottom: 1px solid #f1f5f9;
      cursor: pointer;
      transition: background-color 0.2s;
    }

    .search-result-item:hover {
      background-color: #f8fafc;
    }

    .search-result-item.selected {
      background-color: #3b82f6;
      color: white;
    }

    .search-result-item .asset-id {
      font-weight: 600;
    }

    .search-result-item .asset-details {
      font-size: 0.75rem;
      color: #64748b;
      margin-top: 2px;
    }

    .search-result-item.selected .asset-details {
      color: #e2e8f0;
    }

    .no-results {
      padding: 10px 12px;
      color: #64748b;
      font-style: italic;
    }

    @media (max-width: 768px) {
      .controls {
        flex-direction: column;
        align-items: stretch;
      }

      .filter-group {
        width: 100%;
      }

      .filter-group select,
      .filter-group input {
        width: 100%;
      }

      .header {
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
      }

      .user-info {
        text-align: left;
        width: 100%;
      }

      .logout-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 12px;
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white;
        text-decoration: none;
        border-radius: 6px;
        font-weight: 500;
        transition: all 0.2s ease;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        border: none;
        cursor: pointer;
      }

      .logout-btn:hover {
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        transform: translateY(-1px);
      }

      .logout-btn:active {
        transform: translateY(0);
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
      }

      .logout-icon {
        display: flex;
        align-items: center;
      }

      .logout-text {
        font-size: 0.875rem;
      }
    }
  </style>
</head>

<body>
  <div class="container">
    <div class="header">
      <div>
        <h1>IT Asset Inventory Management</h1>
        <p>ABM Fashion Limited</p>
      </div>
      <div class="user-info">
        <p>Welcome, <?php echo htmlspecialchars($current_user['name']); ?></p>
        <p><?php echo strtoupper($current_user['factory']); ?> - <?php echo ucfirst($current_user['user_type']); ?></p>
        <div style="margin-top: 5px;">
          <a href="logout.php" class="logout-btn">
            <span class="logout-icon">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                <polyline points="16 17 21 12 16 7"></polyline>
                <line x1="21" y1="12" x2="9" y2="12"></line>
              </svg>
            </span>
            <span class="logout-text">Logout</span>
          </a>
        </div>
        <div style="margin-top: 5px;">
          <a href="#" onclick="showNotifications()">
            <span class="notification-badge">🔔 <span id="notificationCount" class="notification-count">0</span></span>
          </a>
        </div>
      </div>
    </div>

    <div class="stats">
      <div class="stat-card">
        <div class="stat-number" style="color: #10b981;" id="activeCount">0</div>
        <div class="stat-label">Active Assets</div>
      </div>
      <div class="stat-card">
        <div class="stat-number" style="color: #f59e0b;" id="inactiveCount">0</div>
        <div class="stat-label">Inactive Assets</div>
      </div>
      <div class="stat-card">
        <div class="stat-number" style="color: #ef4444;" id="outOfOrderCount">0</div>
        <div class="stat-label">Out of Order</div>
      </div>
      <div class="stat-card">
        <div class="stat-number" style="color: #06b6d4;" id="maintenanceCount">0</div>
        <div class="stat-label">Under Maintenance</div>
      </div>
    </div>

    <div class="controls">
      <button class="btn btn-success" id="addItemBtn">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="12" y1="5" x2="12" y2="19"></line>
          <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
        Add New Item
      </button>
      <button class="btn btn-warning" id="serviceBtn">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
          <circle cx="10" cy="16" r="2"></circle>
          <path d="M20 10v4h-4"></path>
          <path d="M16 10v4"></path>
        </svg>
        Send for Servicing
      </button>
      <button class="btn btn-danger" id="removeBtn">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="3 6 5 6 21 6"></polyline>
          <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
        </svg>
        Remove Item
      </button>
      <button class="btn btn-primary" id="exportBtn">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
          <polyline points="7 10 12 15 17 10"></polyline>
          <line x1="12" y1="15" x2="12" y2="3"></line>
        </svg>
        Export
      </button>
      <button class="btn btn-info" id="historyBtn">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="12" cy="12" r="10"></circle>
          <polyline points="12 6 12 12 16 14"></polyline>
        </svg>
        Service History
      </button>

      <div class="filter-group">
        <label>Filter by Status:</label>
        <select id="statusFilter">
          <option value="">All Status</option>
          <option value="ACTIVE">Active</option>
          <option value="INACTIVE">Inactive</option>
          <option value="OUT OF ORDER">Out of Order</option>
          <option value="MAINTENANCE">Maintenance</option>
        </select>
      </div>
      <div class="filter-group">
        <label>Search:</label>
        <input type="text" id="searchInput" placeholder="Search...">
      </div>
    </div>

    <div class="table-container">
      <table id="inventoryTable">
        <thead>
          <tr>
            <th>Asset ID</th>
            <th>Asset Name</th>
            <th>Category</th>
            <th>Brand</th>
            <th>Model</th>
            <th>Serial</th>
            <th>Status</th>
            <th>Location</th>
            <th>Assigned</th>
            <th>Department</th>
            <th>Purchase</th>
            <th>Price</th>
            <th>Warranty</th>
            <th>Last Maintenance</th>
            <th>Priority</th>
            <th>Notes</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="inventoryBody"></tbody>
      </table>
    </div>
  </div>

  <script>
    let inventoryData = [];
    let filteredData = [];

    // DOM Elements
    const addItemBtn = document.getElementById('addItemBtn');
    const serviceBtn = document.getElementById('serviceBtn');
    const removeBtn = document.getElementById('removeBtn');
    const exportBtn = document.getElementById('exportBtn');
    const historyBtn = document.getElementById('historyBtn');
    const statusFilter = document.getElementById('statusFilter');
    const searchInput = document.getElementById('searchInput');
    const notificationCount = document.getElementById('notificationCount');

    // Event Listeners
    addItemBtn.addEventListener('click', showAddItemModal);
    serviceBtn.addEventListener('click', showServiceModal);
    removeBtn.addEventListener('click', showRemoveModal);
    exportBtn.addEventListener('click', exportToCSV);
    historyBtn.addEventListener('click', showAllServiceHistory);
    statusFilter.addEventListener('change', filterTable);
    searchInput.addEventListener('keyup', filterTable);

    // Load data when page loads
    document.addEventListener('DOMContentLoaded', () => {
      loadDataFromDB();
      updateNotificationCount();
    });

    // Notification functions
    function updateNotificationCount() {
      fetch('get_unread_notifications.php')
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            notificationCount.textContent = data.count;
            notificationCount.style.display = data.count > 0 ? 'inline-block' : 'none';
          }
        })
        .catch(error => {
          console.error('Error updating notification count:', error);
        });
    }

    function showNotifications() {
      fetch('get_notifications.php')
        .then(response => response.json())
        .then(notifications => {
          let html = `
            <div id="modal">
              <div class="modal-content">
                <h2>
                  <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                    <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                  </svg>
                  Notifications
                </h2>
                
                ${notifications.length > 0 ? `
                <table class="history-table">
                  <thead>
                    <tr>
                      <th>Date</th>
                      <th>Message</th>
                    </tr>
                  </thead>
                  <tbody>
                    ${notifications.map(notification => `
                      <tr>
                        <td>${new Date(notification.created_at).toLocaleString()}</td>
                        <td>${notification.message}</td>
                      </tr>
                    `).join('')}
                  </tbody>
                </table>
                ` : '<p>No notifications found.</p>'}
                
                <div class="button-group">
                  <button type="button" onclick="markNotificationsAsRead()" class="btn btn-success">
                    Mark as Read
                  </button>
                  <button type="button" onclick="closeModal()" class="btn btn-secondary">
                    Close
                  </button>
                </div>
              </div>
            </div>
          `;

          document.body.insertAdjacentHTML('beforeend', html);
        })
        .catch(error => {
          console.error('Error loading notifications:', error);
          alert('Error loading notifications');
        });
    }

    function markNotificationsAsRead() {
      fetch('mark_notifications_read.php', {
          method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            updateNotificationCount();
            closeModal();
          }
        })
        .catch(error => {
          console.error('Error marking notifications as read:', error);
        });
    }

    // Check for new notifications every 30 seconds
    setInterval(updateNotificationCount, 30000);

    async function loadDataFromDB() {
      try {
        showLoading(true);
        const response = await fetch('get_assets_abm.php');

        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();

        if (data.error) {
          throw new Error(data.error);
        }

        // Sort data in descending order by asset_id
        inventoryData = data.sort((a, b) => {
          return b.asset_id.localeCompare(a.asset_id);
        });

        filteredData = [...inventoryData];
        renderTable();
        updateStats();

        console.log('Data loaded successfully:', inventoryData);
      } catch (error) {
        console.error('Error loading data:', error);
        alert('Error loading data. Please check console for details.');
      } finally {
        showLoading(false);
      }
    }

    function renderTable() {
      const tbody = document.getElementById('inventoryBody');
      tbody.innerHTML = '';

      if (filteredData.length === 0) {
        tbody.innerHTML = '<tr><td colspan="17" style="text-align:center;">No assets found</td></tr>';
        return;
      }

      filteredData.forEach(item => {
        const row = document.createElement('tr');
        row.innerHTML = `
          <td>${item.asset_id || 'N/A'}</td>
          <td>${item.asset_name || 'N/A'}</td>
          <td>${item.category || 'N/A'}</td>
          <td>${item.brand || 'N/A'}</td>
          <td>${item.model || 'N/A'}</td>
          <td>${item.serial_number || 'N/A'}</td>
          <td><span class="status ${getStatusClass(item.status)}">${item.status || 'N/A'}</span></td>
          <td>${item.location || 'N/A'}</td>
          <td>${item.assigned_to || 'N/A'}</td>
          <td>${item.department || 'N/A'}</td>
          <td>${formatDate(item.purchase_date) || 'N/A'}</td>
          <td>${item.purchase_price ? '$' + parseFloat(item.purchase_price).toFixed(2) : 'N/A'}</td>
          <td>${formatDate(item.warranty_expiry) || 'N/A'}</td>
          <td>${formatDate(item.last_maintenance) || 'N/A'}</td>
          <td><span class="priority-${item.priority ? item.priority.toLowerCase() : 'medium'}">${item.priority || 'Medium'}</span></td>
          <td>${item.notes || 'N/A'}</td>
          <td>
            ${item.status === 'MAINTENANCE' ? 
              `<button onclick="showCompleteServiceModal('${item.asset_id}')" class="btn btn-success" style="padding:5px 10px; margin-bottom:5px;">
                Complete Service
              </button><br>` : ''
            }
            <button onclick="showServiceHistory('${item.asset_id}')" class="btn btn-info" style="padding:5px 10px;">
              Service History
            </button>
          </td>
        `;
        tbody.appendChild(row);
      });
    }

    function getStatusClass(status) {
      if (!status) return '';
      switch (status.toUpperCase()) {
        case 'ACTIVE':
          return 'status-active';
        case 'INACTIVE':
          return 'status-inactive';
        case 'OUT OF ORDER':
          return 'status-out-of-order';
        case 'MAINTENANCE':
          return 'status-maintenance';
        default:
          return '';
      }
    }

    function formatDate(dateString) {
      if (!dateString) return '';
      const date = new Date(dateString);
      return isNaN(date.getTime()) ? dateString : date.toISOString().split('T')[0];
    }

    function updateStats() {
      document.getElementById('activeCount').textContent =
        inventoryData.filter(i => i.status && i.status.toUpperCase() === 'ACTIVE').length;
      document.getElementById('inactiveCount').textContent =
        inventoryData.filter(i => i.status && i.status.toUpperCase() === 'INACTIVE').length;
      document.getElementById('outOfOrderCount').textContent =
        inventoryData.filter(i => i.status && i.status.toUpperCase() === 'OUT OF ORDER').length;
      document.getElementById('maintenanceCount').textContent =
        inventoryData.filter(i => i.status && i.status.toUpperCase() === 'MAINTENANCE').length;
    }

    function filterTable() {
      const status = statusFilter.value;
      const search = searchInput.value.toLowerCase();

      filteredData = inventoryData.filter(item => {
        const statusMatch = !status ||
          (item.status && item.status.toUpperCase() === status.toUpperCase());

        const searchMatch = !search ||
          Object.values(item).some(val =>
            val && val.toString().toLowerCase().includes(search));

        return statusMatch && searchMatch;
      });

      renderTable();
    }

    function exportToCSV() {
      if (filteredData.length === 0) {
        alert('No data to export!');
        return;
      }

      const headers = [
        'Asset ID', 'Asset Name', 'Category', 'Brand', 'Model',
        'Serial Number', 'Status', 'Location', 'Assigned To',
        'Department', 'Purchase Date', 'Purchase Price',
        'Warranty Expiry', 'Last Maintenance', 'Priority', 'Notes'
      ];

      const csvRows = [
        headers.join(','),
        ...filteredData.map(item => [
          item.asset_id,
          item.asset_name,
          item.category,
          item.brand,
          item.model,
          item.serial_number,
          item.status,
          item.location,
          item.assigned_to,
          item.department,
          item.purchase_date,
          item.purchase_price,
          item.warranty_expiry,
          item.last_maintenance,
          item.priority,
          item.notes
        ].map(field => `"${(field || '').toString().replace(/"/g, '""')}"`).join(','))
      ];

      const csvContent = csvRows.join('\n');
      const blob = new Blob([csvContent], {
        type: 'text/csv;charset=utf-8;'
      });
      const url = URL.createObjectURL(blob);
      const link = document.createElement('a');
      link.href = url;
      link.setAttribute('download', 'inventory_export_' + new Date().toISOString().slice(0, 10) + '.csv');
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
    }

    function showAddItemModal() {
      const modalHtml = `
      <div id="modal">
        <div class="modal-content">
          <h2>
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <line x1="12" y1="5" x2="12" y2="19"></line>
              <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Add New Asset
          </h2>
          <div class="approval-notice">
            <strong>Note:</strong> All additions require approval from the admin.
          </div>
          <form id="addForm">
            <div>
              <label for="assetName">Asset Name*</label>
              <input type="text" id="assetName" required>
            </div>
            
            <div>
              <label for="category">Category*</label>
              <select id="category" required>
                <option value="">Select Category</option>
                <option value="Desktop">Desktop</option>
                <option value="Laptop">Laptop</option>
                <option value="Server">Server</option>
                <option value="Network">Network</option>
                <option value="Printer">Printer</option>
                <option value="Monitor">Monitor</option>
                <option value="Mobile">Mobile</option>
              </select>
            </div>
            
            <div>
              <label for="brand">Brand*</label>
              <input type="text" id="brand" required>
            </div>
            
            <div>
              <label for="model">Model*</label>
              <input type="text" id="model" required>
            </div>
            
            <div>
              <label for="serial">Serial Number*</label>
              <input type="text" id="serial" required>
            </div>
            
            <div>
              <label for="location">Location*</label>
              <input type="text" id="location" required>
            </div>
            
            <div>
              <label for="assigned">Assigned To</label>
              <input type="text" id="assigned">
            </div>
            
            <div>
              <label for="department">Department*</label>
              <input type="text" id="department" required>
            </div>
            
            <div>
              <label for="purchaseDate">Purchase Date*</label>
              <input type="date" id="purchaseDate" required>
            </div>
            
            <div>
              <label for="price">Purchase Price*</label>
              <input type="text" id="price" required placeholder="$1,000">
            </div>
            
            <div>
              <label for="warranty">Warranty Expiry*</label>
              <input type="date" id="warranty" required>
            </div>
            
            <div>
              <label for="priority">Priority</label>
              <select id="priority">
                <option value="Low">Low</option>
                <option value="Medium" selected>Medium</option>
                <option value="High">High</option>
              </select>
            </div>
            
            <div>
              <label for="notes">Notes</label>
              <textarea id="notes" placeholder="Additional information..."></textarea>
            </div>
            
            <div class="button-group">
              <button type="submit" class="btn btn-success">Add Asset</button>
              <button type="button" onclick="closeModal()" class="btn btn-secondary">Cancel</button>
            </div>
          </form>
        </div>
      </div>`;

      document.body.insertAdjacentHTML('beforeend', modalHtml);

      document.getElementById('addForm').addEventListener('submit', async (e) => {
        e.preventDefault();

        const priceValue = document.getElementById('price').value.replace(/[^0-9.]/g, '');

        const data = {
          asset_name: document.getElementById('assetName').value,
          category: document.getElementById('category').value,
          brand: document.getElementById('brand').value,
          model: document.getElementById('model').value,
          serial_number: document.getElementById('serial').value,
          status: 'PENDING',
          location: document.getElementById('location').value,
          assigned_to: document.getElementById('assigned').value || 'Unassigned',
          department: document.getElementById('department').value,
          purchase_date: document.getElementById('purchaseDate').value,
          purchase_price: priceValue,
          warranty_expiry: document.getElementById('warranty').value,
          last_maintenance: new Date().toISOString().split('T')[0],
          priority: document.getElementById('priority').value,
          notes: document.getElementById('notes').value
        };

        await submitAddItem(data);
      });
    }

    async function submitAddItem(data) {
      try {
        data.action_type = 'ADD';
        data.requested_by = '<?php echo $current_user['name']; ?>';
        data.factory = 'abm';
        data.asset_id = generateAssetId();

        const response = await fetch('request_approval_abm.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify(data)
        });

        const result = await response.json();
        if (result.success) {
          alert('Your request has been submitted for approval. You will be notified once approved.');
          closeModal();
          loadDataFromDB();
        } else {
          alert('Error submitting request: ' + (result.message || 'Unknown error'));
        }
      } catch (error) {
        console.error('Error:', error);
        alert('Error submitting request. Check console for details.');
      }
    }

    function showServiceModal() {
      if (inventoryData.length === 0) {
        alert('No assets available for servicing');
        return;
      }

      const modalHtml = `
      <div id="modal">
        <div class="modal-content">
          <h2>
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
              <circle cx="10" cy="16" r="2"></circle>
              <path d="M20 10v4h-4"></path>
              <path d="M16 10v4"></path>
            </svg>
            Send Asset for Servicing
          </h2>
          <div class="approval-notice">
            <strong>Note:</strong> All service requests require approval from the admin.
          </div>
          
          <div class="modal-search">
            <input type="text" id="serviceSearch" placeholder="Search by Asset ID, Name, Assigned User or Department..." autocomplete="off">
          </div>
          
          <div class="search-results" id="serviceResults">
            ${inventoryData
              .filter(item => ['ACTIVE', 'OUT OF ORDER'].includes(item.status?.toUpperCase()))
              .map(item => `
                <div class="search-result-item" data-asset-id="${item.asset_id}">
                  <div class="asset-id">${item.asset_id}</div>
                  <div class="asset-details">${item.asset_name} (${item.assigned_to || 'Unassigned'}) - ${item.department || 'No Dept'}</div>
                </div>
              `).join('')}
          </div>
          
          <input type="hidden" id="serviceAsset">
          
          <div>
            <label for="serviceType">Service Type:</label>
            <select id="serviceType">
              <option value="Scheduled Maintenance">Scheduled Maintenance</option>
              <option value="Repair">Repair</option>
              <option value="Upgrade">Upgrade</option>
              <option value="Inspection">Inspection</option>
            </select>
          </div>
          
          <div>
            <label for="serviceBy">Serviced By:</label>
            <input type="text" id="serviceBy" placeholder="Technician name">
          </div>
          
          <div>
            <label for="serviceNotes">Service Notes:</label>
            <textarea id="serviceNotes" placeholder="Describe the service required..."></textarea>
          </div>
          
          <div class="button-group">
            <button type="button" onclick="submitService()" class="btn btn-warning">Send for Service</button>
            <button type="button" onclick="closeModal()" class="btn btn-secondary">Cancel</button>
          </div>
        </div>
      </div>`;

      document.body.insertAdjacentHTML('beforeend', modalHtml);

      // Setup search functionality
      const searchInput = document.getElementById('serviceSearch');
      const resultsContainer = document.getElementById('serviceResults');
      const assetIdInput = document.getElementById('serviceAsset');
      const serviceItems = resultsContainer.querySelectorAll('.search-result-item');

      // Select first item by default if available
      if (serviceItems.length > 0) {
        serviceItems[0].classList.add('selected');
        assetIdInput.value = serviceItems[0].getAttribute('data-asset-id');
      }

      // Handle search input
      searchInput.addEventListener('input', () => {
        const searchTerm = searchInput.value.toLowerCase();
        let hasResults = false;

        serviceItems.forEach(item => {
          const text = item.textContent.toLowerCase();
          if (text.includes(searchTerm)) {
            item.style.display = '';
            hasResults = true;
          } else {
            item.style.display = 'none';
          }
        });

        // Show no results message if no matches
        const noResults = document.getElementById('noServiceResults');
        if (!hasResults) {
          if (!noResults) {
            const noResultsMsg = document.createElement('div');
            noResultsMsg.id = 'noServiceResults';
            noResultsMsg.className = 'no-results';
            noResultsMsg.textContent = 'No matching assets found';
            resultsContainer.appendChild(noResultsMsg);
          }
        } else if (noResults) {
          noResults.remove();
        }
      });

      // Handle item selection
      resultsContainer.addEventListener('click', (e) => {
        const item = e.target.closest('.search-result-item');
        if (item) {
          serviceItems.forEach(i => i.classList.remove('selected'));
          item.classList.add('selected');
          assetIdInput.value = item.getAttribute('data-asset-id');
        }
      });
    }

    async function submitService() {
      const assetId = document.getElementById('serviceAsset').value;
      const serviceType = document.getElementById('serviceType').value;
      const serviceBy = document.getElementById('serviceBy').value;
      const serviceNotes = document.getElementById('serviceNotes').value;

      if (!assetId) {
        alert('Please select an asset to service');
        return;
      }

      if (!serviceBy) {
        alert('Please enter technician name');
        return;
      }

      const data = {
        asset_id: assetId,
        status: 'MAINTENANCE',
        service_type: serviceType,
        service_notes: serviceNotes,
        service_by: serviceBy,
        last_maintenance: new Date().toISOString().split('T')[0]
      };

      await submitServiceRequest(data);
    }

    async function submitServiceRequest(data) {
      try {
        data.action_type = 'SERVICE';
        data.requested_by = '<?php echo $current_user['name']; ?>';
        data.factory = 'abm';

        const response = await fetch('request_approval_abm.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify(data)
        });

        const result = await response.json();
        if (result.success) {
          alert('Your service request has been submitted for approval. You will be notified once approved.');
          closeModal();
          loadDataFromDB();
        } else {
          alert('Error submitting service request: ' + (result.message || 'Unknown error'));
        }
      } catch (error) {
        console.error('Error:', error);
        alert('Error submitting service request. Check console for details.');
      }
    }

    function showCompleteServiceModal(assetId) {
      const asset = inventoryData.find(item => item.asset_id === assetId);
      if (!asset) return;

      const modalHtml = `
      <div id="modal">
        <div class="modal-content">
          <h2>
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
              <polyline points="22 4 12 14.01 9 11.01"></polyline>
            </svg>
            Complete Service for ${assetId}
          </h2>
          <div class="approval-notice">
            <strong>Note:</strong> Service completion requires approval from the admin.
          </div>
          <div class="info-box">
            <strong>Asset:</strong> ${asset.asset_name || 'N/A'}<br>
            <strong>Current Status:</strong> ${asset.status || 'N/A'}
          </div>
          
          <div>
            <label for="completionNotes">Completion Notes:</label>
            <textarea id="completionNotes" placeholder="Describe work completed..."></textarea>
          </div>
          
          <div class="button-group">
            <button type="button" onclick="completeService('${assetId}')" class="btn btn-success">Mark as Completed</button>
            <button type="button" onclick="closeModal()" class="btn btn-secondary">Cancel</button>
          </div>
        </div>
      </div>`;

      document.body.insertAdjacentHTML('beforeend', modalHtml);
    }

    async function completeService(assetId) {
      const completionNotes = document.getElementById('completionNotes').value;
      await submitCompleteServiceRequest(assetId, completionNotes);
    }

    async function submitCompleteServiceRequest(assetId, completionNotes) {
      try {
        const data = {
          action_type: 'COMPLETE_SERVICE',
          requested_by: '<?php echo $current_user['name']; ?>',
          factory: 'abm',
          asset_id: assetId,
          completion_notes: completionNotes
        };

        const response = await fetch('request_approval_abm.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify(data)
        });

        const result = await response.json();
        if (result.success) {
          alert('Your completion request has been submitted for approval. You will be notified once approved.');
          closeModal();
          loadDataFromDB();
        } else {
          alert('Error submitting completion request: ' + (result.message || 'Unknown error'));
        }
      } catch (error) {
        console.error('Error:', error);
        alert('Error submitting completion request. Check console for details.');
      }
    }

    function showRemoveModal() {
      if (inventoryData.length === 0) {
        alert('No assets available to remove');
        return;
      }

      const modalHtml = `
      <div id="modal">
        <div class="modal-content">
          <h2>
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="3 6 5 6 21 6"></polyline>
              <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
            </svg>
            Remove Asset from Inventory
          </h2>
          <div class="approval-notice">
            <strong>Note:</strong> All removal requests require approval from the admin.
          </div>
          
          <div class="modal-search">
            <input type="text" id="removeSearch" placeholder="Search by Asset ID, Name, Assigned User or Department..." autocomplete="off">
          </div>
          
          <div class="search-results" id="removeResults">
            ${inventoryData
              .map(item => `
                <div class="search-result-item" data-asset-id="${item.asset_id}">
                  <div class="asset-id">${item.asset_id}</div>
                  <div class="asset-details">${item.asset_name} (${item.assigned_to || 'Unassigned'}) - ${item.department || 'No Dept'} - ${item.status || 'N/A'}</div>
                </div>
              `).join('')}
          </div>
          
          <input type="hidden" id="removeAsset">
          
          <div>
            <label for="removeReason">Reason for Removal:</label>
            <select id="removeReason">
              <option value="End of Life">End of Life</option>
              <option value="Sold">Sold</option>
              <option value="Donated">Donated</option>
              <option value="Disposed">Disposed</option>
              <option value="Lost/Stolen">Lost/Stolen</option>
              <option value="Transfer">Transfer to Another Dept</option>
            </select>
          </div>
          
          <div>
            <label for="removeNotes">Additional Notes:</label>
            <textarea id="removeNotes" placeholder="Additional details about removal..."></textarea>
          </div>
          
          <div class="warning-box">
            <strong>⚠️ Warning:</strong> This action will permanently remove the asset from your inventory. 
            Make sure to backup your data before proceeding.
          </div>
          
          <div class="button-group">
            <button type="button" onclick="submitRemove()" class="btn btn-danger">Remove Asset</button>
            <button type="button" onclick="closeModal()" class="btn btn-secondary">Cancel</button>
          </div>
        </div>
      </div>`;

      document.body.insertAdjacentHTML('beforeend', modalHtml);

      // Setup search functionality
      const searchInput = document.getElementById('removeSearch');
      const resultsContainer = document.getElementById('removeResults');
      const assetIdInput = document.getElementById('removeAsset');
      const removeItems = resultsContainer.querySelectorAll('.search-result-item');

      // Select first item by default if available
      if (removeItems.length > 0) {
        removeItems[0].classList.add('selected');
        assetIdInput.value = removeItems[0].getAttribute('data-asset-id');
      }

      // Handle search input
      searchInput.addEventListener('input', () => {
        const searchTerm = searchInput.value.toLowerCase();
        let hasResults = false;

        removeItems.forEach(item => {
          const text = item.textContent.toLowerCase();
          if (text.includes(searchTerm)) {
            item.style.display = '';
            hasResults = true;
          } else {
            item.style.display = 'none';
          }
        });

        // Show no results message if no matches
        const noResults = document.getElementById('noRemoveResults');
        if (!hasResults) {
          if (!noResults) {
            const noResultsMsg = document.createElement('div');
            noResultsMsg.id = 'noRemoveResults';
            noResultsMsg.className = 'no-results';
            noResultsMsg.textContent = 'No matching assets found';
            resultsContainer.appendChild(noResultsMsg);
          }
        } else if (noResults) {
          noResults.remove();
        }
      });

      // Handle item selection
      resultsContainer.addEventListener('click', (e) => {
        const item = e.target.closest('.search-result-item');
        if (item) {
          removeItems.forEach(i => i.classList.remove('selected'));
          item.classList.add('selected');
          assetIdInput.value = item.getAttribute('data-asset-id');
        }
      });
    }

    async function submitRemove() {
      const assetId = document.getElementById('removeAsset').value;
      const removeReason = document.getElementById('removeReason').value;
      const removeNotes = document.getElementById('removeNotes').value;

      if (!assetId) {
        alert('Please select an asset to remove');
        return;
      }

      const data = {
        asset_id: assetId,
        remove_reason: removeReason,
        remove_notes: removeNotes
      };

      await submitRemoveRequest(data);
    }

    async function submitRemoveRequest(data) {
      try {
        data.action_type = 'DELETE';
        data.requested_by = '<?php echo $current_user['name']; ?>';
        data.factory = 'abm';

        const response = await fetch('request_approval_abm.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify(data)
        });

        const result = await response.json();
        if (result.success) {
          alert('Your removal request has been submitted for approval. You will be notified once approved.');
          closeModal();
          loadDataFromDB();
        } else {
          alert('Error submitting removal request: ' + (result.message || 'Unknown error'));
        }
      } catch (error) {
        console.error('Error:', error);
        alert('Error submitting removal request. Check console for details.');
      }
    }

    function showServiceHistory(assetId) {
      fetch(`get_service_history_abm.php?asset_id=${assetId}`)
        .then(response => response.json())
        .then(history => {
          const asset = inventoryData.find(item => item.asset_id === assetId);
          const modalHtml = `
            <div id="modal">
                <div class="modal-content">
                    <h2>
                      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                      </svg>
                      Service History for ${assetId}
                    </h2>
                    <div class="info-box">
                        <strong>Asset:</strong> ${asset?.asset_name || 'N/A'}<br>
                        <strong>Model:</strong> ${asset?.model || 'N/A'}<br>
                        <strong>Current Status:</strong> ${asset?.status || 'N/A'}
                    </div>
                    
                    ${history.length > 0 ? `
                    <table class="history-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Technician</th>
                                <th>Status</th>
                                <th>Completed</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${history.map(record => `
                                <tr>
                                    <td>${record.service_date || 'N/A'}</td>
                                    <td>${record.service_type || 'N/A'}</td>
                                    <td>${record.service_by || 'N/A'}</td>
                                    <td>${record.status || 'N/A'}</td>
                                    <td>${record.completion_date || 'N/A'}</td>
                                    <td>${record.service_notes || ''}</td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                    ` : '<p>No service history found for this asset.</p>'}
                    
                    <div class="button-group">
                        <button type="button" onclick="closeModal()" class="btn btn-secondary">Close</button>
                    </div>
                </div>
            </div>`;

          document.body.insertAdjacentHTML('beforeend', modalHtml);
        })
        .catch(error => {
          console.error('Error loading service history:', error);
          alert('Error loading service history');
        });
    }

    function showAllServiceHistory() {
      fetch('get_service_history_abm.php')
        .then(response => response.json())
        .then(history => {
          const modalHtml = `
            <div id="modal">
                <div class="modal-content">
                    <h2>
                      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                      </svg>
                      Complete Service History
                    </h2>
                    
                    ${history.length > 0 ? `
                    <table class="history-table">
                        <thead>
                            <tr>
                                <th>Asset ID</th>
                                <th>Asset Name</th>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Technician</th>
                                <th>Status</th>
                                <th>Completed</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${history.map(record => {
                                const asset = inventoryData.find(item => item.asset_id === record.asset_id);
                                return `
                                <tr>
                                    <td>${record.asset_id || 'N/A'}</td>
                                    <td>${asset?.asset_name || 'N/A'}</td>
                                    <td>${record.service_date || 'N/A'}</td>
                                    <td>${record.service_type || 'N/A'}</td>
                                    <td>${record.service_by || 'N/A'}</td>
                                    <td>${record.status || 'N/A'}</td>
                                    <td>${record.completion_date || 'N/A'}</td>
                                    <td>${record.service_notes || ''}</td>
                                </tr>`;
                            }).join('')}
                        </tbody>
                    </table>
                    ` : '<p>No service history records found.</p>'}
                    
                    <div class="button-group">
                        <button type="button" onclick="closeModal()" class="btn btn-secondary">Close</button>
                    </div>
                </div>
            </div>`;

          document.body.insertAdjacentHTML('beforeend', modalHtml);
        })
        .catch(error => {
          console.error('Error loading service history:', error);
          alert('Error loading service history');
        });
    }

    function closeModal() {
      const modal = document.getElementById('modal');
      if (modal) modal.remove();
    }

    function generateAssetId() {
      // Find the highest existing ABMIT- number
      const abmAssets = inventoryData.filter(item => item.asset_id?.startsWith('ABMIT-'));
      let maxNumber = 0;

      abmAssets.forEach(item => {
        const num = parseInt(item.asset_id.split('-')[1]);
        if (num > maxNumber) {
          maxNumber = num;
        }
      });

      // Increment and format with leading zeros
      const nextNumber = (maxNumber + 1).toString().padStart(3, '0');
      return `ABMIT-${nextNumber}`;
    }

    function showLoading(show) {
      if (show) {
        const loadingDiv = document.createElement('div');
        loadingDiv.id = 'loading';
        loadingDiv.innerHTML = '<div class="loading-spinner"></div>';
        document.body.appendChild(loadingDiv);
      } else {
        const loading = document.getElementById('loading');
        if (loading) loading.remove();
      }
    }
  </script>
</body>

</html>