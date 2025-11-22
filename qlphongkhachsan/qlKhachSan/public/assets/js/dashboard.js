document.addEventListener('DOMContentLoaded', function () {
    // Kiểm tra thư viện
    if (typeof Chart === 'undefined') {
        console.error('Lỗi: Thư viện Chart.js chưa được tải.');
        return;
    }

    // Cấu hình màu sắc chung
    Chart.defaults.color = '#888';
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.borderColor = 'rgba(255, 255, 255, 0.05)';

    // --- VẼ BIỂU ĐỒ DOANH THU (STATIC MODE) ---
    const ctxRevenue = document.getElementById('revenueChart');
    
    if (ctxRevenue) {
        const labels = window.chartLabels || [];
        const data = window.chartData || [];

        new Chart(ctxRevenue, {
            type: 'line', 
            data: {
                labels: labels,
                datasets: [{
                    label: 'Doanh thu',
                    data: data,
                    // Màu sắc cố định
                    borderColor: '#E9C46A', 
                    backgroundColor: 'rgba(233, 196, 106, 0.1)', // Màu nền mờ bên dưới
                    borderWidth: 2,
                    
                    // Điểm dữ liệu
                    pointBackgroundColor: '#E9C46A',
                    pointBorderColor: '#fff',
                    pointRadius: 4,
                    pointHoverRadius: 4, // Không phóng to khi hover
                    
                    fill: true, // Tô màu dưới đường
                    tension: 0 // Đường thẳng (0) hoặc cong nhẹ (0.1) -> Để 0 cho nó tĩnh và dứt khoát
                }]
            },
            options: {
                // [QUAN TRỌNG] TẮT TOÀN BỘ HIỆU ỨNG ĐỘNG
                animation: false, 
                animations: {
                    colors: false,
                    x: false,
                },
                transitions: {
                    active: {
                        animation: {
                            duration: 0
                        }
                    }
                },
                
                responsive: true,
                maintainAspectRatio: false,
                
                // Tinh chỉnh hiển thị
                plugins: {
                    legend: {
                        display: false // Ẩn chú thích thừa
                    },
                    tooltip: {
                        // Vẫn giữ tooltip để xem số tiền khi di chuột (rất cần thiết)
                        // Nếu muốn tắt luôn thì đặt enabled: false
                        enabled: true, 
                        backgroundColor: 'rgba(0,0,0,0.8)',
                        titleColor: '#E9C46A',
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(context.raw);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        border: { display: false },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.05)',
                            drawBorder: false,
                        },
                        ticks: {
                            color: '#666',
                            callback: function(value) {
                                if(value >= 1000000) return value/1000000 + 'M';
                                if(value >= 1000) return value/1000 + 'k';
                                return value;
                            }
                        }
                    },
                    x: {
                        grid: { display: false },
                        border: { display: false },
                        ticks: { color: '#888' }
                    }
                }
            }
        });
    }
});