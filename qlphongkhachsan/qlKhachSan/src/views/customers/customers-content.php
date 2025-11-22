<div class="page-header">
    <h2>Quản lý Khách hàng</h2>
    
    <div class="header-actions">
        <form action="customers.php" method="GET" class="search-box">
            <input type="text" name="search" 
                   placeholder="Nhập Tên, SĐT, CMND hoặc Email..." 
                   value="<?php echo htmlspecialchars($search ?? ''); ?>"
                   autocomplete="off">
            
            <?php if(!empty($search)): ?>
                <a href="customers.php" class="btn-clear">×</a>
            <?php endif; ?>
            
            <button type="submit" class="btn-search">🔍</button>
        </form>

        <button id="btnAddCustomer" class="btn btn-primary">
            + Thêm khách hàng
        </button>
    </div>
    
</div>
<div id="pageNotifications" class="alert-container">    </div>
<div class="content-card">
    <table class="customers-table">
        <thead>
            <tr>
                <th>Họ tên</th>
                <th>CMND/CCCD</th>
                <th>Số điện thoại</th>
                <th>Email</th>
                <th>Địa chỉ</th>
                <th>Quốc tịch</th>
                <th class="text-center">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($danh_sach_khach_hang)): ?>
                <tr>
                    <td colspan="7" class="text-center">Không tìm thấy khách hàng nào.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($danh_sach_khach_hang as $khach): ?>
                    <tr data-id="<?php echo $khach['id_khachhang']; ?>"
                        data-ho_ten="<?php echo htmlspecialchars($khach['ho_ten']); ?>"
                        data-cmnd_cccd="<?php echo htmlspecialchars($khach['cmnd_cccd']); ?>"
                        data-so_dien_thoai="<?php echo htmlspecialchars($khach['so_dien_thoai']); ?>"
                        data-email="<?php echo htmlspecialchars($khach['email']); ?>"
                        data-dia_chi="<?php echo htmlspecialchars($khach['dia_chi']); ?>"
                        data-quoc_tich="<?php echo htmlspecialchars($khach['quoc_tich']); ?>"
                    >
                        <td><strong><?php echo htmlspecialchars($khach['ho_ten']); ?></strong></td>
                        <td><?php echo htmlspecialchars($khach['cmnd_cccd']); ?></td>
                        <td><?php echo htmlspecialchars($khach['so_dien_thoai']); ?></td>
                        <td><?php echo htmlspecialchars($khach['email']); ?></td>
                        <td><?php echo htmlspecialchars($khach['dia_chi']); ?></td>
                        <td><?php echo htmlspecialchars($khach['quoc_tich']); ?></td>
                        
                        <td class="table-actions">
                            <button class="btn btn-edit">Sửa</button>
                            <button class="btn btn-delete">Xóa</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <?php 
    // Kiểm tra file helper có tồn tại không để tránh lỗi
    if (file_exists(PROJECT_ROOT . '/public/includes/paginator.php')) {
        require_once PROJECT_ROOT . '/public/includes/paginator.php';
        // Biến $total_pages và $page được tính ở Controller (public/customers.php)
        if (isset($total_pages) && isset($page)) {
            renderPagination($total_pages, $page, 'customers.php'); 
        }
    }
    ?>
</div>

<div id="customerModal" class="modal-overlay" aria-hidden="true">
    <div class="modal-content">
        <h2 id="modalTitle">Thêm khách hàng mới</h2>
        <form id="customerForm">
            <input type="hidden" name="id_khachhang" id="id_khachhang" value="">

            <div class="form-group">
                <label for="ho_ten">Họ tên <span style="color:red">*</span></label>
                <input type="text" name="ho_ten" id="ho_ten" required>
            </div>

            <div class="form-group">
                <label for="cmnd_cccd">CMND/CCCD <span style="color:red">*</span></label>
                <input type="text" name="cmnd_cccd" id="cmnd_cccd" required>
            </div>
            
            <div class="form-group">
                <label for="so_dien_thoai">Số điện thoại</label>
                <input type="text" name="so_dien_thoai" id="so_dien_thoai">
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email">
            </div>

            <div class="form-group">
                <label for="dia_chi">Địa chỉ</label>
                <textarea name="dia_chi" id="dia_chi" rows="2"></textarea>
            </div>
            
            <div class="form-group">
                <label for="quoc_tich">Quốc tịch</label>
                <input type="text" name="quoc_tich" id="quoc_tich" value="Việt Nam">
            </div>

            <div class="modal-actions">
                <button type="button" id="btnCancel" class="btn">Hủy</button>
                <button type="submit" class="btn btn-primary">Lưu lại</button>
            </div>
        </form>
    </div>
</div>