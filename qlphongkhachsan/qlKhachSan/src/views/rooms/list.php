<?php
// Bắt đầu session để lưu trạng thái đăng nhập
session_start();

// Kết nối đến cơ sở dữ liệu
require_once '../../helpers/db.php';

// Lấy danh sách phòng từ cơ sở dữ liệu
function getRooms() {
    $conn = dbConnect();
    $stmt = $conn->prepare("SELECT * FROM rooms");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$rooms = getRooms();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách phòng - Quản lý Khách sạn</title>
    <link rel="stylesheet" href="../../assets/css/styles.css">
</head>
<body>
    <div class="container">
        <h1 class="title">Danh sách phòng</h1>
        <table class="room-list">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên phòng</th>
                    <th>Giá</th>
                    <th>Trạng thái</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rooms as $room): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($room['id']); ?></td>
                        <td><?php echo htmlspecialchars($room['name']); ?></td>
                        <td><?php echo htmlspecialchars($room['price']); ?> VNĐ</td>
                        <td><?php echo htmlspecialchars($room['status']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>