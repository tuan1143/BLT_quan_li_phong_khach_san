<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

// === PHẦN DEBUG (GHI NHẬT KÝ) ===
// Dòng này sẽ ghi tất cả dữ liệu nhận được vào file debug_rooms.txt
$log_data = "Thời gian: " . date('Y-m-d H:i:s') . "\n";
$log_data .= "Dữ liệu nhận được (POST): " . print_r($_POST, true) . "\n";
file_put_contents('debug_rooms.txt', $log_data, FILE_APPEND);
// ================================

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'msg' => 'Chưa đăng nhập']);
    exit;
}

require_once '../includes/db.php';
$db = getDB();

if (!$db) {
    echo json_encode(['success' => false, 'msg' => 'Lỗi kết nối Database']);
    exit;
}

$action = $_POST['action'] ?? '';

try {
    // Xử lý giá phòng: Chuyển chuỗi rỗng thành 0, ép kiểu số thực
    $gia_phong = 0;
    if (isset($_POST['gia_phong']) && $_POST['gia_phong'] !== '') {
        $gia_phong = floatval($_POST['gia_phong']);
    }

    if ($action === 'create') {
        $stmt = $db->prepare("INSERT INTO phong (ten_phong, id_loaiphong, gia_phong, trang_thai, ghi_chu) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['ten_phong'],
            $_POST['id_loaiphong'],
            $gia_phong, // <--- Debug: Kiểm tra xem biến này có giá trị không
            $_POST['trang_thai'],
            $_POST['ghi_chu']
        ]);
        echo json_encode(['success' => true, 'msg' => 'Thêm thành công!']);
    } 
    
    elseif ($action === 'update') {
        $stmt = $db->prepare("UPDATE phong SET ten_phong=?, id_loaiphong=?, gia_phong=?, trang_thai=?, ghi_chu=? WHERE id_phong=?");
        $stmt->execute([
            $_POST['ten_phong'],
            $_POST['id_loaiphong'],
            $gia_phong, // <--- Debug: Kiểm tra xem biến này có giá trị không
            $_POST['trang_thai'],
            $_POST['ghi_chu'],
            $_POST['id_phong']
        ]);
        echo json_encode(['success' => true, 'msg' => 'Cập nhật thành công!']);
    } 
    
    elseif ($action === 'delete') {
        $stmt = $db->prepare("DELETE FROM phong WHERE id_phong=?");
        $stmt->execute([$_POST['id_phong']]);
        echo json_encode(['success' => true]);
    } 
    
    else {
        echo json_encode(['success' => false, 'msg' => 'Hành động không xác định']);
    }

} catch (PDOException $e) {
    // Ghi lỗi vào file debug luôn
    file_put_contents('debug_rooms.txt', "LỖI SQL: " . $e->getMessage() . "\n", FILE_APPEND);
    
    echo json_encode(['success' => false, 'msg' => 'Lỗi: ' . $e->getMessage()]);
}
?>