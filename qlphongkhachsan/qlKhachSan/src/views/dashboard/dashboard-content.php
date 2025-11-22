<div class="stats-grid dashboard-stats">
    <div class="stat-card stat-trong">
        <h3>Phòng trống</h3>
        <div class="stat-value"><?php echo htmlspecialchars($stats['phong_trong'] ?? 0); ?></div>
    </div>
    <div class="stat-card stat-dango">
        <h3>Đang ở</h3>
        <div class="stat-value"><?php echo htmlspecialchars($stats['dang_thue'] ?? 0); ?></div>
    </div>
    <div class="stat-card stat-dadat">
        <h3>Đã đặt</h3>
        <div class="stat-value"><?php echo htmlspecialchars($stats['da_dat'] ?? 0); ?></div>
    </div>
    <div class="stat-card stat-dondep">
        <h3>Đang dọn dẹp</h3>
        <div class="stat-value"><?php echo htmlspecialchars($stats['don_dep'] ?? 0); ?></div>
    </div>
    <div class="stat-card stat-baotri">
        <h3>Bảo trì</h3>
        <div class="stat-value"><?php echo htmlspecialchars($stats['bao_tri'] ?? 0); ?></div>
    </div>
</div>

<div class="stat-card stat-dondep">
  

    <div class="stat-card" style="border-color: #E9C46A; background: rgba(233, 196, 106, 0.1);">
        <h3 style="color:#E9C46A">Doanh Thu Hôm Nay</h3>
        <div class="stat-value" style="color:#E9C46A; font-size: 24px;">
            <?php echo number_format($doanh_thu_hom_nay); ?> đ
        </div>
    </div>

</div> 
<div class="charts-container" style="display: flex; gap: 20px; margin-top: 20px;">
    
    <div class="content-card" style="flex: 2;">
        <h3 style="margin-bottom: 15px; color: #ccc; font-size: 14px; text-transform: uppercase;">
            Biểu đồ doanh thu (7 ngày)
        </h3>
        <div class="chart-wrapper">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>
    
    <div class="content-card" style="flex: 1; display: flex; flex-direction: column; justify-content: center; align-items: center; background: rgba(255, 255, 255, 0.02);">
        
        <h3 style="color: #aaa; font-size: 14px; text-transform: uppercase; margin-bottom: 10px;">
            Tổng khách đang lưu trú
        </h3>
        
        <div style="font-size: 40px; margin-bottom: 10px;">👥</div>
        
        <div style="font-size: 60px; font-weight: bold; color: #4cd137; text-shadow: 0 0 20px rgba(76, 209, 55, 0.3);">
            <?php echo $tong_khach_dang_o; ?>
        </div>
        
        <div style="color: #666; font-size: 13px; margin-top: 5px;">
            người
        </div>

    </div>
</div>