<?php
session_start();
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $location = $_POST['location'];

    try {
        // Check for superadmin credentials
        if (($username === 'superadmin' || $username === 'superadmin2') && $password === '1234' && $location === 'head office') {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND factory = ?");
            $stmt->execute([$username, $location]);
            $user = $stmt->fetch();

            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['user_type'] = $user['user_type'];
                $_SESSION['factory'] = $user['factory'];
                header("Location: dashboard_admin.php");
                exit();
            }
        }

        // Normal user authentication
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND factory = ?");
        $stmt->execute([$username, $location]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_type'] = $user['user_type'];
            $_SESSION['factory'] = $user['factory'];
            
            // Redirect to location-specific dashboard
            switch ($user['factory']) {
                case 'agl':
                    $dashboard = 'dashboard_agl.php';
                    break;
                case 'ajl':
                    $dashboard = 'dashboard_ajl.php';
                    break;
                case 'pwpl':
                    $dashboard = 'dashboard_pwpl.php';
                    break;
                case 'abm':
                    $dashboard = 'dashboard_abm.php';
                    break;
                case 'head office':
                default:
                    $dashboard = 'dashboard.php';
                    break;
            }
            
            header("Location: $dashboard");
            exit();
        } else {
            $error = "Invalid username, password, or location";
        }
    } catch (PDOException $e) {
        $error = "Database error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - IT Asset Management</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2980b9;
            --accent-color: #e74c3c;
            --light-color: #ecf0f1;
            --dark-color: #2c3e50;
            --success-color: #2ecc71;
            --warning-color: #f39c12;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #0773bbff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: var(--dark-color);
        }
        
        .login-container {
            background-color: #ffffffff;;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            width: 400px;
            padding: 40px;
            text-align: center;
            animation: fadeIn 0.5s ease-in-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .logo {
            margin-bottom: 30px;
        }
        
        .logo img {
            height: 60px;
            margin-bottom: 15px;
        }
        
        .logo h1 {
            color: var(--primary-color);
            font-size: 22px;
            font-weight: 700;
            margin-top: 10px;
        }
        
        .input-group {
            margin-bottom: 20px;
            text-align: left;
        }
        
        .input-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--dark-color);
        }
        
        .input-group input, .input-group select {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            transition: border 0.3s;
        }
        
        .input-group input:focus, .input-group select:focus {
            border-color: var(--primary-color);
            outline: none;
        }
        
        .btn {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            width: 100%;
            transition: background-color 0.3s;
        }
        
        .btn:hover {
            background-color: var(--secondary-color);
        }
        
        .message {
            margin-top: 20px;
            padding: 10px;
            border-radius: 5px;
            font-size: 14px;
        }
        
        .error {
            background-color: #ffebee;
            color: var(--accent-color);
        }
        
        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #7f8c8d;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <img src="com.png" alt="Company Logo">
            <h1>Ananta IT Asset Management</h1>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="message error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="input-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <div class="input-group">
                <label for="location">Location</label>
                <select id="location" name="location" required>
                    <option value="">Select Location</option>
                    <option value="agl">AGL</option>
                    <option value="ajl">AJL</option>
                    <option value="pwpl">PWPL</option>
                    <option value="abm">ABM</option>
                    <option value="head office">Head Office</option>
                </select>
            </div>
            
            <button type="submit" class="btn">Login</button>
        </form>
        
        <div class="footer">
            &copy; <?php echo date('Y'); ?>Developed and Managed By ANANTA ICT TEAM
        </div>
    </div>
</body>
</html>