<?php
// admin/includes/header_admin.php - Header Admin Tsubasa Arena (Ukuran Besar)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cek login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Admin - Tsubasa Arena' ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: #f0f2f5;
            color: #1a2a3a;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        
        /* ========== NAVBAR ATAS (UKURAN BESAR) ========== */
        .navbar {
            background: linear-gradient(135deg, #0a2b4e 0%, #1e4d7c 100%);
            color: white;
            padding: 8px 40px;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        }
        
        .nav-container {
            max-width: 1600px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .logo h2 {
            font-size: 28px;
            color: #e63946;
            letter-spacing: 1px;
        }
        
        .logo p {
            font-size: 11px;
            opacity: 0.8;
            letter-spacing: 1px;
        }
        
        .nav-menu {
            display: flex;
            list-style: none;
            gap: 12px;
        }
        
        .nav-menu li a {
            display: block;
            padding: 20px 24px;
            color: white;
            text-decoration: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .nav-menu li a:hover {
            background: #e63946;
            transform: translateY(-2px);
        }
        
        .nav-menu li a.active {
            background: #e63946;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 25px;
        }
        
        .user-name {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 15px;
            font-weight: 500;
            background: rgba(255,255,255,0.1);
            padding: 8px 16px;
            border-radius: 30px;
        }
        
        .logout-link {
            background: #e63946;
            padding: 10px 24px;
            border-radius: 30px;
            color: white;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .logout-link:hover {
            background: #c1121f;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(230,57,70,0.4);
        }
        
        /* ========== MAIN CONTENT ========== */
        .main-content {
            flex: 1;
            padding: 30px 40px;
            max-width: 1600px;
            margin: 0 auto;
            width: 100%;
        }
        
        @media (max-width: 1024px) {
            .navbar {
                padding: 8px 20px;
            }
            .nav-menu li a {
                padding: 15px 18px;
                font-size: 13px;
            }
            .logo h2 {
                font-size: 24px;
            }
            .main-content {
                padding: 20px;
            }
        }
        
        @media (max-width: 768px) {
            .nav-container {
                flex-direction: column;
                gap: 10px;
            }
            .nav-menu {
                flex-wrap: wrap;
                justify-content: center;
                gap: 5px;
            }
            .nav-menu li a {
                padding: 10px 16px;
                font-size: 12px;
            }
            .user-info {
                margin-bottom: 10px;
            }
            .user-name {
                padding: 5px 12px;
                font-size: 13px;
            }
            .logout-link {
                padding: 6px 16px;
                font-size: 12px;
            }
            .main-content {
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <!-- NAVBAR -->
    <div class="navbar">
        <div class="nav-container">
            <div class="logo">
                <h2>⚡ Vinvent Arena</h2>
                <p>ADMIN PANEL</p>
            </div>
            <ul class="nav-menu">
                <li><a href="index.php" class="<?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">📊 Dashboard</a></li>
                <li><a href="lapangan.php" class="<?= basename($_SERVER['PHP_SELF']) == 'lapangan.php' ? 'active' : '' ?>">🏟️ Lapangan</a></li>
                <li><a href="booking.php" class="<?= basename($_SERVER['PHP_SELF']) == 'booking.php' ? 'active' : '' ?>">📅 Booking</a></li>
                <li><a href="user.php" class="<?= basename($_SERVER['PHP_SELF']) == 'user.php' ? 'active' : '' ?>">👥 User</a></li>
                <li><a href="statistik.php" class="<?= basename($_SERVER['PHP_SELF']) == 'statistik.php' ? 'active' : '' ?>">📈 Statistik</a></li>
            </ul>
            <div class="user-info">
                <div class="user-name">
                    <span>⚡</span>
                    <span><?= $_SESSION['admin_nama'] ?? 'Admin' ?></span>
                </div>
                <a href="logout.php" class="logout-link">🚪 Logout</a>
            </div>
        </div>
    </div>
    
    <div class="main-content">