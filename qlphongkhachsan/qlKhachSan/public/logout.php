<?php
// Bắt đầu session
session_start();

// Xóa tất cả các biến phiên
$_SESSION = [];

// Hủy session
session_destroy();

// Chuyển hướng về trang đăng nhập
header('Location: login.php');
exit;
?>