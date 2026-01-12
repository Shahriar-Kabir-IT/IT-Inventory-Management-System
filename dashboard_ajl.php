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
    :root {
      --primary-color: #4e73df;
      --secondary-color: #858796;
      --success-color: #1cc88a;
      --info-color: #36b9cc;
      --warning-color: #f6c23e;
      --danger-color: #e74a3b;
      --dark-color: #5a5c69;
      --light-color: #f8f9fc;
      --border-color: #e3e6f0;
      --text-color: #5a5c69;
      --heading-color: #2c3e50;
      --bg-color: #f8f9fc;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    html, body {
      height: 100%;
      margin: 0;
      padding: 0;
      overflow-x: hidden;
    }

    body {
      font-family: 'Nunito', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
      background: var(--bg-color);
      color: var(--text-color);
      display: flex;
      flex-direction: column;
      min-height: 100vh;
      font-size: 0.9rem;
    }

    .container {
      flex: 1;
      display: flex;
      flex-direction: column;
      min-height: 0;
      width: 100%;
      margin: 0 auto;
      background: white;
    }

    .header {
      background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
      color: white;
      padding: 20px 30px;
      box-shadow: 0 4px 6px rgba(0,0,0,0.1);
      z-index: 20;
    }

    .header-inner {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 24px;
    }

    .brand {
      display: flex;
      flex-direction: column;
      min-width: 240px;
    }

    .header h1 {
      font-size: 1.35rem;
      margin: 0;
      font-weight: 800;
      letter-spacing: 0.2px;
      line-height: 1.2;
    }

    .header .brand p {
      font-size: 0.85rem;
      opacity: 0.85;
      font-weight: 400;
      margin: 3px 0 0;
    }

    .user-info {
      display: flex;
      flex-direction: column;
      align-items: flex-end;
      text-align: right;
      gap: 2px;
      color: rgba(255, 255, 255, 0.95);
      font-size: 0.85rem;
      line-height: 1.2;
    }

    .user-info p { margin: 0; }
    .user-info a { color: rgba(255, 255, 255, 0.95); text-decoration: underline; text-underline-offset: 2px; }
    .user-info a:hover { color: #fff; }

    .stats {
      padding: 20px 30px;
      background: var(--light-color);
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 20px;
      border-bottom: 1px solid var(--border-color);
    }

    .stat-card {
      background: white;
      padding: 20px;
      border-radius: 8px;
      border-left: 4px solid var(--primary-color);
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
      transition: transform 0.2s ease, box-shadow 0.2s ease;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    .stat-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .stat-card:nth-child(1) { border-left-color: var(--success-color); }
    .stat-card:nth-child(2) { border-left-color: var(--warning-color); }
    .stat-card:nth-child(3) { border-left-color: var(--danger-color); }
    .stat-card:nth-child(4) { border-left-color: var(--info-color); }

    .stat-number {
      font-size: 1.5rem;
      font-weight: 800;
      margin-bottom: 5px;
      color: var(--heading-color);
    }

    .stat-label {
      color: var(--secondary-color);
      font-size: 0.8rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .controls {
      padding: 20px 30px;
      background: white;
      border-bottom: 1px solid var(--border-color);
      display: flex;
      gap: 15px;
      flex-wrap: wrap;
      align-items: center;
      justify-content: flex-start;
    }

    .btn {
      padding: 10px 16px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-weight: 600;
      font-size: 0.9rem;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      transition: all 0.2s;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .btn:hover {
      transform: translateY(-1px);
      box-shadow: 0 4px 8px rgba(0,0,0,0.15);
      opacity: 0.95;
    }

    .btn:active {
      transform: translateY(0);
    }

    .btn-primary { background: var(--primary-color); color: white; }
    .btn-success { background: var(--success-color); color: white; }
    .btn-danger { background: var(--danger-color); color: white; }
    .btn-warning { background: var(--warning-color); color: white; }
    .btn-info { background: var(--info-color); color: white; }
    .btn-secondary { background: var(--secondary-color); color: white; }

    .filter-group {
      display: flex;
      gap: 10px;
      align-items: center;
      font-size: 0.9rem;
      background: #f1f3f9;
      padding: 5px 10px;
      border-radius: 6px;
    }

    .filter-group label {
      font-weight: 600;
      color: var(--dark-color);
    }

    .filter-group select,
    .filter-group input {
      padding: 8px 12px;
      border: 1px solid #d1d3e2;
      border-radius: 4px;
      font-size: 0.9rem;
      min-width: 150px;
      background: white;
    }

    .filter-group input:focus, .filter-group select:focus {
      outline: none;
      border-color: var(--primary-color);
      box-shadow: 0 0 0 2px rgba(78, 115, 223, 0.2);
    }

    .table-container {
      flex: 1;
      overflow: auto;
      padding: 0 30px 20px 30px;
    }

    table {
      width: 100%;
      border-collapse: separate;
      border-spacing: 0;
      background: white;
      font-size: 0.9rem;
      box-shadow: 0 0 20px rgba(0,0,0,0.05);
      border-radius: 8px 8px 0 0;
      overflow: hidden;
    }

    th {
      background: #f1f3f9;
      padding: 15px 12px;
      text-align: left;
      font-weight: 700;
      color: var(--primary-color);
      text-transform: uppercase;
      font-size: 0.8rem;
      letter-spacing: 0.5px;
      border-bottom: 2px solid var(--border-color);
      position: sticky;
      top: 0;
      z-index: 10;
    }

    td {
      padding: 12px 12px;
      border-bottom: 1px solid var(--border-color);
      color: #555;
      vertical-align: middle;
    }

    tr:last-child td {
      border-bottom: none;
    }

    tr:hover {
      background-color: #fafbfc;
    }

    .status {
      padding: 6px 12px;
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: 700;
      text-transform: uppercase;
      display: inline-block;
      text-align: center;
      min-width: 80px;
    }

    .status-active { background: #e6fffa; color: #047481; border: 1px solid #b2f5ea; }
    .status-inactive { background: #fffaf0; color: #9c4221; border: 1px solid #fbd38d; }
    .status-out-of-order { background: #fff5f5; color: #c53030; border: 1px solid #feb2b2; }
    .status-maintenance { background: #ebf8ff; color: #2c5282; border: 1px solid #bee3f8; }

    .priority-high { color: var(--danger-color); font-weight: 700; }
    .priority-medium { color: var(--warning-color); font-weight: 700; }
    .priority-low { color: var(--success-color); font-weight: 700; }

    /* Modal Styles */
    #modal {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.6);
      backdrop-filter: blur(2px);
      display: flex;
      justify-content: center;
      align-items: center;
      z-index: 1000;
    }

    .modal-content {
      background: white;
      padding: 30px;
      border-radius: 10px;
      width: 90%;
      max-width: 800px;
      max-height: 85vh;
      overflow-y: auto;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
      position: relative;
    }

    .modal-close {
      position: absolute;
      top: 15px;
      right: 20px;
      font-size: 1.5rem;
      cursor: pointer;
      background: none;
      border: none;
      color: #aaa;
      transition: color 0.2s;
    }

    .modal-close:hover { color: var(--danger-color); }

    .modal-content h2 {
      font-size: 1.5rem;
      margin-bottom: 25px;
      color: var(--heading-color);
      border-bottom: 1px solid var(--border-color);
      padding-bottom: 15px;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .modal-content label {
      font-weight: 600;
      color: var(--text-color);
      margin-bottom: 8px;
      display: block;
    }

    .modal-content input,
    .modal-content select,
    .modal-content textarea {
      width: 100%;
      padding: 12px;
      border: 1px solid var(--border-color);
      border-radius: 6px;
      margin-bottom: 20px;
      font-size: 0.95rem;
      transition: border-color 0.2s;
    }

    .modal-content input:focus,
    .modal-content select:focus,
    .modal-content textarea:focus {
      border-color: var(--primary-color);
      outline: none;
    }
    
    .modal-content textarea {
      height: 100px;
      resize: vertical;
    }

    .button-group {
      display: flex;
      justify-content: flex-end;
      gap: 15px;
      margin-top: 10px;
      border-top: 1px solid var(--border-color);
      padding-top: 20px;
    }
    
    .modal-content button {
      padding: 10px 20px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-weight: 600;
      transition: all 0.2s;
      font-size: 0.9rem;
    }

    /* Scrollbar Styling */
    ::-webkit-scrollbar {
      width: 8px;
      height: 8px;
    }
    ::-webkit-scrollbar-track {
      background: #f1f1f1;
    }
    ::-webkit-scrollbar-thumb {
      background: #ccc;
      border-radius: 4px;
    }
    ::-webkit-scrollbar-thumb:hover {
      background: #aaa;
    }
    
    .notification-badge {
      position: relative;
      display: inline-block;
    }

    .notification-count {
      position: absolute;
      top: -6px;
      right: -6px;
      background-color: var(--danger-color);
      color: white;
      border-radius: 50%;
      padding: 2px 5px;
      font-size: 0.75rem;
      min-width: 18px;
      text-align: center;
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
    
    .approval-notice {
      background-color: #d4edda;
      padding: 10px;
      border-radius: 5px;
      margin-bottom: 15px;
      text-align: center;
      font-size: 0.9rem;
      color: #155724;
      border: 1px solid #c3e6cb;
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

    @media (max-width: 768px) {
      .header { padding: 15px; }
      .header-inner { flex-direction: column; align-items: flex-start; }
      .user-info { align-items: flex-start; text-align: left; width: 100%; }
      .controls { flex-direction: column; align-items: stretch; gap: 10px; padding: 15px; }
      .filter-group { justify-content: space-between; }
      .btn { width: 100%; justify-content: center; }
      .stats { grid-template-columns: 1fr 1fr; gap: 10px; padding: 15px; }
      .table-container { padding: 0 15px; }
      th, td { padding: 8px 4px; font-size: 11px; }
    }
  </style>
</head>

<body>
  <div class="container">
    <div class="header">
      <div class="header-inner">
        <div class="brand">
          <h1>IT Asset Inventory Management</h1>
          <p>Ananta Jeanswear Limited</p>
        </div>
        <div class="user-info">
          <p>Welcome, <?php echo htmlspecialchars($current_user['name'] ?? 'User'); ?></p>
          <p><?php echo strtoupper($current_user['factory'] ?? ''); ?> - <?php echo ucfirst($current_user['user_type'] ?? ''); ?></p>
          <a href="logout.php">Logout</a>
          <div style="margin-top: 5px;">
            <a href="#" onclick="showNotifications()">
              <span class="notification-badge">🔔 <span id="notificationCount" class="notification-count">0</span></span>
            </a>
          </div>
        </div>
      </div>
    </div>

    <div class="stats">
      <div class="stat-card">
        <div class="stat-number" style="color: #28a745;" id="activeCount">0</div>
        <div class="stat-label">Active Assets</div>
      </div>
      <div class="stat-card">
        <div class="stat-number" style="color: #ffc107;" id="inactiveCount">0</div>
        <div class="stat-label">Inactive Assets</div>
      </div>
      <div class="stat-card">
        <div class="stat-number" style="color: #dc3545;" id="outOfOrderCount">0</div>
        <div class="stat-label">Out of Order</div>
      </div>
      <div class="stat-card">
        <div class="stat-number" style="color: #17a2b8;" id="maintenanceCount">0</div>
        <div class="stat-label">Under Maintenance</div>
      </div>
    </div>

    <div class="controls">
      <button class="btn btn-success" id="addItemBtn">➕ Add New Item</button>
      <button class="btn btn-warning" id="serviceBtn">🔧 Send for Servicing</button>
      <button class="btn btn-danger" id="removeBtn">🗑️ Remove Item</button>
      <button class="btn btn-primary" id="exportBtn">⬇️ Export</button>
      <button class="btn btn-info" id="historyBtn">📜 View All Service History</button>

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
    let currentPage = 1;
    const rowsPerPage = 10;

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
                <span class="modal-close" onclick="closeModal()">&times;</span>
                <h2>🔔 Notifications</h2>
                
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
                  <button type="button" onclick="closeModal()" class="btn btn-primary">
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

    // Original functions remain unchanged below this point
async function loadDataFromDB() {
  try {
    showLoading(true);
    const response = await fetch('get_assets_ajl.php');
    
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
        updatePaginationControls();
        return;
      }

      const startIndex = (currentPage - 1) * rowsPerPage;
      const endIndex = Math.min(startIndex + rowsPerPage, filteredData.length);
      const dataToRender = filteredData.slice(startIndex, endIndex);

      dataToRender.forEach(item => {
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
              `<button onclick="showCompleteServiceModal('${item.asset_id}')" class="btn-success" style="padding:5px 10px; margin-bottom:5px;">
                Complete Service
              </button><br>` : ''
            }
            <button onclick="showServiceHistory('${item.asset_id}')" class="btn-info" style="padding:5px 10px;">
              Service History
            </button>
          </td>
        `;
        tbody.appendChild(row);
      });

      updatePaginationControls();
    }

    function updatePaginationControls() {
      const totalPages = Math.ceil(filteredData.length / rowsPerPage);
      const paginationContainer = document.getElementById('paginationControls');
      
      if (!paginationContainer) {
        const container = document.createElement('div');
        container.id = 'paginationControls';
        container.className = 'pagination-controls';
        container.style.display = 'flex';
        container.style.justifyContent = 'center';
        container.style.alignItems = 'center';
        container.style.marginTop = '20px';
        container.style.gap = '15px';
        document.querySelector('.table-container').after(container);
        
        updatePaginationControls(); // Re-run to populate
        return;
      }
      
      paginationContainer.innerHTML = `
        <button onclick="changePage(-1)" class="btn btn-secondary" ${currentPage === 1 ? 'disabled' : ''}>Previous</button>
        <span>Page ${currentPage} of ${totalPages || 1} (${filteredData.length} items)</span>
        <button onclick="changePage(1)" class="btn btn-secondary" ${currentPage === totalPages || totalPages === 0 ? 'disabled' : ''}>Next</button>
      `;
    }

    function changePage(delta) {
      const totalPages = Math.ceil(filteredData.length / rowsPerPage);
      const newPage = currentPage + delta;
      
      if (newPage >= 1 && newPage <= totalPages) {
        currentPage = newPage;
        renderTable();
      }
    }

    function getStatusClass(status) {
      if (!status) return '';
      switch (status.toUpperCase()) {
        case 'ACTIVE': return 'status-active';
        case 'INACTIVE': return 'status-inactive';
        case 'OUT OF ORDER': return 'status-out-of-order';
        case 'MAINTENANCE': return 'status-maintenance';
        default: return '';
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
      
      currentPage = 1; // Reset to first page
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
      const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
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
          <span class="modal-close" onclick="closeModal()">&times;</span>
          <h2>➕ Add New Asset</h2>
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
              <button type="submit" class="btn-success">Add Asset</button>
              <button type="button" onclick="closeModal()" class="btn-primary">Cancel</button>
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
        data.factory = 'ajl';
        data.asset_id = generateAssetId();
        
        const response = await fetch('request_approval_ajl.php', {
          method: 'POST',
          headers: {'Content-Type': 'application/json'},
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

      let options = inventoryData
        .filter(item => ['ACTIVE', 'OUT OF ORDER'].includes(item.status?.toUpperCase()))
        .map(item => `<option value="${item.asset_id}">${item.asset_id} - ${item.asset_name} (${item.assigned_to})</option>`)
        .join('');
      
      const modalHtml = `
      <div id="modal">
        <div class="modal-content">
          <span class="modal-close" onclick="closeModal()">&times;</span>
          <h2>🔧 Send Asset for Servicing</h2>
          <div class="approval-notice">
            <strong>Note:</strong> All service requests require approval from the admin.
          </div>
          <div>
            <label for="serviceAsset">Select Asset to Service:</label>
            <select id="serviceAsset">
              <option value="">Choose an asset...</option>
              ${options}
            </select>
          </div>
          
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
            <button type="button" onclick="submitService()" class="btn-warning">Send for Service</button>
            <button type="button" onclick="closeModal()" class="btn-primary">Cancel</button>
          </div>
        </div>
      </div>`;
      
      document.body.insertAdjacentHTML('beforeend', modalHtml);
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
        data.factory = 'ajl';
        
        const response = await fetch('request_approval_ajl.php', {
          method: 'POST',
          headers: {'Content-Type': 'application/json'},
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
          <span class="modal-close" onclick="closeModal()">&times;</span>
          <h2>✔ Complete Service for ${assetId}</h2>
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
            <button type="button" onclick="completeService('${assetId}')" class="btn-success">Mark as Completed</button>
            <button type="button" onclick="closeModal()" class="btn-primary">Cancel</button>
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
          factory: 'ajl',
          asset_id: assetId,
          completion_notes: completionNotes
        };
        
        const response = await fetch('request_approval_ajl.php', {
          method: 'POST',
          headers: {'Content-Type': 'application/json'},
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

      let options = inventoryData
        .map(item => `<option value="${item.asset_id}">${item.asset_id} - ${item.asset_name} (${item.status})</option>`)
        .join('');
      
      const modalHtml = `
      <div id="modal">
        <div class="modal-content">
          <span class="modal-close" onclick="closeModal()">&times;</span>
          <h2>🗑️ Remove Asset from Inventory</h2>
          <div class="approval-notice">
            <strong>Note:</strong> All removal requests require approval from the admin.
          </div>
          <div>
            <label for="removeAsset">Select Asset to Remove:</label>
            <select id="removeAsset">
              <option value="">Choose an asset...</option>
              ${options}
            </select>
          </div>
          
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
            <button type="button" onclick="submitRemove()" class="btn-danger">Remove Asset</button>
            <button type="button" onclick="closeModal()" class="btn-primary">Cancel</button>
          </div>
        </div>
      </div>`;
      
      document.body.insertAdjacentHTML('beforeend', modalHtml);
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
        data.factory = 'ajl';
        
        const response = await fetch('request_approval_ajl.php', {
          method: 'POST',
          headers: {'Content-Type': 'application/json'},
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
      fetch(`get_service_history_ajl.php?asset_id=${assetId}`)
        .then(response => response.json())
        .then(history => {
            const asset = inventoryData.find(item => item.asset_id === assetId);
            const modalHtml = `
            <div id="modal">
                <div class="modal-content">
                    <h2>📜 Service History for ${assetId}</h2>
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
                        <button type="button" onclick="closeModal()" class="btn-primary">Close</button>
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
      fetch('get_service_history_ajl.php')
        .then(response => response.json())
        .then(history => {
            const modalHtml = `
            <div id="modal">
                <div class="modal-content">
                    <h2>📜 Complete Service History</h2>
                    
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
                        <button type="button" onclick="closeModal()" class="btn-primary">Close</button>
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
    const abmAssets = inventoryData.filter(item => item.asset_id?.startsWith('AJLIT-'));
    let maxNumber = 0;
    
    abmAssets.forEach(item => {
        const num = parseInt(item.asset_id.split('-')[1]);
        if (num > maxNumber) {
            maxNumber = num;
        }
    });
    
    // Increment and format with leading zeros
    const nextNumber = (maxNumber + 1).toString().padStart(3, '0');
    return `AJLIT-${nextNumber}`;
}

    function showLoading(show) {
      const loading = document.getElementById('loading');
      if (show) {
        if (!loading) {
          const div = document.createElement('div');
          div.id = 'loading';
          div.style.position = 'fixed';
          div.style.top = '0';
          div.style.left = '0';
          div.style.width = '100%';
          div.style.height = '100%';
          div.style.backgroundColor = 'rgba(0,0,0,0.5)';
          div.style.display = 'flex';
          div.style.justifyContent = 'center';
          div.style.alignItems = 'center';
          div.style.zIndex = '9999';
          div.innerHTML = '<div style="background: white; padding: 20px; border-radius: 5px;">Loading...</div>';
          document.body.appendChild(div);
        }
      } else {
        if (loading) loading.remove();
      }
    }
  </script>
</body>
</html>
