<?php
define('PROJECT_ROOT', dirname(__DIR__));
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }

$db = getDB();
if (!$db) { die("Không thể kết nối database"); }

// 1. LẤY THAM SỐ LỌC & PHÂN TRANG
$filter_status = $_GET['status'] ?? 'all'; // Mặc định lấy tất cả
$limit = 10;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

try {
    // 2. XÂY DỰNG CÂU WHERE
    $whereClause = "";
    $params = [];
    
    if ($filter_status !== 'all') {
        $whereClause = "WHERE p.trang_thai = ?";
        $params[] = $filter_status;
    }

    // 3. ĐẾM TỔNG SỐ (Có áp dụng lọc)
    $stmtCount = $db->prepare("SELECT COUNT(*) FROM phong p $whereClause");
    $stmtCount->execute($params);
    $total_rows = $stmtCount->fetchColumn();
    $total_pages = ceil($total_rows / $limit);

    // 4. LẤY DANH SÁCH PHÒNG
    $sql = "SELECT p.*, lp.ten_loaiphong 
            FROM phong p
            LEFT JOIN loaiphong lp ON p.id_loaiphong = lp.id_loaiphong
            $whereClause
            ORDER BY p.ten_phong
            LIMIT $limit OFFSET $offset";
            
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $danh_sach_phong = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 5. Lấy loại phòng cho Modal
    $stmt_lp = $db->query("SELECT id_loaiphong, ten_loaiphong FROM loaiphong ORDER BY ten_loaiphong");
    $danh_sach_loai_phong = $stmt_lp->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    error_log("Query Error: " . $e->getMessage());
    die("Lỗi hệ thống.");
}

$page_title = 'Quản lý phòng';
$content_path = PROJECT_ROOT . '/src/views/rooms/rooms-content.php'; 
$extra_css = '<link rel="stylesheet" href="assets/css/rooms.css">';
$extra_js = '
    <script>window.roomsConfig = { actionsUrl: "actions/room-actions.php" };</script>
    <script src="assets/js/rooms.js" defer></script>
';

include 'includes/layout.php';
?>