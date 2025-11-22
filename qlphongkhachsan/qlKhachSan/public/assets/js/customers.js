document.addEventListener('DOMContentLoaded', function () {
    // 1. CHECK THÔNG BÁO TỪ PHIÊN TRƯỚC
    const flashMsg = sessionStorage.getItem('flash_msg');
    if (flashMsg) {
        showNotification(flashMsg, 'success');
        sessionStorage.removeItem('flash_msg');
    }

    // 2. KHAI BÁO CÁC ELEMENT
    const modal = document.getElementById('customerModal');
    const form = document.getElementById('customerForm');
    const btnAdd = document.getElementById('btnAddCustomer');
    const btnCancel = document.getElementById('btnCancel');
    
    // Lấy URL xử lý
    const actionsUrl = window.customersConfig ? window.customersConfig.actionsUrl : 'actions/customer-actions.php';

    // --- HÀM HIỂN THỊ THÔNG BÁO (TOAST) ---
    function showNotification(message, type = 'success') {
        const container = document.getElementById('pageNotifications');
        if (!container) return;

        const div = document.createElement('div');
        div.className = `custom-alert alert-${type}`;
        div.innerHTML = `
            <span>${message}</span>
            <button class="alert-close" onclick="this.parentElement.remove()">×</button>
        `;
        
        container.appendChild(div);

        // Tự tắt sau 3s
        setTimeout(() => {
            div.style.animation = 'fadeOut 0.5s forwards';
            div.addEventListener('animationend', () => { if (div) div.remove(); });
        }, 3000);
    }

    // --- HÀM MỞ MODAL ---
    function openModal(title, data = {}) {
        document.getElementById('modalTitle').textContent = title;
        form.reset();
        
        // Điền dữ liệu vào form (Mapping từ data-attributes vào input name)
        if (form.id_khachhang) form.id_khachhang.value = data.id || '';
        if (form.ho_ten) form.ho_ten.value = data.ho_ten || '';
        if (form.cmnd_cccd) form.cmnd_cccd.value = data.cmnd_cccd || '';
        if (form.so_dien_thoai) form.so_dien_thoai.value = data.so_dien_thoai || '';
        if (form.email) form.email.value = data.email || '';
        if (form.dia_chi) form.dia_chi.value = data.dia_chi || '';
        if (form.quoc_tich) form.quoc_tich.value = data.quoc_tich || 'Việt Nam';

        modal.setAttribute('aria-hidden', 'false');
    }

    function closeModal() { modal.setAttribute('aria-hidden', 'true'); }

    if (btnAdd) btnAdd.onclick = () => openModal('Thêm khách hàng mới');
    if (btnCancel) btnCancel.onclick = closeModal;

    // --- XỬ LÝ CLICK TRONG BẢNG (SỬA / XÓA) ---
    // Dùng Event Delegation để bắt sự kiện chuẩn xác
    const tableBody = document.querySelector('.customers-table');
    if (tableBody) {
        tableBody.addEventListener('click', function(e) {
            const target = e.target;

            // 1. NÚT SỬA (Class mới: btn-edit)
            const btnEdit = target.closest('.btn-edit');
            if (btnEdit) {
                // Tìm thẻ tr chứa nút bấm để lấy dữ liệu
                const tr = btnEdit.closest('tr');
                const data = {
                    id: tr.dataset.id,
                    ho_ten: tr.dataset.ho_ten,
                    cmnd_cccd: tr.dataset.cmnd_cccd,
                    so_dien_thoai: tr.dataset.so_dien_thoai,
                    email: tr.dataset.email,
                    dia_chi: tr.dataset.dia_chi,
                    quoc_tich: tr.dataset.quoc_tich
                };
                openModal('Cập nhật thông tin', data);
            }

            // 2. NÚT XÓA (Class mới: btn-delete)
            const btnDelete = target.closest('.btn-delete');
            if (btnDelete) {
                if (!confirm('Bạn có chắc muốn xóa khách hàng này? Dữ liệu lịch sử đặt phòng cũng có thể bị ảnh hưởng.')) return;
                
                const tr = btnDelete.closest('tr');
                const formData = new FormData();
                formData.append('action', 'delete');
                formData.append('id_khachhang', tr.dataset.id);

                fetch(actionsUrl, { method: 'POST', body: formData })
                .then(r => r.json())
                .then(j => {
                    if (j.success) {
                        sessionStorage.setItem('flash_msg', j.msg || 'Xóa thành công!');
                        location.reload();
                    } else {
                        showNotification(j.msg, 'error');
                    }
                })
                .catch(err => {
                    console.error(err);
                    showNotification('Lỗi kết nối server', 'error');
                });
            }
        });
    }

    // --- XỬ LÝ SUBMIT FORM ---
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(form);
            
            // Xác định Create hay Update dựa vào việc có ID hay không
            const isUpdate = !!formData.get('id_khachhang');
            formData.append('action', isUpdate ? 'update' : 'create');

            fetch(actionsUrl, { method: 'POST', body: formData })
            .then(r => r.json())
            .then(j => {
                if (j.success) {
                    sessionStorage.setItem('flash_msg', j.msg || 'Thao tác thành công!');
                    location.reload();
                } else {
                    showNotification(j.msg, 'error');
                }
            })
            .catch(err => {
                console.error(err);
                showNotification('Lỗi kết nối server', 'error');
            });
        });
    }

    // Đóng modal khi click ra ngoài
    if (modal) {
        modal.addEventListener('click', e => { if (e.target === modal) closeModal(); });
    }
});