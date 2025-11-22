document.addEventListener('DOMContentLoaded', function () {
    // ================= CẤU HÌNH NGÂN HÀNG =================
    const MY_BANK = {
        BANK_ID: 'TCB',       // Ngân hàng MB (hoặc VCB, BIDV, TCB...)
        ACCOUNT_NO: '19039832764013', // Số tài khoản
        TEMPLATE: 'compact2', // Giao diện: compact2 (đẹp nhất), qr_only, print
        ACCOUNT_NAME: 'NGUYEN THANH TRUNG' // Tên chủ tài khoản (viết hoa không dấu)
    };

    // --- 1. KHAI BÁO CÁC ELEMENT ---
    const modal = document.getElementById('paymentModal');
    const form = document.getElementById('paymentForm');
    const btnCancel = document.getElementById('btnCancelPay');
    const btnAddService = document.getElementById('btnAddService');
    const serviceList = document.getElementById('serviceList');
    
    const paymentMethodInput = document.getElementById('paymentMethodInput');
    const tabCash = document.getElementById('tabCash');
    const tabTransfer = document.getElementById('tabTransfer');
    
    const areaCash = document.getElementById('areaCash');
    const areaQR = document.getElementById('areaQR');
    const imgQR = document.getElementById('vietqrImage');
    const qrHint = document.querySelector('.qr-hint'); // Dòng chữ dưới QR

    let currentGrandTotal = 0; 
    let currentRoomPrice = 0;
    let currentDeposit = 0;
    let daysStayed = 0;

    // --- 2. XỬ LÝ CHUYỂN TAB ---
    function switchMethod(method) {
        paymentMethodInput.value = method;

        if (method === 'ChuyenKhoan') {
            tabTransfer.classList.add('active');
            tabCash.classList.remove('active');
            areaQR.style.display = 'block';
            areaCash.style.display = 'none';
            
            // Sinh lại mã QR ngay khi chuyển tab
            generateVietQR(currentGrandTotal);
        } else {
            tabCash.classList.add('active');
            tabTransfer.classList.remove('active');
            areaCash.style.display = 'block';
            areaQR.style.display = 'none';
        }
    }

    if (tabCash) tabCash.addEventListener('click', function() { switchMethod('TienMat'); });
    if (tabTransfer) tabTransfer.addEventListener('click', function() { switchMethod('ChuyenKhoan'); });

    // --- 3. HÀM SINH MÃ VIETQR (ĐÃ SỬA LỖI) ---
    function generateVietQR(amount) {
        // [QUAN TRỌNG] Nếu tiền <= 0 thì KHÔNG sinh mã QR
        if (amount <= 0) {
            imgQR.style.display = 'none'; // Ẩn ảnh lỗi đi
            
            let refundAmount = Math.abs(amount); // Lấy giá trị dương để hiển thị
            qrHint.innerHTML = `<span style="color:#ff6b6b; font-weight:bold; font-size:14px;">
                ⚠️ Khách đã cọc thừa ${formatMoney(refundAmount)}.<br>
                Vui lòng hoàn tiền mặt cho khách!
            </span>`;
            return;
        }

        // Nếu tiền > 0 thì hiện ảnh và sinh mã
        imgQR.style.display = 'inline-block';
        qrHint.innerHTML = 'Khách quét mã để thanh toán';

        const bookingRoom = document.getElementById('payRoom').textContent;
        // Xóa ký tự đặc biệt để tránh lỗi URL
        const cleanRoomName = bookingRoom.replace(/[^a-zA-Z0-9 ]/g, "");
        const content = `TT PHONG ${cleanRoomName}`; 
        
        // URL API VietQR
        const url = `https://img.vietqr.io/image/${MY_BANK.BANK_ID}-${MY_BANK.ACCOUNT_NO}-${MY_BANK.TEMPLATE}.png?amount=${amount}&addInfo=${encodeURIComponent(content)}&accountName=${encodeURIComponent(MY_BANK.ACCOUNT_NAME)}`;
        
        imgQR.src = url;
    }

    // --- 4. LOGIC MỞ MODAL & TÍNH TIỀN ---
    window.openPaymentModal = function(btn) {
        const data = btn.dataset;
        
        document.getElementById('payBookingId').value = data.id;
        document.getElementById('payCustomer').textContent = data.customer;
        document.getElementById('payRoom').textContent = data.room;
        document.getElementById('payCheckin').textContent = formatDate(data.checkin);
        document.getElementById('payCheckout').textContent = formatDate(new Date());

        const checkinDate = new Date(data.checkin);
        const diffTime = Math.abs(new Date() - checkinDate);
        daysStayed = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) || 1;
        
        currentRoomPrice = parseInt(data.price);
        currentDeposit = parseInt(data.deposit);

        const roomTotal = daysStayed * currentRoomPrice;

        document.getElementById('payDays').textContent = daysStayed;
        document.getElementById('payPrice').textContent = formatMoney(currentRoomPrice);
        document.getElementById('payRoomTotal').textContent = formatMoney(roomTotal);
        document.getElementById('depositTotal').textContent = '- ' + formatMoney(currentDeposit);

        serviceList.innerHTML = '';
        
        // Tính toán lần đầu
        calculateTotal();
        
        // Mặc định về Tiền mặt
        switchMethod('TienMat');
        
        modal.setAttribute('aria-hidden', 'false');
    };

    document.querySelector('.payments-table').addEventListener('click', e => {
        const btn = e.target.closest('.btn-checkout');
        if (btn) window.openPaymentModal(btn);
    });

    btnCancel.onclick = () => modal.setAttribute('aria-hidden', 'true');

    btnAddService.onclick = () => {
        const div = document.createElement('div');
        div.className = 'service-item';
        div.innerHTML = `
            <input type="text" name="service_name[]" class="srv-input srv-name" placeholder="Tên dịch vụ" required>
            <input type="number" name="service_price[]" class="srv-input srv-price" value="0" min="0" oninput="calculateTotal()">
            <button type="button" class="btn-del-srv" onclick="this.parentElement.remove(); calculateTotal()">×</button>
        `;
        serviceList.appendChild(div);
    };

    // Hàm tính tổng (được gán window để gọi từ oninput)
    window.calculateTotal = function() {
        const roomTotal = daysStayed * currentRoomPrice;
        
        let serviceTotal = 0;
        document.querySelectorAll('.srv-price').forEach(i => serviceTotal += parseInt(i.value) || 0);
        
        document.getElementById('payServiceTotal').textContent = formatMoney(serviceTotal);
        document.getElementById('finalTotal').textContent = formatMoney(roomTotal + serviceTotal);
        
        // Tính số tiền cuối cùng
        currentGrandTotal = (roomTotal + serviceTotal) - currentDeposit;
        document.getElementById('grandTotal').textContent = formatMoney(currentGrandTotal);

        // Cập nhật QR nếu đang ở tab Chuyển khoản
        if (paymentMethodInput.value === 'ChuyenKhoan') {
            generateVietQR(currentGrandTotal);
        }
    };

    // --- 5. XỬ LÝ SUBMIT ---
   // --- KHAI BÁO MODAL XÁC NHẬN ---
    const confirmModal = document.getElementById('confirmModal');
    const btnConfirmYes = document.getElementById('btnConfirmYes');
    const btnConfirmNo = document.getElementById('btnConfirmNo');
    const confirmMessage = document.getElementById('confirmMessage');

    // Biến lưu trữ dữ liệu form tạm thời để chờ xác nhận
    let pendingFormData = null;

    // 1. Thay thế sự kiện Submit mặc định
    form.addEventListener('submit', e => {
        e.preventDefault();
        
        // Tạo thông báo dựa trên số tiền
        let msgHtml = '';
        if (currentGrandTotal > 0) {
            msgHtml = `Bạn có chắc chắn đã thu đủ <br><b style="color:#E9C46A; font-size:1.2em">${formatMoney(currentGrandTotal)}</b><br>từ khách hàng không?`;
        } else if (currentGrandTotal < 0) {
            msgHtml = `Bạn có chắc chắn đã HOÀN TRẢ <br><b style="color:#ff6b6b; font-size:1.2em">${formatMoney(Math.abs(currentGrandTotal))}</b><br>cho khách hàng không?`;
        } else {
            msgHtml = 'Khách đã thanh toán đủ cọc.<br>Xác nhận hoàn tất trả phòng?';
        }

        // Hiển thị Modal Xác nhận
        confirmMessage.innerHTML = msgHtml;
        confirmModal.setAttribute('aria-hidden', 'false');

        // Lưu dữ liệu form vào biến tạm
        pendingFormData = new FormData(form);
        
        // Append thêm các dữ liệu tính toán (vì FormData chỉ lấy input)
        let serviceTotal = 0;
        document.querySelectorAll('.srv-price').forEach(i => serviceTotal += parseInt(i.value) || 0);
        const roomTotal = daysStayed * currentRoomPrice;

        pendingFormData.append('tong_tien_phong', roomTotal);
        pendingFormData.append('tong_tien_dichvu', serviceTotal);
        pendingFormData.append('tong_thanh_toan', currentGrandTotal);
    });

    // 2. Xử lý khi bấm "Đồng ý"
    btnConfirmYes.addEventListener('click', function() {
        // Ẩn modal xác nhận
        confirmModal.setAttribute('aria-hidden', 'true');
        
        if (!pendingFormData) return;

        // Gửi AJAX
        const actionsUrl = window.paymentConfig ? window.paymentConfig.actionsUrl : 'actions/payment-actions.php';
        
        fetch(actionsUrl, { method: 'POST', body: pendingFormData })
        .then(r => r.json())
        .then(j => {
            if(j.success) {
                // Hiện thông báo thành công và reload
                sessionStorage.setItem('flash_msg', j.msg || 'Thanh toán thành công!');
                location.reload();
            } else {
                alert(j.msg); // Lỗi thì vẫn dùng alert hoặc toast đỏ
            }
        })
        .catch(err => {
            console.error(err);
            alert('Lỗi kết nối hệ thống');
        });
    });

    // 3. Xử lý khi bấm "Xem lại"
    btnConfirmNo.addEventListener('click', function() {
        confirmModal.setAttribute('aria-hidden', 'true');
        pendingFormData = null; // Xóa dữ liệu tạm
    });

    function formatMoney(amount) { return new Intl.NumberFormat('vi-VN').format(amount) + ' đ'; }
    function formatDate(d) { return new Date(d).toLocaleString('vi-VN'); }
});