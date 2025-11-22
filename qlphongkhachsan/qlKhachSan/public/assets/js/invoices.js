document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('invoiceModal');
    const btnClose = document.getElementById('btnCloseModal');
    const tableBody = document.querySelector('.invoices-table');

    // Hàm mở Modal
    function openModal(data) {
        // Điền dữ liệu vào các thẻ span/strong trong modal
        document.getElementById('detCode').textContent = data.code;
        document.getElementById('detDate').textContent = data.date;
        document.getElementById('detCustomer').textContent = data.customer;
        document.getElementById('detRoom').textContent = data.room;
        document.getElementById('detStaff').textContent = data.staff;
        
        document.getElementById('detRoomMoney').textContent = data.roomMoney + ' đ';
        document.getElementById('detServiceMoney').textContent = data.serviceMoney + ' đ';
        document.getElementById('detTotal').textContent = data.total + ' đ';
        document.getElementById('detMethod').textContent = data.method;

        // Hiện modal
        modal.setAttribute('aria-hidden', 'false');
    }

    function closeModal() {
        modal.setAttribute('aria-hidden', 'true');
    }

    // Bắt sự kiện click nút Xem
    if (tableBody) {
        tableBody.addEventListener('click', function(e) {
            const btn = e.target.closest('.btn-view-detail');
            if (btn) {
                // Lấy dữ liệu từ data-attributes
                const data = {
                    code: btn.dataset.code,
                    date: btn.dataset.date,
                    customer: btn.dataset.customer,
                    room: btn.dataset.room,
                    staff: btn.dataset.staff,
                    roomMoney: btn.dataset.roomMoney,
                    serviceMoney: btn.dataset.serviceMoney,
                    total: btn.dataset.total,
                    method: btn.dataset.method
                };
                openModal(data);
            }
        });
    }

    if (btnClose) btnClose.onclick = closeModal;

    // Đóng khi click ra ngoài
    if (modal) {
        modal.addEventListener('click', e => {
            if (e.target === modal) closeModal();
        });
    }
});