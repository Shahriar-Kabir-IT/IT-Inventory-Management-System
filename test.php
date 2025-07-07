
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

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
      padding: 20px;
    }

    .container {
      max-width: 1400px;
      margin: 0 auto;
      background: white;
      border-radius: 15px;
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
      overflow: hidden;
    }

    .header {
      background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
      color: white;
      padding: 30px;
      text-align: center;
    }

    .header h1 {
      font-size: 2.5rem;
      margin-bottom: 10px;
      font-weight: 300;
    }

    .header p {
      font-size: 1.1rem;
      opacity: 0.9;
    }

    .controls {
      padding: 20px 30px;
      background: #f8f9fa;
      border-bottom: 1px solid #e9ecef;
      display: flex;
      gap: 15px;
      flex-wrap: wrap;
      align-items: center;
    }

    .btn {
      padding: 10px 20px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-weight: 500;
      transition: all 0.3s ease;
      font-size: 14px;
    }

    .btn-primary {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
    }

    .btn-success {
      background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
      color: white;
    }

    .btn-danger {
      background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%);
      color: white;
    }

    .btn-warning {
      background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
      color: black;
    }

    .btn-info {
      background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%);
      color: white;
    }

    .btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }

    .filter-group {
      display: flex;
      gap: 10px;
      align-items: center;
    }

    .filter-group select,
    .filter-group input {
      padding: 8px 12px;
      border: 1px solid #ddd;
      border-radius: 5px;
      font-size: 14px;
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
    }

    th {
      background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
      padding: 15px 8px;
      text-align: left;
      font-weight: 600;
      font-size: 13px;
      color: #495057;
      border-bottom: 2px solid #dee2e6;
      position: sticky;
      top: 0;
      z-index: 10;
    }

    td {
      padding: 12px 8px;
      border-bottom: 1px solid #f1f3f4;
      font-size: 13px;
    }

    tr:hover {
      background-color: #f8f9ff;
    }

    .status {
      padding: 5px 10px;
      border-radius: 20px;
      font-size: 11px;
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
      padding: 20px 30px;
      background: #f8f9fa;
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 20px;
    }

    .stat-card {
      background: white;
      padding: 20px;
      border-radius: 10px;
      text-align: center;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
      transition: transform 0.3s ease;
    }

    .stat-card:hover {
      transform: translateY(-5px);
    }

    .stat-number {
      font-size: 2rem;
      font-weight: bold;
      margin-bottom: 5px;
    }

    .stat-label {
      color: #6c757d;
      font-size: 14px;
    }

    /* Modal styles */
    .modal-overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0,0,0,0.5);
      display: none;
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
      max-height: 90vh;
      overflow-y: auto;
      box-shadow: 0 5px 15px rgba(0,0,0,0.3);
      position: relative;
    }

    .modal-close {
      position: absolute;
      top: 15px;
      right: 15px;
      font-size: 1.5rem;
      cursor: pointer;
      background: none;
      border: none;
      color: #6c757d;
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

    /* User Management Styles */
    .user-management-container {
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      background: white;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 5px 30px rgba(0,0,0,0.3);
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

    .action-buttons button {
      padding: 5px 10px;
      margin-right: 5px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }

    @media (max-width: 768px) {
      .controls {
        flex-direction: column;
        align-items: stretch;
      }

      .filter-group {
        justify-content: space-between;
      }

      th,
      td {
        padding: 8px 4px;
        font-size: 11px;
      }
      .form-control {
  width: 100%;
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 5px;
  margin-bottom: 15px;
  font-size: 14px;
}

.warning-box {
  background: #f8d7da;
  padding: 15px;
  border-radius: 5px;
  border-left: 4px solid #dc3545;
  margin: 15px 0;
  color: #721c24;
}

.button-group {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  margin-top: 20px;
}
    }
  </style>
</head>

<body>
  <div class="container">
    <div class="header">
      <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
          <h1>IT Asset Inventory Management</h1>
          <p>Comprehensive tracking system for IT department assets and equipment</p>
        </div>
        <div class="user-info">
          <p>Welcome, <?php echo htmlspecialchars($current_user['name']); ?></p>
          <p><?php echo strtoupper($current_user['factory']); ?> - <?php echo ucfirst($current_user['user_type']); ?></p>
          <a href="logout.php">Logout</a>
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
      <?php if ($is_admin): ?>
        <button class="btn btn-success" id="addItemBtn">➕ Add New Item</button>
        <button class="btn btn-danger" id="removeBtn">🗑️ Remove Item</button>
        <button class="btn btn-info" id="userManagementBtn">👥 User Management</button>
      <?php endif; ?>
      <button class="btn btn-warning" id="serviceBtn">🔧 Send for Servicing</button>
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

  <!-- Modal Overlay (for all modals) -->
  <div id="modalOverlay" class="modal-overlay">
    <div class="modal-content">
      <button class="modal-close">&times;</button>
      <div id="modalContent"></div>
    </div>
  </div>

  <!-- User Management Modal -->
  <div id="userManagementModal" class="user-management-container">
    <button class="user-management-close">&times;</button>
    <h2>👥 User Management</h2>
    <div class="button-group" style="margin-bottom: 20px;">
      <button class="btn btn-success" id="createUserBtn">Create New User</button>
      <button class="btn btn-primary" id="viewUsersBtn">View All Users</button>
    </div>
    
    <div id="userFormContainer" class="user-form" style="display: none;">
      <form id="userForm">
        <input type="hidden" id="userId">
        <div>
          <label for="name">Full Name*</label>
          <input type="text" id="name" required>
        </div>
        <div>
          <label for="username">Username*</label>
          <input type="text" id="username" required>
        </div>
        <div>
          <label for="password">Password*</label>
          <input type="password" id="password" required>
        </div>
        <div>
          <label for="employeeId">Employee ID*</label>
          <input type="text" id="employeeId" required>
        </div>
        <div>
          <label for="userType">User Type*</label>
          <select id="userType" required>
            <option value="user">User</option>
            <option value="admin">Admin</option>
          </select>
        </div>
        <div>
          <label for="factory">Factory*</label>
          <select id="factory" required>
            <option value="agl">AGL</option>
            <option value="ajl">AJL</option>
            <option value="pwpl">PWPL</option>
            <option value="abm">ABM</option>
            <option value="head office">Head Office</option>
          </select>
        </div>
        <div class="button-group">
          <button type="submit" class="btn btn-success">Save</button>
          <button type="button" class="btn btn-primary" onclick="hideUserForm()">Cancel</button>
        </div>
      </form>
    </div>
    
    <div id="userListContainer" class="user-list">
      <!-- User list will be loaded here -->
    </div>
  </div>

  <script>
    let inventoryData = [];
    let filteredData = [];
    let currentUserType = "<?php echo $current_user['user_type']; ?>";
    let currentFactory = "<?php echo $current_user['factory']; ?>";

    // DOM Elements
    const addItemBtn = document.getElementById('addItemBtn');
    const serviceBtn = document.getElementById('serviceBtn');
    const removeBtn = document.getElementById('removeBtn');
    const exportBtn = document.getElementById('exportBtn');
    const historyBtn = document.getElementById('historyBtn');
    const userManagementBtn = document.getElementById('userManagementBtn');
    const statusFilter = document.getElementById('statusFilter');
    const searchInput = document.getElementById('searchInput');
    const modalOverlay = document.getElementById('modalOverlay');
    const modalContent = document.getElementById('modalContent');
    const modalClose = document.querySelector('.modal-close');
    const userManagementModal = document.getElementById('userManagementModal');
    const userManagementClose = document.querySelector('.user-management-close');
    const createUserBtn = document.getElementById('createUserBtn');
    const viewUsersBtn = document.getElementById('viewUsersBtn');
    const userFormContainer = document.getElementById('userFormContainer');
    const userListContainer = document.getElementById('userListContainer');
    const userForm = document.getElementById('userForm');

    // Event Listeners
    if (addItemBtn) addItemBtn.addEventListener('click', showAddItemModal);
    serviceBtn.addEventListener('click', showServiceModal);
    if (removeBtn) removeBtn.addEventListener('click', showRemoveModal);
    exportBtn.addEventListener('click', exportToCSV);
    historyBtn.addEventListener('click', showAllServiceHistory);
    if (userManagementBtn) userManagementBtn.addEventListener('click', showUserManagementModal);
    statusFilter.addEventListener('change', filterTable);
    searchInput.addEventListener('keyup', filterTable);
    modalClose.addEventListener('click', closeModal);
    userManagementClose.addEventListener('click', closeUserManagementModal);
    if (createUserBtn) createUserBtn.addEventListener('click', showUserForm);
    if (viewUsersBtn) viewUsersBtn.addEventListener('click', viewAllUsers);
    if (userForm) userForm.addEventListener('submit', handleUserFormSubmit);

    // Close modal when clicking outside content
    modalOverlay.addEventListener('click', (e) => {
      if (e.target === modalOverlay) {
        closeModal();
      }
    });

    // Close user management modal when clicking outside
    userManagementModal.addEventListener('click', (e) => {
      if (e.target === userManagementModal) {
        closeUserManagementModal();
      }
    });

    // Load data when page loads
    document.addEventListener('DOMContentLoaded', loadDataFromDB);

    function showModal(content) {
      modalContent.innerHTML = content;
      modalOverlay.style.display = 'flex';
    }

    function closeModal() {
      modalOverlay.style.display = 'none';
    }

    function showUserManagementModal() {
      userManagementModal.style.display = 'block';
      viewAllUsers();
    }

    function closeUserManagementModal() {
      userManagementModal.style.display = 'none';
    }

    function showUserForm() {
      userFormContainer.style.display = 'block';
      userListContainer.style.display = 'none';
      userForm.reset();
      document.getElementById('userId').value = '';
    }

    function hideUserForm() {
      userFormContainer.style.display = 'none';
      userListContainer.style.display = 'block';
    }

    async function loadDataFromDB() {
      try {
        showLoading(true);
        let url = 'get_assets.php';
        if (currentUserType !== 'admin') {
          url += `?factory=${currentFactory}`;
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

   // 1. Add Item Modal
function showAddItemModal() {
  const modalHtml = `
    <h2>➕ Add New Asset</h2>
    <form id="addForm">
      <div>
        <label for="assetName">Asset Name*</label>
        <input type="text" id="assetName" class="form-control" required>
      </div>
      
      <div>
        <label for="category">Category*</label>
        <select id="category" class="form-control" required>
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
        <input type="text" id="brand" class="form-control" required>
      </div>
      
      <div>
        <label for="model">Model*</label>
        <input type="text" id="model" class="form-control" required>
      </div>
      
      <div>
        <label for="serial">Serial Number*</label>
        <input type="text" id="serial" class="form-control" required>
      </div>
      
      <div>
        <label for="location">Location*</label>
        <select id="location" class="form-control" required>
          <option value="agl">AGL</option>
          <option value="ajl">AJL</option>
          <option value="pwpl">PWPL</option>
          <option value="head office">Head Office</option>
        </select>
      </div>
      
      <div>
        <label for="assigned">Assigned To</label>
        <input type="text" id="assigned" class="form-control">
      </div>
      
      <div>
        <label for="department">Department*</label>
        <input type="text" id="department" class="form-control" required>
      </div>
      
      <div>
        <label for="purchaseDate">Purchase Date*</label>
        <input type="date" id="purchaseDate" class="form-control" required>
      </div>
      
      <div>
        <label for="price">Purchase Price*</label>
        <input type="text" id="price" class="form-control" required placeholder="$1,000">
      </div>
      
      <div>
        <label for="warranty">Warranty Expiry*</label>
        <input type="date" id="warranty" class="form-control" required>
      </div>
      
      <div>
        <label for="priority">Priority</label>
        <select id="priority" class="form-control">
          <option value="Low">Low</option>
          <option value="Medium" selected>Medium</option>
          <option value="High">High</option>
        </select>
      </div>
      
      <div>
        <label for="notes">Notes</label>
        <textarea id="notes" class="form-control" placeholder="Additional information..."></textarea>
      </div>
      
      <div class="button-group">
        <button type="submit" class="btn btn-success">Add Asset</button>
        <button type="button" onclick="closeModal()" class="btn btn-primary">Cancel</button>
      </div>
    </form>
  `;
  
  showModal(modalHtml);
      
      document.getElementById('addForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const priceValue = document.getElementById('price').value.replace(/[^0-9.]/g, '');
        
        const data = {
          asset_name: document.getElementById('assetName').value,
          category: document.getElementById('category').value,
          brand: document.getElementById('brand').value,
          model: document.getElementById('model').value,
          serial_number: document.getElementById('serial').value,
          status: 'ACTIVE',
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

        try {
          const response = await fetch('add_asset.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(data)
          });
          
          const result = await response.json();
          if (result.success) {
            alert('Asset added successfully!');
            closeModal();
            loadDataFromDB();
          } else {
            alert('Error adding asset: ' + (result.message || 'Unknown error'));
          }
        } catch (error) {
          console.error('Error:', error);
          alert('Error adding asset. Check console for details.');
        }
      });
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
    <h2>🔧 Send Asset for Servicing</h2>
    
    <div>
      <label for="serviceAsset">Select Asset to Service:</label>
      <select id="serviceAsset" class="form-control">
        <option value="">Choose an asset...</option>
        ${options}
      </select>
    </div>
    
    <div>
      <label for="serviceType">Service Type:</label>
      <select id="serviceType" class="form-control">
        <option value="Scheduled Maintenance">Scheduled Maintenance</option>
        <option value="Repair">Repair</option>
        <option value="Upgrade">Upgrade</option>
        <option value="Inspection">Inspection</option>
      </select>
    </div>
    
    <div>
      <label for="serviceBy">Serviced By:</label>
      <input type="text" id="serviceBy" class="form-control" placeholder="Technician name">
    </div>
    
    <div>
      <label for="serviceNotes">Service Notes:</label>
      <textarea id="serviceNotes" class="form-control" placeholder="Describe the service required..."></textarea>
    </div>
    
    <div class="button-group">
      <button type="button" onclick="submitService()" class="btn btn-warning">Send for Service</button>
      <button type="button" onclick="closeModal()" class="btn btn-primary">Cancel</button>
    </div>
  `;
  
  showModal(modalHtml);
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

      try {
        const response = await fetch('update_status.php', {
          method: 'POST',
          headers: {'Content-Type': 'application/json'},
          body: JSON.stringify({
            asset_id: assetId,
            status: 'MAINTENANCE',
            service_type: serviceType,
            service_notes: serviceNotes,
            service_by: serviceBy,
            last_maintenance: new Date().toISOString().split('T')[0]
          })
        });
        
        const result = await response.json();
        if (result.success) {
          alert('Asset sent for servicing successfully!');
          closeModal();
          loadDataFromDB();
        } else {
          alert('Error updating asset: ' + (result.message || 'Unknown error'));
        }
      } catch (error) {
        console.error('Error:', error);
        alert('Error sending for service. Check console for details.');
      }
    }

    function showCompleteServiceModal(assetId) {
  const asset = inventoryData.find(item => item.asset_id === assetId);
  if (!asset) return;

  const modalHtml = `
    <h2>✔ Complete Service for ${assetId}</h2>
    <div class="info-box">
      <strong>Asset:</strong> ${asset.asset_name || 'N/A'}<br>
      <strong>Current Status:</strong> ${asset.status || 'N/A'}
    </div>
    
    <div>
      <label for="completionNotes">Completion Notes:</label>
      <textarea id="completionNotes" class="form-control" placeholder="Describe work completed..."></textarea>
    </div>
    
    <div class="button-group">
      <button type="button" onclick="completeService('${assetId}')" class="btn btn-success">Mark as Completed</button>
      <button type="button" onclick="closeModal()" class="btn btn-primary">Cancel</button>
    </div>
  `;
  
  showModal(modalHtml);
}

// 4. Remove Asset Modal (already shown above)
// 5. Service History Modal
function showServiceHistory(assetId) {
  fetch(`get_service_history.php?asset_id=${assetId}`)
    .then(response => response.json())
    .then(history => {
        const asset = inventoryData.find(item => item.asset_id === assetId);
        const modalHtml = `
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
                <button type="button" onclick="closeModal()" class="btn btn-primary">Close</button>
            </div>
        `;
        
        showModal(modalHtml);
    })
    .catch(error => {
        console.error('Error loading service history:', error);
        alert('Error loading service history');
    });
}

    async function completeService(assetId) {
      const completionNotes = document.getElementById('completionNotes').value;

      try {
        const response = await fetch('complete_service.php', {
          method: 'POST',
          headers: {'Content-Type': 'application/json'},
          body: JSON.stringify({
            asset_id: assetId,
            completion_notes: completionNotes
          })
        });
        
        const result = await response.json();
        if (result.success) {
          alert('Service completed successfully! Asset status set to ACTIVE.');
          closeModal();
          loadDataFromDB();
        } else {
          alert('Error completing service: ' + (result.message || 'Unknown error'));
        }
      } catch (error) {
        console.error('Error:', error);
        alert('Error completing service. Check console for details.');
      }
    }

 <!-- Replace your existing showRemoveModal() function with this: -->
function showRemoveModal() {
  if (inventoryData.length === 0) {
    alert('No assets available to remove');
    return;
  }

  let options = inventoryData
    .map(item => `<option value="${item.asset_id}">${item.asset_id} - ${item.asset_name} (${item.status})</option>`)
    .join('');
  
  const modalHtml = `
    <h2>🗑️ Remove Asset from Inventory</h2>
    
    <div>
      <label for="removeAsset">Select Asset to Remove:</label>
      <select id="removeAsset" class="form-control">
        <option value="">Choose an asset...</option>
        ${options}
      </select>
    </div>
    
    <div>
      <label for="removeReason">Reason for Removal:</label>
      <select id="removeReason" class="form-control">
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
      <textarea id="removeNotes" class="form-control" placeholder="Additional details about removal..."></textarea>
    </div>
    
    <div class="warning-box">
      <strong>⚠️ Warning:</strong> This action will permanently remove the asset from your inventory. 
      Make sure to backup your data before proceeding.
    </div>
    
    <div class="button-group">
      <button type="button" onclick="submitRemove()" class="btn btn-danger">Remove Asset</button>
      <button type="button" onclick="closeModal()" class="btn btn-primary">Cancel</button>
    </div>
  `;
  
  showModal(modalHtml);
}

    async function submitRemove() {
      const assetId = document.getElementById('removeAsset').value;
      const removeReason = document.getElementById('removeReason').value;
      const removeNotes = document.getElementById('removeNotes').value;

      if (!assetId) {
        alert('Please select an asset to remove');
        return;
      }

      if (!confirm('Are you sure you want to remove this asset? This action cannot be undone.')) {
        return;
      }

      try {
        const response = await fetch('delete_asset.php', {
          method: 'POST',
          headers: {'Content-Type': 'application/json'},
          body: JSON.stringify({
            asset_id: assetId,
            remove_reason: removeReason,
            remove_notes: removeNotes
          })
        });
        
        const result = await response.json();
        if (result.success) {
          alert('Asset removed successfully!');
          closeModal();
          loadDataFromDB();
        } else {
          alert('Error removing asset: ' + (result.message || 'Unknown error'));
        }
      } catch (error) {
        console.error('Error:', error);
        alert('Error removing asset. Check console for details.');
      }
    }

    function showServiceHistory(assetId) {
      fetch(`get_service_history.php?asset_id=${assetId}`)
        .then(response => response.json())
        .then(history => {
            const asset = inventoryData.find(item => item.asset_id === assetId);
            const modalHtml = `
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
            `;
            
            showModal(modalHtml);
        })
        .catch(error => {
            console.error('Error loading service history:', error);
            alert('Error loading service history');
        });
    }

    function showAllServiceHistory() {
  fetch('get_service_history.php')
    .then(response => response.json())
    .then(history => {
        const modalHtml = `
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
                <button type="button" onclick="closeModal()" class="btn btn-primary">Close</button>
            </div>
        `;
        
        showModal(modalHtml);
    })
    .catch(error => {
        console.error('Error loading service history:', error);
        alert('Error loading service history');
    });
}

    async function viewAllUsers() {
      try {
        const response = await fetch('get_users.php');
        const users = await response.json();
        
        let html = `
          <table>
            <thead>
              <tr>
                <th>Name</th>
                <th>Username</th>
                <th>Employee ID</th>
                <th>Type</th>
                <th>Factory</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              ${users.map(user => `
                <tr>
                  <td>${user.name}</td>
                  <td>${user.username}</td>
                  <td>${user.employee_id}</td>
                  <td>${user.user_type}</td>
                  <td>${user.factory}</td>
                  <td class="action-buttons">
                    <button onclick="editUser('${user.id}')" class="btn-warning">Edit</button>
                    <button onclick="deleteUser('${user.id}')" class="btn-danger">Delete</button>
                  </td>
                </tr>
              `).join('')}
            </tbody>
          </table>
        `;
        
        userListContainer.innerHTML = html;
        userFormContainer.style.display = 'none';
        userListContainer.style.display = 'block';
      } catch (error) {
        console.error('Error loading users:', error);
        userListContainer.innerHTML = '<p>Error loading users. Please try again.</p>';
      }
    }

    async function editUser(userId) {
      try {
        const response = await fetch(`get_user.php?id=${userId}`);
        const user = await response.json();
        
        document.getElementById('userId').value = user.id;
        document.getElementById('name').value = user.name;
        document.getElementById('username').value = user.username;
        document.getElementById('password').value = '';
        document.getElementById('employeeId').value = user.employee_id;
        document.getElementById('userType').value = user.user_type;
        document.getElementById('factory').value = user.factory;
        
        userFormContainer.style.display = 'block';
        userListContainer.style.display = 'none';
      } catch (error) {
        console.error('Error loading user:', error);
        alert('Error loading user data');
      }
    }

    async function deleteUser(userId) {
      if (confirm('Are you sure you want to delete this user?')) {
        try {
          const response = await fetch(`delete_user.php?id=${userId}`, {
            method: 'DELETE'
          });
          
          const result = await response.json();
          if (result.success) {
            alert('User deleted successfully!');
            viewAllUsers();
          } else {
            alert('Error deleting user: ' + (result.message || 'Unknown error'));
          }
        } catch (error) {
          console.error('Error:', error);
          alert('Error deleting user. Check console for details.');
        }
      }
    }

    async function handleUserFormSubmit(e) {
      e.preventDefault();
      
      const userId = document.getElementById('userId').value;
      const userData = {
        name: document.getElementById('name').value,
        username: document.getElementById('username').value,
        password: document.getElementById('password').value,
        employee_id: document.getElementById('employeeId').value,
        user_type: document.getElementById('userType').value,
        factory: document.getElementById('factory').value
      };

      try {
        const url = userId ? `update_user.php?id=${userId}` : 'create_user.php';
        const method = userId ? 'PUT' : 'POST';
        
        const response = await fetch(url, {
          method: method,
          headers: {'Content-Type': 'application/json'},
          body: JSON.stringify(userData)
        });
        
        const result = await response.json();
        if (result.success) {
          alert(`User ${userId ? 'updated' : 'created'} successfully!`);
          viewAllUsers();
        } else {
          alert(`Error ${userId ? 'updating' : 'creating'} user: ` + (result.message || 'Unknown error'));
        }
      } catch (error) {
        console.error('Error:', error);
        alert(`Error ${userId ? 'updating' : 'creating'} user. Check console for details.`);
      }
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