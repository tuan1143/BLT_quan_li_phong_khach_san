<?php
session_start();
require_once 'includes/db.php';

// 1. Check Login
if (!isset($_SESSION['user_id'])) { die("Vui lòng đăng nhập."); }

// 2. Lấy dữ liệu hóa đơn
$db = getDB();
try {
    $sql = "SELECT 
                hd.*,
                k.ho_ten as ten_khach,
                p.ten_phong,
                nv.ho_ten as ten_nhanvien
            FROM hoadon hd
            JOIN datphong dp ON hd.id_datphong = dp.id_datphong
            JOIN khachhang k ON dp.id_khachhang = k.id_khachhang
            JOIN phong p ON dp.id_phong = p.id_phong
            JOIN nhanvien nv ON hd.id_nhanvien_lap = nv.id_nhanvien
            ORDER BY hd.ngay_lap DESC";
    $stmt = $db->query($sql);
    $invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die("Lỗi: " . $e->getMessage());
}

// 3. Header Excel
$filename = "BaoCaoDoanhThu_" . date('Y-m-d_H-i') . ".xls";
header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Pragma: no-cache");
header("Expires: 0");
echo "\xEF\xBB\xBF"; // BOM UTF-8
?>

<html xmlns:x="urn:schemas-microsoft-com:office:excel">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <style>
        table { border-collapse: collapse; width: 100%; font-family: Arial, sans-serif; }
        td, th { border: 1px solid #000; padding: 8px; }
        .header { background-color: #E9C46A; font-size: 16px; font-weight: bold; text-align: center; height: 50px; }
        .thead { background-color: #333; color: #fff; text-align: center; }
        .num { text-align: right; mso-number-format:"\#\,\#\#0"; }
        .center { text-align: center; }
        .total-row { background-color: #ffffcc; font-weight: bold; }
    </style>
</head>
<body>
    <table>
        <tr>
            <td colspan="8" class="header" valign="middle">
                BÁO CÁO DOANH THU - LỊCH SỬ HÓA ĐƠN
            </td>
        </tr>
        <tr>
            <td colspan="8" class="center">
                Ngày xuất: <?php echo date('d/m/Y H:i'); ?>
            </td>
        </tr>
        <tr><td colspan="8"></td></tr>

        <tr class="thead">
            <th>STT</th>
            <th>Mã HĐ</th>
            <th>Ngày Lập</th>
            <th>Khách Hàng</th>
            <th>Phòng</th>
            <th>Tiền Phòng</th>
            <th>Tiền Dịch Vụ</th>
            <th>Tổng Cộng (VNĐ)</th>
        </tr>

        <?php 
        $i = 1;
        $total_revenue = 0;
        foreach ($invoices as $row): 
            $total_revenue += $row['tong_thanh_toan'];
        ?>
        <tr>
            <td class="center"><?php echo $i++; ?></td>
            <td class="center"><?php echo $row['ma_hoadon'] ?? $row['id_hoadon']; ?></td>
            <td class="center"><?php echo date('d/m/Y H:i', strtotime($row['ngay_lap'])); ?></td>
            <td><?php echo $row['ten_khach']; ?></td>
            <td class="center"><?php echo $row['ten_phong']; ?></td>
            <td class="num"><?php echo $row['tong_tien_phong']; ?></td>
            <td class="num"><?php echo $row['tong_tien_dichvu']; ?></td>
            <td class="num" style="font-weight:bold;"><?php echo $row['tong_thanh_toan']; ?></td>
        </tr>
        <?php endforeach; ?>

        <tr class="total-row">
            <td colspan="7" class="text-right" style="text-align:right;">TỔNG DOANH THU:</td>
            <td class="num"><?php echo $total_revenue; ?></td>
        </tr>
    </table>
</body>
</html>