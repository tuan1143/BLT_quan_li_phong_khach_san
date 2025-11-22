<?php
// File này đã có session_start() và kiểm tra login ở file (dashboard.php) gọi nó
// Lấy thông tin user từ session để hiển thị
$ho_ten = $_SESSION['ho_ten'] ?? ($_SESSION['username'] ?? 'Người dùng');
$chuc_vu = $_SESSION['chuc_vu'] ?? '';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Trung Tuấn Hotel'; ?></title>
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <?php if (isset($extra_css)) echo $extra_css; ?>
</head>
<body>
    <div class="dashboard-container">
        
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="main-content">
            <header class="top-bar">
                <div class="page-title"><?php echo htmlspecialchars($page_title ?? 'Dashboard'); ?></div>
                <div class="user-menu">
                    <span class="user-info">
                        <span class="user-name"><?php echo htmlspecialchars($ho_ten); ?></span>
                        <span class="user-role"><?php echo htmlspecialchars($chuc_vu); ?></span>
                    </span>
                  <a href="logout.php" class="logout-btn">Đăng xuất</a>
            </div>
        </header> <div class="luxury-divider"></div>

        <div class="dashboard-content">
            <?php include $content_path; ?>
        </div>
    </main>
    </div>
    
    <?php if (isset($extra_js)) echo $extra_js; ?>
</body>
</html>