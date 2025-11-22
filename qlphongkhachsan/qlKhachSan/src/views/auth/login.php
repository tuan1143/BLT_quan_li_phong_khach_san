<?php
// Bắt đầu session để lưu trạng thái đăng nhập
session_start();

// Thông tin kết nối CSDL
$db_host = 'localhost';
$db_name = 'hotel_booking_system'; // Tên DB
$db_user = 'root';
$db_pass = ''; // Mật khẩu của bạn

// Biến lưu trữ thông báo lỗi
$error_message = '';

// Xử lý khi người dùng gửi form (nhấn nút Đăng nhập)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Kiểm tra nếu thông tin bị bỏ trống
    if (empty($username) || empty($password)) {
        $error_message = 'Vui lòng nhập đầy đủ tên đăng nhập và mật khẩu!';
    } else {
        try {
            // Kết nối CSDL bằng PDO
            $conn = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Chuẩn bị câu lệnh SQL
            $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$username]);

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Kiểm tra người dùng và mật khẩu
            if ($user && password_verify($password, $user['password_hash'])) {
                // Đăng nhập thành công: Lưu thông tin vào Session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];

                // Chuyển hướng đến trang quản lý (dashboard)
                header('Location: dashboard.php');
                exit;
            } else {
                // Đăng nhập thất bại
                $error_message = 'Tên đăng nhập hoặc mật khẩu không chính xác!';
            }
        } catch(PDOException $e) {
            // Lỗi kết nối CSDL
            $error_message = 'Lỗi kết nối CSDL: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - Hệ thống Đặt phòng Khách sạn</title>
    <link rel="stylesheet" href="/assets/css/styles.css">
</head>
<body>
    <div class="login-container">
        <h1>Đăng nhập</h1>
        <?php if (!empty($error_message)): ?>
            <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        <form action="login.php" method="POST">
            <label for="username">Tên đăng nhập</label>
            <input type="text" id="username" name="username" required>
            <label for="password">Mật khẩu</label>
            <input type="password" id="password" name="password" required>
            <button type="submit">Đăng nhập</button>
        </form>
    </div>
</body>
</html>