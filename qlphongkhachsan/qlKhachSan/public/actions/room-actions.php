<?php
// File: public/actions/room-actions.php
session_start();
header('Content-Type: application/json; charset=utf-8'); // Header chuẩn UTF-8

require_once '../includes/db.php';

// Hàm trợ giúp gửi JSON
function send_json($data) {
    echo json_encode($data);
    exit;
}

// 1. Kiểm tra Request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_json(['success' => false, 'msg' => 'Yêu cầu không hợp lệ']);
}

// 2. Kiểm tra Đăng nhập
if (!isset($_SESSION['user_id'])) {
    send_json(['success' => false, 'msg' => 'Chưa đăng nhập']);
}

// 3. Kết nối Database
$db = getDB();
if (!$db) {
    send_json(['success' => false, 'msg' => 'Lỗi kết nối database']);
}

$action = $_POST['action'] ?? '';

try {
    switch ($action) {
        // === THÊM PHÒNG MỚI ===
        case 'create':
            // Lấy giá phòng (nếu rỗng thì = 0)
            $gia_phong = isset($_POST['gia_phong']) && $_POST['gia_phong'] !== '' ? floatval($_POST['gia_phong']) : 0;

            $stmt = $db->prepare("INSERT INTO phong (ten_phong, id_loaiphong, gia_phong, trang_thai, ghi_chu) 
                                 VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $_POST['ten_phong'],
                $_POST['id_loaiphong'],
                $gia_phong, // <--- Lưu giá phòng
                $_POST['trang_thai'],
                $_POST['ghi_chu']
            ]);
            send_json(['success' => true, 'msg' => 'Thêm phòng thành công!']);
            break;

        // === CẬP NHẬT THÔNG TIN ĐẦY ĐỦ ===
        case 'update':
            $gia_phong = isset($_POST['gia_phong']) && $_POST['gia_phong'] !== '' ? floatval($_POST['gia_phong']) : 0;

            $stmt = $db->prepare("UPDATE phong SET 
                                 ten_phong = ?, id_loaiphong = ?, gia_phong = ?, trang_thai = ?, ghi_chu = ?
                                 WHERE id_phong = ?");
            $stmt->execute([
                $_POST['ten_phong'],
                $_POST['id_loaiphong'],
                $gia_phong, // <--- Cập nhật giá phòng
                $_POST['trang_thai'], // Lưu ý: input hidden này trong form
                $_POST['ghi_chu'],
                $_POST['id_phong']
            ]);
            send_json(['success' => true, 'msg' => 'Cập nhật thông tin thành công!']);
            break;

        // === [MỚI] CẬP NHẬT TRẠNG THÁI NHANH ===
        case 'update_status':
            $stmt = $db->prepare("UPDATE phong SET trang_thai = ? WHERE id_phong = ?");
            $stmt->execute([
                $_POST['trang_thai'],
                $_POST['id_phong']
            ]);
            send_json(['success' => true, 'msg' => 'Trạng thái đã được cập nhật!']);
            break;

        // === XÓA PHÒNG ===
        case 'delete':
            $stmt = $db->prepare("DELETE FROM phong WHERE id_phong = ?");
            $stmt->execute([$_POST['id_phong']]);
            send_json(['success' => true, 'msg' => 'Xóa phòng thành công!']);
            break;

        default:
            send_json(['success' => false, 'msg' => 'Hành động không xác định']);
    }

} catch (PDOException $e) {
    $msg = $e->getMessage();
    // Bắt lỗi trùng tên phòng
    if (strpos($msg, 'Duplicate entry') !== false) {
        $msg = 'Tên phòng này đã tồn tại!';
    }
    error_log("Room Action Error: " . $e->getMessage());
    send_json(['success' => false, 'msg' => 'Lỗi: ' . $msg]);
}
?>