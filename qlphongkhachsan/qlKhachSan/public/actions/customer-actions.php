<?php
// File: public/actions/customer-actions.php
session_start();
header('Content-Type: application/json; charset=utf-8'); // Đặt header ngay đầu file

require_once '../includes/db.php'; // Đi lùi 1 cấp

// Hàm trợ giúp để gửi JSON và thoát
function send_json($data) {
    echo json_encode($data);
    exit;
}

// 1. Kiểm tra Request Method
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
        case 'create':
            $stmt = $db->prepare("INSERT INTO khachhang (ho_ten, cmnd_cccd, so_dien_thoai, email, dia_chi, quoc_tich) 
                                 VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $_POST['ho_ten'],
                $_POST['cmnd_cccd'],
                $_POST['so_dien_thoai'],
                $_POST['email'],
                $_POST['dia_chi'],
                $_POST['quoc_tich']
            ]);
            // [QUAN TRỌNG] Thêm msg để JS hiển thị thông báo
            send_json(['success' => true, 'msg' => 'Thêm khách hàng thành công!']);
            break;

        case 'update':
            $stmt = $db->prepare("UPDATE khachhang SET 
                                 ho_ten = ?, cmnd_cccd = ?, so_dien_thoai = ?, 
                                 email = ?, dia_chi = ?, quoc_tich = ?
                                 WHERE id_khachhang = ?");
            $stmt->execute([
                $_POST['ho_ten'],
                $_POST['cmnd_cccd'],
                $_POST['so_dien_thoai'],
                $_POST['email'],
                $_POST['dia_chi'],
                $_POST['quoc_tich'],
                $_POST['id_khachhang']
            ]);
            // [QUAN TRỌNG] Thêm msg
            send_json(['success' => true, 'msg' => 'Cập nhật thông tin thành công!']);
            break;

        case 'delete':
            $stmt = $db->prepare("DELETE FROM khachhang WHERE id_khachhang = ?");
            $stmt->execute([$_POST['id_khachhang']]);
            // [QUAN TRỌNG] Thêm msg
            send_json(['success' => true, 'msg' => 'Xóa khách hàng thành công!']);
            break;

        default:
            send_json(['success' => false, 'msg' => 'Hành động không xác định']);
    }

} catch (PDOException $e) {
    $msg = $e->getMessage();
    // Bắt lỗi UNIQUE (trùng CMND, SĐT, Email)
    if (strpos($msg, 'Duplicate entry') !== false) {
        $msg = 'Lỗi: Số CMND/CCCD, SĐT, hoặc Email này đã tồn tại trong hệ thống!';
    }
    
    // Ghi log lỗi để admin kiểm tra nếu cần
    error_log("Customer Action Error: " . $e->getMessage());
    
    send_json(['success' => false, 'msg' => $msg]);
}
?>