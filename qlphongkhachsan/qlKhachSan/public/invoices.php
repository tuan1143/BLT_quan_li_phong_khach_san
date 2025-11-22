<?php
define('PROJECT_ROOT', dirname(__DIR__));
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }

$db = getDB();
$search = trim($_GET['search'] ?? '');
$limit = 10;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

try {
    // 1. XÂY DỰNG CÂU WHERE CHO TÌM KIẾM
    $whereClause = "";
    $params = [];
    
    if ($search) {
        // Tìm theo Mã hóa đơn HOẶC CMND/CCCD khách hàng
        $whereClause = "WHERE (hd.ma_hoadon LIKE ? OR k.cmnd_cccd LIKE ?)";
        $term = "%$search%";
        $params = [$term, $term];
    }

    // 2. ĐẾM TỔNG (để phân trang)
    $sqlCount = "SELECT COUNT(*) 
                 FROM hoadon hd
                 JOIN datphong dp ON hd.id_datphong = dp.id_datphong
                 JOIN khachhang k ON dp.id_khachhang = k.id_khachhang
                 $whereClause";
    $stmtCount = $db->prepare($sqlCount);
    $stmtCount->execute($params);
    $total_rows = $stmtCount->fetchColumn();
    $total_pages = ceil($total_rows / $limit);

    // 3. LẤY DỮ LIỆU CHI TIẾT
    $sql = "SELECT 
                hd.*,
                k.ho_ten as ten_khach,
                k.cmnd_cccd,
                p.ten_phong,
                nv.ho_ten as ten_nhanvien
            FROM hoadon hd
            JOIN datphong dp ON hd.id_datphong = dp.id_datphong
            JOIN khachhang k ON dp.id_khachhang = k.id_khachhang
            JOIN phong p ON dp.id_phong = p.id_phong
            JOIN nhanvien nv ON hd.id_nhanvien_lap = nv.id_nhanvien
            $whereClause
            ORDER BY hd.ngay_lap DESC 
            LIMIT $limit OFFSET $offset";

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $hoadons = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Lỗi truy vấn: " . $e->getMessage());
}

$page_title = 'Lịch sử Hóa đơn';
$content_path = PROJECT_ROOT . '/src/views/invoices/invoices-content.php'; 
$extra_css = '<link rel="stylesheet" href="assets/css/invoices.css">';
// Thêm file JS mới cho trang này
$extra_js = '<script src="assets/js/invoices.js" defer></script>'; 

include 'includes/layout.php';
?>