<?php
// Bắt đầu session để lưu trạng thái đăng nhập
session_start();

// Kết nối đến cơ sở dữ liệu
require_once '../../helpers/db.php';

// Lấy danh sách đặt phòng từ cơ sở dữ liệu
try {
    $conn = dbConnect();
    $stmt = $conn->prepare("SELECT b.*, r.room_number, u.username FROM Bookings b
                             JOIN Rooms r ON b.room_id = r.id
                             JOIN Users u ON b.user_id = u.id");
    $stmt->execute();
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_message = 'Lỗi kết nối CSDL: ' . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách Đặt phòng</title>
    <link rel="stylesheet" href="../../assets/css/styles.css">
</head>
<body>
    <div class="container">
        <h1>Danh sách Đặt phòng</h1>

        <?php if (!empty($error_message)): ?>
            <div class="error">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên người dùng</th>
                    <th>Số phòng</th>
                    <th>Ngày đặt</th>
                    <th>Ngày đến</th>
                    <th>Ngày đi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bookings as $booking): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($booking['id']); ?></td>
                        <td><?php echo htmlspecialchars($booking['username']); ?></td>
                        <td><?php echo htmlspecialchars($booking['room_number']); ?></td>
                        <td><?php echo htmlspecialchars($booking['booking_date']); ?></td>
                        <td><?php echo htmlspecialchars($booking['check_in']); ?></td>
                        <td><?php echo htmlspecialchars($booking['check_out']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>