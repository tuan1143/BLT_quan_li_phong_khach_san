<div class="page-header">
    <h2>Danh sách phòng đang hoạt động</h2>
</div>

<div id="pageNotifications" class="alert-container"></div>

<div class="content-card">
    <table class="payments-table">
        <thead>
            <tr>
                <th>Phòng</th>
                <th>Khách hàng</th>
                <th>Ngày đến</th>
                <th>Giá phòng (VNĐ)</th>
                <th class="text-center">Tác vụ</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($bookings)): ?>
                <tr><td colspan="5" class="text-center">Không có phòng nào đang thuê.</td></tr>
            <?php else: ?>
                <?php foreach ($bookings as $row): ?>
                <?php $gia_thuc_te = ($row['gia_phong'] > 0) ? $row['gia_phong'] : $row['gia_loai_phong']; ?>
                <tr>
                    <td><span class="room-badge"><?php echo htmlspecialchars($row['ten_phong']); ?></span></td>
                    <td><strong><?php echo htmlspecialchars($row['ho_ten']); ?></strong></td>
                    <td><?php echo date('d/m/Y H:i', strtotime($row['ngay_checkin'])); ?></td>
                    <td><?php echo number_format($gia_thuc_te); ?></td>
                    <td class="text-center">
                        <button class="btn btn-checkout"
                            data-id="<?php echo $row['id_datphong']; ?>"
                            data-room="<?php echo htmlspecialchars($row['ten_phong']); ?>"
                            data-customer="<?php echo htmlspecialchars($row['ho_ten']); ?>"
                            data-checkin="<?php echo $row['ngay_checkin']; ?>"
                            data-price="<?php echo $gia_thuc_te; ?>"
                            data-deposit="<?php echo $row['tien_dat_coc']; ?>">
                            Thanh toán
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="modal-overlay" id="paymentModal" aria-hidden="true">
    <div class="modal-content modal-lg" style="max-width: 900px;">
        <h2 class="invoice-title">Thanh toán & Trả phòng</h2>
        
        <form id="paymentForm">
            <input type="hidden" name="id_datphong" id="payBookingId">
            <input type="hidden" name="action" value="process_payment">

            <div class="payment-container">
                <div class="bill-section">
                    <div class="info-group">
                        <div class="info-row">
                            <span class="label">Khách hàng:</span>
                            <strong id="payCustomer">...</strong>
                        </div>
                        <div class="info-row">
                            <span class="label">Phòng:</span>
                            <strong id="payRoom" class="highlight-text">...</strong>
                        </div>
                    </div>

                    <hr class="divider">

                    <div class="section-box">
                        <h4>1. Tiền phòng</h4>
                        <div class="flex-row">
                            <span>Check-in: <span id="payCheckin"></span></span>
                            <span>Check-out: <span id="payCheckout"></span></span>
                        </div>
                        <div class="flex-row math-row">
                            <span><b id="payDays">0</b> ngày x <b id="payPrice">0</b> đ</span>
                            <span class="money" id="payRoomTotal">0 đ</span>
                        </div>
                    </div>

                    <div class="section-box">
                        <div class="flex-row">
                            <h4>2. Dịch vụ</h4>
                            <button type="button" class="btn-add-service" id="btnAddService">+ Thêm món</button>
                        </div>
                        <div id="serviceList">
                            </div>
                        <div class="flex-row math-row total-service-row">
                            <span>Tổng dịch vụ:</span>
                            <span class="money" id="payServiceTotal">0 đ</span>
                        </div>
                    </div>

                    <div class="bill-summary">
                        <div class="sum-row">
                            <span>Tổng cộng:</span>
                            <span id="finalTotal">0 đ</span>
                        </div>
                        <div class="sum-row">
                            <span>Đã cọc:</span>
                            <span id="depositTotal" class="minus-money">0 đ</span>
                        </div>
                        <div class="sum-row grand-total">
                            <span>CẦN THANH TOÁN:</span>
                            <span id="grandTotal">0 đ</span>
                        </div>
                    </div>
                </div>

                <div class="payment-method-section">
                    <h4>Phương thức thanh toán</h4>
                    
                    <div class="method-tabs">
                        <div class="method-tab active" id="tabCash" onclick="selectMethod('TienMat')">
                            💵 Tiền mặt
                        </div>
                        <div class="method-tab" id="tabTransfer" onclick="selectMethod('ChuyenKhoan')">
                            🏦 Chuyển khoản
                        </div>
                        <input type="hidden" name="phuong_thuc" id="paymentMethodInput" value="TienMat">
                    </div>

                    <div class="method-content">
                        <div id="areaCash" class="method-area">
                            <div class="cash-icon">💰</div>
                            <p>Thu tiền mặt trực tiếp tại quầy.</p>
                        </div>

                        <div id="areaQR" class="method-area" style="display:none;">
                            <div class="qr-box">
                                <img id="vietqrImage" src="" alt="QR Code">
                            </div>
                            <p class="qr-hint">Khách quét mã để thanh toán</p>
                        </div>
                    </div>

                    <div class="action-buttons">
                        <button type="button" class="btn btn-cancel" id="btnCancelPay">Hủy bỏ</button>
                        <button type="submit" class="btn btn-confirm">Xác nhận đã thu tiền</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<div id="confirmModal" class="modal-overlay" style="z-index: 11000;">
    <div class="modal-content" style="max-width: 400px; text-align: center; padding-top: 30px;">
        <div class="confirm-icon">?</div>
        
        <h3 style="color: #E9C46A; margin: 20px 0 10px 0;">Xác nhận thanh toán</h3>
        
        <p id="confirmMessage" style="font-size: 15px; color: #ccc; margin-bottom: 30px; line-height: 1.5;">
            </p>

        <div class="modal-actions" style="justify-content: center; gap: 15px;">
            <button type="button" id="btnConfirmNo" class="btn" style="background: #444; color: #fff; min-width: 100px;">Xem lại</button>
            <button type="button" id="btnConfirmYes" class="btn btn-primary" style="min-width: 100px;">Đồng ý</button>
        </div>
    </div>
</div>