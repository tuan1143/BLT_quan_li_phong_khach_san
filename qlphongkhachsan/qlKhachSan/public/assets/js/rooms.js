document.addEventListener('DOMContentLoaded', function () {
    // --- SETUP THÔNG BÁO ---
    function showNotification(message, type = 'success') {
        const container = document.getElementById('pageNotifications');
        if (!container) return;
        const div = document.createElement('div');
        div.className = `custom-alert alert-${type}`;
        div.innerHTML = `<span>${message}</span><button class="alert-close" onclick="this.parentElement.remove()">×</button>`;
        container.appendChild(div);
        setTimeout(() => div.remove(), 3000);
    }
    
    const flashMsg = sessionStorage.getItem('flash_msg');
    if (flashMsg) { showNotification(flashMsg); sessionStorage.removeItem('flash_msg'); }

    const actionsUrl = window.roomsConfig ? window.roomsConfig.actionsUrl : 'actions/room-actions.php';

    // --- MODAL 1: CHI TIẾT & SỬA ---
    const modal = document.getElementById('roomModal');
    const form = document.getElementById('roomForm');
    const btnAdd = document.getElementById('btnAddRoom');
    const btnCancel = document.getElementById('btnCancel');

    function openModal(title, data = {}) {
        document.getElementById('modalTitle').textContent = title;
        form.reset();
        
        if (form.id_phong) form.id_phong.value = data.id_phong || '';
        if (form.ten_phong) form.ten_phong.value = data.ten_phong || '';
        if (form.id_loaiphong) form.id_loaiphong.value = data.id_loaiphong || ''; 
        if (form.gia_phong) form.gia_phong.value = (data.gia_phong > 0) ? data.gia_phong : '';
        if (form.ghi_chu) form.ghi_chu.value = data.ghi_chu || '';
        // Giữ nguyên trạng thái cũ trong input hidden
        document.getElementById('roomStatusHidden').value = data.trang_thai || 'Trong';
        
        document.getElementById('formAction').value = data.id_phong ? 'update' : 'create';
        modal.setAttribute('aria-hidden', 'false');
    }

    if(btnAdd) btnAdd.onclick = () => openModal('Thêm phòng mới');
    if(btnCancel) btnCancel.onclick = () => modal.setAttribute('aria-hidden', 'true');

    // --- MODAL 2: ĐỔI TRẠNG THÁI NHANH ---
    const statusModal = document.getElementById('statusModal');
    const statusForm = document.getElementById('statusForm');
    const btnCancelStatus = document.getElementById('btnCancelStatus');

    if(btnCancelStatus) btnCancelStatus.onclick = () => statusModal.setAttribute('aria-hidden', 'true');

    // --- XỬ LÝ CLICK TRONG BẢNG ---
    const tableBody = document.querySelector('.rooms-table');
    if (tableBody) {
        tableBody.addEventListener('click', function(e) {
            const target = e.target;

            // 1. Nút CHI TIẾT
            const btnView = target.closest('.btn-view');
            if (btnView) {
                const data = {
                    id_phong: btnView.dataset.id,
                    ten_phong: btnView.dataset.ten,
                    id_loaiphong: btnView.dataset.loaiId,
                    gia_phong: btnView.dataset.price,
                    trang_thai: btnView.dataset.status,
                    ghi_chu: btnView.dataset.note
                };
                openModal('Chi tiết / Chỉnh sửa Phòng', data);
            }

            // 2. Nút ĐỔI TRẠNG THÁI NHANH (Bấm vào badge)
            const btnStatus = target.closest('.btn-quick-status');
            if (btnStatus) {
                document.getElementById('statusRoomId').value = btnStatus.dataset.id;
                document.getElementById('quickStatusSelect').value = btnStatus.dataset.status;
                statusModal.setAttribute('aria-hidden', 'false');
            }

            // 3. Nút XÓA
            const btnDelete = target.closest('.btn-delete');
            if (btnDelete) {
                if (!confirm('Xóa phòng này?')) return;
                const fd = new FormData();
                fd.append('action', 'delete');
                fd.append('id_phong', btnDelete.dataset.id);
                sendData(fd, actionsUrl);
            }
        });
    }

    // --- SUBMIT FORM CHÍNH ---
    if(form) {
        form.addEventListener('submit', e => {
            e.preventDefault();
            sendData(new FormData(form), actionsUrl);
        });
    }

    // --- SUBMIT FORM TRẠNG THÁI ---
    if(statusForm) {
        statusForm.addEventListener('submit', e => {
            e.preventDefault();
            sendData(new FormData(statusForm), actionsUrl);
        });
    }

    // Hàm gửi AJAX chung
    function sendData(formData, url) {
        fetch(url, { method: 'POST', body: formData })
        .then(r => r.json())
        .then(j => {
            if(j.success) {
                sessionStorage.setItem('flash_msg', j.msg || 'Thành công!');
                location.reload();
            } else {
                showNotification(j.msg, 'error');
            }
        });
    }
});