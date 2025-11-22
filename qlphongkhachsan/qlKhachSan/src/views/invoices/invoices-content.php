<div class="page-header">
    <h2>Danh sách hóa đơn đã thanh toán</h2>
    
    <div class="header-actions">
        <form action="invoices.php" method="GET" class="search-box">
            <input type="text" name="search" 
                   placeholder="Nhập Mã HĐ hoặc CCCD khách..." 
                   value="<?php echo htmlspecialchars($search); ?>"
                   autocomplete="off">
            <?php if($search): ?>
                <a href="invoices.php" class="btn-clear">×</a>
            <?php endif; ?>
            <button type="submit" class="btn-search">🔍</button>
        
        </form>
            <a href="export_invoices.php" target="_blank" class="btn btn-excel" style="height: 38px; box-sizing: border-box;">
            <span style="margin-right: 5px;">📥</span> Xuất Báo Cáo
        </a>
    </div>
</div>

<div class="content-card">
    <table class="invoices-table">
        <thead>
            <tr>
                <th>Mã HĐ</th>
                <th>Ngày thanh toán</th>
                <th>Khách hàng</th>
                <th class="text-right">Tổng tiền (VNĐ)</th>
                <th class="text-center">Chi tiết</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($hoadons)): ?>
                <tr><td colspan="5" class="text-center">Không tìm thấy hóa đơn nào.</td></tr>
            <?php else: ?>
                <?php foreach ($hoadons as $hd): ?>
                <tr>
                    <td>
                        <span class="invoice-id">
                            <?php echo htmlspecialchars($hd['ma_hoadon'] ?? ('#' . $hd['id_hoadon'])); ?>
                        </span>
                    </td>
                    
                    <td><?php echo date('d/m/Y H:i', strtotime($hd['ngay_lap'])); ?></td>
                    
                    <td>
                        <strong><?php echo htmlspecialchars($hd['ten_khach']); ?></strong>
                        <div style="font-size:11px; color:#888;"><?php echo htmlspecialchars($hd['cmnd_cccd']); ?></div>
                    </td>
                    
                    <td class="text-right">
                        <span class="money-total"><?php echo number_format($hd['tong_thanh_toan']); ?></span>
                    </td>
                    
                    <td class="text-center">
                        <button class="btn btn-view-detail"
                            data-code="<?php echo htmlspecialchars($hd['ma_hoadon'] ?? $hd['id_hoadon']); ?>"
                            data-date="<?php echo date('d/m/Y H:i', strtotime($hd['ngay_lap'])); ?>"
                            data-customer="<?php echo htmlspecialchars($hd['ten_khach']); ?>"
                            data-room="<?php echo htmlspecialchars($hd['ten_phong']); ?>"
                            data-staff="<?php echo htmlspecialchars($hd['ten_nhanvien']); ?>"
                            data-room-money="<?php echo number_format($hd['tong_tien_phong']); ?>"
                            data-service-money="<?php echo number_format($hd['tong_tien_dichvu']); ?>"
                            data-total="<?php echo number_format($hd['tong_thanh_toan']); ?>"
                            data-method="<?php echo htmlspecialchars($hd['phuong_thuc_thanh_toan']); ?>">
                            Xem
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
        renderPagination($total_pages, $page, 'invoices.php'); 
    }
    ?>
</div>

<div id="invoiceModal" class="modal-overlay" aria-hidden="true">
    <div class="modal-content">
        <h2 class="invoice-title" style="border-bottom: 1px dashed #444;">Chi tiết Hóa đơn</h2>
        
        <div class="invoice-details">
            <div class="detail-row">
                <span>Mã hóa đơn:</span>
                <span id="detCode" class="invoice-id">...</span>
            </div>
            <div class="detail-row">
                <span>Ngày lập:</span>
                <strong id="detDate">...</strong>
            </div>
            <div class="detail-row">
                <span>Khách hàng:</span>
                <strong id="detCustomer">...</strong>
            </div>
            <div class="detail-row">
                <span>Phòng:</span>
                <strong id="detRoom" class="room-tag">...</strong>
            </div>
             <div class="detail-row">
                <span>Người lập:</span>
                <span id="detStaff">...</span>
            </div>
            
            <hr class="luxury-divider">
            
            <div class="detail-row">
                <span>Tiền phòng:</span>
                <span id="detRoomMoney">0</span>
            </div>
            <div class="detail-row">
                <span>Dịch vụ:</span>
                <span id="detServiceMoney">0</span>
            </div>
            
            <div class="detail-row grand-total-row">
                <span>TỔNG THANH TOÁN:</span>
                <span id="detTotal" class="money-total">0</span>
            </div>
             <div class="detail-row">
                <span>Phương thức:</span>
                <span id="detMethod" style="font-style:italic; color:#888">...</span>
            </div>
        </div>

        <div class="modal-actions" style="justify-content: center; margin-top: 20px;">
            <button type="button" id="btnCloseModal" class="btn">Đóng</button>
            </div>
    </div>
</div>