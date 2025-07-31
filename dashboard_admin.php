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

// Check if user is super admin
$is_super_admin = ($current_user['user_type'] === 'super_admin');
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
}

html, body {
  height: 100%;
  margin: 0;
  padding: 0;
  overflow-x: hidden;
}

body {
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  background: #f5f5f5;
  display: flex;
  flex-direction: column;
  min-height: 100vh;
  font-size: 14px;
}

.container {
  flex: 1;
  display: flex;
  flex-direction: column;
  min-height: 0;
  width: 100%;
  margin: 0 auto;
  background: white;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.header {
  background: #2c3e50;
  color: white;
  padding: 15px;
  text-align: center;
}

.header h1 {
  font-size: 1.8rem;
  margin-bottom: 5px;
  font-weight: 300;
}

.header p {
  font-size: 1rem;
  opacity: 0.9;
}

.controls {
  padding: 10px;
  background: #f8f9fa;
  border-bottom: 1px solid #e9ecef;
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
  align-items: center;
}

.btn {
  padding: 8px 12px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-weight: 500;
  font-size: 0.85rem;
  display: inline-flex;
  align-items: center;
  gap: 5px;
}

.btn-primary {
  background: #4e73df;
  color: white;
}

.btn-success {
  background: #1cc88a;
  color: white;
}

.btn-danger {
  background: #e74a3b;
  color: white;
}

.btn-warning {
  background: #f6c23e;
  color: #2c3e50;
}

.btn-info {
  background: #36b9cc;
  color: white;
}

.btn-secondary {
  background: #858796;
  color: white;
}

.btn:hover {
  opacity: 0.9;
}

.filter-group {
  display: flex;
  gap: 5px;
  align-items: center;
  font-size: 0.9rem;
}

.filter-group select,
.filter-group input {
  padding: 8px;
  border: 1px solid #ddd;
  border-radius: 3px;
  font-size: 0.9rem;
  min-width: 120px;
}

.table-container {
  flex: 1;
  overflow: auto;
  position: relative;
}

table {
  width: 100%;
  border-collapse: collapse;
  background: white;
  font-size: 0.85rem;
}

th {
  background: #f8f9fa;
  padding: 10px 8px;
  text-align: left;
  font-weight: 600;
  color: #495057;
  border-bottom: 1px solid #dee2e6;
  position: sticky;
  top: 0;
  z-index: 10;
}

td {
  padding: 8px;
  border-bottom: 1px solid #f1f3f4;
}

tr:hover {
  background-color: #f8f9ff;
}

.status {
  padding: 4px 8px;
  border-radius: 10px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.status-active {
  background: #d4edda;
  color: #155724;
}

.status-inactive {
  background: #fff3cd;
  color: #856404;
}

.status-out-of-order {
  background: #f8d7da;
  color: #721c24;
}

.status-maintenance {
  background: #d1ecf1;
  color: #0c5460;
}

.priority-high {
  color: #dc3545;
  font-weight: bold;
}

.priority-medium {
  color: #ffc107;
  font-weight: bold;
}

.priority-low {
  color: #28a745;
  font-weight: bold;
}

.stats {
  padding: 10px;
  background: #f8f9fa;
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
  gap: 10px;
  flex-shrink: 0;
}

.stat-card {
  background: white;
  padding: 10px;
  border-radius: 5px;
  text-align: center;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
}

.stat-card:hover {
  transform: translateY(-5px);
}

.stat-number {
  font-size: 1.2rem;
  font-weight: bold;
  margin-bottom: 3px;
}

.stat-label {
  color: #6c757d;
  font-size: 0.9rem;
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  display: none;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.modal-content {
  background: white;
  padding: 15px;
  border-radius: 5px;
  width: 95%;
  max-width: 900px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
  position: relative;
}

#approvalModal .modal-content {
  max-width: 950px;
}

.modal-close {
  position: absolute;
  top: 5px;
  right: 5px;
  font-size: 1.2rem;
  cursor: pointer;
  background: none;
  border: none;
  color: #6c757d;
}

.modal-close:hover {
  color: #000;
}

.modal-content h2 {
  font-size: 1.5rem;
  margin-bottom: 20px;
  color: #2c3e50;
  display: flex;
  align-items: center;
  gap: 10px;
}

.modal-content label {
  display: block;
  margin-bottom: 8px;
  font-weight: 600;
  color: #495057;
}

.modal-content input,
.modal-content select,
.modal-content textarea {
  width: 100%;
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 5px;
  margin-bottom: 15px;
  font-size: 14px;
  transition: border-color 0.3s;
}

.modal-content input:focus,
.modal-content select:focus,
.modal-content textarea:focus {
  border-color: #4e73df;
  outline: none;
}

.modal-content textarea {
  height: 80px;
  resize: vertical;
}

.button-group {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  margin-top: 20px;
}

.warning-box {
  background: #f8d7da;
  padding: 15px;
  border-radius: 5px;
  border-left: 4px solid #dc3545;
  margin: 15px 0;
  color: #721c24;
}

.info-box {
  background: #e2f3f8;
  padding: 15px;
  border-radius: 5px;
  border-left: 4px solid #17a2b8;
  margin: 15px 0;
  color: #0c5460;
}

.history-table {
  width: 100%;
  border-collapse: collapse;
  margin: 15px 0;
}

.history-table th,
.history-table td {
  padding: 10px;
  border: 1px solid #ddd;
  text-align: left;
}

.history-table th {
  background-color: #f8f9fa;
}

.history-table tr:nth-child(even) {
  background-color: #f9f9f9;
}

.user-info {
  text-align: right;
  color: white;
  padding: 10px;
}

.user-info a {
  color: white;
  text-decoration: underline;
}

.user-management-container {
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  background: white;
  padding: 30px;
  border-radius: 10px;
  box-shadow: 0 5px 30px rgba(0, 0, 0, 0.3);
  z-index: 1000;
  width: 90%;
  max-width: 800px;
  max-height: 90vh;
  overflow-y: auto;
  display: none;
}

.user-management-close {
  position: absolute;
  top: 15px;
  right: 15px;
  font-size: 1.5rem;
  cursor: pointer;
  background: none;
  border: none;
  color: #6c757d;
  transition: color 0.3s;
}

.user-management-close:hover {
  color: #000;
}

.user-form {
  margin-top: 20px;
}

.user-form label {
  display: block;
  margin-bottom: 5px;
  font-weight: 600;
}

.user-form input,
.user-form select {
  width: 100%;
  padding: 10px;
  margin-bottom: 15px;
  border: 1px solid #ddd;
  border-radius: 5px;
}

.user-list {
  margin-top: 20px;
}

.user-list table {
  width: 100%;
  border-collapse: collapse;
}

.user-list th,
.user-list td {
  padding: 10px;
  border: 1px solid #ddd;
  text-align: left;
}

.user-list th {
  background-color: #f8f9fa;
}

.action-buttons {
  display: flex;
  flex-direction: column;
  gap: 40px;
  padding: 10px 0;
}

.loading-spinner {
  display: inline-block;
  width: 20px;
  height: 20px;
  border: 3px solid rgba(255,255,255,.3);
  border-radius: 50%;
  border-top-color: #fff;
  animation: spin 1s ease-in-out infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.approval-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 0.85rem;
}

.approval-table th {
  background: #f8f9fa;
  padding: 10px 12px;
  font-weight: 600;
  text-align: left;
  white-space: nowrap;
}

.approval-table td {
  padding: 10px 12px;
  border-bottom: 1px solid #eee;
  vertical-align: top;
}

.compact-details {
  max-width: 300px;
  font-size: 0.85rem;
  line-height: 1.5;
}

.detail-line {
  white-space: normal;
  overflow: visible;
  text-overflow: clip;
  margin-bottom: 4px;
}

.detail-line strong {
  display: inline-block;
  width: 110px;
  color: #666;
}

.badge {
  padding: 3px 6px;
  border-radius: 3px;
  font-size: 11px;
  font-weight: 600;
}

.badge-add {
  background: #d4edda;
  color: #155724;
}

.badge-service {
  background: #cce5ff;
  color: #004085;
}

.table-responsive {
  overflow-x: auto;
  max-width: 100%;
}

.approval-action-btn {
  min-width: 80px;
  white-space: nowrap;
}

@media (max-width: 768px) {
  .controls {
    gap: 5px;
  }

  .filter-group {
    justify-content: space-between;
  }

  th,
  td {
    padding: 8px 4px;
    font-size: 11px;
  }

  .button-group {
    flex-direction: column;
  }

  .btn {
    padding: 6px 8px;
    font-size: 0.7rem;
  }
  
  .history-table {
    width: 100%;
    border-collapse: collapse;
    margin: 15px 0;
    font-size: 0.9em;
  }

  .history-table th,
  .history-table td {
    padding: 8px 12px;
    border: 1px solid #ddd;
    text-align: left;
  }

  .history-table th {
    background-color: #f8f9fa;
    position: sticky;
    top: 0;
  }

  .history-table tr:nth-child(even) {
    background-color: #f9f9f9;
  }

  .history-table tr:hover {
    background-color: #f1f1f1;
  }

  .error {
    color: #dc3545;
    font-weight: bold;
  }
}
  </style>
</head>

<body>
  <div class="container">
    <div class="header">
      <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
          <h1>                     IT Asset Inventory</h1>
          <p>Ananta Companies Head</p>
        </div>
        <div style="color: white; text-align: right; padding: 10px;">
          <p>Welcome, <?php echo htmlspecialchars($current_user['name']); ?></p>
          <p><?php echo strtoupper($current_user['factory']); ?> - <?php echo ucfirst($current_user['user_type']); ?></p>
          <a href="logout.php" style="color: white; text-decoration: underline;">Logout</a>
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
      <button class="btn btn-info" id="historyBtn"><i class="btn-icon">📜</i> Service History</button>
      <button class="btn btn-secondary" id="viewDeletedBtn"><i class="btn-icon">🗑️</i> Deleted Assets</button>

      <div class="filter-group">
        <label>Factory:</label>
        <select id="factoryFilter">
          <option value="all">Head Office</option>
          <option value="agl">AGL</option>
          <option value="ajl">AJL & PWPL</option>
          <option value="abm">ABM</option>
        </select>
      </div>

      <div class="filter-group">
        <label>Status:</label>
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
            <th id="actionsHeader">Actions</th>
          </tr>
        </thead>
        <tbody id="inventoryBody"></tbody>
      </table>
    </div>
  </div>

  <!-- Modal Overlay (for all modals) -->
  <div id="modalOverlay" class="modal-overlay">
    <div class="modal-content">
      <button class="modal-close">&times;</button>
      <div id="modalContent"></div>
    </div>
  </div>

  <!-- Deleted Assets Modal -->
  <div id="deletedAssetsModal" class="modal-overlay">
    <div class="modal-content">
      <button class="modal-close" onclick="closeDeletedAssetsModal()">&times;</button>
      <h2><i class="btn-icon">🗑️</i> Deleted Assets</h2>

      <div class="filter-group" style="margin-bottom: 20px;">
        <label>Factory:</label>
        <select id="deletedFactoryFilter">
          <option value="head_office">Head Office</option>
          <option value="agl">AGL</option>
          <option value="ajl">AJL & PWPL</option>
          <option value="abm">ABM</option>
        </select>
      </div>

      <div id="deletedAssetsContent">
        <p>Select a factory and click "Load Data" to view deleted assets.</p>
      </div>

      <div class="button-group">
        <button type="button" onclick="loadDeletedAssets()" class="btn btn-primary">
          <i class="btn-icon">🔍</i> Load Data
        </button>
        <button type="button" onclick="closeDeletedAssetsModal()" class="btn btn-secondary">
          <i class="btn-icon">✕</i> Close
        </button>
      </div>
    </div>
  </div>

  <script>
    let inventoryData = [];
    let filteredData = [];
    let currentUserType = "<?php echo $current_user['user_type']; ?>";
    let currentFactory = "<?php echo $current_user['factory']; ?>";

    // DOM Elements
    const historyBtn = document.getElementById('historyBtn');
    const viewDeletedBtn = document.getElementById('viewDeletedBtn');
    const factoryFilter = document.getElementById('factoryFilter');
    const statusFilter = document.getElementById('statusFilter');
    const searchInput = document.getElementById('searchInput');
    const modalOverlay = document.getElementById('modalOverlay');
    const modalContent = document.getElementById('modalContent');
    const modalClose = document.querySelector('.modal-close');
    const deletedAssetsModal = document.getElementById('deletedAssetsModal');
    const deletedFactoryFilter = document.getElementById('deletedFactoryFilter');
    const deletedAssetsContent = document.getElementById('deletedAssetsContent');

    // Event Listeners
    historyBtn.addEventListener('click', showAllServiceHistory);
    viewDeletedBtn.addEventListener('click', showDeletedAssetsModal);
    factoryFilter.addEventListener('change', () => {
      loadDataFromDB();
    });
    statusFilter.addEventListener('change', filterTable);
    searchInput.addEventListener('keyup', filterTable);
    modalClose.addEventListener('click', closeModal);

    // Close modal when clicking outside content
    modalOverlay.addEventListener('click', (e) => {
      if (e.target === modalOverlay) {
        closeModal();
      }
    });

    deletedAssetsModal.addEventListener('click', (e) => {
      if (e.target === deletedAssetsModal) {
        closeDeletedAssetsModal();
      }
    });

    // Load data when page loads
    document.addEventListener('DOMContentLoaded', () => {
      loadDataFromDB();
    });

    function showModal(content) {
      modalContent.innerHTML = content;
      modalOverlay.style.display = 'flex';
    }

    function closeModal() {
      modalOverlay.style.display = 'none';
    }

    function showDeletedAssetsModal() {
      deletedAssetsModal.style.display = 'flex';
      deletedAssetsContent.innerHTML = '<p>Select a factory and click "Load Data" to view deleted assets.</p>';
    }

    function closeDeletedAssetsModal() {
      deletedAssetsModal.style.display = 'none';
    }

    function loadDeletedAssets() {
      const factory = deletedFactoryFilter.value;
      showLoading(true);

      fetch(`get_deleted_assets.php?factory=${factory}`)
        .then(response => response.json())
        .then(data => {
          if (data.error) {
            deletedAssetsContent.innerHTML = `<p class="error">${data.error}</p>`;
            return;
          }

          if (data.length === 0) {
            deletedAssetsContent.innerHTML = '<p>No deleted assets found for this factory.</p>';
            return;
          }

          let html = `
            <div class="info-box">
              Showing ${data.length} deleted assets for ${factory === 'head_office' ? 'Head Office' : factory.toUpperCase()}
            </div>
            <table class="history-table">
              <thead>
                <tr>
                  <th>Asset Name</th>
                  <th>Category</th>
                  <th>Brand</th>
                  <th>Model</th>
                  <th>Location</th>
                  <th>Assigned To</th>
                  <th>Purchase Date</th>
                  <th>Removal Reason</th>
                  <th>Removal Notes</th>
                  <th>Removed By</th>
                  <th>Removal Date</th>
                </tr>
              </thead>
              <tbody>
          `;

          data.forEach(asset => {
            html += `
              <tr>
                <td>${asset.asset_name || 'N/A'}</td>
                <td>${asset.category || 'N/A'}</td>
                <td>${asset.brand || 'N/A'}</td>
                <td>${asset.model || 'N/A'}</td>
                <td>${asset.location || 'N/A'}</td>
                <td>${asset.assigned_to || 'N/A'}</td>
                <td>${formatDate(asset.purchase_date) || 'N/A'}</td>
                <td>${asset.removal_reason || 'N/A'}</td>
                <td>${asset.removal_notes || 'N/A'}</td>
                <td>${asset.removed_by || 'N/A'}</td>
                <td>${formatDate(asset.removal_date) || 'N/A'}</td>
              </tr>
            `;
          });

          html += `
              </tbody>
            </table>
          `;

          deletedAssetsContent.innerHTML = html;
        })
        .catch(error => {
          console.error('Error loading deleted assets:', error);
          deletedAssetsContent.innerHTML = '<p class="error">Error loading deleted assets. Please try again.</p>';
        })
        .finally(() => {
          showLoading(false);
        });
    }

    async function loadDataFromDB() {
      try {
        showLoading(true);
        const selectedFactory = factoryFilter.value;
        let url = 'get_assets.php';

        if (selectedFactory !== 'all') {
          url = `get_assets_${selectedFactory}.php`;
        }

        const response = await fetch(url);

        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();

        if (data.error) {
          throw new Error(data.error);
        }

        inventoryData = data;
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

      const selectedFactory = factoryFilter.value;
      const isHeadOffice = selectedFactory === 'all';

      filteredData.forEach(item => {
        const row = document.createElement('tr');

        // Common columns
        let rowHtml = `
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
        `;

        // Only show Actions column for Head Office
        if (isHeadOffice) {
          rowHtml += `
                <td>
                    <button onclick="showServiceHistory('${item.asset_id}')" class="btn btn-info btn-sm">
                        <i class="btn-icon">📜</i> History
                    </button>
                </td>
            `;
        }

        row.innerHTML = rowHtml;
        tbody.appendChild(row);
      });

      // Also hide the Actions header if not Head Office
      const actionHeader = document.querySelector('#inventoryTable thead tr th:nth-child(17)');
      if (actionHeader) {
        actionHeader.style.display = isHeadOffice ? '' : 'none';
      }
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

    function showServiceHistory(assetId) {
  showLoading(true);
  
  fetch(`get_service_history.php?asset_id=${assetId}`)
    .then(response => {
      if (!response.ok) {
        throw new Error('Network response was not ok');
      }
      return response.json();
    })
    .then(history => {
      const asset = inventoryData.find(item => item.asset_id === assetId);
      let modalHtml = `
        <h2><i class="btn-icon">📜</i> Service History for ${assetId}</h2>
        <div class="info-box">
          <strong>Asset:</strong> ${asset?.asset_name || 'N/A'}<br>
          <strong>Model:</strong> ${asset?.model || 'N/A'}<br>
          <strong>Current Status:</strong> ${asset?.status || 'N/A'}
        </div>`;
      
      if (history.length > 0) {
        modalHtml += `
        <table class="history-table">
          <thead>
            <tr>
              <th>Date</th>
              <th>Type</th>
              <th>Technician</th>
              <th>Status</th>
              <th>Completed</th>
              <th>Notes</th>
              <th>Factory</th>
            </tr>
          </thead>
          <tbody>
            ${history.map(record => `
              <tr>
                <td>${formatDate(record.service_date) || 'N/A'}</td>
                <td>${record.service_type || 'N/A'}</td>
                <td>${record.service_by || 'N/A'}</td>
                <td>${record.status || 'N/A'}</td>
                <td>${formatDate(record.completion_date) || 'N/A'}</td>
                <td>${record.service_notes || ''}</td>
                <td>${record.factory ? record.factory.toUpperCase() : 'N/A'}</td>
              </tr>
            `).join('')}
          </tbody>
        </table>`;
      } else {
        modalHtml += '<p>No service history found for this asset.</p>';
      }
      
      modalHtml += `
        <div class="button-group">
          <button type="button" onclick="closeModal()" class="btn btn-secondary">
            <i class="btn-icon">✕</i> Close
          </button>
        </div>`;
      
      showModal(modalHtml);
    })
    .catch(error => {
      console.error('Error loading service history:', error);
      showModal(`
        <h2><i class="btn-icon">📜</i> Service History Error</h2>
        <div class="error-box">
          <p>Failed to load service history for asset ${assetId}</p>
          <p>Error: ${error.message}</p>
        </div>
        <div class="button-group">
          <button type="button" onclick="closeModal()" class="btn btn-secondary">
            <i class="btn-icon">✕</i> Close
          </button>
        </div>
      `);
    })
    .finally(() => {
      showLoading(false);
    });
}

    function showAllServiceHistory() {
      const modalHtml = `
        <h2><i class="btn-icon">📜</i> Complete Service History</h2>
        
        <div class="filter-group" style="margin-bottom: 20px;">
          <label for="historyFactoryFilter">Factory:</label>
          <select id="historyFactoryFilter" onchange="loadServiceHistory()">
            <option value="head_office">Head Office</option>
            <option value="agl">AGL</option>
            <option value="ajl">AJL & PWPL</option>
            <option value="abm">ABM</option>
            <option value="all">All Factories</option>
          </select>
        </div>
        
        <div id="serviceHistoryContent">
          <p>Loading Head Office service history...</p>
        </div>
        
        <div class="button-group">
          <button type="button" onclick="closeModal()" class="btn btn-secondary">
            <i class="btn-icon">✕</i> Close
          </button>
        </div>
      `;

      showModal(modalHtml);
      // Load Head Office data immediately
      loadServiceHistory();
    }

    function loadServiceHistory() {
      // Get the selected factory or default to head_office
      const factoryFilter = document.getElementById('historyFactoryFilter');
      const factory = factoryFilter ? factoryFilter.value : 'head_office';

      let url = 'get_service_history.php';

      if (factory !== 'all') {
        url += `?factory=${factory}`;
      }

      showLoading(true);
      fetch(url)
        .then(response => response.json())
        .then(history => {
          let historyHtml;

          if (history.length > 0) {
            historyHtml = `
              <div class="info-box">
                Showing ${history.length} service records for ${factory === 'head_office' ? 'Head Office' : factory.toUpperCase()}
              </div>
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
                      <td>${formatDate(record.service_date) || 'N/A'}</td>
                      <td>${record.service_type || 'N/A'}</td>
                      <td>${record.service_by || 'N/A'}</td>
                      <td>${record.status || 'N/A'}</td>
                      <td>${formatDate(record.completion_date) || 'N/A'}</td>
                      <td>${record.service_notes || ''}</td>
                    </tr>`;
                  }).join('')}
                </tbody>
              </table>
            `;
          } else {
            historyHtml = `<p>No service history records found for ${factory === 'head_office' ? 'Head Office' : factory.toUpperCase()}.</p>`;
          }

          document.getElementById('serviceHistoryContent').innerHTML = historyHtml;
          showLoading(false);
        })
        .catch(error => {
          console.error('Error loading service history:', error);
          document.getElementById('serviceHistoryContent').innerHTML =
            '<p class="error">Error loading service history. Please try again.</p>';
          showLoading(false);
        });
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
          div.innerHTML = `
            <div style="background: white; padding: 20px; border-radius: 5px; display: flex; align-items: center; gap: 10px;">
              <div class="loading-spinner"></div>
              <span>Loading...</span>
            </div>
          `;
          document.body.appendChild(div);
        }
      } else {
        if (loading) loading.remove();
      }
    }
  </script>
</body>

</html>