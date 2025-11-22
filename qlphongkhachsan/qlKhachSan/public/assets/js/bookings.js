// File: public/assets/js/bookings.js
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('bookingModal');
    const form = document.getElementById('bookingForm');
    const btnAdd = document.getElementById('btnAddBooking');
    const btnCancel = document.getElementById('btnCancel');
    
    const actionsUrl = window.bookingsConfig.actionsUrl;

    // Hàm chuyển '2025-11-05 14:00:00' thành '2025-11-05T14:00'
    function formatDateTimeForInput(dateTimeStr) {
        if (!dateTimeStr) return '';
        return dateTimeStr.replace(' ', 'T').substring(0, 16);
    }

    function openModal(title, data = {}) {
        document.getElementById('modalTitle').textContent = title;
        form.reset(); 
        
        form.id_datphong.value = data.id_datphong || '';
        form.id_khachhang.value = data.id_khachhang || '';
        form.id_phong.value = data.id_phong || '';
        form.so_nguoi_o.value = data.so_nguoi_o || '1';
        form.tien_dat_coc.value = data.tien_dat_coc || '0';
        form.trang_thai.value = data.trang_thai || 'MoiDat';
        
        // Dùng hàm format
        form.ngay_checkin.value = formatDateTimeForInput(data.ngay_checkin);
        form.ngay_checkout_dukien.value = formatDateTimeForInput(data.ngay_checkout_dukien);
        
        modal.setAttribute('aria-hidden', 'false');
        form.id_khachhang.focus();
    }

    function closeModal() {
        modal.setAttribute('aria-hidden', 'true');
    }

    if (btnAdd) {
        btnAdd.onclick = () => openModal('Đặt phòng mới');
    }

    // Các nút "Sửa"
    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', e => {
            const tr = e.target.closest('tr');
            // Lấy dữ liệu từ <tr> (dataset)
            const bookingData = {
                id_datphong: tr.dataset.id_datphong,
                id_khachhang: tr.dataset.id_khachhang,
                id_phong: tr.dataset.id_phong,
                ngay_checkin: tr.dataset.ngay_checkin,
                ngay_checkout_dukien: tr.dataset.ngay_checkout_dukien,
                so_nguoi_o: tr.dataset.so_nguoi_o,
                tien_dat_coc: tr.dataset.tien_dat_coc,
                trang_thai: tr.dataset.trang_thai
            };
            openModal('Sửa thông tin Đặt phòng', bookingData);
        });
    });

    // Các nút "Hủy" (thay vì Xóa)
    document.querySelectorAll('.cancel-btn').forEach(btn => {
        btn.addEventListener('click', e => {
            if (!confirm('Bạn có chắc chắn muốn HỦY đặt phòng này không? (Phòng sẽ được chuyển về "Trống")')) return;

            const id = e.target.closest('tr').dataset.id_datphong;
            const data = new URLSearchParams({
                action: 'cancel',
                id_datphong: id
            });

            fetch(actionsUrl, { method: 'POST', body: data })
            .then(r => r.json())
            .then(j => {
                if (j.success) location.reload();
                else alert(j.msg || 'Hủy thất bại');
            });
        });
    });

    if(btnCancel) btnCancel.onclick = closeModal;
    
    if(modal) {
        modal.addEventListener('click', e => {
            if (e.target === modal) closeModal();
        });
    }

    if(form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            
            const data = new URLSearchParams(new FormData(form));
            const isUpdate = !!form.id_datphong.value;
            data.append('action', isUpdate ? 'update' : 'create');

            fetch(actionsUrl, { method: 'POST', body: data })
            .then(r => r.json())
            .then(j => {
                if (j.success) location.reload();
                else alert(j.msg || 'Lỗi xử lý');
            });
        });
    }
});