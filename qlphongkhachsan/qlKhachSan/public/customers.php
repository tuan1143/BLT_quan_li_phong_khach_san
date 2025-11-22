<?php
define('PROJECT_ROOT', dirname(__DIR__));
session_start();
require_once 'includes/db.php';

// 1. Check Login
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }

$db = getDB();
if (!$db) { die("Không thể kết nối database"); }

// 2. LẤY TỪ KHÓA TÌM KIẾM
// PHÂN TRANG & TÌM KIẾM
$limit = 10; // Số dòng mỗi trang
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;
$search = trim($_GET['search'] ?? '');

try {
    // BƯỚC A: Đếm tổng số bản ghi trước (Để biết có bao nhiêu trang)
    $sqlCount = "SELECT COUNT(*) FROM khachhang";
    $params = [];
    
    if ($search) {
        $sqlCount .= " WHERE ho_ten LIKE ? OR cmnd_cccd LIKE ? OR so_dien_thoai LIKE ? OR email LIKE ?";
        $term = "%$search%";
        $params = [$term, $term, $term, $term];
    }
    
    $stmtCount = $db->prepare($sqlCount);
    $stmtCount->execute($params);
    $total_rows = $stmtCount->fetchColumn();
    $total_pages = ceil($total_rows / $limit);

    // BƯỚC B: Lấy dữ liệu cho trang hiện tại (Thêm LIMIT và OFFSET)
    $sql = "SELECT * FROM khachhang";
    if ($search) {
        $sql .= " WHERE ho_ten LIKE ? OR cmnd_cccd LIKE ? OR so_dien_thoai LIKE ? OR email LIKE ?";
    }
    $sql .= " ORDER BY ho_ten LIMIT $limit OFFSET $offset"; // <--- Quan trọng
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $danh_sach_khach_hang = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) { /* ... */ }
// 3. Setup View
$page_title = 'Quản lý khách hàng';
$content_path = PROJECT_ROOT . '/src/views/customers/customers-content.php'; 
$extra_css = '<link rel="stylesheet" href="assets/css/customers.css">';
$extra_js = '
    <script>window.customersConfig = { actionsUrl: "actions/customer-actions.php" };</script>
    <script src="assets/js/customers.js" defer></script>
';

include 'includes/layout.php';
?>