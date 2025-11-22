<?php
session_start();
require_once 'includes/db.php';

// 1. Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    die("Vui lòng đăng nhập để xuất dữ liệu.");
}

// 2. Kết nối Database & Lấy dữ liệu
$db = getDB();
try {
    $sql = "SELECT p.*, lp.ten_loaiphong 
            FROM phong p
            LEFT JOIN loaiphong lp ON p.id_loaiphong = lp.id_loaiphong
            ORDER BY p.ten_phong";
    $stmt = $db->query($sql);
    $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die("Lỗi truy vấn: " . $e->getMessage());
}

// 3. Cấu hình Header để tải file Excel
$filename = "DanhSachPhong_" . date('Y-m-d_H-i') . ".xls";
header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Pragma: no-cache");
header("Expires: 0");

// 4. Xuất nội dung (Dùng bảng HTML để định dạng)
echo "\xEF\xBB\xBF"; // BOM để hiển thị đúng tiếng Việt UTF-8
?>

<html xmlns:x="urn:schemas-microsoft-com:office:excel">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <style>
        /* CSS đơn giản cho Excel */
        table { border-collapse: collapse; width: 100%; }
        td, th { border: 1px solid #000; padding: 5px; }
        .header-title { font-size: 18px; font-weight: bold; text-align: center; background-color: #E9C46A; color: #000; }
        .header-info { font-style: italic; text-align: center; }
        .thead-dark { background-color: #333; color: #fff; text-align: center; font-weight: bold; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <table>
        <tr>
            <td colspan="6" class="header-title" style="height: 40px; vertical-align: middle;">
                KHÁCH SẠN TRUNG TUẤN - DANH SÁCH PHÒNG
            </td>
        </tr>
        <tr>
            <td colspan="6" class="header-info">
                Ngày xuất báo cáo: <?php echo date('d/m/Y H:i:s'); ?> | Người xuất: <?php echo $_SESSION['username'] ?? 'Admin'; ?>
            </td>
        </tr>
        <tr><td colspan="6"></td></tr> <tr class="thead-dark">
            <th>STT</th>
            <th>Tên Phòng</th>
            <th>Loại Phòng</th>
            <th>Giá Phòng (VNĐ)</th>
            <th>Trạng Thái</th>
            <th>Ghi Chú</th>
        </tr>

        <?php 
        $i = 1;
        foreach ($rooms as $row): 
            // Xử lý trạng thái sang tiếng Việt
            $statusMap = [
                'Trong' => 'Trống', 'DangO' => 'Đang ở', 'DaDat' => 'Đã đặt',
                'DangDonDep' => 'Đang dọn', 'BaoTri' => 'Bảo trì'
            ];
            $statusText = $statusMap[$row['trang_thai']] ?? $row['trang_thai'];
            
            // Màu nền cho trạng thái (Tùy chọn)
            $bgColor = '';
            if($row['trang_thai'] == 'DangO') $bgColor = 'background-color: #ffcccc;'; // Đỏ nhạt
            if($row['trang_thai'] == 'Trong') $bgColor = 'background-color: #ccffcc;'; // Xanh nhạt
        ?>
        <tr>
            <td class="text-center"><?php echo $i++; ?></td>
            <td class="text-center" style="font-weight:bold;"><?php echo $row['ten_phong']; ?></td>
            <td><?php echo $row['ten_loaiphong']; ?></td>
            <td class="text-right"><?php echo number_format($row['gia_phong']); ?></td>
            <td class="text-center" style="<?php echo $bgColor; ?>"><?php echo $statusText; ?></td>
            <td><?php echo $row['ghi_chu']; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>