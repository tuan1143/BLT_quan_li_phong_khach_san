<?php
define('PROJECT_ROOT', dirname(__DIR__));
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }

$db = getDB();

// 1. LẤY THAM SỐ (Status + Search + Page)
$status = $_GET['status'] ?? 'all';
$search = trim($_GET['search'] ?? '');
$limit = 10;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

try {
    // 2. XÂY DỰNG CÂU WHERE ĐỘNG
    $whereConditions = [];
    $params = [];

    // Lọc theo trạng thái
    if ($status !== 'all') {
        $whereConditions[] = "d.trang_thai = ?";
        $params[] = $status;
    }

    // Tìm kiếm (Tên khách, Tên phòng, SĐT)
    if ($search) {
        $whereConditions[] = "(k.ho_ten LIKE ? OR p.ten_phong LIKE ? OR k.so_dien_thoai LIKE ?)";
        $term = "%$search%";
        $params[] = $term; // Cho ho_ten
        $params[] = $term; // Cho ten_phong
        $params[] = $term; // Cho sdt
    }

    // Ghép câu lệnh WHERE
    $whereSQL = "";
    if (!empty($whereConditions)) {
        $whereSQL = "WHERE " . implode(" AND ", $whereConditions);
    }

    // 3. ĐẾM TỔNG (Phân trang)
    $sqlCount = "SELECT COUNT(*) 
                 FROM datphong d
                 JOIN phong p ON d.id_phong = p.id_phong
                 JOIN khachhang k ON d.id_khachhang = k.id_khachhang
                 $whereSQL";
    $stmtCount = $db->prepare($sqlCount);
    $stmtCount->execute($params);
    $total_rows = $stmtCount->fetchColumn();
    $total_pages = ceil($total_rows / $limit);

    // 4. LẤY DỮ LIỆU
    $sql = "SELECT 
                d.*, 
                p.ten_phong, 
                k.ho_ten AS ten_khach,
                k.so_dien_thoai
            FROM datphong d
            JOIN phong p ON d.id_phong = p.id_phong
            JOIN khachhang k ON d.id_khachhang = k.id_khachhang
            $whereSQL
            ORDER BY d.ngay_dat DESC
            LIMIT $limit OFFSET $offset";

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $danh_sach_dat_phong = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 5. Lấy dữ liệu cho Modal (Khách & Phòng)
    // Khách chưa có phòng
    $sql_kh = "SELECT id_khachhang, ho_ten FROM khachhang WHERE id_khachhang NOT IN (SELECT DISTINCT id_khachhang FROM datphong WHERE trang_thai IN ('MoiDat', 'DaNhanPhong')) ORDER BY ho_ten";
    $danh_sach_khach_hang = $db->query($sql_kh)->fetchAll(PDO::FETCH_ASSOC);

    // Phòng trống
    $stmt_p = $db->query("SELECT id_phong, ten_phong, trang_thai FROM phong WHERE trang_thai = 'Trong' ORDER BY ten_phong");
    $danh_sach_phong = $stmt_p->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    error_log("Query Error: " . $e->getMessage());
    die("Lỗi truy vấn dữ liệu");
}

$page_title = 'Quản lý Đặt phòng';
$content_path = PROJECT_ROOT . '/src/views/bookings/bookings-content.php'; 
$extra_css = '<link rel="stylesheet" href="assets/css/bookings.css">';
$extra_js = '
    <script>window.bookingsConfig = { actionsUrl: "actions/booking-actions.php" };</script>
    <script src="assets/js/bookings.js" defer></script>
';

include 'includes/layout.php';
?>