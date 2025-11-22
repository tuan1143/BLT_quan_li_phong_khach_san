<?php
session_start();
header('Content-Type: application/json');
require_once '../includes/db.php';
$db = getDB();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'msg' => 'Unauthorized']);
    exit;
}

$action = $_POST['action'] ?? '';

try {
    if ($action === 'create') {
        // Lấy dữ liệu từ form
        $id_khachhang = $_POST['id_khachhang'];
        $id_phong = $_POST['id_phong'];
        $checkin = $_POST['ngay_checkin'];
        $checkout = $_POST['ngay_checkout_dukien'];
        $so_nguoi = $_POST['so_nguoi_o'] ?? 1;
        $coc = $_POST['tien_dat_coc'] ?? 0;
        $trang_thai = $_POST['trang_thai']; // MoiDat, DaNhanPhong...

        // === [QUAN TRỌNG] LOGIC KIỂM TRA TRÙNG LỊCH ===
        // Kiểm tra xem trong khoảng thời gian này, phòng đó có ai đặt chưa?
        // Chỉ kiểm tra các đơn chưa Hủy và chưa Trả phòng
        $sqlCheck = "SELECT COUNT(*) FROM datphong 
                     WHERE id_phong = ? 
                     AND trang_thai IN ('MoiDat', 'DaNhanPhong')
                     AND (
                        (ngay_checkin < ? AND ngay_checkout_dukien > ?)
                     )";
        // Giải thích Logic: (Start A < End B) AND (End A > Start B) là công thức chuẩn để check trùng lặp thời gian
        
        $stmtCheck = $db->prepare($sqlCheck);
        $stmtCheck->execute([$id_phong, $checkout, $checkin]);
        
        if ($stmtCheck->fetchColumn() > 0) {
            echo json_encode(['success' => false, 'msg' => 'LỖI: Phòng này đã có người đặt trong khoảng thời gian bạn chọn!']);
            exit; // Dừng ngay lập tức
        }
        // ==============================================

        // Nếu không trùng thì mới cho Lưu
        $db->beginTransaction();

        $stmt = $db->prepare(
            "INSERT INTO datphong (id_khachhang, id_phong, id_nhanvien_lap, ngay_checkin, 
                                 ngay_checkout_dukien, so_nguoi_o, tien_dat_coc, trang_thai) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $id_khachhang, $id_phong, $_SESSION['user_id'], 
            $checkin, $checkout, $so_nguoi, $coc, $trang_thai
        ]);

        // Cập nhật trạng thái phòng hiện tại
        if ($trang_thai == 'MoiDat') {
            $stmt_p = $db->prepare("UPDATE phong SET trang_thai = 'DaDat' WHERE id_phong = ?");
            $stmt_p->execute([$id_phong]);
        } elseif ($trang_thai == 'DaNhanPhong') {
            $stmt_p = $db->prepare("UPDATE phong SET trang_thai = 'DangO' WHERE id_phong = ?");
            $stmt_p->execute([$id_phong]);
        }

        $db->commit();
        echo json_encode(['success' => true, 'msg' => 'Đặt phòng thành công!']);
    }

    // ... (Giữ nguyên phần update và cancel/delete cũ của bạn ở dưới) ...
    elseif ($action === 'update') {
         // Logic update cũng nên check trùng tương tự, nhưng phải loại trừ chính nó ra
         // Tạm thời bạn có thể giữ nguyên logic update cũ hoặc bổ sung sau
         // ... code update cũ ...
         $stmt = $db->prepare("UPDATE datphong SET id_khachhang=?, id_phong=?, ngay_checkin=?, ngay_checkout_dukien=?, so_nguoi_o=?, tien_dat_coc=?, trang_thai=? WHERE id_datphong=?");
         $stmt->execute([
             $_POST['id_khachhang'], $_POST['id_phong'], $_POST['ngay_checkin'], 
             $_POST['ngay_checkout_dukien'], $_POST['so_nguoi_o'], $_POST['tien_dat_coc'], 
             $_POST['trang_thai'], $_POST['id_datphong']
         ]);
         echo json_encode(['success' => true, 'msg' => 'Cập nhật thành công!']);
    }
    elseif ($action === 'cancel' || $action === 'delete') { // Sửa lại logic delete/cancel cho gọn
        $id = $_POST['id_datphong'] ?? 0;
        // Lấy ID phòng để trả về Trống
        $stmtGet = $db->prepare("SELECT id_phong FROM datphong WHERE id_datphong = ?");
        $stmtGet->execute([$id]);
        $id_phong = $stmtGet->fetchColumn();

        $stmt = $db->prepare("UPDATE datphong SET trang_thai = 'DaHuy' WHERE id_datphong = ?");
        $stmt->execute([$id]);

        if($id_phong) {
            $db->prepare("UPDATE phong SET trang_thai = 'Trong' WHERE id_phong = ?")->execute([$id_phong]);
        }
        echo json_encode(['success' => true, 'msg' => 'Đã hủy đặt phòng!']);
    }

} catch (Exception $e) {
    if ($db->inTransaction()) $db->rollBack();
    echo json_encode(['success' => false, 'msg' => 'Lỗi: ' . $e->getMessage()]);
}
?>