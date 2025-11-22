

<div class="page-header">
    <h2>Danh sách đặt phòng</h2>
   <div class="header-actions">
        <form action="bookings.php" method="GET" class="search-box">
            <input type="hidden" name="status" value="<?php echo htmlspecialchars($status); ?>">
            
            <input type="text" name="search" 
                   placeholder="Tên khách, Phòng, SĐT..." 
                   value="<?php echo htmlspecialchars($search); ?>"
                   autocomplete="off">
            
            <?php if($search): ?>
                <a href="bookings.php?status=<?php echo $status; ?>" class="btn-clear">×</a>
            <?php endif; ?>
            
            <button type="submit" class="btn-search">🔍</button>
        </form>

        <button id="btnAddBooking" class="btn btn-primary">
            + Đặt phòng mới
        </button>
    </div>
</div>

<div class="filter-bar">
    <?php 
        // Hàm tạo link giữ search param
        function makeLink($stt, $search_term) {
            $link = "bookings.php?status=$stt";
            if ($search_term) $link .= "&search=" . urlencode($search_term);
            return $link;
        }
    ?>
    <a href="<?php echo makeLink('all', $search); ?>" class="filter-item <?php echo $status === 'all' ? 'active' : ''; ?>">
        Tất cả
    </a>
    <a href="<?php echo makeLink('MoiDat', $search); ?>" class="filter-item <?php echo $status === 'MoiDat' ? 'active' : ''; ?>">
        Mới đặt (Cọc)
    </a>
    <a href="<?php echo makeLink('DaNhanPhong', $search); ?>" class="filter-item <?php echo $status === 'DaNhanPhong' ? 'active' : ''; ?>">
        Đang ở
    </a>
    <a href="<?php echo makeLink('DaTraPhong', $search); ?>" class="filter-item <?php echo $status === 'DaTraPhong' ? 'active' : ''; ?>">
        Lịch sử (Đã trả)
    </a>
    <a href="<?php echo makeLink('DaHuy', $search); ?>" class="filter-item <?php echo $status === 'DaHuy' ? 'active' : ''; ?>">
        Đã hủy
    </a>
</div>

<div class="content-card">
    <table class="bookings-table">
        <thead>
            <tr>
                <th>Phòng</th>
                <th>Khách hàng</th>
                <th>Check-in</th>
                <th>Check-out (Dự kiến)</th>
                <th>Tiền cọc</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($danh_sach_dat_phong)): ?>
                <tr>
                    <td colspan="7">Chưa có lượt đặt phòng nào.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($danh_sach_dat_phong as $dp): ?>
                    <!-- Gắn data-* cho TẤT CẢ các cột để JS "Sửa" -->
                    <tr data-id_datphong="<?php echo $dp['id_datphong']; ?>"
                        data-id_khachhang="<?php echo $dp['id_khachhang']; ?>"
                        data-id_phong="<?php echo $dp['id_phong']; ?>"
                        data-ngay_checkin="<?php echo $dp['ngay_checkin']; ?>"
                        data-ngay_checkout_dukien="<?php echo $dp['ngay_checkout_dukien']; ?>"
                        data-so_nguoi_o="<?php echo $dp['so_nguoi_o']; ?>"
                        data-tien_dat_coc="<?php echo $dp['tien_dat_coc']; ?>"
                        data-trang_thai="<?php echo $dp['trang_thai']; ?>"
                    >
                        <td><?php echo htmlspecialchars($dp['ten_phong']); ?></td>
                        <td><?php echo htmlspecialchars($dp['ten_khach']); ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($dp['ngay_checkin'])); ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($dp['ngay_checkout_dukien'])); ?></td>
                        <td><?php echo number_format($dp['tien_dat_coc'], 0, ',', '.'); ?> đ</td>
                        <td>
                            <?php $status_class = strtolower(htmlspecialchars($dp['trang_thai'])); ?>
                            <span class="status-badge status-<?php echo $status_class; ?>">
                                <?php echo htmlspecialchars($dp['trang_thai']); ?>
                            </span>
                        </td>
                        <td class="table-actions">
                            <button class="btn edit-btn">Sửa</button>
                            <!-- Nút Hủy, không phải Xóa -->
                            <button class="btn cancel-btn btn-danger">Hủy</button> 
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- 
    MODAL (POP-UP) ĐỂ THÊM/SỬA ĐẶT PHÒNG
-->
<div id="bookingModal" class="modal-overlay" aria-hidden="true">
    <div class="modal-content">
        <h2 id="modalTitle">Đặt phòng mới</h2>
        <form id="bookingForm">
            <input type="hidden" name="id_datphong" id="id_datphong" value="">

            <div class="form-group">
                <label for="id_khachhang">Khách hàng</label>
                <select name="id_khachhang" id="id_khachhang" required>
                    <option value="">-- Chọn khách hàng --</option>
                    <?php foreach ($danh_sach_khach_hang as $kh): ?>
                        <option value="<?php echo $kh['id_khachhang']; ?>">
                            <?php echo htmlspecialchars($kh['ho_ten']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

          <div class="form-group">
                <label>Chọn phòng *</label>
                <select name="id_phong" id="bookingRoom" required>
                    <option value="">-- Chọn phòng --</option>
                    <?php foreach ($danh_sach_phong as $p): ?>
                        <?php 
                            // Tạo ký hiệu trạng thái
                            $statusLabel = '';
                            $colorStyle = '';
                            switch($p['trang_thai']) {
                                case 'Trong': 
                                    $statusLabel = '(Trống)'; 
                                    $colorStyle = 'color: #4cd137; font-weight:bold;'; // Xanh lá
                                    break;
                                case 'DangO': 
                                    $statusLabel = '(Đang ở)'; 
                                    $colorStyle = 'color: #ff6b6b;'; // Đỏ
                                    break;
                                case 'DaDat': 
                                    $statusLabel = '(Đã đặt)'; 
                                    $colorStyle = 'color: #E9C46A;'; // Vàng
                                    break;
                                default: $statusLabel = '('.$p['trang_thai'].')';
                            }
                        ?>
                        <option value="<?php echo $p['id_phong']; ?>" style="<?php echo $colorStyle; ?>">
                            <?php echo htmlspecialchars($p['ten_phong']); ?> - <?php echo $statusLabel; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

         


            <div class="form-group">
                <label for="ngay_checkin">Ngày Check-in</label>
                <!-- Dùng datetime-local cho tiện -->
                <input type="datetime-local" name="ngay_checkin" id="ngay_checkin" required>
            </div>

            <div class="form-group">
                <label for="ngay_checkout_dukien">Ngày Check-out (Dự kiến)</label>
                <input type="datetime-local" name="ngay_checkout_dukien" id="ngay_checkout_dukien" required>
            </div>
            
             <div class="form-group">
                <label for="so_nguoi_o">Số người ở</label>
                <input type="number" name="so_nguoi_o" id="so_nguoi_o" min="1" value="1">
            </div>

            <div class="form-group">
                <label for="tien_dat_coc">Tiền đặt cọc</label>
                <input type="number" name="tien_dat_coc" id="tien_dat_coc" min="0" value="0">
            </div>

            <div class="form-group">
                <label for="trang_thai">Trạng thái</label>
                <select name="trang_thai" id="trang_thai" required>
                    <option value="MoiDat">Mới Đặt</option>
                    <option value="DaNhanPhong">Đã Nhận Phòng</option>
                    <option value="DaTraPhong">Đã Trả Phòng</option>
                    <option value="DaHuy">Đã Hủy</option>
                </select>
            </div>

            <div class="modal-actions">
                <button type="submit" class="btn btn-primary">Lưu lại</button>
                <button type="button" id="btnCancel" class="btn">Hủy</button>
            </div>
        </form>
    </div>
</div>