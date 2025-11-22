<?php
define('PROJECT_ROOT', dirname(__DIR__));
session_start();
require_once 'includes/db.php';

// 1. KIỂM TRA ĐĂNG NHẬP
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// 2. KẾT NỐI DATABASE
$db = getDB();
if (!$db) { die("Không thể kết nối database"); }

try {
    // --- 0. THIẾT LẬP MÚI GIỜ (QUAN TRỌNG) ---
    date_default_timezone_set('Asia/Ho_Chi_Minh');

    // --- A. THỐNG KÊ TRẠNG THÁI PHÒNG ---
    $stmt = $db->query("SELECT trang_thai, COUNT(*) as so_luong FROM phong GROUP BY trang_thai");
    $counts = $stmt->fetchAll(PDO::FETCH_KEY_PAIR); 
    
    $stats = [
        'phong_trong' => $counts['Trong'] ?? 0,
        'dang_thue'   => $counts['DangO'] ?? 0,
        'da_dat'      => $counts['DaDat'] ?? 0,
        'don_dep'     => $counts['DangDonDep'] ?? 0,
        'bao_tri'     => $counts['BaoTri'] ?? 0,
        'tong_phong'  => array_sum($counts)
    ];

    // --- B. [FIX] TÍNH BIỂU ĐỒ VÀ DOANH THU CÙNG LÚC ---
    $chart_labels = [];
    $chart_data = [];
    $doanh_thu_hom_nay = 0; // Khởi tạo bằng 0

    // Chạy vòng lặp 7 ngày qua (từ 6 ngày trước đến hôm nay)
    for ($i = 6; $i >= 0; $i--) {
        $date_loop = date('Y-m-d', strtotime("-$i days"));
        $label = date('d/m', strtotime("-$i days"));

        $stmt_chart = $db->prepare("SELECT SUM(tong_thanh_toan) FROM hoadon WHERE DATE(ngay_lap) = ?");
        $stmt_chart->execute([$date_loop]);
        $total = $stmt_chart->fetchColumn() ?: 0;

        $chart_labels[] = $label;
        $chart_data[] = $total;

        // [QUAN TRỌNG] Nếu là vòng lặp cuối cùng ($i = 0) -> Chính là HÔM NAY
        // Ta lấy luôn giá trị này gán cho biến tổng, đảm bảo khớp 100% với biểu đồ
        if ($i === 0) {
            $doanh_thu_hom_nay = $total;
        }
    }
    
    // --- C. TÍNH TỔNG KHÁCH ĐANG LƯU TRÚ ---
    $stmt_guests = $db->query("SELECT SUM(so_nguoi_o) FROM datphong WHERE trang_thai = 'DaNhanPhong'");
    $tong_khach_dang_o = $stmt_guests->fetchColumn() ?: 0; 

    // --- D. DANH SÁCH ĐẶT PHÒNG MỚI NHẤT ---
    $stmt_recent = $db->query("SELECT 
        p.ten_phong,
        kh.ho_ten as ten_khach,
        d.ngay_checkin,
        d.trang_thai
        FROM datphong d
        JOIN phong p ON d.id_phong = p.id_phong
        JOIN khachhang kh ON d.id_khachhang = kh.id_khachhang
        ORDER BY d.ngay_dat DESC
        LIMIT 5");
    $recent_bookings = $stmt_recent->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    error_log("Query Error: " . $e->getMessage());
    die("Đã xảy ra lỗi khi truy vấn dữ liệu");
}

// 3. THIẾT LẬP BIẾN CHO TEMPLATE
$page_title = 'Dashboard - Tổng quan';
$content_path = PROJECT_ROOT . '/src/views/dashboard/dashboard-content.php'; 

$extra_js = '
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        window.chartLabels = ' . json_encode($chart_labels) . ';
        window.chartData = ' . json_encode($chart_data) . ';
    </script>
    <script src="assets/js/dashboard.js" defer></script>
';

include 'includes/layout.php';
?>