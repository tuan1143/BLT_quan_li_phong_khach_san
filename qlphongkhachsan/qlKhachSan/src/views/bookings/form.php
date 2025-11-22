<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt phòng - Quản lý Khách sạn</title>
    <link rel="stylesheet" href="/assets/css/styles.css">
</head>
<body>
    <div class="container">
        <h1 class="title">Đặt phòng</h1>
        <form action="/bookings/store" method="POST" class="booking-form">
            <div class="form-group">
                <label for="room_id">Chọn phòng:</label>
                <select id="room_id" name="room_id" required>
                    <!-- Options sẽ được tạo động từ danh sách phòng có sẵn -->
                </select>
            </div>

            <div class="form-group">
                <label for="user_id">Tên người đặt:</label>
                <input type="text" id="user_id" name="user_id" required placeholder="Nhập tên người đặt">
            </div>

            <div class="form-group">
                <label for="check_in">Ngày nhận phòng:</label>
                <input type="date" id="check_in" name="check_in" required>
            </div>

            <div class="form-group">
                <label for="check_out">Ngày trả phòng:</label>
                <input type="date" id="check_out" name="check_out" required>
            </div>

            <button type="submit" class="btn">Đặt phòng</button>
        </form>
    </div>
</body>
</html>