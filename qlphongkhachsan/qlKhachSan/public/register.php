<?php
// Bắt đầu session để lưu trạng thái đăng nhập
session_start();

// 1. Thông tin kết nối CSDL (Thay đổi cho phù hợp)
$db_host = 'localhost';
$db_name = 'quanly_khachsan'; // Tên DB từ phần trước
$db_user = 'root';
$db_pass = ''; // Mật khẩu của bạn

// Biến lưu trữ thông báo lỗi
$error_message = '';

// 2. Xử lý khi người dùng gửi form (nhấn nút Đăng ký)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Kiểm tra nếu thông tin bị bỏ trống
    if (empty($username) || empty($password) || empty($confirm_password)) {
        $error_message = 'Vui lòng nhập đầy đủ thông tin!';
    } elseif ($password !== $confirm_password) {
        $error_message = 'Mật khẩu không khớp!';
    } else {
        try {
            // 3. Kết nối CSDL bằng PDO (An toàn hơn)
            $conn = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // 4. Kiểm tra xem tên đăng nhập đã tồn tại chưa
            $stmt = $conn->prepare("SELECT * FROM NhanVien WHERE username = ?");
            $stmt->execute([$username]);

            if ($stmt->fetch(PDO::FETCH_ASSOC)) {
                $error_message = 'Tên đăng nhập đã tồn tại!';
            } else {
                // 5. Hash mật khẩu và lưu thông tin người dùng
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO NhanVien (username, password_hash) VALUES (?, ?)");
                $stmt->execute([$username, $password_hash]);

                // Đăng ký thành công
                header('Location: login.php');
                exit;
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
    <title>Đăng ký - Quản lý Khách sạn</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <div class="container">
        <h1>Đăng ký tài khoản</h1>
        <?php if (!empty($error_message)): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>
        <form action="register.php" method="POST">
            <div>
                <label for="username">Tên đăng nhập</label>
                <input id="username" name="username" type="text" required>
            </div>
            <div>
                <label for="password">Mật khẩu</label>
                <input id="password" name="password" type="password" required>
            </div>
            <div>
                <label for="confirm_password">Xác nhận mật khẩu</label>
                <input id="confirm_password" name="confirm_password" type="password" required>
            </div>
            <button type="submit">Đăng ký</button>
        </form>
        <p>Đã có tài khoản? <a href="login.php">Đăng nhập</a></p>
    </div>
</body>
</html>