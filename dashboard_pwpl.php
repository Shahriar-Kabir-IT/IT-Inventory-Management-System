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
  <title>IT Asset Inventory Management - PWPL</title>
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

    .content-area {
      flex: 1;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      padding: 40px;
      text-align: center;
      color: var(--secondary-color);
    }

    .icon-large {
      font-size: 4rem;
      margin-bottom: 20px;
      color: var(--border-color);
    }

    .message {
      font-size: 1.5rem;
      margin-bottom: 10px;
      color: var(--heading-color);
      font-weight: 700;
    }

    .sub-message {
      font-size: 1rem;
      max-width: 500px;
      margin-bottom: 30px;
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
      text-decoration: none;
    }

    .btn:hover {
      transform: translateY(-1px);
      box-shadow: 0 4px 8px rgba(0,0,0,0.15);
      opacity: 0.95;
    }
    
    .btn:active {
      transform: translateY(0);
    }

    .btn-primary {
      background: var(--primary-color);
      color: white;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <div class="header-inner">
        <div class="brand">
          <h1>IT Asset Inventory Management</h1>
          <p>PWPL Unit</p>
        </div>
        <div class="user-info">
          <p>Welcome, <?php echo htmlspecialchars($current_user['name'] ?? 'User'); ?></p>
          <a href="logout.php">Logout</a>
        </div>
      </div>
    </div>

    <div class="content-area">
      <div class="icon-large">🛠️</div>
      <div class="message">Dashboard Under Setup</div>
      <div class="sub-message">
        The PWPL dashboard is currently being set up. Please check back later or contact the administrator for assistance.
      </div>
      <a href="dashboard.php" class="btn btn-primary">Go to Main Dashboard</a>
    </div>
  </div>
</body>
</html>
