<?php
session_start();
header('Content-Type: application/json');
require_once '../includes/db.php';
$db = getDB();

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success'=>false, 'msg'=>'Unauthorized']); exit;
}

$action = $_POST['action'] ?? '';

try {
    if ($action === 'process_payment') {
        $id_datphong = $_POST['id_datphong'];
        $id_nhanvien = $_SESSION['user_id'];
        
        // Nhận dữ liệu chi tiết từ JS
        $tong_tien_phong = $_POST['tong_tien_phong'] ?? 0;
        $tong_tien_dichvu = $_POST['tong_tien_dichvu'] ?? 0;
        $tong_thanh_toan = $_POST['tong_thanh_toan'] ?? 0;
        
        // [MỚI] Nhận phương thức thanh toán (TienMat hoặc ChuyenKhoan)
        $phuong_thuc = $_POST['phuong_thuc'] ?? 'TienMat';

        // Sinh Mã Hóa Đơn Tự Động
        // Ví dụ: HD251121-X9Y8
        $ma_hoadon = 'HD' . date('ymd') . '-' . strtoupper(substr(md5(uniqid()), 0, 4));

        $db->beginTransaction();

        // 1. TẠO HÓA ĐƠN MỚI (Lưu cả phương thức thanh toán)
        $stmtHD = $db->prepare("INSERT INTO hoadon 
            (ma_hoadon, id_datphong, id_nhanvien_lap, tong_tien_phong, tong_tien_dichvu, tong_thanh_toan, phuong_thuc_thanh_toan) 
            VALUES (?, ?, ?, ?, ?, ?, ?)"); // Đã sửa thành 7 dấu hỏi
        
        $stmtHD->execute([
            $ma_hoadon,
            $id_datphong,
            $id_nhanvien,
            $tong_tien_phong,
            $tong_tien_dichvu,
            $tong_thanh_toan,
            $phuong_thuc // <--- Lưu giá trị 'TienMat' hoặc 'ChuyenKhoan'
        ]);

        // 2. CẬP NHẬT ĐẶT PHÒNG (Check-out & Lưu tổng tiền)
        $stmtDP = $db->prepare("UPDATE datphong SET 
            trang_thai = 'DaTraPhong', 
            ngay_checkout_thucte = NOW(),
            tong_tien = ? 
            WHERE id_datphong = ?");
        $stmtDP->execute([$tong_thanh_toan, $id_datphong]);

        // 3. TRẢ PHÒNG (Set về trạng thái Dọn dẹp)
        $stmtGetRoom = $db->prepare("SELECT id_phong FROM datphong WHERE id_datphong = ?");
        $stmtGetRoom->execute([$id_datphong]);
        $id_phong = $stmtGetRoom->fetchColumn();

        if ($id_phong) {
            $stmtRoom = $db->prepare("UPDATE phong SET trang_thai = 'DangDonDep' WHERE id_phong = ?");
            $stmtRoom->execute([$id_phong]);
        }

        $db->commit();
        
        echo json_encode(['success' => true, 'msg' => 'Thanh toán thành công! Mã HĐ: ' . $ma_hoadon]);
    } 
    else {
        echo json_encode(['success' => false, 'msg' => 'Unknown action']);
    }

} catch (Exception $e) {
    if ($db->inTransaction()) $db->rollBack();
    error_log("Payment Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'msg' => 'Lỗi: ' . $e->getMessage()]);
}
?>