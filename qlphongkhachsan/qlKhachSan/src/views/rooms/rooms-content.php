<?php
// Hàm hiển thị trạng thái (Badge)
if (!function_exists('getStatusBadge')) {
    function getStatusBadge($status) {
        switch ($status) {
            case 'Trong': return '<span class="status-badge status-trong">Trống</span>';
            case 'DangO': return '<span class="status-badge status-dango">Đang ở</span>';
            case 'DaDat': return '<span class="status-badge status-dadat">Đã đặt</span>';
            case 'DangDonDep': return '<span class="status-badge status-dondep">Dọn dẹp</span>';
            case 'BaoTri': return '<span class="status-badge status-baotri">Bảo trì</span>';
            default: return '<span class="status-badge">' . $status . '</span>';
        }
    }
}
?>

<div class="page-header">
    <h2>Danh sách phòng đang hoạt động</h2>
    
    <div class="header-actions">

  <button id="btnAddRoom" class="btn btn-primary">
            + Thêm phòng mới
        </button>

        <a href="export_rooms.php" target="_blank" class="btn btn-excel">
            <span style="margin-right: 5px;">📥</span> Xuất báo cáo
        </a>

      
    </div>
</div>

<div id="pageNotifications" class="alert-container"></div>

<div class="filter-bar">
    <a href="rooms.php?status=all" class="filter-item <?php echo $filter_status === 'all' ? 'active' : ''; ?>">
        Tất cả
    </a>
    <a href="rooms.php?status=Trong" class="filter-item <?php echo $filter_status === 'Trong' ? 'active' : ''; ?>">
        ✅ Trống
    </a>
    <a href="rooms.php?status=DangO" class="filter-item <?php echo $filter_status === 'DangO' ? 'active' : ''; ?>">
        🔴 Đang ở
    </a>
    <a href="rooms.php?status=DaDat" class="filter-item <?php echo $filter_status === 'DaDat' ? 'active' : ''; ?>">
        🟡 Đã đặt
    </a>
    <a href="rooms.php?status=DangDonDep" class="filter-item <?php echo $filter_status === 'DangDonDep' ? 'active' : ''; ?>">
        🧹 Dọn dẹp
    </a>
    <a href="rooms.php?status=BaoTri" class="filter-item <?php echo $filter_status === 'BaoTri' ? 'active' : ''; ?>">
        🛠 Bảo trì
    </a>
</div>

<div class="content-card">
    <table class="rooms-table">
        <thead>
            <tr>
                <th style="width: 20%;">Tên phòng</th>
                <th style="width: 25%;">Giá phòng (VNĐ)</th>
                <th style="width: 25%;">Trạng thái</th>
                <th class="text-center">Tác vụ</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($danh_sach_phong)): ?>
                <tr><td colspan="4" class="text-center">Chưa có dữ liệu phòng.</td></tr>
            <?php else: ?>
                <?php foreach ($danh_sach_phong as $phong): ?>
                <tr>
                    <td><span class="room-badge"><?php echo htmlspecialchars($phong['ten_phong']); ?></span></td>
                    
                    <td>
                        <?php 
                            if (!empty($phong['gia_phong']) && $phong['gia_phong'] > 0) {
                                echo '<span style="color:#E9C46A; font-weight:bold;">' . number_format($phong['gia_phong']) . '</span>';
                            } else {
                                echo '<span style="color:#aaa;">' . number_format($phong['gia_mac_dinh'] ?? 0) . '</span>';
                            }
                        ?>
                    </td>

                    <td>
                        <div style="cursor:pointer;" class="btn-quick-status" 
                             data-id="<?php echo $phong['id_phong']; ?>" 
                             data-status="<?php echo $phong['trang_thai']; ?>">
                            <?php echo getStatusBadge($phong['trang_thai']); ?> 
                            <small style="color:#666; font-size:10px;">(Sửa)</small>
                        </div>
                    </td>
                    
                    <td class="room-actions text-center">
                        <button class="btn btn-view" 
                            data-id="<?php echo $phong['id_phong']; ?>"
                            data-ten="<?php echo htmlspecialchars($phong['ten_phong']); ?>"
                            data-loai-ten="<?php echo htmlspecialchars($phong['ten_loaiphong'] ?? 'Chưa set'); ?>"
                            data-loai-id="<?php echo $phong['id_loaiphong']; ?>"
                            data-price="<?php echo $phong['gia_phong']; ?>"
                            data-status="<?php echo $phong['trang_thai']; ?>"
                            data-note="<?php echo htmlspecialchars($phong['ghi_chu']); ?>">
                            Chi tiết
                        </button>
                        
                        <button class="btn btn-delete" 
                            data-id="<?php echo $phong['id_phong']; ?>">
                            Xóa
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    
    <?php 
    if (file_exists(PROJECT_ROOT . '/public/includes/paginator.php')) {
        require_once PROJECT_ROOT . '/public/includes/paginator.php';
        renderPagination($total_pages, $page, 'rooms.php'); 
    }
    ?>
</div>

<div class="modal-overlay" id="roomModal" aria-hidden="true">
    <div class="modal-content">
        <h2 id="modalTitle">Chi tiết phòng</h2>
        <form id="roomForm">
            <input type="hidden" name="id_phong" id="roomId">
            <input type="hidden" name="action" id="formAction" value="create">

            <div class="form-group">
                <label>Tên phòng <span style="color:red">*</span></label>
                <input type="text" name="ten_phong" id="roomName" required>
            </div>

            <div class="form-group">
                <label>Loại phòng</label>
                <select name="id_loaiphong" id="roomType">
                    <?php foreach ($danh_sach_loai_phong as $lp): ?>
                        <option value="<?php echo $lp['id_loaiphong']; ?>">
                            <?php echo htmlspecialchars($lp['ten_loaiphong']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Giá riêng (VNĐ)</label>
                <input type="number" name="gia_phong" id="roomPrice" placeholder="Nhập 0 nếu theo giá loại">
            </div>

            <div class="form-group">
                <label>Ghi chú</label>
                <textarea name="ghi_chu" id="roomNote" rows="3"></textarea>
            </div>
            
            <input type="hidden" name="trang_thai" id="roomStatusHidden">

            <div class="modal-actions">
                <button type="button" class="btn" id="btnCancel">Đóng</button>
                <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
            </div>
        </form>
    </div>
</div>

<div class="modal-overlay" id="statusModal" aria-hidden="true" style="z-index: 1050;">
    <div class="modal-content" style="max-width: 350px;">
        <h3 style="color:#E9C46A; text-align:center; margin-bottom: 20px;">Cập nhật trạng thái</h3>
        <form id="statusForm">
            <input type="hidden" name="id_phong" id="statusRoomId">
            <input type="hidden" name="action" value="update_status">
            
            <div class="form-group">
                <label>Chọn trạng thái mới:</label>
                <select name="trang_thai" id="quickStatusSelect" style="padding: 12px; font-size: 15px;">
                    <option value="Trong">✅ Trống (Sẵn sàng)</option>
                    <option value="DangO">🔴 Đang ở</option>
                    <option value="DaDat">🟡 Đã đặt</option>
                    <option value="DangDonDep">🧹 Đang dọn dẹp</option>
                    <option value="BaoTri">🛠 Bảo trì</option>
                </select>
            </div>

            <div class="modal-actions" style="justify-content: center;">
                <button type="button" class="btn" id="btnCancelStatus">Hủy</button>
                <button type="submit" class="btn btn-primary">Cập nhật</button>
            </div>
        </form>
    </div>
</div>