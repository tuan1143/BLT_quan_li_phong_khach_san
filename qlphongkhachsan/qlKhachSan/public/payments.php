<?php
define('PROJECT_ROOT', dirname(__DIR__));
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }

$db = getDB();
// Lấy danh sách các phòng ĐANG CÓ KHÁCH (DaNhanPhong) hoặc Mới đặt (để hủy/thanh toán cọc)
// Ở đây ta tập trung vào việc Checkout cho khách Đang ở
$sql = "SELECT 
            dp.id_datphong,
            dp.ngay_checkin,
            dp.tien_dat_coc,
            k.ho_ten,
            p.ten_phong,
            p.gia_phong,
            lp.gia_ngay as gia_loai_phong
        FROM datphong dp
        JOIN khachhang k ON dp.id_khachhang = k.id_khachhang
        JOIN phong p ON dp.id_phong = p.id_phong
        LEFT JOIN loaiphong lp ON p.id_loaiphong = lp.id_loaiphong
        WHERE dp.trang_thai = 'DaNhanPhong' 
        ORDER BY p.ten_phong ASC";

$stmt = $db->query($sql);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

$page_title = 'Thanh toán & Trả phòng';
$content_path = PROJECT_ROOT . '/src/views/payments/payments-content.php'; 
$extra_css = '<link rel="stylesheet" href="assets/css/payments.css">';
$extra_js = '
    <script>window.paymentConfig = { actionsUrl: "actions/payment-actions.php" };</script>
    <script src="assets/js/payments.js" defer></script>
';

include 'includes/layout.php';
?>