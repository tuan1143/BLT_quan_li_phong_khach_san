<?php
// Lấy tên file hiện tại, bỏ đuôi .php
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<aside class="sidebar">
    <div class="hotel-brand">
        <img src="assets/img/logo.png" alt="Logo" class="sidebar-logo">
        <h1>Trung Tuấn Hotel</h1>
        <div class="subtitle">Sang trọng - Đẳng cấp</div>
    </div>
    <nav class="nav-menu">
        <a href="dashboard.php" class="nav-item <?php echo $current_page === 'dashboard' ? 'active' : ''; ?>">
            <span class="icon">📊</span>
            Dashboard
        </a>
        <a href="rooms.php" class="nav-item <?php echo $current_page === 'rooms' ? 'active' : ''; ?>">
            <span class="icon">🛏️</span>
            Quản lý phòng
        </a>
        <a href="bookings.php" class="nav-item <?php echo $current_page === 'bookings' ? 'active' : ''; ?>">
            <span class="icon">📝</span>
            Đặt phòng
        </a>
        <a href="customers.php" class="nav-item <?php echo $current_page === 'customers' ? 'active' : ''; ?>">
            <span class="icon">👥</span>
            Khách hàng
        </a>
        <a href="payments.php" class="nav-item <?php echo $current_page === 'payments' ? 'active' : ''; ?>">
            <span class="icon">💰</span>
            Thanh toán & Trả phòng
        </a>
       <a href="invoices.php" class="nav-item <?php echo $current_page === 'invoices' ? 'active' : ''; ?>">
            <span class="icon">📜</span>
            Lịch sử Hóa đơn
        </a>
    </nav>
</aside>